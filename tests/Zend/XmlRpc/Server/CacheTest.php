<?php
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
 * @package Zend_XmlRpc
 * @subpackage UnitTests
 * @version $Id$
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
