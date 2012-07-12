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
class Zend_Service_DeveloperGarden_Request_ConferenceCall_GetConferenceTemplateListRequest
    extends Zend_Service_DeveloperGarden_Request_AbstractRequest
{
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
     * @param string $ownerId
     */
    public function __construct($environment, $ownerId = null)
    {
        parent::__construct($environment);
        $this->setOwnerId($ownerId);
    }

    /**
     * sets $ownerId
     *
     * @param $ownerId
     * @return Zend_Service_DeveloperGarden_Request_ConferenceCall_GetConferenceTemplateListRequest
     */
    public function setOwnerId($ownerId)
    {
        $this->ownerId = $ownerId;
        return $this;
    }
}
