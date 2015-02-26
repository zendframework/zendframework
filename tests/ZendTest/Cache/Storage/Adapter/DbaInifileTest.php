<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Cache\Storage\Adapter;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Cache\Storage\Adapter\Dba;

/**
 * @group      Zend_Cache
 */
class DbaInifileTest extends TestCase
{
    public function testSpecifyingInifileHandlerRaisesException()
    {
        $this->setExpectedException('Zend\Cache\Exception\ExtensionNotLoadedException', 'inifile');
        $cache = new Dba(array(
            'pathname' => sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid('zfcache_dba_') . '.inifile',
            'handler'  => 'inifile',
        ));
    }
}
