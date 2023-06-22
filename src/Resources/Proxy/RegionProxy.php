<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Resources\Proxy;

use MyParcelCom\ApiSdk\Resources\Interfaces\RegionInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceProxyInterface;
use MyParcelCom\ApiSdk\Resources\Region;
use MyParcelCom\ApiSdk\Resources\Traits\JsonSerializable;
use MyParcelCom\ApiSdk\Resources\Traits\ProxiesResource;
use MyParcelCom\ApiSdk\Resources\Traits\Resource;

/**
 * @method Region getResource()
 */
class RegionProxy implements RegionInterface, ResourceProxyInterface
{
    use JsonSerializable;
    use ProxiesResource;
    use Resource;

    private ?string $id = null;

    private string $type = ResourceInterface::TYPE_REGION;

    public function setCountryCode(?string $countryCode): self
    {
        $this->getResource()->setCountryCode($countryCode);

        return $this;
    }

    public function getCountryCode(): ?string
    {
        return $this->getResource()->getCountryCode();
    }

    public function setRegionCode(?string $regionCode): self
    {
        $this->getResource()->setRegionCode($regionCode);

        return $this;
    }

    public function getRegionCode(): ?string
    {
        return $this->getResource()->getRegionCode();
    }

    public function setCurrency(?string $currency): self
    {
        $this->getResource()->setCurrency($currency);

        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->getResource()->getCurrency();
    }

    public function setName(string $name): self
    {
        $this->getResource()->setName($name);

        return $this;
    }

    public function getName(): string
    {
        return $this->getResource()->getName();
    }

    public function setCategory(?string $category): self
    {
        $this->getResource()->setCategory($category);

        return $this;
    }

    public function getCategory(): ?string
    {
        return $this->getResource()->getCategory();
    }

    public function setParent(?RegionInterface $region): self
    {
        $this->getResource()->setParent($region);

        return $this;
    }

    public function getParent(): ?RegionInterface
    {
        return $this->getResource()->getParent();
    }

    /**
     * This function puts all object properties in an array and returns it.
     */
    public function jsonSerialize(): array
    {
        $values = get_object_vars($this);
        unset($values['resource']);
        unset($values['api']);
        unset($values['uri']);

        return $this->arrayValuesToArray($values);
    }
}
