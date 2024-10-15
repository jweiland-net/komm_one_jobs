<?php

/*
 * This file is part of the package jweiland/komm-one-jobs.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\KommOneJobs\Service;

use JWeiland\KommOneJobs\Client\JobsClient;
use JWeiland\KommOneJobs\Configuration\ApiConfiguration;
use JWeiland\KommOneJobs\Configuration\ExtConf;
use JWeiland\KommOneJobs\Configuration\JobFilter;
use JWeiland\KommOneJobs\Parser\XmlJobParser;
use Psr\Log\LoggerInterface;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class JobService
{
    private const TEMP_FILE_PATH = '%s/kommOneJobs/jobs-ce-%d.xml';

    private const TEST_XML_JOBS = 'EXT:komm_one_jobs/Tests/Functional/Fixtures/test.xml';

    private JobsClient $client;

    private XmlJobParser $xmlJobParser;

    private LoggerInterface $logger;

    public function __construct(JobsClient $client, XmlJobParser $xmlJobParser, LoggerInterface $logger)
    {
        $this->client = $client;
        $this->xmlJobParser = $xmlJobParser;
        $this->logger = $logger;
    }

    public function updateStoredJobs(int $contentElementUid, ApiConfiguration $apiConfiguration): bool
    {
        if ($this->getExtConf()->isActivateXmlTestFile()) {
            return true;
        }

        $tempFileForJobs = $this->getTempFilePath($contentElementUid);

        if ($content = $this->client->request($apiConfiguration)) {
            $errorMessages = GeneralUtility::writeFileToTypo3tempDir($tempFileForJobs, $content);
        } else {
            $errorMessages = GeneralUtility::writeFileToTypo3tempDir($tempFileForJobs, '');
        }

        if ($errorMessages === null) {
            return true;
        }

        $this->logger->error($errorMessages);

        return false;
    }

    public function getStoredJobs(int $contentElementUid, JobFilter $filter): array
    {
        $tempFileForJobs = $this->getTempFilePath($contentElementUid);
        if ($this->getExtConf()->isActivateXmlTestFile()) {
            $tempFileForJobs = GeneralUtility::getFileAbsFileName(self::TEST_XML_JOBS);
        }

        if (!GeneralUtility::validPathStr($tempFileForJobs)) {
            $this->logger->error('Temporary file not found: ' . $tempFileForJobs);
            return [];
        }

        $xmlContent = trim(file_get_contents($tempFileForJobs));
        if ($xmlContent === '') {
            $this->logger->error('Temporary file with stored jobs is empty: ' . $tempFileForJobs);
            return [];
        }

        return $this->sortJobs(
            $this->xmlJobParser->parse($xmlContent, $filter)
        );
    }

    public function filterJobs(array $jobs, JobFilter $filter): array
    {
        // Early return, if time model is not selected
        $timeModel = $filter->getTimeModel();
        if ($timeModel === '') {
            return $jobs;
        }

        return array_filter($jobs, static function ($job) use ($timeModel): bool {
            if (!isset($job['time_model'])) {
                return false;
            }

            // <time_model/> will be converted to empty array
            if ($job['time_model'] === []) {
                return false;
            }

            return $job['time_model'] === $timeModel;
        });
    }

    public function searchJobs(array $jobs, string $search = ''): array
    {
        // Early return, if no search
        $search = trim($search);
        if ($search === '') {
            return $jobs;
        }

        return array_filter($jobs, static function ($job) use ($search): bool {
            if (!isset($job['title'], $job['description'])) {
                return false;
            }

            // <title/> will be converted to empty array
            if ($job['title'] === []) {
                $job['title'] = '';
            }

            // <description/> will be converted to empty array
            if ($job['description'] === []) {
                $job['description'] = '';
            }

            return (
                mb_stripos($job['title'], $search) !== false
                || mb_stripos($job['description'], $search) !== false
            );
        });
    }

    private function sortJobs(array $jobs, string $column = 'title'): array
    {
        $sortValues = array_column($jobs, $column);
        array_multisort($sortValues, SORT_ASC, SORT_STRING, $jobs);

        return $jobs;
    }

    private function getTempFilePath(int $contentElementUid): string
    {
        return sprintf(self::TEMP_FILE_PATH, Environment::getVarPath(), $contentElementUid);
    }

    private function getExtConf(): ExtConf
    {
        return GeneralUtility::makeInstance(ExtConf::class);
    }
}
