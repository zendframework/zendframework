<?php

require_once 'PHPUnit/Framework/TestCase.php';
//require_once 'Zend/Oauth/Signature/Rsa.php';

class Zend_Oauth_Signature_RsaTest extends PHPUnit_Framework_TestCase
{

    public function testSignatureWithoutAccessSecretIsHashedWithConsumerSecret() 
    {
        $this->markTestIncomplete('Zend_Crypt_Rsa finalisation outstanding');
    }

    public function testSignatureWithAccessSecretIsHashedWithConsumerAndAccessSecret() 
    {
        $this->markTestIncomplete('Zend_Crypt_Rsa finalisation outstanding');
    }

}