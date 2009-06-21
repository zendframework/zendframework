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
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

require_once 'Zend/Db/TestSetup.php';

require_once 'Zend/Db/Statement/Exception.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

abstract class Zend_Db_Statement_TestCommon extends Zend_Db_TestSetup
{

    public function testStatementConstruct()
    {
        $statementClass = 'Zend_Db_Statement_' . $this->getDriver();

        $select = $this->_db->select()
            ->from('zfproducts');
        $sql = $select->__toString();
        $stmt = new $statementClass($this->_db, $sql);
        $this->assertType('Zend_Db_Statement_Interface', $stmt);
        $stmt->closeCursor();
    }

    public function testStatementConstructWithSelectObject()
    {
        $statementClass = 'Zend_Db_Statement_' . $this->getDriver();

        $select = $this->_db->select()
            ->from('zfproducts');
        $stmt = new $statementClass($this->_db, $select);
        $this->assertType('Zend_Db_Statement_Interface', $stmt);
        $stmt->closeCursor();
    }

    public function testStatementConstructFromPrepare()
    {
        $select = $this->_db->select()
            ->from('zfproducts');
        $stmt = $this->_db->prepare($select->__toString());
        $this->assertType('Zend_Db_Statement_Interface', $stmt);
        $stmt->closeCursor();
    }

    public function testStatementConstructFromQuery()
    {
        $select = $this->_db->select()
            ->from('zfproducts');
        $stmt = $this->_db->query($select);
        $this->assertType('Zend_Db_Statement_Interface', $stmt);
        $stmt->closeCursor();
    }

    public function testStatementConstructFromSelect()
    {
        $stmt = $this->_db->select()
            ->from('zfproducts')
            ->query();
        $this->assertType('Zend_Db_Statement_Interface', $stmt);
        $stmt->closeCursor();
    }

    public function testStatementConstructExceptionBadSql()
    {
        $sql = "SELECT * FROM *";
        try {
            $stmt = $this->_db->query($sql);
            $this->fail('Expected to catch Zend_Db_Statement_Exception');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Statement_Exception', $e,
                'Expecting object of type Zend_Db_Statement_Exception, got '.get_class($e));
        }
    }

    public function testStatementRowCount()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        $product_id = $this->_db->quoteIdentifier('product_id');

        $stmt = $this->_db->prepare("DELETE FROM $products WHERE $product_id = 1");

        $n = $stmt->rowCount();
        $this->assertType('integer', $n);
        $this->assertEquals(0, $n, 'Expecting row count to be 0 before executing query');

        $stmt->execute();

        $n = $stmt->rowCount();
        $stmt->closeCursor();

        $this->assertType('integer', $n);
        $this->assertEquals(1, $n, 'Expected row count to be one after executing query');
    }

    public function testStatementColumnCountForSelect()
    {
        $select = $this->_db->select()
            ->from('zfproducts');

        $stmt = $this->_db->prepare($select->__toString());

        $n = $stmt->columnCount();
        $this->assertEquals(0, $n, 'Expecting column count to be 0 before executing query');

        $stmt->execute();

        $n = $stmt->columnCount();
        $stmt->closeCursor();

        $this->assertType('integer', $n);
        $this->assertEquals(2, $n);
    }

    public function testStatementColumnCountForDelete()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        $product_id = $this->_db->quoteIdentifier('product_id');

        $stmt = $this->_db->prepare("DELETE FROM $products WHERE $product_id = 1");

        $n = $stmt->columnCount();
        $this->assertEquals(0, $n, 'Expecting column count to be 0 before executing query');

        $stmt->execute();

        $n = $stmt->columnCount();
        $this->assertEquals(0, $n, 'Expecting column count to be null after executing query');
    }

    public function testStatementExecuteWithParams()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        $product_id = $this->_db->quoteIdentifier('product_id');
        $product_name = $this->_db->quoteIdentifier('product_name');

        $stmt = $this->_db->prepare("INSERT INTO $products ($product_id, $product_name) VALUES (?, ?)");
        $stmt->execute(array(4, 'Solaris'));

        $select = $this->_db->select()
            ->from('zfproducts')
            ->where("$product_id = 4");
        $result = $this->_db->fetchAll($select);
        $stmt->closeCursor();

        $this->assertEquals(array(array('product_id'=>4, 'product_name'=>'Solaris')), $result);
    }

    public function testStatementErrorCodeKeyViolation()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        $product_id = $this->_db->quoteIdentifier('product_id');
        $product_name = $this->_db->quoteIdentifier('product_name');

        $stmt = $this->_db->prepare("INSERT INTO $products ($product_id, $product_name) VALUES (?, ?)");
        try {
            // INSERT a value that results in a key violation
            $retval = $stmt->execute(array(1, 'Solaris'));
            if ($retval === false) {
                throw new Zend_Db_Statement_Exception('dummy');
            }
            $this->fail('Expected to catch Zend_Db_Statement_Exception');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Statement_Exception', $e,
                'Expecting object of type Zend_Db_Statement_Exception, got '.get_class($e));
        }
        $code = $stmt->errorCode();
        // @todo  what to assert here?
    }

    public function testStatementErrorInfoKeyViolation()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        $product_id = $this->_db->quoteIdentifier('product_id');
        $product_name = $this->_db->quoteIdentifier('product_name');

        $stmt = $this->_db->prepare("INSERT INTO $products ($product_id, $product_name) VALUES (?, ?)");
        try {
            // INSERT a value that results in a key violation
            $retval = $stmt->execute(array(1, 'Solaris'));
            if ($retval === false) {
                throw new Zend_Db_Statement_Exception('dummy');
            }
            $this->fail('Expected to catch Zend_Db_Statement_Exception');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Statement_Exception', $e,
                'Expecting object of type Zend_Db_Statement_Exception, got '.get_class($e));
        }
        $code = $stmt->errorCode();
        $info = $stmt->errorInfo();
        $this->assertEquals($code, $info[0]);
        // @todo  what to assert here?
    }

    public function testStatementSetFetchModeAssoc()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        $product_id = $this->_db->quoteIdentifier('product_id');

        // set the adapter fetch mode to something different
        $this->_db->setFetchMode(Zend_Db::FETCH_BOTH);

        $stmt = $this->_db->query("SELECT * FROM $products WHERE $product_id > 1 ORDER BY $product_id ASC");
        $stmt->setFetchMode(Zend_Db::FETCH_ASSOC);
        $result = $stmt->fetchAll();

        $this->assertEquals(2, count($result));
        $this->assertEquals(2, count($result[0]));

        // check for FETCH_ASSOC entries
        $this->assertEquals(2, $result[0]['product_id']);
        $this->assertEquals('Linux', $result[0]['product_name']);

        // check FETCH_NUM entries
        $this->assertFalse(isset($result[0][0]));
        $this->assertFalse(isset($result[0][1]));
    }

    public function testStatementSetFetchModeNum()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        $product_id = $this->_db->quoteIdentifier('product_id');

        // set the adapter fetch mode to something different
        $this->_db->setFetchMode(Zend_Db::FETCH_BOTH);

        $stmt = $this->_db->query("SELECT * FROM $products WHERE $product_id > 1 ORDER BY $product_id ASC");
        $stmt->setFetchMode(Zend_Db::FETCH_NUM);
        $result = $stmt->fetchAll();

        $this->assertEquals(2, count($result));
        $this->assertEquals(2, count($result[0]));

        // check for FETCH_ASSOC entries
        $this->assertFalse(isset($result[0]['product_id']));
        $this->assertFalse(isset($result[0]['product_name']));

        // check FETCH_NUM entries
        $this->assertEquals(2, $result[0][0]);
        $this->assertEquals('Linux', $result[0][1]);
    }

    public function testStatementSetFetchModeBoth()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        $product_id = $this->_db->quoteIdentifier('product_id');

        // set the adapter fetch mode to something different
        $this->_db->setFetchMode(Zend_Db::FETCH_ASSOC);

        $stmt = $this->_db->query("SELECT * FROM $products WHERE $product_id > 1 ORDER BY $product_id ASC");
        $stmt->setFetchMode(Zend_Db::FETCH_BOTH);
        $result = $stmt->fetchAll();

        $this->assertEquals(2, count($result));
        $this->assertEquals(4, count($result[0]));

        // check for FETCH_ASSOC entries
        $this->assertEquals(2, $result[0]['product_id']);
        $this->assertEquals('Linux', $result[0]['product_name']);

        // check FETCH_NUM entries
        $this->assertEquals(2, $result[0][0]);
        $this->assertEquals('Linux', $result[0][1]);
    }

    public function testStatementSetFetchModeObj()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        $product_id = $this->_db->quoteIdentifier('product_id');

        // set the adapter fetch mode to something different
        $this->_db->setFetchMode(Zend_Db::FETCH_BOTH);

        $stmt = $this->_db->query("SELECT * FROM $products WHERE $product_id > 1 ORDER BY $product_id ASC");
        $stmt->setFetchMode(Zend_Db::FETCH_OBJ);
        $result = $stmt->fetchAll();

        $this->assertEquals(2, count($result));
        $this->assertType('stdClass', $result[0]);

        // check for FETCH_OBJ entries
        $this->assertEquals(2, $result[0]->product_id);
        $this->assertEquals('Linux', $result[0]->product_name);
    }

    public function testStatementSetFetchModeInvalidException()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        $product_id = $this->_db->quoteIdentifier('product_id');

        $stmt = $this->_db->query("SELECT * FROM $products WHERE $product_id > 1 ORDER BY $product_id ASC");
        try {
            // invalid value
            $stmt->setFetchMode(-999);
            $this->fail('Expected to catch Zend_Db_Statement_Exception');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Statement_Exception', $e,
                'Expecting object of type Zend_Db_Statement_Exception, got '.get_class($e));
            $this->assertContains('invalid fetch mode', $e->getMessage());
        }
    }

    public function testStatementFetchAll()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        $product_id = $this->_db->quoteIdentifier('product_id');

        $stmt = $this->_db->query("SELECT * FROM $products WHERE $product_id > 1 ORDER BY $product_id ASC");
        $result = $stmt->fetchAll();

        $this->assertEquals(2, count($result));
        $this->assertEquals(2, count($result[0]));
        $this->assertEquals(2, $result[0]['product_id']);
        $this->assertFalse(isset($result[0][0]));
    }

    public function testStatementFetchAllStyleNum()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        $product_id = $this->_db->quoteIdentifier('product_id');

        $stmt = $this->_db->query("SELECT * FROM $products WHERE $product_id > 1 ORDER BY $product_id ASC");
        $result = $stmt->fetchAll(Zend_Db::FETCH_NUM);

        $this->assertEquals(2, count($result));
        $this->assertEquals(2, count($result[0]));
        $this->assertEquals(2, $result[0][0]);
        $this->assertEquals('Linux', $result[0][1]);
        $this->assertFalse(isset($result[0]['product_id']));
    }

    public function testStatementFetchAllStyleAssoc()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        $product_id = $this->_db->quoteIdentifier('product_id');

        $stmt = $this->_db->query("SELECT * FROM $products WHERE $product_id > 1 ORDER BY $product_id ASC");
        $result = $stmt->fetchAll(Zend_Db::FETCH_ASSOC);

        $this->assertEquals(2, count($result));
        $this->assertEquals(2, count($result[0]));
        $this->assertEquals(2, $result[0]['product_id']);
        $this->assertFalse(isset($result[0][0]));
    }

    public function testStatementFetchAllStyleBoth()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        $product_id = $this->_db->quoteIdentifier('product_id');

        $stmt = $this->_db->query("SELECT * FROM $products WHERE $product_id > 1 ORDER BY $product_id ASC");
        $result = $stmt->fetchAll(Zend_Db::FETCH_BOTH);

        $this->assertEquals(2, count($result));
        $this->assertEquals(4, count($result[0]));
        $this->assertEquals(2, $result[0][0]);
        $this->assertEquals('Linux', $result[0][1]);
        $this->assertEquals(2, $result[0]['product_id']);
        $this->assertEquals('Linux', $result[0]['product_name']);
    }

    public function testStatementFetchAllStyleObj()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        $product_id = $this->_db->quoteIdentifier('product_id');

        $stmt = $this->_db->query("SELECT * FROM $products WHERE $product_id > 1 ORDER BY $product_id ASC");
        $result = $stmt->fetchAll(Zend_Db::FETCH_OBJ);

        $this->assertEquals(2, count($result));
        $this->assertType('stdClass', $result[0]);
        $this->assertEquals(2, $result[0]->product_id);
    }

    public function testStatementFetchAllStyleColumn()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        $product_id = $this->_db->quoteIdentifier('product_id');

        $stmt = $this->_db->query("SELECT * FROM $products WHERE $product_id > 1 ORDER BY $product_id ASC");
        $result = $stmt->fetchAll(Zend_Db::FETCH_COLUMN);

        $this->assertEquals(2, count($result));
        $this->assertEquals(2, $result[0]);
        $this->assertEquals(3, $result[1]);
    }

    public function testStatementFetchAllStyleColumnWithArg()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        $product_id = $this->_db->quoteIdentifier('product_id');

        $stmt = $this->_db->query("SELECT * FROM $products WHERE $product_id > 1 ORDER BY $product_id ASC");
        $result = $stmt->fetchAll(Zend_Db::FETCH_COLUMN, 1);

        $this->assertEquals(2, count($result));
        $this->assertType('string', $result[0]);
        $this->assertEquals('Linux', $result[0]);
        $this->assertEquals('OS X', $result[1]);
    }

    public function testStatementFetchAllStyleException()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        $product_id = $this->_db->quoteIdentifier('product_id');

        $stmt = $this->_db->query("SELECT * FROM $products WHERE $product_id > 1 ORDER BY $product_id ASC");
        try {
            $result = $stmt->fetchAll(-99);
            $this->fail('Expected to catch Zend_Db_Statement_Exception');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Statement_Exception', $e,
                'Expecting object of type Zend_Db_Statement_Exception, got '.get_class($e));
        }
        $stmt->closeCursor();
    }

    public function testStatementFetchColumn()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        $product_id = $this->_db->quoteIdentifier('product_id');

        $stmt = $this->_db->query("SELECT * FROM $products WHERE $product_id > 1 ORDER BY $product_id ASC");

        $result = $stmt->fetchColumn();
        $this->assertEquals(2, $result);
        $result = $stmt->fetchColumn();
        $this->assertEquals(3, $result);

        $stmt->closeCursor();
    }

    public function testStatementFetchColumnEmptyResult()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        $product_id = $this->_db->quoteIdentifier('product_id');

        // query that is known to return zero rows
        $stmt = $this->_db->query("SELECT * FROM $products WHERE $product_id < 1 ORDER BY $product_id ASC");

        $result = $stmt->fetchColumn();
        $stmt->closeCursor();

        $this->assertFalse($result);
    }

    public function testStatementFetchColumnWithArg()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        $product_id = $this->_db->quoteIdentifier('product_id');

        $stmt = $this->_db->query("SELECT * FROM $products WHERE $product_id > 1 ORDER BY $product_id ASC");

        $result = $stmt->fetchColumn(1);
        $this->assertEquals('Linux', $result);
        $result = $stmt->fetchColumn(1);
        $this->assertEquals('OS X', $result);

        $stmt->closeCursor();
    }

    public function testStatementFetchObject()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        $product_id = $this->_db->quoteIdentifier('product_id');

        $stmt = $this->_db->query("SELECT * FROM $products WHERE $product_id > 1 ORDER BY $product_id ASC");
        $result = $stmt->fetchObject();
        $stmt->closeCursor();

        $this->assertType('stdClass', $result,
            'Expecting object of type stdClass, got '.get_class($result));
        $this->assertEquals('Linux', $result->product_name);
    }

    public function testStatementFetchObjectEmptyResult()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        $product_id = $this->_db->quoteIdentifier('product_id');

        // query that is known to return zero rows
        $stmt = $this->_db->query("SELECT * FROM $products WHERE $product_id < 1 ORDER BY $product_id ASC");
        $result = $stmt->fetchObject();
        $stmt->closeCursor();

        $this->assertFalse($result);
    }

    public function testStatementFetchStyleNum()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        $product_id = $this->_db->quoteIdentifier('product_id');

        $stmt = $this->_db->query("SELECT * FROM $products WHERE $product_id > 1 ORDER BY $product_id ASC");
        $result = $stmt->fetch(Zend_Db::FETCH_NUM);
        $stmt->closeCursor();

        $this->assertType('array', $result);
        $this->assertEquals('Linux', $result[1]);
        $this->assertFalse(isset($result['product_name']));
    }

    public function testStatementFetchStyleAssoc()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        $product_id = $this->_db->quoteIdentifier('product_id');

        $stmt = $this->_db->query("SELECT * FROM $products WHERE $product_id > 1 ORDER BY $product_id ASC");
        $result = $stmt->fetch(Zend_Db::FETCH_ASSOC);
        $stmt->closeCursor();

        $this->assertType('array', $result);
        $this->assertEquals('Linux', $result['product_name']);
        $this->assertFalse(isset($result[1]));
    }

    public function testStatementFetchStyleBoth()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        $product_id = $this->_db->quoteIdentifier('product_id');

        $stmt = $this->_db->query("SELECT * FROM $products WHERE $product_id > 1 ORDER BY $product_id ASC");
        $result = $stmt->fetch(Zend_Db::FETCH_BOTH);
        $stmt->closeCursor();

        $this->assertType('array', $result);
        $this->assertEquals('Linux', $result[1]);
        $this->assertEquals('Linux', $result['product_name']);
    }

    public function testStatementFetchStyleObj()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        $product_id = $this->_db->quoteIdentifier('product_id');

        $stmt = $this->_db->query("SELECT * FROM $products WHERE $product_id > 1 ORDER BY $product_id ASC");
        $result = $stmt->fetch(Zend_Db::FETCH_OBJ);
        $stmt->closeCursor();

        $this->assertType('stdClass', $result,
            'Expecting object of type stdClass, got '.get_class($result));
        $this->assertEquals('Linux', $result->product_name);
    }

    public function testStatementFetchStyleException()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        $product_id = $this->_db->quoteIdentifier('product_id');

        $stmt = $this->_db->query("SELECT * FROM $products WHERE $product_id > 1 ORDER BY $product_id ASC");
        try {
            $result = $stmt->fetch(-99);
            $this->fail('Expected to catch Zend_Db_Statement_Exception');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Statement_Exception', $e,
                'Expecting object of type Zend_Db_Statement_Exception, got '.get_class($e));
        }
        $stmt->closeCursor();
    }

    public function testStatementBindParamByPosition()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        $product_id = $this->_db->quoteIdentifier('product_id');
        $product_name = $this->_db->quoteIdentifier('product_name');

        $productIdValue   = 4;
        $productNameValue = 'AmigaOS';

        $stmt = $this->_db->prepare("INSERT INTO $products ($product_id, $product_name) VALUES (?, ?)");
        $this->assertTrue($stmt->bindParam(1, $productIdValue), 'Expected bindParam(1) to return true');
        $this->assertTrue($stmt->bindParam(2, $productNameValue), 'Expected bindParam(2) to return true');

        // we should be able to set the values after binding them
        $productIdValue   = 4;
        $productNameValue = 'Solaris';

        // no params as args to execute()
        $this->assertTrue($stmt->execute(), 'Expected execute() to return true');

        $select = $this->_db->select()
            ->from('zfproducts')
            ->where("$product_id = 4");
        $result = $this->_db->fetchAll($select);

        $this->assertEquals(array(array('product_id' => $productIdValue, 'product_name' => $productNameValue)), $result);
    }

    public function testStatementBindParamByName()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        $product_id = $this->_db->quoteIdentifier('product_id');
        $product_name = $this->_db->quoteIdentifier('product_name');

        $productIdValue   = 4;
        $productNameValue = 'AmigaOS';

        $stmt = $this->_db->prepare("INSERT INTO $products ($product_id, $product_name) VALUES (:id, :name)");
        // test with colon prefix
        $this->assertTrue($stmt->bindParam(':id', $productIdValue), 'Expected bindParam(\':id\') to return true');
        // test with no colon prefix
        $this->assertTrue($stmt->bindParam('name', $productNameValue), 'Expected bindParam(\'name\') to return true');

        // we should be able to set the values after binding them
        $productIdValue   = 4;
        $productNameValue = 'Solaris';

        // no params as args to execute()
        $this->assertTrue($stmt->execute(), 'Expected execute() to return true');

        $select = $this->_db->select()
            ->from('zfproducts')
            ->where("$product_id = 4");
        $result = $this->_db->fetchAll($select);
        $stmt->closeCursor();

        $this->assertEquals(array(array('product_id' => $productIdValue, 'product_name' => $productNameValue)), $result);
    }

    public function testStatementBindValueByPosition()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        $product_id = $this->_db->quoteIdentifier('product_id');
        $product_name = $this->_db->quoteIdentifier('product_name');

        $productIdValue   = 4;
        $productNameValue = 'AmigaOS';

        $stmt = $this->_db->prepare("INSERT INTO $products ($product_id, $product_name) VALUES (?, ?)");
        $this->assertTrue($stmt->bindValue(1, $productIdValue), 'Expected bindValue(1) to return true');
        $this->assertTrue($stmt->bindValue(2, $productNameValue), 'Expected bindValue(2) to return true');

        // we should be able to change the values without changing what gets inserted
        $productIdValue   = 5;
        $productNameValue = 'Solaris';

        // no params as args to execute()
        $this->assertTrue($stmt->execute(), 'Expected execute() to return true');

        $select = $this->_db->select()
            ->from('zfproducts')
            ->where("$product_id >= 4");
        $result = $this->_db->fetchAll($select);
        $stmt->closeCursor();

        $this->assertEquals(array(array('product_id' => '4', 'product_name' => 'AmigaOS')), $result);
    }

    public function testStatementBindValueByName()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        $product_id = $this->_db->quoteIdentifier('product_id');
        $product_name = $this->_db->quoteIdentifier('product_name');

        $productIdValue   = 4;
        $productNameValue = 'AmigaOS';

        $stmt = $this->_db->prepare("INSERT INTO $products ($product_id, $product_name) VALUES (:id, :name)");
        // test with colon prefix
        $this->assertTrue($stmt->bindValue(':id', $productIdValue), 'Expected bindValue(\':id\') to return true');
        // test with no colon prefix
        $this->assertTrue($stmt->bindValue('name', $productNameValue), 'Expected bindValue(\'name\') to return true');

        // we should be able to change the values without changing what gets inserted
        $productIdValue   = 5;
        $productNameValue = 'Solaris';

        // no params as args to execute()
        $this->assertTrue($stmt->execute(), 'Expected execute() to return true');

        $select = $this->_db->select()
            ->from('zfproducts')
            ->where("$product_id >= 4");
        $result = $this->_db->fetchAll($select);
        $stmt->closeCursor();

        $this->assertEquals(array(array('product_id' => '4', 'product_name' => 'AmigaOS')), $result);
    }

    public function testStatementBindColumnByPosition()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        $product_id = $this->_db->quoteIdentifier('product_id');

        $prodIdValue = -99;
        $prodNameValue = 'AmigaOS';

        $stmt = $this->_db->query("SELECT * FROM $products WHERE $product_id > 1 ORDER BY $product_id ASC");

        $this->assertTrue($stmt->bindColumn(1, $prodIdValue),
            'Expected bindColumn(product_id) to return true');
        $this->assertTrue($stmt->bindColumn(2, $prodNameValue),
            'Expected bindColumn(product_name) to return true');

        $this->assertTrue($stmt->fetch(Zend_Db::FETCH_BOUND),
            'Expected fetch() call 1 to return true');
        $this->assertEquals(2, $prodIdValue);
        $this->assertEquals('Linux', $prodNameValue);

        $this->assertTrue($stmt->fetch(Zend_Db::FETCH_BOUND),
            'Expected fetch() call 2 to return true');
        $this->assertEquals(3, $prodIdValue);
        $this->assertEquals('OS X', $prodNameValue);

        $stmt->closeCursor();
    }

    public function testStatementBindColumnByName()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        $product_id = $this->_db->quoteIdentifier('product_id');

        $prodIdValue = -99;
        $prodNameValue = 'AmigaOS';

        $stmt = $this->_db->query("SELECT * FROM $products WHERE $product_id > 1 ORDER BY $product_id ASC");

        $this->assertTrue($stmt->bindColumn('product_id', $prodIdValue),
            'Expected bindColumn(product_id) to return true');
        $this->assertTrue($stmt->bindColumn('product_name', $prodNameValue),
            'Expected bindColumn(product_name) to return true');

        $this->assertTrue($stmt->fetch(Zend_Db::FETCH_BOUND),
            'Expected fetch() call 1 to return true');
        $this->assertEquals(2, $prodIdValue);
        $this->assertEquals('Linux', $prodNameValue);

        $this->assertTrue($stmt->fetch(Zend_Db::FETCH_BOUND),
            'Expected fetch() call 2 to return true');
        $this->assertEquals(3, $prodIdValue);
        $this->assertEquals('OS X', $prodNameValue);

        $stmt->closeCursor();
    }

    public function testStatementBindColumnByPositionAndName()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        $product_id = $this->_db->quoteIdentifier('product_id');

        $prodIdValue = -99;
        $prodNameValue = 'AmigaOS';

        $stmt = $this->_db->query("SELECT * FROM $products WHERE $product_id > 1 ORDER BY $product_id ASC");

        $this->assertTrue($stmt->bindColumn(1, $prodIdValue),
            'Expected bindColumn(1) to return true');
        $this->assertTrue($stmt->bindColumn('product_name', $prodNameValue),
            'Expected bindColumn(product_name) to return true');

        $this->assertTrue($stmt->fetch(Zend_Db::FETCH_BOUND),
            'Expected fetch() call 1 to return true');
        $this->assertEquals(2, $prodIdValue);
        $this->assertEquals('Linux', $prodNameValue);

        $this->assertTrue($stmt->fetch(Zend_Db::FETCH_BOUND),
            'Expected fetch() call 2 to return true');
        $this->assertEquals(3, $prodIdValue);
        $this->assertEquals('OS X', $prodNameValue);

        $stmt->closeCursor();
    }

    protected $_getColumnMetaKeys = array(
        'native_type', 'flags', 'table', 'name', 'len', 'precision', 'pdo_type'
    );

    public function testStatementGetColumnMeta()
    {
        $select = $this->_db->select()
            ->from('zfbugs');
        $stmt = $this->_db->prepare($select->__toString());
        $stmt->execute();
        for ($i = 0; $i < $stmt->columnCount(); ++$i) {
            $meta = $stmt->getColumnMeta($i);
            $this->assertType('array', $meta);
            foreach ($this->_getColumnMetaKeys as $key) {
                if ($key == 'table' && version_compare(PHP_VERSION, '5.2.0', '<')) {
                    continue;
                }
                $this->assertContains($key, array_keys($meta));
            }
        }
    }

    public function testStatementNextRowset()
    {
        $select = $this->_db->select()
            ->from('zfproducts');
        $stmt = $this->_db->prepare($select->__toString());
        try {
            $stmt->nextRowset();
            $this->fail('Expected to catch Zend_Db_Statement_Exception');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Statement_Exception', $e,
                'Expecting object of type Zend_Db_Statement_Exception, got '.get_class($e));
            $this->assertEquals('nextRowset() is not implemented', $e->getMessage());
        }
        $stmt->closeCursor();
    }

    public function testStatementGetSetAttribute()
    {
        $select = $this->_db->select()
            ->from('zfproducts');
        $stmt = $this->_db->prepare($select->__toString());

        $value = 'value';
        try {
            $stmt->setAttribute(1234, $value);
        } catch (Zend_Exception $e) {
            $this->assertContains('This driver doesn\'t support setting attributes', $e->getMessage());
        }

        try {
            $this->assertEquals($value, $stmt->getAttribute(1234), "Expected '$value' #1");
        } catch (Zend_Exception $e) {
            $this->assertContains('This driver doesn\'t support getting attributes', $e->getMessage());
            return;
        }

        $valueArray = array('value1', 'value2');
        $stmt->setAttribute(1235, $valueArray);
        $this->assertEquals($valueArray, $stmt->getAttribute(1235), "Expected array #1");
        $this->assertEquals($value, $stmt->getAttribute(1234), "Expected '$value' #2");

        $valueObject = new stdClass();
        $stmt->setAttribute(1236, $valueObject);
        $this->assertSame($valueObject, $stmt->getAttribute(1236), "Expected object");
        $this->assertEquals($valueArray, $stmt->getAttribute(1235), "Expected array #2");
        $this->assertEquals($value, $stmt->getAttribute(1234), "Expected '$value' #2");
    }
}