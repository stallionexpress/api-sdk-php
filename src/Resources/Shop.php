<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Resources;

use DateTime;
use MyParcelCom\ApiSdk\Resources\Interfaces\AddressInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\OrganizationInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShopInterface;
use MyParcelCom\ApiSdk\Resources\Traits\JsonSerializable;
use MyParcelCom\ApiSdk\Resources\Traits\Resource;

class Shop implements ShopInterface
{
    use JsonSerializable;
    use Resource;

    const ATTRIBUTE_NAME = 'name';
    const ATTRIBUTE_WEBSITE = 'website';
    const ATTRIBUTE_SENDER_ADDRESS = 'sender_address';
    const ATTRIBUTE_RETURN_ADDRESS = 'return_address';
    const ATTRIBUTE_CREATED_AT = 'created_at';

    const RELATIONSHIP_ORGANIZATION = 'organization';

    private ?string $id = null;

    private string $type = ResourceInterface::TYPE_SHOP;

    private array $attributes = [
        self::ATTRIBUTE_NAME           => null,
        self::ATTRIBUTE_WEBSITE        => null,
        self::ATTRIBUTE_SENDER_ADDRESS => null,
        self::ATTRIBUTE_RETURN_ADDRESS => null,
        self::ATTRIBUTE_CREATED_AT     => null,
    ];

    private array $relationships = [
        self::RELATIONSHIP_ORGANIZATION => [
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
    public function setWebsite($website)
    {
        $this->attributes[self::ATTRIBUTE_WEBSITE] = $website;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getWebsite()
    {
        return $this->attributes[self::ATTRIBUTE_WEBSITE];
    }

    /**
     * {@inheritdoc}
     */
    public function setSenderAddress(AddressInterface $senderAddress)
    {
        $this->attributes[self::ATTRIBUTE_SENDER_ADDRESS] = $senderAddress;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSenderAddress()
    {
        return $this->attributes[self::ATTRIBUTE_SENDER_ADDRESS];
    }

    /**
     * {@inheritdoc}
     */
    public function setReturnAddress(AddressInterface $returnAddress)
    {
        $this->attributes[self::ATTRIBUTE_RETURN_ADDRESS] = $returnAddress;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getReturnAddress()
    {
        return $this->attributes[self::ATTRIBUTE_RETURN_ADDRESS];
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt($time)
    {
        if (is_int($time)) {
            $this->attributes[self::ATTRIBUTE_CREATED_AT] = $time;
        } elseif ($time instanceof DateTime) {
            $this->attributes[self::ATTRIBUTE_CREATED_AT] = $time->getTimestamp();
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return (new DateTime())->setTimestamp($this->attributes[self::ATTRIBUTE_CREATED_AT]);
    }

    /**
     * {@inheritdoc}
     */
    public function setOrganization(OrganizationInterface $organization)
    {
        $this->relationships[self::RELATIONSHIP_ORGANIZATION]['data'] = $organization;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getOrganization()
    {
        return $this->relationships[self::RELATIONSHIP_ORGANIZATION]['data'];
    }
}
