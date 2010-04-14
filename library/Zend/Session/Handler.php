<?php

namespace Zend\Session;

use Zend\Messenger;

interface Handler
{
    public function start();
    public function destroy();
    public function stop();
    public function writeClose();

    public function getName();
    public function setName($name);
    public function getId();
    public function setId($id);
    public function regenerateId();
    public function rememberMe($ttl = null);
    public function forgetMe();

    public function setValidatorChain(Messenger\Delivery $chain);
    public function getValidatorChain();
    public function isValid();

    public function sessionExists();
    public function expireSessionCookie();

    public function setConfiguration(Configuration $config);
    public function getConfiguration();
    public function setStorage(Storage $storage);
    public function getStorage();
}
