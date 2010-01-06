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
 * @see Zend_Db_Table_TestSetup
 */
require_once 'Zend/Db/Table/TestSetup.php';


PHPUnit_Util_Filter::addFileToFilter(__FILE__);


/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Db
 * @group      Zend_Db_Table
 * @group      Zend_Db_Table_Rowset
 */
abstract class Zend_Db_Table_Rowset_TestCommon extends Zend_Db_Table_TestSetup
{

    public function testTableRowsetIterator()
    {
        $table = $this->_table['bugs'];

        $rows = $table->find(array(1, 2));
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $rows,
            'Expecting object of type Zend_Db_Table_Rowset_Abstract, got '.get_class($rows));

        // see if we're at the beginning
        $this->assertEquals(0, $rows->key());
        $this->assertTrue($rows->valid());

        // get first row and see if it's the right one
        $row1 = $rows->current();
        $this->assertType('Zend_Db_Table_Row_Abstract', $row1,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($row1));
        $bug_id = $this->_db->foldCase('bug_id');
        $this->assertEquals(1, $row1->$bug_id);

        // advance to next row
        $rows->next();
        $this->assertEquals(1, $rows->key());
        $this->assertTrue($rows->valid());

        // get second row and see if it's the right one
        $row2 = $rows->current();
        $this->assertType('Zend_Db_Table_Row_Abstract', $row2,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($row2));
        $this->assertEquals(2, $row2->$bug_id);

        // advance beyond last row
        $rows->next();
        $this->assertEquals(2, $rows->key());
        $this->assertFalse($rows->valid());

        // current() returns null if beyond last row
        $row3 = $rows->current();
        $this->assertNull($row3);

        // rewind to beginning
        $result = $rows->rewind();
        $this->assertEquals($result, $rows);
        $this->assertEquals(0, $rows->key());
        $this->assertTrue($rows->valid());

        // get row at beginning and compare it to
        // the one we got earlier
        $row1Copy = $rows->current();
        $this->assertType('Zend_Db_Table_Row_Abstract', $row1,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($row1));
        $this->assertEquals(1, $row1->$bug_id);
        $this->assertSame($row1, $row1Copy);

        // test seeking to infinite fails
        $rows->rewind();
        try{
            $rows->seek(99999); // this index should not exist
            $this->fail('An exception should have been thrown here');
        }catch(Zend_Db_Table_Rowset_Exception $e){ }
        try{
            $rows->seek(-20);
            $this->fail('An exception should have been thrown here');
        }catch(Zend_Db_Table_Rowset_Exception $e){ }


        $rows->seek(1);
        $row = $rows->current();
        $this->assertEquals(1, $rows->key());
        $this->assertTrue($rows->valid());
        $this->assertEquals(2, $row2->$bug_id);

        $rows->rewind();
        $row = $rows->getRow(1);
        $this->assertEquals(0, $rows->key()); // pointer should not have moved
        $this->assertEquals(1, $row1->$bug_id);

        $rows->rewind();
        $row = $rows->getRow(1, true);
        $this->assertEquals(1, $rows->key()); // pointer should have moved
        $this->assertEquals(1, $row1->$bug_id);

        $rows->rewind();
        $rowcopy = $rows->seek(1)->current();
        $rows->rewind();
        $row1 = $rows->getRow(1);
        $this->assertSame($rowcopy, $row1);

        try{
            $rows->getRow(99999); // this index should not exist
            $this->fail('An exception should have been thrown here');
        }catch(Zend_Db_Table_Rowset_Exception $e){
            // has the exception correctly been overwritten by getRow() ?
            $this->assertRegExp('#No row could be found at position \d+#',$e->getMessage());
        }
    }

    public function testTableRowSetArrayAccess()
    {
        $table = $this->_table['bugs'];
        $rowset = $table->fetchAll();

        $this->assertTrue(isset($rowset[0]));
        $this->assertType('Zend_Db_Table_Row', $rowset[0]);
    }

    public function testTableRowsetEmpty()
    {
        $bug_id = $this->_db->quoteIdentifier('bug_id', true);

        $table = $this->_table['bugs'];

        $rows = $table->fetchAll("$bug_id = -1");
        $this->assertEquals(0, count($rows));
        $this->assertNull($rows->current());
    }

    public function testTableRowsetToArray()
    {
        $table = $this->_table['bugs'];
        $bug_description = $this->_db->foldCase('bug_description');

        $rows = $table->find(array(1, 2));
        $this->assertEquals(2, count($rows));

        // iterate through the rowset, because that's the only way
        // to force it to instantiate the individual Rows
        foreach ($rows as $row) {
            $row->$bug_description = 'foo';
        }

        $a = $rows->toArray();

        $this->assertTrue(is_array($a));
        $this->assertEquals(count($a), count($rows));
        $this->assertTrue(is_array($a[0]));
        $this->assertEquals(8, count($a[0]));
        $this->assertEquals('foo', $a[0][$bug_description]);
    }

    public function testTableRowsetGetConnected()
    {
        $table = $this->_table['bugs'];
        $bug_description = $this->_db->foldCase('bug_description');

        $rows = $table->find(1);

        $this->assertTrue($rows->isConnected());
    }

    public function testTableRowsetGetTable()
    {
        $table = $this->_table['bugs'];
        $bug_description = $this->_db->foldCase('bug_description');

        $rows = $table->find(1);
        $rows->setTable($table);

        $this->assertEquals($table, $rows->getTable());
    }

    public function testTableRowsetGetTableClass()
    {
        $table = $this->_table['bugs'];
        $bug_description = $this->_db->foldCase('bug_description');

        $rows = $table->find(1);
        $this->assertEquals(get_class($table), $rows->getTableClass());
    }

    public function testTableSerializeRowset()
    {
        $table = $this->_table['bugs'];

        $rows = $table->find(1);

        $serRows = serialize($rows);

        $rowsNew = unserialize($serRows);

        $this->assertFalse($rowsNew->isConnected());
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $rowsNew,
            'Expecting object of type Zend_Db_Table_Rowset_Abstract, got '.get_class($rowsNew));

        $row1New = $rowsNew->current();
        $this->assertType('Zend_Db_Table_Row_Abstract', $row1New,
            'Expecting object of type Zend_Db_Table_Row_Abstract, got '.get_class($row1New));
    }

    public function testTableSerializeRowsetExceptionWrongTable()
    {
        $table = $this->_table['bugs'];
        $bug_description = $this->_db->foldCase('bug_description');

        $rows = $table->find(1);

        // iterate through the rowset, because that's the only way
        // to force it to instantiate the individual Rows
        foreach ($rows as $row)
        {
            $row->$bug_description = $row->$bug_description;
        }

        $serRows = serialize($rows);

        $rowsNew = unserialize($serRows);
        $this->assertType('Zend_Db_Table_Rowset_Abstract', $rowsNew,
            'Expecting object of type Zend_Db_Table_Rowset_Abstract, got '.get_class($rowsNew));

        $table2 = $this->_table['products'];
        $connected = false;
        try {
            $connected = $rowsNew->setTable($table2);
            $this->fail('Expected to catch Zend_Db_Table_Row_Exception');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Table_Row_Exception', $e,
                'Expecting object of type Zend_Db_Table_Row_Exception, got '.get_class($e));
            $this->assertEquals('The specified Table is of class My_ZendDbTable_TableProducts, expecting class to be instance of My_ZendDbTable_TableBugs', $e->getMessage());
        }
        $this->assertFalse($connected);
    }

}
