<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

call_user_func(function () {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'JWeiland.KommOneJobs',
        'Job',
        [
            \JWeiland\KommOneJobs\Controller\JobController::class => 'list',
        ]
    );

    if (!isset($GLOBALS['TYPO3_CONF_VARS']['LOG']['JWeiland']['KommOneJobs']['writerConfiguration'])) {
        $GLOBALS['TYPO3_CONF_VARS']['LOG']['JWeiland']['KommOneJobs']['writerConfiguration'] = [
            \Psr\Log\LogLevel::INFO => [
                \TYPO3\CMS\Core\Log\Writer\FileWriter::class => [
                    'logFileInfix' => 'komm_one_jobs',
                ],
            ],
        ];
    }

    // Add komm.ONE plugin to new element wizard
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
        '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:komm_one_jobs/Configuration/TSconfig/ContentElementWizard.tsconfig">'
    );
});
