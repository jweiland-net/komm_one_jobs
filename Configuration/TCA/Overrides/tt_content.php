<?php

if (!defined('TYPO3')) {
    die('Access denied.');
}

$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['kommonejobs_job'] = 'layout,select_key,pages,recursive';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['kommonejobs_job'] = 'pi_flexform';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    'kommonejobs_job',
    'FILE:EXT:komm_one_jobs/Configuration/Flexforms/Jobs.xml'
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'KommOneJobs',
    'Job',
    'Komm.ONE Jobs'
);
