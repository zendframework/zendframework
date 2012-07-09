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
class Zend_Service_DeveloperGarden_Response_VoiceButler_NewCallResponse
    extends Zend_Service_DeveloperGarden_Response_VoiceButler_AbstractVoiceButler
{
    /**
     * returns the session id
     * @return string
     */
    public function getSessionId()
    {
        if (isset($this->return->sessionId)) {
            return $this->return->sessionId;
        }
        return null;
    }

    /**
     * prints the session on casting to string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getSessionId();
    }
}
