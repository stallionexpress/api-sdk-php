<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Resources\Interfaces;

use JsonSerializable;

interface ErrorInterface extends JsonSerializable
{
    public function setId(string $id): self;

    public function getId(): ?string;

    public function setLinks(array $links): self;

    public function getLinks(): array;

    public function setStatus(string $status): self;

    public function getStatus(): ?string;

    public function setCode(string $code): self;

    public function getCode(): ?string;

    public function setTitle(string $title): self;

    public function getTitle(): ?string;

    public function setDetail(string $detail): self;

    public function getDetail(): ?string;

    public function setSource(array $source): self;

    public function getSource(): array;

    public function setMeta(array $meta): self;

    public function getMeta(): array;
}
