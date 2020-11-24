<?php

namespace MyParcelCom\ApiSdk\Resources;

use MyParcelCom\ApiSdk\Resources\Interfaces\CustomsInterface;
use MyParcelCom\ApiSdk\Resources\Traits\JsonSerializable;

class Customs implements CustomsInterface
{
    use JsonSerializable;

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

    /**
     * {@inheritdoc}
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * {@inheritdoc}
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getInvoiceNumber()
    {
        return $this->invoiceNumber;
    }

    /**
     * {@inheritdoc}
     */
    public function setInvoiceNumber($invoiceNumber)
    {
        $this->invoiceNumber = $invoiceNumber;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getNonDelivery()
    {
        return $this->nonDelivery;
    }

    /**
     * {@inheritdoc}
     */
    public function setNonDelivery($nonDelivery)
    {
        $this->nonDelivery = $nonDelivery;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getIncoterm()
    {
        return $this->incoterm;
    }

    /**
     * {@inheritdoc}
     */
    public function setIncoterm($incoterm)
    {
        $this->incoterm = $incoterm;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getLicenseNumber()
    {
        return $this->licenseNumber;
    }

    /**
     * {@inheritdoc}
     */
    public function setLicenseNumber($licenseNumber)
    {
        $this->licenseNumber = $licenseNumber;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCertificateNumber()
    {
        return $this->certificateNumber;
    }

    /**
     * {@inheritdoc}
     */
    public function setCertificateNumber($certificateNumber)
    {
        $this->certificateNumber = $certificateNumber;

        return $this;
    }
}
