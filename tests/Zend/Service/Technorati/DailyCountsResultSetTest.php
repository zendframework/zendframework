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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/**
 * Test helper
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR .'TestCase.php';

/**
 * @see Zend_Service_Technorati_DailyCountsResultSet
 */
require_once 'Zend/Service/Technorati/DailyCountsResultSet.php';


/**
 * @category   Zend
 * @package    Zend_Service_Technorati
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Service
 * @group      Zend_Service_Technorati
 */
class Zend_Service_Technorati_DailyCountsResultSetTest extends Zend_Service_Technorati_TestCase
{
    public function setUp()
    {
        $this->dom = self::getTestFileContentAsDom('TestDailyCountsResultSet.xml');
    }

    public function testConstruct()
    {
        $this->_testConstruct('Zend_Service_Technorati_DailyCountsResultSet', array($this->dom));
    }

    public function testConstructThrowsExceptionWithInvalidDom()
    {
        $this->_testConstructThrowsExceptionWithInvalidDom('Zend_Service_Technorati_DailyCountsResultSet', 'DOMDocument');
    }

    public function testDailyCountsResultSet()
    {
        $object = new Zend_Service_Technorati_DailyCountsResultSet($this->dom);

        // check counts
        $this->assertType('integer', $object->totalResults());
        $this->assertEquals(5, $object->totalResults());
        $this->assertType('integer', $object->totalResultsAvailable());
        $this->assertEquals(5, $object->totalResultsAvailable());

        // check properties
        $this->assertType('Zend_Uri_Http', $object->getSearchUrl());
        $this->assertEquals(Zend_Uri::factory('http://technorati.com/search/google'), $object->getSearchUrl());
    }

    public function testDailyCountsResultSetItemsInstanceOfResult()
    {
        $this->_testResultSetItemsInstanceOfResult(
                    'Zend_Service_Technorati_DailyCountsResultSet',
                    array($this->dom),
                    'Zend_Service_Technorati_DailyCountsResult');
    }

    public function testDailyCountsResultSetSerialization()
    {
        $this->_testResultSetSerialization(new Zend_Service_Technorati_DailyCountsResultSet($this->dom));
    }
}
