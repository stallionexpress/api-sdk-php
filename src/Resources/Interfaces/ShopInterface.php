<?php

namespace MyParcelCom\Sdk\Resources\Interfaces;

interface ShopInterface extends ResourceInterface
{
    /**
     * @param string $id
     * @return $this
     */
    public function setId($id);

    /**
     * @param $name
     * @return $this
     */
    public function setName($name);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param AddressInterface $billingAddress
     * @return $this
     */
    public function setBillingAddress(AddressInterface $billingAddress);

    /**
     * @return AddressInterface
     */
    public function getBillingAddress();

    /**
     * @param AddressInterface $returnAddress
     * @return $this
     */
    public function setReturnAddress(AddressInterface $returnAddress);

    /**
     * @return AddressInterface
     */
    public function getReturnAddress();

    /**
     * @param RegionInterface $region
     * @return $this
     */
    public function setRegion(RegionInterface $region);

    /**
     * @return RegionInterface
     */
    public function getRegion();
}
