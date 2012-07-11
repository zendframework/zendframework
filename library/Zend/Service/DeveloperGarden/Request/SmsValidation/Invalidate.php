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
class Zend_Service_DeveloperGarden_Request_SmsValidation_Invalidate
    extends Zend_Service_DeveloperGarden_Request_AbstractRequest
{
    /**
     * the number
     *
     * @var string
     */
    public $number = null;

    /**
     * create the class for validation a sms keyword
     *
     * @param integer $environment
     * @param string $keyword
     * @param string $number
     */
    public function __construct($environment, $number = null)
    {
        parent::__construct($environment);
        $this->setNumber($number);
    }

    /**
     * returns the number
     *
     * @return string $number
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * set a new number
     *
     * @param string $number
     * @return Zend_Service_DeveloperGarden_Request_SmsValidation_Validate
     */
    public function setNumber($number)
    {
        $this->number = $number;
        return $this;
    }
}
