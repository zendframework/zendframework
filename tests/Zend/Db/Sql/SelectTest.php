<?php
namespace ZendTest\Db\Sql;

use Zend\Db\Sql\Select,
    Zend\Db\Sql\Expression,
    Zend\Db\Sql\Where,
    Zend\Db\Adapter\ParameterContainer;

class SelectTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @testdox unit test: Test from() returns Select object (is chainable)
     * @covers Zend\Db\Sql\Select::from
     */
    public function testFrom()
    {
        $select = new Select;
        $return = $select->from('foo', 'bar');
        $this->assertSame($select, $return);
        return $return;
    }

    /**
     * @testdox unit test: Test getRawState() returns infromation populated via from()
     * @covers Zend\Db\Sql\Select::getRawState
     * @depends testFrom
     */
    public function testGetRawStateViaFrom(Select $select)
    {
        $this->assertEquals('foo', $select->getRawState('table'));
        $this->assertEquals('bar', $select->getRawState('schema'));
    }

    /**
     * @testdox unit test: Test columns() returns Select object (is chainable)
     * @covers Zend\Db\Sql\Select::columns
     */
    public function testColumns()
    {
        $select = new Select;
        $return = $select->columns(array('foo', 'bar'));
        $this->assertSame($select, $return);
        return $select;
    }

    /**
     * @testdox unit test: Test getRawState() returns information populated via columns()
     * @covers Zend\Db\Sql\Select::getRawState
     * @depends testColumns
     */
    public function testGetRawStateViaColumns(Select $select)
    {
        $this->assertEquals(array('foo', 'bar'), $select->getRawState('columns'));
    }

    /**
     * @testdox unit test: Test join() returns same Select object (is chainable)
     * @covers Zend\Db\Sql\Select::join
     */
    public function testJoin()
    {
        $select = new Select;
        $return = $select->join('foo', 'x = y', Select::SQL_WILDCARD, Select::JOIN_INNER);
        $this->assertSame($select, $return);
        return $return;
    }

    /**
     * @testdox unit test: Test getRawState() returns information populated via join()
     * @covers Zend\Db\Sql\Select::getRawState
     * @depends testJoin
     */
    public function testGetRawStateViaJoin(Select $select)
    {
        $this->assertEquals(
            array(array(
                'name' => 'foo',
                'on' => 'x = y',
                'columns' => array(Select::SQL_WILDCARD),
                'type' => Select::JOIN_INNER
            )),
            $select->getRawState('joins')
        );
    }

    /**
     * @testdox unit test: Test where() returns Select object (is chainable)
     * @covers Zend\Db\Sql\Select::where
     */
    public function testWhereReturnsSameSelectObject()
    {
        $select = new Select;
        $this->assertSame($select, $select->where('x = y'));
    }

    /**
     * @testdox unit test: Test where() will accept a string for the predicate to create an expression predicate
     * @covers Zend\Db\Sql\Select::where
     */
    public function testWhereArgument1IsString()
    {
        $select = new Select;
        $select->where('x = y');

        /** @var $where Where */
        $where = $select->getRawState('where');
        $predicates = $where->getPredicates();
        $this->assertEquals(1, count($predicates));
        $this->assertInstanceOf('Zend\Db\Sql\Predicate\Expression', $predicates[0][1]);
        $this->assertEquals(Where::OP_AND, $predicates[0][0]);
        $this->assertEquals('x = y', $predicates[0][1]->getExpression());
    }

    /**
     * @testdox unit test: Test where() will accept an array with a string key (containing ?) used as an expression with placeholder
     * @covers Zend\Db\Sql\Select::where
     */
    public function testWhereArgument1IsAssociativeArrayContainingReplacementCharacter()
    {
        $select = new Select;
        $select->where(array('foo > ?' => 5));

        /** @var $where Where */
        $where = $select->getRawState('where');
        $predicates = $where->getPredicates();
        $this->assertEquals(1, count($predicates));
        $this->assertInstanceOf('Zend\Db\Sql\Predicate\Expression', $predicates[0][1]);
        $this->assertEquals(Where::OP_AND, $predicates[0][0]);
        $this->assertEquals('foo > ?', $predicates[0][1]->getExpression());
        $this->assertEquals(array(5), $predicates[0][1]->getParameters());
    }

    /**
     * @testdox unit test: Test where() will accept any array with string key (without ?) to be used as Operator predicate
     * @covers Zend\Db\Sql\Select::where
     */
    public function testWhereArugment1IsAssociativeArrayNotContainingReplacementCharacter()
    {
        $select = new Select;
        $select->where(array('name' => 'Ralph', 'age' => 33));

        /** @var $where Where */
        $where = $select->getRawState('where');
        $predicates = $where->getPredicates();
        $this->assertEquals(2, count($predicates));

        $this->assertInstanceOf('Zend\Db\Sql\Predicate\Operator', $predicates[0][1]);
        $this->assertEquals(Where::OP_AND, $predicates[0][0]);
        $this->assertEquals('name', $predicates[0][1]->getLeft());
        $this->assertEquals('Ralph', $predicates[0][1]->getRight());

        $this->assertInstanceOf('Zend\Db\Sql\Predicate\Operator', $predicates[1][1]);
        $this->assertEquals(Where::OP_AND, $predicates[1][0]);
        $this->assertEquals('age', $predicates[1][1]->getLeft());
        $this->assertEquals(33, $predicates[1][1]->getRight());
    }

    /**
     * @testdox unit test: Test where() will accept an indexed array to be used by joining string expressions
     * @covers Zend\Db\Sql\Select::where
     */
    public function testWhereArugment1IsIndexedArray()
    {
        $select = new Select;
        $select->where(array('name = "Ralph"'));

        /** @var $where Where */
        $where = $select->getRawState('where');
        $predicates = $where->getPredicates();
        $this->assertEquals(1, count($predicates));

        $this->assertInstanceOf('Zend\Db\Sql\Predicate\Expression', $predicates[0][1]);
        $this->assertEquals(Where::OP_AND, $predicates[0][0]);
        $this->assertEquals('name = "Ralph"', $predicates[0][1]->getExpression());
    }

    /**
     * @testdox unit test: Test where() will accept an indexed array to be used by joining string expressions, combined by OR
     * @covers Zend\Db\Sql\Select::where
     */
    public function testWhereArugment1IsIndexedArrayArgument2IsOr()
    {
        $select = new Select;
        $select->where(array('name = "Ralph"'), Where::OP_OR);

        /** @var $where Where */
        $where = $select->getRawState('where');
        $predicates = $where->getPredicates();
        $this->assertEquals(1, count($predicates));

        $this->assertInstanceOf('Zend\Db\Sql\Predicate\Expression', $predicates[0][1]);
        $this->assertEquals(Where::OP_OR, $predicates[0][0]);
        $this->assertEquals('name = "Ralph"', $predicates[0][1]->getExpression());
    }

    /**
     * @testdox unit test: Test where() will accept a closure to be executed with Where object as argument
     * @covers Zend\Db\Sql\Select::where
     */
    public function testWhereArugment1IsClosure()
    {
        $select = new Select;
        $where = $select->getRawState('where');

        $test = $this;
        $select->where(function ($what) use ($test, $where) {
            $test->assertSame($where, $what);
        });
    }

    /**
     * @testdox unit test: Test where() will accept a Where object
     * @covers Zend\Db\Sql\Select::where
     */
    public function testWhereArugment1IsWhereObject()
    {
        $select = new Select;
        $select->where($newWhere = new Where);
        $this->assertSame($newWhere, $select->getRawState('where'));
    }

    /**
     * @testdox unit test: Test prepareStatement() will produce expected sql and parameters based on a variety of provided arguments [uses data provider]
     * @covers Zend\Db\Sql\Select::prepareStatement
     * @dataProvider providerForPrepareStatement
     */
    public function testPrepareStatement(Select $select, $expectedSqlString, $expectedParameters = array())
    {
        $mockDriver = $this->getMock('Zend\Db\Adapter\Driver\DriverInterface');
        $mockDriver->expects($this->any())->method('getPrepareType')->will($this->returnValue($mockDriver::PARAMETERIZATION_POSITIONAL));
        $mockDriver->expects($this->any())->method('formatParameterName')->will($this->returnValue('?'));
        $mockAdapter = $this->getMock('Zend\Db\Adapter\Adapter', null, array($mockDriver));

        $parameterContainer = new ParameterContainer();

        $mockStatement = $this->getMock('Zend\Db\Adapter\Driver\StatementInterface');
        $mockStatement->expects($this->any())->method('getParameterContainer')->will($this->returnValue($parameterContainer));
        $mockStatement->expects($this->any())->method('setSql')->with($this->equalTo($expectedSqlString));

        $select->prepareStatement($mockAdapter, $mockStatement);

        if ($expectedParameters) {
            $this->assertEquals($expectedParameters, $parameterContainer->toArray());
        }
    }

    public function providerForPrepareStatement()
    {
        // basic table
        $select0 = new Select;
        $select0->from('foo');
        $sql0 = 'SELECT * FROM "foo"';

        // table + schema
        $select1 = new Select();
        $select1->from('foo', 'bar');
        $sql1 = 'SELECT * FROM "bar"."foo"';

        // columns
        $select2 = new Select;
        $select2->from('foo')->columns(array('bar', 'baz'));
        $sql2 = 'SELECT "bar", "baz" FROM "foo"';

        // columns with column fragement (proper quoting)
        $select3 = new Select;
        $select3->from('foo')->columns(array('baz AS bar'));
        $sql3 = 'SELECT "baz" AS "bar" FROM "foo"';

        // columns with AS associative array
        $select4 = new Select;
        $select4->from('foo')->columns(array('bar' => 'baz'));
        $sql4 = 'SELECT "baz" AS "bar" FROM "foo"';

        // columns where value is Expression, with AS
        $select5 = new Select;
        $select5->from('foo')->columns(array('bar' => new Expression('COUNT(some_column)')));
        $sql5 = 'SELECT COUNT(some_column) AS "bar" FROM "foo"';

        // columns where value is Expression
        $select6 = new Select;
        $select6->from('foo')->columns(array(new Expression('COUNT(some_column) AS bar')));
        $sql6 = 'SELECT COUNT(some_column) AS bar FROM "foo"';

        // columns where value is Expression with parameters
        $select7 = new Select;
        $select7->from('foo')->columns(
            array(
                new Expression(
                    '(COUNT(?) + ?) AS ?',
                    array('some_column', 5, 'bar'),
                    array(Expression::TYPE_IDENTIFIER, Expression::TYPE_VALUE, Expression::TYPE_IDENTIFIER)
                )
            )
        );
        $sql7 = 'SELECT (COUNT("some_column") + ?) AS "bar" FROM "foo"';
        $params7 = array(5);

        // joins (plain)
        $select8 = new Select;
        $select8->from('foo')->join('zac', 'm = n');
        $sql8 = 'SELECT *, "zac".* FROM "foo" INNER JOIN "zac" ON "m" = "n"';

        // join with columns
        $select9 = new Select;
        $select9->from('foo')->join('zac', 'm = n', array('bar', 'baz'));
        $sql9 = 'SELECT *, "zac"."bar", "zac"."baz" FROM "foo" INNER JOIN "zac" ON "m" = "n"';

        // join with alternate type
        $select10 = new Select;
        $select10->from('foo')->join('zac', 'm = n', array('bar', 'baz'), Select::JOIN_OUTER);
        $sql10 = 'SELECT *, "zac"."bar", "zac"."baz" FROM "foo" OUTER JOIN "zac" ON "m" = "n"';

        // where (simple string)
        $select11 = new Select;
        $select11->from('foo')->where('x = 5');
        $sql11 = 'SELECT * FROM "foo" WHERE x = 5';

        // where (returning parameters)
        $select12 = new Select;
        $select12->from('foo')->where(array('x = ?' => 5));
        $sql12 = 'SELECT * FROM "foo" WHERE x = ?';
        $params12 = array(5);

        return array(
            array($select0, $sql0),
            array($select1, $sql1),
            array($select2, $sql2),
            array($select3, $sql3),
            array($select4, $sql4),
            array($select5, $sql5),
            array($select6, $sql6),
            array($select7, $sql7, $params7),
            array($select8, $sql8),
            array($select9, $sql9),
            array($select10, $sql10),
            array($select11, $sql11),
            array($select12, $sql12, $params12)
        );
    }

    /**
     * @testdox unit test: Test getSqlString() will produce expected sql and parameters based on a variety of provided arguments [uses data provider]
     * @covers Zend\Db\Sql\Select::getSqlString
     * @dataProvider providerForGetSqlString
     */
    public function testGetSqlString(Select $select, $expectedSqlString, $expectedParameters = array())
    {
        $this->assertEquals($expectedSqlString, $select->getSqlString());
    }

    public function providerForGetSqlString()
    {
        $data = $this->providerForPrepareStatement();

        // using prepare data, alter for use in getSqlString()

        $data[7][1] = 'SELECT (COUNT("some_column") + \'5\') AS "bar" FROM "foo"';
        unset($data[7][2]); // remove parameters

        $data[12][1] = 'SELECT * FROM "foo" WHERE x = \'5\'';
        unset($data[12][2]); // remove parameters

        return $data;
    }

    /**
     * @testdox unit test: Test __get() returns expected objects magically
     * @covers Zend\Db\Sql\Select::__get
     */
    public function test__get()
    {
        $select = new Select;
        $this->assertInstanceOf('Zend\Db\Sql\Where', $select->where);
    }

    /**
     * @testdox unit test: Test __clone() will clone the where object so that this select can be used in multiple contexts
     * @covers Zend\Db\Sql\Select::__clone
     */
    public function test__clone()
    {
        $select = new Select;
        $select1 = clone $select;
        $select1->where('id = foo');

        $this->assertEquals(0, $select->where->count());
        $this->assertEquals(1, $select1->where->count());
    }
}
