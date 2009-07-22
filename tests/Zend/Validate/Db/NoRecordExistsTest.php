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
 * @package    Zend_Validate
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */


/**
 * PHPUnit_Framework_TestCase
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @see Zend_Db_Adapter_Pdo_Sqlite
 */
require_once 'Zend/Db/Adapter/Pdo/Sqlite.php';

/**
 * @see Zend_Db_Table_Abstract
 */
require_once 'Zend/Db/Table/Abstract.php';

/**
 * @see Zend_Validate_Db_Abstract.php
 */
require_once 'Zend/Validate/Db/Abstract.php';

/**
 * @see Zend_Validate_Db_RecordExists.php
 */
require_once 'Zend/Validate/Db/NoRecordExists.php';

/**
 *
 */
class Zend_Validate_Db_NoRecordExistsTest extends PHPUnit_Framework_TestCase
{
    /**
     * Set up test configuration
     *
     * @return void
     */
    public function setUp()
    {
        if (!extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('No sqlite available');
        }

        $this->_db = new Zend_Db_Adapter_Pdo_Sqlite(
            array('dbname' => ':memory:')
        );

        Zend_Db_Table_Abstract::setDefaultAdapter($this->_db);

        $createTable = 'CREATE TABLE [users] ( '
                   . '[id] INTEGER  NOT NULL PRIMARY KEY, '
                   . '[field1] VARCHAR(20),'
                   . '[field2] VARCHAR(20) )';
        $this->_db->query($createTable);

        $insert1 = 'INSERT INTO users (id, field1, field2) '
                            . 'VALUES (1, "value1", "value2")';
        $insert2 = 'INSERT INTO users (id, field1, field2) '
                            . 'VALUES (2, "value3", "value4")';

        $this->_db->query($insert1);
        $this->_db->query($insert2);

    }

    /**
     * Test basic function of RecordExists (no exclusion)
     *
     * @return void
     */
    public function testBasicFindsRecord()
    {
        $validator = new Zend_Validate_Db_NoRecordExists('users', 'field1');
        $this->assertFalse($validator->isValid('value1'));
    }

    /**
     * Test basic function of RecordExists (no exclusion)
     *
     * @return void
     */
    public function testBasicFindsNoRecord()
    {
        $validator = new Zend_Validate_Db_NoRecordExists('users', 'field1');
        $this->assertTrue($validator->isValid('nosuchvalue'));
    }

    /**
     * Test the exclusion function
     *
     * @return void
     */
    public function testExcludeWithArray()
    {
        $validator = new Zend_Validate_Db_NoRecordExists('users', 'field1', array('field' => 'id', 'value' => 1));
        $this->assertFalse($validator->isValid('value3'));
    }

    /**
     * Test the exclusion function
     * with an array
     *
     * @return void
     */
    public function testExcludeWithArrayNoRecord()
    {
        $validator = new Zend_Validate_Db_NoRecordExists('users', 'field1', array('field' => 'id', 'value' => 1));
        $this->assertTrue($validator->isValid('nosuchvalue'));
    }

    /**
     * Test the exclusion function
     * with a string
     *
     * @return void
     */
    public function testExcludeWithString()
    {
        $validator = new Zend_Validate_Db_NoRecordExists('users', 'field1', 'id != 1');
        $this->assertFalse($validator->isValid('value3'));
    }

    /**
     * Test the exclusion function
     * with a string
     *
     * @return void
     */
    public function testExcludeWithStringNoRecord()
    {
        $validator = new Zend_Validate_Db_NoRecordExists('users', 'field1', 'id != 1');
        $this->assertTrue($validator->isValid('nosuchvalue'));
    }

    /**
     * Test that the class throws an exception if no adapter is provided
     * and no default is set.
     *
     * @return void
     */
    public function testThrowsExceptionWithNoAdapter()
    {
        Zend_Db_Table_Abstract::setDefaultAdapter(null);
        try {
            $validator = new Zend_Validate_Db_NoRecordExists('users', 'field1', 'id != 1');
            $valid = $validator->isValid('nosuchvalue');
            $this->markTestFailed('Did not throw exception');
        } catch (Exception $e) {
        }
    }

    public function tearDown()
    {
        $dropTable = 'DROP TABLE [users]';
        $this->_db->query($dropTable);
    }
}
