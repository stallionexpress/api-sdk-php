<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk;

use MyParcelCom\ApiSdk\Authentication\AuthenticatorInterface;
use MyParcelCom\ApiSdk\Collection\CollectionInterface;
use MyParcelCom\ApiSdk\Exceptions\MyParcelComException;
use MyParcelCom\ApiSdk\Http\Exceptions\RequestException;
use MyParcelCom\ApiSdk\Resources\Interfaces\CarrierInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceRateInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShipmentInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShopInterface;
use Psr\Http\Message\ResponseInterface;

interface MyParcelComApiInterface
{
    const PATH_CARRIERS = '/carriers';
    const PATH_FILES_ID = '/files/{file_id}';
    const PATH_PUDO_LOCATIONS = '/carriers/{carrier_id}/pickup-dropoff-locations/{country_code}/{postal_code}';
    const PATH_REGIONS = '/regions';
    const PATH_SERVICES = '/services';
    const PATH_SERVICE_RATES = '/service-rates';
    const PATH_SHIPMENTS = '/shipments';
    const PATH_SHIPMENT_STATUSES = '/shipments/{shipment_id}/statuses';
    const PATH_SHOPS = '/shops';

    const HEADER_IDEMPOTENCY_KEY = 'Idempotency-Key';

    const TTL_NO_CACHE = 0;
    const TTL_10MIN = 600;
    const TTL_WEEK = 604800;
    const TTL_MONTH = 2592000;

    /**
     * Authenticate to the API using the given authenticator.
     *
     * @throws MyParcelComException
     */
    public function authenticate(AuthenticatorInterface $authenticator): self;

    /**
     * @deprecated
     */
    public function getRegions(array $filters = [], int $ttl = self::TTL_10MIN): CollectionInterface;

    /**
     * Get all the carriers from the API.
     *
     * @throws MyParcelComException
     */
    public function getCarriers(int $ttl = self::TTL_10MIN): CollectionInterface;

    /**
     * Get the pick-up/drop-off locations around a given location.
     * If no specific carrier is given, an array of pick-up location collections for all available carriers is returned.
     */
    public function getPickUpDropOffLocations(
        string $countryCode,
        string $postalCode,
        ?string $streetName = null,
        ?string $streetNumber = null,
        CarrierInterface $specificCarrier = null,
        bool $onlyActiveContracts = true,
        int $ttl = self::TTL_10MIN,
    ): CollectionInterface|array;

    /**
     * Get the shops from the API.
     *
     * @throws MyParcelComException
     */
    public function getShops(int $ttl = self::TTL_10MIN): CollectionInterface;

    /**
     * Get the default shop that will be used when interacting with the API and no specific shop has been set.
     *
     * @throws MyParcelComException
     */
    public function getDefaultShop(int $ttl = self::TTL_10MIN): ShopInterface;

    /**
     * Get all services that can be used for given shipment. If no shipment is
     * provided, all available services are returned.
     *
     * @throws MyParcelComException
     */
    public function getServices(
        ShipmentInterface $shipment = null,
        array $filters = ['has_active_contract' => 'true'],
        int $ttl = self::TTL_10MIN,
    ): CollectionInterface;

    /**
     * Get all the services that are available for the given carrier.
     *
     * @throws MyParcelComException
     */
    public function getServicesForCarrier(CarrierInterface $carrier, int $ttl = self::TTL_10MIN): CollectionInterface;

    /**
     * Retrieves service rates based on the set filters. Available filters are: service, contract and weight. Note that
     * this function could return service rates which are dynamic. Their price and availability depends on the shipment
     * data and requires communication with the carrier. This info can be retrieved using resolveDynamicServiceRates().
     */
    public function getServiceRates(
        array $filters = ['has_active_contract' => 'true'],
        int $ttl = self::TTL_10MIN,
    ): CollectionInterface;

    /**
     * Retrieves service rates based on the shipment.
     * The shipment needs to have a recipient/sender_address and a weight set.
     */
    public function getServiceRatesForShipment(
        ShipmentInterface $shipment,
        int $ttl = self::TTL_10MIN,
    ): CollectionInterface;

    /**
     * Retrieve dynamic rates (price / options / availability) from the carrier, based on the provided shipment data.
     * The shipment should have a service, contract, addresses, weight and sometimes dimensions are required as well.
     * If you have a ServiceRate which is_dynamic, you can pass it and its service and contract will be used instead.
     *
     * @throws RequestException
     */
    public function resolveDynamicServiceRates(
        ShipmentInterface|array $shipmentData,
        ?ServiceRateInterface $dynamicServiceRate = null
    ): array;

    /**
     * Get shipments for a given shop. If no shop is given the default shop is used.
     *
     * @throws MyParcelComException
     */
    public function getShipments(ShopInterface $shop = null, int $ttl = self::TTL_NO_CACHE): CollectionInterface;

    /**
     * Get a specific shipment from the API.
     *
     * @throws MyParcelComException
     */
    public function getShipment(string $id, int $ttl = self::TTL_NO_CACHE): ShipmentInterface;

    /**
     * Creates a given shipment or updates it depending on if the id is already set.
     * It returns the just created or updated version of the shipment.
     * When certain properties for a new shipment are not set, defaults should be
     * used. When no default value is available, an exception should be thrown.
     *
     * @throws MyParcelComException
     */
    public function saveShipment(ShipmentInterface $shipment): ShipmentInterface;

    /**
     * Update the given shipment and returns the updated version of the shipment.
     *
     * @throws MyParcelComException
     */
    public function updateShipment(ShipmentInterface $shipment): ShipmentInterface;

    /**
     * Creates a given shipment and returns the created version of the shipment.
     * When certain properties on the shipment are not set, defaults should be
     * used. When no default value is available, an exception should be thrown.
     *
     * @see https://docs.myparcel.com/api/create-a-shipment.html
     * @see https://docs.myparcel.com/api/create-a-shipment/idempotency.html
     * @throws MyParcelComException
     */
    public function createShipment(ShipmentInterface $shipment, ?string $idempotencyKey = null): ShipmentInterface;

    /**
     * Get the resource of given type with given id.
     *
     * @throws MyParcelComException
     */
    public function getResourceById(string $resourceType, string $id, int $ttl = self::TTL_NO_CACHE): ResourceInterface;

    /**
     * Get an array of all the resources from given uri.
     *
     * @return ResourceInterface[]
     */
    public function getResourcesFromUri(string $uri): array;

    /**
     * Do an async request to given uri on the API.
     *
     * @throws RequestException
     */
    public function doRequest(
        string $uri,
        string $method = 'get',
        array $body = [],
        array $headers = [],
        $ttl = self::TTL_NO_CACHE,
    ): ResponseInterface;
}
