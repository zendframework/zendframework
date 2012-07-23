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
class Zend_Service_DeveloperGarden_Response_VoiceButler_CallStatusResponse
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
     * returns the connection time for participant a
     *
     * @return integer
     */
    public function getConnectionTimeA()
    {
        if (isset($this->return->connectiontimea)) {
            return $this->return->connectiontimea;
        }
        return null;
    }

    /**
     * returns the connection time for participant b
     *
     * @return integer
     */
    public function getConnectionTimeB()
    {
        if (isset($this->return->connectiontimeb)) {
            return $this->return->connectiontimeb;
        }
        return null;
    }

    /**
     * returns the description time for participant a
     *
     * @return string
     */
    public function getDescriptionA()
    {
        if (isset($this->return->descriptiona)) {
            return $this->return->descriptiona;
        }
        return null;
    }

    /**
     * returns the description time for participant b
     *
     * @return string
     */
    public function getDescriptionB()
    {
        if (isset($this->return->descriptionb)) {
            return $this->return->descriptionb;
        }
        return null;
    }

    /**
     * returns the reason time for participant a
     *
     * @return integer
     */
    public function getReasonA()
    {
        if (isset($this->return->reasona)) {
            return $this->return->reasona;
        }
        return null;
    }

    /**
     * returns the reason time for participant b
     *
     * @return integer
     */
    public function getReasonB()
    {
        if (isset($this->return->reasonb)) {
            return $this->return->reasonb;
        }
        return null;
    }

    /**
     * returns the state time for participant a
     *
     * @return string
     */
    public function getStateA()
    {
        if (isset($this->return->statea)) {
            return $this->return->statea;
        }
        return null;
    }

    /**
     * returns the state time for participant b
     *
     * @return string
     */
    public function getStateB()
    {
        if (isset($this->return->stateb)) {
            return $this->return->stateb;
        }
        return null;
    }
}
