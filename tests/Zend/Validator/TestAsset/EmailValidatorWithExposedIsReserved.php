<?php
namespace ZendTest\Validator\TestAsset;

use Zend\Validator\EmailAddress;

class EmailValidatorWithExposedIsReserved extends EmailAddress
{
    public function isReserved($host)
    {
        return parent::isReserved($host);
    }
}
