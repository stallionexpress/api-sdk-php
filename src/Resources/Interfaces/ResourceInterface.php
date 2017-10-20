<?php

namespace MyParcelCom\Sdk\Resources\Interfaces;

/**
 * Interface that should be implemented by each class that represents a resource
 * af the API.
 */
interface ResourceInterface extends JsonInterface
{
    const TYPE_CARRIER = 'carriers';
    const TYPE_CONTRACT = 'contracts';
    const TYPE_FILE = 'files';
    const TYPE_PUDO_LOCATION = 'pickup-dropoff-locations';
    const TYPE_REGION = 'regions';
    const TYPE_SHIPMENT = 'shipments';
    const TYPE_SHOP = 'shops';
    const TYPE_SERVICE = 'services';
    const TYPE_SERVICE_GROUP = 'service-groups';
    const TYPE_SERVICE_OPTION = 'service-options';
    const TYPE_SERVICE_INSURANCE = 'service-insurances';

    /**
     * @return string
     */
    public function getId();

    /**
     * @return string
     */
    public function getType();
}
