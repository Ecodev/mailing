<?php
namespace Fab\Mailing\Backend;

/*
 * This file is part of the Fab/Mailing project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Backend\Wizard\NewContentElementWizardHookInterface;

/**
 * Class that adds the wizard icon.
 */
class Wizard implements NewContentElementWizardHookInterface
{

    /**
     * @param array $items
     * @param \TYPO3\CMS\Backend\Controller\ContentElement\NewContentElementController
     * @return void
     */
    public function manipulateWizardItems(&$wizardItems, &$parentObject)
    {
        $wizardItems['plugins_tx_mailing_message'] = array(
            'iconIdentifier' => 'tx-mailing-icon',
            'title' => $this->getLanguageService()->sL('LLL:EXT:mailing/Resources/Private/Language/locallang.xlf:wizard.title'),
            'description' => $this->getLanguageService()->sL('LLL:EXT:mailing/Resources/Private/Language/locallang.xlf:wizard.description'),
            'params' => '&defVals[tt_content][CType]=list&defVals[tt_content][list_type]=mailing_messages'
        );
    }

    /**
     * @return \TYPO3\CMS\Core\Localization\LanguageService
     */
    protected function getLanguageService()
    {
        return $GLOBALS['LANG'];
    }

}
