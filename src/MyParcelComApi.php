<?php

namespace MyParcelCom\Sdk;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\RequestOptions;
use MyParcelCom\Sdk\Authentication\AuthenticatorInterface;
use MyParcelCom\Sdk\Exceptions\InvalidResourceException;
use MyParcelCom\Sdk\Resources\Interfaces\CarrierInterface;
use MyParcelCom\Sdk\Resources\Interfaces\RegionInterface;
use MyParcelCom\Sdk\Resources\Interfaces\ResourceFactoryInterface;
use MyParcelCom\Sdk\Resources\Interfaces\ResourceInterface;
use MyParcelCom\Sdk\Resources\Interfaces\ResourceProxyInterface;
use MyParcelCom\Sdk\Resources\Interfaces\ServiceInterface;
use MyParcelCom\Sdk\Resources\Interfaces\ShipmentInterface;
use MyParcelCom\Sdk\Resources\Interfaces\ShopInterface;
use MyParcelCom\Sdk\Resources\ResourceFactory;
use MyParcelCom\Sdk\Shipments\ContractSelector;
use MyParcelCom\Sdk\Shipments\PriceCalculator;
use MyParcelCom\Sdk\Shipments\ServiceMatcher;
use MyParcelCom\Sdk\Validators\ShipmentValidator;
use Psr\Http\Message\ResponseInterface;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Cache\Simple\FilesystemCache;
use function GuzzleHttp\Promise\promise_for;
use function GuzzleHttp\Promise\unwrap;

class MyParcelComApi implements MyParcelComApiInterface
{
    const API_VERSION = 'v1';
    const TTL_WEEK = 604800;
    const TTL_10MIN = 600;

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
        return array_filter($regions, function (RegionInterface $region) use ($countryCode, $regionCode) {
            return ($countryCode === null || $countryCode === $region->getCountryCode())
                && ($regionCode === null || $regionCode === $region->getRegionCode());
        });
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
        CarrierInterface $carrier = null
    ) {
        $carriers = $carrier ? [$carrier] : $this->getCarriers();

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

        $promises = array_map(function (CarrierInterface $carrier) use ($uri) {
            $carrierUri = str_replace('{carrier_id}', $carrier->getId(), $uri);

            // These resources can be stored for a week.
            return $this->getResourcesPromise($carrierUri, self::TTL_WEEK)
                ->otherwise(function (RequestException $reason) {
                    return $this->handleRequestException($reason);
                });
        }, $carriers);

        $resources = call_user_func_array('array_merge', unwrap($promises));

        return $resources;
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
        // Services can be cached for a week.
        $services = $this->getResourcesPromise($this->apiUri . self::PATH_SERVICES, self::TTL_WEEK)
            ->wait();

        if ($shipment !== null) {
            if ($shipment->getSenderAddress() === null) {
                $shipment->setSenderAddress($this->getDefaultShop()->getReturnAddress());
            }

            $matcher = new ServiceMatcher();
            $services = array_filter($services, function (ServiceInterface $service) use ($shipment, $matcher) {
                return $matcher->matches($shipment, $service);
            });
        }

        return $services;
    }

    /**
     * {@inheritdoc}
     */
    public function getServicesForCarrier(CarrierInterface $carrier)
    {
        // For now, we need to manually filter the services
        return array_filter($this->getServices(), function (ServiceInterface $service) use ($carrier) {
            return $service->getCarrier()->getId() === $carrier->getId();
        });
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
        return array_filter($shipments, function (ShipmentInterface $shipment) use ($shop) {
            return $shipment->getShop()->getId() === $shop->getId();
        });
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
     * given url. A time-to-live can be specified for how long this request
     * should be cached (defaults to 10 minutes).
     *
     * @param string $url
     * @param int    $ttl
     * @return PromiseInterface
     * @internal param string $path
     */
    protected function getResourcesPromise($url, $ttl = self::TTL_10MIN)
    {
        $cacheKey = 'get.' . str_replace([':', '{', '}', '(', ')', '/', '\\', '@'], '-', $url);
        if (($resources = $this->cache->get($cacheKey))) {
            return promise_for($resources);
        }

        return $this->getHttpClient()->requestAsync(
            'get',
            $url,
            [
                RequestOptions::HEADERS => $this->authenticator->getAuthorizationHeader(),
            ]
        )->then(function (ResponseInterface $response) use ($cacheKey, $ttl) {
            $json = \GuzzleHttp\json_decode((string)$response->getBody(), true);

            $resources = $this->jsonToResources($json['data']);

            // If there is no next link, we don't have to retrieve any more data
            if (!isset($json['links']['next'])) {
                return $resources;
            }

            return $this->getResourcesPromise($json['links']['next'])
                ->then(function ($nextResources) use ($resources, $cacheKey, $ttl) {

                    $combinedResources = array_merge($resources, $nextResources);

                    $this->cache->set($cacheKey, $combinedResources, $ttl);

                    return $combinedResources;
                });
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
            $data += array_map(function ($relationship) {
                return $relationship['data'];
            }, $resourceData['relationships']);
        }

        if (isset($resourceData['links'])) {
            $data['links'] = $resourceData['links'];
        }

        return $data;
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
        $this->authenticator->getAuthorizationHeader(true);

        return $this->getResourcesPromise($exception->getRequest()->getUri());
    }

    /**
     * @param string $resourceType
     * @param string $id
     * @return ResourceInterface
     */
    public function getResourceById($resourceType, $id)
    {
        return reset($this->getResourcesPromise(
            $this->getResourceUri($resourceType, $id)
        )->wait());
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
                RequestOptions::JSON => ['data' => $resource],
            ]
        )->then(function (ResponseInterface $response) {
            $json = \GuzzleHttp\json_decode($response->getBody(), true);

            return $this->jsonToResources($json['data']);
        }, function (RequestException $reason) {
            return $this->handleRequestException($reason);
        });


        return reset($promise->wait());
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
