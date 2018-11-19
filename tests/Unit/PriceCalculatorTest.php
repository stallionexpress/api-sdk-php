<?php

namespace MyParcelCom\ApiSdk\Tests\Unit;

use MyParcelCom\ApiSdk\Exceptions\CalculationException;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShipmentInterface;
use MyParcelCom\ApiSdk\Shipments\PriceCalculator;
use MyParcelCom\ApiSdk\Tests\Traits\MocksContract;
use PHPUnit\Framework\TestCase;

class PriceCalculatorTest extends TestCase
{
    use MocksContract;

    // TODO: Fix!
    /** @test */
    public function testCalculateGroupPrice()
    {
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
        $calculator = new PriceCalculator();
        $shipment = $this->createMock(ShipmentInterface::class);
        $shipment->method('getServiceContract')->willReturn(null);

        $this->expectException(CalculationException::class);
        $calculator->calculate($shipment);
    }

    /** @test */
    public function testCalculateGroupPriceException()
    {
        $calculator = new PriceCalculator();
        $shipment = $this->createMock(ShipmentInterface::class);
        $shipment->method('getServiceContract')->willReturn(null);

        $this->expectException(CalculationException::class);
        $calculator->calculateGroupPrice($shipment);
    }

    /** @test */
    public function testCalculateOptionsPriceException()
    {
        $calculator = new PriceCalculator();
        $shipment = $this->createMock(ShipmentInterface::class);
        $shipment->method('getServiceContract')->willReturn(null);

        $this->expectException(CalculationException::class);
        $calculator->calculateOptionsPrice($shipment);
    }
}
