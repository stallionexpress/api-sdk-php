<?php

namespace MyParcelCom\ApiSdk\Tests\Traits;

use MyParcelCom\ApiSdk\Resources\Interfaces\ContractInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceContractInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceGroupInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceOptionInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceOptionPriceInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShipmentInterface;
use MyParcelCom\ApiSdk\Resources\PhysicalProperties;

trait MocksContract
{
    protected function getMockedServiceContract(array $groups = [], array $options = [])
    {
        $contract = $this->getMockBuilder(ServiceContractInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        $contract->method('getServiceGroups')
            ->willReturn($this->getMockedServiceGroups($groups));
        $contract->method('getServiceOptionPrices')
            ->willReturn($this->getMockedServiceOptionPrices($options));

        return $contract;
    }

//    public function getMockedServiceRate(
//        $weight = 5000,
//        array $options = [],
//        ServiceInterface $service,
//        ContractInterface $contract
//    ) {
//        $serviceRateMock = $this->getMockBuilder(ServiceRateInterface::class)
//            ->disableOriginalConstructor()
//            ->disableOriginalClone()
//            ->disableArgumentCloning()
//            ->disallowMockingUnknownTypes();
//
//
//    }

    /**
     * @param int                    $weight
     * @param array                  $options
     * @param ServiceInterface|null  $service
     * @param ContractInterface|null $contract
     * @return ShipmentInterface
     */
    protected function getMockedShipment(
        $weight = 5000,
        array $options = [],
        ServiceInterface $service = null,
        ContractInterface $contract = null
    ) {
        $shipment = $this->getMockBuilder(ShipmentInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        $shipment->method('getService')
            ->willReturn($service);
        $shipment->method('getContract')
            ->willReturn($contract);
        $shipment->method('getPhysicalProperties')
            ->willReturn($this->createMock(PhysicalProperties::class)
                ->method('getWeight')
                ->willReturn($weight));

        $optionMocks = array_map(function ($option) {
            $optionMock = $this->getMockBuilder(ServiceOptionInterface::class)
                ->disableOriginalConstructor()
                ->disableOriginalClone()
                ->disableArgumentCloning()
                ->disallowMockingUnknownTypes()
                ->getMock();
            $optionMock->method('getId')
                ->willReturn($option);

            return $optionMock;
        }, $options);

        $shipment->method('getServiceOptions')
            ->willReturn($optionMocks);

        /** @var ShipmentInterface $shipment */
        return $shipment;
    }


    /**
     * @param array $groups
     * @return ServiceGroupInterface[]
     */
    protected function getMockedServiceGroups(array $groups)
    {
        $groupMockBuilder = $this->getMockBuilder(ServiceGroupInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes();

        $groupMocks = [];
        foreach ($groups as $group) {
            $groupMock = $groupMockBuilder->getMock();
            $groupMock->method('getWeightMin')
                ->willReturn($group['weight_min']);
            $groupMock->method('getWeightMax')
                ->willReturn($group['weight_max']);
            $groupMock->method('getCurrency')
                ->willReturn('EUR');
            $groupMock->method('getPrice')
                ->willReturn($group['price']);
            $groupMock->method('getStepSize')
                ->willReturn($group['step_size']);
            $groupMock->method('getStepPrice')
                ->willReturn($group['step_price']);
            $groupMocks[] = $groupMock;
        }

        return $groupMocks;
    }

    /**
     * @param array $options
     * @return ServiceOptionInterface[]
     */
    protected function getMockedServiceOptionPrices(array $options)
    {
        $serviceOptionMockBuilder = $this->getMockBuilder(ServiceOptionInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes();
        $serviceOptionPriceMockBuilder = $this->getMockBuilder(ServiceOptionPriceInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes();

        $serviceMocks = [];
        foreach ($options as $option) {
            $serviceOptionPriceMock = $serviceOptionPriceMockBuilder->getMock();
            $serviceOptionMock = $serviceOptionMockBuilder->getMock();

            $serviceOptionMock->method('getId')
                ->willReturn($option['id']);

            $serviceOptionPriceMock->method('getCurrency')
                ->willReturn('EUR');
            $serviceOptionPriceMock->method('getPrice')
                ->willReturn($option['price']);
            $serviceOptionPriceMock->method('getServiceOption')
                ->willReturn($serviceOptionMock);

            $serviceMocks[] = $serviceOptionPriceMock;
        }

        return $serviceMocks;
    }
}
