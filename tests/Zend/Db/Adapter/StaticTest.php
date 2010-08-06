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
 * @package    Zend_Db
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace ZendTest\Db\Adapter;
use Zend\Config;
use Zend\Db;

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Db
 * @group      Zend_Db_Adapter
 */
class StaticTest extends \PHPUnit_Framework_TestCase
{

    protected static $_isCaseSensitiveFileSystem = null;

//    public function setup()
//    {
//        $this->markTestSkipped('This suite is skipped until Zend\Db can be refactored.');
//    }
    
    public function testDbConstructor()
    {
        $db = new TestAsset\StaticAdapter( array('dbname' => 'dummy') );
        $this->assertType('Zend\Db\Adapter\AbstractAdapter', $db);
        $this->assertEquals('dummy', $db->config['dbname']);
    }

    public function testDbConstructorExceptionInvalidOptions()
    {
        list($major, $minor, $revision) = explode('.', PHP_VERSION);
        if ($minor >= 2) {
            try {
                $db = new TestAsset\StaticAdapter('scalar');
                $this->fail('Expected exception not thrown');
            } catch (\Exception $e) {
                $this->assertContains('Adapter parameters must be in an array or a Zend_Config object', $e->getMessage());
            }
        } else {
            $this->markTestIncomplete('Failure to meet type hint results in fatal error in PHP < 5.2.0');
        }
    }

    public function testDbConstructorZendConfig()
    {
        $configData1 = array(
            'adapter' => 'StaticAdapter',
            'params' => array(
                'dbname' => 'dummy'
            )
        );
        $config1 = new Config\Config($configData1);
        $db = new TestAsset\StaticAdapter($config1->params);
        $this->assertType('Zend\Db\Adapter\AbstractAdapter', $db);
        $this->assertEquals('dummy', $db->config['dbname']);
    }

    public function testDbFactory()
    {
        $this->markTestSkipped('Forcing autoloader invalidates this.');
        $db = Db\Db::factory('StaticAdapter', array('dbname' => 'dummy') );
        $this->assertType('Zend\Db\Adapter\AbstractAdapter', $db);
        $this->assertTrue(class_exists('ZendTest\Db\Adapter\TestAsset\StaticAdapter'));
        $this->assertType('ZendTest\Db\Adapter\TestAsset\StaticAdapter', $db);
        $this->assertEquals('dummy', $db->config['dbname']);
    }

    public function testDbFactoryAlternateNamespace()
    {
        try {
            // this test used to read as 'TestNamespace', but due to ZF-5606 has been changed
            $db = Db\Db::factory('StaticAdapter', array('dbname' => 'dummy', 'adapterNamespace' => '\ZendTest\Db\Adapter\TestAsset\Testnamespace'));
        } catch (\Zend\Exception $e) {
            $this->fail('Caught exception of type '.get_class($e).' where none was expected: '.$e->getMessage());
        }
        
        $this->assertType('Zend\Db\Adapter\AbstractAdapter', $db);
        $this->assertTrue(class_exists('ZendTest\Db\Adapter\TestAsset\StaticAdapter'));
        $this->assertType('ZendTest\Db\Adapter\TestAsset\StaticAdapter', $db);
        $this->assertTrue(class_exists('ZendTest\Db\Adapter\TestAsset\Testnamespace\StaticAdapter'));
        $this->assertType('ZendTest\Db\Adapter\TestAsset\Testnamespace\StaticAdapter', $db);
    }

    public function testDbFactoryAlternateNamespaceExceptionInvalidAdapter()
    {

        try {
            $db = Db\Db::factory('Version', array('dbname' => 'dummy', 'adapterNamespace' => 'Zend'));
            $this->fail('Expected to catch Zend_Db_Exception');
        } catch (\Zend\Exception $e) {
            $this->assertType('Zend\Db\Exception', $e,
                'Expected exception of type Zend_Db_Exception, got '.get_class($e));
            $this->assertEquals("Adapter class 'Zend\Version' does not extend Zend\Db\Adapter\AbstractAdapter", $e->getMessage());
        }
    }

    public function testDbFactoryExceptionInvalidDriverName()
    {
        try {
            $db = Db\Db::factory(null);
            $this->fail('Expected to catch Zend_Db_Exception');
        } catch (\Zend\Exception $e) {
            $this->assertType('Zend\Db\Exception', $e,
                'Expected exception of type Zend_Db_Exception, got '.get_class($e));
            $this->assertEquals($e->getMessage(), 'Adapter name must be specified in a string');
        }
    }

    public function testDbFactoryExceptionInvalidOptions()
    {
        list($major, $minor, $revision) = explode('.', PHP_VERSION);
        if ($minor >= 2) {
            try {
                $db = Db\Db::factory('StaticAdapter', 'scalar');
                $this->fail('Expected exception not thrown');
            } catch (\Exception $e) {
                $this->assertContains('Adapter parameters must be in an array or a Zend_Config object', $e->getMessage());
            }
        } else {
            $this->markTestIncomplete('Failure to meet type hint results in fatal error in PHP < 5.2.0');
        }
    }

    public function testDbFactoryExceptionNoConfig()
    {
        $this->markTestSkipped('Invalid due to autoload requirement.');
        list($major, $minor, $revision) = explode('.', PHP_VERSION);
        if ($minor >= 2) {
            try {
                $db = Db\Db::factory('StaticAdapter');
                $this->fail('Expected exception not thrown');
            } catch (\Exception $e) {
                $this->assertContains('Configuration must have a key for \'dbname\' that names the database instance', $e->getMessage());
            }
        } else {
            $this->markTestIncomplete('Failure to meet type hint results in fatal error in PHP < 5.2.0');
        }
    }

    public function testDbFactoryExceptionNoDatabaseName()
    {
        $this->markTestSkipped('Invalid due to autoload requirement.');
        try {
            $db = Db\Db::factory('StaticAdapter', array());
            $this->fail('Expected to catch Zend_Db_Adapter_Exception');
        } catch (\Zend\Exception $e) {
            $this->assertType('Zend_Db_Adapter_Exception', $e,
                'Expected exception of type Zend_Db_Adapter_Exception, got '.get_class($e));
            $this->assertEquals("Configuration must have a key for 'dbname' that names the database instance", $e->getMessage());
        }
    }

    public function testDbFactoryZendConfig()
    {
        $this->markTestSkipped('Invalid due to autoload requirement.');
        $configData1 = array(
            'adapter' => 'StaticAdapter',
            'params' => array(
                'dbname' => 'dummy'
            )
        );
        $config1 = new Config\Config($configData1);
        $db = Db\Db::factory($config1);
        $this->assertType('ZendTest\Db\Adapter\TestAsset\StaticAdapter', $db);
        $this->assertEquals('dummy', $db->config['dbname']);
    }

    public function testDbFactoryZendConfigExceptionNoAdapter()
    {
        $this->markTestSkipped('Invalid due to autoload requirement.');
        $configData1 = array(
            'params' => array(
                'dbname' => 'dummy'
            )
        );
        $config1 = new Config\Config($configData1);
        try {
            $db = Db\Db::factory($config1);
            $this->fail('Expected to catch Zend_Db_Exception');
        } catch (\Zend\Exception $e) {
            $this->assertType('Zend_Db_Exception', $e,
                'Expected exception of type Zend_Db_Exception, got '.get_class($e));
            $this->assertEquals($e->getMessage(), 'Adapter name must be specified in a string');
        }
    }

    public function testDbFactoryZendConfigOverrideArray()
    {
        $this->markTestSkipped('Invalid due to autoload requirement.');
        $configData1 = array(
            'adapter' => 'Static',
            'params' => array(
                'dbname' => 'dummy'
            )
        );
        $configData2 = array(
            'dbname' => 'vanilla'
        );
        $config1 = new Config\Config($configData1);
        $db = Db\Db::factory($config1, $configData2);
        $this->assertType('Zend_Db_Adapter_Static', $db);
        // second arg should be ignored
        $this->assertEquals('dummy', $db->config['dbname']);
    }

    public function testDbFactoryZendConfigOverrideZendConfig()
    {
        $this->markTestSkipped('Invalid due to autoload requirement.');
        $configData1 = array(
            'adapter' => 'Static',
            'params' => array(
                'dbname' => 'dummy'
            )
        );
        $configData2 = array(
            'dbname' => 'vanilla'
        );
        $config1 = new Config\Config($configData1);
        $config2 = new Config\Config($configData2);
        $db = Db\Db::factory($config1, $config2);
        $this->assertType('Zend_Db_Adapter_Static', $db);
        // second arg should be ignored
        $this->assertEquals('dummy', $db->config['dbname']);
    }

    public function testDbGetConnection()
    {
        $db = Db\Db::factory('StaticAdapter', array('dbname' => 'dummy', 'adapterNamespace' => '\ZendTest\Db\Adapter\TestAsset'));
        $conn = $db->getConnection();
        $this->assertType('\ZendTest\Db\Adapter\TestAsset\StaticAdapter', $conn);
    }

    public function testDbGetFetchMode()
    {
        $db = Db\Db::factory('StaticAdapter', array('dbname' => 'dummy', 'adapterNamespace' => '\ZendTest\Db\Adapter\TestAsset'));
        $mode = $db->getFetchMode();
        $this->assertType('integer', $mode);
    }

    /**
     * @group ZF-5099
     */
    public function testDbGetServerVersion()
    {
        $db = Db\Db::factory('StaticAdapter', array('dbname' => 'dummy', 'adapterNamespace' => '\ZendTest\Db\Adapter\TestAsset'));
        $version = $db->getServerVersion();
        $this->assertEquals($version, '5.6.7.8');
        $this->assertTrue(version_compare($version, '1.0.0', '>'));
        $this->assertTrue(version_compare($version, '99.0.0', '<'));
    }

    /**
     * @group ZF-5050
     */
    public function testDbCloseConnection()
    {
        $db = Db\Db::factory('StaticAdapter', array('dbname' => 'dummy', 'adapterNamespace' => '\ZendTest\Db\Adapter\TestAsset'));
        $db->getConnection();
        $this->assertTrue($db->isConnected());
        $db->closeConnection();
        $this->assertFalse($db->isConnected());
    }

    /**
     * @group ZF-5606
     */
    public function testDbFactoryDoesNotNormalizeNamespace()
    {
        try {
            $adapter = Db\Db::factory(
                'Dbadapter',
                array('dbname' => 'dummy', 'adapterNamespace' => '\ZendTest\Db\Adapter\TestAsset\Test\MyCompany1')
                );
        } catch (\Exception $e) {
            $this->fail('Could not load file for reason: ' . $e->getMessage());
        }
        $this->assertEquals('ZendTest\Db\Adapter\TestAsset\Test\MyCompany1\Dbadapter', get_class($adapter));
    }

    /**
     * @group ZF-5606
     */
    public function testDbFactoryWillThrowExceptionWhenAssumingBadBehavior()
    {
        if (!$this->_isCaseSensitiveFileSystem()) {
            $this->markTestSkipped('This test is irrelevant on case-inspecific file systems.');
            return;
        }

        try {
            $adapter = Db\Db::factory(
                'Dbadapter',
                array('dbname' => 'dummy', 'adapterNamespace' => '\ZendTest\Db\Adapter\TestAsset\Test\MyCompany2')
                );
        } catch (\Exception $e) {
            $this->assertContains('failed to open stream', $e->getMessage());
            return;
        }

        $this->assertType('ZendTest\Db\Adapter\TestAsset\Test\MyCompany2\Dbadapter', $adapter);
    }

    /**
     * @group ZF-7924
     */
    public function testDbFactoryWillLoadCaseInsensitiveAdapterName()
    {
        $this->markTestSkipped('Invalid due to autoload requirement.');
        try {
            $adapter = Db\Db::factory(
                'DB_ADAPTER',
                array('dbname' => 'dummy', 'adapterNamespace' => '\ZendTest\Db\Adapter\TestAsset\Test\MyCompany1')
                );
        } catch (\Exception $e) {
            $this->fail('Could not load file for reason: ' . $e->getMessage());
        }
        $this->assertEquals('\ZendTest\Db\Adapter\TestAsset\Test\MyCompany1\Db\Adapter', get_class($adapter));

    }

    protected function _isCaseSensitiveFileSystem()
    {
        return true;
//        if (self::$_isCaseSensitiveFileSystem === null) {
//            self::$_isCaseSensitiveFileSystem = !(@include 'Test/MyCompany1/iscasespecific.php');
//        }
//
//        return self::$_isCaseSensitiveFileSystem;
    }

    public function getDriver()
    {
        return 'StaticAdapter';
    }

}
