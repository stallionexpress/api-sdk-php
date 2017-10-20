<?php

namespace MyParcelCom\Sdk\Authentication;

use MyParcelCom\Sdk\Exceptions\AuthenticationException;

interface AuthenticatorInterface
{
    const GRANT_CLIENT_CREDENTIALS = 'client_credentials';

    /**
     * Authenticate with the OAuth2 server and return the header to be used in
     * requests to the API.
     *
     * @throws AuthenticationException
     * @return array
     */
    public function getAuthorizationHeader();
}
