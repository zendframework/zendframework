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
class Zend_Service_DeveloperGarden_ConferenceCall_ParticipantStatus
{
    /**
     * @var string
     */
    public $name = null;

    /**
     * @var string
     */
    public $value = null;

    /**
     * constructor for participant status object
     *
     * @param string $vame
     * @param string $value
     */
    public function __construct($name, $value = null)
    {
        $this->setName($name)
             ->setValue($value);
    }

    /**
     * returns the value of $name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * sets $name
     *
     * @param string $name
     * @return Zend_Service_DeveloperGarden_ConferenceCall_ParticipantStatus
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * returns the value of $value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * sets $value
     *
     * @param string $value
     * @return Zend_Service_DeveloperGarden_ConferenceCall_ParticipantStatus
     */
    public function setValue($value = null)
    {
        $this->value = $value;
        return $this;
    }
}
