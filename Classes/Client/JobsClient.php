<?php

/*
 * This file is part of the package jweiland/komm-one-jobs.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\KommOneJobs\Client;

use GuzzleHttp\Exception\GuzzleException;
use JWeiland\KommOneJobs\Configuration\ApiConfiguration;
use Psr\Log\LoggerInterface;
use TYPO3\CMS\Core\Http\RequestFactory;

class JobsClient
{
    private RequestFactory $requestFactory;

    private LoggerInterface $logger;

    public function __construct(RequestFactory $requestFactory, LoggerInterface $logger)
    {
        $this->requestFactory = $requestFactory;
        $this->logger = $logger;
    }

    public function request(ApiConfiguration $apiConfiguration): string
    {
        try {
            $response = $this->requestFactory->request(
                $apiConfiguration->getEndPoint(),
                'GET',
                [
                    'auth' => [
                        $apiConfiguration->getUsername(),
                        $apiConfiguration->getPassword(),
                    ],
                    'allow_redirects' => false,
                    'timeout' => 10,
                ]
            );
        } catch (GuzzleException $exception) {
            $this->logger->error($exception->getMessage());
            return '';
        }

        if ($response->getStatusCode() === 302) {
            // Request initiates a redirect. In most cases we will get HTML then.
            $this->logger->error('Your server is not allowed to access the API or credentials are wrong');
            return '';
        } elseif ($response->getStatusCode() !== 200) {
            $this->logger->error(
                'The defined endpoint results in status code ' . $response->getStatusCode() . '. Skipping.'
            );
            return '';
        }

        return (string)$response->getBody();
    }
}
