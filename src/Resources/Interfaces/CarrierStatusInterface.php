<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Resources\Interfaces;

use DateTime;
use JsonSerializable;

interface CarrierStatusInterface extends JsonSerializable
{
    public function getCode(): string;

    public function setCode(string $code): self;

    public function getDescription(): string;

    public function setDescription(string $description): self;

    public function getAssignedAt(): DateTime;

    public function setAssignedAt(DateTime|int|string $assignedAt): self;

    public function getTrackingCode(): ?string;

    public function setTrackingCode(?string $trackingCode): self;
}
