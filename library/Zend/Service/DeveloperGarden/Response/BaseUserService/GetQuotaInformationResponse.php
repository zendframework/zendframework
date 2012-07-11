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
class Zend_Service_DeveloperGarden_Response_BaseUserService_GetQuotaInformationResponse
    extends Zend_Service_DeveloperGarden_Response_AbstractResponse
{
    /**
     * System defined limit of quota points per day
     *
     * @var integer
     */
    public $maxQuota = null;

    /**
     * User specific limit of quota points per day
     * cant be more than $maxQuota
     *
     * @var integer
     */
    public $maxUserQuota = null;

    /**
     * Used quota points for the current day
     *
     * @var integer
     */
    public $quotaLevel = null;

    /**
     * returns the quotaLevel
     *
     * @return integer
     */
    public function getQuotaLevel()
    {
        return $this->quotaLevel;
    }

    /**
     * returns the maxUserQuota
     *
     * @return integer
     */
    public function getMaxUserQuota()
    {
        return $this->maxUserQuota;
    }

    /**
     * return the maxQuota
     *
     * @return integer
     */
    public function getMaxQuota()
    {
        return $this->maxQuota;
    }
}
