<?php

namespace Lianhua\SymphonInvoice;

/*
SymphonInvoice Library
Copyright (C) 2020  Lianhua Studio

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

use Exception;
use Lianhua\SymphonInvoice\base\ChorusAPIPath;

class ChorusAPI
{
    public const OAUTH_URL = "https://oauth.aife.economie.gouv.fr/api/oauth/token";
    public const OAUTH_SANDBOX_URL = "https://sandbox-oauth.aife.economie.gouv.fr/api/oauth/token";

    /**
     * Sets the API in sandbox mode
     * @var bool
     */
    private $sandbox;

    /**
     * The oauth bearer for requests
     * @var string
     */
    private $bearer;

    /**
     * The choruspro login/password for requests
     * @var string
     */
    private $chorusProAccount;

    /**
     * Returns the POST fields for the authentification request
     * @param string $clientId The oauth client id
     * @param string $clientSecret The oauth client secret
     * @return string The fields encoded in x-www-form-urlencoded
     */
    private function getOauthPostFields(string $clientId, string $clientSecret): string
    {
        $pf = "grant_type=client_credentials";
        $pf .= "&client_id=" . $clientId;

        if (!empty($clientSecret)) {
            $pf .= "&client_secret=" . $clientSecret;
        }

        $pf .= "&scope=openid";

        return $pf;
    }

    /**
     * Connect to oauth
     * @param string $clientId The oauth client id
     * @param string $clientSecret The oauth client secret
     * @return void
     * @throws Exception
     */
    public function connectWithOauth(string $clientId, string $clientSecret): void
    {
        if (empty($clientId)) {
            throw new Exception("You must provide a clientId in order to connect");
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->sandbox ? self::OAUTH_SANDBOX_URL : self::OAUTH_URL);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-type" => "application/x-www-form-urlencoded"]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $this->getOauthPostFields($clientId, $clientSecret));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $response  = json_decode(curl_exec($ch), true);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpcode == 200) {
            $this->bearer = $response["access_token"];
        } else {
            throw new Exception($response["error"] . ": "  . $response["error_description"], $httpcode);
        }
    }

    /**
     * Sets the choruspro account to use
     * @param string $user The user id
     * @param string $password The password
     * @return void
     */
    public function setChorusproCredentials(string $user, string $password): void
    {
        $this->chorusProAccount = base64_encode($user . ":" . $password);
    }

    /**
     * Connect with a bearer
     * @param string $bearer
     * @return void
     */
    public function connectWithBearer(string $bearer): void
    {
        $this->bearer = $bearer;
    }

    /**
     * Returns the bearer
     * @return null|string The bearer
     */
    public function getBearer(): ?string
    {
        return $this->bearer;
    }

    /**
     * Sets the API in sandbox mode
     * @param bool $sandbox The mode
     * @return void
     */
    public function sandboxMode(bool $sandbox): void
    {
        $this->sandbox = $sandbox;
    }

    /**
     * Executes a request to ChorusPro Invoice
     * @param ChorusAPIPath $request The request class
     * @return null|array The response from ChrousPro
     * @throws Exception
     */
    public function request(ChorusAPIPath $request): ?array
    {
        return $request->execute($this->bearer, $this->chorusProAccount, $this->sandbox);
    }

    /**
     * The constructor
     * @param bool $sandbox Sets the API in sandbox mode
     * @return void
     */
    public function __construct(bool $sandbox = false)
    {
        $this->sandbox = $sandbox;
    }
}
