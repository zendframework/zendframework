<?php

class Zend_Crypt_Rsa_Key implements Countable
{
    
    protected $_pemString = null;

    protected $_details = array();

    protected $_opensslKeyResource = null;

    public function getOpensslKeyResource() 
    {
        return $this->_opensslKeyResource;
    }

    public function toString() 
    {
        if (!empty($this->_pemString)) {
            return $this->_pemString;
        } elseif (!empty($this->_certificateString)) {
            return $this->_certificateString;
        }
        require_once 'Zend/Crypt/Exception.php';
        throw new Zend_Crypt_Exception('No public key string representation is available');
    }

    public function __toString() 
    {
        return $this->toString();
    }

    public function count() 
    {
        return $this->_details['bits'];
    }

    public function getType() 
    {
        return $this->_details['type'];
    }
}