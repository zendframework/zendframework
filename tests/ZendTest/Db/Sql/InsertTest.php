<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Db\Sql;

use Zend\Db\Sql\Insert;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Expression;
use Zend\Db\Sql\TableIdentifier;
use ZendTest\Db\TestAsset\TrustingSql92Platform;

class InsertTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Insert
     */
    protected $insert;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->insert = new Insert;
    }

    /**
     * @covers Zend\Db\Sql\Insert::into
     */
    public function testInto()
    {
        $this->insert->into('table', 'schema');
        $this->assertEquals('table', $this->insert->getRawState('table'));

        $tableIdentifier = new TableIdentifier('table', 'schema');
        $this->insert->into($tableIdentifier);
        $this->assertEquals($tableIdentifier, $this->insert->getRawState('table'));
    }

    /**
     * @covers Zend\Db\Sql\Insert::columns
     */
    public function testColumns()
    {
        $this->insert->columns(array('foo', 'bar'));
        $this->assertEquals(array('foo', 'bar'), $this->insert->getRawState('columns'));
    }

    /**
     * @covers Zend\Db\Sql\Insert::values
     */
    public function testValues()
    {
        $this->insert->values(array('foo' => 'bar'));
        $this->assertEquals(array('foo'), $this->insert->getRawState('columns'));
        $this->assertEquals(array('bar'), $this->insert->getRawState('values'));

        // test will merge cols and values of previously set stuff
        $this->insert->values(array('foo' => 'bax'), Insert::VALUES_MERGE);
        $this->insert->values(array('boom' => 'bam'), Insert::VALUES_MERGE);
        $this->assertEquals(array('foo', 'boom'), $this->insert->getRawState('columns'));
        $this->assertEquals(array('bax', 'bam'), $this->insert->getRawState('values'));

        $this->insert->values(array('foo' => 'bax'));
        $this->assertEquals(array('foo'), $this->insert->getRawState('columns'));
        $this->assertEquals(array('bax'), $this->insert->getRawState('values'));
    }

    /**
     * @covers Zend\Db\Sql\Insert::values
     */
    public function testValuesThrowsExceptionWhenNotArrayOrSelect()
    {
        $this->setExpectedException(
            'Zend\Db\Sql\Exception\InvalidArgumentException',
            'values() expects an array of values or Zend\Db\Sql\Select instance'
        );
        $this->insert->values(5);
    }

    /**
     * @covers Zend\Db\Sql\Insert::values
     */
    public function testValuesThrowsExceptionWhenSelectMergeOverArray()
    {
        $this->insert->values(array('foo' => 'bar'));

        $this->setExpectedException(
            'Zend\Db\Sql\Exception\InvalidArgumentException',
            'A Zend\Db\Sql\Select instance cannot be provided with the merge flag'
        );
        $this->insert->values(new Select, Insert::VALUES_MERGE);
    }

    /**
     * @covers Zend\Db\Sql\Insert::values
     */
    public function testValuesThrowsExceptionWhenArrayMergeOverSelect()
    {
        $this->insert->values(new Select);

        $this->setExpectedException(
            'Zend\Db\Sql\Exception\InvalidArgumentException',
            'An array of values cannot be provided with the merge flag when a Zend\Db\Sql\Select instance already exists as the value source'
        );
        $this->insert->values(array('foo' => 'bar'), Insert::VALUES_MERGE);
    }

    /**
     * @covers Zend\Db\Sql\Insert::values
     * @group ZF2-4926
     */
    public function testEmptyArrayValues()
    {
        $this->insert->values(array());
        $this->assertEquals(array(), $this->readAttribute($this->insert, 'columns'));
    }

    /**
     * @covers Zend\Db\Sql\Insert::prepareStatement
     */
    public function testPrepareStatement()
    {
        $mockDriver = $this->getMock('Zend\Db\Adapter\Driver\DriverInterface');
        $mockDriver->expects($this->any())->method('getPrepareType')->will($this->returnValue('positional'));
        $mockDriver->expects($this->any())->method('formatParameterName')->will($this->returnValue('?'));
        $mockAdapter = $this->getMock('Zend\Db\Adapter\Adapter', null, array($mockDriver));

        $mockStatement = $this->getMock('Zend\Db\Adapter\Driver\StatementInterface');
        $pContainer = new \Zend\Db\Adapter\ParameterContainer(array());
        $mockStatement->expects($this->any())->method('getParameterContainer')->will($this->returnValue($pContainer));
        $mockStatement->expects($this->at(1))
            ->method('setSql')
            ->with($this->equalTo('INSERT INTO "foo" ("bar", "boo") VALUES (?, NOW())'));

        $this->insert->into('foo')
            ->values(array('bar' => 'baz', 'boo' => new Expression('NOW()')));

        $this->insert->prepareStatement($mockAdapter, $mockStatement);

        // with TableIdentifier
        $this->insert = new Insert;
        $mockDriver = $this->getMock('Zend\Db\Adapter\Driver\DriverInterface');
        $mockDriver->expects($this->any())->method('getPrepareType')->will($this->returnValue('positional'));
        $mockDriver->expects($this->any())->method('formatParameterName')->will($this->returnValue('?'));
        $mockAdapter = $this->getMock('Zend\Db\Adapter\Adapter', null, array($mockDriver));

        $mockStatement = $this->getMock('Zend\Db\Adapter\Driver\StatementInterface');
        $pContainer = new \Zend\Db\Adapter\ParameterContainer(array());
        $mockStatement->expects($this->any())->method('getParameterContainer')->will($this->returnValue($pContainer));
        $mockStatement->expects($this->at(1))
            ->method('setSql')
            ->with($this->equalTo('INSERT INTO "sch"."foo" ("bar", "boo") VALUES (?, NOW())'));

        $this->insert->into(new TableIdentifier('foo', 'sch'))
            ->values(array('bar' => 'baz', 'boo' => new Expression('NOW()')));

        $this->insert->prepareStatement($mockAdapter, $mockStatement);
    }

    /**
     * @covers Zend\Db\Sql\Insert::prepareStatement
     */
    public function testPrepareStatementWithSelect()
    {
        $mockDriver = $this->getMock('Zend\Db\Adapter\Driver\DriverInterface');
        $mockDriver->expects($this->any())->method('getPrepareType')->will($this->returnValue('positional'));
        $mockDriver->expects($this->any())->method('formatParameterName')->will($this->returnValue('?'));
        $mockAdapter = $this->getMock('Zend\Db\Adapter\Adapter', null, array($mockDriver));

        $mockStatement = new \Zend\Db\Adapter\StatementContainer();

        $select = new Select('bar');
        $this->insert
                ->into('foo')
                ->columns(array('col1'))
                ->select($select->where(array('x'=>5)))
                ->prepareStatement($mockAdapter, $mockStatement);

        $this->assertEquals(
            'INSERT INTO "foo" ("col1") SELECT "bar".* FROM "bar" WHERE "x" = ?',
            $mockStatement->getSql()
        );
        $parameters = $mockStatement->getParameterContainer()->getNamedArray();
        $this->assertSame(array('subselect1where1'=>5), $parameters);
    }

    /**
     * @covers Zend\Db\Sql\Insert::getSqlString
     */
    public function testGetSqlString()
    {
        $this->insert->into('foo')
            ->values(array('bar' => 'baz', 'boo' => new Expression('NOW()'), 'bam' => null));

        $this->assertEquals('INSERT INTO "foo" ("bar", "boo", "bam") VALUES (\'baz\', NOW(), NULL)', $this->insert->getSqlString(new TrustingSql92Platform()));

        // with TableIdentifier
        $this->insert = new Insert;
        $this->insert->into(new TableIdentifier('foo', 'sch'))
            ->values(array('bar' => 'baz', 'boo' => new Expression('NOW()'), 'bam' => null));

        $this->assertEquals('INSERT INTO "sch"."foo" ("bar", "boo", "bam") VALUES (\'baz\', NOW(), NULL)', $this->insert->getSqlString(new TrustingSql92Platform()));

        // with Select
        $this->insert = new Insert;
        $select = new Select();
        $this->insert->into('foo')->select($select->from('bar'));

        $this->assertEquals('INSERT INTO "foo"  SELECT "bar".* FROM "bar"', $this->insert->getSqlString(new TrustingSql92Platform()));

        // with Select and columns
        $this->insert->columns(array('col1', 'col2'));

        $this->assertEquals('INSERT INTO "foo" ("col1", "col2") SELECT "bar".* FROM "bar"', $this->insert->getSqlString(new TrustingSql92Platform()));
    }

    /**
     * @covers Zend\Db\Sql\Insert::__set
     */
    public function test__set()
    {
        $this->insert->foo = 'bar';
        $this->assertEquals(array('foo'), $this->insert->getRawState('columns'));
        $this->assertEquals(array('bar'), $this->insert->getRawState('values'));
    }

    /**
     * @covers Zend\Db\Sql\Insert::__unset
     */
    public function test__unset()
    {
        $this->insert->foo = 'bar';
        $this->assertEquals(array('foo'), $this->insert->getRawState('columns'));
        $this->assertEquals(array('bar'), $this->insert->getRawState('values'));
        unset($this->insert->foo);
        $this->assertEquals(array(), $this->insert->getRawState('columns'));
        $this->assertEquals(array(), $this->insert->getRawState('values'));
    }

    /**
     * @covers Zend\Db\Sql\Insert::__isset
     */
    public function test__isset()
    {
        $this->insert->foo = 'bar';
        $this->assertTrue(isset($this->insert->foo));
    }

    /**
     * @covers Zend\Db\Sql\Insert::__get
     */
    public function test__get()
    {
        $this->insert->foo = 'bar';
        $this->assertEquals('bar', $this->insert->foo);
    }

    /**
     * @group ZF2-536
     */
    public function testValuesMerge()
    {
        $this->insert->into('foo')
            ->values(array('bar' => 'baz', 'boo' => new Expression('NOW()'), 'bam' => null));
        $this->insert->into('foo')
            ->values(array('qux' => 100), Insert::VALUES_MERGE);

        $this->assertEquals('INSERT INTO "foo" ("bar", "boo", "bam", "qux") VALUES (\'baz\', NOW(), NULL, \'100\')', $this->insert->getSqlString(new TrustingSql92Platform()));
    }

    /**
     * @coversNothing
     */
    public function testSpecificationconstantsCouldBeOverridedByExtensionInPrepareStatement()
    {
        $replace = new Replace();

        $mockDriver = $this->getMock('Zend\Db\Adapter\Driver\DriverInterface');
        $mockDriver->expects($this->any())->method('getPrepareType')->will($this->returnValue('positional'));
        $mockDriver->expects($this->any())->method('formatParameterName')->will($this->returnValue('?'));
        $mockAdapter = $this->getMock('Zend\Db\Adapter\Adapter', null, array($mockDriver));

        $mockStatement = $this->getMock('Zend\Db\Adapter\Driver\StatementInterface');
        $pContainer = new \Zend\Db\Adapter\ParameterContainer(array());
        $mockStatement->expects($this->any())->method('getParameterContainer')->will($this->returnValue($pContainer));
        $mockStatement->expects($this->at(1))
            ->method('setSql')
            ->with($this->equalTo('REPLACE INTO "foo" ("bar", "boo") VALUES (?, NOW())'));

        $replace->into('foo')
            ->values(array('bar' => 'baz', 'boo' => new Expression('NOW()')));

        $replace->prepareStatement($mockAdapter, $mockStatement);

        // with TableIdentifier
        $replace = new Replace();

        $mockDriver = $this->getMock('Zend\Db\Adapter\Driver\DriverInterface');
        $mockDriver->expects($this->any())->method('getPrepareType')->will($this->returnValue('positional'));
        $mockDriver->expects($this->any())->method('formatParameterName')->will($this->returnValue('?'));
        $mockAdapter = $this->getMock('Zend\Db\Adapter\Adapter', null, array($mockDriver));

        $mockStatement = $this->getMock('Zend\Db\Adapter\Driver\StatementInterface');
        $pContainer = new \Zend\Db\Adapter\ParameterContainer(array());
        $mockStatement->expects($this->any())->method('getParameterContainer')->will($this->returnValue($pContainer));
        $mockStatement->expects($this->at(1))
            ->method('setSql')
            ->with($this->equalTo('REPLACE INTO "sch"."foo" ("bar", "boo") VALUES (?, NOW())'));

        $replace->into(new TableIdentifier('foo', 'sch'))
            ->values(array('bar' => 'baz', 'boo' => new Expression('NOW()')));

        $replace->prepareStatement($mockAdapter, $mockStatement);
    }

    /**
     * @coversNothing
     */
    public function testSpecificationconstantsCouldBeOverridedByExtensionInGetSqlString()
    {
        $replace = new Replace();
        $replace->into('foo')
            ->values(array('bar' => 'baz', 'boo' => new Expression('NOW()'), 'bam' => null));

        $this->assertEquals('REPLACE INTO "foo" ("bar", "boo", "bam") VALUES (\'baz\', NOW(), NULL)', $replace->getSqlString(new TrustingSql92Platform()));

        // with TableIdentifier
        $replace = new Replace();
        $replace->into(new TableIdentifier('foo', 'sch'))
            ->values(array('bar' => 'baz', 'boo' => new Expression('NOW()'), 'bam' => null));

        $this->assertEquals('REPLACE INTO "sch"."foo" ("bar", "boo", "bam") VALUES (\'baz\', NOW(), NULL)', $replace->getSqlString(new TrustingSql92Platform()));
    }
}

class Replace extends Insert
{
    const SPECIFICATION_INSERT = 'replace';

    protected $specifications = array(
        self::SPECIFICATION_INSERT => 'REPLACE INTO %1$s (%2$s) VALUES (%3$s)',
        self::SPECIFICATION_SELECT => 'REPLACE INTO %1$s %2$s %3$s',
    );

    protected function processreplace(\Zend\Db\Adapter\Platform\PlatformInterface $platform, \Zend\Db\Adapter\Driver\DriverInterface $driver = null, \Zend\Db\Adapter\ParameterContainer $parameterContainer = null)
    {
        return parent::processInsert($platform, $driver, $parameterContainer);
    }
}
