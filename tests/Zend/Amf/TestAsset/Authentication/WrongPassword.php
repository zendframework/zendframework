<?php

namespace ZendTest\Amf\TestAsset\Authentication;

use Zend\Amf\AbstractAuthentication,
    Zend\Authentication\Result;

class WrongPassword extends AbstractAuthentication
{
    public function authenticate() {
        return new Result(
            Result::FAILURE_CREDENTIAL_INVALID,
            null,
            array('Wrong Password')
        );
    }
}


