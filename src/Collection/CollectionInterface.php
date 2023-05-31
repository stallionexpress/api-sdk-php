<?php

namespace MyParcelCom\ApiSdk\Collection;

use Iterator;
use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceInterface;

interface CollectionInterface extends Iterator
{
    /**
     * Counts the amount of resources in the collection.
     */
    public function count(): int;

    /**
     * Retrieves the resources based on limit and offset.
     * Default (and max) limit is 100.
     *
     * @return ResourceInterface[]
     */
    public function get(): array;

    /**
     * Sets an offset on which resource to start retrieving.
     */
    public function offset(int $offset): self;

    /**
     * Sets the amount of resources to be retrieved by get().
     * Default (and max) limit is 100.
     */
    public function limit(int $limit = 100): self;
}
