<?php
namespace Fab\Mailing\Backend;

/*
 * This file is part of the Fab/Mailing project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

/**
 * Class that adds the wizard icon.
 */
class Wizard
{

    /**
     * Processing the wizard items array
     *
     * @param array $wizardItems : The wizard items
     * @return array
     */
    function proc($wizardItems)
    {
        $wizardItems['plugins_tx_mailing_mail'] = array(
            'icon' => ExtensionManagementUtility::extRelPath('mailing') . 'Resources/Public/Images/Mailing.png',
            'title' => $this->getLanguageService()->sL('LLL:EXT:mailing/Resources/Private/Language/locallang.xlf:wizard.title'),
            'description' => $this->getLanguageService()->sL('LLL:EXT:mailing/Resources/Private/Language/locallang.xlf:wizard.description'),
            'params' => '&defVals[tt_content][CType]=list&defVals[tt_content][list_type]=mailing_mail'
        );

        return $wizardItems;
    }

    /**
     * @return \TYPO3\CMS\Lang\LanguageService
     */
    protected function getLanguageService()
    {
        return $GLOBALS['LANG'];
    }

}
