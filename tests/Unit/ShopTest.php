<?php

namespace MyParcelCom\Sdk\Tests\Unit;

use MyParcelCom\Sdk\Resources\Interfaces\AddressInterface;
use MyParcelCom\Sdk\Resources\Interfaces\RegionInterface;
use MyParcelCom\Sdk\Resources\Shop;
use PHPUnit\Framework\TestCase;

class ShopTest extends TestCase
{
    /** @test */
    public function testId()
    {
        $shop = new Shop();
        $this->assertEquals('shop-id', $shop->setId('shop-id')->getId());
    }

    /** @test */
    public function testGetType()
    {
        $shop = new Shop();
        $this->assertEquals('shops', $shop->getType());
    }

    /** @test */
    public function testName()
    {
        $shop = new Shop();
        $this->assertEquals('MyParcel.com Test Shop', $shop->setName('MyParcel.com Test Shop')->getName());
    }

    /** @test */
    public function testBillingAddress()
    {
        $shop = new Shop();

        $mock = $this->getMockClass(AddressInterface::class);
        $address = new $mock();

        $this->assertEquals($address, $shop->setBillingAddress($address)->getBillingAddress());
    }

    /** @test */
    public function testReturnAddress()
    {
        $shop = new Shop();

        $mock = $this->getMockClass(AddressInterface::class);
        $address = new $mock();

        $this->assertEquals($address, $shop->setReturnAddress($address)->getReturnAddress());
    }

    /** @test */
    public function testRegion()
    {
        $shop = new Shop();

        $mock = $this->getMockClass(RegionInterface::class);
        $region = new $mock();

        $this->assertEquals($region, $shop->setRegion($region)->getRegion());
    }

    /** @test */
    public function testCreatedAd()
    {
        $shop = new Shop();

        $this->assertEquals(1509001337, $shop->setCreatedAt(1509001337)->getCreatedAt()->getTimestamp());
        $dateTime = (new \DateTime())->setTimestamp(1509009001);
        $this->assertEquals($dateTime, $shop->setCreatedAt($dateTime)->getCreatedAt());
    }

    /** @test */
    public function testJsonSerialize()
    {
        $returnAddress = $this->getMockBuilder(AddressInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $returnAddress->method('jsonSerialize')
            ->willReturn([
                'street_1'             => 'Diagonally',
                'street_2'             => 'Apartment 4',
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
            ]);

        $billingAddress = $this->getMockBuilder(AddressInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $billingAddress->method('jsonSerialize')
            ->willReturn([
                'street_1'             => 'Diagonally',
                'street_2'             => 'Apartment 4',
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
            ]);

        $region = $this->getMockBuilder(RegionInterface::class)
            ->disableOriginalConstructor()
            ->disableOriginalClone()
            ->disableArgumentCloning()
            ->disallowMockingUnknownTypes()
            ->getMock();
        $region->method('jsonSerialize')
            ->willReturn([
                'id'   => 'region-id-1',
                'type' => 'regions',
            ]);

        $shop = (new Shop())
            ->setId('shop-id')
            ->setName('MyParcel.com Test Shop')
            ->setRegion($region)
            ->setReturnAddress($returnAddress)
            ->setBillingAddress($billingAddress)
            ->setCreatedAt(1509001337);

        $this->assertEquals([
            'id'            => 'shop-id',
            'type'          => 'shops',
            'attributes'    => [
                'name'            => 'MyParcel.com Test Shop',
                'billing_address' => [
                    'street_1'             => 'Diagonally',
                    'street_2'             => 'Apartment 4',
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
                    'street_2'             => 'Apartment 4',
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
                'created_at'      => 1509001337,
            ],
            'relationships' => [
                'region' => ['data' => ['id' => 'region-id-1', 'type' => 'regions']],
            ],
        ], $shop->jsonSerialize());
    }
}
