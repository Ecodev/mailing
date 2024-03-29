<?php
namespace Fab\Mailing\ViewHelpers\Message;

/*
 * This file is part of the Fab/Mailing project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\Messenger\Redirect\RedirectService;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * View helper to some honey pot field.
 */
class DevelopmentViewHelper extends AbstractViewHelper
{

    /**
     * @return string
     * @throws \InvalidArgumentException
     * @throws \Fab\Messenger\Exception\InvalidEmailFormatException
     */
    public function render()
    {
        $redirectTo = $this->getRedirectService()->getRedirections();
        $output = '';

        // Means we want to redirect email.
        if (is_array($redirectTo) && $redirectTo) {
            $settings = $this->templateVariableContainer->get('settings');

            $output = sprintf(
                "<pre style='clear: both'>%s CONTEXT<br /> %s %s</pre>",
                strtoupper((string)Environment::getContext()),
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
    protected function isSenderOk(array $settings) {
        return !empty($settings['emailFrom']) || !empty($GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromAddress']);
    }

    /**
     * @return object|RedirectService
     * @throws \InvalidArgumentException
     */
    protected function getRedirectService() {
        return GeneralUtility::makeInstance(RedirectService::class);
    }

}
