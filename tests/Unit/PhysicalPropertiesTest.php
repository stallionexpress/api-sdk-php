<?php

namespace MyParcelCom\ApiSdk\Tests\Unit;

use MyParcelCom\ApiSdk\Resources\PhysicalProperties;
use PHPUnit\Framework\TestCase;

class PhysicalPropertiesTest extends TestCase
{

    /** @test */
    public function testWeight()
    {
        $physicalProperties = new PhysicalProperties();
        $this->assertEquals(1000, $physicalProperties->setWeight(1000)->getWeight());
    }

    /** @test */
    public function testLength()
    {
        $physicalProperties = new PhysicalProperties();
        $this->assertEquals(1100, $physicalProperties->setLength(1100)->getLength());
    }

    /** @test */
    public function testVolume()
    {
        $physicalProperties = new PhysicalProperties();
        $this->assertEquals(1200, $physicalProperties->setVolume(1200)->getVolume());
    }

    /** @test */
    public function testHeight()
    {
        $physicalProperties = new PhysicalProperties();
        $this->assertEquals(1300, $physicalProperties->setHeight(1300)->getHeight());
    }

    /** @test */
    public function testWidth()
    {
        $physicalProperties = new PhysicalProperties();
        $this->assertEquals(1400, $physicalProperties->setWidth(1400)->getWidth());
    }

    /** @test */
    public function testJsonSerialize()
    {
        $physicalProperties = (new PhysicalProperties())
            ->setWeight(1000)
            ->setLength(1100)
            ->setVolume(1200)
            ->setHeight(1300)
            ->setWidth(1400);

        $this->assertEquals([
            'weight' => 1000,
            'length' => 1100,
            'volume' => 1200,
            'height' => 1300,
            'width'  => 1400,
        ], $physicalProperties->jsonSerialize());
    }
}
