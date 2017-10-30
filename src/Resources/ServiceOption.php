<?php

namespace MyParcelCom\Sdk\Resources;

use MyParcelCom\Sdk\Resources\Interfaces\ResourceInterface;
use MyParcelCom\Sdk\Resources\Interfaces\ServiceOptionInterface;
use MyParcelCom\Sdk\Resources\Traits\JsonSerializable;

class ServiceOption implements ServiceOptionInterface
{
    use JsonSerializable;

    const ATTRIBUTE_CURRENCY = 'currency';
    const ATTRIBUTE_AMOUNT = 'amount';
    const ATTRIBUTE_PRICE = 'price';
    const ATTRIBUTE_NAME = 'name';

    private $id;
    private $type = ResourceInterface::TYPE_SERVICE_OPTION;
    private $attributes = [
        self::ATTRIBUTE_NAME  => null,
        self::ATTRIBUTE_PRICE => [],
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
    public function setPrice($price)
    {
        $this->attributes[self::ATTRIBUTE_PRICE][self::ATTRIBUTE_AMOUNT] = $price;

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
    public function setCurrency($currency)
    {
        $this->attributes[self::ATTRIBUTE_PRICE][self::ATTRIBUTE_CURRENCY] = $currency;

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
}
