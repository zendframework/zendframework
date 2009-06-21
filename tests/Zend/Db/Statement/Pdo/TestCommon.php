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

require_once 'Zend/Db/Statement/TestCommon.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

abstract class Zend_Db_Statement_Pdo_TestCommon extends Zend_Db_Statement_TestCommon
{

    public function testStatementConstruct()
    {
        $select = $this->_db->select()
            ->from('zfproducts');
        $sql = $select->__toString();
        $stmt = new Zend_Db_Statement_Pdo($this->_db, $sql);
        $this->assertType('Zend_Db_Statement_Pdo', $stmt);
        $stmt->closeCursor();
    }

    public function testStatementConstructWithSelectObject()
    {
        $select = $this->_db->select()
            ->from('zfproducts');
        $stmt = new Zend_Db_Statement_Pdo($this->_db, $select);
        $this->assertType('Zend_Db_Statement_Interface', $stmt);
        $stmt->closeCursor();
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
            $this->assertEquals('SQLSTATE[IM001]: Driver does not support this function: driver does not support multiple rowsets', $e->getMessage());
        }
        $stmt->closeCursor();
    }

    /**
     * @group ZF-4486
     */
    public function testStatementIsIterableThroughtForeach()
    {
        $select = $this->_db->select()->from('zfproducts');
        $stmt = $this->_db->query($select);
        $stmt->setFetchMode(Zend_Db::FETCH_OBJ);
        foreach ($stmt as $test) {
            $this->assertTrue($test instanceof stdClass);
        }
        $this->assertType('int', iterator_count($stmt));
    }
}
