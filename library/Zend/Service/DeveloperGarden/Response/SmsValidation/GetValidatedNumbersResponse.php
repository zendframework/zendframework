<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
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
