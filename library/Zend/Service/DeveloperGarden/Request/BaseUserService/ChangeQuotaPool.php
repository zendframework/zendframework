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
class Zend_Service_DeveloperGarden_Request_BaseUserService_ChangeQuotaPool
{
    /**
     * string module id
     *
     * @var string
     */
    public $moduleId = null;

    /**
     * integer >= 0 to set new user quota
     *
     * @var integer
     */
    public $quotaMax = 0;

    /**
     * constructor give them the module id
     *
     * @param string $moduleId
     * @param integer $quotaMax
     * @return Zend_Service_Developergarde_Request_ChangeQuotaPool
     */
    public function __construct($moduleId = null, $quotaMax = 0)
    {
        $this->setModuleId($moduleId)
             ->setQuotaMax($quotaMax);
    }

    /**
     * sets a new moduleId
     *
     * @param integer $moduleId
     * @return Zend_Service_Developergarde_Request_ChangeQuotaPool
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

    /**
     * sets new QuotaMax value
     *
     * @param integer $quotaMax
     * @return Zend_Service_Developergarde_Request_ChangeQuotaPool
     */
    public function setQuotaMax($quotaMax = 0)
    {
        $this->quotaMax = $quotaMax;
        return $this;
    }

    /**
     * returns the quotaMax value
     *
     * @return integer
     */
    public function getQuotaMax()
    {
        return $this->quotaMax;
    }
}
