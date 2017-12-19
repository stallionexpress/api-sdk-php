<?php

namespace MyParcelCom\ApiSdk\Resources;

use DateTime;
use MyParcelCom\ApiSdk\Resources\Interfaces\AddressInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\RegionInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShopInterface;
use MyParcelCom\ApiSdk\Resources\Traits\JsonSerializable;

class Shop implements ShopInterface
{
    use JsonSerializable;

    const ATTRIBUTE_NAME = 'name';
    const ATTRIBUTE_BILLING_ADDRESS = 'billing_address';
    const ATTRIBUTE_RETURN_ADDRESS = 'return_address';
    const ATTRIBUTE_CREATED_AT = 'created_at';

    const RELATIONSHIP_REGION = 'region';

    private $id;
    private $type = ResourceInterface::TYPE_SHOP;
    private $attributes = [
        self::ATTRIBUTE_NAME            => null,
        self::ATTRIBUTE_BILLING_ADDRESS => null,
        self::ATTRIBUTE_RETURN_ADDRESS  => null,
        self::ATTRIBUTE_CREATED_AT      => null,
    ];
    private $relationships = [
        self::RELATIONSHIP_REGION => [
            'data' => null,
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
    public function setName($name)
    {
        $this->attributes[self::ATTRIBUTE_NAME] = $name;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->attributes[self::ATTRIBUTE_NAME];
    }

    /**
     * {@inheritdoc}
     */
    public function setBillingAddress(AddressInterface $billingAddress)
    {
        $this->attributes[self::ATTRIBUTE_BILLING_ADDRESS] = $billingAddress;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getBillingAddress()
    {
        return $this->attributes[self::ATTRIBUTE_BILLING_ADDRESS];
    }

    /**
     * {@inheritdoc}
     */
    public function setReturnAddress(AddressInterface $returnAddress)
    {
        $this->attributes[self::ATTRIBUTE_RETURN_ADDRESS] = $returnAddress;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getReturnAddress()
    {
        return $this->attributes[self::ATTRIBUTE_RETURN_ADDRESS];
    }

    /**
     * {@inheritdoc}
     */
    public function setRegion(RegionInterface $region)
    {
        $this->relationships[self::RELATIONSHIP_REGION]['data'] = $region;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRegion()
    {
        return $this->relationships[self::RELATIONSHIP_REGION]['data'];
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt($time)
    {
        if (is_int($time)) {
            $this->attributes[self::ATTRIBUTE_CREATED_AT] = $time;
        } elseif ($time instanceof DateTime) {
            $this->attributes[self::ATTRIBUTE_CREATED_AT] = $time->getTimestamp();
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return (new DateTime())->setTimestamp($this->attributes[self::ATTRIBUTE_CREATED_AT]);
    }
}
