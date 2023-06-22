<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Resources\Interfaces;

use JsonSerializable;

interface ShipmentItemInterface extends JsonSerializable
{
    public function setSku(?string $sku): self;

    public function getSku(): ?string;

    public function setDescription(string $description): self;

    public function getDescription(): string;

    public function setImageUrl(?string $imageUrl): self;

    public function getImageUrl(): ?string;

    public function setHsCode(?string $hsCode): self;

    public function getHsCode(): ?string;

    public function setQuantity(int $quantity): self;

    public function getQuantity(): int;

    public function setItemValue(?int $value): self;

    public function getItemValue(): ?int;

    public function setCurrency(?string $currency): self;

    public function getCurrency(): ?string;

    public function setOriginCountryCode(?string $countryCode): self;

    public function getOriginCountryCode(): ?string;

    public function setItemWeight(?int $weight, ?string $unit = null): self;

    public function getItemWeight(?string $unit = null): ?int;

    public function setItemWeightUnit(string $weightUnit): self;

    public function getItemWeightUnit(): string;

    public function setIsPreferentialOrigin(bool $isPreferentialOrigin): self;

    public function getIsPreferentialOrigin(): bool;
}
