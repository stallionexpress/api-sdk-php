<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Resources\Traits;

/**
 * @property string|null $id
 * @property string $type
 */
trait Resource
{
    public function getId(): ?string
    {
        return $this->id;
    }

    public function setId(string $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
