<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Resources\Interfaces;

interface StatusInterface extends ResourceInterface
{
    const LEVEL_PENDING = 'pending';
    const LEVEL_SUCCESS = 'success';
    const LEVEL_FAILED = 'failed';

    public function setCode(string $code): self;

    public function getCode(): string;

    public function setResourceType(string $resourceType): self;

    public function getResourceType(): string;

    public function setLevel(string $level): self;

    public function getLevel(): string;

    public function setName(string $name): self;

    public function getName(): string;

    public function setDescription(string $description): self;

    public function getDescription(): string;
}
