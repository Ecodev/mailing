<?php

if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'Ecodev.mailing',
    'Mail',
    'Mailing List'
);

$configuration = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['mailing']);

// Possible Static TS loading
if (true === isset($configuration['autoload_typoscript']['value']) && true === (bool)$configuration['autoload_typoscript']['value']) {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile('vidi_frontend', 'Configuration/TypoScript', 'Vidi Frontend: generic List Component');
}

