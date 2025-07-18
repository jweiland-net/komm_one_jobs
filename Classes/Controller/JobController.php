<?php

/*
 * This file is part of the package jweiland/komm-one-jobs.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\KommOneJobs\Controller;

use JWeiland\KommOneJobs\Configuration\ApiConfiguration;
use JWeiland\KommOneJobs\Configuration\JobFilter;
use JWeiland\KommOneJobs\Exception\InvalidApiConfigurationException;
use JWeiland\KommOneJobs\Service\JobService;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Http\HtmlResponse;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * JobController
 */
class JobController extends ActionController
{
    protected JobService $jobService;

    public function injectJobService(JobService $jobService): void
    {
        $this->jobService = $jobService;
    }

    public function listAction(): ResponseInterface
    {
        $contentElementUid = $this->getContentElementUid();
        if ($contentElementUid === 0) {
            return new HtmlResponse('<strong>Content Element UID could not be detected</strong>');
        }

        try {
            $apiConfiguration = $this->getApiConfiguration();
        } catch (InvalidApiConfigurationException $exception) {
            return new HtmlResponse('<strong>' . $exception->getMessage() . '</strong>');
        }

        if (!$this->jobService->updateStoredJobs($contentElementUid, $apiConfiguration)) {
            return new HtmlResponse(
                '<strong>Error while retrieving job data. Please check TYPO3 log file</strong>'
            );
        }

        $filter = new JobFilter(
            GeneralUtility::trimExplode(',', (string)($this->settings['channel'] ?? 'all')),
            GeneralUtility::trimExplode(',', (string)($this->settings['type'] ?? 'all')),
        );

        $jobs = $this->jobService->getStoredJobs($contentElementUid, $filter);

        $this->view->assignMultiple([
            'jobs' => $jobs,
            'timeModelFilter' => $this->getTimeModelFilter($jobs),
            'occupationalGroupFilter' => $this->getOccupationalGroupFilter($jobs),
            'data' => $this->request->getAttribute('currentContentObject')->data ?? [],
        ]);

        return $this->htmlResponse($this->view->render());
    }

    public function searchAction(
        string $search = '',
        string $timeModel = '',
        string $occupationalGroup = ''
    ): ResponseInterface {
        $contentElementUid = $this->getContentElementUid();
        if ($contentElementUid === 0) {
            return new HtmlResponse('<strong>Content Element UID could not be detected</strong>');
        }

        try {
            $apiConfiguration = $this->getApiConfiguration();
        } catch (InvalidApiConfigurationException $exception) {
            return new HtmlResponse('<strong>' . $exception->getMessage() . '</strong>');
        }

        if (!$this->jobService->updateStoredJobs($contentElementUid, $apiConfiguration)) {
            return new HtmlResponse(
                '<strong>Error while retrieving job data. Please check TYPO3 log file</strong>'
            );
        }

        $filter = new JobFilter(
            (string)($this->settings['channel'] ?? 'all'),
            (string)($this->settings['type'] ?? 'all'),
            $search ?? '',
            $timeModel ?? '',
            $occupationalGroup ?? ''
        );

        $jobs = $this->jobService->getStoredJobs($contentElementUid, $filter);

        $this->view->assignMultiple([
            'jobs' => $this->jobService->searchJobs(
                $this->jobService->filterJobs($jobs, $filter),
                $search
            ),
            'search' => $filter->getSearch(),
            'selectedTimeModel' => $filter->getTimeModel(),
            'timeModelFilter' => $this->getTimeModelFilter($jobs),
            'selectedOccupationalGroup' => $filter->getOccupationalGroup(),
            'occupationalGroupFilter' => $this->getOccupationalGroupFilter($jobs),
            'data' => $this->request->getAttribute('currentContentObject')->data ?? [],
        ]);

        return $this->htmlResponse($this->view->render());
    }

    protected function getTimeModelFilter(array $jobs): array
    {
        // array_filter will remove the empty <time_model/> (array(0)) entries
        $timeModels = array_unique(array_filter(array_column($jobs, 'time_model')));

        sort($timeModels);

        $timeModels = array_combine($timeModels, $timeModels);
        array_unshift($timeModels, 'all');

        return $timeModels;
    }

    protected function getOccupationalGroupFilter(array $jobs): array
    {
        // array_filter will remove the empty <dvvbw_occupational_group/> (array(0)) entries
        $occupationalGroups = array_unique(array_filter(array_column($jobs, 'dvvbw_occupational_group')));

        sort($occupationalGroups);

        $occupationalGroups = array_combine($occupationalGroups, $occupationalGroups);
        array_unshift($occupationalGroups, 'all');

        return $occupationalGroups;
    }

    protected function getContentElementUid(): int
    {
        return (int)$this->request->getAttribute('currentContentObject')->data['uid'] ?? 0;
    }

    protected function getApiConfiguration(): ApiConfiguration
    {
        if (isset($this->settings['endpoint'], $this->settings['username'], $this->settings['password'])) {
            return new ApiConfiguration(
                $this->settings['endpoint'],
                $this->settings['username'],
                $this->settings['password']
            );
        }

        throw new InvalidApiConfigurationException('API configuration in plugin is uncompleted');
    }
}
