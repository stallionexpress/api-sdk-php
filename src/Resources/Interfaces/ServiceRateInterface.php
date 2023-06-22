<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Resources\Interfaces;

interface ServiceRateInterface extends ResourceInterface
{
    public function setWeightMin(int $weightMin): self;

    public function getWeightMin(): int;

    public function setWeightMax(int $weightMax): self;

    public function getWeightMax(): int;

    public function setWeightBracket(array $weightBracket): self;

    public function getWeightBracket(): array;

    public function calculateBracketPrice(int $weight): ?int;

    public function setLengthMax(?int $lengthMax): self;

    public function getLengthMax(): ?int;

    public function setHeightMax(?int $heightMax): self;

    public function getHeightMax(): ?int;

    public function setWidthMax(?int $widthMax): self;

    public function getWidthMax(): ?int;

    public function setVolumeMax(float|int|null $volumeMax): self;

    public function getVolumeMax(): float|int|null;

    public function setCurrency(?string $currency): self;

    public function getCurrency(): ?string;

    public function setPrice(?int $price): self;

    public function getPrice(): ?int;

    public function setFuelSurchargeAmount(?int $amount): self;

    public function getFuelSurchargeAmount(): ?int;

    public function setFuelSurchargeCurrency(?string $currency): self;

    public function getFuelSurchargeCurrency(): ?string;

    public function setService(ServiceInterface $service): self;

    public function getService(): ServiceInterface;

    public function setContract(ContractInterface $contract): self;

    public function getContract(): ContractInterface;

    /**
     * @param ServiceOptionInterface[] $serviceOptions
     */
    public function setServiceOptions(array $serviceOptions): self;

    public function addServiceOption(ServiceOptionInterface $serviceOption): self;

    /**
     * @return ServiceOptionInterface[]
     */
    public function getServiceOptions(): array;

    public function setIsDynamic(bool $isDynamic): self;

    public function isDynamic(): bool;

    public function resolveDynamicRateForShipment(ShipmentInterface $shipment): ServiceRateInterface;
}
