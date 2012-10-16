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
            $this->_options->setServers(array(array(TESTS_ZEND_CACHE_MEMCACHED_HOST, TESTS_ZEND_CACHE_MEMCACHED_PORT)));
        } elseif (defined('TESTS_ZEND_CACHE_MEMCACHED_HOST')) {
            $this->_options->setServers(TESTS_ZEND_CACHE_MEMCACHED_HOST);
        }

        $this->_storage = new Cache\Storage\Adapter\Memcached();
        $this->_storage->setOptions($this->_options);

        parent::setUp();
    }

    public function getServersDefinitions()
    {
        $expectedServers = array(
            array('host' => '127.0.0.1', 'port' => 12345, 'weight' => 1),
            array('host' => 'localhost', 'port' => 54321, 'weight' => 2),
            array('host' => 'examp.com', 'port' => 11211, 'weight' => 1),
        );

        return array(
            // servers as array list
            array(
                array(
                    array('127.0.0.1', 12345, 1),
                    array('localhost', '54321', '2'),
                    array('examp.com'),
                ),
                $expectedServers,
            ),

            // servers as array assoc
            array(
                array(
                    array('127.0.0.1', 12345, 1),
                    array('localhost', '54321', '2'),
                    array('examp.com'),
                ),
                $expectedServers,
            ),

            // servers as string list
            array(
                array(
                    '127.0.0.1:12345?weight=1',
                    'localhost:54321?weight=2',
                    'examp.com',
                ),
                $expectedServers,
            ),

            // servers as string
            array(
                '127.0.0.1:12345?weight=1, localhost:54321?weight=2,tcp://examp.com',
                $expectedServers,
            ),
        );
    }

    /**
     *
     * @dataProvider getServersDefinitions
     */
    public function testOptionServers($servers, $expectedServers)
    {
        $options = new Cache\Storage\Adapter\MemcachedOptions();
        $options->setServers($servers);
        $this->assertEquals($expectedServers, $options->getServers());
    }

    public function testOptionLibOptions()
    {
        $options = new Cache\Storage\Adapter\MemcachedOptions();

        $options->setLibOptions(array(
            'COMPRESSION' => false
        ));

        $this->assertEquals($options->getLibOption(\Memcached::OPT_COMPRESSION), false);
    }

    public function tearDown()
    {
        if ($this->_storage) {
            $this->_storage->flush();
        }

        parent::tearDown();
    }
}
