<?php

use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;

if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

(static function (): void {

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'Fab.mailing',
        'Message',
        [
            'Message' => 'compose, send, feedback',

        ],
        // non-cacheable actions
        [
            'Message' => 'compose, send, feedback',
        ]
    );

    // Possible Static TS loading
    $isTypoScriptAutoLoad = (bool)GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('mailing', 'autoload_typoscript');
    if ($isTypoScriptAutoLoad) {
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript(
            'mailing',
            'constants',
            '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:mailing/Configuration/TypoScript/constants.txt">'
        );

        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript(
            'mailing',
            'setup',
            '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:mailing/Configuration/TypoScript/setup.txt">'
        );
    }

})();

