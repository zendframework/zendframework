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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

require_once dirname(__FILE__) . '/../_files/ZfAppBootstrap.php';
require_once dirname(__FILE__) . '/../_files/resources/Foo.php';

/**
 * @category   Zend
 * @package    Zend_Application
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Application
 */
class Zend_Application_Resource_ResourceAbstractTest extends PHPUnit_Framework_TestCase
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

        Zend_Loader_Autoloader::resetInstance();
        $this->autoloader = Zend_Loader_Autoloader::getInstance();

        $this->application = new Zend_Application('testing');

        $this->bootstrap = new ZfAppBootstrap($this->application);
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

        // Reset autoloader instance so it doesn't affect other tests
        Zend_Loader_Autoloader::resetInstance();
    }

    public function testBootstrapIsNullByDefault()
    {
        $resource = new Zend_Application_BootstrapTest_Resource_Foo();
        $this->assertNull($resource->getBootstrap());
    }

    public function testResourceShouldAllowSettingParentBootstrap()
    {
        $resource = new Zend_Application_BootstrapTest_Resource_Foo();
        $resource->setBootstrap($this->bootstrap);
        $this->assertSame($this->bootstrap, $resource->getBootstrap());
    }

    public function testOptionsAreStoredVerbatim()
    {
        $resource = new Zend_Application_BootstrapTest_Resource_Foo();
        $options  = array(
            'foo' => 'bar',
        );
        $resource->setOptions($options);
        $this->assertEquals($options, $resource->getOptions());
    }

    public function testCallingSetOptionsMultipleTimesMergesOptions()
    {
        $resource = new Zend_Application_BootstrapTest_Resource_Foo();
        $options1  = array(
            'foo' => 'bar',
        );
        $options2  = array(
            'bar' => 'baz',
        );
        $options3  = array(
            'foo' => 'BAR',
        );
        $expected = $resource->mergeOptions($options1, $options2);
        $expected = $resource->mergeOptions($expected, $options3);
        $resource->setOptions($options1)
                 ->setOptions($options2)
                 ->setOptions($options3);
        $this->assertEquals($expected, $resource->getOptions());
    }

    public function testSetOptionsProxiesToLocalSetters()
    {
        $resource = new Zend_Application_BootstrapTest_Resource_Foo();
        $options  = array(
            'someArbitraryKey' => 'test',
        );
        $resource->setOptions($options);
        $this->assertEquals('test', $resource->someArbitraryKey);
    }

    public function testConstructorAcceptsArrayConfiguration()
    {
        $options  = array(
            'foo' => 'bar',
        );
        $resource = new Zend_Application_BootstrapTest_Resource_Foo($options);
        $this->assertEquals($options, $resource->getOptions());
    }

    public function testConstructorAcceptsZendConfigObject()
    {
        $options  = array(
            'foo' => 'bar',
        );
        $config = new Zend_Config($options);
        $resource = new Zend_Application_BootstrapTest_Resource_Foo($config);
        $this->assertEquals($options, $resource->getOptions());
    }

    /**
     * @group ZF-6593
     */
    public function testSetOptionsShouldRemoveBootstrapOptionWhenPassed()
    {
        $resource = new Zend_Application_BootstrapTest_Resource_Foo();
        $resource->setOptions(array(
            'bootstrap' => $this->bootstrap,
        ));
        $this->assertSame($this->bootstrap, $resource->getBootstrap());
        $options = $resource->getOptions();
        $this->assertNotContains('bootstrap', array_keys($options));
    }

    /**
     * @group ZF-8520
     */
    public function testFirstResourceOptionShouldNotBeDropped()
    {
        $options = array(
            array('someData'),
            array('someMoreData'),
        );

        $resource = new Zend_Application_BootstrapTest_Resource_Foo($options);
        $stored   = $resource->getOptions();
        $this->assertSame($options, $stored);
    }
}
