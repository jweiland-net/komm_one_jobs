<?php

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

if (!defined('TYPO3')) {
    die('Access denied.');
}

$pluginSignature = ExtensionUtility::registerPlugin(
    'KommOneJobs',
    'Job',
    'LLL:EXT:komm_one_jobs/Resources/Private/Language/locallang_db.xlf:plugin.title',
    'komm-one-jobs-plugin',
);

ExtensionManagementUtility::addToAllTCAtypes(
    'tt_content',
    '--div--;Configuration,pi_flexform',
    $pluginSignature,
    'after:subheader',
);
ExtensionManagementUtility::addPiFlexFormValue(
    '*',
    'FILE:EXT:komm_one_jobs/Configuration/Flexforms/Jobs.xml',
    $pluginSignature,
);
