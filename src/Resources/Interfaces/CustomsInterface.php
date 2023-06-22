<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Resources\Interfaces;

use JsonSerializable;

interface CustomsInterface extends JsonSerializable
{
    const CONTENT_TYPE_MERCHANDISE = 'merchandise';
    const CONTENT_TYPE_SAMPLE_MERCHANDISE = 'sample_merchandise';
    const CONTENT_TYPE_RETURNED_MERCHANDISE = 'returned_merchandise';
    const CONTENT_TYPE_DOCUMENTS = 'documents';
    const CONTENT_TYPE_GIFTS = 'gifts';

    const NON_DELIVERY_RETURN = 'return';
    const NON_DELIVERY_ABANDON = 'abandon';

    const INCOTERM_DAP = 'DAP';
    const INCOTERM_DDP = 'DDP';

    public function setContentType(?string $contentType): self;

    public function getContentType(): ?string;

    public function setInvoiceNumber(?string $invoiceNumber): self;

    public function getInvoiceNumber(): ?string;

    public function setNonDelivery(?string $nonDelivery): self;

    public function getNonDelivery(): ?string;

    public function setIncoterm(?string $incoterm): self;

    public function getIncoterm(): ?string;

    public function setLicenseNumber(?string $licenseNumber): self;

    public function getLicenseNumber(): ?string;

    public function setCertificateNumber(?string $certificateNumber): self;

    public function getCertificateNumber(): ?string;

    public function setShippingValueAmount(?int $shippingValueAmount): self;

    public function getShippingValueAmount(): ?int;

    public function setShippingValueCurrency(?string $shippingValueCurrency): self;

    public function getShippingValueCurrency(): ?string;
}
