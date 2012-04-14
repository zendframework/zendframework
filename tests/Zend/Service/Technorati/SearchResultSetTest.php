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

namespace ZendTest\Service\Technorati;
use Zend\Service\Technorati;

/**
 * Test helper
 */

/**
 * @see Technorati\SearchResultSet
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
class SearchResultSetTest extends TestCase
{
    public function setUp()
    {
        $this->dom = self::getTestFileContentAsDom('TestSearchResultSet.xml');
    }

    public function testConstruct()
    {
        $this->_testConstruct('Zend\Service\Technorati\SearchResultSet', array($this->dom));
    }

    public function testSearchResultSet()
    {
        $object = new Technorati\SearchResultSet($this->dom);

        // check counts
        $this->assertInternalType('integer', $object->totalResults());
        $this->assertEquals(3, $object->totalResults());
        $this->assertInternalType('integer', $object->totalResultsAvailable());
        $this->assertEquals(4298362, $object->totalResultsAvailable());
    }

    public function testSearchResultSetItemsInstanceOfResult()
    {
        $this->_testResultSetItemsInstanceOfResult(
                    'Zend\Service\Technorati\SearchResultSet',
                    array($this->dom),
                    'Zend\Service\Technorati\SearchResult');
    }

    public function testSearchResultSetSerialization()
    {
        $this->_testResultSetSerialization(new Technorati\SearchResultSet($this->dom));
    }
}
