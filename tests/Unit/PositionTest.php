<?php

namespace MyParcelCom\Sdk\Tests\Unit;

use MyParcelCom\Sdk\Resources\Interfaces\Position;
use PHPUnit\Framework\TestCase;

class PositionTest extends TestCase
{
    /** @test */
    public function getLatitude()
    {
        $position = new Position();
        $this->assertEquals(2.56784948, $position->setLatitude(2.56784948)->getLatitude());
    }

    /** @test */
    public function getLongitude()
    {
        $position = new Position();
        $this->assertEquals(1.123465498, $position->setLongitude(1.123465498)->getLongitude());
    }

    /** @test */
    public function getDistance()
    {
        $position = new Position();
        $this->assertEquals(900, $position->setDistance(900)->getDistance());
    }

    /** @test */
    public function getUnit()
    {
        $position = new Position();
        $this->assertEquals('meters', $position->setUnit('meters')->getUnit());
    }

    /** @test */
    public function testJsonSerialize()
    {
        $position = (new Position())
            ->setLatitude(2.56784948)
            ->setLongitude(1.123465498)
            ->setDistance(900)
            ->setUnit('meters');

        $this->assertEquals([
            'latitude'  => 2.56784948,
            'longitude' => 1.123465498,
            'distance'  => 900,
            'unit'      => 'meters',
        ], $position->jsonSerialize());
    }
}
