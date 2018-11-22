<?php

namespace MyParcelCom\ApiSdk\Shipments;

use MyParcelCom\ApiSdk\Exceptions\CalculationException;
use MyParcelCom\ApiSdk\Exceptions\InvalidResourceException;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceRateInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShipmentInterface;

class PriceCalculator
{
    /**
     * Calculate the total price of given shipment based off its contract, service and weight.
     *
     * @param ShipmentInterface         $shipment
     * @param ServiceRateInterface|null $serviceRate
     * @return int
     */
    public function calculate(ShipmentInterface $shipment, ServiceRateInterface $serviceRate = null)
    {
        if ($serviceRate === null) {
            $serviceRate = $this->determineServiceRateForShipment($shipment);
        }

        // TODO: Check if shipment weight corresponds to service rate weight range.

        // TODO: Early return for no service options.

        return $this->calculateOptionsPrice($shipment, $serviceRate) + $serviceRate->getPrice();
    }

    /**
     * Calculate the price based on the selected options for given shipment.
     *
     * @param ShipmentInterface         $shipment
     * @param ServiceRateInterface|null $serviceRate
     * @return int
     */
    public function calculateOptionsPrice(ShipmentInterface $shipment, ServiceRateInterface $serviceRate = null)
    {
        if ($serviceRate === null) {
            $serviceRate = $this->determineServiceRateForShipment($shipment);
        }

        $price = 0;

        $prices = [];
        foreach ($serviceRate->getServiceOptions() as $option) {
            $prices[$option->getId()] = $option->getPrice();
        }

        foreach ($shipment->getServiceOptions() as $option) {
            if (!array_key_exists($option->getId(), $prices)) {
                throw new CalculationException(
                    'Cannot calculate a price for given shipment; invalid option: ' . $option->getId()
                );
            }

            $price += $prices[$option->getId()];
        }

        return (int)$price;
    }

    /**
     * @param ShipmentInterface $shipment
     * @throws InvalidResourceException
     */
    private function validateShipment(ShipmentInterface $shipment)
    {
        if ($shipment->getPhysicalProperties() === null || $shipment->getPhysicalProperties()->getWeight() === null) {
            throw new InvalidResourceException(
                'Cannot calculate shipment price without a set weight.'
            );
        }
        if ($shipment->getContract() === null) {
            throw new InvalidResourceException(
                'Cannot calculate shipment price without a set contract.'
            );
        }
        if ($shipment->getService() === null) {
            throw new InvalidResourceException(
                'Cannot calculate shipment price without a set service.'
            );
        }
    }

    /**
     * @param ShipmentInterface    $shipment
     * @return mixed|ServiceRateInterface
     */
    private function determineServiceRateForShipment(ShipmentInterface $shipment)
    {
        $this->validateShipment($shipment);

        $serviceRates = $shipment->getService()->getServiceRates([
            'contract' => $shipment->getContract(),
            'weight'   => $shipment->getPhysicalProperties()->getWeight(),
        ]);

        $serviceRate = reset($serviceRates);

        if (!$serviceRate) {
            throw new CalculationException(
                'Cannot find a matching service rate for given shipment'
            );
        }

        return $serviceRate;
    }
}
