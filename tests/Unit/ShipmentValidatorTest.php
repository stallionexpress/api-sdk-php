<?php

namespace MyParcelCom\ApiSdk\Tests\Unit;

use MyParcelCom\ApiSdk\Resources\Interfaces\AddressInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceContractInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShopInterface;
use MyParcelCom\ApiSdk\Resources\Shipment;
use MyParcelCom\ApiSdk\Utils\StringUtils;
use MyParcelCom\ApiSdk\Validators\ShipmentValidator;
use PHPUnit\Framework\TestCase;

class ShipmentValidatorTest extends TestCase
{
    // TODO: FIX!
    /** @var AddressInterface */
    private $recipientAddress;
    /** @var AddressInterface */
    private $senderAddress;
    /** @var ShopInterface */
    private $shop;
    /** @var ServiceContractInterface */
    private $serviceContract;
    /** @var int */
    private $weight;

    public function setUp()
    {
        parent::setUp();

        $this->weight = 24;
        $this->recipientAddress = $this->getMockBuilder(AddressInterface::class)->getMock();
        $this->senderAddress = $this->getMockBuilder(AddressInterface::class)->getMock();
        $this->shop = $this->getMockBuilder(ShopInterface::class)->getMock();
        $this->serviceContract = $this->getMockBuilder(ServiceContractInterface::class)->getMock();
    }

    /** @test */
    public function testHasErrors()
    {
        $validator = new ShipmentValidator(new Shipment());

        $this->assertEquals([], $validator->getErrors());
        $this->assertEquals(false, $validator->hasErrors());

        $validator->setErrors(['woei']);
        $validator->addError('boem');

        $this->assertEquals(['woei', 'boem'], $validator->getErrors());
        $this->assertEquals(true, $validator->hasErrors());

        $validator->clearErrors();
        $this->assertEquals([], $validator->getErrors());
        $this->assertEquals(false, $validator->hasErrors());
    }

    /** @test */
    public function testIsValid()
    {
        $shipment = (new Shipment())
            ->setWeight($this->weight)
            ->setRecipientAddress($this->recipientAddress)
            ->setSenderAddress($this->senderAddress)
            ->setShop($this->shop)
            ->setServiceContract($this->serviceContract);

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
    public function testMissingServiceContract()
    {
        $shipment = $this->createShipmentWithoutProperty('service_contract');

        $validator = new ShipmentValidator($shipment);

        $this->assertFalse($validator->isValid());
    }

    /** @test */
    public function testNegativeWeight()
    {
        $shipment = (new Shipment())
            ->setWeight(-12512)
            ->setRecipientAddress($this->recipientAddress)
            ->setSenderAddress($this->senderAddress)
            ->setShop($this->shop)
            ->setServiceContract($this->serviceContract);

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
        $requiredProperties = ['weight', 'service_contract', 'recipient_address', 'sender_address', 'shop'];

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
