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
    define('PHPUnit_MAIN_METHOD', 'Zend_Application_Resource_DbTest::main');
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
class Zend_Application_Resource_DbTest extends PHPUnit_Framework_TestCase
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

        require_once dirname(__FILE__) . '/../_files/ZfAppBootstrap.php';
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

    public function testAdapterIsNullByDefault()
    {
        require_once 'Zend/Application/Resource/Db.php';
        $resource = new Zend_Application_Resource_Db();
        $this->assertNull($resource->getAdapter());
    }

    public function testDbIsNullByDefault()
    {
        require_once 'Zend/Application/Resource/Db.php';
        $resource = new Zend_Application_Resource_Db();
        $this->assertNull($resource->getDbAdapter());
    }

    public function testParamsAreEmptyByDefault()
    {
        require_once 'Zend/Application/Resource/Db.php';
        $resource = new Zend_Application_Resource_Db();
        $params = $resource->getParams();
        $this->assertTrue(empty($params));
    }

    public function testIsDefaultTableAdapter()
    {
        require_once 'Zend/Application/Resource/Db.php';
        $resource = new Zend_Application_Resource_Db();
        $this->assertTrue($resource->isDefaultTableAdapter());
    }

    public function testPassingDatabaseConfigurationSetsObjectState()
    {
        require_once 'Zend/Application/Resource/Db.php';
        $config = array(
            'adapter' => 'Pdo_Sqlite',
            'params'  => array(
                'dbname' => ':memory:',
            ),
            'isDefaultTableAdapter' => false,
        );
        $resource = new Zend_Application_Resource_Db($config);
        $this->assertFalse($resource->isDefaultTableAdapter());
        $this->assertEquals($config['adapter'], $resource->getAdapter());
        $this->assertEquals($config['params'], $resource->getParams());
    }

    public function testInitShouldInitializeDbAdapter()
    {
        require_once 'Zend/Application/Resource/Db.php';
        $config = array(
            'adapter' => 'Pdo_Sqlite',
            'params'  => array(
                'dbname' => ':memory:',
            ),
            'isDefaultTableAdapter' => false,
        );
        $resource = new Zend_Application_Resource_Db($config);
        $resource->init();
        $db = $resource->getDbAdapter();
        $this->assertTrue($db instanceof Zend_Db_Adapter_Pdo_Sqlite);
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Application_Resource_DbTest::main') {
    Zend_Application_Resource_DbTest::main();
}
