<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Validator
 */

namespace ZendTest\Validator\Db;

use ArrayObject;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Adapter\ParameterContainer;
use Zend\Db\Sql\Sql;
use Zend\Validator\Db\RecordExists;

/**
 * @category   Zend
 * @package    Zend_Validator
 * @subpackage UnitTests
 * @group      Zend_Validator
 */
class RecordExistsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Return a Mock object for a Db result with rows
     *
     * @return \Zend\Db\Adapter\Adapter
     */
    protected function getMockHasResult()
    {
        // mock the adapter, driver, and parts
        $mockConnection = $this->getMock('Zend\Db\Adapter\Driver\ConnectionInterface');

        // Mock has result
        $mockHasResultRow      = new ArrayObject();
        $mockHasResultRow->one = 'one';

        $mockHasResult = $this->getMock('Zend\Db\Adapter\Driver\ResultInterface');
        $mockHasResult->expects($this->any())
            ->method('current')
            ->will($this->returnValue($mockHasResultRow));

        $mockHasResultStatement = $this->getMock('Zend\Db\Adapter\Driver\StatementInterface');
        $mockHasResultStatement->expects($this->any())
            ->method('execute')
            ->will($this->returnValue($mockHasResult));

        $mockHasResultStatement->expects($this->any())
            ->method('getParameterContainer')
            ->will($this->returnValue(new ParameterContainer()));

        $mockHasResultDriver = $this->getMock('Zend\Db\Adapter\Driver\DriverInterface');
        $mockHasResultDriver->expects($this->any())
            ->method('createStatement')
            ->will($this->returnValue($mockHasResultStatement));
        $mockHasResultDriver->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue($mockConnection));

        return $this->getMock('Zend\Db\Adapter\Adapter', null, array($mockHasResultDriver));
    }

    /**
     * Return a Mock object for a Db result without rows
     *
     * @return \Zend\Db\Adapter\Adapter
     */
    protected function getMockNoResult()
    {
        // mock the adapter, driver, and parts
        $mockConnection = $this->getMock('Zend\Db\Adapter\Driver\ConnectionInterface');

        $mockNoResult = $this->getMock('Zend\Db\Adapter\Driver\ResultInterface');
        $mockNoResult->expects($this->any())
            ->method('current')
            ->will($this->returnValue(null));

        $mockNoResultStatement = $this->getMock('Zend\Db\Adapter\Driver\StatementInterface');
        $mockNoResultStatement->expects($this->any())
            ->method('execute')
            ->will($this->returnValue($mockNoResult));

        $mockNoResultStatement->expects($this->any())
            ->method('getParameterContainer')
            ->will($this->returnValue(new ParameterContainer()));

        $mockNoResultDriver = $this->getMock('Zend\Db\Adapter\Driver\DriverInterface');
        $mockNoResultDriver->expects($this->any())
            ->method('createStatement')
            ->will($this->returnValue($mockNoResultStatement));
        $mockNoResultDriver->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue($mockConnection));

        return $this->getMock('Zend\Db\Adapter\Adapter', null, array($mockNoResultDriver));
    }

    /**
     * Test basic function of RecordExists (no exclusion)
     *
     * @return void
     */
    public function testBasicFindsRecord()
    {
        $validator = new RecordExists(array('table'   => 'users',
                                            'field'   => 'field1',
                                            'adapter' => $this->getMockHasResult()));
        $this->assertTrue($validator->isValid('value1'));
    }

    /**
     * Test basic function of RecordExists (no exclusion)
     *
     * @return void
     */
    public function testBasicFindsNoRecord()
    {
        $validator = new RecordExists(array('table'   => 'users',
                                            'field'   => 'field1',
                                            'adapter' => $this->getMockNoResult()));
        $this->assertFalse($validator->isValid('nosuchvalue'));
    }

    /**
     * Test the exclusion function
     *
     * @return void
     */
    public function testExcludeWithArray()
    {
        $validator = new RecordExists(array('table'   => 'users',
                                            'field'   => 'field1',
                                            'exclude' => array('field' => 'id',
                                                               'value' => 1),
                                            'adapter' => $this->getMockHasResult()));
        $this->assertTrue($validator->isValid('value3'));
    }

    /**
     * Test the exclusion function
     * with an array
     *
     * @return void
     */
    public function testExcludeWithArrayNoRecord()
    {
        $validator = new RecordExists(array('table'   => 'users',
                                            'field'   => 'field1',
                                            'exclude' => array('field' => 'id',
                                                               'value' => 1),
                                            'adapter' => $this->getMockNoResult()));
        $this->assertFalse($validator->isValid('nosuchvalue'));
    }

    /**
     * Test the exclusion function
     * with a string
     *
     * @return void
     */
    public function testExcludeWithString()
    {
        $validator = new RecordExists(array('table'   => 'users',
                                            'field'   => 'field1',
                                            'exclude' => 'id != 1',
                                            'adapter' => $this->getMockHasResult()));
        $this->assertTrue($validator->isValid('value3'));
    }

    /**
     * Test the exclusion function
     * with a string
     *
     * @return void
     */
    public function testExcludeWithStringNoRecord()
    {
        $validator = new RecordExists('users', 'field1', 'id != 1', $this->getMockNoResult());
        $this->assertFalse($validator->isValid('nosuchvalue'));
    }

    /**
     * @group ZF-8863
     */
    public function testExcludeConstructor()
    {
        $validator = new RecordExists('users', 'field1', 'id != 1', $this->getMockHasResult());
        $this->assertTrue($validator->isValid('value3'));
    }

    /**
     * Test that the class throws an exception if no adapter is provided
     * and no default is set.
     *
     * @return void
     */
    public function testThrowsExceptionWithNoAdapter()
    {
        $validator = new RecordExists('users', 'field1', 'id != 1');
        $this->setExpectedException('Zend\Validator\Exception\RuntimeException',
                                    'No database adapter present');
        $validator->isValid('nosuchvalue');
    }

    /**
     * Test that schemas are supported and run without error
     *
     * @return void
     */
    public function testWithSchema()
    {
        $validator = new RecordExists(array('table' => 'users', 'schema' => 'my'),
                                      'field1', null, $this->getMockHasResult());
        $this->assertTrue($validator->isValid('value1'));
    }

    /**
     * Test that schemas are supported and run without error
     *
     * @return void
     */
    public function testWithSchemaNoResult()
    {
        $validator = new RecordExists(array('table' => 'users', 'schema' => 'my'),
                                      'field1', null, $this->getMockNoResult());
        $this->assertFalse($validator->isValid('value1'));
    }

    /**
     * Test that the supplied table and schema are successfully passed to the select
     * statement
     */
    public function testSelectAcknowledgesTableAndSchema()
    {
        $validator = new RecordExists(array('table' => 'users', 'schema' => 'my'),
                                      'field1', null, $this->getMockHasResult());
        $table = $validator->getSelect()->getRawState('table');
        $this->assertInstanceOf('Zend\Db\Sql\TableIdentifier', $table);
        $this->assertEquals(array('users','my'), $table->getTableAndSchema());
    }

    public function testEqualsMessageTemplates()
    {
        $validator  = new RecordExists('users', 'field1');
        $this->assertAttributeEquals($validator->getOption('messageTemplates'),
                                     'messageTemplates', $validator);
    }

    /**
     * @testdox Zend\Validator\Db\RecordExists::getSelect
     */
    public function testGetSelect()
    {
        $validator = new RecordExists(
            array(
                'table' => 'users',
                'schema' => 'my'
            ),
            'field1',
            array(
                'field' => 'foo',
                'value' => 'bar'
            ),
            $this->getMockHasResult()
        );
        $select = $validator->getSelect();
        $this->assertInstanceOf('Zend\Db\Sql\Select', $select);
        $this->assertEquals('SELECT "my"."users"."field1" AS "field1" FROM "my"."users" WHERE "field1" = \'\' AND "foo" != \'bar\'', $select->getSqlString());

        $sql = new Sql($this->getMockHasResult());
        $statement = $sql->prepareStatementForSqlObject($select);
        $parameters = $statement->getParameterContainer();
        $this->assertNull($parameters['where1']);
        $this->assertEquals($parameters['where2'], 'bar');
    }
}
