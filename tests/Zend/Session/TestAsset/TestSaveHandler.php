<?php
namespace ZendTest\Session\TestAsset;

use Zend\Session\SaveHandler\SaveHandlerInterface as SaveHandler;

class TestSaveHandler implements SaveHandler
{
    public function open($save_path, $name)
    {}

    public function close()
    {}

    public function read($id)
    {}

    public function write($id, $data)
    {}

    public function destroy($id)
    {}

    public function gc($maxlifetime)
    {}
}
