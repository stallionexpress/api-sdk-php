<?php

namespace MyParcelCom\ApiSdk\Tests\Feature\Proxy;

use GuzzleHttp\ClientInterface;
use MyParcelCom\ApiSdk\Authentication\AuthenticatorInterface;
use MyParcelCom\ApiSdk\MyParcelComApi;
use MyParcelCom\ApiSdk\MyParcelComApiInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceInterface;
use MyParcelCom\ApiSdk\Resources\Proxy\RegionProxy;
use MyParcelCom\ApiSdk\Tests\Traits\MocksApiCommunication;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Simple\NullCache;

class RegionProxyTest extends TestCase
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
        $regionProxy = new RegionProxy();
        $regionProxy
            ->setMyParcelComApi($this->api)
            ->setId('c1048135-db45-404e-adac-fdecd0c7134a');

        $this->assertEquals('c1048135-db45-404e-adac-fdecd0c7134a', $regionProxy->getId());
        $this->assertEquals(ResourceInterface::TYPE_REGION, $regionProxy->getType());
        $this->assertEquals('GB', $regionProxy->getCountryCode());
        $this->assertEquals('ENG', $regionProxy->getRegionCode());
        $this->assertEquals('GBP', $regionProxy->setCurrency('GBP')->getCurrency());
        $this->assertEquals('United Kingdom', $regionProxy->getName());

        // Check if the uri has been called only once.
        // Creating a new proxy for the same resource will
        // change the amount of calls to 2.
        $this->assertEquals(1, $this->clientCalls['https://api/v1/regions/c1048135-db45-404e-adac-fdecd0c7134a']);

        $newProxy = new RegionProxy();
        $newProxy
            ->setMyParcelComApi($this->api)
            ->setId('c1048135-db45-404e-adac-fdecd0c7134a');
        $newProxy->getName();

        $this->assertEquals(2, $this->clientCalls['https://api/v1/files/c1048135-db45-404e-adac-fdecd0c7134a']);
    }
}
