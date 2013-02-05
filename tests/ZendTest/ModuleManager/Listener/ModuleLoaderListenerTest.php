<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_ModuleManager
 */

namespace ZendTest\ModuleManager\Listener;

use ArrayObject;
use InvalidArgumentException;
use PHPUnit_Framework_TestCase as TestCase;
use Zend\ModuleManager\Listener\ModuleResolverListener;
use Zend\ModuleManager\Listener\ModuleLoaderListener;
use Zend\ModuleManager\Listener\ListenerOptions;
use Zend\ModuleManager\ModuleManager;
use Zend\ModuleManager\ModuleEvent;

class ModuleLoaderListenerTest extends TestCase
{
    public function setUp()
    {
        $this->tmpdir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'zend_module_cache_dir';
        @mkdir($this->tmpdir);

        $this->moduleManager = new ModuleManager(array());
        $this->moduleManager->getEventManager()->attach('loadModule.resolve', new ModuleResolverListener, 1000);
    }

    public function tearDown()
    {
        $file = glob($this->tmpdir . DIRECTORY_SEPARATOR . '*');
        @unlink($file[0]); // change this if there's ever > 1 file
        @rmdir($this->tmpdir);
    }

    public function testModuleLoaderListenerFunctionsAsAggregateListenerEnabledCache()
    {
        $options = new ListenerOptions(array(
            'cache_dir'                => $this->tmpdir,
            'module_map_cache_enabled' => true,
            'module_map_cache_key'     => 'foo',
        ));

        $moduleLoaderListener = new ModuleLoaderListener($options);

        $moduleManager = $this->moduleManager;
        $this->assertEquals(1, count($moduleManager->getEventManager()->getListeners(ModuleEvent::EVENT_LOAD_MODULES)));
        $this->assertEquals(0, count($moduleManager->getEventManager()->getListeners(ModuleEvent::EVENT_LOAD_MODULES_POST)));

        $moduleLoaderListener->attach($moduleManager->getEventManager());
        $this->assertEquals(2, count($moduleManager->getEventManager()->getListeners(ModuleEvent::EVENT_LOAD_MODULES)));
        $this->assertEquals(1, count($moduleManager->getEventManager()->getListeners(ModuleEvent::EVENT_LOAD_MODULES_POST)));
    }

    public function testModuleLoaderListenerFunctionsAsAggregateListenerDisabledCache()
    {
        $options = new ListenerOptions(array(
            'cache_dir' => $this->tmpdir,
        ));

        $moduleLoaderListener = new ModuleLoaderListener($options);

        $moduleManager = $this->moduleManager;
        $this->assertEquals(1, count($moduleManager->getEventManager()->getListeners(ModuleEvent::EVENT_LOAD_MODULES)));
        $this->assertEquals(0, count($moduleManager->getEventManager()->getListeners(ModuleEvent::EVENT_LOAD_MODULES_POST)));

        $moduleLoaderListener->attach($moduleManager->getEventManager());
        $this->assertEquals(2, count($moduleManager->getEventManager()->getListeners(ModuleEvent::EVENT_LOAD_MODULES)));
        $this->assertEquals(0, count($moduleManager->getEventManager()->getListeners(ModuleEvent::EVENT_LOAD_MODULES_POST)));
    }

    public function testModuleLoaderListenerFunctionsAsAggregateListenerHasCache()
    {
        $options = new ListenerOptions(array(
            'cache_dir'                => $this->tmpdir,
            'module_map_cache_key'     => 'foo',
            'module_map_cache_enabled' => true,
        ));

        file_put_contents($options->getModuleMapCacheFile(), '<?php return array();');

        $moduleLoaderListener = new ModuleLoaderListener($options);

        $moduleManager = $this->moduleManager;
        $this->assertEquals(1, count($moduleManager->getEventManager()->getListeners(ModuleEvent::EVENT_LOAD_MODULES)));
        $this->assertEquals(0, count($moduleManager->getEventManager()->getListeners(ModuleEvent::EVENT_LOAD_MODULES_POST)));

        $moduleLoaderListener->attach($moduleManager->getEventManager());
        $this->assertEquals(2, count($moduleManager->getEventManager()->getListeners(ModuleEvent::EVENT_LOAD_MODULES)));
        $this->assertEquals(0, count($moduleManager->getEventManager()->getListeners(ModuleEvent::EVENT_LOAD_MODULES_POST)));
    }
}
