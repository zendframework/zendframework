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
 * @category   Zend_Cache
 * @package    UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

namespace ZendTest\Cache;
use Zend\Cache;

/**
 * @category   Zend_Cache
 * @package    UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ManagerTest extends \PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $this->_cache_dir = $this->mkdir();
        $this->_cache = Cache\Cache::factory(
            'Core', 'File',
            array('automatic_serialization'=>true),
            array('cache_dir'=>$this->_cache_dir)
        );
    }

    public function tearDown()
    {
        $this->rmdir();
        $this->_cache = null;
    }

    public function testSetsCacheObject()
    {
        $manager = new Cache\Manager;
        $manager->setCache('cache1', $this->_cache);
        $this->assertTrue($manager->getCache('cache1') instanceof Cache\Frontend);
    }

    public function testLazyLoadsDefaultPageCache()
    {
        $manager = new Cache\Manager;
        $manager->setTemplateOptions('pagetag',array(
            'backend' => array(
                'options' => array(
                    'cache_dir' => $this->_cache_dir
                )
            )
        ));
        $cache = $manager->getCache('page');
        $this->assertTrue($cache instanceof Cache\Frontend);
    }

    public function testCanOverrideCacheFrontendNameConfiguration()
    {
        $manager = new Cache\Manager;
        $manager->setTemplateOptions('pagetag',array(
            'backend' => array(
                'options' => array(
                    'cache_dir' => $this->_cache_dir
                )
            )
        ));
        $manager->setTemplateOptions('page', array(
            'frontend' => array(
                'name'=> 'Page'
            )
        ));
        $this->assertTrue($manager->getCache('page') instanceof Cache\Frontend\Page);
    }

    public function testCanMergeTemplateCacheOptionsFromZendConfig()
    {
        $manager = new Cache\Manager;
        $config = new \Zend\Config\Config(array(
            'backend' => array(
                'options' => array(
                    'cache_dir' => $this->_cache_dir
                )
            )
        ));
        $manager->setTemplateOptions('pagetag', $config);
        $options = $manager->getCacheTemplate('pagetag');
        $this->assertEquals($this->_cache_dir, $options['backend']['options']['cache_dir']);
    }

    public function testCanOverrideCacheBackendendNameConfiguration()
    {
        $manager = new Cache\Manager;
        $manager->setTemplateOptions('pagetag',array(
            'backend' => array(
                'options' => array(
                    'cache_dir' => $this->_cache_dir
                )
            )
        ));
        $manager->setTemplateOptions('page', array(
            'backend' => array(
                'name'=> 'File'
            )
        ));
        $this->assertTrue($manager->getCache('page')->getBackend() instanceof Cache\Backend\File);
    }

    public function testCanOverrideCacheFrontendOptionsConfiguration()
    {
        $manager = new Cache\Manager;
        $manager->setTemplateOptions('page', array(
            'frontend' => array(
                'options'=> array(
                    'lifetime' => 9999
                )
            )
        ));
        $config = $manager->getCacheTemplate('page');
        $this->assertEquals(9999, $config['frontend']['options']['lifetime']);
    }

    public function testCanOverrideCacheBackendOptionsConfiguration()
    {
        $manager = new Cache\Manager;
        $manager->setTemplateOptions('page', array(
            'backend' => array(
                'options'=> array(
                    'public_dir' => './cacheDir'
                )
            )
        ));
        $config = $manager->getCacheTemplate('page');
        $this->assertEquals('./cacheDir', $config['backend']['options']['public_dir']);
    }

    public function testSetsConfigTemplate()
    {
        $manager = new Cache\Manager;
        $config = array(
            'frontend' => array(
                'name' => 'Core',
                'options' => array(
                    'automatic_serialization' => true
                )
            ),
            'backend' => array(
                'name' => 'File',
                'options' => array(
                    'cache_dir' => '../cache',
                )
            )
        );
        $manager->setCacheTemplate('myCache', $config);
        $this->assertSame($config, $manager->getCacheTemplate('myCache'));
    }
    
    public function testSetsConfigTemplateWithoutMultipartNameNormalisation()
    {
        $manager = new Cache\Manager;
        $config = array(
            'frontend' => array(
                'name' => 'Core',
                'options' => array(
                    'automatic_serialization' => true
                )
            ),
            'backend' => array(
                'name' => 'BlackHole'
            )
        );
        $manager->setCacheTemplate('myCache', $config);
        $this->assertSame($config, $manager->getCacheTemplate('myCache'));
    }

    public function testSetsOptionsTemplateUsingZendConfig()
    {
        $manager = new Cache\Manager;
        $config = array(
            'frontend' => array(
                'name' => 'Core',
                'options' => array(
                    'automatic_serialization' => true
                )
            ),
            'backend' => array(
                'name' => 'File',
                'options' => array(
                    'cache_dir' => '../cache',
                )
            )
        );
        $manager->setCacheTemplate('myCache', new \Zend\Config\Config($config));
        $this->assertSame($config, $manager->getCacheTemplate('myCache'));
    }

    public function testConfigTemplatesDetectedAsAvailableCaches()
    {
        $manager = new Cache\Manager;
        $this->assertTrue($manager->hasCache('page'));
    }

    public function testGettingPageCacheAlsoCreatesTagCache()
    {
        $manager = new Cache\Manager;
        $tagCacheConfig = $manager->getCacheTemplate('tagCache');
        $tagCacheConfig['backend']['options']['cache_dir'] = $this->getTmpDir();
        $manager->setTemplateOptions('pagetag', $tagCacheConfig);
        $tagCache = $manager->getCache('page')->getBackend()->getOption('tag_cache');
        $this->assertTrue($tagCache instanceof Cache\Core);
    }

    // Helper Methods

    public function mkdir()
    {
        $tmp = $this->getTmpDir();
        @mkdir($tmp);
        return $tmp;
    }

    public function rmdir()
    {
        $tmpDir = $this->getTmpDir(false);
        foreach (glob("$tmpDir*") as $dirname) {
            @rmdir($dirname);
        }
    }

    public function getTmpDir($date = true)
    {
        $suffix = '';
        $tmp = sys_get_temp_dir();
        if ($date) {
            $suffix = date('mdyHis');
        }
        if (is_writeable($tmp)) {
            return $tmp . DIRECTORY_SEPARATOR . 'zend_cache_tmp_dir_' . $suffix;
        } else {
            throw new Exception("no writable tmpdir found");
        }
    }

}
