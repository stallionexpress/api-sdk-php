<?php

namespace MyParcelCom\Sdk\Resources;

use MyParcelCom\Sdk\Resources\Interfaces\PhysicalPropertiesInterface;
use MyParcelCom\Sdk\Resources\Traits\JsonSerializable;

class PhysicalProperties implements PhysicalPropertiesInterface
{
    use JsonSerializable;

    /** @var int */
    private $weight;
    /** @var int */
    private $length;
    /** @var int */
    private $volume;
    /** @var int */
    private $height;
    /** @var int */
    private $width;

    /**
     * {@inheritdoc}
     */
    public function setWidth($width)
    {
        $this->width = (int)$width;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * {@inheritdoc}
     */
    public function setHeight($height)
    {
        $this->height = (int)$height;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * {@inheritdoc}
     */
    public function setLength($length)
    {
        $this->length = (int)$length;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * {@inheritdoc}
     */
    public function setWeight($weight)
    {
        $this->weight = (int)$weight;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     * {@inheritdoc}
     */
    public function setVolume($volume)
    {
        $this->volume = $volume;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getVolume()
    {
        return $this->volume;
    }
}
