<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Resources\Proxy;

use DateTime;
use MyParcelCom\ApiSdk\Resources\Interfaces\CarrierStatusInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ErrorInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceProxyInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShipmentInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShipmentStatusInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\StatusInterface;
use MyParcelCom\ApiSdk\Resources\Traits\JsonSerializable;
use MyParcelCom\ApiSdk\Resources\Traits\ProxiesResource;
use MyParcelCom\ApiSdk\Resources\Traits\Resource;

class ShipmentStatusProxy implements ShipmentStatusInterface, ResourceProxyInterface
{
    use JsonSerializable;
    use ProxiesResource;
    use Resource;

    private ?string $id = null;

    private string $type = ResourceInterface::TYPE_SHIPMENT_STATUS;

    public function setCarrierStatuses(array $carrierStatuses): self
    {
        $this->getResource()->setCarrierStatuses($carrierStatuses);

        return $this;
    }

    public function addCarrierStatus(CarrierStatusInterface $carrierStatus): self
    {
        $this->getResource()->addCarrierStatus($carrierStatus);

        return $this;
    }

    public function getCarrierStatuses(): array
    {
        return $this->getResource()->getCarrierStatuses();
    }

    public function setErrors(array $errors): self
    {
        $this->getResource()->setErrors($errors);

        return $this;
    }

    public function addError(ErrorInterface $error): self
    {
        $this->getResource()->addError($error);

        return $this;
    }

    public function getErrors(): array
    {
        return $this->getResource()->getErrors();
    }

    public function setCreatedAt(DateTime|int $createdAt): self
    {
        $this->getResource()->setCreatedAt($createdAt);

        return $this;
    }

    public function getCreatedAt(): DateTime
    {
        return $this->getResource()->getCreatedAt();
    }

    public function setShipment(ShipmentInterface $shipment): self
    {
        $this->getResource()->setShipment($shipment);

        return $this;
    }

    public function getShipment(): ShipmentInterface
    {
        return $this->getResource()->getShipment();
    }

    public function setStatus(StatusInterface $status): self
    {
        $this->getResource()->setStatus($status);

        return $this;
    }

    public function getStatus(): StatusInterface
    {
        return $this->getResource()->getStatus();
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
