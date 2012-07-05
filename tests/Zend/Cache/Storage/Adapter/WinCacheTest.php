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
 * @package    Zend_Cache
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Cache\Storage\Adapter;
use \Zend\Cache,
    \Zend\Cache\Exception;

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Cache
 */
class WinCacheTest extends CommonAdapterTest
{

    public function setUp()
    {
        if (!defined('TESTS_ZEND_CACHE_WINCACHE_ENABLED') || !TESTS_ZEND_CACHE_WINCACHE_ENABLED) {
            $this->markTestSkipped("Skipped by TestConfiguration (TESTS_ZEND_CACHE_WINCACHE_ENABLED)");
        }

        if (!extension_loaded('wincache')) {
            $this->markTestSkipped("WinCache extension is not loaded");
        }

        $enabled = ini_get('wincache.ucenabled');
        if (PHP_SAPI == 'cli') {
            $enabled = $enabled && (bool) ini_get('wincache.enablecli');
        }

        if (!$enabled) {
            throw new Exception\ExtensionNotLoadedException(
                "WinCache is disabled - see 'wincache.ucenabled' and 'wincache.enablecli'"
            );
        }

        $this->_options = new Cache\Storage\Adapter\WinCacheOptions();
        $this->_storage = new Cache\Storage\Adapter\WinCache();
        $this->_storage->setOptions($this->_options);

        parent::setUp();
    }

    public function tearDown()
    {
        if (function_exists('wincache_ucache_clear')) {
            wincache_ucache_clear();
        }

        parent::tearDown();
    }
}
