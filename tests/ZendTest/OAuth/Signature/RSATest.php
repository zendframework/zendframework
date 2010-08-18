<?php

namespace ZendTest\OAuth\Signature;

use Zend\OAuth\Signature;

class RSATest extends \PHPUnit_Framework_TestCase
{

    public function testSignatureWithoutAccessSecretIsHashedWithConsumerSecret() 
    {
        $this->markTestIncomplete('Zend\\Crypt\\Rsa finalisation outstanding');
    }

    public function testSignatureWithAccessSecretIsHashedWithConsumerAndAccessSecret() 
    {
        $this->markTestIncomplete('Zend\\Crypt\\Rsa finalisation outstanding');
    }

}
