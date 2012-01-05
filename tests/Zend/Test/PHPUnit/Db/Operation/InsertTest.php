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

/**
 * @namespace
 */
namespace ZendTest\Test\PHPUnit\Db\Operation;
use Zend\Test\PHPUnit\Db;

/**
 * @category   Zend
 * @package    Zend_Test
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Test
 */
class InsertTest extends \PHPUnit_Framework_TestCase
{
    private $operation = null;

    public function setUp()
    {
        $this->operation = new \Zend\Test\PHPUnit\Db\Operation\Insert();
    }

    public function testInsertDataSetUsingAdapterInsert()
    {
        $dataSet = new \PHPUnit_Extensions_Database_DataSet_FlatXmlDataSet(__DIR__."/_files/insertFixture.xml");

        $testAdapter = $this->getMock('Zend\Test\DbAdapter');
        $testAdapter->expects($this->at(0))
                    ->method('insert')
                    ->with('foo', array('foo' => 'foo', 'bar' => 'bar', 'baz' => 'baz'));
        $testAdapter->expects($this->at(1))
                    ->method('insert')
                    ->with('foo', array('foo' => 'bar', 'bar' => 'bar', 'baz' => 'bar'));
        $testAdapter->expects($this->at(2))
                    ->method('insert')
                    ->with('foo', array('foo' => 'baz', 'bar' => 'baz', 'baz' => 'baz'));

        $connection = new Db\Connection($testAdapter, "schema");

        $this->operation->execute($connection, $dataSet);
    }

    public function testInsertExceptionIsTransformed()
    {
        $this->setExpectedException('PHPUnit_Extensions_Database_Operation_Exception');

        $dataSet = new \PHPUnit_Extensions_Database_DataSet_FlatXmlDataSet(__DIR__."/_files/insertFixture.xml");

        $testAdapter = $this->getMock('Zend\Test\DbAdapter');
        $testAdapter->expects($this->any())->method('insert')->will($this->throwException(new \Exception()));

        $connection = new Db\Connection($testAdapter, "schema");
        $this->operation->execute($connection, $dataSet);
    }

    public function testInvalidConnectionGivenThrowsException()
    {
        $this->setExpectedException("Zend\Test\PHPUnit\Db\Exception\InvalidArgumentException");

        $dataSet = $this->getMock('PHPUnit_Extensions_Database_DataSet_IDataSet');
        $connection = $this->getMock('PHPUnit_Extensions_Database_DB_IDatabaseConnection');

        $this->operation->execute($connection, $dataSet);
    }
}
