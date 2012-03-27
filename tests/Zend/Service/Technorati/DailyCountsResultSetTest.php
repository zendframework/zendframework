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
 * @package    Zend_Service_Technorati
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Service\Technorati;
use Zend\Service\Technorati;

/**
 * Test helper
 */

/**
 * @see Technorati\DailyCountsResultSet
 */


/**
 * @category   Zend
 * @package    Zend_Service_Technorati
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Service
 * @group      Zend_Service_Technorati
 */
class DailyCountsResultSetTest extends TestCase
{
    public function setUp()
    {
        $this->dom = self::getTestFileContentAsDom('TestDailyCountsResultSet.xml');
    }

    public function testConstruct()
    {
        $this->_testConstruct('Zend\Service\Technorati\DailyCountsResultSet', array($this->dom));
    }

    public function testDailyCountsResultSet()
    {
        $object = new Technorati\DailyCountsResultSet($this->dom);

        // check counts
        $this->assertInternalType('integer', $object->totalResults());
        $this->assertEquals(5, $object->totalResults());
        $this->assertInternalType('integer', $object->totalResultsAvailable());
        $this->assertEquals(5, $object->totalResultsAvailable());

        // check properties
        $this->assertInstanceOf('Zend\Uri\Http', $object->getSearchUrl());
        $this->assertEquals(\Zend\Uri\UriFactory::factory('http://technorati.com/search/google'), $object->getSearchUrl());
    }

    public function testDailyCountsResultSetItemsInstanceOfResult()
    {
        $this->_testResultSetItemsInstanceOfResult(
                    'Zend\Service\Technorati\DailyCountsResultSet',
                    array($this->dom),
                    'Zend\Service\Technorati\DailyCountsResult');
    }

    public function testDailyCountsResultSetSerialization()
    {
        $this->_testResultSetSerialization(new Technorati\DailyCountsResultSet($this->dom));
    }
}
