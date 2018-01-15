<?php

namespace MyParcelCom\ApiSdk\Tests\Unit;

use MyParcelCom\ApiSdk\Exceptions\CalculationException;
use MyParcelCom\ApiSdk\Resources\Interfaces\ContractInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceGroupInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceInsuranceInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceOptionInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShipmentInterface;
use MyParcelCom\ApiSdk\Shipments\PriceCalculator;
use PHPUnit\Framework\TestCase;

class PriceCalculatorTest extends TestCase
{
    /** @var ContractInterface */
    private $contract;

    /** @var ShipmentInterface */
    private $shipment;

    protected function setUp()
    {
        parent::setUp();

        $this->contract = $this->getMockBuilder(ContractInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
    }

    /** @test */
    public function testCalculateGroupPrice()
    {
        $this->contract->method('getGroups')
            ->willReturn($this->createGroups([
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
            ]));
        $calculator = new PriceCalculator();

        $this->assertEquals(
            250,
            $calculator->calculateGroupPrice($this->createShipment(0))
        );

        $this->assertEquals(
            250,
            $calculator->calculateGroupPrice($this->createShipment(2000))
        );

        $this->assertEquals(
            400,
            $calculator->calculateGroupPrice($this->createShipment(7500))
        );

        $this->assertEquals(
            1499,
            $calculator->calculateGroupPrice($this->createShipment(55000))
        );

        $this->assertEquals(
            99999995999,
            $calculator->calculateGroupPrice($this->createShipment(999999999999))
        );

        $this->expectException(CalculationException::class);
        $calculator->calculateGroupPrice($this->createShipment(-5000));
    }

    /** @test */
    public function testCalculateGroupPriceWeightTooLow()
    {
        $this->contract->method('getGroups')
            ->willReturn($this->createGroups([
                [
                    'weight_min' => 2001,
                    'weight_max' => 7500,
                    'price'      => 695,
                    'step_size'  => 0,
                    'step_price' => 0,
                ],
            ]));
        $calculator = new PriceCalculator();

        $this->expectException(CalculationException::class);
        $calculator->calculateGroupPrice($this->createShipment(1000));
    }

    /** @test */
    public function testCalculateGroupPriceWeightTooHigh()
    {
        $this->contract->method('getGroups')
            ->willReturn($this->createGroups([
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
            ]));
        $calculator = new PriceCalculator();

        $this->expectException(CalculationException::class);
        $calculator->calculateGroupPrice($this->createShipment(98544));
    }

    /** @test */
    public function testCalculateInsurancePrice()
    {
        $this->contract->method('getInsurances')
            ->willReturn($this->createInsurances([
                [
                    'covered' => 1000,
                    'price'   => 99,
                ],
                [
                    'covered' => 2000,
                    'price'   => 500,
                ],
                [
                    'covered' => 123456,
                    'price'   => 789,
                ],
                [
                    'covered' => 100100,
                    'price'   => 600,
                ],
            ]));

        $calculator = new PriceCalculator();

        $this->assertEquals(
            99,
            $calculator->calculateInsurancePrice($this->createShipment(10, 587))
        );
        $this->assertEquals(
            500,
            $calculator->calculateInsurancePrice($this->createShipment(20, 1177))
        );
        $this->assertEquals(
            600,
            $calculator->calculateInsurancePrice($this->createShipment(30, 100000))
        );
        $this->assertEquals(
            789,
            $calculator->calculateInsurancePrice($this->createShipment(40, 123456))
        );
    }

    /** @test */
    public function testCalculateInsurancePriceTooHighInsurance()
    {
        $this->contract->method('getInsurances')
            ->willReturn($this->createInsurances([
                [
                    'covered' => 100,
                    'price'   => 101,
                ],
                [
                    'covered' => 987654,
                    'price'   => 12,
                ],
                [
                    'covered' => 13456,
                    'price'   => 79,
                ],
            ]));

        $calculator = new PriceCalculator();

        $this->expectException(CalculationException::class);
        $calculator->calculateInsurancePrice($this->createShipment(50, 99999999999999999999));
    }

    /** @test */
    public function testCalculateInsurancePriceNegativeInsurance()
    {
        $this->contract->method('getInsurances')
            ->willReturn($this->createInsurances([
                [
                    'covered' => 654,
                    'price'   => 121,
                ],
                [
                    'covered' => 1456,
                    'price'   => 739,
                ],
            ]));

        $calculator = new PriceCalculator();

        $this->expectException(CalculationException::class);
        $calculator->calculateInsurancePrice($this->createShipment(50, -124));
    }

    /** @test */
    public function testCalculateOptionsPrice()
    {
        $this->contract->method('getOptions')
            ->willReturn($this->createOptions([
                [
                    'id'    => 'option-a',
                    'price' => 123,
                ],
                [
                    'id'    => 'option-b',
                    'price' => 738,
                ],
            ]));

        $calculator = new PriceCalculator();

        $this->assertEquals(
            123,
            $calculator->calculateOptionsPrice($this->createShipment(50, 0, ['option-a']))
        );
        $this->assertEquals(
            738,
            $calculator->calculateOptionsPrice($this->createShipment(50, 0, ['option-b']))
        );
        $this->assertEquals(
            861,
            $calculator->calculateOptionsPrice($this->createShipment(50, 0, ['option-a', 'option-b']))
        );
    }

    /** @test */
    public function testCalculateOptionsPriceInvalidOption()
    {
        $this->contract->method('getOptions')
            ->willReturn($this->createOptions([
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
            ]));

        $calculator = new PriceCalculator();

        $this->expectException(CalculationException::class);
        $calculator->calculateOptionsPrice($this->createShipment(50, 0, ['option-q']));
    }

    public function testCalculate()
    {

        $this->contract->method('getOptions')
            ->willReturn($this->createOptions([
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
            ]));
        $this->contract->method('getInsurances')
            ->willReturn($this->createInsurances([
                [
                    'covered' => 654,
                    'price'   => 121,
                ],
                [
                    'covered' => 1456,
                    'price'   => 739,
                ],
            ]));
        $this->contract->method('getGroups')
            ->willReturn($this->createGroups([
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
            ]));

        $calculator = new PriceCalculator();

        $this->assertEquals(
            73 + 739 + 799,
            $calculator->calculate($this->createShipment(30012, 1245, ['option-b']))
        );

        $this->assertEquals(
            12 + 121,
            $calculator->calculate($this->createShipment(30, 100, []))
        );

        $this->assertEquals(
            779,
            $calculator->calculate($this->createShipment(3515, 0, []))
        );

        $this->assertEquals(
            799 + 121 + 1233 + 789,
            $calculator->calculate($this->createShipment(9000, 157, ['option-a', 'option-c']))
        );
    }

    /**
     * @param array $groups
     * @return ServiceGroupInterface[]
     */
    private function createGroups(array $groups)
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
     * @param int $weight
     * @return ShipmentInterface
     */
    private function createShipment($weight = 5000, $insurance = 0, array $options = [])
    {
        $shipment = $this->getMockBuilder(ShipmentInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $shipment->method('getContract')
            ->willReturn($this->contract);
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

        $shipment->method('getOptions')
            ->willReturn($optionMocks);

        return $shipment;
    }

    /**
     * @param array $insurances
     * @return ServiceInsuranceInterface[]
     */
    private function createInsurances(array $insurances)
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
    private function createOptions(array $options)
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
