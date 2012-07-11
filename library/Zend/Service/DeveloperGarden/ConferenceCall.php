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
class Zend_Service_DeveloperGarden_ConferenceCall
    extends Zend_Service_DeveloperGarden_Client_AbstractClient
{
    /**
     * wsdl file
     *
     * @var string
     */
    protected $_wsdlFile = 'https://gateway.developer.telekom.com/p3gw-mod-odg-ccs/services/ccsPort?wsdl';

    /**
     * the local WSDL file
     *
     * @var string
     */
    protected $_wsdlFileLocal = 'Wsdl/ccsPort.wsdl';

    /**
     * Response, Request Classmapping
     *
     * @var array
     *
     */
    protected $_classMap = array(
        //Struct
        'ConferenceDetailStruct'  => 'Zend_Service_DeveloperGarden_ConferenceCall_ConferenceDetail',
        'ConferenceAccStruct'     => 'Zend_Service_DeveloperGarden_ConferenceCall_ConferenceAccount',
        'ScheduleStruct'          => 'Zend_Service_DeveloperGarden_ConferenceCall_ConferenceSchedule',
        'ParticipantStruct'       => 'Zend_Service_DeveloperGarden_ConferenceCall_Participant',
        'ParticipantDetailStruct' => 'Zend_Service_DeveloperGarden_ConferenceCall_ParticipantDetail',
        'ParticipantStatusStruct' => 'Zend_Service_DeveloperGarden_ConferenceCall_ParticipantStatus',

        //Responses
        'CCSResponseType' => 'Zend_Service_DeveloperGarden_Response_ConferenceCall_CCSResponseType',

        //Conference
        'createConferenceResponse'         => 'Zend_Service_DeveloperGarden_Response_ConferenceCall_CreateConferenceResponse',
        'createConferenceResponseType'     => 'Zend_Service_DeveloperGarden_Response_ConferenceCall_CreateConferenceResponseType',
        'removeConferenceResponse'         => 'Zend_Service_DeveloperGarden_Response_ConferenceCall_RemoveConferenceResponse',
        'commitConferenceResponse'         => 'Zend_Service_DeveloperGarden_Response_ConferenceCall_CommitConferenceResponse',
        'updateConferenceResponse'         => 'Zend_Service_DeveloperGarden_Response_ConferenceCall_UpdateConferenceResponse',
        'getConferenceStatusResponse'      => 'Zend_Service_DeveloperGarden_Response_ConferenceCall_GetConferenceStatusResponse',
        'getConferenceStatusResponseType'  => 'Zend_Service_DeveloperGarden_Response_ConferenceCall_GetConferenceStatusResponseType',
        'getRunningConferenceResponse'     => 'Zend_Service_DeveloperGarden_Response_ConferenceCall_GetRunningConferenceResponse',
        'getRunningConferenceResponseType' => 'Zend_Service_DeveloperGarden_Response_ConferenceCall_GetRunningConferenceResponseType',
        'getConferenceListResponse'        => 'Zend_Service_DeveloperGarden_Response_ConferenceCall_GetConferenceListResponse',
        'getConferenceListResponseType'    => 'Zend_Service_DeveloperGarden_Response_ConferenceCall_GetConferenceListResponseType',

        //Participant
        'newParticipantResponse'           => 'Zend_Service_DeveloperGarden_Response_ConferenceCall_NewParticipantResponse',
        'newParticipantResponseType'       => 'Zend_Service_DeveloperGarden_Response_ConferenceCall_NewParticipantResponseType',
        'removeParticipantResponse'        => 'Zend_Service_DeveloperGarden_Response_ConferenceCall_RemoveParticipantResponse',
        'updateParticipantResponse'        => 'Zend_Service_DeveloperGarden_Response_ConferenceCall_UpdateParticipantResponse',
        'getParticipantStatusResponse'     => 'Zend_Service_DeveloperGarden_Response_ConferenceCall_GetParticipantStatusResponse',
        'getParticipantStatusResponseType' => 'Zend_Service_DeveloperGarden_Response_ConferenceCall_GetParticipantStatusResponseType',

        //Templates
        'createConferenceTemplateResponse'             => 'Zend_Service_DeveloperGarden_Response_ConferenceCall_CreateConferenceTemplateResponse',
        'createConferenceTemplateResponseType'         => 'Zend_Service_DeveloperGarden_Response_ConferenceCall_CreateConferenceTemplateResponseType',
        'getConferenceTemplateResponse'                => 'Zend_Service_DeveloperGarden_Response_ConferenceCall_GetConferenceTemplateResponse',
        'getConferenceTemplateResponseType'            => 'Zend_Service_DeveloperGarden_Response_ConferenceCall_GetConferenceTemplateResponseType',
        'updateConferenceTemplateResponse'             => 'Zend_Service_DeveloperGarden_Response_ConferenceCall_UpdateConferenceTemplateResponse',
        'removeConferenceTemplateResponse'             => 'Zend_Service_DeveloperGarden_Response_ConferenceCall_RemoveConferenceTemplateResponse',
        'getConferenceTemplateListResponse'            => 'Zend_Service_DeveloperGarden_Response_ConferenceCall_GetConferenceTemplateListResponse',
        'getConferenceTemplateListResponseType'        => 'Zend_Service_DeveloperGarden_Response_ConferenceCall_GetConferenceTemplateListResponseType',
        'addConferenceTemplateParticipantResponse'     => 'Zend_Service_DeveloperGarden_Response_ConferenceCall_AddConferenceTemplateParticipantResponse',
        'addConferenceTemplateParticipantResponseType' => 'Zend_Service_DeveloperGarden_Response_ConferenceCall_AddConferenceTemplateParticipantResponseType',
        'getConferenceTemplateParticipantResponse'     => 'Zend_Service_DeveloperGarden_Response_ConferenceCall_GetConferenceTemplateParticipantResponse',
        'getConferenceTemplateParticipantResponseType' => 'Zend_Service_DeveloperGarden_Response_ConferenceCall_GetConferenceTemplateParticipantResponseType',
        'updateConferenceTemplateParticipantResponse'  => 'Zend_Service_DeveloperGarden_Response_ConferenceCall_UpdateConferenceTemplateParticipantResponse',
        'removeConferenceTemplateParticipantResponse'  => 'Zend_Service_DeveloperGarden_Response_ConferenceCall_RemoveConferenceTemplateParticipantResponse',
    );

    /**
     * creates a new conference, ownerId should be between 3 and 39
     * chars
     *
     * @param string $ownerId
     * @param Zend_Service_DeveloperGarden_ConferenceCall_ConferenceDetail $conferenceDetails
     * @param Zend_Service_DeveloperGarden_ConferenceCall_ConferenceSchedule $conferenceSchedule
     * @param integer $account
     * @return Zend_Service_DeveloperGarden_Response_ConferenceCall_CreateConferenceResponseType
     */
    public function createConference($ownerId,
        Zend_Service_DeveloperGarden_ConferenceCall_ConferenceDetail $conferenceDetails,
        Zend_Service_DeveloperGarden_ConferenceCall_ConferenceSchedule $conferenceSchedule = null,
        $account = null
    ) {
        $request = new Zend_Service_DeveloperGarden_Request_ConferenceCall_CreateConferenceRequest(
            $this->getEnvironment(),
            $ownerId,
            $conferenceDetails,
            $conferenceSchedule,
            $account
        );

        $result = $this->getSoapClient()->createConference(array(
            'createConferenceRequest' => $request
        ));

        return $result->parse();
    }

    /**
     * commits the given conference
     *
     * @param string $conferenceId
     * @return Zend_Service_DeveloperGarden_Response_ConferenceCall_CommitConferenceResponse
     */
    public function commitConference($conferenceId)
    {
        $request = new Zend_Service_DeveloperGarden_Request_ConferenceCall_CommitConferenceRequest(
            $this->getEnvironment(),
            $conferenceId
        );

        $result = $this->getSoapClient()->commitConference(array(
            'commitConferenceRequest' => $request
        ));

        return $result->parse();
    }

    /**
     * updates a conference with the given parameter
     *
     * @param string $conferenceId
     * @param string $ownerId
     * @param Zend_Service_DeveloperGarden_ConferenceCall_ConferenceDetail $conferenceDetails
     * @param Zend_Service_DeveloperGarden_ConferenceCall_ConferenceSchedule $conferenceSchedule
     * @param string $account
     * @return Zend_Service_DeveloperGarden_Response_ConferenceCall_CCSResponseType
     */
    public function updateConference(
        $conferenceId,
        $ownerId = null,
        Zend_Service_DeveloperGarden_ConferenceCall_ConferenceDetail $conferenceDetails = null,
        Zend_Service_DeveloperGarden_ConferenceCall_ConferenceSchedule $conferenceSchedule = null,
        $account = null
    ) {
        $request = new Zend_Service_DeveloperGarden_Request_ConferenceCall_UpdateConferenceRequest(
            $this->getEnvironment(),
            $conferenceId,
            $ownerId,
            $conferenceDetails,
            $conferenceSchedule,
            $account
        );

        $result = $this->getSoapClient()->updateConference(array(
            'updateConferenceRequest' => $request
        ));

        return $result->parse();
    }

    /**
     * get conference status details
     *
     * @param string $conferenceId
     * @param integer $what
     * @return Zend_Service_DeveloperGarden_Response_ConferenceCall_GetConferenceStatusResponseType
     */
    public function getConferenceStatus($conferenceId, $what = 0)
    {
        $request = new Zend_Service_DeveloperGarden_Request_ConferenceCall_GetConferenceStatusRequest(
            $this->getEnvironment(),
            $conferenceId,
            $what
        );

        $result = $this->getSoapClient()->getConferenceStatus(array(
            'getConferenceStatusRequest' => $request
        ));

        return $result->parse();
    }

    /**
     * returns the conferenceId of the running conference instance for a planned
     * recurring conference or the current conferenceId
     *
     * @return Zend_Service_DeveloperGarden_Response_ConferenceCall_GetRunningConferenceResponseType
     */
    public function getRunningConference($conferenceId)
    {
        $request = new Zend_Service_DeveloperGarden_Request_ConferenceCall_GetRunningConferenceRequest(
            $this->getEnvironment(),
            $conferenceId
        );

        $result = $this->getSoapClient()->getRunningConference(array(
            'getRunningConferenceRequest' => $request
        ));

        return $result->parse();
    }

    /**
     * remove a conference
     *
     * @param string $conferenceId
     * @return Zend_Service_DeveloperGarden_Response_ConferenceCall_CCSResponseType
     */
    public function removeConference($conferenceId)
    {
        $request = new Zend_Service_DeveloperGarden_Request_ConferenceCall_RemoveConferenceRequest(
            $this->getEnvironment(),
            $conferenceId
        );

        $result = $this->getSoapClient()->removeConference(array(
            'removeConferenceRequest' => $request
        ));

        return $result->parse();
    }

    /**
     * returns a list of conferences
     *
     * @param integer $what
     * @param string $ownerId
     * @return Zend_Service_DeveloperGarden_Response_ConferenceCall_GetConferenceListResponseType
     */
    public function getConferenceList($what = 0, $ownerId = null)
    {
        $request = new Zend_Service_DeveloperGarden_Request_ConferenceCall_GetConferenceListRequest(
            $this->getEnvironment(),
            $what,
            $ownerId
        );

        $result = $this->getSoapClient()->getConferenceList(array(
            'getConferenceListRequest' => $request
        ));

        return $result->parse();
    }

    /**
     * adds a new participant to the given conference
     *
     * @param string $conferenceId
     * @param Zend_Service_DeveloperGarden_ConferenceCall_ParticipantDetail $participant
     * @return Zend_Service_DeveloperGarden_Response_ConferenceCall_NewParticipantResponseType
     */
    public function newParticipant(
        $conferenceId,
        Zend_Service_DeveloperGarden_ConferenceCall_ParticipantDetail $participant
    ) {
        $request = new Zend_Service_DeveloperGarden_Request_ConferenceCall_NewParticipantRequest(
            $this->getEnvironment(),
            $conferenceId,
            $participant
        );

        $result = $this->getSoapClient()->newParticipant(array(
            'newParticipantRequest' => $request
        ));

        return $result->parse();
    }

    /**
     * fetches the participant details for the given conferenceId
     *
     * @param string $conferenceId
     * @param string $participantId
     * @return Zend_Service_DeveloperGarden_Response_ConferenceCall_GetParticipantStatusResponseType
     */
    public function getParticipantStatus($conferenceId, $participantId)
    {
        $request = new Zend_Service_DeveloperGarden_Request_ConferenceCall_GetParticipantStatusRequest(
            $this->getEnvironment(),
            $conferenceId,
            $participantId
        );

        $result = $this->getSoapClient()->getParticipantStatus(array(
            'getParticipantStatusRequest' => $request
        ));

        return $result->parse();
    }

    /**
     * removes the given participant from the conference
     *
     * @param string $conferenceId
     * @param string $participantId
     * @return Zend_Service_DeveloperGarden_Response_ConferenceCall_CCSResponseType
     */
    public function removeParticipant($conferenceId, $participantId)
    {
        $request = new Zend_Service_DeveloperGarden_Request_ConferenceCall_RemoveParticipantRequest(
            $this->getEnvironment(),
            $conferenceId,
            $participantId
        );

        $result = $this->getSoapClient()->removeParticipant(array(
            'removeParticipantRequest' => $request
        ));

        return $result->parse();
    }

    /**
     * updates the participant in the given conference
     *
     * @param string $conferenceId
     * @param string $participantId
     * @param integer $action
     * @param Zend_Service_DeveloperGarden_ConferenceCall_ParticipantDetail $participant
     * @return Zend_Service_DeveloperGarden_Response_ConferenceCall_CCSResponseType
     */
    public function updateParticipant(
        $conferenceId,
        $participantId,
        $action = null,
        Zend_Service_DeveloperGarden_ConferenceCall_ParticipantDetail $participant = null
    ) {
        $request = new Zend_Service_DeveloperGarden_Request_ConferenceCall_UpdateParticipantRequest(
            $this->getEnvironment(),
            $conferenceId,
            $participantId,
            $action,
            $participant
        );

        $result = $this->getSoapClient()->updateParticipant(array(
            'updateParticipantRequest' => $request
        ));

        return $result->parse();
    }

    /**
     * creates a new conference template
     *
     * @param string $ownerId
     * @param Zend_Service_DeveloperGarden_ConferenceCall_ConferenceDetail $conferenceDetails
     * @param array $participants
     * @return Zend_Service_DeveloperGarden_Response_ConferenceCall_CreateConferenceTemplateResponseType
     */
    public function createConferenceTemplate(
        $ownerId,
        Zend_Service_DeveloperGarden_ConferenceCall_ConferenceDetail $conferenceDetails,
        array $participants = null
    ) {
        $request = new Zend_Service_DeveloperGarden_Request_ConferenceCall_CreateConferenceTemplateRequest(
            $this->getEnvironment(),
            $ownerId,
            $conferenceDetails,
            $participants
        );

        $result = $this->getSoapClient()->createConferenceTemplate(array(
            'createConferenceTemplateRequest' => $request
        ));

        return $result->parse();
    }

    /**
     * get a specific template
     *
     * @param string $templateId
     * @return Zend_Service_DeveloperGarden_Response_ConferenceCall_GetConferenceTemplateResponseType
     */
    public function getConferenceTemplate($templateId)
    {
        $request = new Zend_Service_DeveloperGarden_Request_ConferenceCall_GetConferenceTemplateRequest(
            $this->getEnvironment(),
            $templateId
        );

        $result = $this->getSoapClient()->getConferenceTemplate(array(
            'getConferenceTemplateRequest' => $request
        ));

        return $result->parse();
    }

    /**
     * updates a conference template
     *
     * @param string $templateId
     * @param string $initiatorId
     * @param Zend_Service_DeveloperGarden_ConferenceCall_ConferenceDetail $conferenceDetails
     * @return Zend_Service_DeveloperGarden_Response_ConferenceCall_CCSResponseType
     */
    public function updateConferenceTemplate(
        $templateId,
        $initiatorId = null,
        Zend_Service_DeveloperGarden_ConferenceCall_ConferenceDetail $conferenceDetails = null
    ) {
        $request = new Zend_Service_DeveloperGarden_Request_ConferenceCall_UpdateConferenceTemplateRequest(
            $this->getEnvironment(),
            $templateId,
            $initiatorId,
            $conferenceDetails
        );

        $result = $this->getSoapClient()->updateConferenceTemplate(array(
            'updateConferenceTemplateRequest' => $request
        ));

        return $result->parse();
    }

    /**
     * remove a conference template
     *
     * @param string $templateId
     * @return Zend_Service_DeveloperGarden_Response_ConferenceCall_CCSResponseType
     */
    public function removeConferenceTemplate($templateId)
    {
        $request = new Zend_Service_DeveloperGarden_Request_ConferenceCall_RemoveConferenceTemplateRequest(
            $this->getEnvironment(),
            $templateId
        );

        $result = $this->getSoapClient()->removeConferenceTemplate(array(
            'removeConferenceTemplateRequest' => $request
        ));

        return $result->parse();
    }

    /**
     * lists all available conference templates for the given owner
     *
     * @param string $ownerId
     * @return Zend_Service_DeveloperGarden_Response_ConferenceCall_GetConferenceTemplateListResponseType
     */
    public function getConferenceTemplateList($ownerId)
    {
        $request = new Zend_Service_DeveloperGarden_Request_ConferenceCall_GetConferenceTemplateListRequest(
            $this->getEnvironment(),
            $ownerId
        );

        $result = $this->getSoapClient()->getConferenceTemplateList(array(
            'getConferenceTemplateListRequest' => $request
        ));

        return $result->parse();
    }

    /**
     * adds a new participants to the template
     *
     * @param string $templateId
     * @param Zend_Service_DeveloperGarden_ConferenceCall_ParticipantDetail $participant
     * @return Zend_Service_DeveloperGarden_Response_ConferenceCall_AddConferenceTemplateParticipantResponseType
     */
    public function addConferenceTemplateParticipant(
        $templateId,
        Zend_Service_DeveloperGarden_ConferenceCall_ParticipantDetail $participant
    ) {
        $request = new Zend_Service_DeveloperGarden_Request_ConferenceCall_AddConferenceTemplateParticipantRequest(
            $this->getEnvironment(),
            $templateId,
            $participant
        );

        $result = $this->getSoapClient()->addConferenceTemplateParticipant(array(
            'addConferenceTemplateParticipantRequest' => $request
        ));

        return $result->parse();
    }

    /**
     * returns a praticipant for the given templateId
     *
     * @param string $templateId
     * @param string $participantId
     * @return Zend_Service_DeveloperGarden_Response_ConferenceCall_GetConferenceTemplateParticipantResponseType
     */
    public function getConferenceTemplateParticipant($templateId, $participantId)
    {
        $request = new Zend_Service_DeveloperGarden_Request_ConferenceCall_GetConferenceTemplateParticipantRequest(
            $this->getEnvironment(),
            $templateId,
            $participantId
        );

        $result = $this->getSoapClient()->getConferenceTemplateParticipant(array(
            'getConferenceTemplateParticipantRequest' => $request
        ));

        return $result->parse();
    }

    /**
     * updates the participants details
     *
     * @param string $templateId
     * @param string $participantId
     * @param Zend_Service_DeveloperGarden_ConferenceCall_ParticipantDetail $participant
     * @return Zend_Service_DeveloperGarden_Response_ConferenceCall_CCSResponseType
     */
    public function updateConferenceTemplateParticipant(
        $templateId,
        $participantId,
        Zend_Service_DeveloperGarden_ConferenceCall_ParticipantDetail $participant
    ) {
        $request = new Zend_Service_DeveloperGarden_Request_ConferenceCall_UpdateConferenceTemplateParticipantRequest(
            $this->getEnvironment(),
            $templateId,
            $participantId,
            $participant
        );

        $result = $this->getSoapClient()->updateConferenceTemplateParticipant(array(
            'updateConferenceTemplateParticipantRequest' => $request
        ));

        return $result->parse();
    }

    /**
     * removes a praticipant from the given templateId
     *
     * @param string $templateId
     * @param string $participantId
     * @return Zend_Service_DeveloperGarden_Response_ConferenceCall_CCSResponseType
     */
    public function removeConferenceTemplateParticipant($templateId, $participantId)
    {
        $request = new Zend_Service_DeveloperGarden_Request_ConferenceCall_RemoveConferenceTemplateParticipantRequest(
            $this->getEnvironment(),
            $templateId,
            $participantId
        );

        $result = $this->getSoapClient()->removeConferenceTemplateParticipant(array(
            'removeConferenceTemplateParticipantRequest' => $request
        ));

        return $result->parse();
    }
}
