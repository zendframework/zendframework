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
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Application_Resource_TranslateTest::main');
}

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../../TestHelper.php';

/**
 * Zend_Loader_Autoloader
 */
require_once 'Zend/Loader/Autoloader.php';

/**
 * @category   Zend
 * @package    Zend_Application
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Application_Resource_TranslateTest extends PHPUnit_Framework_TestCase
{
	private $_translationOptions = array('data' => array(
	    'message1' => 'message1',
	    'message2' => 'message2',
	    'message3' => 'message3'
	));

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

        $this->bootstrap = new Zend_Application_Bootstrap_Bootstrap($this->application);

        Zend_Registry::_unsetInstance();
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

    public function testInitializationInitializesTranslateObject()
    {
        $resource = new Zend_Application_Resource_Translate($this->_translationOptions);
        $resource->setBootstrap($this->bootstrap);
        $resource->init();
        $this->assertTrue($resource->getTranslate() instanceof Zend_Translate);
    }

    public function testInitializationReturnsLocaleObject()
    {
    	$resource = new Zend_Application_Resource_Translate($this->_translationOptions);
    	$resource->setBootstrap($this->bootstrap);
        $test = $resource->init();
        $this->assertTrue($test instanceof Zend_Translate);
    }

    public function testOptionsPassedToResourceAreUsedToSetLocaleState()
    {
        $resource = new Zend_Application_Resource_Translate($this->_translationOptions);
        $resource->setBootstrap($this->bootstrap);
        $resource->init();
        $translate = $resource->getTranslate();
        $this->assertTrue(Zend_Registry::isRegistered('Zend_Translate'));
        $this->assertSame(Zend_Registry::get('Zend_Translate'), $translate);
    }

    public function testResourceThrowsExceptionWithoutData()
    {
    	try {
    	    $resource = new Zend_Application_Resource_Translate();
    	    $resource->getTranslate();
    	    $this->fail('Expected Zend_Application_Resource_Exception');
    	} catch (Zend_Application_Resource_Exception $e) {
    		$this->assertType('Zend_Application_Resource_Exception', $e);
    	}
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Application_Resource_TranslateTest::main') {
    Zend_Application_Resource_TranslateTest::main();
}
