<?php

namespace MyParcelCom\ApiSdk\Tests\Traits;

use MyParcelCom\ApiSdk\Resources\Interfaces\ContractInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\PhysicalPropertiesInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceOptionInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceRateInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShipmentInterface;

trait MocksContract
{
    /**
     * @param int                      $price
     * @param ServiceOptionInterface[] $serviceOptions
     * @return ServiceRateInterface
     */
    protected function getMockedServiceRate($price, array $serviceOptions = [])
    {
        $mock = $this->getMockBuilder(ServiceRateInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $mock
            ->method('getPrice')
            ->willReturn($price);
        $mock
            ->method('getServiceOptions')
            ->willReturn($serviceOptions);

        return $mock;
    }

    /**
     * @param ServiceInterface         $service
     * @param ServiceOptionInterface[] $serviceOptions
     * @return ShipmentInterface
     */
    protected function getMockedShipment(ServiceInterface $service, array $serviceOptions = [])
    {
        $physicalPropertiesMock = $this->getMockBuilder(PhysicalPropertiesInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $physicalPropertiesMock
            ->method('getWeight')
            ->willReturn(1337);

        $contractMock = $this->getMockBuilder(ContractInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        $shipment = $this->getMockBuilder(ShipmentInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        $shipment->method('getPhysicalProperties')->willReturn($physicalPropertiesMock);
        $shipment->method('getContract')->willReturn($contractMock);
        $shipment->method('getService')->willReturn($service);
        $shipment->method('getServiceOptions')->willReturn($serviceOptions);

        return $shipment;
    }

    /**
     * @param string $id
     * @param int    $price
     * @return ServiceOptionInterface
     */
    protected function getMockedServiceOption($id, $price)
    {
        $mock = $this->getMockBuilder(ServiceOptionInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        $mock
            ->method('getId')
            ->willReturn($id);
        $mock
            ->method('getPrice')
            ->willReturn($price);

        return $mock;
    }

    /**
     * @param ServiceRateInterface|null $serviceRate
     * @return ServiceInterface
     */
    protected function getMockedService(ServiceRateInterface $serviceRate = null)
    {
        $mock = $this->getMockBuilder(ServiceInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $mock
            ->method('getServiceRates')
            ->willReturn([$serviceRate]);

        return $mock;
    }
}
