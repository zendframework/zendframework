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
use Zend\Cache;

/**
 * @category   Zend
 * @package    Zend_Cache
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Cache
 */
class ApcTest extends CommonAdapterTest
{

    /**
     * Restore 'apc.use_request_time'
     *
     * @var mixed
     */
    protected $iniUseRequestTime;

    public function setUp()
    {
        if (!defined('TESTS_ZEND_CACHE_APC_ENABLED') || !TESTS_ZEND_CACHE_APC_ENABLED) {
            $this->markTestSkipped("Skipped by TestConfiguration (TESTS_ZEND_CACHE_APC_ENABLED)");
        }

        if (version_compare('3.1.6', phpversion('apc')) > 0) {
            try {
                new Cache\Storage\Adapter\Apc();
                $this->fail("Expected exception Zend\Cache\Exception\ExtensionNotLoadedException");
            } catch (Cache\Exception\ExtensionNotLoadedException $e) {
                $this->markTestSkipped("Missing ext/apc >= 3.1.6");
            }
        }

        $enabled = (bool) ini_get('apc.enabled');
        if (PHP_SAPI == 'cli') {
            $enabled = $enabled && (bool) ini_get('apc.enable_cli');
        }

        if (!$enabled) {
            try {
                new Cache\Storage\Adapter\Apc();
                $this->fail("Expected exception Zend\Cache\Exception\ExtensionNotLoadedException");
            } catch (Cache\Exception\ExtensionNotLoadedException $e) {
                $this->markTestSkipped("ext/apc is disabled - see 'apc.enabled' and 'apc.enable_cli'");
            }
        }

        // needed on test expirations
        $this->iniUseRequestTime = ini_get('apc.use_request_time');
        ini_set('apc.use_request_time', 0);

        $this->_options = new Cache\Storage\Adapter\ApcOptions();
        $this->_storage = new Cache\Storage\Adapter\Apc();
        $this->_storage->setOptions($this->_options);

        parent::setUp();
    }

    public function tearDown()
    {
        if (function_exists('apc_clear_cache')) {
            apc_clear_cache('user');
        }

        // reset ini configurations
        ini_set('apc.use_request_time', $this->iniUseRequestTime);

        parent::tearDown();
    }
}
