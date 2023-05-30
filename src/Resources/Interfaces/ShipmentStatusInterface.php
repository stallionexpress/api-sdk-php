<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Resources\Interfaces;

use DateTime;

interface ShipmentStatusInterface extends ResourceInterface
{
    /**
     * @param CarrierStatusInterface[] $carrierStatuses
     */
    public function setCarrierStatuses(array $carrierStatuses): self;

    public function addCarrierStatus(CarrierStatusInterface $carrierStatus): self;

    /**
     * @return CarrierStatusInterface[]
     */
    public function getCarrierStatuses(): array;

    /**
     * @param ErrorInterface[] $errors
     */
    public function setErrors(array $errors): self;

    public function addError(ErrorInterface $error): self;

    /**
     * @return ErrorInterface[]
     */
    public function getErrors(): array;

    public function setCreatedAt(DateTime|int $createdAt): self;

    public function getCreatedAt(): DateTime;

    public function setShipment(ShipmentInterface $shipment): self;

    public function getShipment(): ShipmentInterface;

    public function setStatus(StatusInterface $status): self;

    public function getStatus(): StatusInterface;
}
