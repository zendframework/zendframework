<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
 */

namespace ZendTest\Service\Technorati;

use Zend\Service\Technorati;

/**
 * @category   Zend
 * @package    Zend_Service_Technorati
 * @subpackage UnitTests
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
