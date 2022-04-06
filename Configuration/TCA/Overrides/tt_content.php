<?php

(static function() {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
        'Fab.mailing',
        'Message',
        'Mailing List'
    );
})();

$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['mailing_message'] = 'pi_flexform';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    'mailing_message',
    sprintf('FILE:EXT:mailing/Configuration/FlexForm/Mailing.xml')
);

$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['mailing_message'] = 'layout, select_key, pages, recursive';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['mailing_message'] = 'pi_flexform';

