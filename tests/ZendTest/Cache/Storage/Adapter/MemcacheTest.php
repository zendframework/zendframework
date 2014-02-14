<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Cache\Storage\Adapter;

use Zend\Cache;

/**
 * @group      Zend_Cache
 */
class MemcacheTest extends CommonAdapterTest
{

    public function setUp()
    {
        if (!defined('TESTS_ZEND_CACHE_MEMCACHE_ENABLED') || !TESTS_ZEND_CACHE_MEMCACHE_ENABLED) {
            $this->markTestSkipped("Skipped by TestConfiguration (TESTS_ZEND_CACHE_MEMCACHE_ENABLED)");
        }

        if (!extension_loaded('memcache')) {
            $this->markTestSkipped("Memcache extension is not loaded");
        }

        $this->_options  = new Cache\Storage\Adapter\MemcacheOptions(array(
            'resource_id' => __CLASS__
        ));

        if (defined('TESTS_ZEND_CACHE_MEMCACHE_HOST') && defined('TESTS_ZEND_CACHE_MEMCACHE_PORT')) {
            $this->_options->getResourceManager()->addServers(__CLASS__, array(
                array(TESTS_ZEND_CACHE_MEMCACHE_HOST, TESTS_ZEND_CACHE_MEMCACHE_PORT)
            ));
        } elseif (defined('TESTS_ZEND_CACHE_MEMCACHE_HOST')) {
            $this->_options->getResourceManager()->addServers(__CLASS__, array(
                array(TESTS_ZEND_CACHE_MEMCACHE_HOST)
            ));
        }

        $this->_storage = new Cache\Storage\Adapter\Memcache();
        $this->_storage->setOptions($this->_options);
        $this->_storage->flush();

        parent::setUp();
    }

    public function getServersDefinitions()
    {
        $expectedServers = array(
            array('host' => '127.0.0.1', 'port' => 12345, 'weight' => 1),
            array('host' => 'localhost', 'port' => 54321, 'weight' => 2),
            array('host' => 'examp.com', 'port' => 11211, 'weight' => 3),
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
     * @dataProvider getServersDefinitions
     */
    public function testOptionSetServers($servers, $expectedServers)
    {
        $options = new Cache\Storage\Adapter\MemcacheOptions();
        $options->setServers($servers);
        $this->assertEquals($expectedServers, $options->getServers());
    }

    public function testLibOptionsSet()
    {
        $options = new Cache\Storage\Adapter\MemcacheOptions();

        $options->setLibOptions(array(
            'compress_threshold' => 100
        ));

        $this->assertEquals($options->getResourceManager()->getLibOption(
            $options->getResourceId(), 'compress_threshold'
        ), 100);

        $memcache = new Cache\Storage\Adapter\Memcache($options);
        $this->assertEquals($memcache->getOptions()->getLibOptions(), array(
            'compress_threshold' => 100
        ));
    }

    public function tearDown()
    {
        if ($this->_storage) {
            $this->_storage->flush();
        }

        parent::tearDown();
    }
}
