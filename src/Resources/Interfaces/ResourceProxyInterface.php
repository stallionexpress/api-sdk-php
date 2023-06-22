<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Resources\Interfaces;

use MyParcelCom\ApiSdk\MyParcelComApiInterface;

interface ResourceProxyInterface
{
    /**
     * Set the API with which to fetch the proxied resource.
     */
    public function setMyParcelComApi(MyParcelComApiInterface $api): self;
}
