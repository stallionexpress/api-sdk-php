<?php

namespace MyParcelCom\Sdk\Authentication;

use MyParcelCom\Sdk\Exceptions\AuthenticationException;

interface AuthenticatorInterface
{
    const HEADER_AUTH = 'Authorization';
    const HEADER_ACCEPT = 'Accept';
    const MIME_TYPE_JSONAPI = 'application/vnd.api+json';
    const GRANT_CLIENT_CREDENTIALS = 'client_credentials';
    const SCOPES = 'shipments.show shipments.manage shops.show';

    /**
     * Authenticate with the OAuth2 server and return the header to be used in
     * requests to the API. When `$forceRefresh` is set to `true`, any possibly
     * cached header is ignored and a new request is done to the auth server.
     *
     * @param bool $forceRefresh
     * @throws AuthenticationException
     * @return array
     */
    public function getAuthorizationHeader($forceRefresh = false);
}
