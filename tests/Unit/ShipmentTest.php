<?php

namespace MyParcelCom\ApiSdk\Tests\Unit;

use MyParcelCom\ApiSdk\Exceptions\MyParcelComException;
use MyParcelCom\ApiSdk\Resources\Interfaces\AddressInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ContractInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\CustomsInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\FileInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\PhysicalPropertiesInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceOptionInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShipmentStatusInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShopInterface;
use MyParcelCom\ApiSdk\Resources\Shipment;
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
    public function testTrackingCode()
    {
        $shipment = new Shipment();
        $this->assertEquals('ATRACKINGCODE', $shipment->setTrackingCode('ATRACKINGCODE')->getTrackingCode());
    }

    /** @test */
    public function testTrackingUrl()
    {
        $shipment = new Shipment();
        $this->assertEquals('https://track/ATRACKINGCODE', $shipment->setTrackingUrl('https://track/ATRACKINGCODE')->getTrackingUrl());
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
    public function testSetWeightInvalidUnit()
    {
        $shipment = new Shipment();

        $this->expectException(MyParcelComException::class);
        $shipment->setWeight(8000, 'tons');
    }

    /** @test */
    public function testGetWeightInvalidUnit()
    {
        $shipment = new Shipment();
        $shipment->setWeight(8000);

        $this->expectException(MyParcelComException::class);
        $shipment->getWeight('truckloads');
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

        $mock = $this->getMockClass(ShipmentStatusInterface::class);
        $status = new $mock();

        $this->assertEquals($status, $shipment->setStatus($status)->getStatus());
    }

    /** @test */
    public function testShop()
    {
        $shipment = new Shipment();

        $this->assertEmpty($shipment->getShop());

        $mock = $this->getMockClass(ShopInterface::class);
        $shop = new $mock();

        $this->assertEquals($shop, $shipment->setShop($shop)->getShop());
    }

    /** @test */
    public function testCustoms()
    {
        $shipment = new Shipment();

        $mock = $this->getMockClass(CustomsInterface::class);
        $customs = new $mock();

        $this->assertEquals($customs, $shipment->setCustoms($customs)->getCustoms());
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
        $label->method('getDocumentType')
            ->willReturn(FileInterface::DOCUMENT_TYPE_LABEL);

        $printcode = $this->getMockBuilder(FileInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $printcode->method('getDocumentType')
            ->willReturn(FileInterface::DOCUMENT_TYPE_PRINTCODE);

        $shipment->setFiles([$label, $printcode]);

        $this->assertCount(1, $shipment->getFiles(FileInterface::DOCUMENT_TYPE_PRINTCODE));
        $files = $shipment->getFiles(FileInterface::DOCUMENT_TYPE_PRINTCODE);
        $this->assertEquals($printcode, reset($files));

        $this->assertCount(1, $shipment->getFiles(FileInterface::DOCUMENT_TYPE_LABEL));
        $files = $shipment->getFiles(FileInterface::DOCUMENT_TYPE_LABEL);
        $this->assertEquals($label, reset($files));
    }

    /** @test */
    public function testStatusHistory()
    {
        $mock = $this->getMockClass(ShipmentStatusInterface::class);
        $statuses = [
            new $mock(),
            new $mock(),
            new $mock(),
        ];

        $shipment = new Shipment();
        $shipment->setStatusHistoryCallback(function () use ($statuses) {
            return $statuses;
        });

        $this->assertEquals($statuses, $shipment->getStatusHistory());

        $statuses = [
            new $mock(),
            new $mock(),
        ];

        $this->assertEquals($statuses, $shipment->setStatusHistory($statuses)->getStatusHistory());
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

        $status = $this->getMockBuilder(ShipmentStatusInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $status->method('jsonSerialize')
            ->willReturn([
                'id'   => 'shipment-status-id-1',
                'type' => 'shipment-statuses',
            ]);

        $customs = $this->getMockBuilder(CustomsInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $customs->method('jsonSerialize')
            ->willReturn([
                'content_type'   => 'documents',
                'invoice_number' => 'NO.5',
                'items'          => [
                    [
                        'sku'                 => '123456789',
                        'description'         => 'OnePlus X',
                        'item_value'          => [
                            'amount'   => 100,
                            'currency' => 'EUR',
                        ],
                        'quantity'            => 2,
                        'hs_code'             => '8517.12.00',
                        'origin_country_code' => 'GB',
                    ],
                ],
                'non_delivery'   => 'return',
                'incoterm'       => 'DDU',
            ]);

        $shipment = (new Shipment())
            ->setId('shipment-id')
            ->setDescription('order #012ASD')
            ->setPickupLocationCode('CODE123')
            ->setPrice(99)
            ->setInsuranceAmount(50)
            ->setCurrency('USD')
            ->setBarcode('S3BARCODE')
            ->setTrackingCode('ATRACKINGCODE')
            ->setTrackingUrl('https://tra.ck/ATRACKINGCODE')
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
            ->setPickupLocationAddress($pudoAddress)
            ->setCustoms($customs);

        $this->assertEquals([
            'id'            => 'shipment-id',
            'type'          => 'shipments',
            'attributes'    => [
                'barcode'             => 'S3BARCODE',
                'tracking_code'       => 'ATRACKINGCODE',
                'tracking_url'        => 'https://tra.ck/ATRACKINGCODE',
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
                'customs'             => [
                    'content_type'   => 'documents',
                    'invoice_number' => 'NO.5',
                    'items'          => [
                        [
                            'sku'                 => '123456789',
                            'description'         => 'OnePlus X',
                            'item_value'          => [
                                'amount'   => 100,
                                'currency' => 'EUR',
                            ],
                            'quantity'            => 2,
                            'hs_code'             => '8517.12.00',
                            'origin_country_code' => 'GB',
                        ],
                    ],
                    'non_delivery'   => 'return',
                    'incoterm'       => 'DDU',
                ],
            ],
            'relationships' => [
                'shop'     => ['data' => ['id' => 'shop-id-1', 'type' => 'shops']],
                'service'  => ['data' => ['id' => 'service-id-1', 'type' => 'services']],
                'contract' => ['data' => ['id' => 'contract-id-1', 'type' => 'contracts']],
                'status'   => ['data' => ['id' => 'shipment-status-id-1', 'type' => 'shipment-statuses']],
                'options'  => ['data' => [['id' => 'option-id-1', 'type' => 'service-options']]],
                'files'    => ['data' => [['id' => 'file-id-1', 'type' => 'files']]],
            ],
        ], $shipment->jsonSerialize());
    }
}
