<?php

namespace MyParcelCom\Sdk\Resources\Traits;

use MyParcelCom\Sdk\MyParcelComApiInterface;
use MyParcelCom\Sdk\Resources\Interfaces\ResourceInterface;

trait ProxiesResource
{
    /** @var ResourceInterface */
    private $resource;
    /** @var MyParcelComApiInterface */
    private $api;

    /**
     * Set the api to use when retrieving the resource.
     *
     * @param MyParcelComApiInterface $api
     * @return $this
     */
    public function setMyParcelComApi(MyParcelComApiInterface $api = null)
    {
        $this->api = $api;

        return $this;
    }

    /**
     * Get the resource that this instance is a proxy for.
     *
     * @return ResourceInterface
     */
    protected function getResource()
    {
        if (!isset($this->resource) && isset($this->api)) {
            $this->resource = $this->api->getResourceById($this->getType(), $this->getId());
        }

        return $this->resource;
    }
}
