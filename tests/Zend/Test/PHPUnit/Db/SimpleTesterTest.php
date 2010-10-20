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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace ZendTest\Test\PHPUnit\Db;
use Zend\Test\PHPUnit\Db;

/**
 * @category   Zend
 * @package    Zend_Test
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Test
 */
class SimpleTesterTest extends \PHPUnit_Framework_TestCase
{
    public function testGetConnection()
    {
        $testAdapter = $this->getMock('Zend\Test\DbAdapter');
        $testAdapter->expects($this->any())
                    ->method('delete')
                    ->will($this->throwException(new \Exception));

        $connection = new Db\Connection($testAdapter, "schema");

        $databaseTester = new Db\SimpleTester($connection);

        $this->assertSame($connection, $databaseTester->getConnection());
    }

    public function testSetupDatabase()
    {
        $testAdapter = $this->getMock('Zend\Test\DbAdapter');
        $testAdapter->expects($this->any())
                    ->method('delete')
                    ->will($this->throwException(new \Exception));

        $connection = new Db\Connection($testAdapter, "schema");

        $databaseTester = new Db\SimpleTester($connection);

        $dataSet = $this->getMock('PHPUnit_Extensions_Database_DataSet_IDataSet');
        $dataSet->expects($this->any())
                ->method('getIterator')
                ->will($this->returnValue($this->getMock('Iterator')));
        $dataSet->expects($this->any())
                ->method('getReverseIterator')
                ->will($this->returnValue($this->getMock('Iterator')));
        $databaseTester->setUpDatabase($dataSet);
    }

    public function testInvalidConnectionGivenThrowsException()
    {
        $this->setExpectedException('Zend\Test\PHPUnit\Db\Exception\InvalidArgumentException');

        $connection = $this->getMock('PHPUnit_Extensions_Database_DB_IDatabaseConnection');

        $databaseTester = new Db\SimpleTester($connection);
    }
}
