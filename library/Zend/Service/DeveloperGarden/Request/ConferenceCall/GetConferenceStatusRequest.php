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
class Zend_Service_DeveloperGarden_Request_ConferenceCall_GetConferenceStatusRequest
    extends Zend_Service_DeveloperGarden_Request_AbstractRequest
{
    /**
     * the conference id
     *
     * @var string
     */
    public $conferenceId = null;

    /**
     * what
     *
     * @var integer
     */
    public $what = null;

    /**
     * possible what values
     *
     * @var array
     */
    private $_whatValues = array(
        0 => 'all conferences',
        1 => 'just detail, acc and startTime',
        2 => 'just participants',
        3 => 'just schedule',
    );

    /**
     * constructor
     *
     * @param integer $environment
     * @param string $conferenceId
     * @param integer $what
     */
    public function __construct($environment, $conferenceId, $what)
    {
        parent::__construct($environment);
        $this->setConferenceId($conferenceId)
             ->setWhat($what);
    }

    /**
     * set the conference id
     *
     * @param string $conferenceId
     * @return Zend_Service_DeveloperGarden_Request_ConferenceCall_GetConferenceStatusRequest
     */
    public function setConferenceId($conferenceId)
    {
        $this->conferenceId = $conferenceId;
        return $this;
    }

    /**
     * sets $what
     *
     * @param integer $what
     * @return Zend_Service_DeveloperGarden_Request_ConferenceCall_GetConferenceStatusRequest
     */
    public function setWhat($what)
    {
        if (!array_key_exists($what, $this->_whatValues)) {
            throw new Zend_Service_DeveloperGarden_Request_Exception('What value not allowed.');
        }
        $this->what = $what;
        return $this;
    }
}
