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
use Zend\Cache\Exception;

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage UnitTests
 * @group      Zend_Cache
 */
class ZendServerShmTest extends CommonAdapterTest
{

    public function setUp()
    {
        if (!defined('TESTS_ZEND_CACHE_ZEND_SERVER_ENABLED') || !TESTS_ZEND_CACHE_ZEND_SERVER_ENABLED) {
            $this->markTestSkipped("Skipped by TestConfiguration (TESTS_ZEND_CACHE_ZEND_SERVER_ENABLED)");
        }

        if (strtolower(PHP_SAPI) == 'cli') {
            $this->markTestSkipped('Zend Server SHM does not work in CLI environment');
            return;
        }

        if (!function_exists('zend_shm_cache_store')) {
            try {
                new Cache\Storage\Adapter\ZendServerShm();
                $this->fail("Missing expected ExtensionNotLoadedException");
            } catch (Exception\ExtensionNotLoadedException $e) {
                $this->markTestSkipped($e->getMessage());
            }
        }

        $this->_options = new Cache\Storage\Adapter\AdapterOptions();
        $this->_storage = new Cache\Storage\Adapter\ZendServerShm($this->_options);
        parent::setUp();
    }

    public function tearDown()
    {
        if (function_exists('zend_shm_cache_clear')) {
            zend_shm_cache_clear();
        }

        parent::tearDown();
    }
}
