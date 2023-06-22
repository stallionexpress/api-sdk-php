<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Resources;

use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\StatusInterface;
use MyParcelCom\ApiSdk\Resources\Traits\JsonSerializable;
use MyParcelCom\ApiSdk\Resources\Traits\Resource;

class Status implements StatusInterface
{
    use JsonSerializable;
    use Resource;

    const ATTRIBUTE_CODE = 'code';
    const ATTRIBUTE_RESOURCE_TYPE = 'resource_type';
    const ATTRIBUTE_LEVEL = 'level';
    const ATTRIBUTE_NAME = 'name';
    const ATTRIBUTE_DESCRIPTION = 'description';

    private ?string $id = null;

    private string $type = ResourceInterface::TYPE_STATUS;

    private array $attributes = [
        self::ATTRIBUTE_CODE          => null,
        self::ATTRIBUTE_RESOURCE_TYPE => null,
        self::ATTRIBUTE_LEVEL         => null,
        self::ATTRIBUTE_NAME          => null,
        self::ATTRIBUTE_DESCRIPTION   => null,
    ];

    public function setCode(string $code): self
    {
        $this->attributes[self::ATTRIBUTE_CODE] = $code;

        return $this;
    }

    public function getCode(): string
    {
        return $this->attributes[self::ATTRIBUTE_CODE];
    }

    public function setResourceType(string $resourceType): self
    {
        $this->attributes[self::ATTRIBUTE_RESOURCE_TYPE] = $resourceType;

        return $this;
    }

    public function getResourceType(): string
    {
        return $this->attributes[self::ATTRIBUTE_RESOURCE_TYPE];
    }

    public function setLevel(string $level): self
    {
        $this->attributes[self::ATTRIBUTE_LEVEL] = $level;

        return $this;
    }

    public function getLevel(): string
    {
        return $this->attributes[self::ATTRIBUTE_LEVEL];
    }

    public function setName(string $name): self
    {
        $this->attributes[self::ATTRIBUTE_NAME] = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->attributes[self::ATTRIBUTE_NAME];
    }

    public function setDescription(string $description): self
    {
        $this->attributes[self::ATTRIBUTE_DESCRIPTION] = $description;

        return $this;
    }

    public function getDescription(): string
    {
        return $this->attributes[self::ATTRIBUTE_DESCRIPTION];
    }
}
