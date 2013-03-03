<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ZendTest\Test\PHPUnit\Util;

use Zend\Test\PHPUnit\Util\ModuleLoader;

class ModuleLoaderTest extends ModuleLoader
{
    public function testCanLoadModule()
    {
        require_once __DIR__ . '/../../_files/Baz/Module.php';

        $this->loadModule('Baz');

        $baz = $this->getModule('Baz');
        $this->assertTrue($baz instanceof \Baz\Module);
    }

    public function testCanLoadModuleWithPath()
    {
        $this->loadModule(array('Baz' => __DIR__ . '/../../_files/Baz'));
    }

    public function testCanLoadModules()
    {
        require_once __DIR__ . '/../../_files/Baz/Module.php';
        require_once __DIR__ . '/../../_files/modules-path/with-subdir/Foo/Module.php';

        $this->loadModules(array('Baz', 'Foo'));
    }

    public function testCanLoadModulesWithPath()
    {
        $this->loadModules(array(
            'Baz' => __DIR__ . '/../../_files/Baz',
            'Foo' => __DIR__ . '/../../_files/modules-path/with-subdir/Foo',
        ));

        $fooObject = $this->getServiceManager()->get('FooObject');
        $this->assertTrue($fooObject instanceof \stdClass);
    }

    public function testCanLoadModulesFromConfig()
    {
        $config = include __DIR__ . '/../../_files/application.config.php';
        $this->loadModulesFromConfig($config);
    }
}
