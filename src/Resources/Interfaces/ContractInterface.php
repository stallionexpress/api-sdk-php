<?php

namespace MyParcelCom\ApiSdk\Resources\Interfaces;

interface ContractInterface extends ResourceInterface
{
    /**
     * @param string $id
     * @return $this
     */
    public function setId($id);

    /**
     * @param ServiceGroupInterface[] $groups
     * @return $this
     */
    public function setGroups(array $groups);

    /**
     * @param ServiceGroupInterface $group
     * @return $this
     */
    public function addGroup(ServiceGroupInterface $group);

    /**
     * @return ServiceGroupInterface[]
     */
    public function getGroups();

    /**
     * @param ServiceOptionInterface[] $options
     * @return $this
     */
    public function setServiceOptions(array $options);

    /**
     * @param ServiceOptionInterface $option
     * @return $this
     */
    public function addServiceOption(ServiceOptionInterface $option);

    /**
     * @return ServiceOptionInterface[]
     */
    public function getServiceOptions();

    /**
     * @param ServiceInsuranceInterface[] $insurances
     * @return $this
     */
    public function setInsurances(array $insurances);

    /**
     * @param ServiceInsuranceInterface $insurance
     * @return $this
     */
    public function addInsurance(ServiceInsuranceInterface $insurance);

    /**
     * @return ServiceInsuranceInterface[]
     */
    public function getInsurances();
}
