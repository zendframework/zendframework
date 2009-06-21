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
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/**
 * Test helper
 */
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR .'TestCase.php';

/**
 * @see Zend_Service_Technorati_TagResultSet
 */
require_once 'Zend/Service/Technorati/TagResultSet.php';


/**
 * @category   Zend
 * @package    Zend_Service_Technorati
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_Technorati_TagResultSetTest extends Zend_Service_Technorati_TestCase
{
    public function setUp()
    {
        $this->dom = self::getTestFileContentAsDom('TestTagResultSet.xml');
    }

    public function testConstruct()
    {
        $this->_testConstruct('Zend_Service_Technorati_TagResultSet', array($this->dom));
    }

    public function testConstructThrowsExceptionWithInvalidDom() 
    {
        $this->_testConstructThrowsExceptionWithInvalidDom('Zend_Service_Technorati_TagResultSet', 'DOMDocument');
    }

    public function testTagResultSet()
    {
        $object = new Zend_Service_Technorati_TagResultSet($this->dom);

        // check counts
        $this->assertType('integer', $object->totalResults());
        $this->assertEquals(3, $object->totalResults());
        $this->assertType('integer', $object->totalResultsAvailable());
        $this->assertEquals(268877, $object->totalResultsAvailable());
        
        // check properties
        $this->assertType('integer', $object->getPostsMatched());
        $this->assertEquals(268877, $object->getPostsMatched());
        $this->assertType('integer', $object->getBlogsMatched());
        $this->assertEquals(1812, $object->getBlogsMatched());
    }
    
    public function testTagResultSetItemsInstanceOfResult() 
    {
        $this->_testResultSetItemsInstanceOfResult(
                    'Zend_Service_Technorati_TagResultSet', 
                    array($this->dom), 
                    'Zend_Service_Technorati_TagResult');
    }

    public function testTagResultSetSerialization()
    {
        $this->_testResultSetSerialization(new Zend_Service_Technorati_TagResultSet($this->dom));
    }
}
