<?php

namespace MyParcelCom\Sdk\Resources\Interfaces;

use MyParcelCom\Sdk\MyParcelComApiInterface;

interface ResourceProxyInterface
{
    /**
     * Set the API with which to fetch the proxied resource.
     *
     * @param MyParcelComApiInterface $api
     * @return $this
     */
    public function setMyParcelComApi(MyParcelComApiInterface $api);
}
