<?php

if (!defined('TYPO3_MODE')) {
    die ('Access denied.');
}

$configuration = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['mailing']);

// Possible Static TS loading
if (true === isset($configuration['autoload_typoscript']['value']) && true === (bool)$configuration['autoload_typoscript']['value']) {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile('mailing', 'Configuration/TypoScript', 'Mailing List');
}


if (TYPO3_MODE === 'BE') {

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
        'Fab.mailing',
        'Message',
        'Mailing List'
    );

    $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['mailing_message'] = 'pi_flexform';
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
        'mailing_message',
        sprintf('FILE:EXT:mailing/Configuration/FlexForm/Mailing.xml')
    );

    $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['mailing_message'] = 'layout, select_key, pages, recursive';
    $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['mailing_message'] = 'pi_flexform';

    $GLOBALS['TBE_MODULES_EXT']["xMOD_db_new_content_el"]['addElClasses']['Fab\Mailing\Backend\Wizard'] = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('mailing') . 'Classes/Backend/Wizard.php';
}