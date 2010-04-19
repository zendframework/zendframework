<?php

namespace Zend\Session;

use Zend\Messenger;

interface Manager
{
    public function __construct($config = null, $storage = null);

    public function getConfig();
    public function getStorage();
    
    public function sessionExists();
    public function start();
    public function destroy();
    public function writeClose();

    public function getName();
    public function setName($name);
    public function getId();
    public function setId($id);
    public function regenerateId();

    public function rememberMe($ttl = null);
    public function forgetMe();
    public function expireSessionCookie();

    public function setValidatorChain(Messenger\Delivery $chain);
    public function getValidatorChain();
    public function isValid();

}
