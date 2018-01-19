<?php

namespace MyParcelCom\ApiSdk\Tests\Feature\Proxy;

use GuzzleHttp\ClientInterface;
use MyParcelCom\ApiSdk\Authentication\AuthenticatorInterface;
use MyParcelCom\ApiSdk\MyParcelComApi;
use MyParcelCom\ApiSdk\MyParcelComApiInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceInterface;
use MyParcelCom\ApiSdk\Resources\Proxy\StatusProxy;
use MyParcelCom\ApiSdk\Tests\Traits\MocksApiCommunication;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Simple\NullCache;

class StatusProxyTest extends TestCase
{
    use MocksApiCommunication;

    /** @var ClientInterface */
    private $client;
    /** @var AuthenticatorInterface */
    private $authenticator;
    /** @var MyParcelComApiInterface */
    private $api;
    /** @var StatusProxy */
    private $statusProxy;

    public function setUp()
    {
        parent::setUp();

        $this->client = $this->getClientMock();
        $this->authenticator = $this->getAuthenticatorMock();
        $this->api = (new MyParcelComApi('https://api'))
            ->setCache(new NullCache())
            ->setHttpClient($this->client)
            ->authenticate($this->authenticator);

        $this->statusProxy = new StatusProxy();
        $this->statusProxy
            ->setMyParcelComApi($this->api)
            ->setId('status-id-1');
    }

    /** @test */
    public function testAttributes()
    {
        $this->assertEquals('status-id-1', $this->statusProxy->getId());
        $this->assertEquals(ResourceInterface::TYPE_STATUS, $this->statusProxy->getType());
        $this->assertEquals('shipment_delivered', $this->statusProxy->getCode());
        $this->assertEquals('success', $this->statusProxy->getLevel());
        $this->assertEquals('Delivered', $this->statusProxy->getName());
        $this->assertEquals('The shipment has been delivered', $this->statusProxy->getDescription());
    }

    /** @test */
    public function testCarrierStatusCodeMeta()
    {
        $carrierStatusCode = $this->statusProxy->getCarrierStatusCode();
        var_dump($carrierStatusCode);
    }
}
