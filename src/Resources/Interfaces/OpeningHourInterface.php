<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Resources\Interfaces;

use DateTime;
use JsonSerializable;

interface OpeningHourInterface extends JsonSerializable
{
    public function setDay(string $day): self;

    public function getDay(): string;

    public function setOpen(DateTime|string $open): self;

    public function getOpen(): ?DateTime;

    public function setClosed(DateTime|string $closed): self;

    public function getClosed(): ?DateTime;
}
