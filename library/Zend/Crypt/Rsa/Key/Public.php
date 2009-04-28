<?php

require_once 'Zend/Crypt/Rsa/Key.php';

class Zend_Crypt_Rsa_Key_Public extends Zend_Crypt_Rsa_Key
{

    protected $_certificateString = null;

    public function __construct($string) 
    {
        $this->_parse($string);
    }

    protected function _parse($string) 
    {
        if (preg_match("/^-----BEGIN CERTIFICATE-----/", $string)) {
            $this->_certificateString = $string;
        } else {
            $this->_pemString = $string;
        }
        $result = openssl_get_publickey($string);
        if (!$result) {
            require_once 'Zend/Crypt/Exception.php';
            throw new Zend_Crypt_Exception('Unable to load public key');
        }
        //openssl_pkey_export($result, $public);
        //$this->_pemString = $public;
        $this->_opensslKeyResource = $result;
        $this->_details = openssl_pkey_get_details($this->_opensslKeyResource);
    }

    public function getCertificate() 
    {
        return $this->_certificateString;
    }

}