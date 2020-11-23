<?php

namespace Lianhua\SymphonInvoice\base;

use Exception;

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

abstract class ChorusAPIPath
{
    /**
     * The root path of API in sandbox mode
     */
    public const SANDBOX_ROOT = "https://sandbox-api.aife.economie.gouv.fr/cpro/factures/v1";

    /**
     * The root path of API in production mode
     */
    public const ROOT = "https://api.aife.economie.gouv.fr/cpro/factures/v1";

    /**
     * Returns the path of the API function
     * @return string The path
     */
    abstract protected function getPath(): string;

    /**
     * Returns the POST data fields
     * @return array The data fields
     * @throws Exception
     */
    abstract protected function getPostFields(): array;

    /**
     * Returns the full url
     * @param bool $sandbox The sandbox mode
     * @return string
     */
    protected function getUrl(bool $sandbox): string
    {
        $url = $sandbox ? self::SANDBOX_ROOT : self::ROOT;
        $url .= $this->getPath();

        return $url;
    }

    /**
     * Executes the request
     * @param string $bearer The bearer
     * @param string $chorusProAccount The choruspro account
     * @param bool $sandbox The sandbox mode
     * @return array The return data
     * @throws Exception
     */
    public function execute(string $bearer, string $chorusProAccount, bool $sandbox): ?array
    {
        $headers = [];
        $headers[] = "cpro-account: " . $chorusProAccount;
        $headers[] = "Authorization: Bearer " . $bearer;
        $headers[] = "Content-type: application/json;charset=utf-8";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->getUrl($sandbox));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($this->getPostFields()));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $response  = json_decode(curl_exec($ch), true);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpcode == 200) {
            return $response;
        } else {
            throw new Exception($response["error"] . ": "  . $response["error_description"], $httpcode);
        }
    }

    /**
     * Throws an exception for missing values
     * @param string $varName The var name
     * @return void
     * @throws Exception
     */
    protected function missingValueException(string $varName): void
    {
        throw new Exception("You must provide a value for var " . $varName);
    }
}
