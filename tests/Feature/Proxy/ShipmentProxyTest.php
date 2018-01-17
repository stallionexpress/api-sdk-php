<?php

namespace MyParcelCom\ApiSdk\Tests\Feature\Proxy;

use GuzzleHttp\ClientInterface;
use MyParcelCom\ApiSdk\Authentication\AuthenticatorInterface;
use MyParcelCom\ApiSdk\MyParcelComApi;
use MyParcelCom\ApiSdk\MyParcelComApiInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\AddressInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ContractInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\PhysicalPropertiesInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceOptionInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShopInterface;
use MyParcelCom\ApiSdk\Resources\Proxy\ShipmentProxy;
use MyParcelCom\ApiSdk\Tests\Traits\MocksApiCommunication;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Simple\NullCache;

class ShipmentProxyTest extends TestCase
{
    use MocksApiCommunication;

    /** @var ClientInterface */
    private $client;
    /** @var AuthenticatorInterface */
    private $authenticator;
    /** @var MyParcelComApiInterface */
    private $api;
    /** @var ShipmentProxy */
    private $shipmentProxy;

    public function setUp()
    {
        parent::setUp();

        $this->client = $this->getClientMock();
        $this->authenticator = $this->getAuthenticatorMock();
        $this->api = (new MyParcelComApi('https://api'))
            ->setCache(new NullCache())
            ->setHttpClient($this->client)
            ->authenticate($this->authenticator);

        $this->shipmentProxy = new ShipmentProxy();
        $this->shipmentProxy
            ->setMyParcelComApi($this->api)
            ->setId('shipment-id-1');
    }

    /** @test */
    public function testAttributes()
    {
        $this->assertEquals('shipment-id-1', $this->shipmentProxy->getId());
        $this->assertEquals(ResourceInterface::TYPE_SHIPMENT, $this->shipmentProxy->getType());
        $this->assertInstanceOf(AddressInterface::class, $this->shipmentProxy->getRecipientAddress());
        $this->assertEquals('Some road', $this->shipmentProxy->getRecipientAddress()->getStreet1());
        $this->assertEquals('1GL HF1', $this->shipmentProxy->getRecipientAddress()->getPostalCode());

        $this->assertInstanceOf(AddressInterface::class, $this->shipmentProxy->getSenderAddress());
        $this->assertEquals(17, $this->shipmentProxy->getSenderAddress()->getStreetNumber());
        $this->assertEquals('Cardiff', $this->shipmentProxy->getSenderAddress()->getCity());

        $this->assertEquals('123456', $this->shipmentProxy->getPickupLocationCode());
        $this->assertInstanceOf(AddressInterface::class, $this->shipmentProxy->getPickupLocationAddress());
        $this->assertEquals('GB', $this->shipmentProxy->getPickupLocationAddress()->getCountryCode());
        $this->assertEquals('Doe', $this->shipmentProxy->getPickupLocationAddress()->getLastName());

        $this->assertEquals('Playstation 4', $this->shipmentProxy
            ->setDescription('Playstation 4')
            ->getDescription()
        );
        $this->assertEquals(50, $this->shipmentProxy->setPrice(50)->getPrice());

        $this->assertEquals(1337, $this->shipmentProxy->setInsuranceAmount(1337)->getInsuranceAmount());
        $this->assertEquals('EUR', $this->shipmentProxy->getCurrency());
        $this->assertEquals('3SABCD0123456789', $this->shipmentProxy->getBarcode());
        $this->assertEquals('TR4CK1NGC0D3', $this->shipmentProxy->getTrackingCode());
        $this->assertEquals('https://track.me/TR4CK1NGC0D3', $this->shipmentProxy->getTrackingUrl());
        $this->assertEquals(24, $this->shipmentProxy->getWeight());
        $this->assertInstanceOf(PhysicalPropertiesInterface::class, $this->shipmentProxy->getPhysicalProperties());
        $this->assertEquals(24, $this->shipmentProxy->getPhysicalProperties()->getHeight());
        $this->assertEquals(50, $this->shipmentProxy->getPhysicalProperties()->getWeight());
        $this->assertEquals(50, $this->shipmentProxy->getPhysicalProperties()->getLength());
        $this->assertEquals(50, $this->shipmentProxy->getPhysicalProperties()->getVolume());
        $this->assertEquals(24, $this->shipmentProxy->getPhysicalProperties()->getWeight());
    }

    /** @test */
    public function testShopRelationShip()
    {
        $this->assertInstanceOf(ShopInterface::class, $this->shipmentProxy->getShop());
        $this->assertEquals(ResourceInterface::TYPE_SHOP, $this->shipmentProxy->getShop()->getType());
        $this->assertEquals('shop-id-1', $this->shipmentProxy->getShop()->getId());
    }

    /** @test */
    public function testServiceRelationship()
    {
        $this->assertInstanceOf(ServiceInterface::class, $this->shipmentProxy->getService());
        $this->assertEquals(ResourceInterface::TYPE_SERVICE, $this->shipmentProxy->getService()->getType());
        $this->assertEquals('service-id-1', $this->shipmentProxy->getService()->getId());
    }

    /** @test */
    public function testContractRelationShip()
    {
        $this->assertInstanceOf(ContractInterface::class, $this->shipmentProxy->getContract());
        $this->assertEquals(ResourceInterface::TYPE_CONTRACT, $this->shipmentProxy->getContract()->getType());
        $this->assertEquals('contract-id-1', $this->shipmentProxy->getContract()->getId());
    }

    /** @test */
    public function testServiceOptionsRelationship()
    {
        // getServiceOptions() should retrieve the options from the stub.
        $serviceOptions = $this->shipmentProxy->getServiceOptions();
        array_walk($serviceOptions, function (ServiceOptionInterface $option) {
            $this->assertInstanceOf(ServiceOptionInterface::class, $option);
            $this->assertEquals(ResourceInterface::TYPE_SERVICE_OPTION, $option->getType());
        });
        $serviceOptionIds = array_map(function (ServiceOptionInterface $serviceOption) {
            return $serviceOption->getId();
        }, $serviceOptions);
        $this->assertContains('service-option-id-1', $serviceOptionIds);

        // First setting the service options should overwrite
        // potential old service options.
        $serviceOption_A = $this->createMock(ServiceOptionInterface::class);
        $serviceOption_A
            ->method('getId')
            ->willReturn('service-option-id-2');
        $serviceOption_B = $this->createMock(ServiceOptionInterface::class);
        $serviceOption_B
            ->method('getId')
            ->willReturn('service-option-id-3');

        $this->shipmentProxy->setServiceOptions([$serviceOption_A, $serviceOption_B]);

        $serviceOptions = $this->shipmentProxy->getServiceOptions();
        array_walk($serviceOptions, function (ServiceOptionInterface $option) {
            $this->assertInstanceOf(ServiceOptionInterface::class, $option);
        });
        $serviceOptionIds = array_map(function (ServiceOptionInterface $serviceOption) {
            return $serviceOption->getId();
        }, $serviceOptions);
        $this->assertArraySubset(['service-option-id-2', 'service-option-id-3'], $serviceOptionIds);

        // Adding a service option should keep the old service options
        // and add a new one to the array.
        $serviceOption_C = $this->createMock(ServiceOptionInterface::class);
        $serviceOption_C
            ->method('getId')
            ->willReturn('service-option-id-4');

        $this->shipmentProxy->addServiceOption($serviceOption_C);

        $serviceOptions = $this->shipmentProxy->getServiceOptions();
        array_walk($serviceOptions, function (ServiceOptionInterface $option) {
            $this->assertInstanceOf(ServiceOptionInterface::class, $option);
        });
        $serviceOptionIds = array_map(function (ServiceOptionInterface $serviceOption) {
            return $serviceOption->getId();
        }, $serviceOptions);
        $this->assertArraySubset([
            'service-option-id-2',
            'service-option-id-3',
            'service-option-id-4'
        ], $serviceOptionIds);
    }
}
