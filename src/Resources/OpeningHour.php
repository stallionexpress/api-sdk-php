<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Resources;

use DateTime;
use MyParcelCom\ApiSdk\Resources\Interfaces\OpeningHourInterface;
use MyParcelCom\ApiSdk\Resources\Traits\JsonSerializable;

class OpeningHour implements OpeningHourInterface
{
    use JsonSerializable;

    private string $day;

    private ?string $open = null;

    private ?DateTime $openDateTime = null;

    private ?string $closed = null;

    private ?DateTime $closedDateTime = null;

    public function setDay($day): self
    {
        $this->day = $day;

        return $this;
    }

    public function getDay(): string
    {
        return $this->day;
    }

    public function setOpen(DateTime|string $open): self
    {
        if (is_string($open)) {
            $this->open = $open;
            $this->openDateTime = new DateTime($open);
        } elseif ($open instanceof DateTime) {
            $this->open = $open->format('H:i');
            $this->openDateTime = $open;
        }

        return $this;
    }

    public function getOpen(): ?DateTime
    {
        return $this->openDateTime;
    }

    public function setClosed(DateTime|string $closed): self
    {
        if (is_string($closed)) {
            $this->closed = $closed;
            $this->closedDateTime = new DateTime($closed);
        } elseif ($closed instanceof DateTime) {
            $this->closed = $closed->format('H:i');
            $this->closedDateTime = $closed;
        }

        return $this;
    }

    public function getClosed(): ?DateTime
    {
        return $this->closedDateTime;
    }
}
