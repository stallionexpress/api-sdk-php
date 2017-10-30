<?php

namespace MyParcelCom\Sdk\Resources;

use MyParcelCom\Sdk\Resources\Interfaces\CarrierInterface;
use MyParcelCom\Sdk\Resources\Interfaces\ResourceInterface;
use MyParcelCom\Sdk\Resources\Traits\JsonSerializable;

class Carrier implements CarrierInterface
{
    use JsonSerializable;

    const ATTRIBUTE_NAME = 'name';

    private $id;
    private $type = ResourceInterface::TYPE_CARRIER;
    private $attributes = [
        self::ATTRIBUTE_NAME => null,
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
    public function getType()
    {
        return $this->type;
    }
}
