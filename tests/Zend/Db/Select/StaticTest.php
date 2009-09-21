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
 * @see Zend_Db_Select_TestCommon
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
class Zend_Db_Select_StaticTest extends Zend_Db_Select_TestCommon
{
    /**
     * Test basic use of the Zend_Db_Select class.
     *
     * @return void
     */
    public function testSelect()
    {
        $select = $this->_select();
        $sql = preg_replace('/\\s+/', ' ', $select->__toString());
        $this->assertEquals('SELECT "zfproducts".* FROM "zfproducts"', $sql);
    }

    /**
     * Test basic use of the Zend_Db_Select class.
     *
     * @return void
     */
    public function testSelectQuery()
    {
        $select = $this->_select();
        $sql = preg_replace('/\\s+/', ' ', $select->__toString());
        $this->assertEquals('SELECT "zfproducts".* FROM "zfproducts"', $sql);
        $stmt = $select->query();
        Zend_Loader::loadClass('Zend_Db_Statement_Static');
        $this->assertType('Zend_Db_Statement_Static', $stmt);
    }

    /**
     * ZF-2017: Test bind use of the Zend_Db_Select class.
     */
    public function testSelectQueryWithBinds()
    {
        $select = $this->_select()->where('product_id = :product_id')
                                  ->bind(array(':product_id' => 1));

        $sql = preg_replace('/\\s+/', ' ', $select->__toString());
        $this->assertEquals('SELECT "zfproducts".* FROM "zfproducts" WHERE (product_id = :product_id)', $sql);

        $stmt = $select->query();
        Zend_Loader::loadClass('Zend_Db_Statement_Static');
        $this->assertType('Zend_Db_Statement_Static', $stmt);
    }

    /**
     * Test Zend_Db_Select specifying columns
     *
     * @return void
     */
    public function testSelectColumnsScalar()
    {
        $select = $this->_selectColumnsScalar();
        $sql = preg_replace('/\\s+/', ' ', $select->__toString());
        $this->assertEquals('SELECT "zfproducts"."product_name" FROM "zfproducts"', $sql);
    }

    /**
     * Test Zend_Db_Select specifying columns
     *
     * @return void
     */
    public function testSelectColumnsArray()
    {
        $select = $this->_selectColumnsArray();
        $sql = preg_replace('/\\s+/', ' ', $select->__toString());
        $this->assertEquals('SELECT "zfproducts"."product_id", "zfproducts"."product_name" FROM "zfproducts"', $sql);
    }

    /**
     * Test support for column aliases.
     * e.g. from('table', array('alias' => 'col1')).
     *
     * @return void
     */
    public function testSelectColumnsAliases()
    {
        $select = $this->_selectColumnsAliases();
        $sql = preg_replace('/\\s+/', ' ', $select->__toString());
        $this->assertEquals('SELECT "zfproducts"."product_name" AS "alias" FROM "zfproducts"', $sql);
    }

    /**
     * Test syntax to support qualified column names,
     * e.g. from('table', array('table.col1', 'table.col2')).
     *
     * @return void
     */
    public function testSelectColumnsQualified()
    {
        $select = $this->_selectColumnsQualified();
        $sql = preg_replace('/\\s+/', ' ', $select->__toString());
        $this->assertEquals('SELECT "zfproducts"."product_name" FROM "zfproducts"', $sql);
    }

    /**
     * Test support for columns defined by Zend_Db_Expr.
     *
     * @return void
     */
    public function testSelectColumnsExpr()
    {
        $select = $this->_selectColumnsExpr();
        $sql = preg_replace('/\\s+/', ' ', $select->__toString());
        $this->assertEquals('SELECT "zfproducts"."product_name" FROM "zfproducts"', $sql);
    }

    /**
     * Test support for automatic conversion of SQL functions to
     * Zend_Db_Expr, e.g. from('table', array('COUNT(*)'))
     * should generate the same result as
     * from('table', array(new Zend_Db_Expr('COUNT(*)')))
     */

    public function testSelectColumnsAutoExpr()
    {
        $select = $this->_selectColumnsAutoExpr();
        $sql = preg_replace('/\\s+/', ' ', $select->__toString());
        $this->assertEquals('SELECT COUNT(*) AS "count" FROM "zfproducts"', $sql);
    }

    /**
     * Test adding the DISTINCT query modifier to a Zend_Db_Select object.
     */

    public function testSelectDistinctModifier()
    {
        $select = $this->_selectDistinctModifier();
        $sql = preg_replace('/\\s+/', ' ', $select->__toString());
        $this->assertEquals('SELECT DISTINCT 327 FROM "zfproducts"', $sql);
    }

    /**
     * Test support for schema-qualified table names in from()
     * e.g. from('schema.table').
     */

    public function testSelectFromQualified()
    {
        $select = $this->_selectFromQualified();
        $sql = preg_replace('/\\s+/', ' ', $select->__toString());
        $this->assertEquals('SELECT "zfproducts".* FROM "dummy"."zfproducts"', $sql);
    }

    public function testSelectColumnsReset()
    {
        $select = $this->_selectColumnsReset()
            ->reset(Zend_Db_Select::COLUMNS)
            ->columns('product_name');
        $sql = preg_replace('/\\s+/', ' ', $select->__toString());
        $this->assertEquals('SELECT "p"."product_name" FROM "zfproducts" AS "p"', $sql);
    }

    public function testSelectFromForUpdate()
    {
        $select = $this->_db->select()
            ->from("zfproducts")
            ->forUpdate();
        $sql = preg_replace('/\\s+/', ' ', $select->__toString());
        $this->assertEquals('SELECT "zfproducts".* FROM "zfproducts" FOR UPDATE', $sql);
    }

    /**
     * Test adding a JOIN to a Zend_Db_Select object.
     */

    public function testSelectJoin()
    {
        $select = $this->_selectJoin();
        $sql = preg_replace('/\\s+/', ' ', $select->__toString());
        $this->assertEquals('SELECT "zfproducts".*, "zfbugs_products".* FROM "zfproducts" INNER JOIN "zfbugs_products" ON "zfproducts"."product_id" = "zfbugs_products"."product_id"', $sql);
    }

    /**
     * Test adding an INNER JOIN to a Zend_Db_Select object.
     * This should be exactly the same as the plain JOIN clause.
     */

    public function testSelectJoinWithCorrelationName()
    {
        $select = $this->_selectJoinWithCorrelationName();
        $sql = preg_replace('/\\s+/', ' ', $select->__toString());
        $this->assertEquals('SELECT "xyz1".*, "xyz2".* FROM "zfproducts" AS "xyz1" INNER JOIN "zfbugs_products" AS "xyz2" ON "xyz1"."product_id" = "xyz2"."product_id" WHERE ("xyz1"."product_id" = 1)', $sql);
    }

    /**
     * Test adding an INNER JOIN to a Zend_Db_Select object.
     * This should be exactly the same as the plain JOIN clause.
     */

    public function testSelectJoinInner()
    {
        $select = $this->_selectJoinInner();
        $sql = preg_replace('/\\s+/', ' ', $select->__toString());
        $this->assertEquals('SELECT "zfproducts".*, "zfbugs_products".* FROM "zfproducts" INNER JOIN "zfbugs_products" ON "zfproducts"."product_id" = "zfbugs_products"."product_id"', $sql);
    }

    /**
     * Test adding an outer join to a Zend_Db_Select object.
     */

    public function testSelectJoinLeft()
    {
        $select = $this->_selectJoinLeft();
        $sql = preg_replace('/\\s+/', ' ', $select->__toString());
        $this->assertEquals('SELECT "zfbugs".*, "zfbugs_products".* FROM "zfbugs" LEFT JOIN "zfbugs_products" ON "zfbugs"."bug_id" = "zfbugs_products"."bug_id"', $sql);
    }

    /**
     * Test adding an outer join to a Zend_Db_Select object.
     */

    public function testSelectJoinRight()
    {
        $select = $this->_selectJoinRight();
        $sql = preg_replace('/\\s+/', ' ', $select->__toString());
        $this->assertEquals('SELECT "zfbugs_products".*, "zfbugs".* FROM "zfbugs_products" RIGHT JOIN "zfbugs" ON "zfbugs_products"."bug_id" = "zfbugs"."bug_id"', $sql);
    }

    /**
     * Test adding a cross join to a Zend_Db_Select object.
     */

    public function testSelectJoinCross()
    {
        $select = $this->_selectJoinCross();
        $sql = preg_replace('/\\s+/', ' ', $select->__toString());
        $this->assertEquals('SELECT "zfproducts".*, "zfbugs_products".* FROM "zfproducts" CROSS JOIN "zfbugs_products"', $sql);
    }

    /**
     * Test support for schema-qualified table names in join(),
     * e.g. join('schema.table', 'condition')
     */

    public function testSelectJoinQualified()
    {
        $select = $this->_selectJoinQualified();
        $sql = preg_replace('/\\s+/', ' ', $select->__toString());
        $this->assertEquals('SELECT "zfproducts".*, "zfbugs_products".* FROM "zfproducts" INNER JOIN "dummy"."zfbugs_products" ON "zfproducts"."product_id" = "zfbugs_products"."product_id"', $sql);
    }

    /**
     * Test adding a JOIN USING to a Zend_Db_Select object.
     */

    public function testSelectJoinUsing()
    {
        $select = $this->_selectJoinUsing();
        $sql = preg_replace('/\\s+/', ' ', $select->__toString());
        $this->assertEquals('SELECT "zfproducts".*, "zfbugs_products".* FROM "zfproducts" INNER JOIN "zfbugs_products" ON "zfbugs_products"."product_id" = "zfproducts"."product_id" WHERE ("zfbugs_products"."product_id" < 3)', $sql);
    }

    /**
     * Test adding a JOIN INNER USING to a Zend_Db_Select object.
     */

    public function testSelectJoinInnerUsing()
    {
        $select = $this->_selectJoinInnerUsing();
        $sql = preg_replace('/\\s+/', ' ', $select->__toString());
        $this->assertEquals('SELECT "zfproducts".*, "zfbugs_products".* FROM "zfproducts" INNER JOIN "zfbugs_products" ON "zfbugs_products"."product_id" = "zfproducts"."product_id" WHERE ("zfbugs_products"."product_id" < 3)', $sql);
    }

    public function testSelectJoinWithNocolumns()
    {
        $select = $this->_selectJoinWithNocolumns();
        $sql = preg_replace('/\\s+/', ' ', $select->__toString());
        $this->assertEquals('SELECT "zfproducts".* FROM "zfproducts" INNER JOIN "zfbugs" ON "zfbugs"."bug_id" = 1 INNER JOIN "zfbugs_products" ON "zfproducts"."product_id" = "zfbugs_products"."product_id" AND "zfbugs_products"."bug_id" = "zfbugs"."bug_id"', $sql);
    }

    /**
     * Test adding a WHERE clause to a Zend_Db_Select object.
     */

    public function testSelectWhere()
    {
        $select = $this->_selectWhere();
        $sql = preg_replace('/\\s+/', ' ', $select->__toString());
        $this->assertEquals('SELECT "zfproducts".* FROM "zfproducts" WHERE ("product_id" = 2)', $sql);
    }

    /**
     * Test adding an array in the WHERE clause to a Zend_Db_Select object.
     */

    public function testSelectWhereArray()
    {
        $select = $this->_selectWhereArray();
        $sql = preg_replace('/\\s+/', ' ', $select->__toString());
        $this->assertEquals('SELECT "zfproducts".* FROM "zfproducts" WHERE ("product_id" IN (1, 2, 3))', $sql);
    }

    /**
     * test adding more WHERE conditions,
     * which should be combined with AND by default.
     */

    public function testSelectWhereAnd()
    {
        $select = $this->_selectWhereAnd();
        $sql = preg_replace('/\\s+/', ' ', $select->__toString());
        $this->assertEquals('SELECT "zfproducts".* FROM "zfproducts" WHERE ("product_id" = 2) AND ("product_id" = 1)', $sql);
    }

    /**
     * Test support for where() with a parameter,
     * e.g. where('id = ?', 1).
     */

    public function testSelectWhereWithParameter()
    {
        $select = $this->_selectWhereWithParameter();
        $sql = preg_replace('/\\s+/', ' ', $select->__toString());
        $this->assertEquals('SELECT "zfproducts".* FROM "zfproducts" WHERE ("product_id" = 2)', $sql);
    }

    /**
     * Test support for where() with a parameter,
     * e.g. where('id = ?', 1).
     */

    public function testSelectWhereWithType()
    {
        $select = $this->_selectWhereWithType();
        $sql = preg_replace('/\\s+/', ' ', $select->__toString());
        $this->assertEquals('SELECT "zfproducts".* FROM "zfproducts" WHERE ("product_id" = 2)', $sql);
    }

    /**
     * Test support for where() with a float parameter,
     * e.g. where('id = ?', 1).
     */

    public function testSelectWhereWithTypeFloat()
    {
        $select = $this->_selectWhereWithTypeFloat();
        $sql = preg_replace('/\\s+/', ' ', $select->__toString());
        $this->assertEquals('SELECT "zfprice".* FROM "zfprice" WHERE ("price_total" = 200.450000)', $sql);
    }

    /**
     *      * Test adding an OR WHERE clause to a Zend_Db_Select object.
     */

    public function testSelectWhereOr()
    {
        $select = $this->_selectWhereOr();
        $sql = preg_replace('/\\s+/', ' ', $select->__toString());
        $this->assertEquals('SELECT "zfproducts".* FROM "zfproducts" WHERE ("product_id" = 1) OR ("product_id" = 2)', $sql);
    }

    /**
     * Test support for where() with a parameter,
     * e.g. orWhere('id = ?', 2).
     */

    public function testSelectWhereOrWithParameter()
    {
        $select = $this->_selectWhereOrWithParameter();
        $sql = preg_replace('/\\s+/', ' ', $select->__toString());
        $this->assertEquals('SELECT "zfproducts".* FROM "zfproducts" WHERE ("product_id" = 1) OR ("product_id" = 2)', $sql);
    }

    /**
     * Test adding a GROUP BY clause to a Zend_Db_Select object.
     */

    public function testSelectGroupBy()
    {
        $select = $this->_selectGroupBy();
        $sql = preg_replace('/\\s+/', ' ', $select->__toString());
        $this->assertEquals('SELECT "zfbugs_products"."bug_id", COUNT(*) AS "thecount" FROM "zfbugs_products" GROUP BY "bug_id" ORDER BY "bug_id" ASC', $sql);
    }

    /**
     * Test support for qualified table in group(),
     * e.g. group('schema.table').
     */

    public function testSelectGroupByQualified()
    {
        $select = $this->_selectGroupByQualified();
        $sql = preg_replace('/\\s+/', ' ', $select->__toString());
        $this->assertEquals('SELECT "zfbugs_products"."bug_id", COUNT(*) AS "thecount" FROM "zfbugs_products" GROUP BY "zfbugs_products"."bug_id" ORDER BY "bug_id" ASC', $sql);
    }

    /**
     * Test support for Zend_Db_Expr in group(),
     * e.g. group(new Zend_Db_Expr('id+1'))
     */

    public function testSelectGroupByExpr()
    {
        $select = $this->_selectGroupByExpr();
        $sql = preg_replace('/\\s+/', ' ', $select->__toString());
        $this->assertEquals('SELECT "bug_id"+1 AS "bug_id", COUNT(*) AS "thecount" FROM "zfbugs_products" GROUP BY "bug_id"+1 ORDER BY "bug_id"+1', $sql);
    }

    /**
     * Test support for automatic conversion of a SQL
     * function to a Zend_Db_Expr in group(),
     * e.g.  group('LOWER(title)') should give the same
     * result as group(new Zend_Db_Expr('LOWER(title)')).
     */


    public function testSelectGroupByAutoExpr()
    {
        $select = $this->_selectGroupByAutoExpr();
        $sql = preg_replace('/\\s+/', ' ', $select->__toString());
        $this->assertEquals('SELECT ABS("zfbugs_products"."bug_id") AS "bug_id", COUNT(*) AS "thecount" FROM "zfbugs_products" GROUP BY ABS("zfbugs_products"."bug_id") ORDER BY ABS("zfbugs_products"."bug_id") ASC', $sql);
    }

    /**
     * Test adding a HAVING clause to a Zend_Db_Select object.
     */

    public function testSelectHaving()
    {
        $select = $this->_selectHaving();
        $sql = preg_replace('/\\s+/', ' ', $select->__toString());
        $this->assertEquals('SELECT "zfbugs_products"."bug_id", COUNT(*) AS "thecount" FROM "zfbugs_products" GROUP BY "bug_id" HAVING (COUNT(*) > 1) ORDER BY "bug_id" ASC', $sql);
    }


    public function testSelectHavingAnd()
    {
        $select = $this->_selectHavingAnd();
        $sql = preg_replace('/\\s+/', ' ', $select->__toString());
        $this->assertEquals('SELECT "zfbugs_products"."bug_id", COUNT(*) AS "thecount" FROM "zfbugs_products" GROUP BY "bug_id" HAVING (COUNT(*) > 1) AND (COUNT(*) = 1) ORDER BY "bug_id" ASC', $sql);
    }

    /**
     * Test support for parameter in having(),
     * e.g. having('count(*) > ?', 1).
     */


    public function testSelectHavingWithParameter()
    {
        $select = $this->_selectHavingWithParameter();
        $sql = preg_replace('/\\s+/', ' ', $select->__toString());
        $this->assertEquals('SELECT "zfbugs_products"."bug_id", COUNT(*) AS "thecount" FROM "zfbugs_products" GROUP BY "bug_id" HAVING (COUNT(*) > 1) ORDER BY "bug_id" ASC', $sql);
    }

    /**
     * Test adding a HAVING clause to a Zend_Db_Select object.
     */


    public function testSelectHavingOr()
    {
        $select = $this->_selectHavingOr();
        $sql = preg_replace('/\\s+/', ' ', $select->__toString());
        $this->assertEquals('SELECT "zfbugs_products"."bug_id", COUNT(*) AS "thecount" FROM "zfbugs_products" GROUP BY "bug_id" HAVING (COUNT(*) > 1) OR (COUNT(*) = 1) ORDER BY "bug_id" ASC', $sql);
    }

    /**
     * Test support for parameter in orHaving(),
     * e.g. orHaving('count(*) > ?', 1).
     */

    public function testSelectHavingOrWithParameter()
    {
        $select = $this->_selectHavingOrWithParameter();
        $sql = preg_replace('/\\s+/', ' ', $select->__toString());
        $this->assertEquals('SELECT "zfbugs_products"."bug_id", COUNT(*) AS "thecount" FROM "zfbugs_products" GROUP BY "bug_id" HAVING (COUNT(*) > 1) OR (COUNT(*) = 1) ORDER BY "bug_id" ASC', $sql);
    }

    /**
     * Test adding an ORDER BY clause to a Zend_Db_Select object.
     */

    public function testSelectOrderBy()
    {
        $select = $this->_selectOrderBy();
        $sql = preg_replace('/\\s+/', ' ', $select->__toString());
        $this->assertEquals('SELECT "zfproducts".* FROM "zfproducts" ORDER BY "product_id" ASC', $sql);
    }


    public function testSelectOrderByArray()
    {
        $select = $this->_selectOrderByArray();
        $sql = preg_replace('/\\s+/', ' ', $select->__toString());
        $this->assertEquals('SELECT "zfproducts".* FROM "zfproducts" ORDER BY "product_name" ASC, "product_id" ASC', $sql);
    }


    public function testSelectOrderByAsc()
    {
        $select = $this->_selectOrderByAsc();
        $sql = preg_replace('/\\s+/', ' ', $select->__toString());
        $this->assertEquals('SELECT "zfproducts".* FROM "zfproducts" ORDER BY "product_id" ASC', $sql);
    }


    public function testSelectOrderByDesc()
    {
        $select = $this->_selectOrderByDesc();
        $sql = preg_replace('/\\s+/', ' ', $select->__toString());
        $this->assertEquals('SELECT "zfproducts".* FROM "zfproducts" ORDER BY "product_id" DESC', $sql);
    }

    /**
     * Test support for qualified table in order(),
     * e.g. order('schema.table').
     */

    public function testSelectOrderByQualified()
    {
        $select = $this->_selectOrderByQualified();
        $sql = preg_replace('/\\s+/', ' ', $select->__toString());
        $this->assertEquals('SELECT "zfproducts".* FROM "zfproducts" ORDER BY "zfproducts"."product_id" ASC', $sql);
    }

    /**
     * Test support for Zend_Db_Expr in order(),
     * e.g. order(new Zend_Db_Expr('id+1')).
     */

    public function testSelectOrderByExpr()
    {
        $select = $this->_selectOrderByExpr();
        $sql = preg_replace('/\\s+/', ' ', $select->__toString());
        $this->assertEquals('SELECT "zfproducts".* FROM "zfproducts" ORDER BY 1', $sql);
    }

    /**
     * Test automatic conversion of SQL functions to
     * Zend_Db_Expr, e.g. order('LOWER(title)')
     * should give the same result as
     * order(new Zend_Db_Expr('LOWER(title)')).
     */

    public function testSelectOrderByAutoExpr()
    {
        $select = $this->_selectOrderByAutoExpr();
        $sql = preg_replace('/\\s+/', ' ', $select->__toString());
        $this->assertEquals('SELECT "zfproducts".* FROM "zfproducts" ORDER BY ABS("zfproducts"."product_id") ASC', $sql);
    }

    /**
     * Test ORDER BY clause that contains multiple lines.
     * See ZF-1822, which says that the regexp matching
     * ASC|DESC fails when string is multi-line.
     */

    public function testSelectOrderByMultiLine()
    {
        $select = $this->_selectOrderByMultiLine();
        $sql = preg_replace('/\\s+/', ' ', $select->__toString());
        $this->assertEquals('SELECT "zfproducts".* FROM "zfproducts" ORDER BY "product_id" DESC', $sql);
    }

    /**
     * Test adding a LIMIT clause to a Zend_Db_Select object.
     */

    public function testSelectLimit()
    {
        $select = $this->_selectLimit();
        $sql = preg_replace('/\\s+/', ' ', $select->__toString());
        $this->assertEquals('SELECT "zfproducts".* FROM "zfproducts" ORDER BY "product_id" ASC LIMIT 1 OFFSET 0', $sql);
    }

    /**
     * Not applicable in static test
     * @group ZF-5263
     */
    public function testSelectLimitFetchCol()
    {}

    public function testSelectLimitNone()
    {
        $select = $this->_selectLimitNone();
        $sql = preg_replace('/\\s+/', ' ', $select->__toString());
        $this->assertEquals('SELECT "zfproducts".* FROM "zfproducts" ORDER BY "product_id" ASC', $sql);
    }


    public function testSelectLimitOffset()
    {
        $select = $this->_selectLimitOffset();
        $sql = preg_replace('/\\s+/', ' ', $select->__toString());
        $this->assertEquals('SELECT "zfproducts".* FROM "zfproducts" ORDER BY "product_id" ASC LIMIT 1 OFFSET 1', $sql);
    }

    /**
     * Test the limitPage() method of a Zend_Db_Select object.
     */

    public function testSelectLimitPageOne()
    {
        $select = $this->_selectLimitPageOne();
        $sql = preg_replace('/\\s+/', ' ', $select->__toString());
        $this->assertEquals('SELECT "zfproducts".* FROM "zfproducts" ORDER BY "product_id" ASC LIMIT 1 OFFSET 0', $sql);
    }


    public function testSelectLimitPageTwo()
    {
        $select = $this->_selectLimitPageTwo();
        $sql = preg_replace('/\\s+/', ' ', $select->__toString());
        $this->assertEquals('SELECT "zfproducts".* FROM "zfproducts" ORDER BY "product_id" ASC LIMIT 1 OFFSET 1', $sql);
    }

    public function testSelectUnionString()
    {
        $select = $this->_selectUnionString();
        $sql = preg_replace('/\\s+/', ' ', $select->__toString());
        $this->assertEquals('SELECT "bug_id" AS "id", "bug_status" AS "name" FROM "zfbugs" UNION SELECT "product_id" AS "id", "product_name" AS "name" FROM "zfproducts" ORDER BY "id" ASC', $sql);
    }
    
    public function testSelectOrderByPosition()
    {
        $select = $this->_selectOrderByPosition(); 
        
        $sql = preg_replace('/\\s+/', ' ', $select->__toString());
        $this->assertEquals('SELECT "zfproducts".* FROM "zfproducts" ORDER BY 2 ASC', $sql);
    }

    public function testSelectOrderByPositionAsc()
    {
        $select = $this->_selectOrderByPositionAsc(); 
        
        $sql = preg_replace('/\\s+/', ' ', $select->__toString());
        $this->assertEquals('SELECT "zfproducts".* FROM "zfproducts" ORDER BY 2 ASC', $sql);
    }

    public function testSelectOrderByPositionDesc()
    {
        $select = $this->_selectOrderByPositionDesc();

        $sql = preg_replace('/\\s+/', ' ', $select->__toString());
        $this->assertEquals('SELECT "zfproducts".* FROM "zfproducts" ORDER BY 2 DESC', $sql);
    }

    public function testSelectOrderByMultiplePositions()
    {
        $select = $this->_selectOrderByMultiplePositions();

        $sql = preg_replace('/\\s+/', ' ', $select->__toString());
        $this->assertEquals('SELECT "zfproducts".* FROM "zfproducts" ORDER BY 2 DESC, 1 DESC', $sql);
    }
    

    public function getDriver()
    {
        return 'Static';
    }

}
