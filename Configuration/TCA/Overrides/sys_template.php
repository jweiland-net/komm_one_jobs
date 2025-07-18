<?php

if (!defined('TYPO3')) {
    die('Access denied.');
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'komm_one_jobs',
    'Configuration/TypoScript',
    'Komm.ONE Jobs'
);
