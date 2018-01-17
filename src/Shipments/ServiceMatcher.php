<?php

namespace MyParcelCom\ApiSdk\Shipments;

use MyParcelCom\ApiSdk\Exceptions\ServiceMatchingException;
use MyParcelCom\ApiSdk\Resources\Interfaces\ContractInterface;
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
        return ($weightContracts = $this->getMatchedWeightGroups($shipment, $service->getContracts()))
            && ($optionContracts = $this->getMatchedOptions($shipment, $weightContracts))
            && $this->getMatchedInsurances($shipment, $optionContracts);
    }

    /**
     * Returns a subset of the given contracts that have weight groups that
     * match the weight of the shipment.
     *
     * @param ShipmentInterface   $shipment
     * @param ContractInterface[] $contracts
     * @return ContractInterface[]
     */
    public function getMatchedWeightGroups(ShipmentInterface $shipment, array $contracts)
    {
        if ($shipment->getWeight() < 0) {
            throw new ServiceMatchingException(
                'Cannot match a service to given shipment; negative weight given'
            );
        }

        $matches = [];
        foreach ($contracts as $contract) {
            foreach ($contract->getGroups() as $group) {
                if (($group->getWeightMin() <= $shipment->getWeight()
                        && $group->getWeightMax() >= $shipment->getWeight())
                    // If weight can be added on top of the set weight group,
                    // this group matches.
                    || ($group->getStepPrice() && $group->getStepSize())) {
                    $matches[] = $contract;
                    continue 2;
                }
            }
        }

        return $matches;
    }

    /**
     * Returns a subset of the given contracts that have all the options that
     * the shipment requires.
     *
     * @param ShipmentInterface   $shipment
     * @param ContractInterface[] $contracts
     * @return ContractInterface[]
     */
    public function getMatchedOptions(ShipmentInterface $shipment, array $contracts)
    {
        $optionIds = array_map(function (ServiceOptionInterface $option) {
            return $option->getId();
        }, $shipment->getServiceOptions());

        $matches = [];
        foreach ($contracts as $contract) {
            $contractOptionIds = array_map(function (ServiceOptionInterface $option) use ($optionIds) {
                return $option->getId();
            }, $contract->getOptions());

            if (!array_diff($optionIds, $contractOptionIds)) {
                $matches[] = $contract;
            }
        }

        return $matches;
    }

    /**
     * Returns a subset of the given contracts that can cover the desired
     * insurance of the shipment.
     *
     * @param ShipmentInterface   $shipment
     * @param ContractInterface[] $contracts
     * @return ContractInterface[]
     */
    public function getMatchedInsurances(ShipmentInterface $shipment, array $contracts)
    {
        if (!$shipment->getInsuranceAmount()) {
            return $contracts;
        }

        if ($shipment->getInsuranceAmount() < 0) {
            throw new ServiceMatchingException(
                'Cannot match a service to given shipment; negative insurance amount given'
            );
        }

        return array_filter($contracts, function (ContractInterface $contract) use ($shipment) {
            foreach ($contract->getInsurances() as $insurance) {
                if ($shipment->getInsuranceAmount() <= $insurance->getCovered()) {
                    return true;
                }
            }

            return false;
        });
    }
}
