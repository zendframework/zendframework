<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage DeveloperGarden
 */

/**
 * @category   Zend
 * @package    Zend_Service
 * @subpackage DeveloperGarden
 * @author     Marco Kaiser
 */
class Zend_Service_DeveloperGarden_Response_SmsValidation_GetValidatedNumbersResponse
    extends Zend_Service_DeveloperGarden_Response_BaseType
{
    /**
     * array of validated numbers
     *
     * @var array of Zend_Service_DeveloperGarden_Response_SmsValidation_ValidatedNumber
     */
    public $validatedNumbers = null;

    /**
     * returns the internal array of validated numbers
     *
     * @return array of Zend_Service_DeveloperGarden_Response_SmsValidation_ValidatedNumber
     */
    public function getNumbers()
    {
        return (array) $this->validatedNumbers;
    }
}
