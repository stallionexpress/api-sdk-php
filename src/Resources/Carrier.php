<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Resources;

use MyParcelCom\ApiSdk\Resources\Interfaces\CarrierInterface;
use MyParcelCom\ApiSdk\Resources\Interfaces\ResourceInterface;
use MyParcelCom\ApiSdk\Resources\Traits\JsonSerializable;
use MyParcelCom\ApiSdk\Resources\Traits\Resource;

class Carrier implements CarrierInterface
{
    use JsonSerializable {
        jsonSerialize as private serialize;
    }
    use Resource;

    const ATTRIBUTE_NAME = 'name';
    const ATTRIBUTE_CODE = 'code';
    const ATTRIBUTE_CREDENTIALS_FORMAT = 'credentials_format';
    const ATTRIBUTE_LABEL_MIME_TYPES = 'label_mime_types';

    private ?string $id = null;

    private string $type = ResourceInterface::TYPE_CARRIER;

    private array $attributes = [
        self::ATTRIBUTE_NAME               => null,
        self::ATTRIBUTE_CODE               => null,
        self::ATTRIBUTE_CREDENTIALS_FORMAT => [],
        self::ATTRIBUTE_LABEL_MIME_TYPES   => [],
    ];

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->attributes[self::ATTRIBUTE_NAME] = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->attributes[self::ATTRIBUTE_NAME];
    }

    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $code
     * @return $this
     */
    public function setCode($code)
    {
        $this->attributes[self::ATTRIBUTE_CODE] = $code;

        return $this;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->attributes[self::ATTRIBUTE_CODE];
    }

    /**
     * @param array $format
     * @return $this
     */
    public function setCredentialsFormat(array $format)
    {
        $this->attributes[self::ATTRIBUTE_CREDENTIALS_FORMAT] = $format;

        return $this;
    }

    /**
     * @return array
     */
    public function getCredentialsFormat()
    {
        return $this->attributes[self::ATTRIBUTE_CREDENTIALS_FORMAT];
    }

    /**
     * @param array $labelMimeTypes
     * @return $this
     */
    public function setLabelMimeTypes(array $labelMimeTypes)
    {
        $this->attributes[self::ATTRIBUTE_LABEL_MIME_TYPES] = $labelMimeTypes;

        return $this;
    }

    /**
     * @return string
     */
    public function getLabelMimeTypes()
    {
        return $this->attributes[self::ATTRIBUTE_LABEL_MIME_TYPES];
    }

    /**
     * This function puts all object properties in an array and returns it.
     */
    public function jsonSerialize(): array
    {
        // The 'credentials_format' can have camelCased properties, which get
        // changed to snake_case by the jsonSerialize() method. So ro prevent
        // that, we unset it and then reset it after serialization is done.
        $credentialsFormat = $this->attributes[self::ATTRIBUTE_CREDENTIALS_FORMAT];
        unset($this->attributes[self::ATTRIBUTE_CREDENTIALS_FORMAT]);

        $json = $this->serialize();

        if (!empty($credentialsFormat)) {
            $json['attributes'][self::ATTRIBUTE_CREDENTIALS_FORMAT] = $credentialsFormat;
        }

        return $json;
    }
}
