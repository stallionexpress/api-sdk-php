<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Resources\Interfaces;

use JsonSerializable;

interface PositionInterface extends JsonSerializable
{
    public function setLatitude(float $latitude): self;

    public function getLatitude(): float;

    public function setLongitude(float $longitude): self;

    public function getLongitude(): float;
}
