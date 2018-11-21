<?php

namespace MyParcelCom\ApiSdk\Shipments;

use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceContractInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceOptionInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShipmentInterface;

class ServiceMatcher
{
    /**
     * Returns true if given service can be used for the given shipment.
     *
     * @param ShipmentInterface $shipment
     * @param ServiceInterface  $service
     * @return bool
     */
    public function matches(ShipmentInterface $shipment, ServiceInterface $service)
    {
        return $this->matchesDeliveryMethod($shipment, $service)
            && ($this->getMatchedOptions($shipment, $service->getServiceRates()));
    }

    /**
     * Returns true if the service has a delivery method that matches the
     * shipment.
     *
     * @param ShipmentInterface $shipment
     * @param ServiceInterface  $service
     * @return bool
     */
    public function matchesDeliveryMethod(ShipmentInterface $shipment, ServiceInterface $service)
    {
        $deliveryMethod = $shipment->getPickupLocationCode()
            ? ServiceInterface::DELIVERY_METHOD_PICKUP
            : ServiceInterface::DELIVERY_METHOD_DELIVERY;

        return $service->getDeliveryMethod() === $deliveryMethod;
    }

    /**
     * Returns a subset of the given service rates that have all the options that
     * the shipment requires.
     *
     * @param ShipmentInterface          $shipment
     * @param ServiceContractInterface[] $serviceRates
     * @return ServiceContractInterface[]
     */
    public function getMatchedOptions(ShipmentInterface $shipment, array $serviceRates)
    {
        $optionIds = array_map(function (ServiceOptionInterface $option) {
            return $option->getId();
        }, $shipment->getServiceOptions());

        $matches = [];
        foreach ($serviceRates as $serviceRate) {
            $contractOptionIds = array_map(function (ServiceOptionInterface $option) use ($optionIds) {
                return $option->getId();
            }, $serviceRate->getServiceOptions());

            if (!array_diff($optionIds, $contractOptionIds)) {
                $matches[] = $serviceRate;
            }
        }

        return $matches;
    }
}
