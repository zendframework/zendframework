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

/**
 * @see Zend_Service_Technorati_TagsResultSet
 */


/**
 * @category   Zend
 * @package    Zend_Service_Technorati
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Service
 * @group      Zend_Service_Technorati
 */
class Zend_Service_Technorati_TagsResultSetTest extends Zend_Service_Technorati_TestCase
{
    public function setUp()
    {
        $this->dom = self::getTestFileContentAsDom('TestTagsResultSet.xml');
        $this->object = new Zend_Service_Technorati_TagsResultSet($this->dom);
    }

    public function testConstruct()
    {
        $this->_testConstruct('Zend_Service_Technorati_TagsResultSet', array($this->dom));
    }

    public function testConstructThrowsExceptionWithInvalidDom()
    {
        $this->_testConstructThrowsExceptionWithInvalidDom('Zend_Service_Technorati_TagsResultSet', 'DOMDocument');
    }

    public function testTagsResultSet()
    {
        $object = new Zend_Service_Technorati_TagsResultSet($this->dom);

        // check counts
        $this->assertType('integer', $object->totalResults());
        $this->assertEquals(3, $object->totalResults());
        $this->assertType('integer', $object->totalResultsAvailable());
        $this->assertEquals(3, $object->totalResultsAvailable());
    }

    public function testTagsResultSetItemsInstanceOfResult()
    {
        $this->_testResultSetItemsInstanceOfResult(
                    'Zend_Service_Technorati_TagsResultSet',
                    array($this->dom),
                    'Zend_Service_Technorati_TagsResult');
    }

    public function testTagsResultSetSerialization()
    {
        $this->_testResultSetSerialization(new Zend_Service_Technorati_TagsResultSet($this->dom));
    }
}
