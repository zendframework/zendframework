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
class Zend_Service_DeveloperGarden_Response_BaseType
    extends Zend_Service_DeveloperGarden_Response_AbstractResponse
{
    /**
     * the status code
     *
     * @var string
     */
    public $statusCode = null;

    /**
     * the status message
     *
     * @var string
     */
    public $statusMessage = null;

    /**
     * parse the result
     *
     * @throws Zend_Service_DeveloperGarden_Response_Exception
     * @return Zend_Service_DeveloperGarden_Response_AbstractResponse
     */
    public function parse()
    {
        if ($this->hasError()) {
            throw new Zend_Service_DeveloperGarden_Response_Exception(
                $this->getStatusMessage(),
                $this->getStatusCode()
            );
        }

        return $this;
    }

    /**
     * returns the error code
     *
     * @return string|null
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * returns the error message
     *
     * @return string
     */
    public function getStatusMessage()
    {
        return $this->statusMessage;
    }

    /**
     * returns true if the errorCode is not null and not 0000
     *
     * @return boolean
     */
    public function isValid()
    {
        return ($this->statusCode === null
             || $this->statusCode == '0000');
    }

    /**
     * returns true if we have a error situation
     *
     * @return boolean
     */
    public function hasError()
    {
        return ($this->statusCode !== null
             && $this->statusCode != '0000');
    }

    /**
     * returns the error code (statusCode)
     *
     * @return string|null
     */
    public function getErrorCode()
    {
        if (empty($this->errorCode)) {
            return $this->statusCode;
        } else {
            return $this->errorCode;
        }
    }

    /**
     * returns the error message
     *
     * @return string
     */
    public function getErrorMessage()
    {
        if (empty($this->errorMessage)) {
            return $this->statusMessage;
        } else {
            return $this->errorMessage;
        }
    }
}
