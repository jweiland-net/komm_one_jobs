<?php

/*
 * This file is part of the package jweiland/komm-one-jobs.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\KommOneJobs\Task;

class Downloadxml extends \TYPO3\CMS\Scheduler\Task\AbstractTask
{
    public function execute()
    {
        // Code

        $objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Extbase\Object\ObjectManager');
        $controller = $objectManager->get('MAi\MaiJobs\Controller\JobController');
        $controller->getJobXml();

        return true; // or false
    }
}
