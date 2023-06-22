<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Resources\Proxy;

use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceProxyInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\StatusInterface;
use MyParcelCom\ApiSdk\Resources\Status;
use MyParcelCom\ApiSdk\Resources\Traits\JsonSerializable;
use MyParcelCom\ApiSdk\Resources\Traits\ProxiesResource;
use MyParcelCom\ApiSdk\Resources\Traits\Resource;

/**
 * @method Status getResource()
 */
class StatusProxy implements StatusInterface, ResourceProxyInterface
{
    use JsonSerializable;
    use ProxiesResource;
    use Resource;

    private ?string $id = null;

    private string $type = ResourceInterface::TYPE_STATUS;

    public function setCode(string $code): self
    {
        $this->getResource()->setCode($code);

        return $this;
    }

    public function getCode(): string
    {
        return $this->getResource()->getCode();
    }

    public function setResourceType(string $resourceType): self
    {
        $this->getResource()->setResourceType($resourceType);

        return $this;
    }

    public function getResourceType(): string
    {
        return $this->getResource()->getResourceType();
    }

    public function setLevel(string $level): self
    {
        $this->getResource()->setLevel($level);

        return $this;
    }

    public function getLevel(): string
    {
        return $this->getResource()->getLevel();
    }

    public function setName(string $name): self
    {
        $this->getResource()->setName($name);

        return $this;
    }

    public function getName(): string
    {
        return $this->getResource()->getName();
    }

    public function setDescription(string $description): self
    {
        $this->getResource()->setDescription($description);

        return $this;
    }

    public function getDescription(): string
    {
        return $this->getResource()->getDescription();
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
