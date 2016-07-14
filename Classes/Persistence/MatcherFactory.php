<?php
namespace Fab\Mailing\Persistence;

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

use Fab\Vidi\Domain\Model\Selection;
use Fab\Vidi\Resolver\FieldPathResolver;
use Fab\VidiFrontend\Tca\FrontendTca;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\MathUtility;
use Fab\Vidi\Persistence\Matcher;
use Fab\Vidi\Tca\Tca;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\SignalSlot\Dispatcher;

/**
 * Factory class related to Matcher object.
 */
class MatcherFactory implements SingletonInterface
{

    /**
     * @var array
     */
    protected $settings = array();

    /**
     * Gets a singleton instance of this class.
     *
     * @return MatcherFactory
     * @throws \InvalidArgumentException
     */
    static public function getInstance()
    {
        return GeneralUtility::makeInstance(MatcherFactory::class);
    }

    /**
     * Returns a matcher object.
     *
     * @param Selection $selection
     * @return Matcher
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException
     * @throws \InvalidArgumentException
     */
    public function getMatcher(Selection $selection)
    {

        /** @var $matcher Matcher */
        $matcher = GeneralUtility::makeInstance(Matcher::class, [], $selection->getDataType());

        /** @var Selection $selection */
        $queryParts = json_decode($selection->getQuery(), true);
        $matcher = $this->parseQuery($queryParts, $matcher, $selection->getDataType());

        // Trigger signal for post processing Matcher Object.
        $this->emitPostProcessMatcherObjectSignal($matcher);

        return $matcher;
    }

    /**
     * Apply criteria specific to jQuery plugin DataTable.
     *
     * @param array $queryParts
     * @param Matcher $matcher
     * @param string $dataType
     * @return Matcher $matcher
     */
    protected function parseQuery(array $queryParts, Matcher $matcher, $dataType)
    {

        foreach ($queryParts as $queryPart) {
            $fieldNameAndPath = key($queryPart);

            $resolvedDataType = $this->getFieldPathResolver()->getDataType($fieldNameAndPath, $dataType);
            $fieldName = $this->getFieldPathResolver()->stripFieldPath($fieldNameAndPath, $dataType);

            // Retrieve the value.
            $value = current($queryPart);

            if (FrontendTca::grid($resolvedDataType)->hasFacet($fieldName) && FrontendTca::grid($resolvedDataType)->facet($fieldName)->canModifyMatcher()) {
                $matcher = FrontendTca::grid($resolvedDataType)->facet($fieldName)->modifyMatcher($matcher, $value);
            } elseif (Tca::table($resolvedDataType)->hasField($fieldName)) {
                // Check whether the field exists and set it as "equal" or "like".
                if ($this->isOperatorEquals($fieldNameAndPath, $dataType, $value)) {
                    $matcher->equals($fieldNameAndPath, $value);
                } else {
                    $matcher->like($fieldNameAndPath, $value);
                }
            } elseif ($fieldNameAndPath === 'text') {
                // Special case if field is "text" which is a pseudo field in this case.
                // Set the search term which means Vidi will
                // search in various fields with operator "like". The fields come from key "searchFields" in the TCA.
                $matcher->setSearchTerm($value);
            }
        }

        return $matcher;
    }

    /**
     * Tell whether the operator should be equals instead of like for a search, e.g. if the value is numerical.
     *
     * @param string $fieldName
     * @param string $dataType
     * @param string $value
     * @return bool
     * @throws \Exception
     */
    protected function isOperatorEquals($fieldName, $dataType, $value)
    {
        return (Tca::table($dataType)->field($fieldName)->hasRelation() && MathUtility::canBeInterpretedAsInteger($value))
        || Tca::table($dataType)->field($fieldName)->isNumerical();
    }

    /**
     * Signal that is called for post-processing a matcher object.
     *
     * @param Matcher $matcher
     * @signal
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException
     */
    protected function emitPostProcessMatcherObjectSignal(Matcher $matcher)
    {
        $this->getSignalSlotDispatcher()->dispatch(MatcherFactory::class, 'postProcessMatcherObject', array($matcher, $matcher->getDataType()));
    }

    /**
     * Get the SignalSlot dispatcher
     *
     * @return Dispatcher
     */
    protected function getSignalSlotDispatcher()
    {
        return $this->getObjectManager()->get(Dispatcher::class);
    }

    /**
     * @return ObjectManager
     */
    protected function getObjectManager()
    {
        return GeneralUtility::makeInstance(ObjectManager::class);
    }

    /**
     * @return FieldPathResolver
     * @throws \InvalidArgumentException
     */
    protected function getFieldPathResolver()
    {
        return GeneralUtility::makeInstance(FieldPathResolver::class);
    }

}
