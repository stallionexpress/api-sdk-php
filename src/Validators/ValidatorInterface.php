<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Validators;

interface ValidatorInterface
{
    /**
     * Returns `true` when the validator deems whatever its validating to be
     * valid.
     *
     * @return bool
     */
    public function isValid();

    /**
     * Returns an array with all the errors found when validating.
     *
     * @return array
     */
    public function getErrors();
}
