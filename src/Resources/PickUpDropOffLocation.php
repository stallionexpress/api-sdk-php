<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Resources;

use MyParcelCom\ApiSdk\Resources\Interfaces\AddressInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\CarrierInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\OpeningHourInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\PickUpDropOffLocationInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\PositionInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceInterface;
use MyParcelCom\ApiSdk\Resources\Traits\JsonSerializable;
use MyParcelCom\ApiSdk\Resources\Traits\Resource;
use MyParcelCom\ApiSdk\Utils\DistanceUtils;

class PickUpDropOffLocation implements PickUpDropOffLocationInterface
{
    use JsonSerializable;
    use Resource;

    const ATTRIBUTE_CODE = 'code';
    const ATTRIBUTE_ADDRESS = 'address';
    const ATTRIBUTE_OPENING_HOURS = 'openingHours';
    const ATTRIBUTE_POSITION = 'position';
    const ATTRIBUTE_CATEGORIES = 'categories';

    const RELATIONSHIP_CARRIER = 'carrier';

    const META_DISTANCE = 'distance';

    private ?string $id = null;

    private string $type = ResourceInterface::TYPE_PUDO_LOCATION;

    private array $attributes = [
        self::ATTRIBUTE_CODE          => null,
        self::ATTRIBUTE_ADDRESS       => null,
        self::ATTRIBUTE_OPENING_HOURS => [],
        self::ATTRIBUTE_POSITION      => null,
        self::ATTRIBUTE_CATEGORIES    => [],
    ];

    private array $relationships = [
        self::RELATIONSHIP_CARRIER => [
            'data' => null,
        ],
    ];

    private array $meta = [
        self::META_DISTANCE => null,
    ];

    public function setCode($code)
    {
        $this->attributes[self::ATTRIBUTE_CODE] = $code;

        return $this;
    }

    public function getCode()
    {
        return $this->attributes[self::ATTRIBUTE_CODE];
    }

    public function setAddress(AddressInterface $address)
    {
        $this->attributes[self::ATTRIBUTE_ADDRESS] = $address;

        return $this;
    }

    public function getAddress()
    {
        return $this->attributes[self::ATTRIBUTE_ADDRESS];
    }

    public function setOpeningHours(array $openingHours)
    {
        $this->attributes[self::ATTRIBUTE_OPENING_HOURS] = [];

        array_walk($openingHours, function ($openingHour) {
            $this->addOpeningHour($openingHour);
        });

        return $this;
    }

    public function addOpeningHour(OpeningHourInterface $openingHour)
    {
        $this->attributes[self::ATTRIBUTE_OPENING_HOURS][] = $openingHour;

        return $this;
    }

    public function getOpeningHours()
    {
        return $this->attributes[self::ATTRIBUTE_OPENING_HOURS];
    }

    public function setPosition(PositionInterface $position)
    {
        $this->attributes[self::ATTRIBUTE_POSITION] = $position;

        return $this;
    }

    public function getPosition()
    {
        return $this->attributes[self::ATTRIBUTE_POSITION];
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

    public function setDistance($distance, $unit = DistanceUtils::UNIT_METER)
    {
        $this->meta[self::META_DISTANCE] = round(DistanceUtils::convertDistance($distance, $unit, DistanceUtils::UNIT_METER));

        return $this;
    }

    public function getDistance($unit = DistanceUtils::UNIT_METER)
    {
        return round(DistanceUtils::convertDistance($this->meta[self::META_DISTANCE], DistanceUtils::UNIT_METER, $unit));
    }

    public function setCategories(array $categories)
    {
        $this->attributes[self::ATTRIBUTE_CATEGORIES] = $categories;

        return $this;
    }

    public function getCategories()
    {
        return $this->attributes[self::ATTRIBUTE_CATEGORIES];
    }
}
