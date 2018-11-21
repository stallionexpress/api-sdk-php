<?php

namespace MyParcelCom\ApiSdk\Tests\Unit;

use MyParcelCom\ApiSdk\Exceptions\CalculationException;
use MyParcelCom\ApiSdk\Resources\Interfaces\ContractInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\PhysicalPropertiesInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceOptionInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceRateInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShipmentInterface;
use MyParcelCom\ApiSdk\Shipments\PriceCalculator;
use MyParcelCom\ApiSdk\Tests\Traits\MocksContract;
use PHPUnit\Framework\TestCase;

class PriceCalculatorTest extends TestCase
{
//    use MocksContract;

    /** @test */
    public function testItCalculatesTheTotalPriceOfAShipment()
    {
        // TODO: Refactor this test.
        $serviceOptionMockBuilder = $this->getMockBuilder(ServiceOptionInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes();

        $serviceOptionMockA = $serviceOptionMockBuilder->getMock();
        $serviceOptionMockA
            ->method('getId')
            ->willReturn('service-option-uno');
        $serviceOptionMockA
            ->method('getPrice')
            ->willReturn(500);

        $serviceOptionMockB = $serviceOptionMockBuilder->getMock();
        $serviceOptionMockB
            ->method('getId')
            ->willReturn('service-option-dos');
        $serviceOptionMockB
            ->method('getPrice')
            ->willReturn(250);
        $serviceOptionMocks = [$serviceOptionMockA, $serviceOptionMockB];

        $serviceRateMock = $this->getMockBuilder(ServiceRateInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $serviceRateMock
            ->method('getPrice')
            ->willReturn(5000);
        $serviceRateMock
            ->method('getServiceOptions')
            ->willReturn($serviceOptionMocks);

        $serviceMock = $this->getMockBuilder(ServiceInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $serviceMock
            ->method('getServiceRates')
            ->willReturn([$serviceRateMock]);

        $contractMock = $this->getMockBuilder(ContractInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        $physicalPropertiesMock = $this->getMockBuilder(PhysicalPropertiesInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $physicalPropertiesMock
            ->method('getWeight')
            ->willReturn(1337);

        $shipment = $this->getMockBuilder(ShipmentInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $shipment->method('getService')->willReturn($serviceMock);
        $shipment->method('getContract')->willReturn($contractMock);
        $shipment->method('getPhysicalProperties')->willReturn($physicalPropertiesMock);
        $shipment->method('getServiceOptions')->willReturn($serviceOptionMocks);

        $priceCalculator = new PriceCalculator();
        $this->assertEquals(5750, $priceCalculator->calculate($shipment));
    }

    /** @test */
    public function testItCalculatesThePriceOfAShipmentWithAGivenServiceRate()
    {
        // TODO: Add this test.
    }

    /** @test */
    public function testItCalculatesTheOptionsPriceForAShipment()
    {
        // TODO: Add this test.
    }

    /** @test */
    public function testItCalculatesTheOptionsPriceForAShipmentWithAGivenServiceRate()
    {
        // TODO: Add this test.
    }


    /** @test */
    public function testCalculateGroupPrice()
    {
        // TODO: Remove this test.
        $contract = $this->getMockedServiceContract([
            [
                'weight_min' => 0,
                'weight_max' => 5000,
                'price'      => 250,
                'step_size'  => 0,
                'step_price' => 0,
            ],
            [
                'weight_min' => 5001,
                'weight_max' => 10000,
                'price'      => 400,
                'step_size'  => 0,
                'step_price' => 0,
            ],
            [
                'weight_min' => 10001,
                'weight_max' => 20000,
                'price'      => 750,
                'step_size'  => 0,
                'step_price' => 0,
            ],
            [
                'weight_min' => 20001,
                'weight_max' => 50000,
                'price'      => 999,
                'step_size'  => 1000,
                'step_price' => 100,
            ],
        ]);

        $calculator = new PriceCalculator();

        $this->assertEquals(
            250,
            $calculator->calculateGroupPrice($this->getMockedShipment(0, [], $contract))
        );

        $this->assertEquals(
            250,
            $calculator->calculateGroupPrice($this->getMockedShipment(2000, [], $contract))
        );

        $this->assertEquals(
            400,
            $calculator->calculateGroupPrice($this->getMockedShipment(7500, [], $contract))
        );

        $this->assertEquals(
            1499,
            $calculator->calculateGroupPrice($this->getMockedShipment(55000, [], $contract))
        );

        $this->assertEquals(
            99999995999,
            $calculator->calculateGroupPrice($this->getMockedShipment(999999999999, [], $contract))
        );

        $this->expectException(CalculationException::class);
        $calculator->calculateGroupPrice($this->getMockedShipment(-5000, [], $contract));
    }

    /** @test */
    public function testCalculateGroupPriceWeightTooLow()
    {
        // TODO: Remove this test.

        $contract = $this->getMockedServiceContract([
            [
                'weight_min' => 2001,
                'weight_max' => 7500,
                'price'      => 695,
                'step_size'  => 0,
                'step_price' => 0,
            ],
        ]);

        $calculator = new PriceCalculator();

        $this->expectException(CalculationException::class);
        $calculator->calculateGroupPrice($this->getMockedShipment(1000, [], $contract));
    }

    /** @test */
    public function testCalculateGroupPriceWeightTooHigh()
    {
        // TODO: Remove this test.

        $contract = $this->getMockedServiceContract([
            [
                'weight_min' => 3500,
                'weight_max' => 3600,
                'price'      => 799,
                'step_size'  => 0,
                'step_price' => 0,
            ],
            [
                'weight_min' => 10000,
                'weight_max' => 68777,
                'price'      => 799,
                'step_size'  => 0,
                'step_price' => 0,
            ],
            [
                'weight_min' => 3601,
                'weight_max' => 9999,
                'price'      => 799,
                'step_size'  => 0,
                'step_price' => 0,
            ],
        ]);
        $calculator = new PriceCalculator();

        $this->expectException(CalculationException::class);
        $calculator->calculateGroupPrice($this->getMockedShipment(98544, [], $contract));
    }

    /** @test */
    public function testCalculateOptionsPrice()
    {
        // TODO: Remove this test.

        $contract = $this->getMockedServiceContract([], [
            [
                'id'    => 'option-a',
                'price' => 123,
            ],
            [
                'id'    => 'option-b',
                'price' => 738,
            ],
        ]);

        $calculator = new PriceCalculator();

        $this->assertEquals(
            123,
            $calculator->calculateOptionsPrice($this->getMockedShipment(50, ['option-a'], $contract))
        );
        $this->assertEquals(
            738,
            $calculator->calculateOptionsPrice($this->getMockedShipment(50, ['option-b'], $contract))
        );
        $this->assertEquals(
            861,
            $calculator->calculateOptionsPrice($this->getMockedShipment(50, ['option-a', 'option-b'], $contract))
        );
    }

    /** @test */
    public function testCalculateOptionsPriceInvalidOption()
    {
        // TODO: Remove this test.

        $contract = $this->getMockedServiceContract([], [
            [
                'id'    => 'option-a',
                'price' => 1233,
            ],
            [
                'id'    => 'option-b',
                'price' => 73,
            ],
            [
                'id'    => 'option-c',
                'price' => 789,
            ],
        ]);

        $calculator = new PriceCalculator();

        $this->expectException(CalculationException::class);
        $calculator->calculateOptionsPrice($this->getMockedShipment(50, ['option-q'], $contract));
    }

    /** @test */
    public function testCalculate()
    {
        // TODO: Remove this test.

        $contract = $this->getMockedServiceContract([
            [
                'weight_min' => 3500,
                'weight_max' => 3600,
                'price'      => 779,
                'step_size'  => 0,
                'step_price' => 0,
            ],
            [
                'weight_min' => 10000,
                'weight_max' => 68777,
                'price'      => 799,
                'step_size'  => 100000,
                'step_price' => 10000,
            ],
            [
                'weight_min' => 3601,
                'weight_max' => 9999,
                'price'      => 799,
                'step_size'  => 0,
                'step_price' => 0,
            ],
            [
                'weight_min' => 0,
                'weight_max' => 3499,
                'price'      => 12,
                'step_size'  => 0,
                'step_price' => 0,
            ],
        ], [
            [
                'id'    => 'option-a',
                'price' => 1233,
            ],
            [
                'id'    => 'option-b',
                'price' => 73,
            ],
            [
                'id'    => 'option-c',
                'price' => 789,
            ],
        ]);

        $calculator = new PriceCalculator();

        $this->assertEquals(
            73 + 799,
            $calculator->calculate($this->getMockedShipment(30012, ['option-b'], $contract))
        );

        $this->assertEquals(
            12,
            $calculator->calculate($this->getMockedShipment(30, [], $contract))
        );

        $this->assertEquals(
            779,
            $calculator->calculate($this->getMockedShipment(3515, [], $contract))
        );

        $this->assertEquals(
            799 + 1233 + 789,
            $calculator->calculate($this->getMockedShipment(9000, ['option-a', 'option-c'], $contract))
        );
    }

    /** @test */
    public function testCalculateException()
    {
        // TODO: Remove this test.

        $calculator = new PriceCalculator();
        $shipment = $this->createMock(ShipmentInterface::class);
        $shipment->method('getServiceContract')->willReturn(null);

        $this->expectException(CalculationException::class);
        $calculator->calculate($shipment);
    }

    /** @test */
    public function testCalculateGroupPriceException()
    {
        // TODO: Remove this test.

        $calculator = new PriceCalculator();
        $shipment = $this->createMock(ShipmentInterface::class);
        $shipment->method('getServiceContract')->willReturn(null);

        $this->expectException(CalculationException::class);
        $calculator->calculateGroupPrice($shipment);
    }

    /** @test */
    public function testCalculateOptionsPriceException()
    {
        // TODO: Remove this test.

        $calculator = new PriceCalculator();
        $shipment = $this->createMock(ShipmentInterface::class);
        $shipment->method('getServiceContract')->willReturn(null);

        $this->expectException(CalculationException::class);
        $calculator->calculateOptionsPrice($shipment);
    }
}
