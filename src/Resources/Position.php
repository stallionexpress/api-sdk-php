<?php

namespace MyParcelCom\Sdk\Resources\Interfaces;

use MyParcelCom\Sdk\Resources\Traits\JsonSerializable;

class Position implements PositionInterface
{
    use JsonSerializable;

    private $latitude;
    private $longitude;
    private $distance;
    private $unit;

    /**
     * {@inheritdoc}
     */
    public function setLatitude($latitude)
    {
        $this->latitude = (float)$latitude;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * {@inheritdoc}
     */
    public function setLongitude($longitude)
    {
        $this->longitude = (float)$longitude;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * {@inheritdoc}
     */
    public function setDistance($distance)
    {
        $this->distance = (int)$distance;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDistance()
    {
        return $this->distance;
    }

    /**
     * {@inheritdoc}
     */
    public function setUnit($unit)
    {
        $this->unit = $unit;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getUnit()
    {
        return $this->unit;
    }
}
