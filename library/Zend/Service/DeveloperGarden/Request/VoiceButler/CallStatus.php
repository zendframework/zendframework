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
class Zend_Service_DeveloperGarden_Request_VoiceButler_CallStatus
    extends Zend_Service_DeveloperGarden_Request_VoiceButler_AbstractVoiceButler
{
    /**
     * extend the keep alive for this call
     *
     * @var integer
     */
    public $keepAlive = null;

    /**
     * constructor give them the environment and the sessionId
     *
     * @param integer $environment
     * @param string $sessionId
     * @param integer $keepAlive
     * @return Zend_Service_DeveloperGarden_Request_AbstractRequest
     */
    public function __construct($environment, $sessionId, $keepAlive = null)
    {
        parent::__construct($environment);
        $this->setSessionId($sessionId)
             ->setKeepAlive($keepAlive);
    }

    /**
     * @return string
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }

    /**
     * sets new sessionId
     *
     * @param string $sessionId
     * @return Zend_Service_DeveloperGarden_Request_VoiceButler_CallStatus
     */
    public function setSessionId($sessionId)
    {
        $this->sessionId = $sessionId;
        return $this;
    }

    /**
     * @return integer
     */
    public function getKeepAlive()
    {
        return $this->keepAlive;
    }

    /**
     * sets new keepAlive flag
     *
     * @param integer $keepAlive
     * @return Zend_Service_DeveloperGarden_Request_VoiceButler_CallStatus
     */
    public function setKeepAlive($keepAlive)
    {
        $this->keepAlive = $keepAlive;
        return $this;
    }
}
