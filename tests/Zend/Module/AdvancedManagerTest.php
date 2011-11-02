<?php

namespace ZendTest\Module;

use PHPUnit_Framework_TestCase as TestCase,
    Zend\Loader\ModuleAutoloader,
    Zend\Module\AdvancedManager as Manager,
    Zend\Module\ManagerOptions,
    InvalidArgumentException;

class AdvancedManagerTest extends TestCase
{
    public function setUp()
    {
        $this->tmpdir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'zend_module_cache_dir';
        @mkdir($this->tmpdir);
        $this->configCache = $this->tmpdir . DIRECTORY_SEPARATOR . 'config.cache.php';
        // Store original autoloaders
        $this->loaders = spl_autoload_functions();
        if (!is_array($this->loaders)) {
            // spl_autoload_functions does not return empty array when no
            // autoloaders registered...
            $this->loaders = array();
        }

        // Store original include_path
        $this->includePath = get_include_path();

        $autoloader = new ModuleAutoloader(array(
            __DIR__ . '/TestAsset',
        ));
        $autoloader->register();
        \AutoInstallModule\Module::$RESPONSE = true;
        \AutoInstallModule\Module::$VERSION = '1.0.0';
    }

    public function tearDown()
    {
        $file = glob($this->tmpdir . DIRECTORY_SEPARATOR . '*');
        @unlink($file[0]); // change this if there's ever > 1 file 
        @rmdir($this->tmpdir);
        // Restore original autoloaders
        $loaders = spl_autoload_functions();
        if (is_array($loaders)) {
            foreach ($loaders as $loader) {
                spl_autoload_unregister($loader);
            }
        }

        foreach ($this->loaders as $loader) {
            spl_autoload_register($loader);
        }

        // Restore original include_path
        set_include_path($this->includePath);
    }

    public function testDoubleProvisionException()
    {
    	 $this->setExpectedException('RuntimeException');
    	 $options = new ManagerOptions(array(
            'enable_dependency_check' => true,
        ));
        // build the cache
        $moduleManager = new Manager(array('BarModule', 'DoubleModule'), $options);
        $moduleManager->loadModules();
        $config = $moduleManager->getMergedConfig();
        $moduleManager->getDependencies();
    }
    
    public function testGetOfDependanciesPostLoad()
    {
    	 $options = new ManagerOptions(array(
            'enable_dependency_check' => true,
        ));
        // build the cache
        $moduleManager = new Manager(array('BarModule'), $options);
        $moduleManager->loadModules();
        $dependencies = $moduleManager->getDependencies();
        $this->assertInternalType('array', $dependencies);
        $this->assertArrayHasKey('php', $dependencies);
    }

	public function testGetOfProvisionsPostLoad()
    {
    	 $options = new ManagerOptions(array(
            'enable_dependency_check' => true,
        ));
        // build the cache
        $moduleManager = new Manager(array('BarModule'), $options);
        $moduleManager->loadModules();
        $provisions = $moduleManager->getProvisions();
        $this->assertInternalType('array', $provisions);
        $this->assertArrayHasKey('BarModule', $provisions);
    }
    
    public function testResolutionOfMinimumPhpVersion()
    {
    	$this->setExpectedException('RuntimeException');
    	$options = new ManagerOptions(array(
            'enable_dependency_check' => true,
        ));
        // build the cache
        $moduleManager = new Manager(array('ImpossibleModule', 'BarModule'), $options);
        $moduleManager->loadModules();
        $dependencies = $moduleManager->getDependencies();
		
    }
    
    public function testForDissatisfactionForPhpVersion()
    {
    	$options = new ManagerOptions(array(
            'enable_dependency_check' => true,
        ));
        // build the cache
        $moduleManager = new Manager(array('ImpossibleModule', 'BooModule'), $options);
        $moduleManager->loadModules();
        $dependencies = $moduleManager->getDependencies();
        $this->assertInternalType('array', $dependencies);
        $this->assertFalse($dependencies['php']['satisfied']);
    }
    
	public function testForDissatisfactionForExtension()
    {
    	$options = new ManagerOptions(array(
            'enable_dependency_check' => true,
        ));
        // build the cache
        $moduleManager = new Manager(array('ImpossibleModule', 'BooModule'), $options);
        $moduleManager->loadModules();
        $dependencies = $moduleManager->getDependencies();
        $this->assertInternalType('array', $dependencies);
        $this->assertFalse($dependencies['php']['satisfied']);
    }
    
	public function testResolutionOnInvalidExtension()
    {
    	$this->setExpectedException('RuntimeException');
    	$options = new ManagerOptions(array(
            'enable_dependency_check' => true,
        ));
        // build the cache
        $moduleManager = new Manager(array('BooModule', 'BorModule'), $options);
        $moduleManager->loadModules();
        $dependencies = $moduleManager->getDependencies();
    }
    
	public function testResolutionOnInvalidModule()
    {
    	$this->setExpectedException('RuntimeException');
    	$options = new ManagerOptions(array(
            'enable_dependency_check' => true,
        ));
        // build the cache
        $moduleManager = new Manager(array('BafModule'), $options);
        $moduleManager->loadModules();
        $dependencies = $moduleManager->getDependencies();
    }
    
	public function testForDissatisfactionForModule()
    {
    	$options = new ManagerOptions(array(
            'enable_dependency_check' => true,
        ));
        // build the cache
        $moduleManager = new Manager(array('BamModule'), $options);
        $moduleManager->loadModules();
        $dependencies = $moduleManager->getDependencies();
        $this->assertInternalType('array', $dependencies);
        $this->assertFalse($dependencies['BooModule']['satisfied']);
    }
    
	public function testThrowsExceptionIfGetDependenciesCalledAndEnableDependencyCheckIsFalse()
    {
    	$this->setExpectedException('RuntimeException');
        $moduleManager = new Manager(array('BooModule', 'BorModule'));
        $moduleManager->loadModules();
        $moduleManager->getDependencies();
    }

	public function testThrowsExceptionIfGetProvisionsCalledAndEnableDependencyCheckIsFalse()
    {
    	$this->setExpectedException('RuntimeException');
        $moduleManager = new Manager(array('BooModule', 'BorModule'));
        $moduleManager->loadModules();
        $moduleManager->getProvisions();
    }

    public function testAutoInstallationAndUpgradeUpdatesManifest()
    {
    	$options = new ManagerOptions(array(
            'enable_auto_installation' => true,
            'auto_install_whitelist' => array('AutoInstallModule'),
            'manifest_dir' => $this->tmpdir,
        ));
        $moduleManager = new Manager(array('AutoInstallModule'), $options);
        $moduleManager->loadModules();
        $manifest = include $this->tmpdir . DIRECTORY_SEPARATOR . '/manifest.php';
        $this->assertEquals($manifest['AutoInstallModule']['version'], '1.0.0');
        // Now test a fake upgrade
        \AutoInstallModule\Module::$VERSION = '1.0.1';
        $moduleManager = new Manager(array('AutoInstallModule'), $options);
        $moduleManager->loadModules();
        $manifest = include $this->tmpdir . DIRECTORY_SEPARATOR . '/manifest.php';
        $this->assertEquals($manifest['AutoInstallModule']['version'], '1.0.1');
    }

    public function testAutoInstallationThrowsExceptionOnFailedInstall()
    {
    	$this->setExpectedException('RuntimeException');
    	$options = new ManagerOptions(array(
            'enable_auto_installation' => true,
            'auto_install_whitelist' => array('AutoInstallModule'),
            'manifest_dir' => $this->tmpdir,
        ));
        \AutoInstallModule\Module::$RESPONSE = false;
        $moduleManager = new Manager(array('AutoInstallModule'), $options);
        $moduleManager->loadModules();
    }

    public function testAutoUpgradeThrowsExceptionOnFailedUpgrade()
    {
    	$this->setExpectedException('RuntimeException');
    	$options = new ManagerOptions(array(
            'enable_auto_installation' => true,
            'auto_install_whitelist' => array('AutoInstallModule'),
            'manifest_dir' => $this->tmpdir,
        ));
        $moduleManager = new Manager(array('AutoInstallModule'), $options);
        \AutoInstallModule\Module::$RESPONSE = false;
        \AutoInstallModule\Module::$VERSION = '1.0.1';
        $moduleManager = new Manager(array('AutoInstallModule'), $options);
        $moduleManager->loadModules();
    }
}
