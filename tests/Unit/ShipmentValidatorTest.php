<?php

namespace MyParcelCom\ApiSdk\Tests\Unit;

use MyParcelCom\ApiSdk\Resources\Address;
use MyParcelCom\ApiSdk\Resources\Contract;
use MyParcelCom\ApiSdk\Resources\Service;
use MyParcelCom\ApiSdk\Resources\Shipment;
use MyParcelCom\ApiSdk\Resources\Shop;
use MyParcelCom\ApiSdk\Utils\StringUtils;
use MyParcelCom\ApiSdk\Validators\ShipmentValidator;
use PHPUnit\Framework\TestCase;

class ShipmentValidatorTest extends TestCase
{
    private $service;
    private $recipientAddress;
    private $senderAddress;
    private $shop;
    private $contract;
    private $weight;

    public function setUp()
    {
        parent::setUp();

        $this->weight = 24;
        $this->service = $this->getMockBuilder(Service::class)->getMock();
        $this->recipientAddress = $this->getMockBuilder(Address::class)->getMock();
        $this->senderAddress = $this->getMockBuilder(Address::class)->getMock();
        $this->shop = $this->getMockBuilder(Shop::class)->getMock();
        $this->contract = $this->getMockBuilder(Contract::class)->getMock();
    }

    /** @test */
    public function testIsValid()
    {
        $shipment = (new Shipment())
            ->setWeight($this->weight)
            ->setService($this->service)
            ->setRecipientAddress($this->recipientAddress)
            ->setSenderAddress($this->senderAddress)
            ->setShop($this->shop)
            ->setContract($this->contract);

        $validator = new ShipmentValidator($shipment);

        $this->assertTrue($validator->isValid());
    }

    /** @test */
    public function testMissingWeight()
    {
        $shipment = $this->createShipmentWithoutProperty('weight');

        $validator = new ShipmentValidator($shipment);

        $this->assertFalse($validator->isValid());
    }

    /** @test */
    public function testMissingService()
    {
        $shipment = $this->createShipmentWithoutProperty('service');

        $validator = new ShipmentValidator($shipment);

        $this->assertFalse($validator->isValid());
    }

    /** @test */
    public function testMissingRecipientAddress()
    {
        $shipment = $this->createShipmentWithoutProperty('recipient_address');

        $validator = new ShipmentValidator($shipment);

        $this->assertFalse($validator->isValid());
    }

    /** @test */
    public function testMissingSenderAddress()
    {
        $shipment = $this->createShipmentWithoutProperty('sender_address');

        $validator = new ShipmentValidator($shipment);

        $this->assertFalse($validator->isValid());
    }

    /** @test */
    public function testMissingShop()
    {
        $shipment = $this->createShipmentWithoutProperty('shop');

        $validator = new ShipmentValidator($shipment);

        $this->assertFalse($validator->isValid());
    }

    /** @test */
    public function testMissingContract()
    {
        $shipment = $this->createShipmentWithoutProperty('contract');

        $validator = new ShipmentValidator($shipment);

        $this->assertFalse($validator->isValid());
    }

    /**
     * Creates and returns a Shipment model with all the required properties
     * except the given property.
     *
     * @param $missingProperty
     * @return Shipment
     */
    private function createShipmentWithoutProperty($missingProperty)
    {
        $missingProperty = StringUtils::snakeToCamelCase($missingProperty);
        $shipment = new Shipment();
        $requiredProperties = ['weight', 'service', 'recipient_address', 'sender_address', 'shop', 'contract'];

        foreach ($requiredProperties as $requiredProperty) {
            $requiredProperty = StringUtils::snakeToCamelCase($requiredProperty);

            if ($missingProperty !== $requiredProperty) {
                $setter = 'set' . ucfirst($requiredProperty);
                $shipment->$setter($this->$requiredProperty);
            }
        }

        return $shipment;
    }
}