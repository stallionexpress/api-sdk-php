<?php

namespace MyParcelCom\ApiSdk\Collection;

use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceInterface;

class ArrayCollection implements CollectionInterface
{
    /** @var ResourceInterface[] */
    protected $resources;

    /** @var int */
    protected $offset = 0;

    /** @var int */
    protected $limit = 100;

    /** @var int */
    protected $currentResourceNumber = 0;

    /**
     * ArrayCollection constructor.
     *
     * @param ResourceInterface[] $resources
     */
    public function __construct(array $resources)
    {
        $this->resources = $resources;
    }

    /**
     * Counts the amount of resources in the collection.
     */
    public function count(): int
    {
        return count($this->resources);
    }

    /**
     * Retrieves the resources based on limit and offset.
     * Default (and max) limit is 100.
     *
     * @return ResourceInterface[]
     */
    public function get(): array
    {
        return array_filter($this->resources, function ($resourceNumber) {
            return $resourceNumber >= $this->offset && $resourceNumber < ($this->offset + $this->limit);
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * Sets an offset on which resource to start retrieving.
     */
    public function offset(int $offset): self
    {
        $this->offset = $offset;
        $this->rewind();

        return $this;
    }

    /**
     * Sets the amount of resources to be retrieved by get().
     * Default (and max) limit is 100.
     */
    public function limit(int $limit = 100): self
    {
        $this->limit = min(100, max(1, $limit));

        return $this;
    }

    public function current(): ?ResourceInterface
    {
        if (!$this->valid()) {
            return null;
        }

        return $this->resources[$this->currentResourceNumber];
    }

    public function next(): void
    {
        $this->currentResourceNumber++;
    }

    public function key(): ?int
    {
        if (!$this->valid()) {
            return null;
        }

        return $this->currentResourceNumber;
    }

    public function valid(): bool
    {
        return isset($this->resources[$this->currentResourceNumber])
            && $this->currentResourceNumber < ($this->offset + $this->limit);
    }

    public function rewind(): void
    {
        $this->currentResourceNumber = $this->offset;
    }
}
