<?php

namespace ZendTest\AMF\TestAsset\Authentication;

use Zend\AMF\AbstractAuthentication,
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


