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
class Zend_Service_DeveloperGarden_Request_BaseUserService_GetAccountBalance
{
    /**
     * array of accounts
     *
     * @var array
     */
    public $Account = array();

    /**
     * constructor give them the account ids or an empty array
     *
     * @param array $Account
     * @return Zend_Service_DeveloperGarden_Request_GetAccountBalance
     */
    public function __construct(array $Account = array())
    {
        $this->setAccount($Account);
    }

    /**
     * sets a new Account array
     *
     * @param array $Account
     * @return Zend_Service_DeveloperGarden_Request_BaseUserService
     */
    public function setAccount(array $Account = array())
    {
        $this->Account = $Account;
        return $this;
    }

    /**
     * returns the moduleId
     *
     * @return string
     */
    public function getAccount()
    {
        return $this->Account;
    }
}
