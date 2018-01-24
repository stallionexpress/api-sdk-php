<?php

namespace MyParcelCom\ApiSdk\Tests\Unit;

use MyParcelCom\ApiSdk\Resources\ServiceOption;
use PHPUnit\Framework\TestCase;

class ServiceOptionTest extends TestCase
{
    /** @test */
    public function testId()
    {
        $option = new ServiceOption();
        $this->assertEquals('service-option-id', $option->setId('service-option-id')->getId());
    }

    /** @test */
    public function testType()
    {
        $option = new ServiceOption();
        $this->assertEquals('service-options', $option->getType());
    }

    /** @test */
    public function testName()
    {
        $option = new ServiceOption();
        $this->assertEquals('Sign on delivery', $option->setName('Sign on delivery')->getName());
    }

    /** @test */
    public function testPrice()
    {
        $option = new ServiceOption();
        $this->assertEquals(55, $option->setPrice(55)->getPrice());
    }

    /** @test */
    public function testCurrency()
    {
        $option = new ServiceOption();
        $this->assertEquals('NOK', $option->setCurrency('NOK')->getCurrency());
    }

    /** @test */
    public function testCode()
    {
        $option = new ServiceOption();
        $this->assertEquals('some-code', $option->setCode('some-code')->getCode());
    }

    /** @test */
    public function testCategory()
    {
        $option = new ServiceOption();
        $this->assertEquals('some-category', $option->setCategory('some-category')->getCategory());
    }

    /** @test */
    public function testJsonSerialize()
    {
        $option = (new ServiceOption())
            ->setId('service-option-id')
            ->setName('Sign on delivery')
            ->setPrice(55)
            ->setCurrency('NOK')
            ->setCode('some-code')
            ->setCategory('some-category');

        $this->assertEquals([
            'id'         => 'service-option-id',
            'type'       => 'service-options',
            'attributes' => [
                'name'     => 'Sign on delivery',
                'price'    => [
                    'amount'   => 55,
                    'currency' => 'NOK',
                ],
                'code'     => 'some-code',
                'category' => 'some-category',
            ],
        ], $option->jsonSerialize());
    }
}
