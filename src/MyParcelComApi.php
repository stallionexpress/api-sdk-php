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
use MyParcelCom\Sdk\Resources\Interfaces\FileInterface;
use MyParcelCom\Sdk\Resources\Interfaces\ResourceFactoryInterface;
use MyParcelCom\Sdk\Resources\Interfaces\ResourceInterface;
use MyParcelCom\Sdk\Resources\Interfaces\ServiceInterface;
use MyParcelCom\Sdk\Resources\Interfaces\ShipmentInterface;
use MyParcelCom\Sdk\Resources\Interfaces\ShopInterface;
use MyParcelCom\Sdk\Resources\ResourceFactory;
use MyParcelCom\Sdk\Validators\ShipmentValidator;
use Psr\Http\Message\ResponseInterface;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Cache\Simple\FilesystemCache;
use function GuzzleHttp\Promise\promise_for;
use function GuzzleHttp\Promise\unwrap;

class MyParcelComApi implements MyParcelComApiInterface
{
    private $shipments = [
        'shipment-id-1' => [
            'id'                  => 'shipment-id-1',
            'recipient_address'   => [
                'street_1'             => 'Some road',
                'street_2'             => 'Room 3',
                'street_number'        => 17,
                'street_number_suffix' => 'A',
                'postal_code'          => '1GL HF1',
                'city'                 => 'Cardiff',
                'region_code'          => 'CRF',
                'country_code'         => 'GB',
                'first_name'           => 'John',
                'last_name'            => 'Doe',
                'company'              => 'Acme Jewelry Co.',
                'email'                => 'john@doe.com',
                'phone_number'         => '+31 234 567 890',
            ],
            'sender_address'      => [
                'street_1'             => 'Some road',
                'street_2'             => 'Room 3',
                'street_number'        => 17,
                'street_number_suffix' => 'A',
                'postal_code'          => '1GL HF1',
                'city'                 => 'Cardiff',
                'region_code'          => 'CRF',
                'country_code'         => 'GB',
                'first_name'           => 'John',
                'last_name'            => 'Doe',
                'company'              => 'Acme Jewelry Co.',
                'email'                => 'john@doe.com',
                'phone_number'         => '+31 234 567 890',
            ],
            'pickup_location'     => [
                'code'    => '123456',
                'address' => [
                    'street_1'             => 'Some road',
                    'street_2'             => 'Room 3',
                    'street_number'        => 17,
                    'street_number_suffix' => 'A',
                    'postal_code'          => '1GL HF1',
                    'city'                 => 'Cardiff',
                    'region_code'          => 'CRF',
                    'country_code'         => 'GB',
                    'first_name'           => 'John',
                    'last_name'            => 'Doe',
                    'company'              => 'Acme Jewelry Co.',
                    'email'                => 'john@doe.com',
                    'phone_number'         => '+31 234 567 890',
                ],
            ],
            'description'         => 'order #8008135',
            'price'               => 100,
            'currency'            => 'EUR',
            'insuranceAmount'     => 100,
            'barcode'             => '3SABCD0123456789',
            'weight'              => 24,
            'physical_properties' => [
                'height' => 24,
                'width'  => 50,
                'length' => 50,
                'volume' => 50,
                'weight' => 24,
            ],
            'service_options'     => [
                [
                    'id' => 'service-id-1',
                ],
            ],
            'shop'                => [
                'id' => 'shop-id-1',
            ],
            'status'              => [
                'id' => 'status-id-1',
            ],
            'service'             => [
                'id' => 'service-id-1',
            ],
            'contract'            => [
                'id' => 'contract-id-1',
            ],
            'files'               => [
                [
                    'id'            => 'file-id-1',
                    'resource_type' => FileInterface::RESOURCE_TYPE_LABEL,
                    'formats'       => [['mime_type' => 'image/png', 'extension' => 'png']],
                    'base64_data'   => 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==',
                ],
                [
                    'id'            => 'file-id-2',
                    'resource_type' => FileInterface::RESOURCE_TYPE_PRINTCODE,
                    'formats'       => [['mime_type' => 'image/png', 'extension' => 'png']],
                    'base64_data'   => 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkkPxfDwAC0gGZ9+czaQAAAABJRU5ErkJggg==',
                ],
            ],
        ],
    ];

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

    /**
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
        return $this->getResourcesPromise($this->apiUri . self::PATH_REGIONS, 604800)
            ->wait();
    }

    /**
     * {@inheritdoc}
     */
    public function getCarriers()
    {
        // These resources can be stored for a week.
        return $this->getResourcesPromise($this->apiUri . self::PATH_CARRIERS, 604800)
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
            return $this->getResourcesPromise($carrierUri, 604800)
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
        return $this->getResourcesPromise($this->apiUri . self::PATH_SHOPS, 604800)
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
        $services = $this->getResourcesPromise($this->apiUri . self::PATH_SERVICES, 604800)
            ->wait();

        if ($shipment !== null) {
            $services = array_filter($services, function (ServiceInterface $service) {
                // TODO

                return true;
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
     * @todo
     * {@inheritdoc}
     */
    public function getShipments(ShopInterface $shop = null)
    {
        return array_map(function ($shipment) use ($shop) {
            return $shop
                ? $this->resourceFactory->create(ResourceInterface::TYPE_SHIPMENT, $shipment)->setShop($shop)
                : $this->resourceFactory->create(ResourceInterface::TYPE_SHIPMENT, $shipment);
        }, $this->shipments);
    }

    /**
     * @todo
     * {@inheritdoc}
     */
    public function getShipment($id)
    {
        return isset($this->shipments[$id])
            ? $this->resourceFactory->create(ResourceInterface::TYPE_SHIPMENT, $this->shipments[$id])
            : null;
    }

    /**
     * @todo
     * {@inheritdoc}
     */
    public function createShipment(ShipmentInterface $shipment)
    {
        // If no shop is set, use the default shop.
        if ($shipment->getShop() === null) {
            $shipment->setShop($this->getDefaultShop());
        }

        // If no service is set, default to the first shipment that is available
        // for this shipment's configuration.
        if ($shipment->getService() === null) {
            $shipment->setService(
                reset($this->getServices($shipment))
            );
        }

        // If no sender address has been set, default to the shop's return
        // address.
        if ($shipment->getSenderAddress() === null) {
            $shipment->setSenderAddress(
                $shipment->getShop()->getReturnAddress()
            );
        }

        $validator = new ShipmentValidator($shipment);
        if (!$validator->isValid()) {
            $exception = new InvalidResourceException(
                'Could not create shipment, shipment was invalid or incomplete'
            );
            $exception->setErrors($validator->getErrors());

            throw $exception;
        }

        $shipment->setPrice(650);
        $shipment->setCurrency('GBP');
        $shipment->setId('shipment-id-99');

        return $shipment;
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
    protected function getResourcesPromise($url, $ttl = 600)
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
        $data = ['id' => $resourceData['id']];

        if (isset($resourceData['attributes'])) {
            $data += $resourceData['attributes'];
        }

        if (isset($resourceData['relationships'])) {
            $data += array_map(function ($relationship) {
                return $relationship['data'];
            }, $resourceData['relationships']);
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
            echo (string)$exception->getRequest()->getUri();
            echo (string)$exception->getResponse()->getBody();

            throw $exception;
        }

        $this->authRetry = true;
        $this->authenticator->getAuthorizationHeader(true);

        return $this->getResourcesPromise($exception->getRequest()->getUri());
    }
}
