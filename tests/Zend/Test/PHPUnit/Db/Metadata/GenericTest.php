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
 * @package    Zend_Test
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Test\PHPUnit\Db\Metadata;

/**
 * @category   Zend
 * @package    Zend_Test
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Test
 */
class GenericTest extends \PHPUnit_Framework_TestCase
{
    private $adapterMock = null;

    private $metadata = null;

    public function setUp()
    {
        $this->adapterMock = $this->getMock('Zend\Test\DbAdapter');
        $this->metadata = new \Zend\Test\PHPUnit\Db\Metadata\Generic($this->adapterMock, "schema");
    }

    public function testGetSchema()
    {
        $this->assertEquals("schema", $this->metadata->getSchema());
    }

    public function testGetColumnNames()
    {
        $fixtureTableName = "foo";

        $this->adapterMock->expects($this->once())
                          ->method('describeTable')
                          ->with($fixtureTableName)
                          ->will($this->returnValue(array("foo" => 1, "bar" => 2)));
        $data = $this->metadata->getTableColumns($fixtureTableName);

        $this->assertEquals(array("foo", "bar"), $data);
    }

    public function testGetTableNames()
    {
        $this->adapterMock->expects($this->once())
                          ->method('listTables')
                          ->will($this->returnValue(array("foo")));
        $tables = $this->metadata->getTableNames();

        $this->assertEquals(array("foo"), $tables);
    }

    public function testGetTablePrimaryKey()
    {
        $fixtureTableName = "foo";

        $tableMeta = array(
            array('PRIMARY' => false, 'COLUMN_NAME' => 'foo'),
            array('PRIMARY' => true, 'COLUMN_NAME' => 'bar'),
            array('PRIMARY' => true, 'COLUMN_NAME' => 'baz'),
        );

        $this->adapterMock->expects($this->once())
                          ->method('describeTable')
                          ->with($fixtureTableName)
                          ->will($this->returnValue($tableMeta));

        $primaryKey = $this->metadata->getTablePrimaryKeys($fixtureTableName);
        $this->assertEquals(array("bar", "baz"), $primaryKey);
    }

    public function testGetAllowCascading()
    {
        $this->assertFalse($this->metadata->allowsCascading());
    }

    public function testQuoteIdentifierIsDelegated()
    {
        $fixtureValue = "foo";

        $this->adapterMock->expects($this->once())
                          ->method('quoteIdentifier')
                          ->with($fixtureValue)
                          ->will($this->returnValue($fixtureValue));

        $actualValue = $this->metadata->quoteSchemaObject($fixtureValue);

        $this->assertEquals($fixtureValue, $actualValue);
    }
}
