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
class Zend_Service_DeveloperGarden_Request_ConferenceCall_GetConferenceListRequest
    extends Zend_Service_DeveloperGarden_Request_AbstractRequest
{
    /**
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
        1 => 'just ad-hoc conferences',
        2 => 'just planned conferences',
        3 => 'just failed conferences',
    );

    /**
     * unique owner id
     *
     * @var string
     */
    public $ownerId = null;

    /**
     * constructor
     *
     * @param integer $environment
     * @param integer $what
     * @param string $ownerId
     */
    public function __construct($environment, $what = 0, $ownerId = null)
    {
        parent::__construct($environment);
        $this->setWhat($what)
             ->setOwnerId($ownerId);
    }

    /**
     * sets $what
     *
     * @param integer $what
     * @return Zend_Service_DeveloperGarden_Request_ConferenceCall_GetConferenceListRequest
     */
    public function setWhat($what)
    {
        if (!array_key_exists($what, $this->_whatValues)) {
            throw new Zend_Service_DeveloperGarden_Request_Exception('What value not allowed.');
        }
        $this->what = $what;
        return $this;
    }

    /**
     * sets $ownerId
     *
     * @param $ownerId
     * @return Zend_Service_DeveloperGarden_Request_ConferenceCall_GetConferenceListRequest
     */
    public function setOwnerId($ownerId)
    {
        $this->ownerId = $ownerId;
        return $this;
    }
}
