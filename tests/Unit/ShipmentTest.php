<?php

namespace MyParcelCom\Sdk\Tests\Unit;

use MyParcelCom\Sdk\Resources\Interfaces\AddressInterface;
use MyParcelCom\Sdk\Resources\Interfaces\ContractInterface;
use MyParcelCom\Sdk\Resources\Interfaces\FileInterface;
use MyParcelCom\Sdk\Resources\Interfaces\PhysicalPropertiesInterface;
use MyParcelCom\Sdk\Resources\Interfaces\ServiceInterface;
use MyParcelCom\Sdk\Resources\Interfaces\ServiceOptionInterface;
use MyParcelCom\Sdk\Resources\Interfaces\ShipmentInterface;
use MyParcelCom\Sdk\Resources\Interfaces\ShopInterface;
use MyParcelCom\Sdk\Resources\Interfaces\StatusInterface;
use MyParcelCom\Sdk\Resources\Shipment;
use PHPUnit\Framework\TestCase;

class ShipmentTest extends TestCase
{
    /** @test */
    public function testId()
    {
        $shipment = new Shipment();
        $this->assertEquals('shipment-id', $shipment->setId('shipment-id')->getId());
    }

    /** @test */
    public function testType()
    {
        $shipment = new Shipment();
        $this->assertEquals('shipments', $shipment->getType());
    }

    /** @test */
    public function testRecipientAddress()
    {
        $shipment = new Shipment();

        $mock = $this->getMockClass(AddressInterface::class);
        $address = new $mock();

        $this->assertEquals($address, $shipment->setRecipientAddress($address)->getRecipientAddress());
    }

    /** @test */
    public function testSenderAddress()
    {
        $shipment = new Shipment();

        $mock = $this->getMockClass(AddressInterface::class);
        $address = new $mock();

        $this->assertEquals($address, $shipment->setSenderAddress($address)->getSenderAddress());
    }

    /** @test */
    public function testPickupLocationCode()
    {
        $shipment = new Shipment();
        $this->assertEquals('CODE123', $shipment->setPickupLocationCode('CODE123')->getPickupLocationCode());
    }

    /** @test */
    public function testPickupLocationAddress()
    {
        $shipment = new Shipment();

        $mock = $this->getMockClass(AddressInterface::class);
        $address = new $mock();

        $this->assertEquals($address, $shipment->setPickupLocationAddress($address)->getPickupLocationAddress());
    }

    /** @test */
    public function testDescription()
    {
        $shipment = new Shipment();
        $this->assertEquals('order #012ASD', $shipment->setDescription('order #012ASD')->getDescription());
    }

    /** @test */
    public function testPrice()
    {
        $shipment = new Shipment();
        $this->assertEquals(99, $shipment->setPrice(99)->getPrice());
    }

    /** @test */
    public function testInsuranceAmount()
    {
        $shipment = new Shipment();
        $this->assertEquals(50, $shipment->setInsuranceAmount(50)->getInsuranceAmount());
    }

    /** @test */
    public function testCurrency()
    {
        $shipment = new Shipment();
        $this->assertEquals('USD', $shipment->setCurrency('USD')->getCurrency());
    }

    /** @test */
    public function testBarcode()
    {
        $shipment = new Shipment();
        $this->assertEquals('S3BARCODE', $shipment->setBarcode('S3BARCODE')->getBarcode());
    }

    /** @test */
    public function testWeight()
    {
        $shipment = new Shipment();
        $this->assertEquals(8000, $shipment->setWeight(8000)->getWeight());
        $this->assertEquals(500, $shipment->setWeight(500, Shipment::WEIGHT_GRAM)->getWeight());
        $this->assertEquals(3000, $shipment->setWeight(3, Shipment::WEIGHT_KILOGRAM)->getWeight());
        $this->assertEquals(1701, $shipment->setWeight(60, Shipment::WEIGHT_OUNCE)->getWeight());
        $this->assertEquals(2268, $shipment->setWeight(5, Shipment::WEIGHT_POUND)->getWeight());
        $this->assertEquals(12701, $shipment->setWeight(2, Shipment::WEIGHT_STONE)->getWeight());
        $this->assertEquals(500, $shipment->setWeight(500)->getWeight(Shipment::WEIGHT_GRAM));
        $this->assertEquals(3, $shipment->setWeight(3000)->getWeight(Shipment::WEIGHT_KILOGRAM));
        $this->assertEquals(60, $shipment->setWeight(1701)->getWeight(Shipment::WEIGHT_OUNCE));
        $this->assertEquals(5, $shipment->setWeight(2268)->getWeight(Shipment::WEIGHT_POUND));
        $this->assertEquals(2, $shipment->setWeight(12701)->getWeight(Shipment::WEIGHT_STONE));
    }

    /** @test */
    public function testPhysicalProperties()
    {
        $shipment = new Shipment();

        $mock = $this->getMockClass(PhysicalPropertiesInterface::class);
        $physicalProperties = new $mock();

        $this->assertEquals($physicalProperties, $shipment->setPhysicalProperties($physicalProperties)->getPhysicalProperties());
    }

    /** @test */
    public function testService()
    {
        $shipment = new Shipment();

        $mock = $this->getMockClass(ServiceInterface::class);
        $service = new $mock();

        $this->assertEquals($service, $shipment->setService($service)->getService());
    }

    /** @test */
    public function testOptions()
    {
        $shipment = new Shipment();

        $this->assertEmpty($shipment->getOptions());

        $mock = $this->getMockClass(ServiceOptionInterface::class);

        $options = [
            new $mock(),
            new $mock(),
        ];
        $shipment->setOptions($options);
        $this->assertCount(2, $shipment->getOptions());
        $this->assertEquals($options, $shipment->getOptions());

        $option = new $mock();
        $shipment->addOption($option);
        $options[] = $option;
        $this->assertCount(3, $shipment->getOptions());
        $this->assertEquals($options, $shipment->getOptions());
    }

    /** @test */
    public function testContract()
    {
        $shipment = new Shipment();

        $mock = $this->getMockClass(ContractInterface::class);
        $contract = new $mock();

        $this->assertEquals($contract, $shipment->setContract($contract)->getContract());
    }

    /** @test */
    public function testStatus()
    {
        $shipment = new Shipment();

        $this->assertEmpty($shipment->getStatus());

        $mock = $this->getMockClass(StatusInterface::class);
        $status = new $mock();

        $this->assertEquals($status, $shipment->setStatus($status)->getStatus());
    }

    /** @test */
    public function testFiles()
    {
        $shipment = new Shipment();

        $this->assertEmpty($shipment->getFiles());

        $mock = $this->getMockClass(FileInterface::class);

        $files = [
            new $mock(),
            new $mock(),
        ];
        $shipment->setFiles($files);
        $this->assertCount(2, $shipment->getFiles());
        $this->assertEquals($files, $shipment->getFiles());

        $file = new $mock();
        $shipment->addFile($file);
        $files[] = $file;
        $this->assertCount(3, $shipment->getFiles());
        $this->assertEquals($files, $shipment->getFiles());
    }

    /** @test */
    public function testGetFilesByType()
    {
        $shipment = new Shipment();

        $label = $this->getMockBuilder(FileInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $label->method('getResourceType')
            ->willReturn(FileInterface::RESOURCE_TYPE_LABEL);

        $printcode = $this->getMockBuilder(FileInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $printcode->method('getResourceType')
            ->willReturn(FileInterface::RESOURCE_TYPE_PRINTCODE);

        $shipment->setFiles([$label, $printcode]);

        $this->assertCount(1, $shipment->getFiles(FileInterface::RESOURCE_TYPE_PRINTCODE));
        $this->assertEquals($printcode, reset($shipment->getFiles(FileInterface::RESOURCE_TYPE_PRINTCODE)));

        $this->assertCount(1, $shipment->getFiles(FileInterface::RESOURCE_TYPE_LABEL));
        $this->assertEquals($label, reset($shipment->getFiles(FileInterface::RESOURCE_TYPE_LABEL)));
    }

    /** @test */
    public function testJsonSerialize()
    {
        $recipientAddress = $this->getMockBuilder(AddressInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $recipientAddress->method('jsonSerialize')
            ->willReturn([
                'street_1'             => 'Diagonally',
                'street_2'             => 'Apartment 4',
                'street_number'        => '1',
                'street_number_suffix' => 'A',
                'postal_code'          => '1AR BR2',
                'city'                 => 'London',
                'region_code'          => 'NH',
                'country_code'         => 'AF',
                'first_name'           => 'Robert',
                'last_name'            => 'Drop Tables',
                'company'              => 'ACME co.',
                'email'                => 'rob@tables.com',
                'phone_number'         => '+31 (0)234 567 890',
            ]);

        $senderAddress = $this->getMockBuilder(AddressInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $senderAddress->method('jsonSerialize')
            ->willReturn([
                'street_1'             => 'Diagonally',
                'street_2'             => 'Apartment 4',
                'street_number'        => '2',
                'street_number_suffix' => 'A',
                'postal_code'          => '1AR BR2',
                'city'                 => 'London',
                'region_code'          => 'NH',
                'country_code'         => 'AF',
                'first_name'           => 'Robert',
                'last_name'            => 'Drop Tables',
                'company'              => 'ACME co.',
                'email'                => 'rob@tables.com',
                'phone_number'         => '+31 (0)234 567 890',
            ]);

        $pudoAddress = $this->getMockBuilder(AddressInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $pudoAddress->method('jsonSerialize')
            ->willReturn([
                'street_1'             => 'Diagonally',
                'street_2'             => 'Apartment 4',
                'street_number'        => '3',
                'street_number_suffix' => 'A',
                'postal_code'          => '1AR BR2',
                'city'                 => 'London',
                'region_code'          => 'NH',
                'country_code'         => 'AF',
                'first_name'           => 'Robert',
                'last_name'            => 'Drop Tables',
                'company'              => 'ACME co.',
                'email'                => 'rob@tables.com',
                'phone_number'         => '+31 (0)234 567 890',
            ]);

        $file = $this->getMockBuilder(FileInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $file->method('jsonSerialize')
            ->willReturn([
                'id'   => 'file-id-1',
                'type' => 'files',
            ]);

        $option = $this->getMockBuilder(ServiceOptionInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $option->method('jsonSerialize')
            ->willReturn([
                'id'   => 'option-id-1',
                'type' => 'service-options',
            ]);

        $service = $this->getMockBuilder(ServiceInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $service->method('jsonSerialize')
            ->willReturn([
                'id'   => 'service-id-1',
                'type' => 'services',
            ]);

        $contract = $this->getMockBuilder(ContractInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $contract->method('jsonSerialize')
            ->willReturn([
                'id'   => 'contract-id-1',
                'type' => 'contracts',
            ]);

        $shop = $this->getMockBuilder(ShopInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $shop->method('jsonSerialize')
            ->willReturn([
                'id'   => 'shop-id-1',
                'type' => 'shops',
            ]);

        $physicalProperties = $this->getMockBuilder(PhysicalPropertiesInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $physicalProperties->method('jsonSerialize')
            ->willReturn([
                'weight' => 1000,
                'length' => 1100,
                'volume' => 1200,
                'height' => 1300,
                'width'  => 1400,
            ]);

        $status = $this->getMockBuilder(StatusInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $status->method('jsonSerialize')
            ->willReturn([
                'id'   => 'status-id-1',
                'type' => 'statuses',
            ]);

        $shipment = (new Shipment())
            ->setId('shipment-id')
            ->setDescription('order #012ASD')
            ->setPickupLocationCode('CODE123')
            ->setPrice(99)
            ->setInsuranceAmount(50)
            ->setCurrency('USD')
            ->setBarcode('S3BARCODE')
            ->setWeight(8000)
            ->setPhysicalProperties($physicalProperties)
            ->setShop($shop)
            ->setService($service)
            ->setOptions([$option])
            ->setFiles([$file])
            ->setContract($contract)
            ->setStatus($status)
            ->setRecipientAddress($recipientAddress)
            ->setSenderAddress($senderAddress)
            ->setPickupLocationAddress($pudoAddress);

        $this->assertEquals([
            'id'            => 'shipment-id',
            'type'          => 'shipments',
            'attributes'    => [
                'barcode'             => 'S3BARCODE',
                'description'         => 'order #012ASD',
                'price'               => [
                    'amount'   => 99,
                    'currency' => 'USD',
                ],
                'insurance'           => [
                    'amount'   => 50,
                    'currency' => 'USD',
                ],
                'weight'              => 8000,
                'physical_properties' => [
                    'weight' => 1000,
                    'length' => 1100,
                    'volume' => 1200,
                    'height' => 1300,
                    'width'  => 1400,
                ],
                'recipient_address'   => [
                    'street_1'             => 'Diagonally',
                    'street_2'             => 'Apartment 4',
                    'street_number'        => '1',
                    'street_number_suffix' => 'A',
                    'postal_code'          => '1AR BR2',
                    'city'                 => 'London',
                    'region_code'          => 'NH',
                    'country_code'         => 'AF',
                    'first_name'           => 'Robert',
                    'last_name'            => 'Drop Tables',
                    'company'              => 'ACME co.',
                    'email'                => 'rob@tables.com',
                    'phone_number'         => '+31 (0)234 567 890',
                ],
                'sender_address'      => [
                    'street_1'             => 'Diagonally',
                    'street_2'             => 'Apartment 4',
                    'street_number'        => '2',
                    'street_number_suffix' => 'A',
                    'postal_code'          => '1AR BR2',
                    'city'                 => 'London',
                    'region_code'          => 'NH',
                    'country_code'         => 'AF',
                    'first_name'           => 'Robert',
                    'last_name'            => 'Drop Tables',
                    'company'              => 'ACME co.',
                    'email'                => 'rob@tables.com',
                    'phone_number'         => '+31 (0)234 567 890',
                ],
                'pickup_location'     => [
                    'code'    => 'CODE123',
                    'address' => [
                        'street_1'             => 'Diagonally',
                        'street_2'             => 'Apartment 4',
                        'street_number'        => '3',
                        'street_number_suffix' => 'A',
                        'postal_code'          => '1AR BR2',
                        'city'                 => 'London',
                        'region_code'          => 'NH',
                        'country_code'         => 'AF',
                        'first_name'           => 'Robert',
                        'last_name'            => 'Drop Tables',
                        'company'              => 'ACME co.',
                        'email'                => 'rob@tables.com',
                        'phone_number'         => '+31 (0)234 567 890',
                    ],
                ],
            ],
            'relationships' => [
                'shop'     => ['data' => ['id' => 'shop-id-1', 'type' => 'shops']],
                'service'  => ['data' => ['id' => 'service-id-1', 'type' => 'services']],
                'contract' => ['data' => ['id' => 'contract-id-1', 'type' => 'contracts']],
                'status'   => ['data' => ['id' => 'status-id-1', 'type' => 'statuses']],
                'options'  => ['data' => [['id' => 'option-id-1', 'type' => 'service-options']]],
                'files'    => ['data' => [['id' => 'file-id-1', 'type' => 'files']]],
            ],
        ], $shipment->jsonSerialize());
    }
}
