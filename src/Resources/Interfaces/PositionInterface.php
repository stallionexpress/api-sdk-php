<?php

namespace MyParcelCom\Sdk\Resources\Interfaces;

interface PositionInterface extends JsonInterface
{
    const UNIT_KILOMETER = 'kilometers';
    const UNIT_METER = 'meters';
    const UNIT_MILE = 'miles';

    /**
     * @param float $latitude
     * @return $this
     */
    public function setLatitude($latitude);

    /**
     * @return float
     */
    public function getLatitude();

    /**
     * @param float $longitude
     * @return $this
     */
    public function setLongitude($longitude);

    /**
     * @return float
     */
    public function getLongitude();

    /**
     * @param int $distance
     * @return $this
     */
    public function setDistance($distance);

    /**
     * @return int
     */
    public function getDistance();

    /**
     * @param string $unit
     * @return $this
     */
    public function setUnit($unit);

    /**
     * @return string|null
     */
    public function getUnit();
}
