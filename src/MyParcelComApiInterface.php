<?php

namespace MyParcelCom\Sdk;

use MyParcelCom\Sdk\Authentication\AuthenticatorInterface;
use MyParcelCom\Sdk\Exceptions\MyParcelComException;
use MyParcelCom\Sdk\Resources\Interfaces\CarrierInterface;
use MyParcelCom\Sdk\Resources\Interfaces\PickUpDropOffLocationInterface;
use MyParcelCom\Sdk\Resources\Interfaces\RegionInterface;
use MyParcelCom\Sdk\Resources\Interfaces\ServiceInterface;
use MyParcelCom\Sdk\Resources\Interfaces\ShipmentInterface;
use MyParcelCom\Sdk\Resources\Interfaces\ShopInterface;

interface MyParcelComApiInterface
{
    /**
     * Authenticate to the API using the given authenticator.
     *
     * @param AuthenticatorInterface $authenticator
     * @throws MyParcelComException
     * @return $this
     */
    public function authenticate(AuthenticatorInterface $authenticator);

    /**
     * Get an array with all the regions, optionally regions can be filtered by
     * country code and region code.
     *
     * @param string|null $countryCode
     * @param string|null $regionCode
     * @return RegionInterface[]
     */
    public function getRegions($countryCode = null, $regionCode = null);

    /**
     * Get all the carriers from the API.
     *
     * @throws MyParcelComException
     * @return CarrierInterface[]
     */
    public function getCarriers();

    /**
     * Get the pick up/drop off locations around a given location. If no carrier
     * is given, the default carrier is used.
     *
     * @param string                $countryCode
     * @param string                $postalCode
     * @param string|null           $streetName
     * @param string|null           $streetNumber
     * @param CarrierInterface|null $carrier
     * @return PickUpDropOffLocationInterface[]
     */
    public function getPickUpDropOffLocations(
        $countryCode,
        $postalCode,
        $streetName = null,
        $streetNumber = null,
        CarrierInterface $carrier = null
    );

    /**
     * Get the shops from the API.
     *
     * @throws MyParcelComException
     * @return ShopInterface[]
     */
    public function getShops();

    /**
     * Get all services that can be used for given shipment. If no shipment is
     * provided, all available services are returned.
     *
     * @param ShipmentInterface|null $shipment
     * @throws MyParcelComException
     * @return ServiceInterface[]
     */
    public function getServices(ShipmentInterface $shipment = null);

    /**
     * Get all the services that are available for the given carrier.
     *
     * @param CarrierInterface $carrier
     * @throws MyParcelComException
     * @return ServiceInterface[]
     */
    public function getServicesForCarrier(CarrierInterface $carrier);

    /**
     * Get shipments for a given shop. Id no shop is given the default shop is
     * used.
     *
     * @param ShopInterface|null $shop
     * @throws MyParcelComException
     * @return ShipmentInterface[]
     */
    public function getShipments(ShopInterface $shop = null);

    /**
     * Get a specific shipment from the API.
     *
     * @param string $id
     * @throws MyParcelComException
     * @return ShipmentInterface
     */
    public function getShipment($id);

    /**
     * Creates given shipment and returns an updated version of the shipment.
     * When certain properties on the shipment are not set, defaults should be
     * used. When no default value is available, an exception should be thrown.
     *
     * @param ShipmentInterface $shipment
     * @throws MyParcelComException
     * @return ShipmentInterface
     */
    public function createShipment(ShipmentInterface $shipment);
}
