<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Resources;

use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceOptionInterface;
use MyParcelCom\ApiSdk\Resources\Traits\JsonSerializable;
use MyParcelCom\ApiSdk\Resources\Traits\Resource;

class ServiceOption implements ServiceOptionInterface
{
    use JsonSerializable;
    use Resource;

    const ATTRIBUTE_NAME = 'name';
    const ATTRIBUTE_CODE = 'code';
    const ATTRIBUTE_CATEGORY = 'category';

    const META_PRICE = 'price';
    const META_PRICE_AMOUNT = 'amount';
    const META_PRICE_CURRENCY = 'currency';
    const META_INCLUDED = 'included';

    private ?string $id = null;

    private string $type = ResourceInterface::TYPE_SERVICE_OPTION;

    private array $attributes = [
        self::ATTRIBUTE_NAME     => null,
        self::ATTRIBUTE_CODE     => null,
        self::ATTRIBUTE_CATEGORY => null,
    ];

    private array $meta = [
        self::META_PRICE    => [
            self::META_PRICE_AMOUNT   => null,
            self::META_PRICE_CURRENCY => null,
        ],
        self::META_INCLUDED => null,
    ];

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

    public function setCategory($category)
    {
        $this->attributes[self::ATTRIBUTE_CATEGORY] = $category;

        return $this;
    }

    public function getCategory()
    {
        return $this->attributes[self::ATTRIBUTE_CATEGORY];
    }

    public function setPrice($price)
    {
        $this->meta[self::META_PRICE][self::META_PRICE_AMOUNT] = $price !== null ? (int) $price : null;

        return $this;
    }

    public function getPrice()
    {
        return $this->meta[self::META_PRICE][self::META_PRICE_AMOUNT];
    }

    public function setCurrency($currency)
    {
        $this->meta[self::META_PRICE][self::META_PRICE_CURRENCY] = $currency;

        return $this;
    }

    public function getCurrency()
    {
        return $this->meta[self::META_PRICE][self::META_PRICE_CURRENCY];
    }

    public function setIncluded($included)
    {
        $this->meta[self::META_INCLUDED] = $included;

        return $this;
    }

    public function isIncluded()
    {
        return $this->meta[self::META_INCLUDED];
    }
}
