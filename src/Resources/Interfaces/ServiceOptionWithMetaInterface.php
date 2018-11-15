<?php

namespace MyParcelCom\ApiSdk\Resources\Interfaces;

interface ServiceOptionWithMetaInterface
{
    public function addMetaForServiceRate($serviceRateId, array $meta);

    public function getMetaForServiceRate($serviceRateId);

    // TODO: Think about this. Maybe it's not a good option.
    // If it is, expand this.
}