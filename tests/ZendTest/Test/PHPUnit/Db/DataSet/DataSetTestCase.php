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
namespace ZendTest\Test\PHPUnit\Db\DataSet;

/**
 * @category   Zend
 * @package    Zend_Test
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
