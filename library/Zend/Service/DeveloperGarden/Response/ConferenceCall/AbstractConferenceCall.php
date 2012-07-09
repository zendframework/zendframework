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
abstract class Zend_Service_DeveloperGarden_Response_ConferenceCall_AbstractConferenceCall
    extends Zend_Service_DeveloperGarden_Response_BaseType
{
    /**
     * returns the response object or null
     *
     * @return mixed
     */
    public function getResponse()
    {
        $r = new ReflectionClass($this);
        foreach ($r->getProperties() as $p) {
            $name = $p->getName();
            if (strpos($name, 'Response') !== false) {
                return $p->getValue($this);
            }
        }
        return null;
    }

    /**
     * parse the response data and throws exceptions
     *
     * @throws Zend_Service_DeveloperGarden_Response_Exception
     * @return mixed
     */
    public function parse()
    {
        $retVal = $this->getResponse();
        if ($retVal === null) {
            $this->statusCode    = 9999;
            $this->statusMessage = 'Internal response property not found.';
        } else {
            $this->statusCode    = $retVal->getStatusCode();
            $this->statusMessage = $retVal->getStatusMessage();
        }
        parent::parse();
        return $retVal;
    }
}
