<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
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

        if (version_compare('2.0.0', phpversion('memcache')) > 0) {
            try {
                new Cache\Storage\Adapter\Memcache();
                $this->fail("Expected exception Zend\Cache\Exception\ExtensionNotLoadedException");
            } catch (Cache\Exception\ExtensionNotLoadedException $e) {
                $this->markTestSkipped("Missing ext/memcache version >= 2.0.0");
            }
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

    /**
     * Data provider to test valid server info
     *
     * Returns an array of the following structure:
     * array(array(
     *     <array|string server options>,
     *     <array expected normalized servers>,
     * )[, ...])
     *
     * @return array
     */
    public function getServersDefinitions()
    {
        $expectedServers = array(
            array('host' => '127.0.0.1', 'port' => 12345, 'weight' => 1, 'status' => true),
            array('host' => 'localhost', 'port' => 54321, 'weight' => 2, 'status' => true),
            array('host' => 'examp.com', 'port' => 11211, 'status' => true),
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
     * @param mixed $servers
     * @param array $expectedServers
     */
    public function testOptionSetServers($servers, $expectedServers)
    {
        $options = new Cache\Storage\Adapter\MemcacheOptions();
        $options->setServers($servers);
        $this->assertEquals($expectedServers, $options->getServers());
    }

    public function testCompressThresholdOptions()
    {
        $threshold = 100;
        $minSavings = 0.2;

        $options = new Cache\Storage\Adapter\MemcacheOptions();
        $options->setAutoCompressThreshold($threshold);
        $options->setAutoCompressMinSavings($minSavings);
        $this->assertEquals(
            $threshold, $options->getResourceManager()->getAutoCompressThreshold($options->getResourceId())
        );
        $this->assertEquals(
            $minSavings, $options->getResourceManager()->getAutoCompressMinSavings($options->getResourceId())
        );

        $memcache = new Cache\Storage\Adapter\Memcache($options);
        $this->assertEquals($memcache->getOptions()->getAutoCompressThreshold(), $threshold);
        $this->assertEquals($memcache->getOptions()->getAutoCompressMinSavings(), $minSavings);
    }

    public function tearDown()
    {
        if ($this->_storage) {
            $this->_storage->flush();
        }

        parent::tearDown();
    }
}
