<?php

namespace MyParcelCom\ApiSdk\Resources\Interfaces;

interface ServiceOptionInterface extends ResourceInterface
{
    /**
     * @param string $id
     * @return $this
     */
    public function setId($id);

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param int $price
     * @return $this
     */
    public function setPrice($price);

    /**
     * @return int
     */
    public function getPrice();

    /**
     * @param string $currency
     * @return $this
     */
    public function setCurrency($currency);

    /**
     * @return string
     */
    public function getCurrency();
}
