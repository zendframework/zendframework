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
    define('PHPUnit_MAIN_METHOD', 'Zend_Application_Resource_MailTest::main');
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
 * @see Zend_Application_Resource_Multidb
 */
require_once 'Zend/Application/Resource/Multidb.php';

require_once 'Zend/Db/Table.php';


/**
 * @category   Zend
 * @package    Zend_Application
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Application
 */
class Zend_Application_Resource_MultidbTest extends PHPUnit_Framework_TestCase
{
    protected $_dbOptions = array('db1' => array('adapter' => 'pdo_mysql','dbname' => 'db1','password' => 'XXXX','username' => 'webuser'),
                                'db2' => array('adapter' => 'pdo_pgsql', 'dbname' => 'db2', 'password' => 'notthatpublic', 'username' => 'dba'));
    
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
        Zend_Db_Table::setDefaultAdapter(null);
        
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

    public function testInitializationInitializesResourcePluginObject()
    {
        $resource = new Zend_Application_Resource_Multidb(array());
        $resource->setBootstrap($this->bootstrap);
        $resource->setOptions($this->_dbOptions);
        $res = $resource->init();
        $this->assertTrue($res instanceof Zend_Application_Resource_Multidb);
    }
    
    public function testDbsAreSetupCorrectlyObject()
    {
        $resource = new Zend_Application_Resource_Multidb(array());
        $resource->setBootstrap($this->bootstrap);
        $resource->setOptions($this->_dbOptions);
        $res = $resource->init();
        $this->assertTrue($res->getDb('db1') instanceof Zend_Db_Adapter_Pdo_Mysql);
        $this->assertTrue($res->getDb('db2') instanceof Zend_Db_Adapter_Pdo_Pgsql);
    }
    
    public function testGetDefaultIsSetAndReturnedObject()
    {
        $options = $this->_dbOptions;
        $options['db2']['default'] = true;
        
        $resource = new Zend_Application_Resource_Multidb(array());
        $resource->setBootstrap($this->bootstrap);
        $resource->setOptions($options);
        $res = $resource->init();
        $this->assertTrue($res->getDb() instanceof Zend_Db_Adapter_Pdo_Pgsql);
        $this->assertTrue($res->isDefault($res->getDb('db2')));
        $this->assertTrue($res->isDefault('db2'));
        
        $options = $this->_dbOptions;
        $options['db2']['isDefaultTableAdapter'] = true;
        
        $resource = new Zend_Application_Resource_Multidb(array());
        $resource->setBootstrap($this->bootstrap);
        $resource->setOptions($options);
        $res = $resource->init();
        $this->assertTrue($res->getDb() instanceof Zend_Db_Adapter_Pdo_Pgsql);
        $this->assertTrue($res->isDefault($res->getDb('db2')));
        $this->assertTrue($res->isDefault('db2'));
        $this->assertTrue(Zend_Db_Table::getDefaultAdapter() instanceof Zend_Db_Adapter_Pdo_Pgsql);
        
    }
    
    public function testGetDefaultRandomWhenNoDefaultWasSetObject()
    {
        $resource = new Zend_Application_Resource_Multidb(array());
        $resource->setBootstrap($this->bootstrap);
        $resource->setOptions($this->_dbOptions);
        $res = $resource->init();
        $this->assertTrue($res->getDefaultDb() instanceof Zend_Db_Adapter_Pdo_Mysql);
        $this->assertTrue($res->getDefaultDb(true) instanceof Zend_Db_Adapter_Pdo_Mysql);
        $this->assertNull($res->getDefaultDb(false));
    }
    
    public function testGetDbWithFaultyDbNameThrowsException()
    {
        $resource = new Zend_Application_Resource_Multidb(array());
        $resource->setBootstrap($this->bootstrap);
        $resource->setOptions($this->_dbOptions);
        $res = $resource->init();

        try {
            $res->getDb('foobar');
            $this->fail('An exception should have been thrown');
        } catch(Zend_Application_Resource_Exception $e) {
            $this->assertEquals($e->getMessage(), 'A DB adapter was tried to retrieve, but was not configured');
        }
    }
    
}

if (PHPUnit_MAIN_METHOD == 'Zend_Application_Resource_LogTest::main') {
    Zend_Application_Resource_LogTest::main();
}
