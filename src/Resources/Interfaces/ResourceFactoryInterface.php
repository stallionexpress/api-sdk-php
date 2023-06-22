<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Resources\Interfaces;

use JsonSerializable;
use MyParcelCom\ApiSdk\Exceptions\MyParcelComException;

/**
 * A class implementing this interface should create resources based on the
 * given type. These types correspond to the types defined by the API.
 */
interface ResourceFactoryInterface
{
    /**
     * Create a resource for given type. Optionally attributes can be supplied
     * to initialize the resource with.
     *
     * @throws MyParcelComException
     */
    public function create(string $type, array $properties = []): ResourceInterface|JsonSerializable;
}
