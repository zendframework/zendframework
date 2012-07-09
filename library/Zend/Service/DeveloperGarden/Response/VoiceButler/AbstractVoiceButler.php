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
abstract class Zend_Service_DeveloperGarden_Response_VoiceButler_AbstractVoiceButler
    extends Zend_Service_DeveloperGarden_Response_AbstractResponse
{
    /**
     * the return from the sms request
     *
     * @var stdClass
     */
    public $return = null;

    /**
     * returns the return object
     *
     * @return stdClass
     */
    public function getReturn()
    {
        return $this->return;
    }

    /**
     * parse the response data and throws exceptions
     *
     * @throws Zend_Service_DeveloperGarden_Response_Exception
     * @return Zend_Service_DeveloperGarden_Response_AbstractResponse
     */
    public function parse()
    {
        if ($this->hasError()) {
            throw new Zend_Service_DeveloperGarden_Response_Exception(
                $this->getErrorMessage(),
                $this->getErrorCode()
            );
        }

        return $this;
    }

    /**
     * returns the error code
     *
     * @return string|null
     */
    public function getErrorCode()
    {
        $retValue = null;
        if ($this->return instanceof stdClass) {
            $retValue = $this->return->status;
        }
        return $retValue;
    }

    /**
     * returns the error message
     *
     * @return string
     */
    public function getErrorMessage()
    {
        $retValue = null;
        if ($this->return instanceof stdClass) {
            $retValue = $this->return->err_msg;
        }
        return $retValue;
    }

    /**
     * returns true if the errorCode is not null and not 0000
     *
     * @return boolean
     */
    public function isValid()
    {
        return ($this->return === null
                || $this->return->status == '0000');
    }

    /**
     * returns true if we have a error situation
     *
     * @return boolean
     */
    public function hasError()
    {
        $retValue = false;
        if ($this->return instanceof stdClass
            && $this->return->status != '0000'
        ) {
            $retValue = true;
        }
        return $retValue;
    }
}
