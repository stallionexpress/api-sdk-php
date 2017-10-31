<?php

namespace MyParcelCom\Sdk\Resources;

use MyParcelCom\Sdk\Resources\Interfaces\CarrierInterface;
use MyParcelCom\Sdk\Resources\Interfaces\ContractInterface;
use MyParcelCom\Sdk\Resources\Interfaces\RegionInterface;
use MyParcelCom\Sdk\Resources\Interfaces\ResourceInterface;
use MyParcelCom\Sdk\Resources\Interfaces\ServiceInterface;
use MyParcelCom\Sdk\Resources\Traits\JsonSerializable;

class Service implements ServiceInterface
{
    use JsonSerializable;

    const ATTRIBUTE_NAME = 'name';
    const ATTRIBUTE_PACKAGE_TYPE = 'package_type';

    const RELATIONSHIP_CARRIER = 'carrier';
    const RELATIONSHIP_REGION_FROM = 'region_from';
    const RELATIONSHIP_REGION_TO = 'region_to';

    /** @var string */
    private $id;
    /** @var string */
    private $type = ResourceInterface::TYPE_SERVICE;
    /** @var ContractInterface[] */
    private $contracts = [];
    /** @var array */
    private $attributes = [
        self::ATTRIBUTE_NAME         => null,
        self::ATTRIBUTE_PACKAGE_TYPE => null,
    ];
    /** @var array */
    private $relationships = [
        self::RELATIONSHIP_CARRIER     => [
            'data' => null,
        ],
        self::RELATIONSHIP_REGION_FROM => [
            'data' => null,
        ],
        self::RELATIONSHIP_REGION_TO   => [
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
    public function setPackageType($packageType)
    {
        $this->attributes[self::ATTRIBUTE_PACKAGE_TYPE] = $packageType;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPackageType()
    {
        return $this->attributes[self::ATTRIBUTE_PACKAGE_TYPE];
    }

    /**
     * {@inheritdoc}
     */
    public function setCarrier(CarrierInterface $carrier)
    {
        $this->relationships[self::RELATIONSHIP_CARRIER]['data'] = $carrier;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCarrier()
    {
        return $this->relationships[self::RELATIONSHIP_CARRIER]['data'];
    }

    /**
     * {@inheritdoc}
     */
    public function setRegionFrom(RegionInterface $region)
    {
        $this->relationships[self::RELATIONSHIP_REGION_FROM]['data'] = $region;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRegionFrom()
    {
        return $this->relationships[self::RELATIONSHIP_REGION_FROM]['data'];
    }

    /**
     * {@inheritdoc}
     */
    public function setRegionTo(RegionInterface $region)
    {
        $this->relationships[self::RELATIONSHIP_REGION_TO]['data'] = $region;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRegionTo()
    {
        return $this->relationships[self::RELATIONSHIP_REGION_TO]['data'];
    }

    /**
     * {@inheritdoc}
     */
    public function setContracts(array $contracts)
    {
        $this->contracts = [];

        array_walk($contracts, function ($contract) {
            $this->addContract($contract);
        });

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addContract(ContractInterface $contract)
    {
        $this->contracts[] = $contract;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getContracts()
    {
        return $this->contracts;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        $values = get_object_vars($this);
        unset($values['contracts']);

        $json = $this->arrayValuesToArray($values);

        if (isset($json['attributes']) && $this->isEmpty($json['attributes'])) {
            unset($json['attributes']);
        }
        if (isset($json['relationships']) && $this->isEmpty($json['relationships'])) {
            unset($json['relationships']);
        }

        return $json;
    }
}
