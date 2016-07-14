<?php
namespace Fab\Mailing\Domain\Validator;

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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator;

/**
 * Validate the honey pot.
 */
class NotEmptyValidator extends AbstractValidator
{

    /**
     * @param string $value
     */
    public function isValid($value)
    {
        if (empty($value)) {
            $this->addError('Empty field', 1468509656);
        }
    }

}
