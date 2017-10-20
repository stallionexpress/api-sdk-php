<?php

namespace MyParcelCom\Sdk\Resources\Interfaces;

interface JsonInterface
{
    /**
     * @return array
     */
    public function toArray();

    /**
     * @return string
     */
    public function toJson();
}
