<?php

namespace MyParcelCom\Sdk\Shipments;

use MyParcelCom\Sdk\Exceptions\InvalidResourceException;
use MyParcelCom\Sdk\Resources\Interfaces\AddressInterface;
use MyParcelCom\Sdk\Resources\Interfaces\ContractInterface;
use MyParcelCom\Sdk\Resources\Interfaces\RegionInterface;
use MyParcelCom\Sdk\Resources\Interfaces\ServiceInterface;
use MyParcelCom\Sdk\Resources\Interfaces\ServiceOptionInterface;
use MyParcelCom\Sdk\Resources\Interfaces\ShipmentInterface;

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
        return $this->matchesRegion($shipment, $service)
            && ($weightContracts = $this->getMatchedWeightGroups($shipment, $service->getContracts()))
            && ($optionContracts = $this->getMatchedOptions($shipment, $weightContracts))
            && $this->getMatchedInsurances($shipment, $optionContracts);
    }

    /**
     * Returns true if the sender and and recipient address on the shipment
     * match the regions from and to on the service.
     *
     * @param ShipmentInterface $shipment
     * @param ServiceInterface  $service
     * @return bool
     */
    public function matchesRegion(ShipmentInterface $shipment, ServiceInterface $service)
    {
        if ($shipment->getRecipientAddress() === null) {
            throw new InvalidResourceException(
                'Missing `recipient_address` on `shipments` resource'
            );
        }
        if ($shipment->getSenderAddress() === null) {
            throw new InvalidResourceException(
                'Missing `sender_address` on `shipments` resource'
            );
        }

        return $this->addressMatchesRegion($shipment->getRecipientAddress(), $service->getRegionTo())
            && $this->addressMatchesRegion($shipment->getSenderAddress(), $service->getRegionFrom());
    }

    /**
     * Returns true if the address matches given region.
     *
     * @param AddressInterface $address
     * @param RegionInterface  $region
     * @return bool
     */
    private function addressMatchesRegion(AddressInterface $address, RegionInterface $region)
    {
        // TODO use child regions from given region to match on
        return $address->getCountryCode() === $region->getCountryCode()
            && $address->getRegionCode() === $region->getRegionCode();
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
        $matches = [];
        foreach ($contracts as $contract) {
            foreach ($contract->getGroups() as $group) {
                if ($group->getWeightMin() <= $shipment->getWeight()
                    && $group->getWeightMax() >= $shipment->getWeight()) {
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
        }, $shipment->getOptions());

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
