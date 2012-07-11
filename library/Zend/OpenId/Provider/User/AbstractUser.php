<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_OpenId
 */

namespace Zend\OpenId\Provider\User;

/**
 * Abstract class to get/store information about logged in user in Web Browser
 *
 * @category   Zend
 * @package    Zend_OpenId
 * @subpackage Zend\OpenId\Provider\GenericProvider
 */
abstract class AbstractUser
{

    /**
     * Stores information about logged in user
     *
     * @param string $id user identity URL
     * @return bool
     */
    abstract public function setLoggedInUser($id);

    /**
     * Returns identity URL of logged in user or false
     *
     * @return mixed
     */
    abstract public function getLoggedInUser();

    /**
     * Performs logout. Clears information about logged in user.
     *
     * @return bool
     */
    abstract public function delLoggedInUser();
}
