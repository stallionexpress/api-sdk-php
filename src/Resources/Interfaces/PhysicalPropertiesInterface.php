<?php

namespace MyParcelCom\Sdk\Resources\Interfaces;

interface PhysicalPropertiesInterface extends \JsonSerializable
{
    /**
     * @param int $width
     * @return $this
     */
    public function setWidth($width);

    /**
     * @return int
     */
    public function getWidth();

    /**
     * @param int $height
     * @return $this
     */
    public function setHeight($height);

    /**
     * @return int
     */
    public function getHeight();

    /**
     * @param int $length
     * @return $this
     */
    public function setLength($length);

    /**
     * @return int
     */
    public function getLength();

    /**
     * @param int $weight
     * @return $this
     */
    public function setWeight($weight);

    /**
     * @return int
     */
    public function getWeight();

    /**
     * @param int $volume
     * @return $this
     */
    public function setVolume($volume);

    /**
     * @return int
     */
    public function getVolume();
}
