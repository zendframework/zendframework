<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_XmlRpc
 */

namespace ZendTest\XmlRpc\Server;

use Zend\XmlRpc\Server;

/**
 * @category   Zend
 * @package    Zend_XmlRpc
 * @subpackage UnitTests
 * @group      Zend_XmlRpc
 */
class CacheTest extends \PHPUnit_Framework_TestCase
{
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
        $this->_file = realpath(__DIR__) . '/xmlrpc.cache';
        $this->_server = new Server();
        $this->_server->setClass('Zend\\XmlRpc\\Server\\Cache', 'cache');
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

        $this->assertTrue(Server\Cache::save($this->_file, $this->_server));
        $expected = $this->_server->listMethods();
        $server = new Server();
        $this->assertTrue(Server\Cache::get($this->_file, $server));
        $actual = $server->listMethods();

        $this->assertSame($expected, $actual);
    }

    /**
     * Zend\XmlRpc\Server\Cache::delete() test
     */
    public function testDelete()
    {
        if (!is_writeable('./')) {
            $this->markTestIncomplete('Directory no writable');
        }

        $this->assertTrue(Server\Cache::save($this->_file, $this->_server));
        $this->assertTrue(Server\Cache::delete($this->_file));
    }

    public function testShouldReturnFalseWithInvalidCache()
    {
        if (!is_writeable('./')) {
            $this->markTestIncomplete('Directory no writable');
        }

        file_put_contents($this->_file, 'blahblahblah');
        $server = new Server();
        $this->assertFalse(Server\Cache::get($this->_file, $server));
    }
}
