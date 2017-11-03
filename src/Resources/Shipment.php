<?php

namespace MyParcelCom\Sdk\Resources;

use MyParcelCom\Sdk\Resources\Interfaces\AddressInterface;
use MyParcelCom\Sdk\Resources\Interfaces\FileInterface;
use MyParcelCom\Sdk\Resources\Interfaces\PhysicalPropertiesInterface;
use MyParcelCom\Sdk\Resources\Interfaces\ResourceInterface;
use MyParcelCom\Sdk\Resources\Interfaces\ServiceInterface;
use MyParcelCom\Sdk\Resources\Interfaces\ServiceOptionInterface;
use MyParcelCom\Sdk\Resources\Interfaces\ShipmentInterface;
use MyParcelCom\Sdk\Resources\Interfaces\ShopInterface;
use MyParcelCom\Sdk\Resources\Interfaces\StatusInterface;
use MyParcelCom\Sdk\Resources\Traits\JsonSerializable;

class Shipment implements ShipmentInterface
{
    use JsonSerializable;

    const ATTRIBUTE_BARCODE = 'barcode';
    const ATTRIBUTE_DESCRIPTION = 'description';
    const ATTRIBUTE_AMOUNT = 'amount';
    const ATTRIBUTE_PRICE = 'price';
    const ATTRIBUTE_INSURANCE = 'insurance';
    const ATTRIBUTE_CURRENCY = 'currency';
    const ATTRIBUTE_WEIGHT = 'weight';
    const ATTRIBUTE_PHYSICAL_PROPERTIES = 'physical_properties';
    const ATTRIBUTE_RECIPIENT_ADDRESS = 'recipient_address';
    const ATTRIBUTE_SENDER_ADDRESS = 'sender_address';
    const ATTRIBUTE_PICKUP = 'pickup_location';
    const ATTRIBUTE_PICKUP_CODE = 'code';
    const ATTRIBUTE_PICKUP_ADDRESS = 'address';

    const RELATIONSHIP_SHOP = 'shop';
    const RELATIONSHIP_SERVICE = 'service';
    const RELATIONSHIP_OPTIONS = 'options';
    const RELATIONSHIP_FILES = 'files';
    const RELATIONSHIP_STATUS = 'status';

    private $id;
    private $type = ResourceInterface::TYPE_SHIPMENT;
    private $attributes = [
        self::ATTRIBUTE_BARCODE             => null,
        self::ATTRIBUTE_DESCRIPTION         => null,
        self::ATTRIBUTE_PRICE               => null,
        self::ATTRIBUTE_INSURANCE           => null,
        self::ATTRIBUTE_WEIGHT              => null,
        self::ATTRIBUTE_PHYSICAL_PROPERTIES => null,
        self::ATTRIBUTE_RECIPIENT_ADDRESS   => null,
        self::ATTRIBUTE_SENDER_ADDRESS      => null,
        self::ATTRIBUTE_PICKUP              => null,
    ];
    private $relationships = [
        self::RELATIONSHIP_SHOP    => [
            'data' => null,
        ],
        self::RELATIONSHIP_SERVICE => [
            'data' => null,
        ],
        self::RELATIONSHIP_STATUS  => [
            'data' => null,
        ],
        self::RELATIONSHIP_OPTIONS => [
            'data' => [],
        ],
        self::RELATIONSHIP_FILES   => [
            'data' => [],
        ],
    ];

    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function setRecipientAddress(AddressInterface $recipientAddress)
    {
        $this->attributes[self::ATTRIBUTE_RECIPIENT_ADDRESS] = $recipientAddress;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRecipientAddress()
    {
        return $this->attributes[self::ATTRIBUTE_RECIPIENT_ADDRESS];
    }

    /**
     * {@inheritdoc}
     */
    public function setSenderAddress(AddressInterface $senderAddress)
    {
        $this->attributes[self::ATTRIBUTE_SENDER_ADDRESS] = $senderAddress;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSenderAddress()
    {
        return $this->attributes[self::ATTRIBUTE_SENDER_ADDRESS];
    }

    /**
     * {@inheritdoc}
     */
    public function setPickupLocationCode($pickupLocationCode)
    {
        $this->attributes[self::ATTRIBUTE_PICKUP][self::ATTRIBUTE_PICKUP_CODE] = $pickupLocationCode;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPickupLocationCode()
    {
        return isset($this->attributes[self::ATTRIBUTE_PICKUP][self::ATTRIBUTE_PICKUP_CODE])
            ? $this->attributes[self::ATTRIBUTE_PICKUP][self::ATTRIBUTE_PICKUP_CODE]
            : null;
    }

    /**
     * {@inheritdoc}
     */
    public function setPickupLocationAddress(AddressInterface $pickupLocationAddress)
    {
        $this->attributes[self::ATTRIBUTE_PICKUP][self::ATTRIBUTE_PICKUP_ADDRESS] = $pickupLocationAddress;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPickupLocationAddress()
    {
        return isset($this->attributes[self::ATTRIBUTE_PICKUP][self::ATTRIBUTE_PICKUP_ADDRESS])
            ? $this->attributes[self::ATTRIBUTE_PICKUP][self::ATTRIBUTE_PICKUP_ADDRESS]
            : null;
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription($description)
    {
        $this->attributes[self::ATTRIBUTE_DESCRIPTION] = $description;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return $this->attributes[self::ATTRIBUTE_DESCRIPTION];
    }

    /**
     * {@inheritdoc}
     */
    public function setPrice($price)
    {
        $this->attributes[self::ATTRIBUTE_PRICE][self::ATTRIBUTE_AMOUNT] = (int)$price;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrice()
    {
        return isset($this->attributes[self::ATTRIBUTE_PRICE][self::ATTRIBUTE_AMOUNT])
            ? $this->attributes[self::ATTRIBUTE_PRICE][self::ATTRIBUTE_AMOUNT]
            : null;
    }

    /**
     * {@inheritdoc}
     */
    public function setInsuranceAmount($insuranceAmount)
    {
        $this->attributes[self::ATTRIBUTE_INSURANCE][self::ATTRIBUTE_AMOUNT] = $insuranceAmount;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getInsuranceAmount()
    {
        return isset($this->attributes[self::ATTRIBUTE_INSURANCE][self::ATTRIBUTE_AMOUNT])
            ? $this->attributes[self::ATTRIBUTE_INSURANCE][self::ATTRIBUTE_AMOUNT]
            : null;
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrency($currency)
    {
        $this->attributes[self::ATTRIBUTE_PRICE][self::ATTRIBUTE_CURRENCY] = $currency;
        $this->attributes[self::ATTRIBUTE_INSURANCE][self::ATTRIBUTE_CURRENCY] = $currency;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrency()
    {
        return isset($this->attributes[self::ATTRIBUTE_PRICE][self::ATTRIBUTE_CURRENCY])
            ? $this->attributes[self::ATTRIBUTE_PRICE][self::ATTRIBUTE_CURRENCY]
            : null;
    }

    /**
     * {@inheritdoc}
     */
    public function setBarcode($barcode)
    {
        $this->attributes[self::ATTRIBUTE_BARCODE] = $barcode;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getBarcode()
    {
        return $this->attributes[self::ATTRIBUTE_BARCODE];
    }

    /**
     * {@inheritdoc}
     */
    public function setWeight($weight)
    {
        $this->attributes[self::ATTRIBUTE_WEIGHT] = $weight;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getWeight()
    {
        return $this->attributes[self::ATTRIBUTE_WEIGHT];
    }

    /**
     * {@inheritdoc}
     */
    public function setPhysicalProperties(PhysicalPropertiesInterface $physicalProperties)
    {
        $this->attributes[self::ATTRIBUTE_PHYSICAL_PROPERTIES] = $physicalProperties;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPhysicalProperties()
    {
        return $this->attributes[self::ATTRIBUTE_PHYSICAL_PROPERTIES];
    }

    /**
     * {@inheritdoc}
     */
    public function setShop(ShopInterface $shop)
    {
        $this->relationships[self::RELATIONSHIP_SHOP]['data'] = $shop;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getShop()
    {
        return $this->relationships[self::RELATIONSHIP_SHOP]['data'];
    }

    /**
     * {@inheritdoc}
     */
    public function setService(ServiceInterface $service)
    {
        $this->relationships[self::RELATIONSHIP_SERVICE]['data'] = $service;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getService()
    {
        return $this->relationships[self::RELATIONSHIP_SERVICE]['data'];
    }

    /**
     * {@inheritdoc}
     */
    public function setOptions(array $options)
    {
        $this->relationships[self::RELATIONSHIP_OPTIONS]['data'] = [];

        array_walk($options, function ($option) {
            $this->addOption($option);
        });

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addOption(ServiceOptionInterface $option)
    {
        $this->relationships[self::RELATIONSHIP_OPTIONS]['data'][] = $option;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        return $this->relationships[self::RELATIONSHIP_OPTIONS]['data'];
    }

    /**
     * {@inheritdoc}
     */
    public function setFiles(array $files)
    {
        $this->relationships[self::RELATIONSHIP_FILES]['data'] = [];

        array_walk($files, function ($file) {
            $this->addFile($file);
        });

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addFile(FileInterface $file)
    {
        $this->relationships[self::RELATIONSHIP_FILES]['data'][] = $file;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFiles()
    {
        return $this->relationships[self::RELATIONSHIP_FILES]['data'];
    }

    /**
     * {@inheritdoc}
     */
    public function setStatus(StatusInterface $status)
    {
        $this->relationships[self::RELATIONSHIP_STATUS]['data'] = $status;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus()
    {
        return $this->relationships[self::RELATIONSHIP_STATUS]['data'];
    }
}
