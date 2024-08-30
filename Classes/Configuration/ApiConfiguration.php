<?php

/*
 * This file is part of the package jweiland/komm-one-jobs.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\KommOneJobs\Configuration;

class ApiConfiguration
{
    private string $endPoint = '';

    private string $username = '';

    private string $password = '';

    public function __construct(string $endPoint, string $username, string $password)
    {
        $this->endPoint = trim($endPoint);
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
