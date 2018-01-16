<?php

namespace MyParcelCom\ApiSdk\Tests\Feature\Proxy;

use GuzzleHttp\ClientInterface;
use MyParcelCom\ApiSdk\Authentication\AuthenticatorInterface;
use MyParcelCom\ApiSdk\MyParcelComApi;
use MyParcelCom\ApiSdk\MyParcelComApiInterface;
use MyParcelCom\ApiSdk\Resources\Address;
use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceInterface;
use MyParcelCom\ApiSdk\Resources\Proxy\ShipmentProxy;
use MyParcelCom\ApiSdk\Resources\Shop;
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

    public function setUp()
    {
        parent::setUp();

        $this->client = $this->getClientMock();
        $this->authenticator = $this->getAuthenticatorMock();
        $this->api = (new MyParcelComApi('https://api'))
            ->setCache(new NullCache())
            ->setHttpClient($this->client)
            ->authenticate($this->authenticator);
    }

    /** @test */
    public function testAttributes()
    {
        $shipmentProxy = new ShipmentProxy();
        $shipmentProxy
            ->setMyParcelComApi($this->api)
            ->setId('shipment-id-1');

        $this->assertEquals('shipment-id-1', $shipmentProxy->getId());
        $this->assertEquals(ResourceInterface::TYPE_SHIPMENT, $shipmentProxy->getType());
        $this->assertInstanceOf(Address::class, $shipmentProxy->getRecipientAddress());
        $this->assertEquals('Some road', $shipmentProxy->getRecipientAddress()->getStreet1());
        $this->assertEquals('1GL HF1', $shipmentProxy->getRecipientAddress()->getPostalCode());

        $this->assertInstanceOf(Address::class, $shipmentProxy->getSenderAddress());
        $this->assertEquals(17, $shipmentProxy->getSenderAddress()->getStreetNumber());
        $this->assertEquals('Cardiff', $shipmentProxy->getSenderAddress()->getCity());

        $this->assertEquals('123456', $shipmentProxy->getPickupLocationCode());
        $this->assertInstanceOf(Address::class, $shipmentProxy->getPickupLocationAddress());
        $this->assertEquals('GB', $shipmentProxy->getPickupLocationAddress()->getCountryCode());
        $this->assertEquals('Doe', $shipmentProxy->getPickupLocationAddress()->getLastName());

        $this->assertEquals('Playstation 4', $shipmentProxy
            ->setDescription('Playstation 4')
            ->getDescription()
        );
        $this->assertEquals(50, $shipmentProxy->setPrice(50)->getPrice());

        $this->assertEquals(1337, $shipmentProxy->setInsuranceAmount(1337)->getInsuranceAmount());
        $this->assertEquals('EUR', $shipmentProxy->getCurrency());
        $this->assertEquals('3SABCD0123456789', $shipmentProxy->getBarcode());
        $this->assertEquals('TR4CK1NGC0D3', $shipmentProxy->getTrackingCode());
        $this->assertEquals('https://track.me/TR4CK1NGC0D3', $shipmentProxy->getTrackingUrl());
        $this->assertEquals(24, $shipmentProxy->getWeight());
        $this->assertInstanceOf(ResourceInterface::TYPE_SHOP, $shipmentProxy->getShop());
        $this->assertEquals('shop-id-1', $shipmentProxy->getShop()->getId());

        // TODO: FINISH!
    }
}