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
class Zend_Service_DeveloperGarden_Request_VoiceButler_NewCallSequenced
    extends Zend_Service_DeveloperGarden_Request_VoiceButler_NewCall
{
    /**
     * array of second numbers to be called sequenced
     *
     * @var array
     */
    public $bNumber = null;

    /**
     * max wait value to wait for new number to be called
     *
     * @var integer
     */
    public $maxWait = null;

    /**
     * @return array
     */
    public function getBNumber()
    {
        return $this->bNumber;
    }

    /**
     * @param array $bNumber
     * @return Zend_Service_DeveloperGarden_Request_VoiceButler_NewCall
     */
    /*public function setBNumber(array $bNumber)
    {
        $this->bNumber = $bNumber;
        return $this;
    }*/

    /**
     * returns the max wait value
     *
     * @return integer
     */
    public function getMaxWait()
    {
        return $this->maxWait;
    }

    /**
     * sets new max wait value for next number call
     *
     * @param integer $maxWait
     * @return Zend_Service_DeveloperGarden_Request_VoiceButler_NewCallSequenced
     */
    public function setMaxWait($maxWait)
    {
        $this->maxWait = $maxWait;
        return $this;
    }
}
