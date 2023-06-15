<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Traits;

trait HasErrors
{
    protected array $errors = [];

    /**
     * Get all the errors.
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Returns true if errors are set.
     */
    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    /**
     * Set all the found errors.
     */
    public function setErrors(array $errors): self
    {
        $this->errors = $errors;

        return $this;
    }

    /**
     * Add an error.
     */
    public function addError(string $error): self
    {
        $this->errors[] = $error;

        return $this;
    }

    /**
     * Clears the errors array.
     */
    public function clearErrors(): self
    {
        $this->errors = [];

        return $this;
    }
}
