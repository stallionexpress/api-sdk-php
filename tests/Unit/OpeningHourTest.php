<?php

namespace MyParcelCom\Sdk\Tests\Unit;

use MyParcelCom\Sdk\Resources\OpeningHour;
use PHPUnit\Framework\TestCase;

class OpeningHourTest extends TestCase
{
    /** @test */
    public function testDay()
    {
        $openingHour = new OpeningHour();
        $this->assertEquals('Sunday', $openingHour->setDay('Sunday')->getDay());
    }

    /** @test */
    public function testOpen()
    {
        $openingHour = new OpeningHour();
        $open = new \DateTime('19:00');
        $this->assertEquals($open, $openingHour->setOpen($open)->getOpen());
    }

    /** @test */
    public function testClosed()
    {
        $openingHour = new OpeningHour();
        $closed = new \DateTime('09:00');
        $this->assertEquals($closed, $openingHour->setClosed($closed)->getClosed());
    }

    /** @test */
    public function testJsonSerialize()
    {
        $openingHour = (new OpeningHour())
            ->setDay('Sunday')
            ->setOpen(new \DateTime('09:00'))
            ->setClosed(new \DateTime('19:00'));

        $this->assertEquals([
            'day'    => 'Sunday',
            'open'   => '09:00',
            'closed' => '19:00',
        ], $openingHour->jsonSerialize());
    }
}
