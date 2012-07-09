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
class Zend_Service_DeveloperGarden_Request_VoiceButler_NewCall
    extends Zend_Service_DeveloperGarden_Request_VoiceButler_AbstractVoiceButler
{
    /**
     * the first number to be called
     *
     * @var string
     */
    public $aNumber = null;

    /**
     * the second number to be called
     *
     * @var string
     */
    public $bNumber = null;

    /**
     * Calling Line Identity Restriction (CLIR) disabled for $aNumber
     *
     * @var boolean
     */
    public $privacyA = null;

    /**
     * Calling Line Identity Restriction (CLIR) disabled for $bNumber
     *
     * @var boolean
     */
    public $privacyB = null;

    /**
     * time in seconds to wait for $aNumber
     *
     * @var integer
     */
    public $expiration = null;

    /**
     * max duration for this call in seconds
     *
     * @var integer
     */
    public $maxDuration = null;

    /**
     * param not used right now
     *
     * @var string
     */
    public $greeter = null;

    /**
     * Account Id which will be pay for this call
     *
     * @var integer
     */
    public $account = null;

    /**
     * @return string
     */
    public function getANumber()
    {
        return $this->aNumber;
    }

    /**
     * @param string $aNumber
     * @return Zend_Service_DeveloperGarden_Request_VoiceButler_NewCall
     */
    public function setANumber($aNumber)
    {
        $this->aNumber = $aNumber;
        return $this;
    }

    /**
     * @return string
     */
    public function getBNumber()
    {
        return $this->bNumber;
    }

    /**
     * @param string $bNumber
     * @return Zend_Service_DeveloperGarden_Request_VoiceButler_NewCall
     */
    public function setBNumber($bNumber)
    {
        $this->bNumber = $bNumber;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getPrivacyA()
    {
        return $this->privacyA;
    }

    /**
     * @param boolean $privacyA
     * @return Zend_Service_DeveloperGarden_Request_VoiceButler_NewCall
     */
    public function setPrivacyA($privacyA)
    {
        $this->privacyA = $privacyA;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getPrivacyB()
    {
        return $this->privacyB;
    }

    /**
     * @param boolean $privacyB
     * @return Zend_Service_DeveloperGarden_Request_VoiceButler_NewCall
     */
    public function setPrivacyB($privacyB)
    {
        $this->privacyB = $privacyB;
        return $this;
    }

    /**
     * @return integer
     */
    public function getExpiration()
    {
        return $this->expiration;
    }

    /**
     * @param integer $expiration
     * @return Zend_Service_DeveloperGarden_Request_VoiceButler_NewCall
     */
    public function setExpiration($expiration)
    {
        $this->expiration = $expiration;
        return $this;
    }

    /**
     * @return integer
     */
    public function getMaxDuration()
    {
        return $this->maxDuration;
    }

    /**
     * @param integer $maxDuration
     * @return Zend_Service_DeveloperGarden_Request_VoiceButler_NewCall
     */
    public function setMaxDuration($maxDuration)
    {
        $this->maxDuration = $maxDuration;
        return $this;
    }

    /**
     * @return string
     */
    public function getGreeter()
    {
        return $this->greeter;
    }

    /**
     * @param string $greeter
     * @return Zend_Service_DeveloperGarden_Request_VoiceButler_NewCall
     */
    public function setGreeter($greeter)
    {
        $this->greeter = $greeter;
        return $this;
    }

    /**
     * @return string
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * @param integer $account
     * @return Zend_Service_DeveloperGarden_Request_VoiceButler_NewCall
     */
    public function setAccount($account)
    {
        $this->account = $account;
        return $this;
    }
}
