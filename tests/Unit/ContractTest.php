<?php

namespace MyParcelCom\ApiSdk\Tests\Unit;

use MyParcelCom\ApiSdk\Resources\Contract;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceGroupInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceInsuranceInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceOptionInterface;
use PHPUnit\Framework\TestCase;

class ContractTest extends TestCase
{

    /** @test */
    public function testId()
    {
        $contract = new Contract();
        $this->assertEquals('contract-id', $contract->setId('contract-id')->getId());
    }

    /** @test */
    public function testGetType()
    {
        $contract = new Contract();
        $this->assertEquals('contracts', $contract->getType());
    }

    /** @test */
    public function testGroups()
    {
        $contract = new Contract();

        $this->assertEmpty($contract->getGroups());

        $mock = $this->getMockClass(ServiceGroupInterface::class);

        $groups = [
            new $mock(),
            new $mock(),
        ];
        $contract->setGroups($groups);
        $this->assertCount(2, $contract->getGroups());
        $this->assertEquals($groups, $contract->getGroups());

        $group = new $mock();
        $contract->addGroup($group);
        $groups[] = $group;
        $this->assertCount(3, $contract->getGroups());
        $this->assertEquals($groups, $contract->getGroups());
    }

    /** @test */
    public function testOptions()
    {
        $contract = new Contract();

        $this->assertEmpty($contract->getOptions());

        $mock = $this->getMockClass(ServiceOptionInterface::class);

        $options = [
            new $mock(),
            new $mock(),
        ];
        $contract->setOptions($options);
        $this->assertCount(2, $contract->getOptions());
        $this->assertEquals($options, $contract->getOptions());

        $option = new $mock();
        $contract->addOption($option);
        $options[] = $option;
        $this->assertCount(3, $contract->getOptions());
        $this->assertEquals($options, $contract->getOptions());
    }

    /** @test */
    public function testInsurances()
    {
        $contract = new Contract();

        $this->assertEmpty($contract->getGroups());

        $mock = $this->getMockClass(ServiceInsuranceInterface::class);

        $insurances = [
            new $mock(),
            new $mock(),
        ];
        $contract->setInsurances($insurances);
        $this->assertCount(2, $contract->getInsurances());
        $this->assertEquals($insurances, $contract->getInsurances());

        $insurance = new $mock();
        $contract->addInsurance($insurance);
        $insurances[] = $insurance;
        $this->assertCount(3, $contract->getInsurances());
        $this->assertEquals($insurances, $contract->getInsurances());
    }

    /** @test */
    public function testJsonSerialize()
    {
        $groupMock = $this->getMockBuilder(ServiceGroupInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        $groupMock->method('jsonSerialize')
            ->willReturn([
                'type'       => 'service-groups',
                'id'         => 'service-group-id',
                'attributes' => [
                    'weight'     => [
                        'min' => 0,
                        'max' => 20,
                    ],
                    'price'      => [
                        'amount'   => 100,
                        'currency' => 'EUR',
                    ],
                    'step_price' => [
                        'amount'   => 100,
                        'currency' => 'EUR',
                    ],
                    'step_size'  => 1,
                ],
            ]);

        $optionMock = $this->getMockBuilder(ServiceOptionInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        $optionMock->method('jsonSerialize')
            ->willReturn([
                'type'       => 'service-options',
                'id'         => 'service-option-id',
                'attributes' => [
                    'name'  => 'signature',
                    'price' => [
                        'amount'   => 100,
                        'currency' => 'EUR',
                    ],
                ],
            ]);

        $insuranceMock = $this->getMockBuilder(ServiceInsuranceInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        $insuranceMock->method('jsonSerialize')
            ->willReturn([
                'type'       => 'service-insurances',
                'id'         => 'service-insurance-id',
                'attributes' => [
                    'covered' => [
                        'amount'   => 100,
                        'currency' => 'EUR',
                    ],
                    'price'   => [
                        'amount'   => 100,
                        'currency' => 'EUR',
                    ],
                ],
            ]);

        $contract = (new Contract())
            ->setId('contract-id')
            ->setInsurances([$insuranceMock])
            ->setGroups([$groupMock])
            ->setOptions([$optionMock]);

        $this->assertEquals([
            'id'         => 'contract-id',
            'type'       => 'contracts',
            'attributes' => [
                'groups'     => [
                    [
                        'type'       => 'service-groups',
                        'id'         => 'service-group-id',
                        'attributes' => [
                            'weight'     => [
                                'min' => 0,
                                'max' => 20,
                            ],
                            'price'      => [
                                'amount'   => 100,
                                'currency' => 'EUR',
                            ],
                            'step_price' => [
                                'amount'   => 100,
                                'currency' => 'EUR',
                            ],
                            'step_size'  => 1,
                        ],
                    ],
                ],
                'options'    => [
                    [
                        'type'       => 'service-options',
                        'id'         => 'service-option-id',
                        'attributes' => [
                            'name'  => 'signature',
                            'price' => [
                                'amount'   => 100,
                                'currency' => 'EUR',
                            ],
                        ],
                    ],
                ],
                'insurances' => [
                    [
                        'type'       => 'service-insurances',
                        'id'         => 'service-insurance-id',
                        'attributes' => [
                            'covered' => [
                                'amount'   => 100,
                                'currency' => 'EUR',
                            ],
                            'price'   => [
                                'amount'   => 100,
                                'currency' => 'EUR',
                            ],
                        ],
                    ],
                ],
            ],
        ], $contract->jsonSerialize());
    }
}
