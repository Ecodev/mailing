<?php
namespace Fab\Mailing\Service;

/*
 * This file is part of the Fab/Mailing project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\Mailing\Persistence\MatcherFactory;
use Fab\Vidi\Domain\Model\Selection;
use Fab\Vidi\Domain\Repository\ContentRepositoryFactory;
use Fab\Vidi\Domain\Repository\SelectionRepository;
use Fab\Vidi\Persistence\Matcher;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * Class RecipientService
 */
class RecipientService
{
    /**
     * @var int
     */
    protected $selectionIdentifier;

    /**
     * @var Matcher
     */
    protected $matcher;


    /**
     * @param int $selectionIdentifier
     */
    public function __construct($selectionIdentifier)
    {
        $this->selectionIdentifier = (int)$selectionIdentifier;
    }

    /**
     * @return array
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException
     * @throws \InvalidArgumentException
     */
    public function findRecipients()
    {
        /** @var SelectionRepository $selectionRepository */
        $selectionRepository = $this->getObjectManager()->get(SelectionRepository::class);

        /** @var Selection $selection */
        $selection = $selectionRepository->findByIdentifier($this->selectionIdentifier);

        $recipients = [];
        if ($selection) {
            $matcher = $this->getMatcher($selection);
            $contentRepository = ContentRepositoryFactory::getInstance($selection->getDataType());
            $recipients = $contentRepository->findBy($matcher);
        }

        return $recipients;

    }

    /**
     * @return int
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException
     * @throws \InvalidArgumentException
     */
    public function countRecipients()
    {

        /** @var SelectionRepository $selectionRepository */
        $selectionRepository = $this->getObjectManager()->get(SelectionRepository::class);

        /** @var Selection $selection */
        $selection = $selectionRepository->findByIdentifier($this->selectionIdentifier);

        $numberOfRecipients = 0;
        if ($selection) {
            $matcher = $this->getMatcher($selection);
            $contentRepository = ContentRepositoryFactory::getInstance($selection->getDataType());
            $numberOfRecipients = $contentRepository->countBy($matcher);
        }
        return $numberOfRecipients;
    }

    /**
     * @return ObjectManager
     * @throws \InvalidArgumentException
     */
    protected function getObjectManager()
    {
        return GeneralUtility::makeInstance(ObjectManager::class);
    }

    /**
     * @param Selection $selection
     * @return Matcher
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException
     * @throws \InvalidArgumentException
     */
    public function getMatcher(Selection $selection)
    {
        if (!$this->matcher) {
            $this->matcher = MatcherFactory::getInstance()->getMatcher($selection);
        }
        return $this->matcher;

    }

}