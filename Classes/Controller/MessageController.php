<?php
namespace Fab\Mailing\Controller;

/*
 * This file is part of the Fab/Mailing project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\Mailing\Service\RecipientService;
use Fab\Messenger\Domain\Model\Message;
use TYPO3\CMS\Core\Utility\GeneralUtility;
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
        /** @var RecipientService $recipientService */
        $recipientService = GeneralUtility::makeInstance(RecipientService::class, $this->settings['selection']);
        $this->view->assign('settings', $this->settings);
        $this->view->assign('recipients', $recipientService->findRecipients());
        $this->view->assign('numberOfRecipients', $recipientService->countRecipients());
    }

    /**
     * @param string $subject
     * @param string $body
     * @validate $subject \Fab\Mailing\Domain\Validator\HoneyPotValidator
     * @validate $subject \Fab\Mailing\Domain\Validator\NotEmptyValidator
     * @validate $body \Fab\Mailing\Domain\Validator\NotEmptyValidator
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
     * @throws \InvalidArgumentException
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     */
    public function sendAction($subject, $body)
    {

        /** @var RecipientService $recipientService */
        $recipientService = GeneralUtility::makeInstance(RecipientService::class, $this->settings['selection']);
        $numberOfSentEmails = 0;
        foreach ($recipientService->findRecipients() as $recipient) {


            if ($recipient['email']) {
                $numberOfSentEmails++;


                /** @var Message $message */
                $message = $this->objectManager->get(Message::class);
//
//                # Minimum required to be set
//                $message->setMessageTemplate($templateIdentifier)
//                    ->setTo($recipient->getEmail);
//
//                # Additional setter
//                $message
//                    // ->assign('recipient', $recipient->toArray()) could be a security risk => expose
//                    ->setMessageLayout($layoutIdentifier);
            }

        }

        $this->redirect('feedback', null, null, ['numberOfSentEmails' => $numberOfSentEmails]);
    }

    /**
     * @param int $numberOfSentEmails
     */
    public function feedbackAction($numberOfSentEmails)
    {
        /** @var RecipientService $recipientService */
        $recipientService = GeneralUtility::makeInstance(RecipientService::class, $this->settings['selection']);
        $this->view->assign('numberOfSentEmails', (int)$numberOfSentEmails);
        $this->view->assign('numberOfRecipients', (int)$recipientService->countRecipients());
    }

}