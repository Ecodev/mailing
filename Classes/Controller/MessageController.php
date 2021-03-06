<?php
namespace Fab\Mailing\Controller;

/*
 * This file is part of the Fab/Mailing project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use Fab\Mailing\Service\RecipientService;
use Fab\Messenger\Domain\Model\Mailing;
use Fab\Messenger\Domain\Model\Message;
use Fab\Vidi\Domain\Model\Content;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * Class MessageController
 */
class MessageController extends ActionController
{

    /**
     * Initialize actions. These actions are meant to be called by an logged-in FE User.
     * @throws \RuntimeException
     */
    public function initializeAction()
    {
        // Perhaps it should go into a validator?
        // Check permission before executing any action.
        $allowedFrontendGroups = trim((string)$this->settings['allowedFrontendGroups']);
        if ($allowedFrontendGroups === '*') {
            if (!$this->getFrontendUser()->user) {
                throw new \RuntimeException('FE User must be logged-in.', 1471442296);
            }
        } elseif ($allowedFrontendGroups !== '') {

            $isAllowed = false;
            $frontendGroups = GeneralUtility::trimExplode(',', $allowedFrontendGroups, true);
            foreach ($frontendGroups as $frontendGroup) {
                if (GeneralUtility::inList($this->getFrontendUser()->user['usergroup'], $frontendGroup)) {
                    $isAllowed = true;
                    break;
                }
            }

            // Throw exception if not allowed
            if (!$isAllowed) {
                throw new \RuntimeException('FE User does not have enough permission.', 1471442297);
            }
        }

    }

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
        $this->view->assign('senderName', $this->getSenderName());
        $this->view->assign('senderEmail', $this->getSenderEmail());
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
     * @throws \Fab\Messenger\Exception\InvalidEmailFormatException
     * @throws \Fab\Messenger\Exception\RecordNotFoundException
     * @throws \Fab\Messenger\Exception\WrongPluginConfigurationException
     * @throws \RuntimeException
     */
    public function sendAction($subject, $body)
    {

        /** @var RecipientService $recipientService */
        $recipientService = GeneralUtility::makeInstance(RecipientService::class, $this->settings['selection']);
        $numberOfSentEmails = 0;

        $mailingName = 'Mailing #' . $GLOBALS['_SERVER']['REQUEST_TIME'];

        foreach ($recipientService->findRecipients() as $recipient) {

            if (filter_var($recipient['email'], FILTER_VALIDATE_EMAIL)) {
                $numberOfSentEmails++;

                /** @var Message $message */
                $message = $this->objectManager->get(Message::class);

                # Minimum required to be set
                $message->setBody($body)
                    ->setSubject($subject)
                    ->setSender($this->getSender())
                    ->setMailingName($mailingName)
                    ->setScheduleDistributionTime($GLOBALS['_SERVER']['REQUEST_TIME'])
                    ->parseToMarkdown((bool)$this->settings['parseToMarkdown'])
                    // ->assign('recipient', $recipient->toArray()) could be a security risk
                    ->setTo($this->getTo($recipient));

                if ($this->settings['layout']) {
                    $message->setMessageLayout($this->settings['layout']);
                }

                if ((bool)$this->settings['enqueue']) {
                    $message->enqueue();
                } else {
                    $message->send();
                }

            }
        }

        $this->redirect('feedback', null, null, ['numberOfSentEmails' => $numberOfSentEmails]);
    }

    /**
     * @param int $numberOfSentEmails
     * @throws \InvalidArgumentException
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotException
     * @throws \TYPO3\CMS\Extbase\SignalSlot\Exception\InvalidSlotReturnException
     */
    public function feedbackAction($numberOfSentEmails)
    {
        /** @var RecipientService $recipientService */
        $recipientService = GeneralUtility::makeInstance(RecipientService::class, $this->settings['selection']);
        $this->view->assign('numberOfSentEmails', (int)$numberOfSentEmails);
        $this->view->assign('numberOfRecipients', (int)$recipientService->countRecipients());
    }

    /**
     * @return array
     */
    protected function getSender()
    {
        $senderName = $this->getSenderName() ? $this->getSenderName() : $this->getSenderEmail();
        return [$this->getSenderEmail() => $senderName];
    }

    /**
     * @return string
     */
    protected function getSenderName()
    {
        $senderName = $this->getFrontendUserData()['first_name'] . ' ' . $this->getFrontendUserData()['last_name'];
        if (!$senderName) {
            $senderName = $this->settings['nameFrom'];
        }
        return $senderName;
    }

    /**
     * @return string
     */
    protected function getSenderEmail()
    {
        // Default
        $senderEmail = 'no_reply@' . gethostname();

        $userData = $this->getFrontendUserData();
        if ($userData['email']) {
            $senderEmail = $userData['email'];
        } elseif ($this->settings['emailFrom']) {
            $senderEmail = $this->settings['emailFrom'];
        }

        return $senderEmail;
    }

    /**
     * @param Content $recipient
     * @return array
     */
    protected function getTo(Content $recipient)
    {
        $email = $recipient['email'];

        $nameParts = [];
        if ($recipient['first_name']) {
            $nameParts[] = $recipient['first_name'];
        }

        if ($recipient['last_name']) {
            $nameParts[] = $recipient['last_name'];
        }

        if (count($nameParts) > 0) {
            $nameParts[] = $email;
        }

        $name = implode(' ', $nameParts);

        return [$email => $name];
    }

    /**
     * Returns an instance of the current Frontend User.
     *
     * @return \TYPO3\CMS\Frontend\Authentication\FrontendUserAuthentication
     */
    protected function getFrontendUser()
    {
        return $GLOBALS['TSFE']->fe_user;
    }

    /**
     * Returns user data of the current Frontend User.
     *
     * @return array
     */
    protected function getFrontendUserData()
    {
        return $this->getFrontendUser()->user ? $this->getFrontendUser()->user : [];
    }

}