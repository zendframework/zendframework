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
    define('PHPUnit_MAIN_METHOD', 'Zend_Application_Resource_LogTest::main');
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
 * @group      Zend_Application
 */
class Zend_Application_Resource_LogTest extends PHPUnit_Framework_TestCase
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
        $this->bootstrap = new Zend_Application_Bootstrap_Bootstrap($this->application);

        Zend_Controller_Front::getInstance()->resetInstance();
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

    public function testInitializationInitializesLogObject()
    {
        $resource = new Zend_Application_Resource_Log(array());
        $resource->setBootstrap($this->bootstrap);
        $resource->setOptions(array(
            'Mock' => array('writerName' => 'Mock'),
        ));
        $resource->init();
        $this->assertTrue($resource->getLog() instanceof Zend_Log);
    }

    public function testInitializationReturnsLogObject()
    {
        $resource = new Zend_Application_Resource_Log(array());
        $resource->setBootstrap($this->bootstrap);
        $resource->setOptions(array(
            'Mock' => array('writerName' => 'Mock'),
        ));
        $test = $resource->init();
        $this->assertTrue($test instanceof Zend_Log);
    }

    public function testOptionsPassedToResourceAreUsedToInitializeLog()
    {
        $stream = fopen('php://memory', 'w+', false);
        $options = array('memory' => array(
            'writerName'   => 'Stream',
            'writerParams' => array(
                'stream' => $stream,
            )
        ));

        $resource = new Zend_Application_Resource_Log($options);
        $resource->setBootstrap($this->bootstrap);
        $resource->init();

        $log      = $resource->getLog();
        $this->assertTrue($log instanceof Zend_Log);

        $log->log($message = 'logged-message', Zend_Log::INFO);
        rewind($stream);
        $this->assertContains($message, stream_get_contents($stream));
    }
    
public function testNumericLogStreamFilterParamsPriorityDoesNotFail() {
        $options = array('stream' =>
                        array('writerName'   => 'Stream',
                              'writerParams' => array('stream' => "php://memory",
                                                      'mode' => 'a'),
                        array('filterName' => 'Priority'),
                        array('filterParams' => array('priority' => '4'))));
        $resource = new Zend_Application_Resource_Log($options);
        $resource->setBootstrap($this->bootstrap);
        $resource->init();
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Application_Resource_LogTest::main') {
    Zend_Application_Resource_LogTest::main();
}
