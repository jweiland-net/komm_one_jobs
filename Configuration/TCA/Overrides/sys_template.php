<?php

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

if (!defined('TYPO3')) {
    die('Access denied.');
}

ExtensionManagementUtility::addStaticFile(
    'komm_one_jobs',
    'Configuration/TypoScript',
    'Komm.ONE Jobs'
);
