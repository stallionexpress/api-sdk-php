<?php

namespace MyParcelCom\Sdk\Resources;

use MyParcelCom\Sdk\Resources\Interfaces\FileInterface;
use MyParcelCom\Sdk\Resources\Interfaces\ResourceInterface;
use MyParcelCom\Sdk\Resources\Traits\JsonSerializable;
use Psr\Http\Message\StreamInterface;

class File implements FileInterface
{
    use JsonSerializable;

    const ATTRIBUTE_RESOURCE_TYPE = 'resource_type';
    const ATTRIBUTE_FORMATS = 'formats';

    /** @var string */
    private $id;
    /** @var string */
    private $type = ResourceInterface::TYPE_FILE;
    /** @var array */
    private $attributes = [
        self::ATTRIBUTE_FORMATS       => [],
        self::ATTRIBUTE_RESOURCE_TYPE => null,
    ];

    /** @var StreamInterface[] */
    private $streams = [];
    /** @var string[] */
    private $base64Data = [];
    /** @var string[] */
    private $paths = [];

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
    public function setResourceType($resourceType)
    {
        $this->attributes[self::ATTRIBUTE_RESOURCE_TYPE] = $resourceType;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getResourceType()
    {
        return $this->attributes[self::ATTRIBUTE_RESOURCE_TYPE];
    }

    /**
     * {@inheritdoc}
     */
    public function setFormats(array $formats)
    {
        $this->attributes[self::ATTRIBUTE_FORMATS] = [];
        array_walk($formats, function ($format) {
            $this->addFormat($format['mime_type'], $format['extension']);
        });

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addFormat($mimeType, $extension)
    {
        $this->attributes[self::ATTRIBUTE_FORMATS][] = [
            'mime_type' => $mimeType,
            'extension' => $extension,
        ];

        usort($this->attributes[self::ATTRIBUTE_FORMATS], function ($formatA, $formatB) {
            $mimeTypeOrder = ['application/pdf' => -3, 'image/png' => -2, 'image/jpeg' => -1];

            return $mimeTypeOrder[$formatA['mime_type']] - $mimeTypeOrder[$formatB['mime_type']];
        });

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getFormats()
    {
        return $this->attributes[self::ATTRIBUTE_FORMATS];
    }

    /**
     * {@inheritdoc}
     */
    public function setStream(StreamInterface $stream, $mimeType)
    {
        $this->streams[$mimeType] = $stream;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getStream($mimeType = null)
    {
        if ($mimeType === null) {
            foreach ($this->getFormats() as $format) {
                $stream = $this->getStream($format['mime_type']);

                if ($stream !== null) {
                    return $stream;
                }
            };

            return null;
        }

        return isset($this->streams[$mimeType])
            ? $this->streams[$mimeType]
            : null;
    }

    /**
     * {@inheritdoc}
     */
    public function setBase64Data($data, $mimeType)
    {
        $this->base64Data[$mimeType] = $data;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getBase64Data($mimeType = null)
    {
        if ($mimeType === null) {
            foreach ($this->getFormats() as $format) {
                $data = $this->getBase64Data($format['mime_type']);

                if ($data !== null) {
                    return $data;
                }
            };

            return null;
        }

        if (isset($this->base64Data[$mimeType])) {
            return $this->base64Data[$mimeType];
        }
        if (isset($this->paths[$mimeType])) {
            return $this->base64Data[$mimeType] = base64_encode(file_get_contents($this->paths[$mimeType]));
        }
        if (isset($this->streams[$mimeType])) {
            return $this->base64Data[$mimeType] = base64_encode($this->streams[$mimeType]->getContents());
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function setTemporaryFilePath($path, $mimeType)
    {
        $this->paths[$mimeType] = $path;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTemporaryFilePath($mimeType = null)
    {
        if ($mimeType === null) {
            foreach ($this->getFormats() as $format) {
                $path = $this->getTemporaryFilePath($format['mime_type']);

                if ($path !== null) {
                    return $path;
                }
            };

            return null;
        }

        if (isset($this->paths[$mimeType])) {
            return $this->paths[$mimeType];
        }
        if (isset($this->base64Data[$mimeType])) {
            $path = tempnam(sys_get_temp_dir(), 'myparcelcom_file');
            file_put_contents($path, base64_decode($this->base64Data[$mimeType]));

            return $this->paths[$mimeType] = $path;
        }
        if (isset($this->streams[$mimeType])) {
            $path = tempnam(sys_get_temp_dir(), 'myparcelcom_file');
            file_put_contents($path, (string)$this->streams[$mimeType]);

            return $this->paths[$mimeType] = $path;
        }

        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize()
    {
        $values = get_object_vars($this);
        unset($values['streams']);
        unset($values['base64Data']);
        unset($values['paths']);

        $json = $this->arrayValuesToArray($values);

        if (isset($json['attributes']) && $this->isEmpty($json['attributes'])) {
            unset($json['attributes']);
        }
        if (isset($json['relationships']) && $this->isEmpty($json['relationships'])) {
            unset($json['relationships']);
        }

        return $json;
    }
}
