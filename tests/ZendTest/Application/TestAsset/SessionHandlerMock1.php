<?php

namespace ZendTest\Application\TestAsset;

use Zend\Session\SaveHandler,
    Zend\Session\Manager;

class SessionHandlerMock1 implements SaveHandler
{
    public $manager;

    public function setManager(Manager $manager)
    {
        $this->manager = $manager;
    }

    public function getManager()
    {
        return $this->manager;
    }

    public function open($save_path, $name)
    {
    }

    public function close()
    {
    }

    public function read($id)
    {
    }

    public function write($id, $data)
    {
    }

    public function destroy($id)
    {
    }

    public function gc($maxlifetime)
    {
    }
}
