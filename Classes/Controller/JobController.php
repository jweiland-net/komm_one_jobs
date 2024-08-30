<?php

/*
 * This file is part of the package jweiland/komm-one-jobs.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\KommOneJobs\Controller;

use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Http\HtmlResponse;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

/**
 * JobController
 */
class JobController extends ActionController
{
    private RequestFactory $requestFactory;
    private string $xml_file = '';
    private string $url = '**REMOVED**';

    public function injectRequestFactory(RequestFactory $requestFactory): void
    {
        $this->requestFactory = $requestFactory;
    }

    public function getJobXml(bool $isSchedulerCall = true)
    {
        $this->xml_file = \TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName('typo3temp/jobs_as_xml.xml');

        $updateFile = false;
        if ($isSchedulerCall) {
            $updateFile = true;
        }

        if (!file_exists($this->xml_file)) {
            $updateFile = true;
            file_put_contents($this->xml_file, '');
        } else {
            // Update, if temp file is too old
            $age = time() - filemtime($this->xml_file);
            if ($age > 1000) {
                $updateFile = true;
            }
        }

        if ($updateFile) {
            $tmpDownloadFile = $this->xml_file . '.download';
            file_put_contents($tmpDownloadFile, '');

            try {
                $response = $this->requestFactory->request($this->url, 'GET', [
                    'allow_redirects' => false,
                    'timeout' => 10,
                ]);
                if ($response->getStatusCode() === 200) {
                    DebuggerUtility::var_dump('XML Url requests redirect. Maybe host or authorization problem');
                }
                if ($response->getStatusCode() === 200) {
                    file_put_contents($tmpDownloadFile, (string)$response->getBody());
                }
            } catch (GuzzleException $e) {
                DebuggerUtility::var_dump($e->getMessage(), 'Exception');
            }

            if (filesize($tmpDownloadFile) > 0) {
                if (file_exists($this->xml_file)) {
                    unlink($this->xml_file);
                }

                rename($tmpDownloadFile, $this->xml_file);
            }
        }
    }

    /**
     * action list
     *
     * @return void
     */
    public function listAction(): ResponseInterface
    {
        $this->getJobXml(false);

        if (filesize($this->xml_file) === 0) {
            return new HtmlResponse('There are no jobs.');
        }

        $xml = simplexml_load_file($this->xml_file, null, LIBXML_NOCDATA);

        $jobs = $this->SimpleXML2Array($xml, false);

        if (!isset($jobs['publication'])) {
            $jobs = null;
        }

        foreach ($jobs as $key => $value) {
            if (gettype($key) == 'string') {
                unset($jobs[$key]);
            }
        }

        // Ausgabe Homepage/Intranet filtern
        if ($this->settings['channel'] == 0) {
            foreach ($jobs as $job) {
                if ($job['publication_channel'] != 'Homepage') {
                    unset($jobs[array_search($job, $jobs)]);
                }
            }
        } else {
            if ($this->settings['channel'] == 1) {
                foreach ($jobs as $job) {
                    if ($job['publication_channel'] != 'Intranet') {
                        unset($jobs[array_search($job, $jobs)]);
                    }
                }
            } else {
                $jobs = null;
            }
        }

        // Ausgabe normale Stellen/Azubi filtern
        if ($this->settings['type'] == 0) {
            foreach ($jobs as $job) {
                if ($job['vacancy_type'] != 'dvvbw_professional') {
                    unset($jobs[array_search($job, $jobs)]);
                }
            }
        } else {
            if ($this->settings['type'] == 1) {
                foreach ($jobs as $job) {
                    if ($job['vacancy_type'] != 'dvvbw_apprentice') {
                        unset($jobs[array_search($job, $jobs)]);
                    }
                }
            } else {
                if ($this->settings['type'] == 2) {
                    foreach ($jobs as $job) {
                        if ($job['vacancy_type'] != 'dvvbw_holiday_job') {
                            unset($jobs[array_search($job, $jobs)]);
                        }
                    }
                } else {
                    $jobs = null;
                }
            }
        }

        $this->view->assign('jobs', $jobs);

        return $this->htmlResponse($this->view->render());
    }

    private function SimpleXML2Array($xml, $innerLoop = true): array
    {
        $array = (array)$xml;

        foreach ($array as $key => $value) {
            if (gettype($value) != 'string') {
                // check if multiple publications or single
                if (!is_array($value)) {
                    if (strpos(get_class($value), 'SimpleXML') !== false) {
                        if (!$innerLoop) {
                            // change array keys to numeric for root element
                            $array[array_search($key, array_keys($array))] = $this->SimpleXML2Array($value);
                        } else {
                            $array[$key] = $this->SimpleXML2Array($value);
                        }
                    }
                } else {
                    foreach ($value as $k => $v) {
                        if (strpos(get_class($v), 'SimpleXML') !== false) {
                            if (!$innerLoop) {
                                // change array keys to numeric for root element
                                $array[array_search($k, array_keys($value))] = $this->SimpleXML2Array($v);
                            } else {
                                $array[$k] = $this->SimpleXML2Array($v);
                            }
                        }
                    }
                }
            } else {
                if (!$innerLoop) {
                    // change array keys to numeric for root element
                    $array[array_search($key, array_keys($array))] = $value;
                } else {
                    if ($key == 'valid_to') {
                        $value = substr($value, 0, -5);
                    }
                    $array[$key] = $value;
                }
            }
        }

        return $array;
    }
}
