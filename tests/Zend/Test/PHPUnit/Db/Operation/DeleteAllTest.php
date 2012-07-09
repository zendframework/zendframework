<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Test
 */

namespace ZendTest\Test\PHPUnit\Db\Operation;
use Zend\Test\PHPUnit\Db;

/**
 * @category   Zend
 * @package    Zend_Test
 * @subpackage UnitTests
 * @group      Zend_Test
 */
class DeleteAllTest extends \PHPUnit_Framework_TestCase
{
    private $operation = null;

    public function setUp()
    {
        $this->operation = new \Zend\Test\PHPUnit\Db\Operation\DeleteAll();
    }

    public function testDeleteAll()
    {
        $dataSet = new \PHPUnit_Extensions_Database_DataSet_FlatXmlDataSet(__DIR__."/_files/truncateFixture.xml");

        $testAdapter = $this->getMock('Zend\Test\DbAdapter');
        $testAdapter->expects($this->at(0))
                    ->method('delete')
                    ->with('foo');
        $testAdapter->expects($this->at(1))
                    ->method('delete')
                    ->with('bar');

        $connection = new Db\Connection($testAdapter, "schema");

        $this->operation->execute($connection, $dataSet);
    }

    public function testDeleteQueryErrorTransformsException()
    {
        $this->setExpectedException('PHPUnit_Extensions_Database_Operation_Exception');

        $dataSet = new \PHPUnit_Extensions_Database_DataSet_FlatXmlDataSet(__DIR__."/_files/truncateFixture.xml");

        $testAdapter = $this->getMock('Zend\Test\DbAdapter');
        $testAdapter->expects($this->any())
                    ->method('delete')
                    ->will($this->throwException(new \Exception));

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
