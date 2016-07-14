<?php
namespace Fab\Mailing\Backend;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use Fab\Mailing\Persistence\MatcherFactory;
use Fab\Vidi\Domain\Model\Selection;
use Fab\Vidi\Domain\Repository\ContentRepositoryFactory;
use Fab\Vidi\Domain\Repository\SelectionRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\BackendConfigurationManager;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Service\TypoScriptService;

/**
 * A class to interact with TCEForms.
 */
class TceForms
{

    /**
     * This method modifies the list of items for FlexForm "selection".
     *
     * @param array $parameters
     */
    public function getSelections(&$parameters)
    {
        $configuration = $this->getPluginConfiguration();

        if (0 === count($configuration) || empty($configuration['settings']['contentTypeSelection'])) {
            $parameters['items'][] = array('No template found. Forgotten to load the static TS template?', '', NULL);
        } else {

            $parameters['items'][] = array('', '', null);

            /** @var SelectionRepository $selectionRepository */
            $selectionRepository = $this->getObjectManager()->get(SelectionRepository::class);

            $contentType = $configuration['settings']['contentTypeSelection'];
            if ($contentType) {

                $selections = $selectionRepository->findForEveryone($contentType);

                if ($selections) {
                    foreach ($selections as $selection) {
                        /** @var Selection $selection */
                        $values = array($selection->getName(), $selection->getUid(), null);
                        $parameters['items'][] = $values;
                    }
                }
            }
        }
    }

    /**
     * Render the field "EmailFrom"
     *
     * @param array $parameters
     * @return string
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException
     * @throws \InvalidArgumentException
     */
    public function previewSelection(array $parameters)
    {
        if (empty($parameters['row']['uid'])) {
            $output = sprintf(
                '<strong>%s</strong>',
                $this->getLanguageService()->sL('LLL:EXT:mailing/Resources/Private/Language/locallang.xlf:message.notSavedYet')
            );
        } else {

            /** @var SelectionRepository $selectionRepository */
            $selectionRepository = $this->getObjectManager()->get(SelectionRepository::class);

            $flexform = $parameters['row']['pi_flexform'];
            $selectionIdentifier = (int)$this->getSettings($flexform, 'selection');

            /** @var Selection $selection */
            $selection = $selectionRepository->findByIdentifier($selectionIdentifier);

            $numberOfRecipients = 0;
            $recipients = [];
            if ($selection) {
                $matcher = MatcherFactory::getInstance()->getMatcher($selection);
                $contentRepository = ContentRepositoryFactory::getInstance($selection->getDataType());
                $recipients = $contentRepository->findBy($matcher);
                $numberOfRecipients = $contentRepository->countBy($matcher);
            }

            $output = [];
            $output[] = sprintf(
                '<div style="text-decoration: underline;">%s %s</div>',
                $numberOfRecipients,
                strtolower($this->getLanguageService()->sL('LLL:EXT:mailing/Resources/Private/Language/locallang.xlf:recipients'))
            );

            foreach ($recipients as $recipient) {
                if ($recipient['email'] && $numberOfRecipients < 500) {
                        $output[] = $recipient['email'] . ', ';
                } else {
                    $output[] = sprintf(
                        '<strong style="color: red">No email for %s:%s</strong>',
                        $recipient->getDataType(),
                        $recipient->getUid()
                    );
                }
            }
        }

        return implode("\n", $output);
    }

    /**
     * Render the field "NameFrom"
     *
     * @param array $parameters
     * @return string
     */
    public function renderNameFrom(array $parameters)
    {
        if (empty($parameters['row']['uid'])) {
            $output = sprintf(
                '<strong>%s</strong>',
                $this->getLanguageService()->sL('LLL:EXT:mailing/Resources/Private/Language/locallang.xlf:message.notSavedYet')
            );
        } else {

            $value = '';
            if (!empty($parameters['itemFormElValue'])) {
                $value = $parameters['itemFormElValue'];
            } elseif (!empty($GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromName'])) {
                $value = $GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromName'];
            }

            $output = sprintf(
                '<input type="text" name="%s" class="form-control t3js-clearable hasDefaultValue" value="%s" placeholder="%s"/>',
                $parameters['itemFormElName'],
                $value,
                $value === '' ? 'Consider giving a value for $GLOBALS[\'TYPO3_CONF_VARS\'][\'MAIL\'][\'defaultMailFromName\']' : ''
            );
        }

        return $output;
    }

    /**
     * Render the field "EmailFrom"
     *
     * @param array $parameters
     * @return string
     */
    public function renderEmailFrom(array $parameters)
    {
        if (empty($parameters['row']['uid'])) {
            $output = sprintf(
                '<strong>%s</strong>',
                $this->getLanguageService()->sL('LLL:EXT:mailing/Resources/Private/Language/locallang.xlf:message.notSavedYet')
            );
        } else {

            $value = '';
            if (!empty($parameters['itemFormElValue'])) {
                $value = $parameters['itemFormElValue'];
            } elseif (!empty($GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromAddress'])) {
                $value = $GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromAddress'];
            }

            $output = sprintf(
                '<input type="text" name="%s" class="form-control t3js-clearable hasDefaultValue" value="%s" placeholder="%s"/>',
                $parameters['itemFormElName'],
                $value,
                $value === '' ? 'Consider giving a value for $GLOBALS[\'TYPO3_CONF_VARS\'][\'MAIL\'][\'defaultMailFromAddress\']' : ''
            );
        }

        return $output;
    }

    /**
     * @param array $flexform
     * @param string $key
     * @return string
     */
    protected function getSettings(array $flexform = array(), $key)
    {

        $value = '';

        if (0 !== count($flexform)) {

            $normalizedFlexform = $this->normalizeFlexForm($flexform);
            if (!empty($normalizedFlexform['settings'][$key])) {
                $value = $normalizedFlexform['settings'][$key];
                if (is_array($value)) {
                    $value = $value[0];
                }
            }
        }
        return $value;
    }

    /**
     * Returns the TypoScript configuration for this extension.
     *
     * @return array
     */
    protected function getPluginConfiguration()
    {
        $setup = $this->getConfigurationManager()->getTypoScriptSetup();

        $pluginConfiguration = array();
        if (is_array($setup['plugin.']['tx_mailing.'])) {
            /** @var TypoScriptService $typoScriptService */
            $typoScriptService = GeneralUtility::makeInstance(TypoScriptService::class);
            $pluginConfiguration = $typoScriptService->convertTypoScriptArrayToPlainArray($setup['plugin.']['tx_mailing.']);
        }
        return $pluginConfiguration;
    }

    /**
     * @return BackendConfigurationManager
     */
    protected function getConfigurationManager()
    {
        return $this->getObjectManager()->get(BackendConfigurationManager::class);
    }

    /**
     * @return ObjectManager
     */
    protected function getObjectManager()
    {
        /** @var ObjectManager $objectManager */
        return GeneralUtility::makeInstance(ObjectManager::class);
    }

    /**
     * Parses the flexForm content and converts it to an array
     * The resulting array will be multi-dimensional, as a value "bla.blubb"
     * results in two levels, and a value "bla.blubb.bla" results in three levels.
     *
     * Note: multi-language flexForms are not supported yet
     *
     * @param array $flexForm flexForm xml string
     * @param string $languagePointer language pointer used in the flexForm
     * @param string $valuePointer value pointer used in the flexForm
     * @return array the processed array
     */
    protected function normalizeFlexForm(array $flexForm, $languagePointer = 'lDEF', $valuePointer = 'vDEF')
    {
        $settings = array();
        $flexForm = isset($flexForm['data']) ? $flexForm['data'] : array();
        foreach (array_values($flexForm) as $languages) {
            if (!is_array($languages[$languagePointer])) {
                continue;
            }
            foreach ($languages[$languagePointer] as $valueKey => $valueDefinition) {
                if (strpos($valueKey, '.') === false) {
                    $settings[$valueKey] = $this->walkFlexFormNode($valueDefinition, $valuePointer);
                } else {
                    $valueKeyParts = explode('.', $valueKey);
                    $currentNode = &$settings;
                    foreach ($valueKeyParts as $valueKeyPart) {
                        $currentNode = &$currentNode[$valueKeyPart];
                    }
                    if (is_array($valueDefinition)) {
                        if (array_key_exists($valuePointer, $valueDefinition)) {
                            $currentNode = $valueDefinition[$valuePointer];
                        } else {
                            $currentNode = $this->walkFlexFormNode($valueDefinition, $valuePointer);
                        }
                    } else {
                        $currentNode = $valueDefinition;
                    }
                }
            }
        }
        return $settings;
    }

    /**
     * Parses a flexForm node recursively and takes care of sections etc
     *
     * @param array $nodeArray The flexForm node to parse
     * @param string $valuePointer The valuePointer to use for value retrieval
     * @return array
     */
    protected function walkFlexFormNode($nodeArray, $valuePointer = 'vDEF')
    {
        if (is_array($nodeArray)) {
            $return = array();
            foreach ($nodeArray as $nodeKey => $nodeValue) {
                if ($nodeKey === $valuePointer) {
                    return $nodeValue;
                }
                if (in_array($nodeKey, array('el', '_arrayContainer'))) {
                    return $this->walkFlexFormNode($nodeValue, $valuePointer);
                }
                if ($nodeKey[0] === '_') {
                    continue;
                }
                if (strpos($nodeKey, '.')) {
                    $nodeKeyParts = explode('.', $nodeKey);
                    $currentNode = &$return;
                    $nodeKeyPartsCount = count($nodeKeyParts);
                    for ($i = 0; $i < $nodeKeyPartsCount - 1; $i++) {
                        $currentNode = &$currentNode[$nodeKeyParts[$i]];
                    }
                    $newNode = array(next($nodeKeyParts) => $nodeValue);
                    $currentNode = $this->walkFlexFormNode($newNode, $valuePointer);
                } elseif (is_array($nodeValue)) {
                    if (array_key_exists($valuePointer, $nodeValue)) {
                        $return[$nodeKey] = $nodeValue[$valuePointer];
                    } else {
                        $return[$nodeKey] = $this->walkFlexFormNode($nodeValue, $valuePointer);
                    }
                } else {
                    $return[$nodeKey] = $nodeValue;
                }
            }
            return $return;
        }
        return $nodeArray;
    }

    /**
     * @return \TYPO3\CMS\Lang\LanguageService
     */
    protected function getLanguageService()
    {
        return $GLOBALS['LANG'];
    }
}