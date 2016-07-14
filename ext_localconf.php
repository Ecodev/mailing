<?php
if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}


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


$configuration = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['mailing']);

if (false === isset($configuration['autoload_typoscript']) || true === (bool)$configuration['autoload_typoscript']) {

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
