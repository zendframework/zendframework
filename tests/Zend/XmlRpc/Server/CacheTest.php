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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\XmlRpc\Server;
use Zend\XmlRpc\Server;

/**
 * Test case for Zend\XmlRpc\Server\Cache
 *
 * @category   Zend
 * @package    Zend_XmlRpc
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
