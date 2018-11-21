<?php

namespace MyParcelCom\ApiSdk\Shipments;

use MyParcelCom\ApiSdk\Exceptions\CalculationException;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceRateInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShipmentInterface;

class PriceCalculator
{
    /**
     * Calculate the total price of given shipment. Optionally a contract can be
     * supplied for the price calculations. If no contract is given, the
     * contract on the shipment is used.
     *
     * @param ShipmentInterface         $shipment
     * @param ServiceRateInterface|null $serviceRate
     * @return int
     */
    public function calculate(ShipmentInterface $shipment, ServiceRateInterface $serviceRate = null)
    {
        if ($serviceRate === null) {
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
        }

        return $this->calculateOptionsPrice($shipment, $serviceRate) + $serviceRate->getPrice();
    }

    /**
     * Calculate the price based on the selected options for given shipment.
     * Optionally a contract can be supplied for the price calculations. If no
     * contract is given, the contract on the shipment is used.
     *
     * @param ShipmentInterface         $shipment
     * @param ServiceRateInterface|null $serviceRate
     * @return int
     */
    public function calculateOptionsPrice(ShipmentInterface $shipment, ServiceRateInterface $serviceRate = null)
    {
        if ($serviceRate === null) {
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
        }

        $price = 0;

        $prices = [];
        foreach ($serviceRate->getServiceOptions() as $option) {
            $prices[$option->getId()] = $option->getPrice();
        }

        foreach ($shipment->getServiceOptions() as $option) {
            if (!isset($prices[$option->getId()])) {
                throw new CalculationException(
                    'Cannot calculate a price for given shipment; invalid option: ' . $option->getId()
                );
            }

            $price += $prices[$option->getId()];
        }

        return (int)$price;
    }
}
