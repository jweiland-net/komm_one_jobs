<?php

use JWeiland\KommOneJobs\Controller\JobController;
use Psr\Log\LogLevel;
use TYPO3\CMS\Core\Log\Writer\FileWriter;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

if (!defined('TYPO3')) {
    die('Access denied.');
}

call_user_func(function () {
    ExtensionUtility::configurePlugin(
        'KommOneJobs',
        'Job',
        [
            JobController::class => 'list, search',
        ],
        [
            JobController::class => 'search',
        ],
        ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT,
    );

    if (!isset($GLOBALS['TYPO3_CONF_VARS']['LOG']['JWeiland']['KommOneJobs']['writerConfiguration'])) {
        $GLOBALS['TYPO3_CONF_VARS']['LOG']['JWeiland']['KommOneJobs']['writerConfiguration'] = [
            LogLevel::INFO => [
                FileWriter::class => [
                    'logFileInfix' => 'komm_one_jobs',
                ],
            ],
        ];
    }
});
