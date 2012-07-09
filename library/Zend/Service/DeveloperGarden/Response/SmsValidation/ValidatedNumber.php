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
class Zend_Service_DeveloperGarden_Response_SmsValidation_ValidatedNumber
{
    /**
     * the number
     * @var string
     */
    public $number = null;

    /**
     * is valid until this date
     * @var string
     */
    public $validUntil = null;

    /**
     * returns the number
     *
     * @return number
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * returns the valid until date
     *
     * @return string
     */
    public function getValidUntil()
    {
        return $this->validUntil;
    }
}
