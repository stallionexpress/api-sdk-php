<?php

namespace MyParcelCom\ApiSdk\Resources;

use MyParcelCom\ApiSdk\Exceptions\MyParcelComException;
use MyParcelCom\ApiSdk\Resources\Interfaces\AddressInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ContractInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\CustomsInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\FileInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\PhysicalPropertiesInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ServiceOptionInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShipmentInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShipmentStatusInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ShopInterface;
use MyParcelCom\ApiSdk\Resources\Traits\JsonSerializable;

class Shipment implements ShipmentInterface
{
    use JsonSerializable;

    const ATTRIBUTE_BARCODE = 'barcode';
    const ATTRIBUTE_TRACKING_CODE = 'tracking_code';
    const ATTRIBUTE_TRACKING_URL = 'tracking_url';
    const ATTRIBUTE_DESCRIPTION = 'description';
    const ATTRIBUTE_AMOUNT = 'amount';
    const ATTRIBUTE_PRICE = 'price';
    const ATTRIBUTE_INSURANCE = 'insurance';
    const ATTRIBUTE_CURRENCY = 'currency';
    const ATTRIBUTE_WEIGHT = 'weight';
    const ATTRIBUTE_PHYSICAL_PROPERTIES = 'physical_properties';
    const ATTRIBUTE_RECIPIENT_ADDRESS = 'recipient_address';
    const ATTRIBUTE_SENDER_ADDRESS = 'sender_address';
    const ATTRIBUTE_PICKUP = 'pickup_location';
    const ATTRIBUTE_PICKUP_CODE = 'code';
    const ATTRIBUTE_PICKUP_ADDRESS = 'address';
    const ATTRIBUTE_CUSTOMS = 'customs';

    const RELATIONSHIP_CONTRACT = 'contract';
    const RELATIONSHIP_FILES = 'files';
    const RELATIONSHIP_OPTIONS = 'options';
    const RELATIONSHIP_SERVICE = 'service';
    const RELATIONSHIP_SHOP = 'shop';
    const RELATIONSHIP_STATUS = 'status';

    private $id;
    private $type = ResourceInterface::TYPE_SHIPMENT;
    private $statusHistory;
    private $statusHistoryCallback;
    private $attributes = [
        self::ATTRIBUTE_BARCODE             => null,
        self::ATTRIBUTE_TRACKING_CODE       => null,
        self::ATTRIBUTE_TRACKING_URL        => null,
        self::ATTRIBUTE_DESCRIPTION         => null,
        self::ATTRIBUTE_PRICE               => null,
        self::ATTRIBUTE_INSURANCE           => null,
        self::ATTRIBUTE_WEIGHT              => null,
        self::ATTRIBUTE_PHYSICAL_PROPERTIES => null,
        self::ATTRIBUTE_RECIPIENT_ADDRESS   => null,
        self::ATTRIBUTE_SENDER_ADDRESS      => null,
        self::ATTRIBUTE_PICKUP              => null,
        self::ATTRIBUTE_CUSTOMS             => null,
    ];

    private $relationships = [
        self::RELATIONSHIP_SHOP     => [
            'data' => null,
        ],
        self::RELATIONSHIP_SERVICE  => [
            'data' => null,
        ],
        self::RELATIONSHIP_CONTRACT => [
            'data' => null,
        ],
        self::RELATIONSHIP_STATUS   => [
            'data' => null,
        ],
        self::RELATIONSHIP_OPTIONS  => [
            'data' => [],
        ],
        self::RELATIONSHIP_FILES    => [
            'data' => [],
        ],
    ];

    private static $unitConversion = [
        self::WEIGHT_GRAM     => 1,
        self::WEIGHT_KILOGRAM => 1000,
        self::WEIGHT_POUND    => 453.59237,
        self::WEIGHT_OUNCE    => 28.349523125,
        self::WEIGHT_STONE    => 6350.29318,
    ];

    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * {@inheritdoc}
     */
    public function setRecipientAddress(AddressInterface $recipientAddress)
    {
        $this->attributes[self::ATTRIBUTE_RECIPIENT_ADDRESS] = $recipientAddress;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getRecipientAddress()
    {
        return $this->attributes[self::ATTRIBUTE_RECIPIENT_ADDRESS];
    }

    /**
     * {@inheritdoc}
     */
    public function setSenderAddress(AddressInterface $senderAddress)
    {
        $this->attributes[self::ATTRIBUTE_SENDER_ADDRESS] = $senderAddress;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getSenderAddress()
    {
        return $this->attributes[self::ATTRIBUTE_SENDER_ADDRESS];
    }

    /**
     * {@inheritdoc}
     */
    public function setPickupLocationCode($pickupLocationCode)
    {
        $this->attributes[self::ATTRIBUTE_PICKUP][self::ATTRIBUTE_PICKUP_CODE] = $pickupLocationCode;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPickupLocationCode()
    {
        return isset($this->attributes[self::ATTRIBUTE_PICKUP][self::ATTRIBUTE_PICKUP_CODE])
            ? $this->attributes[self::ATTRIBUTE_PICKUP][self::ATTRIBUTE_PICKUP_CODE]
            : null;
    }

    /**
     * {@inheritdoc}
     */
    public function setPickupLocationAddress(AddressInterface $pickupLocationAddress)
    {
        $this->attributes[self::ATTRIBUTE_PICKUP][self::ATTRIBUTE_PICKUP_ADDRESS] = $pickupLocationAddress;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPickupLocationAddress()
    {
        return isset($this->attributes[self::ATTRIBUTE_PICKUP][self::ATTRIBUTE_PICKUP_ADDRESS])
            ? $this->attributes[self::ATTRIBUTE_PICKUP][self::ATTRIBUTE_PICKUP_ADDRESS]
            : null;
    }

    /**
     * {@inheritdoc}
     */
    public function setDescription($description)
    {
        $this->attributes[self::ATTRIBUTE_DESCRIPTION] = $description;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getDescription()
    {
        return $this->attributes[self::ATTRIBUTE_DESCRIPTION];
    }

    /**
     * {@inheritdoc}
     */
    public function setPrice($price)
    {
        $this->attributes[self::ATTRIBUTE_PRICE][self::ATTRIBUTE_AMOUNT] = (int)$price;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPrice()
    {
        return isset($this->attributes[self::ATTRIBUTE_PRICE][self::ATTRIBUTE_AMOUNT])
            ? $this->attributes[self::ATTRIBUTE_PRICE][self::ATTRIBUTE_AMOUNT]
            : null;
    }

    /**
     * {@inheritdoc}
     */
    public function setInsuranceAmount($insuranceAmount)
    {
        $this->attributes[self::ATTRIBUTE_INSURANCE][self::ATTRIBUTE_AMOUNT] = $insuranceAmount;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getInsuranceAmount()
    {
        return isset($this->attributes[self::ATTRIBUTE_INSURANCE][self::ATTRIBUTE_AMOUNT])
            ? $this->attributes[self::ATTRIBUTE_INSURANCE][self::ATTRIBUTE_AMOUNT]
            : null;
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrency($currency)
    {
        $this->attributes[self::ATTRIBUTE_PRICE][self::ATTRIBUTE_CURRENCY] = $currency;
        $this->attributes[self::ATTRIBUTE_INSURANCE][self::ATTRIBUTE_CURRENCY] = $currency;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrency()
    {
        return isset($this->attributes[self::ATTRIBUTE_PRICE][self::ATTRIBUTE_CURRENCY])
            ? $this->attributes[self::ATTRIBUTE_PRICE][self::ATTRIBUTE_CURRENCY]
            : null;
    }

    /**
     * {@inheritdoc}
     */
    public function setBarcode($barcode)
    {
        $this->attributes[self::ATTRIBUTE_BARCODE] = $barcode;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getBarcode()
    {
        return $this->attributes[self::ATTRIBUTE_BARCODE];
    }

    /**
     * {@inheritdoc}
     */
    public function setTrackingCode($trackingCode)
    {
        $this->attributes[self::ATTRIBUTE_TRACKING_CODE] = $trackingCode;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTrackingCode()
    {
        return $this->attributes[self::ATTRIBUTE_TRACKING_CODE];
    }

    /**
     * {@inheritdoc}
     */
    public function setTrackingUrl($trackingUrl)
    {
        $this->attributes[self::ATTRIBUTE_TRACKING_URL] = $trackingUrl;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTrackingUrl()
    {
        return $this->attributes[self::ATTRIBUTE_TRACKING_URL];
    }

    /**
     * {@inheritdoc}
     */
    public function setWeight($weight, $unit = self::WEIGHT_GRAM)
    {
        if (!isset(self::$unitConversion[$unit])) {
            throw new MyParcelComException('invalid unit: ' . $unit);
        }

        $this->attributes[self::ATTRIBUTE_WEIGHT] = (int)round($weight * self::$unitConversion[$unit]);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getWeight($unit = self::WEIGHT_GRAM)
    {
        if (!isset(self::$unitConversion[$unit])) {
            throw new MyParcelComException('invalid unit: ' . $unit);
        }

        return (int)round($this->attributes[self::ATTRIBUTE_WEIGHT] / self::$unitConversion[$unit]);
    }

    /**
     * {@inheritdoc}
     */
    public function setPhysicalProperties(PhysicalPropertiesInterface $physicalProperties)
    {
        $this->attributes[self::ATTRIBUTE_PHYSICAL_PROPERTIES] = $physicalProperties;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getPhysicalProperties()
    {
        return $this->attributes[self::ATTRIBUTE_PHYSICAL_PROPERTIES];
    }

    /**
     * {@inheritdoc}
     */
    public function setShop(ShopInterface $shop)
    {
        $this->relationships[self::RELATIONSHIP_SHOP]['data'] = $shop;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getShop()
    {
        return $this->relationships[self::RELATIONSHIP_SHOP]['data'];
    }

    /**
     * {@inheritdoc}
     */
    public function setService(ServiceInterface $service)
    {
        $this->relationships[self::RELATIONSHIP_SERVICE]['data'] = $service;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getService()
    {
        return $this->relationships[self::RELATIONSHIP_SERVICE]['data'];
    }

    /**
     * {@inheritdoc}
     */
    public function setOptions(array $options)
    {
        $this->relationships[self::RELATIONSHIP_OPTIONS]['data'] = [];

        array_walk($options, function ($option) {
            $this->addOption($option);
        });

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addOption(ServiceOptionInterface $option)
    {
        $this->relationships[self::RELATIONSHIP_OPTIONS]['data'][] = $option;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getOptions()
    {
        return $this->relationships[self::RELATIONSHIP_OPTIONS]['data'];
    }

    /**
     * {@inheritdoc}
     */
    public function setFiles(array $files)
    {
        $this->relationships[self::RELATIONSHIP_FILES]['data'] = [];

        array_walk($files, function ($file) {
            $this->addFile($file);
        });

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addFile(FileInterface $file)
    {
        $this->relationships[self::RELATIONSHIP_FILES]['data'][] = $file;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFiles($type = null)
    {
        if ($type === null) {
            return $this->relationships[self::RELATIONSHIP_FILES]['data'];
        }

        return array_filter($this->relationships[self::RELATIONSHIP_FILES]['data'], function (FileInterface $file) use ($type) {
            return $file->getResourceType() === $type;
        });
    }

    /**
     * {@inheritdoc}
     */
    public function setStatus(ShipmentStatusInterface $status)
    {
        $this->relationships[self::RELATIONSHIP_STATUS]['data'] = $status;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getStatus()
    {
        return $this->relationships[self::RELATIONSHIP_STATUS]['data'];
    }

    /**
     * {@inheritdoc}
     */
    public function setStatusHistory(array $statuses)
    {
        $this->statusHistory = $statuses;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getStatusHistory()
    {
        if (!isset($this->statusHistory) && isset($this->statusHistoryCallback)) {
            $this->setStatusHistory(call_user_func($this->statusHistoryCallback));
        }

        return $this->statusHistory;
    }

    /**
     * Set the callback to use when retrieving the status history.
     *
     * @param callable $callback
     * @return $this
     */
    public function setStatusHistoryCallback(callable $callback)
    {
        $this->statusHistoryCallback = $callback;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function setContract(ContractInterface $contract)
    {
        $this->relationships[self::RELATIONSHIP_CONTRACT]['data'] = $contract;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getContract()
    {
        return $this->relationships[self::RELATIONSHIP_CONTRACT]['data'];
    }

    /**
     * {@inheritdoc}
     */
    public function setCustoms(CustomsInterface $customs)
    {
        $this->attributes[self::ATTRIBUTE_CUSTOMS] = $customs;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCustoms()
    {
        return $this->attributes[self::ATTRIBUTE_CUSTOMS];
    }
}
