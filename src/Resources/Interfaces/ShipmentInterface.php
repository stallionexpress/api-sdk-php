<?php

namespace MyParcelCom\ApiSdk\Resources\Interfaces;

interface ShipmentInterface extends ResourceInterface
{
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
     * @param string $trackingCode
     * @return $this
     */
    public function setTrackingCode($trackingCode);

    /**
     * @return string
     */
    public function getTrackingCode();

    /**
     * @param string $trackingUrl
     * @return $this
     */
    public function setTrackingUrl($trackingUrl);

    /**
     * @return string
     */
    public function getTrackingUrl();

    /**
     * @deprecated Use Shipment::getPhysicalProperties()->setWeight() instead.
     *
     * @param int    $weight
     * @param string $unit
     * @return $this
     */
    public function setWeight($weight, $unit = PhysicalPropertiesInterface::WEIGHT_GRAM);

    /**
     * @deprecated Use Shipment::getPhysicalProperties()->getWeight() instead.
     *
     * @param string $unit
     * @return int
     */
    public function getWeight($unit = PhysicalPropertiesInterface::WEIGHT_GRAM);

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
     * @param ServiceContractInterface $serviceContract
     * @return $this
     */
    public function setServiceContract(ServiceContractInterface $serviceContract);

    /**
     * @return ServiceContractInterface
     */
    public function getServiceContract();

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
     * @param PhysicalPropertiesInterface $physicalProperties
     * @return $this
     */
    public function setPhysicalProperties(PhysicalPropertiesInterface $physicalProperties);

    /**
     * @return PhysicalPropertiesInterface|null
     */
    public function getPhysicalProperties();

    /**
     * @param PhysicalPropertiesInterface $physicalProperties
     * @return $this
     */
    public function setPhysicalPropertiesVerified(PhysicalPropertiesInterface $physicalProperties);

    /**
     * @return PhysicalPropertiesInterface|null
     */
    public function getPhysicalPropertiesVerified();

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
     * @param ShipmentStatusInterface $status
     * @return $this
     */
    public function setStatus(ShipmentStatusInterface $status);

    /**
     * @return ShipmentStatusInterface
     */
    public function getStatus();

    /**
     * @param ShipmentStatusInterface[] $statuses
     * @return $this
     */
    public function setStatusHistory(array $statuses);

    /**
     * @return ShipmentStatusInterface[]
     */
    public function getStatusHistory();

    /**
     * @param CustomsInterface $customs
     * @return $this
     */
    public function setCustoms(CustomsInterface $customs);

    /**
     * @return CustomsInterface
     */
    public function getCustoms();
}
