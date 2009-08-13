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
 * @version    $Id $
 */

require_once 'Zend/Db/Select/TestCommon.php';

PHPUnit_Util_Filter::addFileToFilter(__FILE__);

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Db
 * @group      Zend_Db_Select
 */
class Zend_Db_Select_OracleTest extends Zend_Db_Select_TestCommon
{

    /**
     * ZF-4330: this test must be done on string field
     */
    protected function _selectColumnWithColonQuotedParameter ()
    {
        $product_name = $this->_db->quoteIdentifier('product_name');

        $select = $this->_db->select()
                            ->from('zfproducts')
                            ->where($product_name . ' = ?', "as'as:x");
        return $select;
    }

    /**
     * ZF-4330 : Oracle doesn't use 'AS' to identify table alias
     */
    public function testSelectFromSelectObject ()
    {
        $select = $this->_selectFromSelectObject();
        $query = $select->assemble();
        $cmp = 'SELECT ' . $this->_db->quoteIdentifier('t') . '.* FROM (SELECT '
                         . $this->_db->quoteIdentifier('subqueryTable') . '.* FROM '
                         . $this->_db->quoteIdentifier('subqueryTable') . ') '
                         . $this->_db->quoteIdentifier('t');
        $this->assertEquals($query, $cmp);
    }

    /**
     * ZF-4330 : for Oracle, we must add order clause
     */
    public function testSelectWhereOr ()
    {
        $select = $this->_selectWhereOr();
        $select->order('product_id');
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(2, count($result));
        $this->assertEquals(1, $result[0]['product_id']);
        $this->assertEquals(2, $result[1]['product_id']);
    }

    /**
     * ZF-4330 : for Oracle, we must add order clause
     */
    public function testSelectWhereOrWithParameter ()
    {
        $select = $this->_selectWhereOrWithParameter();
        $select->order('product_id');
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(2, count($result));
        $this->assertEquals(1, $result[0]['product_id']);
        $this->assertEquals(2, $result[1]['product_id']);
    }

    public function getDriver ()
    {
        return 'Oracle';
    }
}
