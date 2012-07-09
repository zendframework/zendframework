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
class Zend_Service_DeveloperGarden_Request_ConferenceCall_UpdateConferenceRequest
    extends Zend_Service_DeveloperGarden_Request_AbstractRequest
{
    /**
     * conference id
     *
     * @var string
     */
    public $conferenceId = null;

    /**
     * account to be used for this conference
     *
     * @var integer
     */
    public $account = null;

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
     * object with schedule for this conference
     *
     * @var Zend_Service_DeveloperGarden_ConferenceCall_ConferenceSchedule
     */
    public $schedule = null;

    /**
     * constructor
     *
     * @param integer $environment
     * @param string $conferenceId
     * @param string $ownerId
     * @param Zend_Service_DeveloperGarden_ConferenceCall_ConferenceDetail $conferenceDetails
     * @param Zend_Service_DeveloperGarden_ConferenceCall_ConferenceSchedule $conferenceSchedule
     * @param integer $account
     */
    public function __construct($environment, $conferenceId, $ownerId = null,
        Zend_Service_DeveloperGarden_ConferenceCall_ConferenceDetail $conferenceDetails = null,
        Zend_Service_DeveloperGarden_ConferenceCall_ConferenceSchedule $conferenceSchedule = null,
        $account = null
    ) {
        parent::__construct($environment);
        $this->setConferenceId($conferenceId)
             ->setOwnerId($ownerId)
             ->setDetail($conferenceDetails)
             ->setSchedule($conferenceSchedule)
             ->setAccount($account);
    }

    /**
     * sets $conferenceId
     *
     * @param string $conferenceId
     * @return Zend_Service_DeveloperGarden_Request_ConferenceCall_UpdateConferenceRequest
     */
    public function setConferenceId($conferenceId)
    {
        $this->conferenceId= $conferenceId;
        return $this;
    }

    /**
     * sets $schedule
     *
     * @param Zend_Service_DeveloperGarden_ConferenceCall_ConferenceSchedule $schedule
     * @return Zend_Service_DeveloperGarden_Request_ConferenceCall_CreateConferenceRequest
     */
    public function setSchedule(
        Zend_Service_DeveloperGarden_ConferenceCall_ConferenceSchedule $schedule = null
    ) {
        $this->schedule = $schedule;
        return $this;
    }

    /**
     * sets $detail
     *
     * @param Zend_Service_DeveloperGarden_ConferenceCall_ConferenceDetail $detail
     * @return Zend_Service_DeveloperGarden_Request_ConferenceCall_CreateConferenceRequest
     */
    public function setDetail(
        Zend_Service_DeveloperGarden_ConferenceCall_ConferenceDetail $detail = null
    ) {
        $this->detail = $detail;
        return $this;
    }

    /**
     * sets $ownerId
     *
     * @param string $ownerId
     * @return Zend_Service_DeveloperGarden_Request_ConferenceCall_CreateConferenceRequest
     */
    public function setOwnerId($ownerId = null)
    {
        $this->ownerId = $ownerId;
        return $this;
    }

    /**
     * sets $account
     *
     * @param $account
     * @return Zend_Service_DeveloperGarden_Request_ConferenceCall_CreateConferenceRequest
     */
    public function setAccount($account = null)
    {
        $this->account = $account;
        return $this;
    }
}
