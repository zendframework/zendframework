<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
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
class XCacheTest extends CommonAdapterTest
{

    protected $backupServerArray;

    public function setUp()
    {
        if (!defined('TESTS_ZEND_CACHE_XCACHE_ENABLED') || !TESTS_ZEND_CACHE_XCACHE_ENABLED) {
            $this->markTestSkipped("Skipped by TestConfiguration (TESTS_ZEND_CACHE_XCACHE_ENABLED)");
        }

        if (!extension_loaded('xcache')) {
            try {
                new Cache\Storage\Adapter\XCache();
                $this->fail("Expected exception Zend\Cache\Exception\ExtensionNotLoadedException");
            } catch (Cache\Exception\ExtensionNotLoadedException $e) {
                $this->markTestSkipped($e->getMessage());
            }
        }

        if (PHP_SAPI == 'cli') {
            try {
                new Cache\Storage\Adapter\XCache();
                $this->fail("Expected exception Zend\Cache\Exception\ExtensionNotLoadedException");
            } catch (Cache\Exception\ExtensionNotLoadedException $e) {
                $this->markTestSkipped($e->getMessage());
            }
        }

        if ((int)ini_get('xcache.var_size') <= 0) {
            try {
                new Cache\Storage\Adapter\XCache();
                $this->fail("Expected exception Zend\Cache\Exception\ExtensionNotLoadedException");
            } catch (Cache\Exception\ExtensionNotLoadedException $e) {
                $this->markTestSkipped($e->getMessage());
            }
        }

        $this->_options = new Cache\Storage\Adapter\XCacheOptions(array(
            'admin_auth' => defined('TESTS_ZEND_CACHE_XCACHE_ADMIN_AUTH') ? TESTS_ZEND_CACHE_XCACHE_ADMIN_AUTH : false,
            'admin_user' => defined('TESTS_ZEND_CACHE_XCACHE_ADMIN_USER') ? TESTS_ZEND_CACHE_XCACHE_ADMIN_USER : '',
            'admin_pass' => defined('TESTS_ZEND_CACHE_XCACHE_ADMIN_PASS') ? TESTS_ZEND_CACHE_XCACHE_ADMIN_PASS : '',
        ));
        $this->_storage = new Cache\Storage\Adapter\XCache();
        $this->_storage->setOptions($this->_options);

        // dfsdfdsfsdf
        $this->backupServerArray = $_SERVER;

        parent::setUp();
    }

    public function tearDown()
    {
        if ($this->_storage) {
            $this->_storage->flush();
        }

        parent::tearDown();
    }
}
