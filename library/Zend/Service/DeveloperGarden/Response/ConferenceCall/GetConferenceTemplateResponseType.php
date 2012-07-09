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
class Zend_Service_DeveloperGarden_Response_ConferenceCall_GetConferenceTemplateResponseType
    extends Zend_Service_DeveloperGarden_Response_BaseType
{
    /**
     * details
     *
     * @var Zend_Service_DeveloperGarden_ConferenceCall_ConferenceDetail
     */
    public $detail = null;

    /**
     * array of Zend_Service_DeveloperGarden_ConferenceCall_Participant
     *
     * @var array
     */
    public $participants = null;

    /**
     * returns the details object
     *
     * @return Zend_Service_DeveloperGarden_ConferenceCall_ConferenceDetail
     */
    public function getDetail()
    {
        return $this->detail;
    }

    /**
     * returns array with all participants
     * Zend_Service_DeveloperGarden_ConferenceCall_ParticipantDetail
     *
     * @return array of Zend_Service_DeveloperGarden_ConferenceCall_ParticipantDetail
     */
    public function getParticipants()
    {
        if ($this->participants instanceof Zend_Service_DeveloperGarden_ConferenceCall_Participant) {
            $this->participants = array(
                $this->participants
            );
        }
        return $this->participants;
    }

    /**
     * returns the participant object if found in the response
     *
     * @param string $participantId
     * @return Zend_Service_DeveloperGarden_ConferenceCall_Participant
     */
    public function getParticipantById($participantId)
    {
        $participants = $this->getParticipants();
        if ($participants !== null) {
            foreach ($participants as $participant) {
                if (strcmp($participant->getParticipantId(), $participantId) == 0) {
                    return $participant;
                }
            }
        }
        return null;
    }
}
