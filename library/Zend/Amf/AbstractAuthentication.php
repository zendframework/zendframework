<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Amf
 */

namespace Zend\Amf;

/**
 * AbstractBase abstract class for AMF authentication implementation
 *
 * @package    Zend_Amf
 * @subpackage Auth
 */
abstract class AbstractAuthentication implements \Zend\Authentication\Adapter\AdapterInterface
{
    protected $_username;
    protected $_password;

    public function setCredentials($username, $password)
    {
        $this->_username = $username;
        $this->_password = $password;
    }
}
