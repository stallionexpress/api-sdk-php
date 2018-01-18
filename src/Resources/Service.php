<?php

namespace MyParcelCom\ApiSdk\Resources;

use MyParcelCom\ApiSdk\Resources\Interfaces\CarrierInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ContractInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\RegionInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceInterface;
use MyParcelCom\ApiSdk\Resources\Traits\JsonSerializable;

class Service implements ServiceInterface
{
    use JsonSerializable;

    const ATTRIBUTE_NAME = 'name';
    const ATTRIBUTE_PACKAGE_TYPE = 'package_type';
    const ATTRIBUTE_TRANSIT_TIME = 'transit_time';
    const ATTRIBUTE_TRANSIT_TIME_MIN = 'min';
    const ATTRIBUTE_TRANSIT_TIME_MAX = 'max';

    const RELATIONSHIP_CARRIER = 'carrier';
    const RELATIONSHIP_REGION_FROM = 'region_from';
    const RELATIONSHIP_REGION_TO = 'region_to';
    const ATTRIBUTE_HANDOVER_METHOD = 'handover_method';

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
        self::ATTRIBUTE_TRANSIT_TIME => [
            self::ATTRIBUTE_TRANSIT_TIME_MIN => null,
            self::ATTRIBUTE_TRANSIT_TIME_MAX => null,
        ],
        self::ATTRIBUTE_HANDOVER_METHOD => null,
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
    public function getTransitTimeMin()
    {
        return $this->attributes[self::ATTRIBUTE_TRANSIT_TIME][self::ATTRIBUTE_TRANSIT_TIME_MIN];
    }

    /**
     * {@inheritdoc}
     */
    public function setTransitTimeMin($transitTimeMin)
    {
        $this->attributes[self::ATTRIBUTE_TRANSIT_TIME][self::ATTRIBUTE_TRANSIT_TIME_MIN] = $transitTimeMin;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTransitTimeMax()
    {
        return $this->attributes[self::ATTRIBUTE_TRANSIT_TIME][self::ATTRIBUTE_TRANSIT_TIME_MAX];
    }

    /**
     * {@inheritdoc}
     */
    public function setTransitTimeMax($transitTimeMax)
    {
        $this->attributes[self::ATTRIBUTE_TRANSIT_TIME][self::ATTRIBUTE_TRANSIT_TIME_MAX] = $transitTimeMax;

        return $this;
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
     * @inheritdoc
     */
    public function setHandoverMethod($handoverMethod)
    {
        $this->attributes[self::ATTRIBUTE_HANDOVER_METHOD] = $handoverMethod;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getHandoverMethod()
    {
        return $this->attributes[self::ATTRIBUTE_HANDOVER_METHOD];
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
