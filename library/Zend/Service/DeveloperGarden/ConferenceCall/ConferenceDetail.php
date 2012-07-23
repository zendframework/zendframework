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
class Zend_Service_DeveloperGarden_ConferenceCall_ConferenceDetail
{
    /**
     * name of this conference
     *
     * @var string
     */
    public $name = null;

    /**
     * description of this conference
     *
     * @var string
     */
    public $description = null;

    /**
     * duration in seconds of this conference
     *
     * @var integer
     */
    public $duration = null;

    /**
     * create object
     *
     * @param string $name
     * @param string $description
     * @param integer $duration
     *
     * @return Zend_Service_DeveloperGarden_ConferenceCall_ConferenceDetail
     */
    public function __construct($name, $description, $duration)
    {
        $this->setName($name);
        $this->setDescription($description);
        $this->setDuration($duration);
    }

    /**
     * sets new duration for this conference in seconds
     *
     * @param integer $duration
     * @return Zend_Service_DeveloperGarden_ConferenceCall_ConferenceDetail
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;
        return $this;
    }

    /**
     * @return string
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * set the description of this conference
     *
     * @param $description the description to set
     * @return Zend_Service_DeveloperGarden_ConferenceCall_ConferenceDetail
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * sets the name of this conference
     *
     * @param string $name
     * @return Zend_Service_DeveloperGarden_ConferenceCall_ConferenceDetail
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
