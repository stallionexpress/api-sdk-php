<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Resources;

use MyParcelCom\ApiSdk\Resources\Interfaces\RegionInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceInterface;
use MyParcelCom\ApiSdk\Resources\Traits\JsonSerializable;
use MyParcelCom\ApiSdk\Resources\Traits\Resource;

class Region implements RegionInterface
{
    use JsonSerializable;
    use Resource;

    const ATTRIBUTE_COUNTRY_CODE = 'country_code';
    const ATTRIBUTE_REGION_CODE = 'region_code';
    const ATTRIBUTE_CURRENCY = 'currency';
    const ATTRIBUTE_NAME = 'name';
    const ATTRIBUTE_CATEGORY = 'category';

    const RELATIONSHIP_PARENT = 'parent';

    private ?string $id = null;

    private string $type = ResourceInterface::TYPE_REGION;

    private array $attributes = [
        self::ATTRIBUTE_COUNTRY_CODE => null,
        self::ATTRIBUTE_REGION_CODE  => null,
        self::ATTRIBUTE_CURRENCY     => null,
        self::ATTRIBUTE_NAME         => null,
    ];

    private array $relationships = [
        self::RELATIONSHIP_PARENT => [
            'data' => null,
        ],
    ];

    public function setCountryCode(?string $countryCode): self
    {
        $this->attributes[self::ATTRIBUTE_COUNTRY_CODE] = $countryCode;

        return $this;
    }

    public function getCountryCode(): ?string
    {
        return $this->attributes[self::ATTRIBUTE_COUNTRY_CODE];
    }

    public function setRegionCode(?string $regionCode): self
    {
        $this->attributes[self::ATTRIBUTE_REGION_CODE] = $regionCode;

        return $this;
    }

    public function getRegionCode(): ?string
    {
        return $this->attributes[self::ATTRIBUTE_REGION_CODE];
    }

    public function setCurrency(?string $currency): self
    {
        $this->attributes[self::ATTRIBUTE_CURRENCY] = $currency;

        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->attributes[self::ATTRIBUTE_CURRENCY];
    }

    public function setName(string $name): self
    {
        $this->attributes[self::ATTRIBUTE_NAME] = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->attributes[self::ATTRIBUTE_NAME];
    }

    public function setCategory(?string $category): self
    {
        $this->attributes[self::ATTRIBUTE_CATEGORY] = $category;

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->attributes[self::ATTRIBUTE_CATEGORY];
    }

    public function setParent(?RegionInterface $region): self
    {
        $this->relationships[self::RELATIONSHIP_PARENT]['data'] = $region;

        return $this;
    }

    public function getParent(): ?RegionInterface
    {
        return $this->relationships[self::RELATIONSHIP_PARENT]['data'];
    }
}
