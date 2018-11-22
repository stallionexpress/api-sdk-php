<?php

namespace MyParcelCom\ApiSdk\Tests\Unit;

use MyParcelCom\ApiSdk\Exceptions\CalculationException;
use MyParcelCom\ApiSdk\Shipments\PriceCalculator;
use MyParcelCom\ApiSdk\Tests\Traits\MocksContract;
use PHPUnit\Framework\TestCase;

class PriceCalculatorTest extends TestCase
{
    use MocksContract;

    /** @test */
    public function testItCalculatesTheTotalPriceOfAShipment()
    {
        $serviceOptionMocks = [
            $this->getMockedServiceOption('service-option-id-uno', 250),
            $this->getMockedServiceOption('service-option-id-dos', 850),
        ];
        $serviceRateMock = $this->getMockedServiceRate(5000, $serviceOptionMocks);
        $serviceMock = $this->getMockedService($serviceRateMock);
        $shipment = $this->getMockedShipment($serviceMock, $serviceOptionMocks);

        $priceCalculator = new PriceCalculator();
        $this->assertEquals(6100, $priceCalculator->calculate($shipment));
    }

    /** @test */
    public function testItCalculatesTheOptionsPriceForAShipment()
    {
        $serviceOptionMocks = [
            $this->getMockedServiceOption('service-option-id-uno', 250),
            $this->getMockedServiceOption('service-option-id-dos', 850),
        ];
        $serviceRateMock = $this->getMockedServiceRate(5000, $serviceOptionMocks);
        $serviceMock = $this->getMockedService($serviceRateMock);
        $shipment = $this->getMockedShipment($serviceMock, $serviceOptionMocks);

        $priceCalculator = new PriceCalculator();
        $this->assertEquals(1100, $priceCalculator->calculateOptionsPrice($shipment));
    }

    /** @test */
    public function testItTreatsNullPriceAsZero()
    {
        $serviceOptionMocks = [
            $this->getMockedServiceOption('service-option-id-uno', 250),
            $this->getMockedServiceOption('service-option-id-dos', 400),
            $this->getMockedServiceOption('service-option-id-tres', null),
        ];
        $serviceRateMock = $this->getMockedServiceRate(5000, $serviceOptionMocks);
        $serviceMock = $this->getMockedService($serviceRateMock);
        $shipment = $this->getMockedShipment($serviceMock, $serviceOptionMocks);

        $priceCalculator = new PriceCalculator();
        $this->assertEquals(5650, $priceCalculator->calculate($shipment));
    }

    /** @test */
    public function testItCalculatesThePriceOfShipmentsWithoutServiceOptions()
    {
        $serviceRateMock = $this->getMockedServiceRate(3914);
        $serviceMock = $this->getMockedService($serviceRateMock);
        $shipment = $this->getMockedShipment($serviceMock);

        $priceCalculator = new PriceCalculator();
        $this->assertEquals(3914, $priceCalculator->calculate($shipment));
    }

    /** @test */
    public function testItThrowsAnExceptionWhenNoServiceRateCanBeMatchedForShipment()
    {
        $serviceMock = $this->getMockedService(null);
        $shipment = $this->getMockedShipment($serviceMock);

        $priceCalculator = new PriceCalculator();

        $this->expectException(CalculationException::class);
        $this->expectExceptionMessage('Cannot find a matching service rate for given shipment');
        $priceCalculator->calculate($shipment);
    }

    /** @test */
    public function testItThrowsAnExceptionWhenShipmentHasInvalidOptions()
    {
        $serviceOptionMocks = [
            $this->getMockedServiceOption('service-option-id-uno', 250),
            $this->getMockedServiceOption('service-option-id-dos', 850),
        ];
        $serviceRateMock = $this->getMockedServiceRate(5000);
        $serviceMock = $this->getMockedService($serviceRateMock);

        $shipment = $this->getMockedShipment($serviceMock, $serviceOptionMocks);

        $priceCalculator = new PriceCalculator();

        $this->expectException(CalculationException::class);
        $this->expectExceptionMessage('Cannot calculate a price for given shipment; invalid option: ');
        $priceCalculator->calculate($shipment);
    }
}
