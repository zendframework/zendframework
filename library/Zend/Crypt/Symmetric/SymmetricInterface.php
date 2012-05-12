<?php

namespace Zend\Crypt\Symmetric;

interface SymmetricInterface
{
    public function encrypt($data);
    public function decrypt($data);
    public function setKey($key);
    public function getKey();
    public function getKeySize();
    public function getAlgorithm();
    public function setAlgorithm($algo);
    public function getSupportedAlgorithms();
    public function setSalt($salt);
    public function getSalt();
    public function getSaltSize();
    public function getBlockSize();
    public function setMode($mode);
    public function getMode();
    public function getSupportedModes();
}

