<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Tests\Traits;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use MyParcelCom\ApiSdk\Authentication\AuthenticatorInterface;
use MyParcelCom\ApiSdk\Http\Exceptions\RequestException;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestInterface;
use Symfony\Component\Cache\Adapter\NullAdapter;
use Symfony\Component\Cache\Psr16Cache;

trait MocksApiCommunication
{
    protected array $clientCalls = [];

    protected function getNullCache(): Psr16Cache
    {
        $psr6Cache = new NullAdapter();

        return new Psr16Cache($psr6Cache);
    }

    protected function getClientMock()
    {
        $client = $this->getMockBuilder(ClientInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $client
            ->method('sendRequest')
            ->willReturnCallback(function (RequestInterface $request) {
                $method = strtolower($request->getMethod());
                $uri = urldecode((string) $request->getUri());

                $filePath = implode(DIRECTORY_SEPARATOR, [
                        dirname(__FILE__, 2),
                        'Stubs',
                        $method,
                        str_replace(
                            ['?', '&'],
                            DIRECTORY_SEPARATOR,
                            str_replace(
                                [':', '{', '}', '(', ')', '/', '\\', '@', '[', ']', '='],
                                '-',
                                $uri
                            )
                        ),
                    ]) . '.json';

                $filePath = urldecode($filePath);

                if (str_contains($filePath, 'stream')) {
                    $filePath = preg_replace('/.json/', '.txt', $filePath);
                }

                if (!file_exists($filePath)) {
                    throw new \RuntimeException(sprintf(
                        'File with path `%s` does not exist, please create this file with valid response data',
                        $filePath
                    ));
                }

                $returnJson = file_get_contents($filePath);

                if (str_contains($returnJson, '"errors": [') && !str_contains($returnJson, '"data": ')) {
                    throw new RequestException(
                        new Request($method, $uri),
                        new Response(500, [], $returnJson)
                    );
                }

                $response = new Response(200, [], $returnJson);

                if (!isset($this->clientCalls[$uri])) {
                    $this->clientCalls[$uri] = 0;
                }
                $this->clientCalls[$uri]++;

                return $response;
            });

        return $client;
    }

    protected function getAuthenticatorMock()
    {
        $authenticator = $this->getMockBuilder(AuthenticatorInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        $authenticator->method('getAuthorizationHeader')
            ->willReturn(['Authorization' => 'Bearer test-api-token']);

        return $authenticator;
    }
}
