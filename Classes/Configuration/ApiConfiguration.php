<?php

/*
 * This file is part of the package jweiland/komm-one-jobs.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\KommOneJobs\Configuration;

use TYPO3\CMS\Core\Http\Uri;

class ApiConfiguration
{
    private string $endPoint = '';

    private string $username = '';

    private string $password = '';

    public function __construct(string $endPoint, string $username, string $password)
    {
        // Komm.ONE lacks comprehensive error handling. In case of any failure, they simply issue a 302 redirect
        // to an HTML login page. Therefore, it is necessary to prevent any kind of redirect, such as
        // from http:// to https://. This approach ensures that any general redirect from Komm.ONE is treated
        // and processed as an error on our side.
        //
        // By explicitly setting the URI scheme to https, we proactively avoid accidental protocol downgrades
        // and eliminate the possibility of a transparent http-to-https redirection. This ensures that
        // all communication is performed securely via HTTPS and guards against any redirect-based error masking
        // in downstream handling.
        $endpointUri = new Uri(trim($endPoint));
        $endpointUri = $endpointUri->withScheme('https');

        $this->endPoint = (string)$endpointUri;
        $this->username = trim($username);
        $this->password = trim($password);
    }

    public function getEndPoint(): string
    {
        return $this->endPoint;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
}
