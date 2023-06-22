<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Resources\Interfaces;

use JsonSerializable;

interface AddressInterface extends JsonSerializable
{
    public function getStreet1(): string;

    public function setStreet1(string $street1): self;

    public function getStreet2(): ?string;

    public function setStreet2(?string $street2): self;

    public function getStreetNumber(): ?int;

    public function setStreetNumber(?int $streetNumber): self;

    public function getStreetNumberSuffix(): ?string;

    public function setStreetNumberSuffix(?string $streetNumberSuffix): self;

    public function getPostalCode(): ?string;

    public function setPostalCode(?string $postalCode): self;

    public function getCity(): string;

    public function setCity(string $city): self;

    public function getStateCode(): ?string;

    public function setStateCode(?string $stateCode): self;

    public function getCountryCode(): string;

    public function setCountryCode(string $countryCode): self;

    public function getFirstName(): ?string;

    public function setFirstName(?string $firstName): self;

    public function getLastName(): ?string;

    public function setLastName(?string $lastName): self;

    public function getCompany(): ?string;

    public function setCompany(?string $company): self;

    public function getEmail(): ?string;

    public function setEmail(?string $email): self;

    public function getPhoneNumber(): ?string;

    public function setPhoneNumber(?string $phoneNumber): self;
}
