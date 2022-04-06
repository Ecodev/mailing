<?php

use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;

if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

// Possible Static TS loading
$isTypoScriptAutoLoad = (bool)GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('mailing', 'autoload_typoscript');
if ($isTypoScriptAutoLoad) {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile('mailing', 'Configuration/TypoScript', 'Mailing List');
}

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms']['db_new_content_el']['wizardItemsHook'][\Fab\Mailing\Backend\Wizard::class] = \Fab\Mailing\Backend\Wizard::class;
