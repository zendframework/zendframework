<?php

require_once 'Zend/Crypt/Rsa/Key.php';

class Zend_Crypt_Rsa_Key_Private extends Zend_Crypt_Rsa_Key
{

    protected $_publicKey = null;

    public function __construct($pemString, $passPhrase = null)
    {
        $this->_pemString = $pemString;
        $this->_parse($passPhrase);
    }

    protected function _parse($passPhrase)
    {
        $result = openssl_get_privatekey($this->_pemString, $passPhrase);
        if (!$result) {
            require_once 'Zend/Crypt/Exception.php';
            throw new Zend_Crypt_Exception('Unable to load private key');
        }
        $this->_opensslKeyResource = $result;
        $this->_details = openssl_pkey_get_details($this->_opensslKeyResource);
    }

    public function getPublicKey()
    {
        if (is_null($this->_publicKey)) {
            $this->_publicKey = new Zend_Crypt_Rsa_Key_Public($this->_details['key']);
        }
        return $this->_publicKey;
    }

}