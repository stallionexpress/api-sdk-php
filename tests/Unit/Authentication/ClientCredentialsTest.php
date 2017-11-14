<?php

namespace MyParcelCom\Sdk\Tests\Unit\Authentication;

use GuzzleHttp\ClientInterface;
use MyParcelCom\Sdk\Authentication\ClientCredentials;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use function GuzzleHttp\Promise\promise_for;

class ClientCredentialsTest extends TestCase
{
    /** @test */
    public function testGetAuthorizationHeader()
    {
        // Mock a response from the http client.
        $response = $this->getMockBuilder(ResponseInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $response->method('getBody')
            ->willReturn(json_encode([
                'token_type'   => 'Bearer',
                'expires_in'   => 86400,
                'access_token' => 'an-access-token-for-the-myparcelcom-api',
            ]));

        // Mock an http client.
        $httpClient = $this->getMockBuilder(ClientInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        // Check that if an async request is done, that the correct values are used.
        $httpClient->method('requestAsync')
            ->willReturnCallback(function ($method, $path, $options) use ($response) {
                $this->assertEquals(
                    'POST',
                    strtoupper($method),
                    'Post method should be used when trying to request an access token'
                );
                $this->assertEquals(
                    'https://auth.myparcel.com/access-token',
                    strtolower($path),
                    'Requested path should match set uri followed by `/access-token`'
                );

                $expectedJson = [
                    'grant_type'    => 'client_credentials',
                    'client_id'     => 'client-id',
                    'client_secret' => 'shhh-dont-tell-anyone',
                    'scope'         => ClientCredentials::SCOPES,
                ];
                if (isset($options['body'])) {
                    $this->assertEquals(
                        json_encode($expectedJson),
                        $options['body'],
                        'Request body did not contain required json fields'
                    );
                } elseif (isset($options['json'])) {
                    $this->assertEquals(
                        $expectedJson,
                        $options['json'],
                        'Request body did not contain required json fields'
                    );
                }

                return promise_for($response);
            });
        // Check that if a normal request is done, that the correct values are used
        $httpClient->method('request')
            ->willReturnCallback(function ($method, $path, $options) use ($response) {
                $this->assertEquals(
                    'POST',
                    strtoupper($method),
                    'Post method should be used when trying to request an access token'
                );
                $this->assertEquals(
                    'https://auth.myparcel.com/access-token',
                    strtolower($path),
                    'Requested path should match set uri followed by `/access-token`'
                );

                $expectedJson = [
                    'grant_type'    => 'client_credentials',
                    'client_id'     => 'client-id',
                    'client_secret' => 'shhh-dont-tell-anyone',
                    'scope'         => ClientCredentials::SCOPES,
                ];
                if (isset($options['body'])) {
                    $this->assertEquals(
                        json_encode($expectedJson),
                        $options['body'],
                        'Request body did not contain required json fields'
                    );
                } elseif (isset($options['json'])) {
                    $this->assertEquals(
                        $expectedJson,
                        $options['json'],
                        'Request body did not contain required json fields'
                    );
                }

                return $response;
            });

        $clientCredentials = (new ClientCredentials(
            'client-id',
            'shhh-dont-tell-anyone',
            'https://auth.myparcel.com'
        ))->setHttpClient($httpClient);

        $this->assertEquals(
            [
                'Authorization' => 'Bearer an-access-token-for-the-myparcelcom-api',
            ],
            $clientCredentials->getAuthorizationHeader()
        );
    }
}
