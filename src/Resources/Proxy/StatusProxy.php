<?php

namespace MyParcelCom\Sdk\Resources\Proxy;

use MyParcelCom\Sdk\Resources\Interfaces\ResourceInterface;
use MyParcelCom\Sdk\Resources\Interfaces\ResourceProxyInterface;
use MyParcelCom\Sdk\Resources\Interfaces\StatusInterface;
use MyParcelCom\Sdk\Resources\Traits\JsonSerializable;
use MyParcelCom\Sdk\Resources\Traits\ProxiesResource;

class StatusProxy implements StatusInterface, ResourceProxyInterface
{
    use JsonSerializable;
    use ProxiesResource;

    /** @var string */
    private $id;
    /** @var string */
    private $type = ResourceInterface::TYPE_STATUS;

    /**
     * Set the identifier for this file.
     *
     * @param string $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $code
     * @return $this
     */
    public function setCode($code)
    {
        $this->getResource()->setCode($code);

        return $this;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->getResource()->getCode();
    }

    /**
     * @param string $level
     * @return $this
     */
    public function setLevel($level)
    {
        $this->getResource()->setLevel($level);

        return $this;
    }

    /**
     * @return string
     */
    public function getLevel()
    {
        return $this->getResource()->getLevel();
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->getResource()->setName($name);

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->getResource()->getName();
    }

    /**
     * @param string $description
     * @return $this
     */
    public function setDescription($description)
    {
        $this->getResource()->setDescription($description);

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->getResource()->getDescription();
    }

    /**
     * @param string $carrierCode
     * @return $this
     */
    public function setCarrierStatusCode($carrierCode)
    {
        $this->getResource()->setCarrierStatusCode($carrierCode);

        return $this;
    }

    /**
     * @return string
     */
    public function getCarrierStatusCode()
    {
        return $this->getResource()->getCarrierStatusCode();
    }

    /**
     * @param string $carrierDescription
     * @return $this
     */
    public function setCarrierStatusDescription($carrierDescription)
    {
        $this->getResource()->setCarrierStatusDescription($carrierDescription);

        return $this;
    }

    /**
     * @return string
     */
    public function getCarrierStatusDescription()
    {
        return $this->getResource()->getCode();
    }

    /**
     * @param int|\DateTime $timestamp
     * @return $this
     */
    public function setCarrierTimestamp($timestamp)
    {
        $this->getResource()->setCarrierTimestamp($timestamp);

        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCarrierTimestamp()
    {
        return $this->getResource()->getCarrierTimestamp();
    }

    /**
     * This function puts all object properties in an array and returns it.
     *
     * @return array
     */
    public function jsonSerialize()
    {
        $values = get_object_vars($this);
        unset($values['resource']);
        unset($values['api']);

        return $this->arrayValuesToArray($values);
    }
}
