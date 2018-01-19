<?php

namespace MyParcelCom\ApiSdk\Tests\Feature\Proxy;

use GuzzleHttp\ClientInterface;
use MyParcelCom\ApiSdk\Authentication\AuthenticatorInterface;
use MyParcelCom\ApiSdk\MyParcelComApi;
use MyParcelCom\ApiSdk\MyParcelComApiInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\CarrierInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\RegionInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceInterface;
use MyParcelCom\ApiSdk\Resources\Proxy\ServiceProxy;
use MyParcelCom\ApiSdk\Tests\Traits\MocksApiCommunication;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Simple\NullCache;

class ServiceProxyTest extends TestCase
{
    use MocksApiCommunication;

    /** @var ClientInterface */
    private $client;
    /** @var AuthenticatorInterface */
    private $authenticator;
    /** @var MyParcelComApiInterface */
    private $api;
    /** @var ServiceProxy */
    private $serviceProxy;

    public function setUp()
    {
        parent::setUp();

        $this->client = $this->getClientMock();
        $this->authenticator = $this->getAuthenticatorMock();
        $this->api = (new MyParcelComApi('https://api'))
            ->setCache(new NullCache())
            ->setHttpClient($this->client)
            ->authenticate($this->authenticator);

        $this->serviceProxy = new ServiceProxy();
        $this->serviceProxy
            ->setMyParcelComApi($this->api)
            ->setId('433285bb-2e34-435c-9109-1120e7c4bce4');
    }

    /** @test */
    public function testAttributes()
    {
        $this->assertEquals('433285bb-2e34-435c-9109-1120e7c4bce4', $this->serviceProxy->getId());
        $this->assertEquals(ResourceInterface::TYPE_SERVICE, $this->serviceProxy->getType());
        $this->assertEquals('Letterbox Test', $this->serviceProxy->getName());
        $this->assertEquals('letterbox', $this->serviceProxy->getPackageType());
        $this->assertEquals(4, $this->serviceProxy->setTransitTimeMin(4)->getTransitTimeMin());
        $this->assertEquals(3, $this->serviceProxy->getTransitTimeMax());
    }

    /** @test */
    public function testCarrierRelationship()
    {
        $carrier = $this->serviceProxy->getCarrier();
        $this->assertInstanceOf(CarrierInterface::class, $carrier);
        $this->assertEquals('eef00b32-177e-43d3-9b26-715365e4ce46', $carrier->getId());
        $this->assertEquals(ResourceInterface::TYPE_CARRIER, $carrier->getType());
    }

    /** @test */
    public function testRegionFromRelationship()
    {
        $regionFrom = $this->serviceProxy->getRegionFrom();
        $this->assertInstanceOf(RegionInterface::class, $regionFrom);
        $this->assertEquals('c1048135-db45-404e-adac-fdecd0c7134a', $regionFrom->getId());
        $this->assertEquals(ResourceInterface::TYPE_REGION, $regionFrom->getType());
    }

    /** @test */
    public function testRegionToRelationship()
    {
        $regionTo = $this->serviceProxy->getRegionTo();
        $this->assertInstanceOf(RegionInterface::class, $regionTo);
        $this->assertEquals('c1048135-db45-404e-adac-fdecd0c7134a', $regionTo->getId());
        $this->assertEquals(ResourceInterface::TYPE_REGION, $regionTo->getType());
    }
}