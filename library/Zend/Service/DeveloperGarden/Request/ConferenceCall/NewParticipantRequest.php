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
class Zend_Service_DeveloperGarden_Request_ConferenceCall_NewParticipantRequest
    extends Zend_Service_DeveloperGarden_Request_AbstractRequest
{
    /**
     * the conference id
     *
     * @var string
     */
    public $conferenceId = null;

    /**
     * conference participant
     *
     * @var Zend_Service_DeveloperGarden_ConferenceCall_ParticipantDetail
     */
    public $participant = null;

    /**
     * constructor
     *
     * @param integer $environment
     * @param string $conferenceId
     * @param Zend_Service_DeveloperGarden_ConferenceCall_ParticipantDetail $participant
     */
    public function __construct($environment, $conferenceId,
        Zend_Service_DeveloperGarden_ConferenceCall_ParticipantDetail $participant)
    {
        parent::__construct($environment);
        $this->setConferenceId($conferenceId)
             ->setParticipant($participant);
    }

    /**
     * set the conference id
     *
     * @param string $conferenceId
     * @return Zend_Service_DeveloperGarden_Request_ConferenceCall_NewParticipantRequest
     */
    public function setConferenceId($conferenceId)
    {
        $this->conferenceId = $conferenceId;
        return $this;
    }

    /**
     * sets new participant
     *
     * @param Zend_Service_DeveloperGarden_ConferenceCall_ParticipantDetail $participant
     * @return Zend_Service_DeveloperGarden_Request_ConferenceCall_NewParticipantRequest
     */
    public function setParticipant(Zend_Service_DeveloperGarden_ConferenceCall_ParticipantDetail $participant)
    {
        $this->participant = $participant;
        return $this;
    }
}
