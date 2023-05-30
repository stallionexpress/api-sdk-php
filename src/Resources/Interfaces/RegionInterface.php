<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Resources\Interfaces;

interface RegionInterface extends ResourceInterface
{
    public function setCountryCode(?string $countryCode): self;

    public function getCountryCode(): ?string;

    public function setRegionCode(?string $regionCode): self;

    public function getRegionCode(): ?string;

    public function setCurrency(?string $currency): self;

    public function getCurrency(): ?string;

    public function setName(string $name): self;

    public function getName(): string;

    public function setCategory(?string $category);

    public function getCategory(): ?string;

    public function setParent(?RegionInterface $region): self;

    public function getParent(): ?RegionInterface;
}
