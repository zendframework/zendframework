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
 * @package    Zend_Application
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Application\Resource;

use Zend\Loader\Autoloader,
    Zend\Application\Resource\CacheManager,
    Zend\Application,
    Zend\Controller\Front as FrontController,
    ZendTest\Application\TestAsset\ZfAppBootstrap;

/**
 * @category   Zend
 * @package    Zend_Application
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Application
 */
class CacheManagerTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->application = new Application\Application('testing');
        $this->bootstrap = new ZfAppBootstrap($this->application);
    }

    public function tearDown()
    {
        FrontController::getInstance()->resetInstance();
    }

    public function testInitializationCreatesCacheManagerInstance()
    {

        $resource = new CacheManager(array());
        $resource->init();
        $this->assertTrue($resource->getCachemanager() instanceof \Zend\Cache\Manager);
    }

    public function testShouldReturnCacheManagerWhenComplete()
    {
        $resource = new CacheManager(array());
        $manager = $resource->init();
        $this->assertTrue($manager instanceof \Zend\Cache\Manager);
    }

    public function testShouldMergeConfigsIfOptionsPassedForDefaultCacheTemplate()
    {
        $options = array(
            'page' => array(
                'backend' => array(
                    'options' => array(
                        'cache_dir' => '/foo'
                    )
                )
            )
        );
        $resource = new CacheManager($options);
        $manager = $resource->init();
        $cacheTemplate = $manager->getCacheTemplate('page');
        $this->assertEquals('/foo', $cacheTemplate['backend']['options']['cache_dir']);

    }

    public function testShouldCreateNewCacheTemplateIfConfigNotMatchesADefaultTemplate()
    {
        $options = array(
            'foo' => array(
                'backend' => array(
                    'options' => array(
                        'cache_dir' => '/foo'
                    )
                )
            )
        );
        $resource = new CacheManager($options);
        $manager = $resource->init();
        $cacheTemplate = $manager->getCacheTemplate('foo');
        $this->assertSame($options['foo'], $cacheTemplate);
    }

    public function testShouldNotMeddleWithFrontendOrBackendCapitalisation()
    {
        $options = array(
            'foo' => array(
                'backend' => array(
                    'name' => 'BlackHole'
                )
            )
        );
        $resource = new CacheManager($options);
        $manager = $resource->init();
        $cacheTemplate = $manager->getCacheTemplate('foo');
        $this->assertEquals('BlackHole', $cacheTemplate['backend']['name']);
    }

    public function testEmptyBackendOptionsShouldNotResultInError()
    {
        $options = array(
            'foo' => array(
                'frontend' => array(
                    'name' => 'Core',
                    'options' => array(
                        'lifetime' => 7200,
                    ),
                ),
                'backend' => array(
                    'name' => 'black.hole',
                ),
            ),
        );
        $resource = new CacheManager($options);
        $manager = $resource->init();
        $cache = $manager->getCache('foo');
        $this->assertTrue($cache instanceof \Zend\Cache\Frontend\Core);
    }

    /**
     * @group ZF-9738
     */
    public function testZendServer()
    {
        if (!function_exists('zend_disk_cache_store')) {
            $this->markTestSkipped('ZendServer is required for this test');
        }

        $options = array(
            'foo' => array(
                'frontend' => array(
                    'name' => 'Core',
                    'options' => array(
                        'lifetime' => 7200,
                    ),
                ),
                'backend' => array(
                    'name' => 'ZendServer_Disk',
                ),
            ),
        );
        $resource = new CacheManager($options);
        $manager = $resource->init();
        $cache = $manager->getCache('foo')->getBackend();
        $this->assertTrue($cache instanceof \Zend\Cache\Backend\ZendServer\Disk);
    }

    /**
     * @group ZF-9737
     */
    public function testCustomFrontendBackendNaming()
    {

//        $incPath = get_include_path();
//        set_include_path(implode(PATH_SEPARATOR, array(
//            __DIR__ . '/../../Cache/TestAsset',
//            $incPath,
//        )));
//        $options = array(
//            'zf9737' => array(
//                'frontend' => array(
//                    'name'                 => 'custom-naming',
//                    'customFrontendNaming' => false),
//                'backend' => array('name'                    => 'ZendTest\Cache\TestAsset\Backend\CustomNaming',
//                                   'customBackendNaming'     => true),
//                'frontendBackendAutoload' => true)
//        );
//
//        $resource = new CacheManager($options);
//        $manager  = $resource->init();
//        $cache    = $manager->getCache('zf9737');
//        $this->assertTrue($cache->getBackend() instanceof \ZendTest\Cache\TestAsset\Backend\CustomNaming);
//        $this->assertTrue($cache instanceof \Zend\Cache\Frontend\CustomNaming);
//        set_include_path($incPath);
    }
}
