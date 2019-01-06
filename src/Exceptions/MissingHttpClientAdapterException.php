<?php

namespace MyParcelCom\ApiSdk\Exceptions;

/**
 * Thrown when an existing HTTP client was found, but
 * the required adapter package has not been installed.
 */
class MissingHttpClientAdapterException extends \Exception
{

}