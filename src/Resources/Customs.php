<?php

namespace MyParcelCom\Sdk\Resources;

use MyParcelCom\Sdk\Resources\Interfaces\CustomsInterface;
use MyParcelCom\Sdk\Resources\Interfaces\CustomsItemInterface;
use MyParcelCom\Sdk\Resources\Traits\JsonSerializable;

class Customs implements CustomsInterface
{
    use JsonSerializable;

    /** @var string */
    private $contentType;
    /** @var string */
    private $invoiceNumber;
    /** @var CustomsItemInterface[] */
    private $items = [];
    /** @var string */
    private $nonDelivery;
    /** @var string */
    private $incoterm;

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
    public function getItems()
    {
        return $this->items;
    }

    /**
     * {@inheritdoc}
     */
    public function addItem(CustomsItemInterface $item)
    {
        $this->items[] = $item;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setItems(array $items)
    {
        $this->items = [];

        array_walk($items, function (CustomsItemInterface $item) {
            $this->addItem($item);
        });

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
}
