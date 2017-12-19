<?php

namespace MyParcelCom\ApiSdk\Shipments;

use MyParcelCom\ApiSdk\Exceptions\MyParcelComException;
use MyParcelCom\ApiSdk\Resources\Interfaces\ContractInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceGroupInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceInsuranceInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShipmentInterface;

class PriceCalculator
{
    /**
     * Calculate the total price of given shipment. Optionally a contract can be
     * supplied for the price calculations. If no contract is given, the
     * contract on the shipment is used.
     *
     * @param ShipmentInterface      $shipment
     * @param ContractInterface|null $contract
     * @return int
     */
    public function calculate(ShipmentInterface $shipment, ContractInterface $contract = null)
    {
        if ($contract === null) {
            $contract = $shipment->getContract();
        }

        if ($contract === null) {
            throw new MyParcelComException(
                'Cannot calculate a price for given shipment without a contract'
            );
        }

        return $this->calculateGroupPrice($shipment, $contract)
            + $this->calculateOptionsPrice($shipment, $contract)
            + $this->calculateInsurancePrice($shipment, $contract);
    }

    /**
     * Calculate the price based on the weight group for given shipment.
     * Optionally a contract can be supplied for the price calculations. If no
     * contract is given, the contract on the shipment is used.
     *
     * @param ShipmentInterface      $shipment
     * @param ContractInterface|null $contract
     * @return int
     */
    public function calculateGroupPrice(ShipmentInterface $shipment, ContractInterface $contract = null)
    {
        if ($contract === null) {
            $contract = $shipment->getContract();
        }

        if ($contract === null) {
            throw new MyParcelComException(
                'Cannot calculate a price for given shipment without a contract'
            );
        }

        $price = 0;

        // Order all the groups to have the highest weight group last.
        $groups = $contract->getGroups();
        usort($groups, function (ServiceGroupInterface $a, ServiceGroupInterface $b) {
            // Sort based on the min weight;
            $minDiff = $a->getWeightMin() - $b->getWeightMin();
            if ($minDiff !== 0) {
                return $minDiff;
            }

            // If the min weight of both groups are equal, sort by max weight.
            $maxDiff = $a->getWeightMax() - $b->getWeightMax();
            if ($maxDiff !== 0) {
                return $maxDiff;
            }

            // If the max weights are equal, sort by step size.
            $stepDiff = $a->getStepSize() - $b->getStepSize();
            if ($stepDiff !== 0) {
                return $stepDiff;
            }

            // If the step sizes are equal, sort by price.
            return $a->getPrice() - $b->getPrice();
        });

        // Find the weight group this shipment is in.
        foreach ($groups as $group) {
            if ($shipment->getWeight() >= $group->getWeightMin()
                || $shipment->getWeight() <= $group->getWeightMax()) {
                break;
            }
        }

        // Add the group price to the price
        $price += $group->getPrice();

        // Add any weight over the max to the price.
        if ($shipment->getWeight() > $group->getWeightMax()
            && $group->getStepSize()
            && $group->getStepPrice()) {
            $price +=
                ceil(($shipment->getWeight() - $group->getWeightMax()) / $group->getStepSize())
                * $group->getStepPrice();
        }

        return (int)$price;
    }

    /**
     * Calculate the price based on the selected options for given shipment.
     * Optionally a contract can be supplied for the price calculations. If no
     * contract is given, the contract on the shipment is used.
     *
     * @param ShipmentInterface      $shipment
     * @param ContractInterface|null $contract
     * @return int
     */
    public function calculateOptionsPrice(ShipmentInterface $shipment, ContractInterface $contract = null)
    {
        if ($contract === null) {
            $contract = $shipment->getContract();
        }

        if ($contract === null) {
            throw new MyParcelComException('Cannot calculate a price for given shipment without a contract');
        }

        $price = 0;

        $optionPrices = [];
        foreach ($contract->getOptions() as $option) {
            $optionPrices[$option->getId()] = $option->getPrice();
        }

        foreach ($shipment->getOptions() as $option) {
            $price += $optionPrices[$option->getId()];
        }

        return (int)$price;
    }

    /**
     * Calculate the price based on the desired insurance for given shipment.
     * Optionally a contract can be supplied for the price calculations. If no
     * contract is given, the contract on the shipment is used.
     *
     * @param ShipmentInterface      $shipment
     * @param ContractInterface|null $contract
     * @return int
     */
    public function calculateInsurancePrice(ShipmentInterface $shipment, ContractInterface $contract = null)
    {
        if ($contract === null) {
            $contract = $shipment->getContract();
        }

        if ($contract === null) {
            throw new MyParcelComException('Cannot calculate a price for given shipment without a contract');
        }

        if (!$shipment->getInsuranceAmount() || !($insurances = $contract->getInsurances())) {
            return 0;
        }

        usort($insurances, function (ServiceInsuranceInterface $a, ServiceInsuranceInterface $b) {
            return $a->getCovered() - $b->getCovered();
        });

        foreach ($insurances as $insurance) {
            if ($shipment->getInsuranceAmount() <= $insurance->getCovered()) {
                break;
            }
        }

        return $insurance->getPrice();
    }
}
