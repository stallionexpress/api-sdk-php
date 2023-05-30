<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Resources;

use MyParcelCom\ApiSdk\Resources\Interfaces\CarrierInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceRateInterface;
use MyParcelCom\ApiSdk\Resources\Traits\JsonSerializable;
use MyParcelCom\ApiSdk\Resources\Traits\Resource;

class Service implements ServiceInterface
{
    use JsonSerializable;
    use Resource;

    const ATTRIBUTE_NAME = 'name';
    const ATTRIBUTE_CODE = 'code';
    const ATTRIBUTE_PACKAGE_TYPE = 'package_type';
    const ATTRIBUTE_TRANSIT_TIME = 'transit_time';
    const ATTRIBUTE_DELIVERY_DAYS = 'delivery_days';
    const ATTRIBUTE_TRANSIT_TIME_MIN = 'min';
    const ATTRIBUTE_TRANSIT_TIME_MAX = 'max';
    const ATTRIBUTE_HANDOVER_METHOD = 'handover_method';
    const ATTRIBUTE_DELIVERY_METHOD = 'delivery_method';
    const ATTRIBUTE_REGIONS_FROM = 'regions_from';
    const ATTRIBUTE_REGIONS_TO = 'regions_to';
    const ATTRIBUTE_USES_VOLUMETRIC_WEIGHT = 'uses_volumetric_weight';

    const RELATIONSHIP_CARRIER = 'carrier';

    private ?string $id = null;

    private string $type = ResourceInterface::TYPE_SERVICE;

    private array $attributes = [
        self::ATTRIBUTE_NAME            => null,
        self::ATTRIBUTE_CODE            => null,
        self::ATTRIBUTE_PACKAGE_TYPE    => null,
        self::ATTRIBUTE_REGIONS_FROM    => [],
        self::ATTRIBUTE_REGIONS_TO      => [],
        self::ATTRIBUTE_TRANSIT_TIME    => [
            self::ATTRIBUTE_TRANSIT_TIME_MIN => null,
            self::ATTRIBUTE_TRANSIT_TIME_MAX => null,
        ],
        self::ATTRIBUTE_HANDOVER_METHOD => null,
        self::ATTRIBUTE_DELIVERY_DAYS   => [],
        self::ATTRIBUTE_DELIVERY_METHOD => null,
    ];

    private array $relationships = [
        self::RELATIONSHIP_CARRIER => [
            'data' => null,
        ],
    ];

    /** @var ServiceRateInterface[] */
    private $serviceRates = [];

    /** @var callable */
    private $serviceRatesCallback;

    public function setName($name)
    {
        $this->attributes[self::ATTRIBUTE_NAME] = $name;

        return $this;
    }

    public function getName()
    {
        return $this->attributes[self::ATTRIBUTE_NAME];
    }

    public function setCode($code)
    {
        $this->attributes[self::ATTRIBUTE_CODE] = $code;

        return $this;
    }

    public function getCode()
    {
        return $this->attributes[self::ATTRIBUTE_CODE];
    }

    public function setPackageType($packageType)
    {
        $this->attributes[self::ATTRIBUTE_PACKAGE_TYPE] = $packageType;

        return $this;
    }

    public function getPackageType()
    {
        return $this->attributes[self::ATTRIBUTE_PACKAGE_TYPE];
    }

    public function getTransitTimeMin()
    {
        return $this->attributes[self::ATTRIBUTE_TRANSIT_TIME][self::ATTRIBUTE_TRANSIT_TIME_MIN];
    }

    public function setTransitTimeMin($transitTimeMin)
    {
        $this->attributes[self::ATTRIBUTE_TRANSIT_TIME][self::ATTRIBUTE_TRANSIT_TIME_MIN] = $transitTimeMin;

        return $this;
    }

    public function getTransitTimeMax()
    {
        return $this->attributes[self::ATTRIBUTE_TRANSIT_TIME][self::ATTRIBUTE_TRANSIT_TIME_MAX];
    }

    public function setTransitTimeMax($transitTimeMax)
    {
        $this->attributes[self::ATTRIBUTE_TRANSIT_TIME][self::ATTRIBUTE_TRANSIT_TIME_MAX] = $transitTimeMax;

        return $this;
    }

    public function setCarrier(CarrierInterface $carrier)
    {
        $this->relationships[self::RELATIONSHIP_CARRIER]['data'] = $carrier;

        return $this;
    }

    public function getCarrier()
    {
        return $this->relationships[self::RELATIONSHIP_CARRIER]['data'];
    }

    public function setHandoverMethod($handoverMethod)
    {
        $this->attributes[self::ATTRIBUTE_HANDOVER_METHOD] = $handoverMethod;

        return $this;
    }

    public function getHandoverMethod()
    {
        return $this->attributes[self::ATTRIBUTE_HANDOVER_METHOD];
    }

    public function setDeliveryDays(array $deliveryDays)
    {
        $this->attributes[self::ATTRIBUTE_DELIVERY_DAYS] = [];

        array_walk($deliveryDays, function ($deliveryDay) {
            $this->addDeliveryDay($deliveryDay);
        });

        return $this;
    }

    public function addDeliveryDay($deliveryDay)
    {
        $this->attributes[self::ATTRIBUTE_DELIVERY_DAYS][] = $deliveryDay;

        return $this;
    }

    public function getDeliveryDays()
    {
        return $this->attributes[self::ATTRIBUTE_DELIVERY_DAYS];
    }

    public function getDeliveryMethod()
    {
        return $this->attributes[self::ATTRIBUTE_DELIVERY_METHOD];
    }

    public function setDeliveryMethod($deliveryMethod)
    {
        $this->attributes[self::ATTRIBUTE_DELIVERY_METHOD] = $deliveryMethod;

        return $this;
    }

    /**
     * @param array $regions
     * @return $this
     */
    public function setRegionsFrom(array $regions)
    {
        $this->attributes[self::ATTRIBUTE_REGIONS_FROM] = $regions;

        return $this;
    }

    /**
     * @return array
     */
    public function getRegionsFrom()
    {
        return $this->attributes[self::ATTRIBUTE_REGIONS_FROM];
    }

    /**
     * @param array $regions
     * @return $this
     */
    public function setRegionsTo(array $regions)
    {
        $this->attributes[self::ATTRIBUTE_REGIONS_TO] = $regions;

        return $this;
    }

    /**
     * @return array
     */
    public function getRegionsTo()
    {
        return $this->attributes[self::ATTRIBUTE_REGIONS_TO];
    }

    public function setUsesVolumetricWeight($usesVolumetricWeight)
    {
        $this->attributes[self::ATTRIBUTE_USES_VOLUMETRIC_WEIGHT] = $usesVolumetricWeight;

        return $this;
    }

    public function usesVolumetricWeight()
    {
        return $this->attributes[self::ATTRIBUTE_USES_VOLUMETRIC_WEIGHT];
    }

    public function setServiceRates(array $serviceRates)
    {
        $this->serviceRates = [];

        array_walk($serviceRates, function ($serviceRate) {
            $this->addServiceRate($serviceRate);
        });

        return $this;
    }

    public function addServiceRate(ServiceRateInterface $serviceRate)
    {
        $this->serviceRates[] = $serviceRate;

        return $this;
    }

    public function getServiceRates(array $filters = ['has_active_contract' => 'true'])
    {
        if (empty($this->serviceRates) && isset($this->serviceRatesCallback)) {
            $this->setServiceRates(call_user_func_array($this->serviceRatesCallback, [$filters]));
        }

        return $this->serviceRates;
    }

    /**
     * @param callable $callback
     * @return $this
     */
    public function setServiceRatesCallback(callable $callback)
    {
        $this->serviceRatesCallback = $callback;

        return $this;
    }

    public function jsonSerialize(): array
    {
        $values = get_object_vars($this);
        unset($values['serviceRates']);

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
