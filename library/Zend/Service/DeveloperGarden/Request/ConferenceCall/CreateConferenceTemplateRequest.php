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
class Zend_Service_DeveloperGarden_Request_ConferenceCall_CreateConferenceTemplateRequest
    extends Zend_Service_DeveloperGarden_Request_AbstractRequest
{
    /**
     * unique owner id
     *
     * @var string
     */
    public $ownerId = null;

    /**
     * object with details for this conference
     *
     * @var Zend_Service_DeveloperGarden_ConferenceCall_ConferenceDetail
     */
    public $detail = null;

    /**
     * array with Zend_Service_DeveloperGarden_ConferenceCall_ParticipantDetail elements
     *
     * @var array
     */
    public $participants = null;

    /**
     * constructor
     *
     * @param integer $environment
     * @param string $ownerId
     * @param Zend_Service_DeveloperGarden_ConferenceCall_ConferenceDetail $conferenceDetails
     * @param array $conferenceParticipants
     */
    public function __construct($environment, $ownerId,
        Zend_Service_DeveloperGarden_ConferenceCall_ConferenceDetail $conferenceDetails,
        array $conferenceParticipants = null
    ) {
        parent::__construct($environment);
        $this->setOwnerId($ownerId)
             ->setDetail($conferenceDetails)
             ->setParticipants($conferenceParticipants);
    }

    /**
     * sets $participants
     *
     * @param array $participants
     * @return Zend_Service_DeveloperGarden_Request_ConferenceCall_CreateConferenceTemplateRequest
     */
    public function setParticipants(array $participants = null)
    {
        $this->participants = $participants;
        return $this;
    }

    /**
     * sets $detail
     *
     * @param Zend_Service_DeveloperGarden_ConferenceCall_ConferenceDetail $detail
     * @return Zend_Service_DeveloperGarden_Request_ConferenceCall_CreateConferenceTemplateRequest
     */
    public function setDetail(Zend_Service_DeveloperGarden_ConferenceCall_ConferenceDetail $detail)
    {
        $this->detail = $detail;
        return $this;
    }

    /**
     * sets $ownerId
     *
     * @param string $ownerId
     * @return Zend_Service_DeveloperGarden_Request_ConferenceCall_CreateConferenceTemplateRequest
     */
    public function setOwnerId($ownerId)
    {
        $this->ownerId = $ownerId;
        return $this;
    }
}
