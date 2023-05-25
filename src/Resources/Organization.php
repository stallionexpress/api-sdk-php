<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Resources;

use MyParcelCom\ApiSdk\Resources\Interfaces\OrganizationInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceInterface;
use MyParcelCom\ApiSdk\Resources\Traits\JsonSerializable;
use MyParcelCom\ApiSdk\Resources\Traits\Resource;

class Organization implements OrganizationInterface
{
    use JsonSerializable;
    use Resource;

    const ATTRIBUTE_NAME = 'name';

    private ?string $id = null;

    private string $type = ResourceInterface::TYPE_ORGANIZATIONS;

    private array $attributes = [
        self::ATTRIBUTE_NAME => null,
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
}
