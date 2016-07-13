<?php
namespace Fab\Mailing\Controller;


/*
 * This file is part of the Fab/Mailing project under GPLv2 or later.
 *
 * For the full copyright and license information, please read the
 * LICENSE.md file that was distributed with this source code.
 */

use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * Class MailingController
 */
class MailingController extends ActionController
{

    /**
     * @return void
     */
    public function indexAction()
    {

    }

    /**
     * @return void
     */
    public function sendAction()
    {
        $this->redirect('feedback');
    }

    /**
     * @return void
     */
    public function feedbackAction()
    {

    }

}