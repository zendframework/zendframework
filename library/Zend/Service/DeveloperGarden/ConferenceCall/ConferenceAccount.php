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
class Zend_Service_DeveloperGarden_ConferenceCall_ConferenceAccount
{
    /**
     * type of billing
     *
     * @var string
     */
    public $billingtype = null;

    /**
     * account id
     *
     * @var integer
     */
    public $account = null;

    /**
     * @return integer
     */
    public function getAccount()
    {
        return $this->account;
    }

    /**
     * @return string
     */
    public function getBillingType()
    {
        return $this->billingtype;
    }
}
