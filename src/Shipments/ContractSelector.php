<?php

namespace MyParcelCom\Sdk\Shipments;

use MyParcelCom\Sdk\Resources\Interfaces\ContractInterface;
use MyParcelCom\Sdk\Resources\Interfaces\ShipmentInterface;

class ContractSelector
{
    /**
     * Choose the cheapest contract for given shipment.
     *
     * @param ShipmentInterface   $shipment
     * @param ContractInterface[] $contracts
     * @return ContractInterface
     */
    public function selectCheapest(ShipmentInterface $shipment, array $contracts)
    {
        $prices = [];

        $matcher = new ServiceMatcher();
        $matchingContracts = $matcher->getMatchedOptions(
            $shipment,
            $matcher->getMatchedWeightGroups($shipment, $contracts)
        );

        $calculator = new PriceCalculator();
        foreach ($matchingContracts as $contract) {
            $prices[$calculator->calculate($shipment, $contract)] = $contract;
        }

        ksort($prices);

        return reset($prices);
    }
}
