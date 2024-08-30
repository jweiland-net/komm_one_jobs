<?php

/*
 * This file is part of the package jweiland/komm-one-jobs.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

namespace JWeiland\KommOneJobs\Hook;

use TYPO3\CMS\Backend\View\PageLayoutView;
use TYPO3\CMS\Backend\View\PageLayoutViewDrawItemHookInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class CmsLayout implements PageLayoutViewDrawItemHookInterface
{
    /**
     * Rendering for custom content elements
     *
     * @param PageLayoutView $parentObject
     * @param bool $drawItem
     * @param string $headerContent
     * @param string $itemContent
     * @param array $row
     */
    public function preProcess(PageLayoutView &$parentObject, &$drawItem, &$headerContent, &$itemContent, array &$row)
    {
        if ($row['CType'] !== 'meinCType') {
            return;
        }

        $drawItem = false;
        $headerContent = '<strong>' . htmlspecialchars($row['header']) . '</strong><br />';

        // Sammelt die Flexform-Einstellungen und entfernt bestimmte Array-Keys ("data", "sDEF", "lDEF", "vDEF") zur besseren Nutzung in Fluid
        $flexform = $this->cleanUpArray(GeneralUtility::xml2array($row['pi_flexform']), ['data', 'sDEF', 'lDEF', 'vDEF']
        );

        // Festlegen der Template-Datei
        /** @var \TYPO3\CMS\Fluid\View\StandaloneView $fluidTemplate */
        $fluidTmplFilePath = GeneralUtility::getFileAbsFileName(
            'typo3conf/ext/meineExtension/Resources/Private/Templates/MeinBackendTemplate.html'
        );
        $fluidTmpl = GeneralUtility::makeInstance('TYPO3\CMS\Fluid\View\StandaloneView');
        $fluidTmpl->setTemplatePathAndFilename($fluidTmplFilePath);
        $fluidTmpl->assign('flex', $flexform);

        // Rendern
        $itemContent = $parentObject->linkEditContent($fluidTmpl->render(), $row);
    }

    /**
     * @param array $cleanUpArray
     * @param array $notAllowed
     * @return array|mixed
     */
    public function cleanUpArray(array $cleanUpArray, array $notAllowed)
    {
        $cleanArray = [];
        foreach ($cleanUpArray as $key => $value) {
            if (in_array($key, $notAllowed)) {
                return is_array($value) ? $this->cleanUpArray($value, $notAllowed) : $value;
            } else {
                if (is_array($value)) {
                    $cleanArray[$key] = $this->cleanUpArray($value, $notAllowed);
                }
            }
        }
        return $cleanArray;
    }
}
