<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Resources;

use MyParcelCom\ApiSdk\Resources\Interfaces\CarrierInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ContractInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceInterface;
use MyParcelCom\ApiSdk\Resources\Traits\JsonSerializable;
use MyParcelCom\ApiSdk\Resources\Traits\Resource;

class Contract implements ContractInterface
{
    use JsonSerializable;
    use Resource;

    const ATTRIBUTE_CURRENCY = 'currency';
    const ATTRIBUTE_NAME = 'name';
    const ATTRIBUTE_STATUS = 'status';

    const RELATIONSHIP_CARRIER = 'carrier';

    private ?string $id = null;

    private string $type = ResourceInterface::TYPE_CONTRACT;

    private array $attributes = [
        self::ATTRIBUTE_CURRENCY => null,
        self::ATTRIBUTE_NAME     => null,
        self::ATTRIBUTE_STATUS   => null,
    ];

    private array $relationships = [
        self::RELATIONSHIP_CARRIER => [
            'data' => null,
        ],
    ];

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->attributes[self::ATTRIBUTE_NAME] = $name;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->attributes[self::ATTRIBUTE_NAME];
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrency($currency)
    {
        $this->attributes[self::ATTRIBUTE_CURRENCY] = $currency;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrency()
    {
        return $this->attributes[self::ATTRIBUTE_CURRENCY];
    }

    /**
     * {@inheritdoc}
     */
    public function setCarrier(CarrierInterface $carrier)
    {
        $this->relationships[self::RELATIONSHIP_CARRIER]['data'] = $carrier;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCarrier()
    {
        return $this->relationships[self::RELATIONSHIP_CARRIER]['data'];
    }

    /**
     * {@inheritdoc}
     */
    public function setStatus($status)
    {
        $this->attributes[self::ATTRIBUTE_STATUS] = $status;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus()
    {
        return $this->attributes[self::ATTRIBUTE_STATUS];
    }
}
