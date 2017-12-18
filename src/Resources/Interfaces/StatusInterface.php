<?php

namespace MyParcelCom\ApiSdk\Resources\Interfaces;

interface StatusInterface extends ResourceInterface
{
    const LEVEL_PENDING = 'pending';
    const LEVEL_SUCCESS = 'success';
    const LEVEL_FAILED = 'failed';

    /**
     * @param string $id
     * @return $this
     */
    public function setId($id);

    /**
     * @param string $code
     * @return $this
     */
    public function setCode($code);

    /**
     * @return string
     */
    public function getCode();

    /**
     * @param string $level
     * @return $this
     */
    public function setLevel($level);

    /**
     * @return string
     */
    public function getLevel();

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param string $description
     * @return $this
     */
    public function setDescription($description);

    /**
     * @return string
     */
    public function getDescription();

    /**
     * @param string $carrierCode
     * @return $this
     */
    public function setCarrierStatusCode($carrierCode);

    /**
     * @return string
     */
    public function getCarrierStatusCode();

    /**
     * @param string $carrierDescription
     * @return $this
     */
    public function setCarrierStatusDescription($carrierDescription);

    /**
     * @return string
     */
    public function getCarrierStatusDescription();

    /**
     * @param int|\DateTime $timestamp
     * @return $this
     */
    public function setCarrierTimestamp($timestamp);

    /**
     * @return \DateTime
     */
    public function getCarrierTimestamp();
}
