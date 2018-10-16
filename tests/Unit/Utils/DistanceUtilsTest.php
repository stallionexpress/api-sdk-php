<?php

namespace MyParcelCom\ApiSdk\Tests\Unit\Utils;

use MyParcelCom\ApiSdk\Exceptions\ConversionException;
use MyParcelCom\ApiSdk\Utils\DistanceUtils;
use PHPUnit\Framework\TestCase;

class DistanceUtilsTest extends TestCase
{
    /** @test */
    public function testDistanceConversion()
    {
        $initialDistance = 100;
        $expectedDistance = $initialDistance * 1000 / 1609.344;

        $convertedDistance = DistanceUtils::convertDistance($initialDistance, 'kilometers', 'miles');
        $revertedDistance = DistanceUtils::convertDistance($convertedDistance, 'miles', 'kilometers');

        $this->assertEquals($expectedDistance, $convertedDistance);
        $this->assertEquals($initialDistance, $revertedDistance);
    }

    /** @test */
    public function testConversionExceptionSourceUnit()
    {
        $this->expectException(ConversionException::class);
        DistanceUtils::convertDistance(1, 'hand', 'feet');
    }

    /** @test */
    public function testConversionExceptionDestinationUnit()
    {
        $this->expectException(ConversionException::class);
        DistanceUtils::convertDistance(1, 'feet', 'hand');
    }
}
