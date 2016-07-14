<?php
namespace Fab\Mailing\Controller;

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
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * Class MessageController
 */
class MessageController extends ActionController
{

    /**
     * @return void
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException
     * @throws \InvalidArgumentException
     */
    public function composeAction()
    {
        /** @var SelectionRepository $selectionRepository */
        $selectionRepository = $this->objectManager->get(SelectionRepository::class);

        /** @var Selection $selection */
        $selection = $selectionRepository->findByIdentifier($this->settings['selection']);

        $numberOfRecipients = 0;
        $recipients = [];
        if ($selection) {
            $matcher = MatcherFactory::getInstance()->getMatcher($selection);
            $contentRepository = ContentRepositoryFactory::getInstance($selection->getDataType());
            $recipients = $contentRepository->findBy($matcher);
            $numberOfRecipients = $contentRepository->countBy($matcher);
        }

        $this->view->assign('settings', $this->settings);
        $this->view->assign('recipients', $recipients);
        $this->view->assign('numberOfRecipients', $numberOfRecipients);
    }

    /**
     * @param string $subject
     * @param string $body
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
     * @validate $subject \Fab\Formule\Domain\Validator\HoneyPotValidator
     */
    public function sendAction($subject, $body)
    {
        var_dump($subject);

        var_dump($this->settings);
        exit();
        $this->redirect('feedback');
    }

    /**
     * @return void
     */
    public function feedbackAction()
    {

    }

}