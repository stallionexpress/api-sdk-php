<?php

namespace MyParcelCom\Sdk\Validators;

use MyParcelCom\Sdk\Resources\Interfaces\ShipmentInterface;
use MyParcelCom\Sdk\Traits\HasErrors;
use MyParcelCom\Sdk\Utils\StringUtils;

class ShipmentValidator implements ValidatorInterface
{
    use HasErrors;

    protected $shipment;

    public function __construct(ShipmentInterface $shipment)
    {
        $this->shipment = $shipment;
    }

    /**
     * {@inheritdoc}
     */
    public function isValid()
    {
        $this->clearErrors();

        $this->checkRequired();

        return !$this->hasErrors();
    }

    /**
     * Check if the required properties are set. Add any errors to the errors
     * array.
     *
     * @return void
     */
    protected function checkRequired()
    {
        $required = ['weight', 'service', 'recipient_address', 'sender_address', 'shop', 'contract'];

        array_walk($required, function ($required) {
            $getter = 'get' . StringUtils::snakeToPascalCase($required);
            $value = $this->shipment->$getter();
            if (empty($value)) {
                $this->addError(sprintf('Required property `%s` is empty', $required));
            }
        });
    }
}
