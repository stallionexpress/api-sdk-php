<?php

namespace MyParcelCom\Sdk\Tests\Feature;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response;
use MyParcelCom\Sdk\Authentication\AuthenticatorInterface;
use MyParcelCom\Sdk\Exceptions\InvalidResourceException;
use MyParcelCom\Sdk\MyParcelComApi;
use MyParcelCom\Sdk\Resources\Address;
use MyParcelCom\Sdk\Resources\Interfaces\CarrierInterface;
use MyParcelCom\Sdk\Resources\Interfaces\FileInterface;
use MyParcelCom\Sdk\Resources\Interfaces\PickUpDropOffLocationInterface;
use MyParcelCom\Sdk\Resources\Interfaces\RegionInterface;
use MyParcelCom\Sdk\Resources\Interfaces\ResourceInterface;
use MyParcelCom\Sdk\Resources\Interfaces\ServiceInterface;
use MyParcelCom\Sdk\Resources\Interfaces\ShipmentInterface;
use MyParcelCom\Sdk\Resources\Interfaces\ShopInterface;
use MyParcelCom\Sdk\Resources\Shipment;
use PHPUnit\Framework\TestCase;
use function GuzzleHttp\Promise\promise_for;

class MyParcelComApiTest extends TestCase
{
    /** @var AuthenticatorInterface */
    private $authenticator;
    /** @var MyParcelComApi */
    private $api;
    /** @var ClientInterface */
    private $client;

    public function setUp()
    {
        $this->authenticator = $this->getMockBuilder(AuthenticatorInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        $this->authenticator->method('getAuthorizationHeader')
            ->willReturn(['Authorization' => 'Bearer test-api-token']);

        $this->client = $this->getMockBuilder(ClientInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $this->client
            ->method('requestAsync')
            ->willReturnCallback(function ($method, $uri, $options) {
                $filePath = implode(DIRECTORY_SEPARATOR, [
                        dirname(dirname(__FILE__)),
                        'Stubs',
                        $method,
                        str_replace([':', '{', '}', '(', ')', '/', '\\', '@', '?', '[', ']', '=', '&'], '-', $uri),
                    ]) . '.json';

                if (!file_exists($filePath)) {
                    throw new \RuntimeException(sprintf(
                        'File with path `%s` does not exist, please create this file with valid response data',
                        $filePath
                    ));
                }

                $returnJson = file_get_contents($filePath);
                if ($method === 'post') {
                    // Any post will have the data from the stub added to the
                    // original request. This simulates the api creating the
                    // resource and returning it with added attributes.
                    $returnJson = \GuzzleHttp\json_encode(
                        array_merge_recursive(
                            \GuzzleHttp\json_decode($returnJson, true),
                            // You may wonder why we would encode and then
                            // decode this, but it is possible that the json in
                            // the options array is not an associative array,
                            // which we need to be able to merge.
                            \GuzzleHttp\json_decode(\GuzzleHttp\json_encode($options['json']), true)
                        )
                    );
                }

                $response = new Response(200, [], $returnJson);

                return promise_for($response);
            });

        $this->api = (new MyParcelComApi('https://api'))
            ->setHttpClient($this->client)
            ->authenticate($this->authenticator);
    }

    /** @test */
    public function testSingleton()
    {
        $this->assertNull(MyParcelComApi::getSingleton());

        $api = MyParcelComApi::createSingleton($this->authenticator);
        $this->assertInstanceOf(MyParcelComApi::class, $api);
        $this->assertEquals($api, MyParcelComApi::getSingleton());
    }

    /** @test */
    public function testAuthenticate()
    {
        $api = new MyParcelComApi();

        $this->assertEquals(
            $api,
            $api->authenticate($this->authenticator),
            'Api should return itself and not throw an error when a functioning authenticator is used'
        );
    }

    /** @test */
    public function testCreateMinimumViableShipment()
    {
        $recipient = (new Address())
            ->setFirstName('Bobby')
            ->setLastName('Tables')
            ->setCity('Birmingham')
            ->setStreet1('Newbourne Hill')
            ->setStreetNumber(12)
            ->setPostalCode('B48 7QN')
            ->setCountryCode('GB');

        // Minimum required data should be a recipient address and weight. All
        // other data should be filled with defaults.
        $shipment = (new Shipment())
            ->setWeight(500)
            ->setRecipientAddress($recipient);

        $shipment = $this->api->createShipment($shipment);

        $this->assertNotNull(
            $shipment->getService(),
            'When no service has been selected, the preferred service for given shipment should be used'
        );
        $this->assertNotNull(
            $shipment->getId(),
            'Once the shipment has been created, it should have an id'
        );
        $this->assertNotNull(
            $shipment->getPrice(),
            'Successfully created shipments should have a price'
        );
        $this->assertEquals(
            $this->api->getDefaultShop()->getReturnAddress(),
            $shipment->getSenderAddress(),
            'The shipment\'s sender address should default to the default shop\'s return address'
        );
        $this->assertEquals(
            $recipient,
            $shipment->getRecipientAddress(),
            'The shipment\'s recipient address should not have changed'
        );
    }

    /** @test */
    public function testCreateInvalidShipment()
    {
        $shipment = new Shipment();

        // Shipments with no recipient and weight, should cause the api to throw
        // an exception.
        $this->expectException(InvalidResourceException::class);
        $this->api->createShipment($shipment);
    }

    /** @test */
    public function testGetCarriers()
    {
        $carriers = $this->api->getCarriers();

        $this->assertInternalType('array', $carriers);
        array_walk($carriers, function ($carrier) {
            $this->assertInstanceOf(CarrierInterface::class, $carrier);
        });
    }

    /** @test */
    public function testGetPickUpDropOffLocations()
    {
        $pudoLocations = $this->api->getPickUpDropOffLocations('GB', 'B48 7QN');

        $this->assertInternalType('array', $pudoLocations);
        array_walk($pudoLocations, function ($pudoLocation) {
            $this->assertInstanceOf(PickUpDropOffLocationInterface::class, $pudoLocation);
        });
    }

    /** @test */
    public function testGetRegions()
    {
        $regions = $this->api->getRegions();

        $this->assertInternalType('array', $regions);
        $this->assertCount(78, $regions);
        array_walk($regions, function ($region) {
            $this->assertInstanceOf(RegionInterface::class, $region);
        });
    }

    /** @test */
    public function testGetGbRegions()
    {
        $regions = $this->api->getRegions('GB');

        $this->assertInternalType('array', $regions);
        $this->assertCount(5, $regions);
        array_walk($regions, function ($region) {
            $this->assertInstanceOf(RegionInterface::class, $region);
            $this->assertEquals('GB', $region->getCountryCode());
        });

        $ireland = $this->api->getRegions('GB', 'NIR');

        $this->assertInternalType('array', $ireland);
        $this->assertCount(1, $ireland);
        array_walk($ireland, function ($region) {
            $this->assertInstanceOf(RegionInterface::class, $region);
            $this->assertEquals('GB', $region->getCountryCode());
            $this->assertEquals('NIR', $region->getRegionCode());
        });
    }

    /** @test */
    public function testGetServices()
    {
        $services = $this->api->getServices();

        $this->assertInternalType('array', $services);
        array_walk($services, function ($service) {
            $this->assertInstanceOf(ServiceInterface::class, $service);
        });
    }

    /** @test */
    public function testGetServicesForShipment()
    {
        $recipient = (new Address())
            ->setFirstName('Bobby')
            ->setLastName('Tables')
            ->setCity('Birmingham')
            ->setStreet1('Newbourne Hill')
            ->setStreetNumber(12)
            ->setPostalCode('B48 7QN')
            ->setCountryCode('GB');
        $shipment = (new Shipment())
            ->setWeight(500)
            ->setRecipientAddress($recipient);

        $services = $this->api->getServices($shipment);

        $this->assertInternalType('array', $services);
        array_walk($services, function ($service) {
            $this->assertInstanceOf(ServiceInterface::class, $service);
        });
    }

    /** @test */
    public function testGetServicesForCarrier()
    {
        $carriers = $this->api->getCarriers();

        array_walk($carriers, function ($carrier) {
            $services = $this->api->getServicesForCarrier($carrier);

            array_walk($services, function ($service) use ($carrier) {
                $this->assertInstanceOf(ServiceInterface::class, $service);
                $this->assertEquals($carrier->getId(), $service->getCarrier()->getId());
            });
        });
    }

    /** @test */
    public function testGetShipments()
    {
        $shipments = $this->api->getShipments();

        $this->assertInternalType('array', $shipments);
        array_walk($shipments, function ($shipment) {
            $this->assertInstanceOf(ShipmentInterface::class, $shipment);
        });
    }

    /** @test */
    public function testGetShipmentsForShop()
    {
        $shops = $this->api->getShops();

        array_walk($shops, function ($shop) {
            $shipments = $this->api->getShipments($shop);

            $this->assertInternalType('array', $shipments);
            array_walk($shipments, function ($shipment) use ($shop) {
                $this->assertInstanceOf(ShipmentInterface::class, $shipment);
                $this->assertEquals($shop->getId(), $shipment->getShop()->getId());
                $this->assertEquals($shop->getType(), $shipment->getShop()->getType());
                $this->assertEquals($shop->getCreatedAt(), $shipment->getShop()->getCreatedAt());
                $this->assertEquals($shop->getBillingAddress(), $shipment->getShop()->getBillingAddress());
                $this->assertEquals($shop->getReturnAddress(), $shipment->getShop()->getReturnAddress());
                $this->assertEquals($shop->getName(), $shipment->getShop()->getName());
                $this->assertEquals($shop->getRegion(), $shipment->getShop()->getRegion());
            });
        });
    }

    /** @test */
    public function testGetShipment()
    {
        $shipments = $this->api->getShipments();

        array_walk($shipments, function ($shipment) {
            $this->assertEquals($shipment, $this->api->getShipment($shipment->getId()));
        });
    }

    /** @test */
    public function testGetShops()
    {
        $shops = $this->api->getShops();

        $this->assertInternalType('array', $shops);
        array_walk($shops, function ($shop) {
            $this->assertInstanceOf(ShopInterface::class, $shop);
        });
    }

    /** @test */
    public function testGetDefaultShop()
    {
        $shop = $this->api->getDefaultShop();
        $this->assertInstanceOf(ShopInterface::class, $shop);
    }

    /** @test */
    public function testGetResourceById()
    {
        /** @var FileInterface $file */
        $file = $this->api->getResourceById(ResourceInterface::TYPE_FILE, 'file-id-1');
        $this->assertInstanceOf(FileInterface::class, $file);
        $this->assertEquals('files', $file->getType());
        $this->assertEquals('file-id-1', $file->getId());
        $this->assertEquals('label', $file->getResourceType());
        $this->assertEquals([['extension' => 'pdf', 'mime_type' => 'application/pdf']], $file->getFormats());

        /** @var ShopInterface $shop */
        $shop = $this->api->getResourceById(ResourceInterface::TYPE_SHOP, 'shop-id-1');
        $this->assertInstanceOf(ShopInterface::class, $shop);
        $this->assertEquals('shops', $shop->getType());
        $this->assertEquals('shop-id-1', $shop->getId());
        $this->assertEquals('Testshop', $shop->getName());
        $this->assertEquals((new \DateTime())->setTimestamp(1509378904), $shop->getCreatedAt());
        $this->assertEquals(
            (new Address())
                ->setStreet1('Hoofdweg')
                ->setStreetNumber(679)
                ->setPostalCode('1AA BB2')
                ->setCity('London')
                ->setCountryCode('GB')
                ->setFirstName('Mister')
                ->setLastName('Billing')
                ->setCompany('MyParcel.com')
                ->setEmail('info@myparcel.com')
                ->setPhoneNumber('+31 85 208 5997'),
            $shop->getBillingAddress()
        );
        $this->assertEquals(
            (new Address())
                ->setStreet1('Hoofdweg')
                ->setStreetNumber(679)
                ->setPostalCode('1AA BB2')
                ->setCity('London')
                ->setCountryCode('GB')
                ->setFirstName('Mister')
                ->setLastName('Return')
                ->setCompany('MyParcel.com')
                ->setEmail('info@myparcel.com')
                ->setPhoneNumber('+31 85 208 5997'),
            $shop->getReturnAddress()
        );
    }
}
