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
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
     * @validate $subject \Fab\Mailing\Domain\Validator\HoneyPotValidator
     */
    public function sendAction($subject, $body)
    {

        /** @var RecipientService $recipientService */
        $recipientService = GeneralUtility::makeInstance(RecipientService::class, $this->settings['selection']);
        foreach ($recipientService->findRecipients() as $recipient) {

//            $templateIdentifier = 1; // uid
//            $layoutIdentifier = 1; // uid
//            $recipients = array('john@doe.com' => 'John Doe');
//            $markers = array(
//                'first_name' => 'John',
//                'last_name' => 'Doe',
//            );
//            $languageIdentifier = 0; // sys_language_uid
//            $pathToFile = 'some-path-to-file'; // @todo replace me with FAL identifier
//
//            /** @var Message $message */
//            $message = $this->objectManager->get(Message::class);
//
//            # Minimum required to be set
//            $message->setMessageTemplate($templateIdentifier)
//                ->setTo($recipient->getEmail);

//            # Additional setter
//            $message->assign('recipient', $recipient->toArray())
//            ->setLanguage($languageIdentifier)
//            ->addAttachment($pathToFile)
//            ->setMessageLayout($layoutIdentifier);
//
//            # Possible debug before sending.
//            # var_dump($message->toArray());
//
//            # Send the email...
//            $isSent = $message->send();
        }


//
//        var_dump($subject);
//        var_dump($body);
//        var_dump($this->settings);
//        exit();
        $this->redirect('feedback');
    }

    /**
     * @return void
     */
    public function feedbackAction()
    {

    }

}