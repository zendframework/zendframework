<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Cache
 */

namespace ZendTest\Cache\Storage\Adapter;

use Zend\Cache;

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage UnitTests
 * @group      Zend_Cache
 */
class MemcachedTest extends CommonAdapterTest
{

    public function setUp()
    {
        if (!defined('TESTS_ZEND_CACHE_MEMCACHED_ENABLED') || !TESTS_ZEND_CACHE_MEMCACHED_ENABLED) {
            $this->markTestSkipped("Skipped by TestConfiguration (TESTS_ZEND_CACHE_MEMCACHED_ENABLED)");
        }

        if (!extension_loaded('memcached')) {
            $this->markTestSkipped("Memcached extension is not loaded");
        }

        $this->_options = new Cache\Storage\Adapter\MemcachedOptions();
        if (defined('TESTS_ZEND_CACHE_MEMCACHED_HOST') && defined('TESTS_ZEND_CACHE_MEMCACHED_PORT')) {
            $this->_options->addServer(TESTS_ZEND_CACHE_MEMCACHED_HOST, TESTS_ZEND_CACHE_MEMCACHED_PORT);
        } elseif (defined('TESTS_ZEND_CACHE_MEMCACHED_HOST')) {
            $this->_options->addServer(TESTS_ZEND_CACHE_MEMCACHED_HOST);
        }

        $this->_storage = new Cache\Storage\Adapter\Memcached($this->_options);

        parent::setUp();
    }

    public function testOptionsAddServer()
    {
        $options = new Cache\Storage\Adapter\MemcachedOptions();
        $options->addServer('127.0.0.1', 11211);
        $options->addServer('localhost');
        $options->addServer('domain.com', 11215);

        $servers = array(
            array('127.0.0.1', 11211),
            array('localhost', 11211),
            array('domain.com', 11215),
        );

        $this->assertEquals($options->getServers(), $servers);
        $memcached = new Cache\Storage\Adapter\Memcached($options);
        $this->assertEquals($memcached->getOptions()->getServers(), $servers);
    }

    public function testOptionsSetServers()
    {
        $options = new Cache\Storage\Adapter\MemcachedOptions();
        $servers = array(
            array('127.0.0.1', 12345),
            array('localhost', 54321),
            array('domain.com')
        );

        $options->setServers($servers);
        $servers[2][1] = 11211;
        $this->assertEquals($options->getServers(), $servers);

        $memcached = new Cache\Storage\Adapter\Memcached($options);
        $this->assertEquals($memcached->getOptions()->getServers(), $servers);
    }

    public function testLibOptionsSet()
    {
        $options = new Cache\Storage\Adapter\MemcachedOptions();

        $options->setLibOptions(array(
            'COMPRESSION' => false
        ));

        $this->assertEquals($options->getLibOption(\Memcached::OPT_COMPRESSION), false);

        $memcached = new Cache\Storage\Adapter\Memcached($options);
        $this->assertEquals($memcached->getOptions()->getLibOptions(), array(
            \Memcached::OPT_COMPRESSION => false
        ));
    }

    public function testNoOptionsSetsDefaultServer()
    {
        $memcached = new Cache\Storage\Adapter\Memcached();

        $this->assertEquals($memcached->getOptions()->getServers(), array(array('127.0.0.1', 11211)));
    }

    public function tearDown()
    {
        if ($this->_storage) {
            $this->_storage->flush();
        }

        parent::tearDown();
    }
}
