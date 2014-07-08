<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Db\Sql\Platform\IbmDb2;

use Zend\Db\Sql\Platform\IbmDb2\SelectDecorator;
use Zend\Db\Sql\Select;
use Zend\Db\Adapter\ParameterContainer;
use Zend\Db\Adapter\Platform\IbmDb2 as IbmDb2Platform;

class SelectDecoratorTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @testdox integration test: Testing SelectDecorator will use Select to produce properly IBM Db2 dialect prepared sql
     * @covers Zend\Db\Sql\Platform\SqlServer\SelectDecorator::prepareStatement
     * @covers Zend\Db\Sql\Platform\SqlServer\SelectDecorator::processLimitOffset
     * @dataProvider dataProvider
     */
    public function testPrepareStatement(Select $select, $notUsed, $expectedParams, $expectedSql)
    {
        $driver = $this->getMock('Zend\Db\Adapter\Driver\DriverInterface');

        // test
        $adapter = $this->getMock(
            'Zend\Db\Adapter\Adapter',
            null,
            array(
                $driver,
                new IbmDb2Platform()
            )
        );

        $parameterContainer = new ParameterContainer;
        $statement = $this->getMock('Zend\Db\Adapter\Driver\StatementInterface');
        $statement->expects($this->any())->method('getParameterContainer')->will($this->returnValue($parameterContainer));

        $statement->expects($this->once())->method('setSql')->with($expectedSql);

        $selectDecorator = new SelectDecorator;
        $selectDecorator->setSubject($select);
        $selectDecorator->prepareStatement($adapter, $statement);

        $this->assertEquals($expectedParams, $parameterContainer->getNamedArray());
    }

    /**
     * @testdox integration test: Testing SelectDecorator will use Select to produce properly Ibm DB2 dialect sql statements
     * @covers Zend\Db\Sql\Platform\IbmDb2\SelectDecorator::getSqlString
     * @dataProvider dataProvider
     */
    public function testGetSqlString(Select $select, $notUsed, $notUsed, $expectedSql)
    {
        $parameterContainer = new ParameterContainer;
        $statement = $this->getMock('Zend\Db\Adapter\Driver\StatementInterface');
        $statement->expects($this->any())->method('getParameterContainer')->will($this->returnValue($parameterContainer));

        $selectDecorator = new SelectDecorator;
        $selectDecorator->setSubject($select);
        $this->assertEquals($expectedSql, $selectDecorator->getSqlString(new IbmDb2Platform));
    }

    /**
     * Data provider for testGetSqlString
     *
     * @return array
     */
    public function dataProvider()
    {
        $select0 = new Select;
        $select0->from(array('x' => 'foo'))->limit(5);
        $expectedPrepareSql0 = 'SELECT "x".* FROM "foo" AS "x" FETCH FIRST ? ROW ONLY';
        $expectedParams0 = array('limit' => 5);
        $expectedSql0 = 'SELECT "x".* FROM "foo" AS "x" FETCH FIRST 5 ROW ONLY';

        $select1 = new Select;
        $select1->from(array('x' => 'foo'))->limit(5)->offset(10);
        $expectedPrepareSql1 = 'SELECT z2.* FROM (SELECT ROW_NUMBER() OVER() AS "ZEND_ROWNUM", z1.* FROM (SELECT "x".* FROM "foo" AS "x") z1) z2 WHERE z2.ZEND_ROWNUM BETWEEN ? AND ?';
        $expectedParams1 = array('offset' => 10, 'limit' => 5);
        $expectedSql1 = 'SELECT z2.* FROM (SELECT ROW_NUMBER() OVER() AS "ZEND_ROWNUM", z1.* FROM (SELECT "x".* FROM "foo" AS "x") z1) z2 WHERE z2.ZEND_ROWNUM BETWEEN 10 AND 15';

        $select2 = new Select;
        $select2->from(array('x' => 'foo'))->limit(5)->offset(10)->quantifier(Select::QUANTIFIER_DISTINCT);
        $expectedPrepareSql2 = 'SELECT z2.* FROM (SELECT DENSE_RANK() OVER() AS "ZEND_ROWNUM", z1.* FROM (SELECT DISTINCT "x".* FROM "foo" AS "x") z1) z2 WHERE z2.ZEND_ROWNUM BETWEEN ? AND ?';
        $expectedParams2 = array('offset' => 10, 'limit' => 5);
        $expectedSql2 = 'SELECT z2.* FROM (SELECT DENSE_RANK() OVER() AS "ZEND_ROWNUM", z1.* FROM (SELECT DISTINCT "x".* FROM "foo" AS "x") z1) z2 WHERE z2.ZEND_ROWNUM BETWEEN 10 AND 15';

        return array(
            array($select0, $expectedPrepareSql0, $expectedParams0, $expectedSql0),
            array($select1, $expectedPrepareSql1, $expectedParams1, $expectedSql1),
            array($select2, $expectedPrepareSql2, $expectedParams2, $expectedSql2)
        );
    }

}
