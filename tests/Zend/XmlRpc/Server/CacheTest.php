<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_XmlRpc
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version $Id$
 */

// Call Zend_XmlRpc_Server_CacheTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_XmlRpc_Server_CacheTest::main");
}

require_once dirname(__FILE__) . '/../../../TestHelper.php';
require_once 'Zend/XmlRpc/Server.php';
require_once 'Zend/XmlRpc/Server/Cache.php';

/**
 * Test case for Zend_XmlRpc_Server_Cache
 *
 * @category   Zend
 * @package    Zend_XmlRpc
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_XmlRpc
 */
class Zend_XmlRpc_Server_CacheTest extends PHPUnit_Framework_TestCase 
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_XmlRpc_Server_CacheTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Zend_XmlRpc_Server object
     * @var Zend_XmlRpc_Server
     */
    protected $_server;

    /**
     * Local file for caching
     * @var string 
     */
    protected $_file;

    /**
     * Setup environment
     */
    public function setUp() 
    {
        $this->_file = realpath(dirname(__FILE__)) . '/xmlrpc.cache';
        $this->_server = new Zend_XmlRpc_Server();
        $this->_server->setClass('Zend_XmlRpc_Server_Cache', 'cache');
    }

    /**
     * Teardown environment
     */
    public function tearDown() 
    {
        if (file_exists($this->_file)) {
            unlink($this->_file);
        }
        unset($this->_server);
    }

    /**
     * Tests functionality of both get() and save()
     */
    public function testGetSave()
    {
        if (!is_writeable('./')) {
            $this->markTestIncomplete('Directory no writable');
        }

        $this->assertTrue(Zend_XmlRpc_Server_Cache::save($this->_file, $this->_server));
        $expected = $this->_server->listMethods();
        $server = new Zend_XmlRpc_Server();
        $this->assertTrue(Zend_XmlRpc_Server_Cache::get($this->_file, $server));
        $actual = $server->listMethods();

        $this->assertSame($expected, $actual);
    }

    /**
     * Zend_XmlRpc_Server_Cache::delete() test
     */
    public function testDelete()
    {
        if (!is_writeable('./')) {
            $this->markTestIncomplete('Directory no writable');
        }

        $this->assertTrue(Zend_XmlRpc_Server_Cache::save($this->_file, $this->_server));
        $this->assertTrue(Zend_XmlRpc_Server_Cache::delete($this->_file));
    }

    public function testShouldReturnFalseWithInvalidCache()
    {
        if (!is_writeable('./')) {
            $this->markTestIncomplete('Directory no writable');
        }

        file_put_contents($this->_file, 'blahblahblah');
        $server = new Zend_XmlRpc_Server();
        $this->assertFalse(Zend_XmlRpc_Server_Cache::get($this->_file, $server));
    }
}

// Call Zend_XmlRpc_Server_CacheTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_XmlRpc_Server_CacheTest::main") {
    Zend_XmlRpc_Server_CacheTest::main();
}
