<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Resources\Proxy;

use DateTime;
use MyParcelCom\ApiSdk\Resources\Interfaces\AddressInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\OrganizationInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceProxyInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShopInterface;
use MyParcelCom\ApiSdk\Resources\Traits\JsonSerializable;
use MyParcelCom\ApiSdk\Resources\Traits\ProxiesResource;
use MyParcelCom\ApiSdk\Resources\Traits\Resource;

class ShopProxy implements ShopInterface, ResourceProxyInterface
{
    use JsonSerializable;
    use ProxiesResource;
    use Resource;

    private ?string $id = null;

    private string $type = ResourceInterface::TYPE_SHOP;

    /**
     * @param $name
     * @return $this
     */
    public function setName($name)
    {
        $this->getResource()->setName($name);

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->getResource()->getName();
    }

    /**
     * @param string $website
     * @return $this
     */
    public function setWebsite($website)
    {
        $this->getResource()->setWebsite($website);

        return $this;
    }

    /**
     * @return string
     */
    public function getWebsite()
    {
        return $this->getResource()->getWebsite();
    }

    /**
     * @param AddressInterface $senderAddress
     * @return $this
     */
    public function setSenderAddress(AddressInterface $senderAddress)
    {
        $this->getResource()->setSenderAddress($senderAddress);

        return $this;
    }

    /**
     * @return AddressInterface
     */
    public function getSenderAddress()
    {
        return $this->getResource()->getSenderAddress();
    }

    /**
     * @param AddressInterface $returnAddress
     * @return $this
     */
    public function setReturnAddress(AddressInterface $returnAddress)
    {
        $this->getResource()->setReturnAddress($returnAddress);

        return $this;
    }

    /**
     * @return AddressInterface
     */
    public function getReturnAddress()
    {
        return $this->getResource()->getReturnAddress();
    }

    /**
     * @param int|DateTime $time
     * @return $this
     */
    public function setCreatedAt($time)
    {
        $this->getResource()->setCreatedAt($time);

        return $this;
    }

    /**
     * @return DateTime
     */
    public function getCreatedAt()
    {
        return $this->getResource()->getCreatedAt();
    }

    /**
     * @param OrganizationInterface $organization
     * @return $this
     */
    public function setOrganization(OrganizationInterface $organization)
    {
        return $this->getResource()->setOrganization($organization);
    }

    /**
     * @return OrganizationInterface
     */
    public function getOrganization()
    {
        return $this->getResource()->getOrganization();
    }

    /**
     * This function puts all object properties in an array and returns it.
     */
    public function jsonSerialize(): array
    {
        $values = get_object_vars($this);
        unset($values['resource']);
        unset($values['api']);
        unset($values['uri']);

        return $this->arrayValuesToArray($values);
    }
}
