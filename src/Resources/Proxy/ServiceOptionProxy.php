<?php

namespace MyParcelCom\ApiSdk\Resources\Proxy;

use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceProxyInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceOptionInterface;
use MyParcelCom\ApiSdk\Resources\Traits\JsonSerializable;
use MyParcelCom\ApiSdk\Resources\Traits\ProxiesResource;

class ServiceOptionProxy implements ServiceOptionInterface, ResourceProxyInterface
{
    use JsonSerializable;
    use ProxiesResource;

    /** @var string */
    private $id;

    /** @var string */
    private $type = ResourceInterface::TYPE_SERVICE_OPTION;

    /** @var array */
    private $serviceRateDetails = [];

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
     * @param string $category
     * @return $this
     */
    public function setCategory($category)
    {
        $this->getResource()->setCategory($category);

        return $this;
    }

    /**
     * @return string
     */
    public function getCategory()
    {
        return $this->getResource()->getCategory();
    }

    /**
     * @param string $serviceRateId
     * @param array  $details
     * @return ServiceOptionProxy
     */
    public function addDetailsForServiceRate($serviceRateId, array $details)
    {
        // TODO: Maybe do this whole thing different.
        $this->serviceRateDetails[$serviceRateId] = $details;

        return $this;
    }

    /**
     * @param $serviceRateId
     * @return array|null
     */
    public function getDetailsForServiceRate($serviceRateId)
    {
        // TODO: Maybe do this whole thing different.

        return array_key_exists($serviceRateId, $this->serviceRateDetails)
            ? $this->serviceRateDetails[$serviceRateId]
            : null;
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
        unset($values['uri']);

        // TODO: Maybe not.
        if (empty($values['serviceRateDetails'])) {
            unset($values['serviceRateDetails']);
        }

        return $this->arrayValuesToArray($values);
    }
}
