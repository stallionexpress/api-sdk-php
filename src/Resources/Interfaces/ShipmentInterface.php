<?php

namespace MyParcelCom\Sdk\Resources\Interfaces;

interface ShipmentInterface extends ResourceInterface
{
    const WEIGHT_GRAM = 'grams';
    const WEIGHT_KILOGRAM = 'kilograms';
    const WEIGHT_POUND = 'pounds';
    const WEIGHT_OUNCE = 'ounces';
    const WEIGHT_STONE = 'stones';

    /**
     * @param string $id
     * @return $this
     */
    public function setId($id);

    /**
     * @param AddressInterface $recipientAddress
     * @return $this
     */
    public function setRecipientAddress(AddressInterface $recipientAddress);

    /**
     * @return AddressInterface
     */
    public function getRecipientAddress();

    /**
     * @param AddressInterface $senderAddress
     * @return $this
     */
    public function setSenderAddress(AddressInterface $senderAddress);

    /**
     * @return AddressInterface
     */
    public function getSenderAddress();

    /**
     * @param string $pickupLocationCode
     * @return $this
     */
    public function setPickupLocationCode($pickupLocationCode);

    /**
     * @return string|null
     */
    public function getPickupLocationCode();

    /**
     * @param AddressInterface $pickupLocationAddress
     * @return $this
     */
    public function setPickupLocationAddress(AddressInterface $pickupLocationAddress);

    /**
     * @return AddressInterface|null
     */
    public function getPickupLocationAddress();

    /**
     * @param string $description
     * @return $this
     */
    public function setDescription($description);

    /**
     * @return string|null
     */
    public function getDescription();

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
     * @param int $insuranceAmount
     * @return $this
     */
    public function setInsuranceAmount($insuranceAmount);

    /**
     * @return int
     */
    public function getInsuranceAmount();

    /**
     * @param string $currency
     * @return $this
     */
    public function setCurrency($currency);

    /**
     * @return string
     */
    public function getCurrency();

    /**
     * @param string $barcode
     * @return $this
     */
    public function setBarcode($barcode);

    /**
     * @return string
     */
    public function getBarcode();

    /**
     * @param int    $weight
     * @param string $unit
     * @return $this
     */
    public function setWeight($weight, $unit = self::WEIGHT_GRAM);

    /**
     * @param string $unit
     * @return int
     */
    public function getWeight($unit = self::WEIGHT_GRAM);

    /**
     * @param ShopInterface $shop
     * @return $this
     */
    public function setShop(ShopInterface $shop);

    /**
     * @return ShopInterface
     */
    public function getShop();

    /**
     * @param ServiceInterface $service
     * @return $this
     */
    public function setService(ServiceInterface $service);

    /**
     * @return ServiceInterface
     */
    public function getService();

    /**
     * @param ContractInterface $contract
     * @return $this
     */
    public function setContract(ContractInterface $contract);

    /**
     * @return ContractInterface
     */
    public function getContract();

    /**
     * @param ServiceOptionInterface[] $options
     * @return $this
     */
    public function setOptions(array $options);

    /**
     * @param ServiceOptionInterface $option
     * @return $this
     */
    public function addOption(ServiceOptionInterface $option);

    /**
     * @return ServiceOptionInterface[]
     */
    public function getOptions();

    /**
     * @param PhysicalPropertiesInterface $physicalProperties
     * @return $this
     */
    public function setPhysicalProperties(PhysicalPropertiesInterface $physicalProperties);

    /**
     * @return PhysicalPropertiesInterface|null
     */
    public function getPhysicalProperties();

    /**
     * @param FileInterface[] $files
     * @return $this
     */
    public function setFiles(array $files);

    /**
     * @param FileInterface $file
     * @return $this
     */
    public function addFile(FileInterface $file);

    /**
     * @param string|null $type
     * @return FileInterface[]
     */
    public function getFiles($type = null);

    /**
     * @param StatusInterface $status
     * @return $this
     */
    public function setStatus(StatusInterface $status);

    /**
     * @return StatusInterface
     */
    public function getStatus();
}
