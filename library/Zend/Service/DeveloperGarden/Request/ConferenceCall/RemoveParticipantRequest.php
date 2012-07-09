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
class Zend_Service_DeveloperGarden_Request_ConferenceCall_RemoveParticipantRequest
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
     * constructor
     *
     * @param integer $environment
     * @param string $conferenceId
     * @param string $participantId
     */
    public function __construct($environment, $conferenceId, $participantId)
    {
        parent::__construct($environment);
        $this->setConferenceId($conferenceId)
             ->setParticipantId($participantId);
    }

    /**
     * set the conference id
     *
     * @param string $conferenceId
     * @return Zend_Service_DeveloperGarden_Request_ConferenceCall_RemoveParticipantRequest
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
     * @return Zend_Service_DeveloperGarden_Request_ConferenceCall_RemoveParticipantRequest
     */
    public function setParticipantId($participantId)
    {
        $this->participantId = $participantId;
        return $this;
    }
}
