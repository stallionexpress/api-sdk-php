<?php

namespace MyParcelCom\Sdk\Resources\Interfaces;

use Psr\Http\Message\StreamInterface;

/**
 * Interface FileInterface
 *
 * @package MyParcelCom\Sdk\Resources\Interfaces
 */
interface FileInterface extends ResourceInterface
{
    const RESOURCE_TYPE_LABEL = 'label';
    const RESOURCE_TYPE_PRINTCODE = 'printcode';
    const RESOURCE_TYPE_INVOICE = 'invoice';

    /**
     * Set the identifier for this file.
     *
     * @param string $id
     * @return $this
     */
    public function setId($id);

    /**
     * Set the type of file. See constants for possible options.
     *
     * @param string $resourceType
     * @return $this
     */
    public function setResourceType($resourceType);

    /**
     * Get the type of file. See constants for possible options.
     *
     * @return string
     */
    public function getResourceType();

    /**
     * Set the formats that this file has available.
     *
     * @param array $formats
     * @return $this
     */
    public function setFormats(array $formats);

    /**
     * Add a format this file is available as.
     *
     * @param string $mimeType
     * @param string $extension
     * @return $this
     */
    public function addFormat($mimeType, $extension);

    /**
     * Get an array of the available formats for this file. Each format is an
     * associative array with keys 'mime_type' and 'extension'.
     *
     * @return array
     */
    public function getFormats();

    /**
     * Get the a stream for this file. If no mime type supplied one is chosen in
     * the following order from the available formats:
     * application/pdf > image/png > image/jpeg > other
     *
     * @param string|null $mimeType
     * @return StreamInterface
     */
    public function getStream($mimeType = null);

    /**
     * Get the file data as a base64 encoded string. If no mime type supplied
     * one is chosen in the following order from the available formats:
     * application/pdf > image/png > image/jpeg > other
     *
     * @param string|null $mimeType
     * @return string
     */
    public function getBase64Data($mimeType = null);

    /**
     * Get a path for this file saved in a temporary location on the filesystem.
     * If no mime type is supplied one is chosen in the following order from the
     * available formats:
     * application/pdf > image/png > image/jpeg > other
     *
     * @param string|null $mimeType
     * @return string
     */
    public function getTemporaryFilePath($mimeType = null);
}
