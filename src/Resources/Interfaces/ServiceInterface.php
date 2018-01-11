<?php

namespace MyParcelCom\ApiSdk\Resources\Interfaces;

interface ServiceInterface extends ResourceInterface
{
    const PACKAGE_TYPE_PARCEL = 'parcel';
    const PACKAGE_TYPE_LETTER = 'letter';
    const PACKAGE_TYPE_LETTERBOX = 'letterbox';

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
     * @param string $packageType
     * @return $this
     */
    public function setPackageType($packageType);

    /**
     * @return string
     */
    public function getPackageType();

    /**
     * @return int|null
     */
    public function getTransitTimeMin();

    /**
     * @param int|null $transitTimeMin
     * @return $this
     */
    public function setTransitTimeMin($transitTimeMin);

    /**
     * @return int|null
     */
    public function getTransitTimeMax();

    /**
     * @param int|null $transitTimeMax
     * @return $this
     */
    public function setTransitTimeMax($transitTimeMax);

    /**
     * @param CarrierInterface $carrier
     * @return $this
     */
    public function setCarrier(CarrierInterface $carrier);

    /**
     * @return CarrierInterface
     */
    public function getCarrier();

    /**
     * @param RegionInterface $region
     * @return $this
     */
    public function setRegionFrom(RegionInterface $region);

    /**
     * @return RegionInterface
     */
    public function getRegionFrom();

    /**
     * @param RegionInterface $region
     * @return $this
     */
    public function setRegionTo(RegionInterface $region);

    /**
     * @return RegionInterface
     */
    public function getRegionTo();

    /**
     * @param ContractInterface[] $contracts
     * @return $this
     */
    public function setContracts(array $contracts);

    /**
     * @param ContractInterface $contract
     * @return $this
     */
    public function addContract(ContractInterface $contract);

    /**
     * @return ContractInterface[]
     */
    public function getContracts();
}
