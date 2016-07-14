<?php
namespace Fab\Mailing\ViewHelpers\Message;

/*
 * This file is part of the Fab/Mailing project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\Messenger\Redirect\RedirectService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * View helper to some honey pot field.
 */
class DevelopmentViewHelper extends AbstractViewHelper
{

    /**
     * @throws \TYPO3\CMS\Fluid\Core\ViewHelper\Exception\InvalidVariableException
     * @return string
     * @throws \InvalidArgumentException
     * @throws \Fab\Messenger\Exception\InvalidEmailFormatException
     */
    public function render()
    {
        $redirectTo = $this->getRedirectService()->redirectionForCurrentContext();
        $output = '';

        // Means we want to redirect email.
        if (!empty($redirectTo)) {
            $settings = $this->templateVariableContainer->get('settings');

            $output = sprintf(
                "<pre style='clear: both'>%s CONTEXT<br /> %s %s</pre>",
                strtoupper((string)GeneralUtility::getApplicationContext()),
                '<br />- All emails will be redirected to ' . implode(', ', array_keys($redirectTo)) . '.',
                $this->isSenderOk($settings) ? '' : '<br/>- ATTENTION! No sender could be found. This will be a problem when sending emails.'
            );
        }

        return $output;
    }

    /**
     * @param array $settings
     * @return bool
     */
    public function isSenderOk(array $settings) {
        $isOk = true;
        if ($this->hasEmails($settings)) {
            $isOk = !empty($settings['emailFrom']) || !empty($GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromAddress']);
        }
        return $isOk;
    }

    /**
     * @return RedirectService
     * @throws \InvalidArgumentException
     */
    public function getRedirectService() {
        return GeneralUtility::makeInstance(RedirectService::class);
    }

}
