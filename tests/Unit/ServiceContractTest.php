<?php

namespace MyParcelCom\ApiSdk\Tests\Unit;

use MyParcelCom\ApiSdk\Resources\Interfaces\ContractInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceGroupInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceOptionPriceInterface;
use MyParcelCom\ApiSdk\Resources\ServiceContract;
use PHPUnit\Framework\TestCase;

class ServiceContractTest extends TestCase
{

    /** @test */
    public function testId()
    {
        $serviceContract = new ServiceContract();
        $this->assertEquals('contract-id', $serviceContract->setId('contract-id')->getId());
    }

    /** @test */
    public function testGetType()
    {
        $serviceContract = new ServiceContract();
        $this->assertEquals('service-contracts', $serviceContract->getType());
    }

    /** @test */
    public function testService()
    {
        $serviceContract = new ServiceContract();

        $mock = $this->getMockClass(ServiceInterface::class);
        $service = new $mock();

        $this->assertEquals($service, $serviceContract->setService($service)->getService());
    }

    /** @test */
    public function testContract()
    {
        $serviceContract = new ServiceContract();

        $mock = $this->getMockClass(ContractInterface::class);
        $contract = new $mock();

        $this->assertEquals($contract, $serviceContract->setContract($contract)->getContract());
    }

    /** @test */
    public function testServiceGroups()
    {
        $serviceContract = new ServiceContract();

        $this->assertEmpty($serviceContract->getServiceGroups());

        $mock = $this->getMockClass(ServiceGroupInterface::class);

        $groups = [
            new $mock(),
            new $mock(),
        ];
        $serviceContract->setServiceGroups($groups);
        $this->assertCount(2, $serviceContract->getServiceGroups());
        $this->assertEquals($groups, $serviceContract->getServiceGroups());

        $group = new $mock();
        $serviceContract->addServiceGroup($group);
        $groups[] = $group;
        $this->assertCount(3, $serviceContract->getServiceGroups());
        $this->assertEquals($groups, $serviceContract->getServiceGroups());
    }

    /** @test */
    public function testServiceOptionPrices()
    {
        $serviceContract = new ServiceContract();

        $this->assertEmpty($serviceContract->getServiceOptionPrices());

        $mock = $this->getMockClass(ServiceOptionPriceInterface::class);

        $options = [
            new $mock(),
            new $mock(),
        ];
        $serviceContract->setServiceOptionPrices($options);
        $this->assertCount(2, $serviceContract->getServiceOptionPrices());
        $this->assertEquals($options, $serviceContract->getServiceOptionPrices());

        $option = new $mock();
        $serviceContract->addServiceOptionPrice($option);
        $options[] = $option;
        $this->assertCount(3, $serviceContract->getServiceOptionPrices());
        $this->assertEquals($options, $serviceContract->getServiceOptionPrices());
    }

    /** @test */
    public function testJsonSerialize()
    {
        $contractMock = $this->getMockBuilder(ContractInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        $contractMock->method('jsonSerialize')
            ->willReturn([
                'type' => 'contracts',
                'id'   => 'contract-id',
            ]);

        $serviceMock = $this->getMockBuilder(ServiceInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        $serviceMock->method('jsonSerialize')
            ->willReturn([
                'type' => 'services',
                'id'   => 'service-id',
            ]);

        $groupMock = $this->getMockBuilder(ServiceGroupInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        $groupMock->method('jsonSerialize')
            ->willReturn([
                'type' => 'service-groups',
                'id'   => 'service-group-id',
            ]);

        $optionMock = $this->getMockBuilder(ServiceOptionPriceInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        $optionMock->method('jsonSerialize')
            ->willReturn([
                'type' => 'service-options',
                'id'   => 'service-option-id',
            ]);

        $serviceContract = (new ServiceContract())
            ->setId('contract-id')
            ->setService($serviceMock)
            ->setContract($contractMock)
            ->setServiceGroups([$groupMock])
            ->setServiceOptionPrices([$optionMock]);

        $this->assertEquals([
            'id'            => 'contract-id',
            'type'          => 'service-contracts',
            'relationships' => [
                'service'               => [
                    'data' => [
                        'type' => 'services',
                        'id'   => 'service-id',
                    ],
                ],
                'contract'      => [
                    'data' => [
                        'type' => 'contracts',
                        'id'   => 'contract-id',
                    ],
                ],
                'service_groups'        => [
                    'data' => [
                        [
                            'type' => 'service-groups',
                            'id'   => 'service-group-id',
                        ],
                    ],
                ],
                'service_option_prices' => [
                    'data' => [
                        [
                            'type' => 'service-options',
                            'id'   => 'service-option-id',
                        ],
                    ],
                ],
            ],
        ], $serviceContract->jsonSerialize());
    }
}
