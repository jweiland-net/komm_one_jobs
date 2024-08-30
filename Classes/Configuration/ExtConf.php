<?php

/*
 * This file is part of the package jweiland/komm-one-jobs.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\KommOneJobs\Configuration;

use Psr\Log\LoggerInterface;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;

class ExtConf
{
    private bool $activateXmlTestFile = false;

    public function __construct(ExtensionConfiguration $extensionConfiguration, LoggerInterface $logger)
    {
        $extConf = [];

        try {
            $extConf = (array)$extensionConfiguration->get('komm_one_jobs');
        } catch (ExtensionConfigurationExtensionNotConfiguredException $exception) {
            $logger->error('No extension settings could be found for extension: komm_one_jobs', [
                'exception' => $exception,
            ]);
        } catch (ExtensionConfigurationPathDoesNotExistException $exception) {
            $logger->error('No extension settings could be found in TYPO3_CONF_VARS for extension: komm_one_jobs', [
                'exception' => $exception,
            ]);
            return;
        }

        if ($extConf === []) {
            return;
        }

        // call setter method foreach configuration entry
        foreach ($extConf as $key => $value) {
            $methodName = 'set' . ucfirst($key);
            if (method_exists($this, $methodName)) {
                $this->$methodName($value);
            }
        }
    }

    public function isActivateXmlTestFile(): bool
    {
        return $this->activateXmlTestFile;
    }

    public function setActivateXmlTestFile(string $activateXmlTestFile): void
    {
        $this->activateXmlTestFile = (bool)$activateXmlTestFile;
    }
}
