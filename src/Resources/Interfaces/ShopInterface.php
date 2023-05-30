<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Resources\Interfaces;

use DateTime;

interface ShopInterface extends ResourceInterface
{
    public function setName(string $name): self;

    public function getName(): string;

    public function setWebsite(?string $website): self;

    public function getWebsite(): ?string;

    public function setSenderAddress(AddressInterface $senderAddress): self;

    public function getSenderAddress(): AddressInterface;

    public function setReturnAddress(AddressInterface $returnAddress): self;

    public function getReturnAddress(): AddressInterface;

    public function setCreatedAt(DateTime|int $createdAt): self;

    public function getCreatedAt(): DateTime;

    public function setOrganization(OrganizationInterface $organization): self;

    public function getOrganization(): OrganizationInterface;
}
