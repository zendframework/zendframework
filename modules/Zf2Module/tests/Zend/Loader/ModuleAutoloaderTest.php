<?php

namespace ZendTest\Loader\ModuleAutoloaderTest;

use PHPUnit_Framework_TestCase as TestCase,
    Zend\Loader\ModuleAutoloader,
    InvalidArgumentException;

class ManagerTest extends TestCase
{
    public function testCanRegisterPathsFromConstructor()
    {
        $paths = array(__DIR__ . '/TestAsset/');
        $loader = new ModuleAutoloader($paths);
        $registeredPaths = $loader->getPaths();
        $this->assertSame($paths, $registeredPaths);
    }

    public function testPathsNormalizedWithTrailingSlash()
    {
        $paths = array(
            __DIR__ . DIRECTORY_SEPARATOR . 'TestAsset',
            __DIR__ . DIRECTORY_SEPARATOR . 'TestAsset///',
            __DIR__ . DIRECTORY_SEPARATOR . 'TestAsset\\\\',
        );
        $loader = new ModuleAutoloader($paths);
        $registeredPaths = $loader->getPaths();
        $this->assertSame(__DIR__ . DIRECTORY_SEPARATOR . 'TestAsset' . DIRECTORY_SEPARATOR, $registeredPaths[0]);
        $this->assertSame(__DIR__ . DIRECTORY_SEPARATOR . 'TestAsset' . DIRECTORY_SEPARATOR, $registeredPaths[1]);
        $this->assertSame(__DIR__ . DIRECTORY_SEPARATOR . 'TestAsset' . DIRECTORY_SEPARATOR, $registeredPaths[2]);
    }

    public function testCanAutoloadModule()
    {
        $loader = new ModuleAutoloader;
        $loader->registerPath(__DIR__ . '/TestAsset/');
        $moduleClass = $loader->autoload('FooModule\Module');
        $this->assertSame('FooModule\Module', $moduleClass);
        $module = new \FooModule\Module;
        $this->assertInstanceOf('FooModule\Module', $module);
    }

    public function testCanAutoloadSubModule()
    {
        $loader = new ModuleAutoloader;
        $loader->registerPath(__DIR__ . '/TestAsset/');
        $loader->register();
        $subModule = new \FooModule\SubModule\Module;
        $this->assertInstanceOf('FooModule\SubModule\Module', $subModule);
    }

    public function testCanAutoloadPharModules()
    {
        $loader = new ModuleAutoloader;
        $loader->registerPath(__DIR__ . '/TestAsset/');
        $loader->register();
        $this->assertSame(true, class_exists('PharModule\Module'));
        $this->assertSame(true, class_exists('PharModuleGz\Module'));
        $this->assertSame(true, class_exists('PharModuleBz2\Module'));
        $this->assertSame(true, class_exists('PharModulePharTar\Module'));
        $this->assertSame(true, class_exists('PharModulePharTarGz\Module'));
        $this->assertSame(true, class_exists('PharModulePharTarBz2\Module'));
        $this->assertSame(true, class_exists('PharModulePharZip\Module'));
        $this->assertSame(true, class_exists('PharModuleTar\Module'));
        $this->assertSame(true, class_exists('PharModuleTarGz\Module'));
        $this->assertSame(true, class_exists('PharModuleTarBz2\Module'));
        $this->assertSame(true, class_exists('PharModuleZip\Module'));
    }

    public function testProvidesFluidInterface()
    {
        $loader = new ModuleAutoloader;
        $this->assertInstanceOf('Zend\Loader\ModuleAutoloader', $loader->setOptions(array('foo')));
        $this->assertInstanceOf('Zend\Loader\ModuleAutoloader', $loader->registerPaths(array('foo')));
        $this->assertInstanceOf('Zend\Loader\ModuleAutoloader', $loader->registerPath('foo'));
    }

    public function testReturnsFalseForNonModuleClass()
    {
        $loader = new ModuleAutoloader;
        $loader->registerPath(__DIR__ . '/TestAsset/');
        $moduleClass = $loader->autoload('FooModule\NotModule');
        $this->assertSame(false, $moduleClass);
    }

    public function testReturnsFalseForNonExistantModuleClass()
    {
        $loader = new ModuleAutoloader;
        $loader->registerPath(__DIR__ . '/TestAsset/');
        $moduleClass = $loader->autoload('NonExistantModule\Module');
        $this->assertSame(false, $moduleClass);
    }


    public function testInvalidPathThrowsException()
    {
        $loader = new ModuleAutoloader;
        $this->setExpectedException('InvalidArgumentException');
        $loader->registerPath(123);
    }

    public function testInvalidPathsThrowsException()
    {
        $loader = new ModuleAutoloader;
        $this->setExpectedException('InvalidArgumentException');
        $loader->registerPaths(123);
    }
}
