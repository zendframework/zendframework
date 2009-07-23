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
 * @version    $Id$
 */


/**
 * @see Zend_Db_TestSetup
 */
require_once 'Zend/Db/TestSetup.php';


PHPUnit_Util_Filter::addFileToFilter(__FILE__);


/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Db_Select_TestCommon extends Zend_Db_TestSetup
{
    /**
     * Test basic use of the Zend_Db_Select class.
     *
     * @return Zend_Db_Select
     */
    protected function _select()
    {
        $select = $this->_db->select();
        $select->from('zfproducts');
        return $select;
    }

    public function testSelect()
    {
        $select = $this->_select();
        $this->assertType('Zend_Db_Select', $select,
            'Expecting object of type Zend_Db_Select, got '.get_class($select));
        $stmt = $this->_db->query($select);
        $row = $stmt->fetch();
        $stmt->closeCursor();
        $this->assertEquals(2, count($row)); // correct number of fields
        $this->assertEquals(1, $row['product_id']); // correct data
    }

    public function testSelectToString()
    {
        $select = $this->_select();
        $this->assertEquals($select->__toString(), $select->assemble()); // correct data
    }

    /**
     * Test basic use of the Zend_Db_Select class.
     */
    public function testSelectQuery()
    {
        $select = $this->_select();
        $this->assertType('Zend_Db_Select', $select,
            'Expecting object of type Zend_Db_Select, got '.get_class($select));
        $stmt = $select->query();
        $row = $stmt->fetch();
        $stmt->closeCursor();
        $this->assertEquals(2, count($row)); // correct number of fields
        $this->assertEquals(1, $row['product_id']); // correct data
    }

    /**
     * ZF-2017: Test bind use of the Zend_Db_Select class.
     * @group ZF-2017
     */
    public function testSelectQueryWithBinds()
    {
        $product_id = $this->_db->quoteIdentifier('product_id');

        $select = $this->_select()->where("$product_id = :product_id")
                                  ->bind(array(':product_id' => 1));

        $this->assertType('Zend_Db_Select', $select,
            'Expecting object of type Zend_Db_Select, got '.get_class($select));
        $stmt = $select->query();
        $row = $stmt->fetch();
        $stmt->closeCursor();
        $this->assertEquals(2, count($row)); // correct number of fields
        $this->assertEquals(1, $row['product_id']); // correct data
    }

    /**
     * Test Zend_Db_Select specifying columns
     */
    protected function _selectColumnsScalar()
    {
        $select = $this->_db->select()
            ->from('zfproducts', 'product_name'); // scalar
        return $select;
    }

    public function testSelectColumnsScalar()
    {
        $select = $this->_selectColumnsScalar();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(3, count($result), 'Expected count of result set to be 2');
        $this->assertEquals(1, count($result[0]), 'Expected column count of result set to be 1');
        $this->assertThat($result[0], $this->arrayHasKey('product_name'));
    }

    protected function _selectColumnsArray()
    {
        $select = $this->_db->select()
            ->from('zfproducts', array('product_id', 'product_name')); // array
        return $select;
    }

    public function testSelectColumnsArray()
    {
        $select = $this->_selectColumnsArray();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(3, count($result), 'Expected count of result set to be 2');
        $this->assertEquals(2, count($result[0]), 'Expected column count of result set to be 2');
        $this->assertThat($result[0], $this->arrayHasKey('product_id'));
        $this->assertThat($result[0], $this->arrayHasKey('product_name'));
    }

    /**
     * Test support for column aliases.
     * e.g. from('table', array('alias' => 'col1')).
     */
    protected function _selectColumnsAliases()
    {
        $select = $this->_db->select()
            ->from('zfproducts', array('alias' => 'product_name'));
        return $select;
    }

    public function testSelectColumnsAliases()
    {
        $select = $this->_selectColumnsAliases();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(3, count($result), 'Expected count of result set to be 2');
        $this->assertThat($result[0], $this->arrayHasKey('alias'));
        $this->assertThat($result[0], $this->logicalNot($this->arrayHasKey('product_name')));
    }

    /**
     * Test syntax to support qualified column names,
     * e.g. from('table', array('table.col1', 'table.col2')).
     */
    protected function _selectColumnsQualified()
    {
        $select = $this->_db->select()
            ->from('zfproducts', "zfproducts.product_name");
        return $select;
    }

    public function testSelectColumnsQualified()
    {
        $select = $this->_selectColumnsQualified();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertThat($result[0], $this->arrayHasKey('product_name'));
    }

    /**
     * Test support for columns defined by Zend_Db_Expr.
     */
    protected function _selectColumnsExpr()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        $product_name = $this->_db->quoteIdentifier('product_name');

        $select = $this->_db->select()
            ->from('zfproducts', new Zend_Db_Expr($products.'.'.$product_name));
        return $select;
    }

    public function testSelectColumnsExpr()
    {
        $select = $this->_selectColumnsExpr();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertThat($result[0], $this->arrayHasKey('product_name'));
    }

    /**
     * Test support for automatic conversion of SQL functions to
     * Zend_Db_Expr, e.g. from('table', array('COUNT(*)'))
     * should generate the same result as
     * from('table', array(new Zend_Db_Expr('COUNT(*)')))
     */
    protected function _selectColumnsAutoExpr()
    {
        $select = $this->_db->select()
            ->from('zfproducts', array('count' => 'COUNT(*)'));
        return $select;
    }

    public function testSelectColumnsAutoExpr()
    {
        $select = $this->_selectColumnsAutoExpr();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertThat($result[0], $this->arrayHasKey('count'));
        $this->assertEquals(3, $result[0]['count']);
    }

    /**
     * Test adding the DISTINCT query modifier to a Zend_Db_Select object.
     */
    protected function _selectDistinctModifier()
    {
        $select = $this->_db->select()
            ->distinct()
            ->from('zfproducts', new Zend_Db_Expr(327));
        return $select;
    }

    public function testSelectDistinctModifier()
    {
        $select = $this->_selectDistinctModifier();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(1, count($result));
    }

    /**
     * Test adding the FOR UPDATE query modifier to a Zend_Db_Select object.
     *
    public function testSelectForUpdateModifier()
    {
    }
     */

    /**
     * Test support for schema-qualified table names in from()
     * e.g. from('schema.table').
     */
    protected function _selectFromQualified()
    {
        $schema = $this->_util->getSchema();
        $select = $this->_db->select()
            ->from("$schema.zfproducts");
        return $select;
    }

    public function testSelectFromQualified()
    {
        $select = $this->_selectFromQualified();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(3, count($result));
    }

    /**
     * Test support for nested select in from()
     */
    protected function _selectFromSelectObject()
    {
        $subquery = $this->_db->select()
            ->from('subqueryTable');

        $select = $this->_db->select()
            ->from($subquery);
        return $select;
    }

    public function testSelectFromSelectObject()
    {
        $select = $this->_selectFromSelectObject();
        $query = $select->assemble();
        $cmp = 'SELECT ' . $this->_db->quoteIdentifier('t') . '.* FROM (SELECT '
                         . $this->_db->quoteIdentifier('subqueryTable') . '.* FROM '
                         . $this->_db->quoteIdentifier('subqueryTable') . ') AS '
                         . $this->_db->quoteIdentifier('t');
        $this->assertEquals($query, $cmp);
    }

    /**
     * Test support for nested select in from()
     */
    protected function _selectColumnsReset()
    {
        $select = $this->_db->select()
            ->from(array('p' => 'zfproducts'), array('product_id', 'product_name'));
        return $select;
    }

    public function testSelectColumnsReset()
    {
        $select = $this->_selectColumnsReset()
            ->reset(Zend_Db_Select::COLUMNS)
            ->columns('product_name');
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertContains('product_name', array_keys($result[0]));
        $this->assertNotContains('product_id', array_keys($result[0]));

        $select = $this->_selectColumnsReset()
            ->reset(Zend_Db_Select::COLUMNS)
            ->columns('p.product_name');
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertContains('product_name', array_keys($result[0]));
        $this->assertNotContains('product_id', array_keys($result[0]));

        $select = $this->_selectColumnsReset()
            ->reset(Zend_Db_Select::COLUMNS)
            ->columns('product_name', 'p');
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertContains('product_name', array_keys($result[0]));
        $this->assertNotContains('product_id', array_keys($result[0]));
    }

    public function testSelectColumnsResetBeforeFrom()
    {
        $select = $this->_selectColumnsReset();
        try {
            $select->reset(Zend_Db_Select::COLUMNS)
                   ->reset(Zend_Db_Select::FROM)
                   ->columns('product_id');
            $this->fail('Expected exception of type "Zend_Db_Select_Exception"');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Select_Exception', $e,
                              'Expected exception of type "Zend_Db_Select_Exception", got ' . get_class($e));
            $this->assertEquals("No table has been specified for the FROM clause", $e->getMessage());
        }
    }

    protected function _selectColumnWithColonQuotedParameter()
    {
        $product_id = $this->_db->quoteIdentifier('product_id');

        $select = $this->_db->select()
            ->from('zfproducts')
            ->where($product_id . ' = ?', "as'as:x");
        return $select;
    }

    public function testSelectColumnWithColonQuotedParameter()
    {
        $stmt = $select = $this->_selectColumnWithColonQuotedParameter()
            ->query();
        $result = $stmt->fetchAll();
        $this->assertEquals(0, count($result));
    }

    /**
     * Test support for FOR UPDATE
     * e.g. from('schema.table').
     */
    public function testSelectFromForUpdate()
    {
        $select = $this->_db->select()
            ->from("zfproducts")
            ->forUpdate();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(3, count($result));
    }

    /**
     * Test adding a JOIN to a Zend_Db_Select object.
     */
    protected function _selectJoin()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        $product_id = $this->_db->quoteIdentifier('product_id');
        $bugs_products = $this->_db->quoteIdentifier('zfbugs_products');

        $select = $this->_db->select()
            ->from('zfproducts')
            ->join('zfbugs_products', "$products.$product_id = $bugs_products.$product_id");
        return $select;
    }

    public function testSelectJoin()
    {
        $select = $this->_selectJoin();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(6, count($result));
        $this->assertEquals(3, count($result[0]));
    }

    /**
     * Test adding an INNER JOIN to a Zend_Db_Select object.
     * This should be exactly the same as the plain JOIN clause.
     */
    protected function _selectJoinWithCorrelationName()
    {
        $product_id = $this->_db->quoteIdentifier('product_id');
        $xyz1 = $this->_db->quoteIdentifier('xyz1');
        $xyz2 = $this->_db->quoteIdentifier('xyz2');

        $select = $this->_db->select()
            ->from( array('xyz1' => 'zfproducts') )
            ->join( array('xyz2' => 'zfbugs_products'), "$xyz1.$product_id = $xyz2.$product_id")
            ->where("$xyz1.$product_id = 1");
        return $select;
    }

    public function testSelectJoinWithCorrelationName()
    {
        $select = $this->_selectJoinWithCorrelationName();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(1, count($result));
        $this->assertEquals(3, count($result[0]));
    }

    /**
     * Test adding an INNER JOIN to a Zend_Db_Select object.
     * This should be exactly the same as the plain JOIN clause.
     */
    protected function _selectJoinInner()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        $product_id = $this->_db->quoteIdentifier('product_id');
        $bugs_products = $this->_db->quoteIdentifier('zfbugs_products');

        $select = $this->_db->select()
            ->from('zfproducts')
            ->joinInner('zfbugs_products', "$products.$product_id = $bugs_products.$product_id");
        return $select;
    }

    public function testSelectJoinInner()
    {
        $select = $this->_selectJoinInner();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(6, count($result));
        $this->assertEquals(3, count($result[0]));
    }

    /**
     * Test adding a JOIN to a Zend_Db_Select object.
     */
    protected function _selectJoinWithNocolumns()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        $bug_id = $this->_db->quoteIdentifier('bug_id');
        $product_id = $this->_db->quoteIdentifier('product_id');
        $bugs_products = $this->_db->quoteIdentifier('zfbugs_products');
        $bugs = $this->_db->quoteIdentifier('zfbugs');

        $select = $this->_db->select()
            ->from('zfproducts')
            ->join('zfbugs', "$bugs.$bug_id = 1", array())
            ->join('zfbugs_products', "$products.$product_id = $bugs_products.$product_id AND $bugs_products.$bug_id = $bugs.$bug_id", null);
        return $select;
    }

    public function testSelectJoinWithNocolumns()
    {
        $select = $this->_selectJoinWithNocolumns();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(3, count($result));
        $this->assertEquals(2, count($result[0]));
    }

    /**
     * Test adding an outer join to a Zend_Db_Select object.
     */
    protected function _selectJoinLeft()
    {
        $bugs = $this->_db->quoteIdentifier('zfbugs');
        $bugs_products = $this->_db->quoteIdentifier('zfbugs_products');
        $bug_id = $this->_db->quoteIdentifier('bug_id');

        $select = $this->_db->select()
            ->from('zfbugs')
            ->joinLeft('zfbugs_products', "$bugs.$bug_id = $bugs_products.$bug_id");
        return $select;
    }

    public function testSelectJoinLeft()
    {
        $select = $this->_selectJoinLeft();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(7, count($result));
        $this->assertEquals(9, count($result[0]));
        $this->assertEquals(3, $result[3]['product_id']);
        $this->assertNull($result[6]['product_id']);
    }

    /**
     * Returns a select object that uses table aliases and specifies a mixed ordering of columns,
     * for testing whether the user-specified ordering is preserved.
     *
     * @return Zend_Db_Select
     */
    protected function _selectJoinLeftTableAliasesColumnOrderPreserve()
    {
        $bugsBugId        = $this->_db->quoteIdentifier('b.bug_id');
        $bugsProductBugId = $this->_db->quoteIdentifier('bp.bug_id');

        $select = $this->_db->select()
            ->from(array('b' => 'zfbugs'), array('b.bug_id', 'bp.product_id', 'b.bug_description'))
            ->joinLeft(array('bp' => 'zfbugs_products'), "$bugsBugId = $bugsProductBugId", array());

        return $select;
    }

    /**
     * Ensures that when table aliases are used with a mixed ordering of columns, the user-specified
     * column ordering is preserved.
     *
     * @return void
     */
    public function testJoinLeftTableAliasesColumnOrderPreserve()
    {
        $select = $this->_selectJoinLeftTableAliasesColumnOrderPreserve();
        $this->assertRegExp('/^.*b.*bug_id.*,.*bp.*product_id.*,.*b.*bug_description.*$/s', $select->assemble());
    }

    /**
     * Test adding an outer join to a Zend_Db_Select object.
     */
    protected function _selectJoinRight()
    {
        $bugs = $this->_db->quoteIdentifier('zfbugs');
        $bugs_products = $this->_db->quoteIdentifier('zfbugs_products');
        $bug_id = $this->_db->quoteIdentifier('bug_id');

        $select = $this->_db->select()
            ->from('zfbugs_products')
            ->joinRight('zfbugs', "$bugs_products.$bug_id = $bugs.$bug_id");
        return $select;
    }

    public function testSelectJoinRight()
    {
        $select = $this->_selectJoinRight();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(7, count($result));
        $this->assertEquals(9, count($result[0]));
        $this->assertEquals(3, $result[3]['product_id']);
        $this->assertNull($result[6]['product_id']);
    }

    /**
     * Test adding a cross join to a Zend_Db_Select object.
     */
    protected function _selectJoinCross()
    {
        $select = $this->_db->select()
            ->from('zfproducts')
            ->joinCross('zfbugs_products');
        return $select;
    }

    public function testSelectJoinCross()
    {
        $select = $this->_selectJoinCross();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(18, count($result));
        $this->assertEquals(3, count($result[0]));
    }

    /**
     * Test support for schema-qualified table names in join(),
     * e.g. join('schema.table', 'condition')
     */
    protected function _selectJoinQualified()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        $bugs_products = $this->_db->quoteIdentifier('zfbugs_products');
        $product_id = $this->_db->quoteIdentifier('product_id');

        $schema = $this->_util->getSchema();
        $select = $this->_db->select()
            ->from('zfproducts')
            ->join("$schema.zfbugs_products", "$products.$product_id = $bugs_products.$product_id");
        return $select;
    }

    public function testSelectJoinQualified()
    {
        $select = $this->_selectJoinQualified();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(6, count($result));
        $this->assertEquals(3, count($result[0]));
    }

    protected function _selectJoinUsing()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        $bugs_products = $this->_db->quoteIdentifier('zfbugs_products');
        $product_id = $this->_db->quoteIdentifier('product_id');

        $select = $this->_db->select()
            ->from('zfproducts')
            ->joinUsing("zfbugs_products", "$product_id")
            ->where("$bugs_products.$product_id < ?", 3);
        return $select;
    }

    public function testSelectMagicMethod()
    {
        $select = $this->_selectJoinUsing();
        try {
            $select->foo();
            $this->fail('Expected exception of type "Zend_Db_Select_Exception"');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Select_Exception', $e,
                              'Expected exception of type "Zend_Db_Select_Exception", got ' . get_class($e));
            $this->assertEquals("Unrecognized method 'foo()'", $e->getMessage());
        }
    }

    public function testSelectJoinUsing()
    {
        $select = $this->_selectJoinUsing();
        $sql = preg_replace('/\\s+/', ' ', $select->assemble());
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(3, count($result));
        $this->assertEquals(1, $result[0]['product_id']);
    }

    protected function _selectJoinInnerUsing()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        $bugs_products = $this->_db->quoteIdentifier('zfbugs_products');
        $product_id = $this->_db->quoteIdentifier('product_id');

        $select = $this->_db->select()
            ->from('zfproducts')
            ->joinInnerUsing("zfbugs_products", "$product_id")
            ->where("$bugs_products.$product_id < ?", 3);
        return $select;
    }

    public function testSelectJoinInnerUsing()
    {
        $select = $this->_selectJoinInnerUsing();
        $sql = preg_replace('/\\s+/', ' ', $select->assemble());
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(3, count($result));
        $this->assertEquals(1, $result[0]['product_id']);
    }

    public function testSelectJoinInnerUsingException()
    {
        $select = $this->_selectJoinInnerUsing();
        try {
            $select->joinFooUsing();
            $this->fail('Expected exception of type "Zend_Db_Select_Exception"');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Select_Exception', $e,
                              'Expected exception of type "Zend_Db_Select_Exception", got ' . get_class($e));
            $this->assertEquals("Unrecognized method 'joinFooUsing()'", $e->getMessage());
        }
    }

    protected function _selectJoinCrossUsing()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        $bugs_products = $this->_db->quoteIdentifier('zfbugs_products');
        $product_id = $this->_db->quoteIdentifier('product_id');

        $select = $this->_db->select()
            ->from('zfproducts')
            ->where("$bugs_products.$product_id < ?", 3);
        return $select;
    }

    public function testSelectJoinCrossUsing()
    {
        $product_id = $this->_db->quoteIdentifier('product_id');
        $select = $this->_selectJoinCrossUsing();
        try {
            $select->joinCrossUsing("zfbugs_products", "$product_id");
            $this->fail('Expected exception of type "Zend_Db_Select_Exception"');
        } catch (Zend_Exception $e) {
            $this->assertType('Zend_Db_Select_Exception', $e,
                              'Expected exception of type "Zend_Db_Select_Exception", got ' . get_class($e));
            $this->assertEquals("Cannot perform a joinUsing with method 'joinCrossUsing()'", $e->getMessage());
        }
    }

    /**
     * Test adding a WHERE clause to a Zend_Db_Select object.
     */
    protected function _selectWhere()
    {
        $product_id = $this->_db->quoteIdentifier('product_id');

        $select = $this->_db->select()
            ->from('zfproducts')
            ->where("$product_id = 2");
        return $select;
    }

    public function testSelectWhere()
    {
        $select = $this->_selectWhere();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(1, count($result));
        $this->assertEquals(2, $result[0]['product_id']);
    }

    /**
     * Test support for nested select in from()
     */
    protected function _selectWhereSelectObject()
    {
        $subquery = $this->_db->select()
            ->from('subqueryTable');

        $select = $this->_db->select()
            ->from('table')
            ->where('foo IN ?', $subquery);
        return $select;
    }

    public function testSelectWhereSelectObject()
    {
        $select = $this->_selectWhereSelectObject();
        $query = $select->assemble();
        $cmp = 'SELECT ' . $this->_db->quoteIdentifier('table') . '.* FROM '
                         . $this->_db->quoteIdentifier('table') . ' WHERE (foo IN (SELECT '
                         . $this->_db->quoteIdentifier('subqueryTable') . '.* FROM '
                         . $this->_db->quoteIdentifier('subqueryTable') . '))';
        $this->assertEquals($query, $cmp);
    }

    protected function _selectWhereArray()
    {
        $product_id = $this->_db->quoteIdentifier('product_id');

        $select = $this->_db->select()
            ->from('zfproducts')
            ->where("$product_id IN (?)", array(1, 2, 3));
        return $select;
    }

    public function testSelectWhereArray()
    {
        $select = $this->_selectWhereArray();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(3, count($result));
    }

    /**
     * test adding more WHERE conditions,
     * which should be combined with AND by default.
     */
    protected function _selectWhereAnd()
    {
        $product_id = $this->_db->quoteIdentifier('product_id');

        $select = $this->_db->select()
            ->from('zfproducts')
            ->where("$product_id = 2")
            ->where("$product_id = 1");
        return $select;
    }

    public function testSelectWhereAnd()
    {
        $select = $this->_selectWhereAnd();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(0, count($result));
    }

    /**
     * Test support for where() with a parameter,
     * e.g. where('id = ?', 1).
     */
    protected function _selectWhereWithParameter()
    {
        $product_id = $this->_db->quoteIdentifier('product_id');

        $select = $this->_db->select()
            ->from('zfproducts')
            ->where("$product_id = ?", 2);
        return $select;
    }

    public function testSelectWhereWithParameter()
    {
        $select = $this->_selectWhereWithParameter();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(1, count($result));
        $this->assertEquals(2, $result[0]['product_id']);
    }

    /**
     * Test support for where() with a specified type,
     * e.g. where('id = ?', 1, 'int').
     */
    protected function _selectWhereWithType()
    {
        $product_id = $this->_db->quoteIdentifier('product_id');

        $select = $this->_db->select()
            ->from('zfproducts')
            ->where("$product_id = ?", 2, 'int');
        return $select;
    }

    public function testSelectWhereWithType()
    {
        $select = $this->_selectWhereWithType();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(1, count($result));
        $this->assertEquals(2, $result[0]['product_id']);
    }

    /**
     * Test support for where() with a specified type,
     * e.g. where('id = ?', 1, 'int').
     */
    protected function _selectWhereWithTypeFloat()
    {
        $price_total = $this->_db->quoteIdentifier('price_total');

        $select = $this->_db->select()
            ->from('zfprice')
            ->where("$price_total = ?", 200.45, Zend_Db::FLOAT_TYPE);
        return $select;
    }

    public function testSelectWhereWithTypeFloat()
    {
        $locale = setlocale(LC_ALL, null);

        $select = $this->_selectWhereWithTypeFloat();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(1, count($result));
        $this->assertEquals(200.45, $result[0]['price_total']);

        try {
            setlocale(LC_ALL, 'fr_BE.UTF-8');
            $select = $this->_selectWhereWithTypeFloat();
            $stmt = $this->_db->query($select);
            $result = $stmt->fetchAll();
            $this->assertEquals(1, count($result));
            $this->assertEquals(200.45, $result[0]['price_total']);
        } catch (Zend_Exception $e) {
            setlocale(LC_ALL, $locale);
            throw $e;
        }

        setlocale(LC_ALL, $locale);
    }

    /**
     * Test adding an OR WHERE clause to a Zend_Db_Select object.
     */
    protected function _selectWhereOr()
    {
        $product_id = $this->_db->quoteIdentifier('product_id');

        $select = $this->_db->select()
            ->from('zfproducts')
            ->orWhere("$product_id = 1")
            ->orWhere("$product_id = 2");
        return $select;
    }

    public function testSelectWhereOr()
    {
        $select = $this->_selectWhereOr();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(2, count($result));
        $this->assertEquals(1, $result[0]['product_id']);
        $this->assertEquals(2, $result[1]['product_id']);
    }

    /**
     * Test support for where() with a parameter,
     * e.g. orWhere('id = ?', 2).
     */
    protected function _selectWhereOrWithParameter()
    {
        $product_id = $this->_db->quoteIdentifier('product_id');

        $select = $this->_db->select()
            ->from('zfproducts')
            ->orWhere("$product_id = ?", 1)
            ->orWhere("$product_id = ?", 2);
        return $select;
    }

    public function testSelectWhereOrWithParameter()
    {
        $select = $this->_selectWhereOrWithParameter();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(2, count($result));
        $this->assertEquals(1, $result[0]['product_id']);
        $this->assertEquals(2, $result[1]['product_id']);
    }

    /**
     * Test adding a GROUP BY clause to a Zend_Db_Select object.
     */
    protected function _selectGroupBy()
    {
        $thecount = $this->_db->quoteIdentifier('thecount');

        $select = $this->_db->select()
            ->from('zfbugs_products', array('bug_id', new Zend_Db_Expr("COUNT(*) AS $thecount")))
            ->group('bug_id')
            ->order('bug_id');
        return $select;
    }

    public function testSelectGroupBy()
    {
        $select = $this->_selectGroupBy();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(3, count($result),
            'Expected count of first result set to be 2');
        $this->assertEquals(1, $result[0]['bug_id']);
        $this->assertEquals(3, $result[0]['thecount'],
            'Expected count(*) of first result set to be 2');
        $this->assertEquals(2, $result[1]['bug_id']);
        $this->assertEquals(1, $result[1]['thecount']);
    }

    /**
     * Test support for qualified table in group(),
     * e.g. group('schema.table').
     */
    protected function _selectGroupByQualified()
    {
        $thecount = $this->_db->quoteIdentifier('thecount');

        $select = $this->_db->select()
            ->from('zfbugs_products', array('bug_id', new Zend_Db_Expr("COUNT(*) AS $thecount")))
            ->group("zfbugs_products.bug_id")
            ->order('bug_id');
        return $select;
    }

    public function testSelectGroupByQualified()
    {
        $select = $this->_selectGroupByQualified();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(3, count($result),
            'Expected count of first result set to be 2');
        $this->assertEquals(1, $result[0]['bug_id']);
        $this->assertEquals(3, $result[0]['thecount'],
            'Expected count(*) of first result set to be 2');
        $this->assertEquals(2, $result[1]['bug_id']);
        $this->assertEquals(1, $result[1]['thecount']);
    }

    /**
     * Test support for Zend_Db_Expr in group(),
     * e.g. group(new Zend_Db_Expr('id+1'))
     */
    protected function _selectGroupByExpr()
    {
        $thecount = $this->_db->quoteIdentifier('thecount');
        $bug_id = $this->_db->quoteIdentifier('bug_id');

        $select = $this->_db->select()
            ->from('zfbugs_products', array('bug_id'=>new Zend_Db_Expr("$bug_id+1"), new Zend_Db_Expr("COUNT(*) AS $thecount")))
            ->group(new Zend_Db_Expr("$bug_id+1"))
            ->order(new Zend_Db_Expr("$bug_id+1"));
        return $select;
    }

    public function testSelectGroupByExpr()
    {
        $select = $this->_selectGroupByExpr();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(3, count($result),
            'Expected count of first result set to be 2');
        $this->assertEquals(2, $result[0]['bug_id'],
            'Expected first bug_id to be 2');
        $this->assertEquals(3, $result[0]['thecount'],
            'Expected count(*) of first group to be 2');
        $this->assertEquals(3, $result[1]['bug_id'],
            'Expected second bug_id to be 3');
        $this->assertEquals(1, $result[1]['thecount'],
            'Expected count(*) of second group to be 1');
    }

    /**
     * Test support for automatic conversion of a SQL
     * function to a Zend_Db_Expr in group(),
     * e.g.  group('LOWER(title)') should give the same
     * result as group(new Zend_Db_Expr('LOWER(title)')).
     */

    protected function _selectGroupByAutoExpr()
    {
        $thecount = $this->_db->quoteIdentifier('thecount');
        $bugs_products = $this->_db->quoteIdentifier('zfbugs_products');
        $bug_id = $this->_db->quoteIdentifier('bug_id');

        $select = $this->_db->select()
            ->from('zfbugs_products', array('bug_id'=>"ABS($bugs_products.$bug_id)", new Zend_Db_Expr("COUNT(*) AS $thecount")))
            ->group("ABS($bugs_products.$bug_id)")
            ->order("ABS($bugs_products.$bug_id)");
        return $select;
    }

    public function testSelectGroupByAutoExpr()
    {
        $select = $this->_selectGroupByAutoExpr();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(3, count($result), 'Expected count of first result set to be 2');
        $this->assertEquals(1, $result[0]['bug_id']);
        $this->assertEquals(3, $result[0]['thecount'], 'Expected count(*) of first result set to be 2');
        $this->assertEquals(2, $result[1]['bug_id']);
        $this->assertEquals(1, $result[1]['thecount']);
    }

    /**
     * Test adding a HAVING clause to a Zend_Db_Select object.
     */
    protected function _selectHaving()
    {
        $select = $this->_db->select()
            ->from('zfbugs_products', array('bug_id', 'COUNT(*) AS thecount'))
            ->group('bug_id')
            ->having('COUNT(*) > 1')
            ->order('bug_id');
        return $select;
    }

    public function testSelectHaving()
    {
        $select = $this->_selectHaving();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(2, count($result));
        $this->assertEquals(1, $result[0]['bug_id']);
        $this->assertEquals(3, $result[0]['thecount']);
    }

    protected function _selectHavingAnd()
    {
        $select = $this->_db->select()
            ->from('zfbugs_products', array('bug_id', 'COUNT(*) AS thecount'))
            ->group('bug_id')
            ->having('COUNT(*) > 1')
            ->having('COUNT(*) = 1')
            ->order('bug_id');
        return $select;
    }

    public function testSelectHavingAnd()
    {
        $select = $this->_selectHavingAnd();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(0, count($result));
    }

    /**
     * Test support for parameter in having(),
     * e.g. having('count(*) > ?', 1).
     */

    protected function _selectHavingWithParameter()
    {
        $select = $this->_db->select()
            ->from('zfbugs_products', array('bug_id', 'COUNT(*) AS thecount'))
            ->group('bug_id')
            ->having('COUNT(*) > ?', 1)
            ->order('bug_id');
        return $select;
    }

    public function testSelectHavingWithParameter()
    {
        $select = $this->_selectHavingWithParameter();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(2, count($result));
        $this->assertEquals(1, $result[0]['bug_id']);
        $this->assertEquals(3, $result[0]['thecount']);
    }

    /**
     * Test adding a HAVING clause to a Zend_Db_Select object.
     */

    protected function _selectHavingOr()
    {
        $select = $this->_db->select()
            ->from('zfbugs_products', array('bug_id', 'COUNT(*) AS thecount'))
            ->group('bug_id')
            ->orHaving('COUNT(*) > 1')
            ->orHaving('COUNT(*) = 1')
            ->order('bug_id');
        return $select;
    }

    public function testSelectHavingOr()
    {
        $select = $this->_selectHavingOr();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(3, count($result));
        $this->assertEquals(1, $result[0]['bug_id']);
        $this->assertEquals(3, $result[0]['thecount']);
        $this->assertEquals(2, $result[1]['bug_id']);
        $this->assertEquals(1, $result[1]['thecount']);
    }

    /**
     * Test support for parameter in orHaving(),
     * e.g. orHaving('count(*) > ?', 1).
     */
    protected function _selectHavingOrWithParameter()
    {
        $select = $this->_db->select()
            ->from('zfbugs_products', array('bug_id', 'COUNT(*) AS thecount'))
            ->group('bug_id')
            ->orHaving('COUNT(*) > ?', 1)
            ->orHaving('COUNT(*) = ?', 1)
            ->order('bug_id');
        return $select;
    }

    public function testSelectHavingOrWithParameter()
    {
        $select = $this->_selectHavingOrWithParameter();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(3, count($result));
        $this->assertEquals(1, $result[0]['bug_id']);
        $this->assertEquals(3, $result[0]['thecount']);
        $this->assertEquals(2, $result[1]['bug_id']);
        $this->assertEquals(1, $result[1]['thecount']);
    }

    /**
     * Test adding an ORDER BY clause to a Zend_Db_Select object.
     */
    protected function _selectOrderBy()
    {
        $select = $this->_db->select()
            ->from('zfproducts')
            ->order('product_id');
        return $select;
    }

    public function testSelectOrderBy()
    {
        $select = $this->_selectOrderBy();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(1, $result[0]['product_id']);
    }

    protected function _selectOrderByArray()
    {
        $select = $this->_db->select()
            ->from('zfproducts')
            ->order(array('product_name', 'product_id'));
        return $select;
    }

    public function testSelectOrderByArray()
    {
        $select = $this->_selectOrderByArray();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(3, count($result),
            'Expected count of result set to be 3');
        $this->assertEquals('Linux', $result[0]['product_name']);
        $this->assertEquals(2, $result[0]['product_id']);
    }

    protected function _selectOrderByAsc()
    {
        $select = $this->_db->select()
            ->from('zfproducts')
            ->order("product_id ASC");
        return $select;
    }

    public function testSelectOrderByAsc()
    {
        $select = $this->_selectOrderByAsc();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(3, count($result),
            'Expected count of result set to be 2');
        $this->assertEquals(1, $result[0]['product_id']);
    }

    protected function _selectOrderByDesc()
    {
        $select = $this->_db->select()
            ->from('zfproducts')
            ->order("product_id DESC");
        return $select;
    }

    public function testSelectOrderByDesc()
    {
        $select = $this->_selectOrderByDesc();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(3, count($result),
            'Expected count of result set to be 2');
        $this->assertEquals(3, $result[0]['product_id']);
    }

    /**
     * Test support for qualified table in order(),
     * e.g. order('schema.table').
     */
    protected function _selectOrderByQualified()
    {
        $select = $this->_db->select()
            ->from('zfproducts')
            ->order("zfproducts.product_id");
        return $select;
    }

    public function testSelectOrderByQualified()
    {
        $select = $this->_selectOrderByQualified();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(1, $result[0]['product_id']);
    }

    /**
     * Test support for Zend_Db_Expr in order(),
     * e.g. order(new Zend_Db_Expr('id+1')).
     */
    protected function _selectOrderByExpr()
    {
        $select = $this->_db->select()
            ->from('zfproducts')
            ->order(new Zend_Db_Expr("1"));
        return $select;
    }

    public function testSelectOrderByExpr()
    {
        $select = $this->_selectOrderByExpr();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(1, $result[0]['product_id']);
    }

    /**
     * Test automatic conversion of SQL functions to
     * Zend_Db_Expr, e.g. order('LOWER(title)')
     * should give the same result as
     * order(new Zend_Db_Expr('LOWER(title)')).
     */
    protected function _selectOrderByAutoExpr()
    {
        $products = $this->_db->quoteIdentifier('zfproducts');
        $product_id = $this->_db->quoteIdentifier('product_id');

        $select = $this->_db->select()
            ->from('zfproducts')
            ->order("ABS($products.$product_id)");
        return $select;
    }

    public function testSelectOrderByAutoExpr()
    {
        $select = $this->_selectOrderByAutoExpr();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(1, $result[0]['product_id']);
    }

    /**
     * Test ORDER BY clause that contains multiple lines.
     * See ZF-1822, which says that the regexp matching
     * ASC|DESC fails when string is multi-line.
     */
    protected function _selectOrderByMultiLine()
    {
        $select = $this->_db->select()
            ->from('zfproducts')
            ->order("product_id\nDESC");
        return $select;
    }

    public function testSelectOrderByMultiLine()
    {
        $select = $this->_selectOrderByMultiLine();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(3, $result[0]['product_id']);
    }

    /**
     * @group ZF-4246
     */
    protected function _checkExtraField($result)
    {
        // Check that extra field ZEND_DB_ROWNUM isn't present
        // (particulary with Db2 & Oracle)
        $this->assertArrayNotHasKey('zend_db_rownum', $result);
        $this->assertArrayNotHasKey('ZEND_DB_ROWNUM', $result);
    }

    /**
     * Test adding a LIMIT clause to a Zend_Db_Select object.
     */
    protected function _selectLimit()
    {
        $select = $this->_db->select()
            ->from('zfproducts')
            ->order('product_id')
            ->limit(1);
        return $select;
    }

    /**
     * @group ZF-4246
     */
    public function testSelectLimit()
    {
        $select = $this->_selectLimit();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(1, count($result));
        $this->assertEquals(1, $result[0]['product_id']);
        $this->_checkExtraField($result[0]);
    }

    /**
     * @group ZF-5263
     * @group ZF-4246
     */
    public function testSelectLimitFetchCol()
    {
        $product_id = $this->_db->quoteIdentifier('product_id');

        $select = $this->_db->select()
            ->from('zfproducts', 'product_name')
            ->where($product_id . ' = ?', 3)
            ->limit(1);

        $result = $this->_db->fetchCol($select);
        $this->assertEquals(1, count($result));
        $this->assertEquals('OS X', $result[0]);
        $this->_checkExtraField($result);
    }

    protected function _selectLimitNone()
    {
        $select = $this->_db->select()
            ->from('zfproducts')
            ->order('product_id')
            ->limit(); // no limit
        return $select;
    }

    /**
     * @group ZF-4246
     */
    public function testSelectLimitNone()
    {
        $select = $this->_selectLimitNone();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(3, count($result));
        $this->_checkExtraField($result[0]);
    }

    protected function _selectLimitOffset()
    {
        $select = $this->_db->select()
            ->from('zfproducts')
            ->order('product_id')
            ->limit(1, 1);
        return $select;
    }

    /**
     * @group ZF-4246
     */
    public function testSelectLimitOffset()
    {
        $select = $this->_selectLimitOffset();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(1, count($result));
        $this->assertEquals(2, $result[0]['product_id']);
        $this->_checkExtraField($result[0]);
    }

    /**
     * Test the limitPage() method of a Zend_Db_Select object.
     */
    protected function _selectLimitPageOne()
    {
        $select = $this->_db->select()
            ->from('zfproducts')
            ->order('product_id')
            ->limitPage(1, 1); // first page, length 1
        return $select;
    }

    /**
     * @group ZF-4246
     */
    public function testSelectLimitPageOne()
    {
        $select = $this->_selectLimitPageOne();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(1, count($result));
        $this->assertEquals(1, $result[0]['product_id']);
        $this->_checkExtraField($result[0]);
    }

    protected function _selectLimitPageTwo()
    {
        $select = $this->_db->select()
            ->from('zfproducts')
            ->order('product_id')
            ->limitPage(2, 1); // second page, length 1
        return $select;
    }

    /**
     * @group ZF-4246
     */
    public function testSelectLimitPageTwo()
    {
        $select = $this->_selectLimitPageTwo();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(1, count($result));
        $this->assertEquals(2, $result[0]['product_id']);
        $this->_checkExtraField($result[0]);
    }

    /**
     * Test the getPart() and reset() methods of a Zend_Db_Select object.
     */
    public function testSelectGetPartAndReset()
    {
        $select = $this->_db->select()
            ->from('zfproducts')
            ->limit(1);
        $count = $select->getPart(Zend_Db_Select::LIMIT_COUNT);
        $this->assertEquals(1, $count);

        $select->reset(Zend_Db_Select::LIMIT_COUNT);
        $count = $select->getPart(Zend_Db_Select::LIMIT_COUNT);
        $this->assertNull($count);

        $select->reset(); // reset the whole object
        $from = $select->getPart(Zend_Db_Select::FROM);
        $this->assertTrue(empty($from));
    }

    /**
     * Test the UNION statement for a Zend_Db_Select object.
     */
    protected function _selectUnionString()
    {
        $bugs = $this->_db->quoteIdentifier('zfbugs');
        $bug_id = $this->_db->quoteIdentifier('bug_id');
        $bug_status = $this->_db->quoteIdentifier('bug_status');
        $products = $this->_db->quoteIdentifier('zfproducts');
        $product_id = $this->_db->quoteIdentifier('product_id');
        $product_name = $this->_db->quoteIdentifier('product_name');
        $id = $this->_db->quoteIdentifier('id');
        $name = $this->_db->quoteIdentifier('name');
        $sql1 = "SELECT $bug_id AS $id, $bug_status AS $name FROM $bugs";
        $sql2 = "SELECT $product_id AS $id, $product_name AS $name FROM $products";

        $select = $this->_db->select()
            ->union(array($sql1, $sql2))
            ->order('id');
        return $select;
    }

    public function testSelectUnionString()
    {
        $select = $this->_selectUnionString();
        $stmt = $this->_db->query($select);
        $result = $stmt->fetchAll();
        $this->assertEquals(7, count($result));
        $this->assertEquals(1, $result[0]['id']);
    }

    public function testSerializeSelect()
    {
        /* checks if the adapter has effectively gotten serialized,
           no exceptions are thrown here, so it's all right */
        $serialize = serialize($this->_select());
        $this->assertType('string',$serialize);
    }

}
