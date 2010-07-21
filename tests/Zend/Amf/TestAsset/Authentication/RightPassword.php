<?php

namespace ZendTest\Amf\TestAsset\Authentication;

use Zend\Amf\AbstractAuthentication,
    Zend\Authentication\Result;

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

