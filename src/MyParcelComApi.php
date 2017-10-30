<?php

namespace MyParcelCom\Sdk;

use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\RequestOptions;
use MyParcelCom\Sdk\Authentication\AuthenticatorInterface;
use MyParcelCom\Sdk\Exceptions\InvalidResourceException;
use MyParcelCom\Sdk\Resources\Interfaces\CarrierInterface;
use MyParcelCom\Sdk\Resources\Interfaces\FileInterface;
use MyParcelCom\Sdk\Resources\Interfaces\ResourceFactoryInterface;
use MyParcelCom\Sdk\Resources\Interfaces\ResourceInterface;
use MyParcelCom\Sdk\Resources\Interfaces\ShipmentInterface;
use MyParcelCom\Sdk\Resources\Interfaces\ShopInterface;
use MyParcelCom\Sdk\Resources\ResourceFactory;
use MyParcelCom\Sdk\Validators\ShipmentValidator;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Cache\Simple\FilesystemCache;

class MyParcelComApi implements MyParcelComApiInterface
{
    private $regions = [
        ['country_code' => 'NL', 'region_code' => 'NH'],
        ['country_code' => 'NL', 'region_code' => 'ZH'],
        ['country_code' => 'GB'],
        ['country_code' => 'US'],
        ['country_code' => 'DE'],
        ['country_code' => 'BE'],
        ['country_code' => 'FR'],
        ['country_code' => 'AF'],
        ['country_code' => 'AQ'],
        ['country_code' => 'BS'],
        ['country_code' => 'BH'],
    ];

    private $carriers = [
        ['name' => 'Spring'],
        ['name' => 'DPD'],
    ];

    private $shops = [
        [
            'id'              => 'shop-id-1',
            'name'            => 'MyParcel.com Test Shop',
            'billing_address' => [
                'street_1'      => 'Some road',
                'street_number' => 17,
                'postal_code'   => '1GL HF1',
                'city'          => 'Cardiff',
                'country_code'  => 'GB',
                'first_name'    => 'John',
                'last_name'     => 'Doe',
                'email'         => 'john@doe.com',
                'phone_number'  => '+31 234 567 890',
            ],
            'return_address'  => [
                'street_1'      => 'Some road',
                'street_number' => 17,
                'postal_code'   => '1GL HF1',
                'city'          => 'Cardiff',
                'country_code'  => 'GB',
                'first_name'    => 'John',
                'last_name'     => 'Doe',
                'email'         => 'john@doe.com',
                'phone_number'  => '+31 234 567 890',
            ],
            'region'          => [
                'country_code' => 'GB',
            ],
        ],
    ];

    private $pudoLocations = [
        [
            'id'         => 'pudo-id',
            'type'       => 'pickup-dropoff-locations',
            'attributes' => [
                'address'       => [
                    'street_1'             => 'Diagonally',
                    'street_number'        => '1',
                    'street_number_suffix' => 'A',
                    'postal_code'          => '1AR BR2',
                    'city'                 => 'London',
                    'region_code'          => 'NH',
                    'country_code'         => 'AF',
                    'first_name'           => 'Robert',
                    'last_name'            => 'Drop Tables',
                    'company'              => 'ACME co.',
                    'email'                => 'rob@tables.com',
                    'phone_number'         => '+31 (0)234 567 890',
                ],
                'opening_hours' => [
                    [
                        'day'    => 'Sunday',
                        'open'   => '09:00',
                        'closed' => '19:00',
                    ],
                ],
                'position'      => [
                    'latitude'  => 1.2345,
                    'longitude' => 2.34567,
                    'distance'  => 5000,
                    'unit'      => 'meters',
                ],
            ],
        ],
        [
            'id'         => 'pudo-id',
            'type'       => 'pickup-dropoff-locations',
            'attributes' => [
                'address'       => [
                    'street_1'             => 'Diagonally',
                    'street_number'        => '2',
                    'street_number_suffix' => 'A',
                    'postal_code'          => '1AR BR2',
                    'city'                 => 'London',
                    'region_code'          => 'NH',
                    'country_code'         => 'AF',
                    'first_name'           => 'Robert',
                    'last_name'            => 'Drop Tables',
                    'company'              => 'ACME co.',
                    'email'                => 'rob@tables.com',
                    'phone_number'         => '+31 (0)234 567 890',
                ],
                'opening_hours' => [
                    [
                        'day'    => 'Sunday',
                        'open'   => '09:00',
                        'closed' => '19:00',
                    ],
                ],
                'position'      => [
                    'latitude'  => 1.2345,
                    'longitude' => 2.34567,
                    'distance'  => 5000,
                    'unit'      => 'meters',
                ],
            ],
        ],
        [
            'id'         => 'pudo-id',
            'type'       => 'pickup-dropoff-locations',
            'attributes' => [
                'address'       => [
                    'street_1'             => 'Diagonally',
                    'street_number'        => '3',
                    'street_number_suffix' => 'A',
                    'postal_code'          => '1AR BR2',
                    'city'                 => 'London',
                    'region_code'          => 'NH',
                    'country_code'         => 'AF',
                    'first_name'           => 'Robert',
                    'last_name'            => 'Drop Tables',
                    'company'              => 'ACME co.',
                    'email'                => 'rob@tables.com',
                    'phone_number'         => '+31 (0)234 567 890',
                ],
                'opening_hours' => [
                    [
                        'day'    => 'Sunday',
                        'open'   => '09:00',
                        'closed' => '19:00',
                    ],
                ],
                'position'      => [
                    'latitude'  => 1.2345,
                    'longitude' => 2.34567,
                    'distance'  => 5000,
                    'unit'      => 'meters',
                ],
            ],
        ],

    ];

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

    private $services = [
        [
            'id' => 'service-id-1',

            'name'         => 'Parcel to Parcelshop',
            'package_type' => 'parcel',
            'carrier'      => [
                'id' => 'c9ce29a4-6325-11e7-907b-a6006ad3dba0',
            ],
            'region_from'  => ['country_code' => 'GB'],

            'region_to' => ['country_code' => 'GB'],
            'contracts' => [
                'groups'     => [],
                'insurances' => [],
                'options'    => [],
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
        return array_map(function ($region) {
            return $this->resourceFactory->create(ResourceInterface::TYPE_REGION, $region);
        }, array_filter($this->regions, function ($region) use ($countryCode, $regionCode) {
            return ($countryCode === null
                    || $region['country_code'] === $countryCode)
                && ($regionCode === null
                    || $region['region_code'] === $regionCode);
        }));
    }

    /**
     * {@inheritdoc}
     */
    public function getCarriers()
    {
        return array_map(function ($carrier) {
            return $this->resourceFactory->create(ResourceInterface::TYPE_CARRIER, $carrier);
        }, $this->carriers);
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
        return array_map(function ($pudoLocation) {
            return $this->resourceFactory->create(ResourceInterface::TYPE_PUDO_LOCATION, $pudoLocation);
        }, $this->pudoLocations);
    }

    /**
     * {@inheritdoc}
     */
    public function getShops()
    {
        return array_map(function ($shop) {
            return $this->resourceFactory->create(ResourceInterface::TYPE_SHOP, $shop);
        }, $this->shops);
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultShop()
    {
        return reset($this->getShops());
    }

    /**
     * {@inheritdoc}
     */
    public function getServices(ShipmentInterface $shipment = null)
    {
        return array_map(function ($service) {
            return $this->resourceFactory->create(ResourceInterface::TYPE_SERVICE, $service);
        }, $this->services);
    }

    /**
     * {@inheritdoc}
     */
    public function getServicesForCarrier(CarrierInterface $carrier)
    {
        return array_map(function ($service) use ($carrier) {
            return $this->resourceFactory->create(ResourceInterface::TYPE_SERVICE, $service)->setCarrier($carrier);
        }, $this->services);
    }

    /**
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
     * {@inheritdoc}
     */
    public function getShipment($id)
    {
        return isset($this->shipments[$id])
            ? $this->resourceFactory->create(ResourceInterface::TYPE_SHIPMENT, $this->shipments[$id])
            : null;
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
                    ] + $this->authenticator->getAuthorizationHeader(),
            ]);
        }

        return $this->client;
    }
}
