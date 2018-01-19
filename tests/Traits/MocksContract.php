<?php

namespace MyParcelCom\ApiSdk\Tests\Traits;

use MyParcelCom\ApiSdk\Resources\Interfaces\ContractInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceGroupInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceInsuranceInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceOptionInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShipmentInterface;

trait MocksContract
{
    protected function getMockedContract(array $groups = [], array $insurances = [], array $options = [])
    {
        $contract = $this->getMockBuilder(ContractInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        $contract->method('getGroups')
            ->willReturn($this->getMockedGroups($groups));
        $contract->method('getInsurances')
            ->willReturn($this->getMockedInsurances($insurances));
        $contract->method('getServiceOptions')
            ->willReturn($this->getMockedOptions($options));

        return $contract;
    }

    /**
     * @param int   $weight
     * @param int   $insurance
     * @param array $options
     * @return ShipmentInterface
     */
    protected function getMockedShipment($weight = 5000, $insurance = 0, array $options = [], $contract = null)
    {
        $shipment = $this->getMockBuilder(ShipmentInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        $shipment->method('getContract')
            ->willReturn($contract);
        $shipment->method('getWeight')
            ->willReturn($weight);
        $shipment->method('getInsuranceAmount')
            ->willReturn($insurance);

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
    protected function getMockedGroups(array $groups)
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
     * @param array $insurances
     * @return ServiceInsuranceInterface[]
     */
    protected function getMockedInsurances(array $insurances)
    {
        $insuranceMockBuilder = $this->getMockBuilder(ServiceInsuranceInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes();

        $insuranceMocks = [];
        foreach ($insurances as $insurance) {
            $insuranceMock = $insuranceMockBuilder->getMock();
            $insuranceMock->method('getCovered')
                ->willReturn($insurance['covered']);
            $insuranceMock->method('getCurrency')
                ->willReturn('EUR');
            $insuranceMock->method('getPrice')
                ->willReturn($insurance['price']);
            $insuranceMocks[] = $insuranceMock;
        }

        return $insuranceMocks;
    }

    /**
     * @param array $options
     * @return ServiceOptionInterface[]
     */
    protected function getMockedOptions(array $options)
    {
        $serviceMockBuilder = $this->getMockBuilder(ServiceOptionInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes();

        $serviceMocks = [];
        foreach ($options as $option) {
            $serviceMock = $serviceMockBuilder->getMock();
            $serviceMock->method('getCurrency')
                ->willReturn('EUR');
            $serviceMock->method('getPrice')
                ->willReturn($option['price']);
            $serviceMock->method('getId')
                ->willReturn($option['id']);
            $serviceMocks[] = $serviceMock;
        }

        return $serviceMocks;
    }
}
