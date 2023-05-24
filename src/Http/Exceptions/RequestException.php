<?php

declare(strict_types=1);

namespace MyParcelCom\ApiSdk\Http\Exceptions;

use Exception;
use MyParcelCom\ApiSdk\Http\Contracts\HttpClient\RequestExceptionInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Throwable;

class RequestException extends Exception implements RequestExceptionInterface
{
    public function __construct(
        private RequestInterface $request,
        private ResponseInterface $response,
        ?Throwable $previous = null,
    ) {
        parent::__construct($response->getReasonPhrase(), $response->getStatusCode(), $previous);
    }

    /**
     * Returns the request.
     *
     * The request object MAY be a different object from the one passed to ClientInterface::sendRequest()
     */
    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    /**
     * Returns the response of the failed request.
     *
     * May return null if there was no response.
     */
    public function getResponse(): ?ResponseInterface
    {
        return $this->response;
    }
}
