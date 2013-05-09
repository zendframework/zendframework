<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Json
 */

namespace ZendTest\Json\Server;

use Zend\Json\Server;

/**
 * Test class for Zend_JSON_Server_Cache
 *
 * @category   Zend
 * @package    Zend_JSON_Server
 * @subpackage UnitTests
 * @group      Zend_JSON
 * @group      Zend_JSON_Server
 */
class CacheTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        $this->server = new Server\Server();
        $this->server->setClass('ZendTest\Json\Server\Foo', 'foo');
        $this->cacheFile = tempnam(sys_get_temp_dir(), 'zjs');

        // if (!is_writeable(__DIR__)) {
        if (!is_writeable($this->cacheFile)) {
            $this->markTestSkipped('Cannot write test caches due to permissions');
        }

        if (file_exists($this->cacheFile)) {
            unlink($this->cacheFile);
        }
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    public function tearDown()
    {
        if (file_exists($this->cacheFile)) {
            unlink($this->cacheFile);
        }
    }

    public function testRetrievingSmdCacheShouldReturnFalseIfCacheDoesNotExist()
    {
        $this->assertFalse(Server\Cache::getSmd($this->cacheFile));
    }

    public function testSavingSmdCacheShouldReturnTrueOnSuccess()
    {
        $this->assertTrue(Server\Cache::saveSmd($this->cacheFile, $this->server));
    }

    public function testSavedCacheShouldMatchGeneratedCache()
    {
        $this->testSavingSmdCacheShouldReturnTrueOnSuccess();
        $json = $this->server->getServiceMap()->toJSON();
        $test = Server\Cache::getSmd($this->cacheFile);
        $this->assertSame($json, $test);
    }

    public function testDeletingSmdShouldReturnFalseOnFailure()
    {
        $this->assertFalse(Server\Cache::deleteSmd($this->cacheFile));
    }

    public function testDeletingSmdShouldReturnTrueOnSuccess()
    {
        $this->testSavingSmdCacheShouldReturnTrueOnSuccess();
        $this->assertTrue(Server\Cache::deleteSmd($this->cacheFile));
    }
}

/**
 * Class for testing JSON-RPC server caching
 */
class Foo
{
    /**
     * Bar
     *
     * @param  bool $one
     * @param  string $two
     * @param  mixed $three
     * @return array
     */
    public function bar($one, $two = 'two', $three = null)
    {
        return array($one, $two, $three);
    }

    /**
     * Baz
     *
     * @return void
     */
    public function baz()
    {
        throw new \Exception('application error');
    }
}
