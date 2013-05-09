<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Session
 */

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
