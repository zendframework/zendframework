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
class Zend_Service_DeveloperGarden_Request_BaseUserService_GetQuotaInformation
{
    /**
     * string module id
     *
     * @var string
     */
    public $moduleId = null;

    /**
     * constructor give them the module id
     *
     * @param string $moduleId
     * @return Zend_Service_DeveloperGarden_Request_BaseUserService
     */
    public function __construct($moduleId = null)
    {
        $this->setModuleId($moduleId);
    }

    /**
     * sets a new moduleId
     *
     * @param integer $moduleId
     * @return Zend_Service_DeveloperGarden_Request_BaseUserService
     */
    public function setModuleId($moduleId = null)
    {
        $this->moduleId = $moduleId;
        return $this;
    }

    /**
     * returns the moduleId
     *
     * @return string
     */
    public function getModuleId()
    {
        return $this->moduleId;
    }
}
