<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Test
 */

namespace ZendTest\Test\PHPUnit\Db\DataSet;

/**
 * @category   Zend
 * @package    Zend_Test
 * @subpackage UnitTests
 * @group      Zend_Test
 */
abstract class DataSetTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Extensions_Database_DB_IDatabaseConnection
     */
    protected $connectionMock = null;

    public function setUp()
    {
        $this->connectionMock = $this->getMock('Zend\Test\PHPUnit\Db\Connection', array(), array(), '', false);
    }

    public function decorateConnectionMockWithZendAdapter()
    {
        $this->decorateConnectionGetConnectionWith(new \Zend\Test\DbAdapter());
    }

    public function decorateConnectionGetConnectionWith($returnValue)
    {
        $this->connectionMock->expects($this->any())
                             ->method('getConnection')
                             ->will($this->returnValue($returnValue));
    }
}
