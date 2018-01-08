<?php

namespace MyParcelCom\ApiSdk;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\RequestOptions;
use MyParcelCom\ApiSdk\Authentication\AuthenticatorInterface;
use MyParcelCom\ApiSdk\Exceptions\InvalidResourceException;
use MyParcelCom\ApiSdk\Resources\Interfaces\CarrierInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\RegionInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceFactoryInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceProxyInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShipmentInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShopInterface;
use MyParcelCom\ApiSdk\Resources\ResourceFactory;
use MyParcelCom\ApiSdk\Shipments\ContractSelector;
use MyParcelCom\ApiSdk\Shipments\PriceCalculator;
use MyParcelCom\ApiSdk\Shipments\ServiceMatcher;
use MyParcelCom\ApiSdk\Utils\UrlBuilder;
use MyParcelCom\ApiSdk\Validators\ShipmentValidator;
use Psr\Http\Message\ResponseInterface;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Cache\Simple\FilesystemCache;
use function GuzzleHttp\Promise\promise_for;
use function GuzzleHttp\Promise\unwrap;
use function GuzzleHttp\Psr7\parse_response;
use function GuzzleHttp\Psr7\str;

class MyParcelComApi implements MyParcelComApiInterface
{
    const API_VERSION = 'v1';

    /** @var string */
    protected $apiUri;
    /** @var CacheInterface */
    protected $cache;
    /** @var ResourceFactoryInterface */
    protected $resourceFactory;
    /** @var AuthenticatorInterface */
    protected $authenticator;

    /** @var ClientInterface */
    private $client;
    /** @var bool */
    private $authRetry = false;

    /** @var MyParcelComApi */
    private static $singleton;

    /**
     * Create an singleton instance of this class, which will be available in
     * subsequent calls to `getSingleton()`.
     *
     * @param AuthenticatorInterface        $authenticator
     * @param string                        $apiUri
     * @param CacheInterface|null           $cache
     * @param ResourceFactoryInterface|null $resourceFactory
     * @return MyParcelComApi
     */
    public static function createSingleton(
        AuthenticatorInterface $authenticator,
        $apiUri = 'https://sandbox-api.myparcel.com',
        CacheInterface $cache = null,
        ResourceFactoryInterface $resourceFactory = null
    ) {
        return self::$singleton = (new self($apiUri, $cache, $resourceFactory))
            ->authenticate($authenticator);
    }

    /**
     * Get the singleton instance created.
     *
     * @return MyParcelComApi
     */
    public static function getSingleton()
    {
        return self::$singleton;
    }

    /**
     * Create an instance for the api with given uri. If no cache is given, the
     * filesystem is used for caching. If no resource factory is given, the
     * default factory is used.
     *
     * @param string                        $apiUri
     * @param CacheInterface|null           $cache
     * @param ResourceFactoryInterface|null $resourceFactory
     */
    public function __construct(
        $apiUri = 'https://sandbox-api.myparcel.com',
        CacheInterface $cache = null,
        ResourceFactoryInterface $resourceFactory = null
    ) {
        $this->setApiUri($apiUri);

        // Either use the given cache or instantiate a new one that uses the
        // filesystem as cache. By default the `FilesystemCache` will use the
        // system temp directory as a cache.
        $this->setCache($cache ?: new FilesystemCache('myparcelcom'));

        // Either use the given resource factory or instantiate a new one.
        $this->setResourceFactory($resourceFactory ?: new ResourceFactory());
    }

    /**
     * {@inheritdoc}
     */
    public function authenticate(AuthenticatorInterface $authenticator)
    {
        $this->authenticator = $authenticator;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRegions($countryCode = null, $regionCode = null)
    {
        // These resources can be stored for a week.
        $regions = $this->getResourcesPromise($this->apiUri . self::PATH_REGIONS, self::TTL_WEEK)
            ->wait();

        // For now, we need to manually filter the regions.
        return array_values(array_filter($regions, function (RegionInterface $region) use ($countryCode, $regionCode) {
            return ($countryCode === null || $countryCode === $region->getCountryCode())
                && ($regionCode === null || $regionCode === $region->getRegionCode());
        }));
    }

    /**
     * {@inheritdoc}
     */
    public function getCarriers()
    {
        // These resources can be stored for a week.
        return $this->getResourcesPromise($this->apiUri . self::PATH_CARRIERS, self::TTL_WEEK)
            ->wait();
    }

    /**
     * {@inheritdoc}
     */
    public function getPickUpDropOffLocations(
        $countryCode,
        $postalCode,
        $streetName = null,
        $streetNumber = null,
        CarrierInterface $specificCarrier = null
    ) {
        $carriers = $specificCarrier ? [$specificCarrier] : $this->getCarriers();

        $uri = $this->apiUri
            . str_replace(
                [
                    '{country_code}',
                    '{postal_code}',
                ],
                [
                    $countryCode,
                    $postalCode,
                ],
                self::PATH_PUDO_LOCATIONS
            );

        if ($streetName || $streetNumber) {
            $uri .= '?';
        }
        if ($streetName) {
            $uri .= 'street=' . $streetName;
        }
        if ($streetNumber) {
            $uri .= 'street_number=' . $streetNumber;
        }

        $promises = [];

        foreach ($carriers as $carrier) {
            $carrierUri = str_replace('{carrier_id}', $carrier->getId(), $uri);

            // These resources can be stored for a week.
            $promise = $this->getResourcesPromise($carrierUri, self::TTL_WEEK);
            if ($specificCarrier) {
                return $promise->wait();
            }
            // When something fails while retrieving the locations
            // for a carrier, the locations of the other carriers should
            // still be returned. The failing carrier returns null.
            $promises[$carrier->getId()] = $promise->otherwise(function () {
                return null;
            });
        };

        return unwrap($promises);
    }

    /**
     * {@inheritdoc}
     */
    public function getShops()
    {
        // These resources can be stored for a week. Or should be removed from
        // cache when updated
        return $this->getResourcesPromise($this->apiUri . self::PATH_SHOPS, self::TTL_WEEK)
            ->wait();
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultShop()
    {
        $shops = $this->getShops();

        // For now the oldest shop shop will be the default shop.
        usort($shops, function (ShopInterface $shopA, ShopInterface $shopB) {
            return $shopA->getCreatedAt()->getTimestamp() - $shopB->getCreatedAt()->getTimestamp();
        });

        return reset($shops);
    }

    /**
     * {@inheritdoc}
     */
    public function getServices(ShipmentInterface $shipment = null)
    {
        $url = new UrlBuilder($this->apiUri . self::PATH_SERVICES);

        if ($shipment === null) {
            return $this->getResourcesPromise($url->getUrl(), self::TTL_WEEK)
                ->wait();
        }

        if ($shipment->getSenderAddress() === null) {
            $shipment->setSenderAddress($this->getDefaultShop()->getReturnAddress());
        }

        if ($shipment->getRecipientAddress() === null) {
            throw new InvalidResourceException(
                'Missing `recipient_address` on `shipments` resource'
            );
        }
        if ($shipment->getSenderAddress() === null) {
            throw new InvalidResourceException(
                'Missing `sender_address` on `shipments` resource'
            );
        }

        $regionsFrom = $this->getRegions(
            $shipment->getSenderAddress()->getCountryCode(),
            $shipment->getSenderAddress()->getRegionCode()
        );
        $regionsTo = $this->getRegions(
            $shipment->getSenderAddress()->getCountryCode(),
            $shipment->getSenderAddress()->getRegionCode()
        );

        $url->addQuery([
            'filter[region_from]' => reset($regionsFrom)->getId(),
            'filter[region_to]'   => reset($regionsTo)->getId(),
        ]);

        // Services can be cached for a week.
        $services = $this->getResourcesPromise($url->getUrl(), self::TTL_WEEK)
            ->wait();

        $matcher = new ServiceMatcher();
        $services = array_values(array_filter($services, function (ServiceInterface $service) use ($shipment, $matcher) {
            return $matcher->matches($shipment, $service);
        }));

        return $services;
    }

    /**
     * {@inheritdoc}
     */
    public function getServicesForCarrier(CarrierInterface $carrier)
    {
        // For now, we need to manually filter the services
        return array_values(array_filter($this->getServices(), function (ServiceInterface $service) use ($carrier) {
            return $service->getCarrier()->getId() === $carrier->getId();
        }));
    }

    /**
     * {@inheritdoc}
     */
    public function getShipments(ShopInterface $shop = null)
    {
        $shipments = $this->getResourcesPromise($this->apiUri . self::PATH_SHIPMENTS)->wait();

        if ($shop === null) {
            return $shipments;
        }

        // For now filter manually.
        return array_values(array_filter($shipments, function (ShipmentInterface $shipment) use ($shop) {
            return $shipment->getShop()->getId() === $shop->getId();
        }));
    }

    /**
     * {@inheritdoc}
     */
    public function getShipment($id)
    {
        return $this->getResourceById(ResourceInterface::TYPE_SHIPMENT, $id);
    }

    /**
     * {@inheritdoc}
     */
    public function createShipment(ShipmentInterface $shipment)
    {
        // If no shop is set, use the default shop.
        if ($shipment->getShop() === null) {
            $shipment->setShop($this->getDefaultShop());
        }

        // If no sender address has been set, default to the shop's return
        // address.
        if ($shipment->getSenderAddress() === null) {
            $shipment->setSenderAddress(
                $shipment->getShop()->getReturnAddress()
            );
        }

        // If no contract is set, select the cheapest one.
        if ($shipment->getContract() === null) {
            $this->determineContract($shipment);
        }

        $validator = new ShipmentValidator($shipment);

        if (!$validator->isValid()) {
            $exception = new InvalidResourceException(
                'Could not create shipment, shipment was invalid or incomplete'
            );
            $exception->setErrors($validator->getErrors());

            throw $exception;
        }

        return $this->postResource($shipment);
    }

    /**
     * Determine what contract (and service) to use for given shipment and
     * update the shipment.
     *
     * @param ShipmentInterface $shipment
     * @return $this
     */
    protected function determineContract(ShipmentInterface $shipment)
    {
        if ($shipment->getService() !== null) {
            $shipment->setContract((new ContractSelector())->selectCheapest(
                $shipment,
                $shipment->getService()->getContracts()
            ));

            return $this;
        }
        $selector = new ContractSelector();
        $calculator = new PriceCalculator();
        $contracts = array_map(
            function (ServiceInterface $service) use ($selector, $calculator, $shipment) {
                $contract = $selector->selectCheapest($shipment, $service->getContracts());

                return [
                    'price'    => $calculator->calculate($shipment, $contract),
                    'contract' => $contract,
                    'service'  => $service,
                ];
            },
            $this->getServices($shipment)
        );

        usort($contracts, function ($a, $b) {
            return $a['price'] - $b['price'];
        });

        $cheapest = reset($contracts);
        $shipment->setContract($cheapest['contract'])
            ->setService($cheapest['service']);

        return $this;
    }

    /**
     * Set the URI of the MyParcel.com API.
     *
     * @param string $apiUri
     * @return $this
     */
    public function setApiUri($apiUri)
    {
        // Remove trailing whitespace and a trailing slash.
        $this->apiUri = rtrim($apiUri, " \t\n\r\0\x0B/");

        return $this;
    }

    /**
     * Set the factory to use when creating resources.
     *
     * @param ResourceFactoryInterface $resourceFactory
     * @return $this
     */
    public function setResourceFactory(ResourceFactoryInterface $resourceFactory)
    {
        // Let this fetch the resources if the factory allows proxying of resources.
        if ($resourceFactory instanceof ResourceProxyInterface) {
            $resourceFactory->setMyParcelComApi($this);
        }

        $this->resourceFactory = $resourceFactory;

        return $this;
    }

    /**
     * Set the cache which will be used to store resources.
     *
     * @param CacheInterface $cache
     * @return $this
     */
    public function setCache(CacheInterface $cache)
    {
        $this->cache = $cache;

        return $this;
    }

    /**
     * Clear the cached resources and the authorization cache.
     *
     * @return $this
     */
    public function clearCache()
    {
        $this->cache->clear();
        $this->authenticator->clearCache();

        return $this;
    }

    /**
     * Set the Guzzle client to use to connect to the api.
     *
     * @param ClientInterface $client
     * @return $this
     */
    public function setHttpClient(ClientInterface $client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * Get the guzzle client.
     *
     * @return ClientInterface
     */
    protected function getHttpClient()
    {
        if (!isset($this->client)) {
            $this->client = new Client([
                'base_uri'              => $this->apiUri,
                RequestOptions::HEADERS => [
                    'Content-Type' => 'application/vnd.api+json',
                ],
            ]);
        }

        return $this->client;
    }

    /**
     * Get a promise that will return an array with resources requested from
     * given uri. A time-to-live can be specified for how long this request
     * should be cached (defaults to 10 minutes).
     *
     * @param string $uri
     * @param int    $ttl
     * @return PromiseInterface
     * @internal param string $path
     */
    protected function getResourcesPromise($uri, $ttl = self::TTL_10MIN)
    {
        return $this->doRequest($uri, 'get', [], [], $ttl)
            ->then(function (ResponseInterface $response) {
                $json = \GuzzleHttp\json_decode((string)$response->getBody(), true);

                $resources = $this->jsonToResources($json['data']);

                // If there is no next link, we don't have to retrieve any more data
                if (!isset($json['links']['next'])) {
                    return $resources;
                }

                return $this->getResourcesPromise($json['links']['next'])
                    ->then(function ($nextResources) use ($resources) {
                        return array_merge($resources, $nextResources);
                    });
            });
    }

    /**
     * {@inheritdoc}
     */
    public function doRequest($uri, $method = 'get', array $body = [], array $headers = [], $ttl = self::TTL_10MIN)
    {
        if (strpos($uri, $this->apiUri) !== 0) {
            $uri = $this->apiUri . $uri;
        }
        $headers += $this->authenticator->getAuthorizationHeader() + [
                AuthenticatorInterface::HEADER_ACCEPT => AuthenticatorInterface::MIME_TYPE_JSONAPI,
            ];

        $cacheKey = sha1($method . join($headers) . $uri);

        if (($response = $this->cache->get($cacheKey))) {
            return promise_for(parse_response($response));
        }

        return $this->getHttpClient()->requestAsync(
            $method,
            $uri,
            [
                RequestOptions::JSON    => $body,
                RequestOptions::HEADERS => $headers,
            ]
        )->then(function (ResponseInterface $response) use ($cacheKey, $ttl) {
            $this->cache->set($cacheKey, str($response), $ttl);

            return $response;
        }, function (RequestException $reason) {
            return $this->handleRequestException($reason);
        });
    }

    /**
     * Convert the data from a json request to an array of resources.
     *
     * @param array $json
     * @return array
     */
    protected function jsonToResources(array $json)
    {
        $resources = [];

        if (isset($json['type'])) {
            $json = [$json];
        }

        foreach ($json as $resourceData) {
            $attributes = $this->flattenResourceData($resourceData);

            $resources[] = $this->resourceFactory->create($resourceData['type'], $attributes);
        }

        return $resources;
    }

    /**
     * Flattens the data of the resource into a single array, effectively
     * removing the `attributes` and `relationships` arrays.
     *
     * @param array $resourceData
     * @return array
     */
    private function flattenResourceData(array $resourceData)
    {
        $data = [
            'id'   => $resourceData['id'],
            'type' => $resourceData['type'],
        ];

        if (isset($resourceData['attributes'])) {
            $data += $resourceData['attributes'];
        }

        if (isset($resourceData['relationships'])) {
            $data += array_map(
                [$this, 'flattenRelationship'],
                $resourceData['relationships']
            );
        }

        if (isset($resourceData['links'])) {
            $data['links'] = $resourceData['links'];
        }

        return $data;
    }

    /**
     * @param array $relationship
     * @return array
     */
    private function flattenRelationship(array $relationship)
    {
        $data = isset($relationship['data'])
            ? $relationship['data']
            : [];
        $links = isset($relationship['links'])
            ? $relationship['links']
            : [];

        return $data + $links;
    }

    /**
     * @param RequestException $exception
     * @return PromiseInterface
     */
    protected function handleRequestException(RequestException $exception)
    {
        $response = $exception->getResponse();

        if ($response->getStatusCode() !== 401 || $this->authRetry) {
            // TODO actually do something
            // echo (string)$exception->getRequest()->getUri();
            // echo (string)$exception->getResponse()->getBody();

            throw $exception;
        }

        $this->authRetry = true;
        $authHeaders = $this->authenticator->getAuthorizationHeader(true);

        $request = $exception->getRequest();
        $body = (string)$request->getBody();
        $jsonBody = $body
            ? \GuzzleHttp\json_decode($body, true)
            : [];

        return $this->doRequest(
            $request->getUri(),
            $request->getMethod(),
            $jsonBody,
            $authHeaders + $request->getHeaders()
        );
    }

    /**
     * @param string $resourceType
     * @param string $id
     * @return ResourceInterface
     */
    public function getResourceById($resourceType, $id)
    {
        $resources = $this->getResourcesPromise(
            $this->getResourceUri($resourceType, $id)
        )->wait();

        return reset($resources);
    }

    /**
     * {@inheritdoc}
     */
    public function getResourcesFromUri($uri)
    {
        return $this->getResourcesPromise($uri)->wait();
    }

    /**
     * Post given resource and return the resource that was returned.
     *
     * @param ResourceInterface $resource
     * @return ResourceInterface|null
     */
    protected function postResource(ResourceInterface $resource)
    {
        $promise = $this->getHttpClient()->requestAsync(
            'post',
            $this->getResourceUri($resource->getType()),
            [
                RequestOptions::HEADERS => $this->authenticator->getAuthorizationHeader() + [
                        AuthenticatorInterface::HEADER_ACCEPT => AuthenticatorInterface::MIME_TYPE_JSONAPI,
                    ],
                RequestOptions::JSON    => ['data' => $resource],
            ]
        )->then(function (ResponseInterface $response) {
            $json = \GuzzleHttp\json_decode($response->getBody(), true);

            return $this->jsonToResources($json['data']);
        }, function (RequestException $reason) {
            return $this->handleRequestException($reason);
        });

        $resources = $promise->wait();

        return reset($resources);
    }

    /**
     * @param string      $resourceType
     * @param string|null $id
     * @return string
     */
    protected function getResourceUri($resourceType, $id = null)
    {
        return implode(
            '/',
            array_filter([
                $this->apiUri,
                self::API_VERSION,
                $resourceType,
                $id,
            ])
        );
    }
}
