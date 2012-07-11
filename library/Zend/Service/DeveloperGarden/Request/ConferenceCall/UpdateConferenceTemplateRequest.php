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
class Zend_Service_DeveloperGarden_Request_ConferenceCall_UpdateConferenceTemplateRequest
    extends Zend_Service_DeveloperGarden_Request_AbstractRequest
{
    /**
     * the template id
     *
     * @var string
     */
    public $templateId = null;

    /**
     * the initiator id
     *
     * @var string
     */
    public $initiatorId = null;

    /**
     * the details
     *
     * @var Zend_Service_DeveloperGarden_ConferenceCall_ConferenceDetail
     */
    public $detail = null;

    /**
     * constructor
     *
     * @param integer $environment
     * @param string $templateId
     * @param string $initiatorId
     * @param Zend_Service_DeveloperGarden_ConferenceCall_ConferenceDetail $conferenceDetails
     */
    public function __construct($environment, $templateId, $initiatorId = null,
        Zend_Service_DeveloperGarden_ConferenceCall_ConferenceDetail $conferenceDetails = null
    ) {
        parent::__construct($environment);
        $this->setTemplateId($templateId)
             ->setInitiatorId($initiatorId)
             ->setDetail($conferenceDetails);
    }

    /**
     * set the template id
     *
     * @param string $templateId
     * @return Zend_Service_DeveloperGarden_Request_ConferenceCall_UpdateConferenceTemplateRequest
     */
    public function setTemplateId($templateId)
    {
        $this->templateId = $templateId;
        return $this;
    }

    /**
     * set the initiator id
     *
     * @param string $initiatorId
     * @return Zend_Service_DeveloperGarden_Request_ConferenceCall_UpdateConferenceTemplateRequest
     */
    public function setInitiatorId($initiatorId)
    {
        $this->initiatorId = $initiatorId;
        return $this;
    }

    /**
     * sets $detail
     *
     * @param Zend_Service_DeveloperGarden_ConferenceCall_ConferenceDetail $detail
     * @return Zend_Service_DeveloperGarden_Request_ConferenceCall_UpdateConferenceTemplateRequest
     */
    public function setDetail(
        Zend_Service_DeveloperGarden_ConferenceCall_ConferenceDetail $detail = null
    ) {
        $this->detail = $detail;
        return $this;
    }
}
