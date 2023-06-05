<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Validators;

use MyParcelCom\ApiSdk\Resources\Interfaces\ShipmentInterface;
use MyParcelCom\ApiSdk\Traits\HasErrors;
use MyParcelCom\ApiSdk\Utils\StringUtils;

class ShipmentValidator implements ValidatorInterface
{
    use HasErrors;

    public function __construct(
        protected ShipmentInterface $shipment,
    ) {
    }

    public function isValid(): bool
    {
        $this->clearErrors();
        $this->checkRequired();
        $this->checkWeight();

        return !$this->hasErrors();
    }

    /**
     * Check if the required properties are set. Add any errors to the errors array.
     */
    protected function checkRequired(): void
    {
        $required = ['weight', 'recipient_address', 'sender_address', 'shop'];

        array_walk($required, function ($required) {
            $getter = 'get' . StringUtils::snakeToPascalCase($required);
            $value = $this->shipment->$getter();
            if (empty($value)) {
                $this->addError(sprintf('Required property `%s` is empty', $required));
            }
        });
    }

    /**
     * Check whether the weight property is not an invalid value.
     */
    protected function checkWeight(): void
    {
        if ($this->shipment->getWeight() < 0) {
            $this->addError('Negative weight is not allowed');
        }
    }
}
