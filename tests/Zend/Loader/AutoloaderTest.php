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
 * @package    Zend_Loader
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Loader_AutoloaderTest::main');
}

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../TestHelper.php';

/**
 * @see Zend_Loader_Autoloader
 */
require_once 'Zend/Loader/Autoloader.php';

/**
 * @see Zend_Loader_Autoloader_Interface
 */
require_once 'Zend/Loader/Autoloader/Interface.php';

/**
 * @category   Zend
 * @package    Zend_Loader
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Loader
 */
class Zend_Loader_AutoloaderTest extends PHPUnit_Framework_TestCase
{
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite(__CLASS__);
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function setUp()
    {
        // Store original autoloaders
        $this->loaders = spl_autoload_functions();
        if (!is_array($this->loaders)) {
            // spl_autoload_functions does not return empty array when no
            // autoloaders registered...
            $this->loaders = array();
        }

        // Store original include_path
        $this->includePath = get_include_path();

        Zend_Loader_Autoloader::resetInstance();
        $this->autoloader = Zend_Loader_Autoloader::getInstance();

        // initialize 'error' member for tests that utilize error handling
        $this->error = null;
    }

    public function tearDown()
    {
        // Restore original autoloaders
        $loaders = spl_autoload_functions();
        foreach ($loaders as $loader) {
            spl_autoload_unregister($loader);
        }

        foreach ($this->loaders as $loader) {
            spl_autoload_register($loader);
        }

        // Retore original include_path
        set_include_path($this->includePath);

        // Reset autoloader instance so it doesn't affect other tests
        Zend_Loader_Autoloader::resetInstance();
    }

    public function testAutoloaderShouldBeSingleton()
    {
        $autoloader = Zend_Loader_Autoloader::getInstance();
        $this->assertSame($this->autoloader, $autoloader);
    }

    public function testSingletonInstanceShouldAllowReset()
    {
        Zend_Loader_Autoloader::resetInstance();
        $autoloader = Zend_Loader_Autoloader::getInstance();
        $this->assertNotSame($this->autoloader, $autoloader);
    }

    public function testAutoloaderShouldRegisterItselfWithSplAutoloader()
    {
        $autoloaders = spl_autoload_functions();
        $found = false;
        foreach ($autoloaders as $loader) {
            if (is_array($loader)) {
                if (('autoload' == $loader[1]) && ($loader[0] === get_class($this->autoloader))) {
                    $found = true;
                    break;
                }
            }
        }
        $this->assertTrue($found, 'Autoloader instance not found in spl_autoload stack: ' . var_export($autoloaders, 1));
    }

    public function testDefaultAutoloaderShouldBeZendLoader()
    {
        $this->assertSame(array('Zend_Loader', 'loadClass'), $this->autoloader->getDefaultAutoloader());
    }

    public function testDefaultAutoloaderShouldBeMutable()
    {
        $this->autoloader->setDefaultAutoloader(array($this, 'autoload'));
        $this->assertSame(array($this, 'autoload'), $this->autoloader->getDefaultAutoloader());
    }

    /**
     * @expectedException Zend_Loader_Exception
     */
    public function testSpecifyingInvalidDefaultAutoloaderShouldRaiseException()
    {
        $this->autoloader->setDefaultAutoloader(uniqid());
    }

    public function testZfNamespacesShouldBeRegisteredByDefault()
    {
        $namespaces = $this->autoloader->getRegisteredNamespaces();
        $this->assertContains('Zend_', $namespaces);
        $this->assertContains('ZendX_', $namespaces);
    }

    public function testAutoloaderShouldAllowRegisteringArbitraryNamespaces()
    {
        $this->autoloader->registerNamespace('Phly_');
        $namespaces = $this->autoloader->getRegisteredNamespaces();
        $this->assertContains('Phly_', $namespaces);
    }

    public function testAutoloaderShouldAllowRegisteringMultipleNamespacesAtOnce()
    {
        $this->autoloader->registerNamespace(array('Phly_', 'Solar_'));
        $namespaces = $this->autoloader->getRegisteredNamespaces();
        $this->assertContains('Phly_', $namespaces);
        $this->assertContains('Solar_', $namespaces);
    }

    /**
     * @expectedException Zend_Loader_Exception
     */
    public function testRegisteringInvalidNamespaceSpecShouldRaiseException()
    {
        $o = new stdClass;
        $this->autoloader->registerNamespace($o);
    }

    public function testAutoloaderShouldAllowUnregisteringNamespaces()
    {
        $this->autoloader->unregisterNamespace('Zend');
        $namespaces = $this->autoloader->getRegisteredNamespaces();
        $this->assertNotContains('Zend', $namespaces);
    }

    public function testAutoloaderShouldAllowUnregisteringMultipleNamespacesAtOnce()
    {
        $this->autoloader->unregisterNamespace(array('Zend', 'ZendX'));
        $namespaces = $this->autoloader->getRegisteredNamespaces();
        $this->assertNotContains('Zend', $namespaces);
        $this->assertNotContains('ZendX', $namespaces);
    }

    /**
     * @expectedException Zend_Loader_Exception
     */
    public function testUnregisteringInvalidNamespaceSpecShouldRaiseException()
    {
        $o = new stdClass;
        $this->autoloader->unregisterNamespace($o);
    }

    /**
     * @group ZF-6536
     */
    public function testWarningSuppressionShouldBeDisabledByDefault()
    {
        $this->assertFalse($this->autoloader->suppressNotFoundWarnings());
    }

    public function testAutoloaderSuppressNotFoundWarningsFlagShouldBeMutable()
    {
        $this->autoloader->suppressNotFoundWarnings(true);
        $this->assertTrue($this->autoloader->suppressNotFoundWarnings());
    }

    public function testFallbackAutoloaderFlagShouldBeOffByDefault()
    {
        $this->assertFalse($this->autoloader->isFallbackAutoloader());
    }

    public function testFallbackAutoloaderFlagShouldBeMutable()
    {
        $this->autoloader->setFallbackAutoloader(true);
        $this->assertTrue($this->autoloader->isFallbackAutoloader());
    }

    public function testUnshiftAutoloaderShouldAddToTopOfAutoloaderStack()
    {
        $this->autoloader->unshiftAutoloader('require');
        $autoloaders = $this->autoloader->getAutoloaders();
        $test = array_shift($autoloaders);
        $this->assertEquals('require', $test);
    }

    public function testUnshiftAutoloaderWithoutNamespaceShouldRegisterAsEmptyNamespace()
    {
        $this->autoloader->unshiftAutoloader('require');
        $autoloaders = $this->autoloader->getNamespaceAutoloaders('');
        $test = array_shift($autoloaders);
        $this->assertEquals('require', $test);
    }

    public function testUnshiftAutoloaderShouldAllowSpecifyingSingleNamespace()
    {
        $this->autoloader->unshiftAutoloader('require', 'Foo');
        $autoloaders = $this->autoloader->getNamespaceAutoloaders('Foo');
        $test = array_shift($autoloaders);
        $this->assertEquals('require', $test);
    }

    public function testUnshiftAutoloaderShouldAllowSpecifyingMultipleNamespaces()
    {
        $this->autoloader->unshiftAutoloader('require', array('Foo', 'Bar'));

        $autoloaders = $this->autoloader->getNamespaceAutoloaders('Foo');
        $test = array_shift($autoloaders);
        $this->assertEquals('require', $test);

        $autoloaders = $this->autoloader->getNamespaceAutoloaders('Bar');
        $test = array_shift($autoloaders);
        $this->assertEquals('require', $test);
    }

    public function testPushAutoloaderShouldAddToEndOfAutoloaderStack()
    {
        $this->autoloader->pushAutoloader('require');
        $autoloaders = $this->autoloader->getAutoloaders();
        $test = array_pop($autoloaders);
        $this->assertEquals('require', $test);
    }

    public function testPushAutoloaderWithoutNamespaceShouldRegisterAsEmptyNamespace()
    {
        $this->autoloader->pushAutoloader('require');
        $autoloaders = $this->autoloader->getNamespaceAutoloaders('');
        $test = array_pop($autoloaders);
        $this->assertEquals('require', $test);
    }

    public function testPushAutoloaderShouldAllowSpecifyingSingleNamespace()
    {
        $this->autoloader->pushAutoloader('require', 'Foo');
        $autoloaders = $this->autoloader->getNamespaceAutoloaders('Foo');
        $test = array_pop($autoloaders);
        $this->assertEquals('require', $test);
    }

    public function testPushAutoloaderShouldAllowSpecifyingMultipleNamespaces()
    {
        $this->autoloader->pushAutoloader('require', array('Foo', 'Bar'));

        $autoloaders = $this->autoloader->getNamespaceAutoloaders('Foo');
        $test = array_pop($autoloaders);
        $this->assertEquals('require', $test);

        $autoloaders = $this->autoloader->getNamespaceAutoloaders('Bar');
        $test = array_pop($autoloaders);
        $this->assertEquals('require', $test);
    }

    public function testAutoloaderShouldAllowRemovingConcreteAutoloadersFromStackByCallback()
    {
        $this->autoloader->pushAutoloader('require');
        $this->autoloader->removeAutoloader('require');
        $autoloaders = $this->autoloader->getAutoloaders();
        $this->assertNotContains('require', $autoloaders);
    }

    public function testRemovingAutoloaderShouldAlsoRemoveAutoloaderFromNamespacedAutoloaders()
    {
        $this->autoloader->pushAutoloader('require', array('Foo', 'Bar'))
                         ->pushAutoloader('include');
        $this->autoloader->removeAutoloader('require');
        $test = $this->autoloader->getNamespaceAutoloaders('Foo');
        $this->assertTrue(empty($test));
        $test = $this->autoloader->getNamespaceAutoloaders('Bar');
        $this->assertTrue(empty($test));
    }

    public function testAutoloaderShouldAllowRemovingCallbackFromSpecifiedNamespaces()
    {
        $this->autoloader->pushAutoloader('require', array('Foo', 'Bar'))
                         ->pushAutoloader('include');
        $this->autoloader->removeAutoloader('require', 'Foo');
        $test = $this->autoloader->getNamespaceAutoloaders('Foo');
        $this->assertTrue(empty($test));
        $test = $this->autoloader->getNamespaceAutoloaders('Bar');
        $this->assertFalse(empty($test));
    }

    public function testAutoloadShouldReturnFalseWhenNamespaceIsNotRegistered()
    {
        $this->assertFalse(Zend_Loader_Autoloader::autoload('Foo_Bar'));
    }

    public function testAutoloadShouldReturnFalseWhenNamespaceIsNotRegisteredButClassfileExists()
    {
        $this->addTestIncludePath();
        $this->assertFalse(Zend_Loader_Autoloader::autoload('ZendLoaderAutoloader_Foo'));
    }

    public function testAutoloadShouldLoadClassWhenNamespaceIsRegisteredAndClassfileExists()
    {
        $this->addTestIncludePath();
        $this->autoloader->registerNamespace('ZendLoaderAutoloader');
        $result = Zend_Loader_Autoloader::autoload('ZendLoaderAutoloader_Foo');
        $this->assertFalse($result === false);
        $this->assertTrue(class_exists('ZendLoaderAutoloader_Foo', false));
    }

    public function testAutoloadShouldNotSuppressFileNotFoundWarningsWhenFlagIsDisabled()
    {
        $this->addTestIncludePath();
        $this->autoloader->suppressNotFoundWarnings(false);
        $this->autoloader->registerNamespace('ZendLoaderAutoloader');
        set_error_handler(array($this, 'handleErrors'));
        $this->assertFalse(Zend_Loader_Autoloader::autoload('ZendLoaderAutoloader_Bar'));
        restore_error_handler();
        $this->assertNotNull($this->error);
    }

    public function testAutoloadShouldReturnTrueIfFunctionBasedAutoloaderMatchesAndReturnsNonFalseValue()
    {
        $this->autoloader->pushAutoloader('ZendLoaderAutoloader_Autoload');
        $this->assertTrue(Zend_Loader_Autoloader::autoload('ZendLoaderAutoloader_Foo_Bar'));
    }

    public function testAutoloadShouldReturnTrueIfMethodBasedAutoloaderMatchesAndReturnsNonFalseValue()
    {
        $this->autoloader->pushAutoloader(array($this, 'autoload'));
        $this->assertTrue(Zend_Loader_Autoloader::autoload('ZendLoaderAutoloader_Foo_Bar'));
    }

    public function testAutoloadShouldReturnTrueIfAutoloaderImplementationReturnsNonFalseValue()
    {
        $this->autoloader->pushAutoloader(new Zend_Loader_AutoloaderTest_Autoloader());
        $this->assertTrue(Zend_Loader_Autoloader::autoload('ZendLoaderAutoloader_Foo_Bar'));
    }

    public function testUsingAlternateDefaultLoaderShouldOverrideUsageOfZendLoader()
    {
        $this->autoloader->setDefaultAutoloader(array($this, 'autoload'));
        $class = $this->autoloader->autoload('Zend_ThisClass_WilNever_Exist');
        $this->assertEquals('Zend_ThisClass_WilNever_Exist', $class);
        $this->assertFalse(class_exists($class, false));
    }

    public function addTestIncludePath()
    {
        set_include_path(dirname(__FILE__) . '/_files/' . PATH_SEPARATOR . $this->includePath);
    }

    public function handleErrors($errno, $errstr)
    {
        $this->error = $errstr;
    }

    public function autoload($class)
    {
        return $class;
    }
}

function ZendLoaderAutoloader_Autoload($class)
{
    return $class;
}

class Zend_Loader_AutoloaderTest_Autoloader implements Zend_Loader_Autoloader_Interface
{
    public function autoload($class)
    {
        return $class;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Loader_AutoloaderTest::main') {
    Zend_Loader_AutoloaderTest::main();
}
