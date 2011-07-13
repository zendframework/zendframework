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
 * @package    Zend_Tool
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Tool\Framework\System\Provider;
use Zend\Loader\StandardAutoloader,
    Zend\Tool\Framework\System\Provider\Config as ProviderConfig,
    Zend\Tool\Framework\Client\Config as ClientConfig,
    Zend\Tool\Framework\Client\Console\Console as ConsoleClient,
    Zend\Tool\Framework\Registry\FrameworkRegistry,
    Zend\Tool\Framework\Exception\RuntimeException;

/**
 * @category   Zend
 * @package    Zend_Tool
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 *
 * @group Zend_Tool
 * @group Zend_Tool_Framework
 * @group Zend_Tool_Framework_System_Provider
 */
class ConfigTest extends AbstractTest
{
    /**
     * @var Zend\Tool\Framework\Provider\Config
     */
    private $_configProvider;

    public function setUp()
    {
        parent::setUp();

        $this->_configProvider = new ProviderConfig;
        $this->_configProvider->setRegistry($this->_registry);

        // Store original autoloaders
        $this->loaders = spl_autoload_functions();
        if (!is_array($this->loaders)) {
            // spl_autoload_functions does not return empty array when no
            // autoloaders registered...
            $this->loaders = array();
        }

        // Store original include_path
        $this->includePath = get_include_path();
    }

    public function tearDown()
    {
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

    public function testAppendResponseWhenCallEnableAction()
    {
        $config = $this->_configProvider;
        $config->enable();
        $this->assertRegExp('/.*\senable\s.*provider.*/', $this->_response->getContent());
        // Use either "zf enable config.provider" or "zf enable config.manifest
    }

    public function testAppendResponseWhenCallDisableAction()
    {
        $config = $this->_configProvider;
        $config->disable();
        $this->assertRegExp('/.*\sdisable\s.*provider.*/', $this->_response->getContent());
        // Use either "zf disable config.provider" or "zf enable config.manifest
    }

    public function testEnableAndDisableProvider()
    {
        $ini = __DIR__. '/_files/enable.ini';
        file_put_contents($ini, '');

        $clientConfig = new ClientConfig;
        $clientConfig->setConfigFilePath($ini);
        $this->_registry->setConfig($clientConfig);
        $config = $this->_configProvider;

        $providerName = 'Zend\\Tool\Framework\\System\\Provider\\Version';

        $config->enableProvider($providerName);
        $configs = $clientConfig->basicloader->classes;
        $this->assertEquals($providerName, $configs->current());

        try {
            $config->enableProvider($providerName);
            $this->fail('RuntimeException was expected but not thrown');
        } catch (RuntimeException $re ) {
        }

        $config->disableProvider($providerName);
        $this->assertEmpty($configs->current());
    }

    public function testLoadUserConfigIfExists()
    {
        $ref = new \ReflectionObject($this->_configProvider);
        $method = $ref->getMethod('_loadUserConfigIfExists');
        $method->setAccessible(true);

        $method->invoke($this->_configProvider);
        $this->assertNotEmpty($this->_response->getContent());

        $this->_response->setContent('');

        $clientConfig = new ClientConfig;
        $clientConfig->setConfigFilePath(__DIR__.'/_files/'.'empty.ini');
        $this->_registry->setConfig($clientConfig);
        $method->invoke($this->_configProvider);
        $this->assertEmpty($this->_response->getContent());
    }
    
    /**
     * borrowed from Zend\Loader\StandardAutoloader
     */
    public function testCanActAsFallbackAutoloader()
    {
        $loader = new StandardAutoloader();
        $loader->setFallbackAutoloader(true);
        set_include_path(__DIR__ . '/TestAsset/' . PATH_SEPARATOR . $this->includePath);
        $loader->autoload('TestNamespace\TestProvider');
        $loader->autoload('TestNamespace\TestInvalidProvider');

        //initialize ini
        $ini = __DIR__. '/_files/enable.ini';
        file_put_contents($ini, '');

        $clientConfig = new ClientConfig;
        $clientConfig->setConfigFilePath($ini);
        $this->_registry->setConfig($clientConfig);
        $config = $this->_configProvider;

        // Test enable provider 
        try {
            $config->enableProvider('TestNamespace\TestInvalidProvider');
            $this->fail('RuntimeException was expected but not thrown');
        } catch (RuntimeException $re) {
        }

        $config->enableProvider('TestNamespace\TestProvider');
        $configs = $clientConfig->basicloader->classes;
        $this->assertEquals('TestNamespace\TestProvider', $configs->current());
        
        // Test disable provider
        try {
            $config->disableProvider('TestNamespace\TestInvalidProvider');
            $this->fail('RuntimeException was expected but not thrown');
        } catch (RuntimeException $re) {
        }

        $config->disableProvider('TestNamespace\TestProvider');
        $this->assertEmpty($configs->current(), 'No config setting will exists');

        /**
         * manifest test
         */
        $loader->autoload('TestNamespace\TestManifest');
        $loader->autoload('TestNamespace\TestInvalidManifest');

        try {
            $config->enableManifest('TestNamespace\TestInvalidManifest');
            $this->fail('RuntimeException was expected but not thrown');
        } catch (RuntimeException $re) {
        }

        $config->enableManifest('TestNamespace\TestManifest');

        $configs = $clientConfig->basicloader->classes;
        $this->assertEquals('TestNamespace\TestManifest', $configs->current());

        try {
            $config->disableManifest('TestNamespace\TestInvalidManifest');
            $this->fail('RuntimeException was expected but not thrown');
        } catch (RuntimeException $re) {
        }

        $config->disableManifest('TestNamespace\TestManifest');
        $this->assertEmpty($configs->current(), 'No config setting will exists');

        // cleanup
        file_put_contents($ini, '');
    }
}
