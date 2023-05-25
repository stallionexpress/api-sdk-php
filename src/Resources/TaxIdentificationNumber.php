<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Resources;

use MyParcelCom\ApiSdk\Enums\TaxTypeEnum;
use MyParcelCom\ApiSdk\Resources\Traits\JsonSerializable;

class TaxIdentificationNumber
{
    use JsonSerializable;

    private string $countryCode;

    private string $number;

    private ?string $description = null;

    private string $type;

    public function setCountryCode(string $countryCode): self
    {
        $this->countryCode = $countryCode;

        return $this;
    }

    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    public function setNumber(string $number): self
    {
        $this->number = $number;

        return $this;
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setType(TaxTypeEnum $type): self
    {
        $this->type = $type->getValue();

        return $this;
    }

    public function getType(): ?TaxTypeEnum
    {
        return $this->type ? new TaxTypeEnum($this->type) : null;
    }
}
