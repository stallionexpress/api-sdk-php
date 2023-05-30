<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Resources;

use DateTime;
use MyParcelCom\ApiSdk\Resources\Interfaces\CarrierStatusInterface;
use MyParcelCom\ApiSdk\Resources\Traits\JsonSerializable;
use MyParcelCom\ApiSdk\Utils\DateUtils;

class CarrierStatus implements CarrierStatusInterface
{
    use JsonSerializable;

    protected string $code;

    protected string $description;

    protected int $assignedAt;

    private ?string $trackingCode = null;

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getAssignedAt(): DateTime
    {
        return (new DateTime())->setTimestamp($this->assignedAt);
    }

    public function setAssignedAt(DateTime|int|string $assignedAt): self
    {
        $this->assignedAt = DateUtils::toTimestamp($assignedAt);

        return $this;
    }

    public function getTrackingCode(): ?string
    {
        return $this->trackingCode;
    }

    public function setTrackingCode(?string $trackingCode): self
    {
        $this->trackingCode = $trackingCode;

        return $this;
    }
}
