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
class Zend_Service_DeveloperGarden_ConferenceCall_Participant
{
    /**
     * @var Zend_Service_DeveloperGarden_ConferenceCall_ParticipantDetail
     */
    public $detail = null;

    /**
     * @var string
     */
    public $participantId = null;

    /**
     * @var array
     */
    public $status = null;

    /**
     * participant details
     *
     * @return Zend_Service_DeveloperGarden_ConferenceCall_ParticipantDetail
     */
    public function getDetail()
    {
        return $this->detail;
    }

    /**
     * participant id
     *
     * @return string
     */
    public function getParticipantId()
    {
        return $this->participantId;
    }

    /**
     * get the status
     * returns an
     * array of Zend_Service_DeveloperGarden_ConferenceCall_ParticipantStatus
     *
     * @return array
     */
    public function getStatus()
    {
        return $this->status;
    }
}
