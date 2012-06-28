<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Amf
 */

namespace ZendTest\Amf\TestAsset\Authentication;

use Zend\Amf\AbstractAuthentication;
use Zend\Authentication\Result;

class RightPassword extends AbstractAuthentication
{
    public function __construct($name, $role)
    {
        $this->_name = $name;
        $this->_role = $role;
    }

    public function authenticate()
    {
        $id       = new \stdClass();
        $id->role = $this->_role;
        $id->name = $this->_name;
        return new Result(Result::SUCCESS, $id);
    }
}

