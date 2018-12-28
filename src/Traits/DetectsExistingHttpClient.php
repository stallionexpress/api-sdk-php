<?php

namespace MyParcelCom\ApiSdk\Traits;

use MyParcelCom\ApiSdk\Exceptions\MissingHttpClientAdapterException;
use MyParcelCom\ApiSdk\Http\Contracts\HttpClient\ClientInterface;

trait DetectsExistingHttpClient
{
    /**
     * @return ClientInterface
     * @throws MissingHttpClientAdapterException
     */
    private function detectExistingHttpClient()
    {
        if (class_exists('GuzzleHttp\Client')) {
            // The BatchResults class was removed in Guzzle 6, so
            // we assume we're at Guzzle 5
            if (class_exists('GuzzleHttp\BatchResults')) {
                // Return guzzle 5 adapter
                if (!class_exists('Http\Adapter\Guzzle5\Client')) {
                    throw new MissingHttpClientAdapterException('Missing cURL client package. Run `composer require php-http/guzzle5-adapter`.');
                }

                return new Http\Adapter\Guzzle5\Client();
            }

            // Otherwise return guzzle 6 adapter
            if (!class_exists('Http\Adapter\Guzzle6\Client')) {
                throw new MissingHttpClientAdapterException('Missing cURL client package. Run `composer require php-http/guzzle6-adapter`.');
            }

            return new Http\Adapter\Guzzle6\Client();
        }

        if (function_exists('curl_version')) {
            // Return Curl client
            if (!class_exists('Http\Client\Curl\Client')) {
                throw new MissingHttpClientAdapterException('Missing cURL client package. Run `composer require php-http/curl-client`.');
            }

            return new Http\Client\Curl\Client();
        }
    }
}