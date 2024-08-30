<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

call_user_func(function () {
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks'][\JWeiland\KommOneJobs\Task\Downloadxml::class] = [
        'extension' => 'KommOneJobs',
        'title' => 'Download Job-XML',
        'description' => 'Herunterladen der XML-Datei aus dem Jobportal',
    ];

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'JWeiland.KommOneJobs',
        'Job',
        [
            \JWeiland\KommOneJobs\Controller\JobController::class => 'list',
        ],
        // non-cacheable actions
        [
            \JWeiland\KommOneJobs\Controller\JobController::class => 'list',
        ]
    );

    // Add komm.ONE plugin to new element wizard
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
        '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:komm_one_jobs/Configuration/TSconfig/ContentElementWizard.tsconfig">'
    );
});
