<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Resources;

use MyParcelCom\ApiSdk\Resources\Interfaces\CustomsInterface;
use MyParcelCom\ApiSdk\Resources\Traits\JsonSerializable;

class Customs implements CustomsInterface
{
    use JsonSerializable;

    const AMOUNT = 'amount';
    const CURRENCY = 'currency';

    /** @var string */
    private $contentType;

    /** @var string */
    private $invoiceNumber;

    /** @var string */
    private $nonDelivery;

    /** @var string */
    private $incoterm;

    /** @var string|null */
    private $licenseNumber;

    /** @var string|null */
    private $certificateNumber;

    /** @var array */
    private $shippingValue = [
        self::AMOUNT   => null,
        self::CURRENCY => null,
    ];

    public function getContentType(): ?string
    {
        return $this->contentType;
    }

    public function setContentType(?string $contentType): self
    {
        $this->contentType = $contentType;

        return $this;
    }

    public function getInvoiceNumber(): ?string
    {
        return $this->invoiceNumber;
    }

    public function setInvoiceNumber(?string $invoiceNumber): self
    {
        $this->invoiceNumber = $invoiceNumber;

        return $this;
    }

    public function getNonDelivery(): ?string
    {
        return $this->nonDelivery;
    }

    public function setNonDelivery(?string $nonDelivery): self
    {
        $this->nonDelivery = $nonDelivery;

        return $this;
    }

    public function getIncoterm(): ?string
    {
        return $this->incoterm;
    }

    public function setIncoterm(?string $incoterm): self
    {
        $this->incoterm = $incoterm;

        return $this;
    }

    public function getLicenseNumber(): ?string
    {
        return $this->licenseNumber;
    }

    public function setLicenseNumber(?string $licenseNumber): self
    {
        $this->licenseNumber = $licenseNumber;

        return $this;
    }

    public function getCertificateNumber(): ?string
    {
        return $this->certificateNumber;
    }

    public function setCertificateNumber(?string $certificateNumber): self
    {
        $this->certificateNumber = $certificateNumber;

        return $this;
    }

    public function getShippingValueCurrency(): ?string
    {
        return $this->shippingValue[self::CURRENCY];
    }

    public function setShippingValueCurrency(?string $shippingValueCurrency): self
    {
        $this->shippingValue[self::CURRENCY] = $shippingValueCurrency;

        return $this;
    }

    public function getShippingValueAmount(): ?int
    {
        return $this->shippingValue[self::AMOUNT];
    }

    public function setShippingValueAmount(?int $shippingValueAmount): self
    {
        $this->shippingValue[self::AMOUNT] = (int) $shippingValueAmount;

        return $this;
    }
}
