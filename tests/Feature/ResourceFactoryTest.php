<?php

namespace MyParcelCom\ApiSdk\Tests\Feature;

use MyParcelCom\ApiSdk\MyParcelComApiInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\CarrierInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ContractInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\FileInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\PickUpDropOffLocationInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\RegionInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceGroupInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceInsuranceInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceOptionInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShipmentInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShopInterface;
use MyParcelCom\ApiSdk\Resources\ResourceFactory;
use PHPUnit\Framework\TestCase;

class ResourceFactoryTest extends TestCase
{
    /** @test */
    public function testCreateEmptyCarrier()
    {
        $resourceFactory = new ResourceFactory();
        $carrier = $resourceFactory->create('carriers');

        $this->assertInstanceOf(CarrierInterface::class, $carrier);
        $this->assertEquals([
            'type' => 'carriers',
        ], $carrier->jsonSerialize());
    }

    /** @test */
    public function testCreateCarrier()
    {
        $carrierAttributes = [
            'name' => 'MyParcel.com Carrier',
        ];

        $resourceFactory = new ResourceFactory();
        $carrier = $resourceFactory->create('carriers', $carrierAttributes);

        $this->assertInstanceOf(CarrierInterface::class, $carrier);
        $this->assertEquals([
            'type'       => 'carriers',
            'attributes' => $carrierAttributes,
        ], $carrier->jsonSerialize());
    }

    /** @test */
    public function testCreateEmptyContract()
    {
        $resourceFactory = new ResourceFactory();
        $contract = $resourceFactory->create('contracts');

        $this->assertInstanceOf(ContractInterface::class, $contract);
        $this->assertEquals([
            'type' => 'contracts',
        ], $contract->jsonSerialize());
    }

    /** @test */
    public function testCreateContract()
    {
        $contractAttributes = [
            'groups'     => [
                [
                    'type'       => 'service-groups',
                    'id'         => 'service-group-id',
                    'weight_min' => 0,
                    'weight_max' => 20,
                    'price'      => 100,
                    'currency'   => 'EUR',
                    'step_price' => 100,
                    'step_size'  => 1,
                ],
            ],
            'options'    => [
                [
                    'type'     => 'service-options',
                    'id'       => 'service-option-id',
                    'name'     => 'signature',
                    'price'    => 100,
                    'currency' => 'EUR',
                ],
            ],
            'insurances' => [
                [
                    'type'     => 'service-insurances',
                    'id'       => 'service-insurance-id',
                    'covered'  => 100,
                    'currency' => 'EUR',
                    'price'    => 100,
                ],
            ],
        ];

        $resourceFactory = new ResourceFactory();
        $contract = $resourceFactory->create('contracts', $contractAttributes);

        $this->assertInstanceOf(ContractInterface::class, $contract);
        $this->assertEquals([
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

    /** @test */
    public function testCreateEmptyPickUpDropOffLocation()
    {
        $resourceFactory = new ResourceFactory();
        $pudoLocation = $resourceFactory->create('pickup-dropoff-locations');

        $this->assertInstanceOf(PickUpDropOffLocationInterface::class, $pudoLocation);
        $this->assertEquals([
            'type' => 'pickup-dropoff-locations',
        ], $pudoLocation->jsonSerialize());
    }

    /** @test */
    public function testCreatePickUpDropOffLocation()
    {
        $pudoAttributes = [
            'address'       => [
                'street_1'             => 'Diagonally',
                'street_2'             => 'Apartment A',
                'street_number'        => '40',
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
            'opening_hours' => [
                [
                    'day'    => 'Monday',
                    'open'   => '12:00',
                    'closed' => '15:00',
                ],
                [
                    'day'    => 'Tuesday',
                    'open'   => '09:00',
                    'closed' => '19:00',
                ],
                [
                    'day'    => 'Wednesday',
                    'open'   => '09:00',
                    'closed' => '19:00',
                ],
                [
                    'day'    => 'Thursday',
                    'open'   => '09:00',
                    'closed' => '19:00',
                ],
                [
                    'day'    => 'Friday',
                    'open'   => '09:00',
                    'closed' => '19:00',
                ],
                [
                    'day'    => 'Saturday',
                    'open'   => '10:00',
                    'closed' => '17:00',
                ],
                [
                    'day'    => 'Sunday',
                    'open'   => '00:00',
                    'closed' => '00:00',
                ],
            ],
            'position'      => [
                'latitude'  => 1.2345,
                'longitude' => 2.34567,
                'distance'  => 5000,
                'unit'      => 'meters',
            ],
        ];

        $resourceFactory = new ResourceFactory();
        $pudoLocation = $resourceFactory->create('pickup-dropoff-locations', $pudoAttributes);

        $this->assertInstanceOf(PickUpDropOffLocationInterface::class, $pudoLocation);
        $this->assertEquals([
            'type'       => 'pickup-dropoff-locations',
            'attributes' => $pudoAttributes,
        ], $pudoLocation->jsonSerialize());
    }

    /** @test */
    public function testCreateEmptyRegion()
    {
        $resourceFactory = new ResourceFactory();
        $region = $resourceFactory->create('regions');

        $this->assertInstanceOf(RegionInterface::class, $region);
        $this->assertEquals([
            'type' => 'regions',
        ], $region->jsonSerialize());
    }

    /** @test */
    public function testCreateRegion()
    {
        $regionAttributes = [
            'country_code' => 'NL',
            'region_code'  => 'ZH',
            'currency'     => 'EUR',
            'name'         => 'Rotterdam',
        ];

        $resourceFactory = new ResourceFactory();
        $region = $resourceFactory->create('regions', $regionAttributes);

        $this->assertInstanceOf(RegionInterface::class, $region);
        $this->assertEquals([
            'type'       => 'regions',
            'attributes' => $regionAttributes,
        ], $region->jsonSerialize());
    }

    /** @test */
    public function testCreateEmptyService()
    {
        $resourceFactory = new ResourceFactory();
        $service = $resourceFactory->create('services');

        $this->assertInstanceOf(ServiceInterface::class, $service);
        $this->assertEquals([
            'type' => 'services',
        ], $service->jsonSerialize());
    }

    /** @test */
    public function testCreateService()
    {
        $resourceFactory = new ResourceFactory();
        $service = $resourceFactory->create('services', [
            'name'         => 'Easy Delivery Service',
            'package_type' => ServiceInterface::PACKAGE_TYPE_PARCEL,
            'carrier'      => [
                'id' => 'carrier-id-1',
            ],
            'region_from'  => [
                'id' => 'region-id-1',
            ],
            'region_to'    => [
                'id' => 'region-id-2',
            ],
        ]);

        $this->assertInstanceOf(ServiceInterface::class, $service);
        $this->assertEquals([
            'type'          => 'services',
            'attributes'    => [
                'name'         => 'Easy Delivery Service',
                'package_type' => ServiceInterface::PACKAGE_TYPE_PARCEL,
            ],
            'relationships' => [
                'carrier'     => [
                    'data' => [
                        'id'   => 'carrier-id-1',
                        'type' => 'carriers',
                    ],
                ],
                'region_from' => [
                    'data' => [
                        'id'   => 'region-id-1',
                        'type' => 'regions',
                    ],
                ],
                'region_to'   => [
                    'data' => [
                        'id'   => 'region-id-2',
                        'type' => 'regions',
                    ],
                ],
            ],
        ], $service->jsonSerialize());
    }

    /** @test */
    public function testCreateEmptyServiceGroup()
    {
        $resourceFactory = new ResourceFactory();
        $serviceGroup = $resourceFactory->create('service-groups');

        $this->assertInstanceOf(ServiceGroupInterface::class, $serviceGroup);
        $this->assertEquals([
            'type' => 'service-groups',
        ], $serviceGroup->jsonSerialize());
    }

    /** @test */
    public function testCreateServiceGroup()
    {
        $resourceFactory = new ResourceFactory();
        $serviceGroup = $resourceFactory->create('service-groups', [
            'price'      => 741,
            'currency'   => 'GBP',
            'step_price' => 10,
            'step_size'  => 10,
            'weight_max' => 987,
            'weight_min' => 123,
        ]);

        $this->assertInstanceOf(ServiceGroupInterface::class, $serviceGroup);
        $this->assertEquals([
            'type'       => 'service-groups',
            'attributes' => [
                'price'      => [
                    'amount'   => 741,
                    'currency' => 'GBP',
                ],
                'step_price' => [
                    'amount'   => 10,
                    'currency' => 'GBP',
                ],
                'step_size'  => 10,
                'weight'     => [
                    'max' => 987,
                    'min' => 123,
                ],
            ],
        ], $serviceGroup->jsonSerialize());
    }

    /** @test */
    public function testCreateEmptyServiceOption()
    {
        $resourceFactory = new ResourceFactory();
        $serviceOption = $resourceFactory->create('service-options');

        $this->assertInstanceOf(ServiceOptionInterface::class, $serviceOption);
        $this->assertEquals([
            'type' => 'service-options',
        ], $serviceOption->jsonSerialize());
    }

    /** @test */
    public function testCreateServiceOption()
    {
        $resourceFactory = new ResourceFactory();
        $serviceOption = $resourceFactory->create('service-options', [
            'name'     => 'Sign on delivery',
            'price'    => 55,
            'currency' => 'NOK',
        ]);

        $this->assertInstanceOf(ServiceOptionInterface::class, $serviceOption);
        $this->assertEquals([
            'type'       => 'service-options',
            'attributes' => [
                'name'  => 'Sign on delivery',
                'price' => [
                    'amount'   => 55,
                    'currency' => 'NOK',
                ],
            ],
        ], $serviceOption->jsonSerialize());
    }

    /** @test */
    public function testCreateEmptyServiceInsurance()
    {
        $resourceFactory = new ResourceFactory();
        $serviceInsurance = $resourceFactory->create('service-insurances');

        $this->assertInstanceOf(ServiceInsuranceInterface::class, $serviceInsurance);
        $this->assertEquals([
            'type' => 'service-insurances',
        ], $serviceInsurance->jsonSerialize());
    }

    /** @test */
    public function testCreateServiceInsurance()
    {
        $resourceFactory = new ResourceFactory();
        $serviceInsurance = $resourceFactory->create('service-insurances', [
            'covered'  => 10000,
            'price'    => 500,
            'currency' => 'EUR',
        ]);

        $this->assertInstanceOf(ServiceInsuranceInterface::class, $serviceInsurance);
        $this->assertEquals([
            'type'       => 'service-insurances',
            'attributes' => [
                'covered' => [
                    'amount'   => 10000,
                    'currency' => 'EUR',
                ],
                'price'   => [
                    'amount'   => 500,
                    'currency' => 'EUR',
                ],
            ],
        ], $serviceInsurance->jsonSerialize());
    }

    /** @test */
    public function testCreateEmptyShipment()
    {
        $resourceFactory = new ResourceFactory();
        $shipment = $resourceFactory->create('shipments');

        $this->assertInstanceOf(ShipmentInterface::class, $shipment);
        $this->assertEquals([
            'type' => 'shipments',
        ], $shipment->jsonSerialize());
    }

    /** @test */
    public function testCreateShipment()
    {
        $resourceFactory = new ResourceFactory();
        $shipment = $resourceFactory->create('shipments', [
            'barcode'                 => 'S3BARCODE',
            'description'             => 'order #012ASD',
            'price'                   => 99,
            'currency'                => 'USD',
            'insurance_amount'        => 50,
            'weight'                  => 8000,
            'physical_properties'     => [
                'weight' => 1000,
                'length' => 1100,
                'volume' => 1200,
                'height' => 1300,
                'width'  => 1400,
            ],
            'recipient_address'       => [
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
            'sender_address'          => [
                'street_1'             => 'Diagonally',
                'street_2'             => 'Apartment 9',
                'street_number'        => '4',
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
            'pickup_location_code'    => 'CODE123',
            'pickup_location_address' => [
                'street_1'             => 'Diagonally',
                'street_2'             => 'Apartment 41',
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
            'shop'                    => ['id' => 'shop-id-1', 'type' => 'shops'],
            'service'                 => ['id' => 'service-id-1', 'type' => 'services'],
            'contract'                => ['id' => 'contract-id-1', 'type' => 'contracts'],
            'status'                  => ['id' => 'status-id-1', 'type' => 'statuses'],
            'options'                 => [['id' => 'option-id-1', 'type' => 'service-options']],
            'files'                   => [['id' => 'file-id-1', 'type' => 'files']],
        ]);

        $this->assertInstanceOf(ShipmentInterface::class, $shipment);
        $this->assertEquals([
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
                    'street_2'             => 'Apartment 9',
                    'street_number'        => '4',
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
                        'street_2'             => 'Apartment 41',
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

    /** @test */
    public function testCreateShipmentWitCustoms()
    {
        $resourceFactory = new ResourceFactory();
        $shipment = $resourceFactory->create('shipments', [
            'barcode'                 => 'S3BARCODE',
            'description'             => 'order #012ASD',
            'price'                   => 99,
            'currency'                => 'USD',
            'insurance_amount'        => 50,
            'weight'                  => 8000,
            'physical_properties'     => [
                'weight' => 1000,
                'length' => 1100,
                'volume' => 1200,
                'height' => 1300,
                'width'  => 1400,
            ],
            'recipient_address'       => [
                'street_1'             => 'Diagonally',
                'street_2'             => 'Apartment 4',
                'street_number'        => '1',
                'street_number_suffix' => 'A',
                'postal_code'          => '1AR BR2',
                'city'                 => 'London',
                'country_code'         => 'GB',
                'first_name'           => 'Robert',
                'last_name'            => 'Drop Tables',
                'company'              => 'ACME co.',
                'email'                => 'rob@tables.com',
                'phone_number'         => '+31 (0)234 567 890',
            ],
            'sender_address'          => [
                'street_1'             => 'Diagonally',
                'street_2'             => 'Apartment 9',
                'street_number'        => '4',
                'street_number_suffix' => 'A',
                'postal_code'          => '1AR BR2',
                'city'                 => 'London',
                'country_code'         => 'NL',
                'first_name'           => 'Robert',
                'last_name'            => 'Drop Tables',
                'company'              => 'ACME co.',
                'email'                => 'rob@tables.com',
                'phone_number'         => '+31 (0)234 567 890',
            ],
            'pickup_location_code'    => 'CODE123',
            'pickup_location_address' => [
                'street_1'             => 'Diagonally',
                'street_2'             => 'Apartment 41',
                'street_number'        => '2',
                'street_number_suffix' => 'A',
                'postal_code'          => '1AR BR2',
                'city'                 => 'London',
                'country_code'         => 'GB',
                'first_name'           => 'Robert',
                'last_name'            => 'Drop Tables',
                'company'              => 'ACME co.',
                'email'                => 'rob@tables.com',
                'phone_number'         => '+31 (0)234 567 890',
            ],
            'customs'                 => [
                'content_type'   => 'documents',
                'invoice_number' => 'NO.5',
                'items'          => [
                    [
                        'sku'                 => '123456789',
                        'description'         => 'OnePlus X',
                        'item_value'          => 100,
                        'currency'            => 'GBP',
                        'quantity'            => 2,
                        'hs_code'             => '8517.12.00',
                        'origin_country_code' => 'GB',
                    ],
                    [
                        'sku'                 => '213425',
                        'description'         => 'OnePlus One',
                        'item_value'          => 200,
                        'currency'            => 'GBP',
                        'quantity'            => 3,
                        'hs_code'             => '8517.12.00',
                        'origin_country_code' => 'GB',
                    ],
                    [
                        'sku'                 => '6876',
                        'description'         => 'OnePlus Two',
                        'item_value'          => 300,
                        'currency'            => 'GBP',
                        'quantity'            => 1,
                        'hs_code'             => '8517.12.00',
                        'origin_country_code' => 'GB',
                    ],
                ],
                'non_delivery'   => 'return',
                'incoterm'       => 'DDU',
            ],
            'shop'                    => ['id' => 'shop-id-1', 'type' => 'shops'],
            'service'                 => ['id' => 'service-id-1', 'type' => 'services'],
            'contract'                => ['id' => 'contract-id-1', 'type' => 'contracts'],
            'status'                  => ['id' => 'status-id-1', 'type' => 'statuses'],
            'options'                 => [['id' => 'option-id-1', 'type' => 'service-options']],
            'files'                   => [['id' => 'file-id-1', 'type' => 'files']],
        ]);

        $this->assertInstanceOf(ShipmentInterface::class, $shipment);
        $this->assertEquals([
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
                    'country_code'         => 'GB',
                    'first_name'           => 'Robert',
                    'last_name'            => 'Drop Tables',
                    'company'              => 'ACME co.',
                    'email'                => 'rob@tables.com',
                    'phone_number'         => '+31 (0)234 567 890',
                ],
                'sender_address'      => [
                    'street_1'             => 'Diagonally',
                    'street_2'             => 'Apartment 9',
                    'street_number'        => '4',
                    'street_number_suffix' => 'A',
                    'postal_code'          => '1AR BR2',
                    'city'                 => 'London',
                    'country_code'         => 'NL',
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
                        'street_2'             => 'Apartment 41',
                        'street_number'        => '2',
                        'street_number_suffix' => 'A',
                        'postal_code'          => '1AR BR2',
                        'city'                 => 'London',
                        'country_code'         => 'GB',
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
                                'currency' => 'GBP',
                            ],
                            'quantity'            => 2,
                            'hs_code'             => '8517.12.00',
                            'origin_country_code' => 'GB',
                        ],
                        [
                            'sku'                 => '213425',
                            'description'         => 'OnePlus One',
                            'item_value'          => [
                                'amount'   => 200,
                                'currency' => 'GBP',
                            ],
                            'quantity'            => 3,
                            'hs_code'             => '8517.12.00',
                            'origin_country_code' => 'GB',
                        ],
                        [
                            'sku'                 => '6876',
                            'description'         => 'OnePlus Two',
                            'item_value'          => [
                                'amount'   => 300,
                                'currency' => 'GBP',
                            ],
                            'quantity'            => 1,
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
                'status'   => ['data' => ['id' => 'status-id-1', 'type' => 'statuses']],
                'options'  => ['data' => [['id' => 'option-id-1', 'type' => 'service-options']]],
                'files'    => ['data' => [['id' => 'file-id-1', 'type' => 'files']]],
            ],
        ], $shipment->jsonSerialize());
    }

    /** @test */
    public function testCreateEmptyShop()
    {
        $resourceFactory = new ResourceFactory();
        $shop = $resourceFactory->create('shops');

        $this->assertInstanceOf(ShopInterface::class, $shop);
        $this->assertEquals([
            'type' => 'shops',
        ], $shop->jsonSerialize());
    }

    /** @test */
    public function testCreateShop()
    {
        $resourceFactory = new ResourceFactory();
        $shop = $resourceFactory->create('shops', [
            'name'            => 'MyParcel.com Test Shop',
            'billing_address' => [
                'street_1'             => 'Diagonally',
                'street_2'             => 'Apartment 3',
                'street_number'        => '4',
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
            'return_address'  => [
                'street_1'             => 'Diagonally',
                'street_2'             => 'Apartment 1',
                'street_number'        => '4',
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
            'region'          => ['id' => 'region-id-1', 'type' => 'regions'],
        ]);

        $this->assertInstanceOf(ShopInterface::class, $shop);
        $this->assertEquals([
            'type'          => 'shops',
            'attributes'    => [
                'name'            => 'MyParcel.com Test Shop',
                'billing_address' => [
                    'street_1'             => 'Diagonally',
                    'street_2'             => 'Apartment 3',
                    'street_number'        => '4',
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
                'return_address'  => [
                    'street_1'             => 'Diagonally',
                    'street_2'             => 'Apartment 1',
                    'street_number'        => '4',
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
            'relationships' => [
                'region' => ['data' => ['id' => 'region-id-1', 'type' => 'regions']],
            ],
        ], $shop->jsonSerialize());
    }

    /** @test */
    public function testCreateEmptyFile()
    {
        $resourceFactory = new ResourceFactory();
        $file = $resourceFactory->create('files');

        $this->assertInstanceOf(FileInterface::class, $file);
        $this->assertEquals([
            'type' => 'files',
        ], $file->jsonSerialize());
    }

    /** @test */
    public function testCreateFile()
    {
        // Mock a response from the http client.
        $api = $this->getMockBuilder(MyParcelComApiInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();

        $resourceFactory = (new ResourceFactory())
            ->setMyParcelComApi($api);

        $file = $resourceFactory->create('files', [
            'id'            => 'file-id',
            'resource_type' => 'label',
            'formats'       => [
                [
                    'extension' => 'pdf',
                    'mime_type' => 'application/pdf',
                ],
                [
                    'extension' => 'png',
                    'mime_type' => 'image/png',
                ],
            ],
        ]);

        $this->assertInstanceOf(FileInterface::class, $file);
        $this->assertEquals([
            'id'         => 'file-id',
            'type'       => 'files',
            'attributes' => [
                'resource_type' => 'label',
                'formats'       => [
                    [
                        'extension' => 'pdf',
                        'mime_type' => 'application/pdf',
                    ],
                    [
                        'extension' => 'png',
                        'mime_type' => 'image/png',
                    ],
                ],
            ],
        ], $file->jsonSerialize());
    }
}
