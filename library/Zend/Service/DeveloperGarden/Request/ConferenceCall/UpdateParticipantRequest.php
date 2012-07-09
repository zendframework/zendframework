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
class Zend_Service_DeveloperGarden_Request_ConferenceCall_UpdateParticipantRequest
    extends Zend_Service_DeveloperGarden_Request_AbstractRequest
{
    /**
     * the conference id
     *
     * @var string
     */
    public $conferenceId = null;

    /**
     * the participant id
     *
     * @var string
     */
    public $participantId = null;

    /**
     * conference participant
     *
     * @var Zend_Service_DeveloperGarden_ConferenceCall_ParticipantDetail
     */
    public $participant = null;

    /**
     * possible action
     *
     * @var integer
     */
    public $action = null;

    /**
     * constructor
     *
     * @param integer $environment
     * @param string $conferenceId
     * @param string $participantId
     * @param integer $action
     * @param Zend_Service_DeveloperGarden_ConferenceCall_ParticipantDetail $participant
     */
    public function __construct($environment, $conferenceId, $participantId,
        $action = null,
        Zend_Service_DeveloperGarden_ConferenceCall_ParticipantDetail $participant = null
    ) {
        parent::__construct($environment);
        $this->setConferenceId($conferenceId)
             ->setParticipantId($participantId)
             ->setAction($action)
             ->setParticipant($participant);
    }

    /**
     * set the conference id
     *
     * @param string $conferenceId
     * @return Zend_Service_DeveloperGarden_Request_ConferenceCall_UpdateParticipantRequest
     */
    public function setConferenceId($conferenceId)
    {
        $this->conferenceId = $conferenceId;
        return $this;
    }

    /**
     * set the participant id
     *
     * @param string $participantId
     * @return Zend_Service_DeveloperGarden_Request_ConferenceCall_UpdateParticipantRequest
     */
    public function setParticipantId($participantId)
    {
        $this->participantId = $participantId;
        return $this;
    }

    /**
     * sets new action
     *
     * @param integer $action
     * @return Zend_Service_DeveloperGarden_Request_ConferenceCall_UpdateParticipantRequest
     */
    public function setAction($action = null)
    {
        if ($action !== null) {
            Zend_Service_DeveloperGarden_ConferenceCall::checkParticipantAction($action);
        }
        $this->action = $action;
        return $this;
    }

    /**
     * sets new participant
     *
     * @param Zend_Service_DeveloperGarden_ConferenceCall_ParticipantDetail $participant
     * @return Zend_Service_DeveloperGarden_Request_ConferenceCall_UpdateParticipantRequest
     */
    public function setParticipant(
        Zend_Service_DeveloperGarden_ConferenceCall_ParticipantDetail $participant = null
    ) {
        $this->participant = $participant;
        return $this;
    }
}
