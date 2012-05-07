<?php
namespace ZendTest\Session\TestAsset;

use Zend\EventManager\EventManagerInterface,
    Zend\Session\AbstractManager,
    Zend\Session\Configuration\ConfigurationInterface as SessionConfiguration,
    Zend\Session\SaveHandler\SaveHandlerInterface as SessionSaveHandler,
    Zend\Session\Storage\StorageInterface as SessionStorage;

class TestManager extends AbstractManager
{
    public $started = false;

    protected $configDefaultClass = 'Zend\\Session\\Configuration\\StandardConfiguration';
    protected $storageDefaultClass = 'Zend\\Session\\Storage\\ArrayStorage';

    public function start()
    {
        $this->started = true;
    }

    public function destroy()
    {
        $this->started = false;
    }

    public function stop()
    {}

    public function writeClose()
    {
        $this->started = false;
    }

    public function getName()
    {}

    public function setName($name)
    {}

    public function getId()
    {}

    public function setId($id)
    {}

    public function regenerateId()
    {}

    public function rememberMe($ttl = null)
    {}

    public function forgetMe()
    {}


    public function setValidatorChain(EventManagerInterface $chain)
    {}

    public function getValidatorChain()
    {}

    public function isValid()
    {}


    public function sessionExists()
    {}

    public function expireSessionCookie()
    {}
}
