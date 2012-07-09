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
class Zend_Service_DeveloperGarden_BaseUserService_AccountBalance
{
    /**
     * @var integer
     */
    public $Account = null;

    /**
     * @var integer $Credits
     */
    public $Credits = null;

    /**
     * returns the account id
     *
     * @return integer
     */
    public function getAccount()
    {
        return $this->Account;
    }

    /**
     * returns the credits
     *
     * @return integer
     */
    public function getCredits()
    {
        return $this->Credits;
    }
}
