<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Collection;

use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceInterface;

class RequestCollection implements CollectionInterface
{
    /** @var callable */
    protected $promiseCreator;

    /** @var callable */
    protected $resourceCreator;

    /** @var ResourceInterface[] */
    protected array $resources = [];

    protected int $offset = 0;
    protected int $limit = 100;
    protected int $count;
    protected int $currentResourceNumber = 0;

    public function __construct(callable $promiseCreator, callable $resourceCreator)
    {
        $this->promiseCreator = $promiseCreator;
        $this->resourceCreator = $resourceCreator;
    }

    /**
     * Counts the amount of resources in the collection.
     */
    public function count(): int
    {
        if (!isset($this->count)) {
            $this->get();
        }

        return $this->count;
    }

    /**
     * Retrieves the resources based on limit and offset.
     * Default (and max) limit is 100.
     *
     * @return ResourceInterface[]
     */
    public function get(): array
    {
        if (isset($this->count) && $this->offset >= $this->count) {
            return [];
        }

        // Retrieving the resources from the api is done by page.
        // A maximum of 100 resources per page can be retrieved at once.
        // We need to specify which page should be retrieved from the api,
        // or which pages if the set offset and limit overlaps two pages.
        $firstPage = (int) ceil(($this->offset + 1) / $this->limit);
        $secondPage = (int) ceil(($this->offset + $this->limit) / $this->limit);

        if (!isset($this->resources[$this->offset])) {
            $this->retrieveResources($firstPage);
        }

        if (
            $firstPage !== $secondPage
            && !isset($this->resources[$this->offset + $this->limit - 1])
            && (($secondPage - 1) * $this->limit) < $this->count
        ) {
            $this->retrieveResources($secondPage);
        }

        return array_filter($this->resources, function ($resourceNumber) {
            return $resourceNumber >= $this->offset && $resourceNumber < ($this->offset + $this->limit);
        }, ARRAY_FILTER_USE_KEY);
    }

    /**
     * Retrieve the resources for the given page number and store them in this->resources according to their number.
     */
    private function retrieveResources(int $pageNumber): void
    {
        $response = call_user_func_array($this->promiseCreator, [$pageNumber, $this->limit]);

        $body = json_decode((string) $response->getBody(), true);

        $this->count = $body['meta']['total_records'];
        $included = $body['included'] ?? null;
        $resources = call_user_func($this->resourceCreator, $body['data'], $included);

        $resourceNumber = ($pageNumber - 1) * $this->limit;

        array_walk($resources, function ($resource) use ($pageNumber, &$resourceNumber) {
            $this->resources[$resourceNumber] = $resource;
            $resourceNumber++;
        });
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

    public function key(): ?int
    {
        if (!$this->valid()) {
            return null;
        }

        return $this->currentResourceNumber;
    }

    public function valid(): bool
    {
        if (!isset($this->resources[$this->currentResourceNumber])) {
            $this->get();
        }

        return isset($this->resources[$this->currentResourceNumber])
            && $this->currentResourceNumber < ($this->offset + $this->limit);
    }

    public function rewind(): void
    {
        $this->currentResourceNumber = $this->offset;
    }

    public function next(): void
    {
        $this->currentResourceNumber++;
    }
}
