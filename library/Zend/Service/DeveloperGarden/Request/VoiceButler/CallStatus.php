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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @uses       Zend_Service_DeveloperGarden_VoiceButler_AbstractVoiceButler
 * @category   Zend
 * @package    Zend_Service
 * @subpackage DeveloperGarden
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @author     Marco Kaiser
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
