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
 * @see Zend_Service_Technorati_GetInfoResult
 */
require_once 'Zend/Service/Technorati/GetInfoResult.php';


/**
 * @category   Zend
 * @package    Zend_Service_Technorati
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_Technorati_GetInfoResultTest extends Zend_Service_Technorati_TestCase
{
    public function setUp()
    {
        $this->dom = self::getTestFileContentAsDom('TestGetInfoResult.xml');
    }
    
    public function testConstruct()
    {
        $this->_testConstruct('Zend_Service_Technorati_GetInfoResult', array($this->dom));
    }
    
    public function testConstructThrowsExceptionWithInvalidDom() 
    {
        $this->_testConstructThrowsExceptionWithInvalidDom('Zend_Service_Technorati_GetInfoResult', 'DOMDocument');
    }

    public function testGetInfoResult()
    {
        $object = new Zend_Service_Technorati_GetInfoResult($this->dom);

        // check author
        $author = $object->getAuthor();
        $this->assertType('Zend_Service_Technorati_Author', $author);
        $this->assertEquals('weppos', $author->getUsername());

        // check weblogs
        $weblogs = $object->getWeblogs();
        $this->assertType('array', $weblogs);
        $this->assertEquals(2, count($weblogs));
        $this->assertType('Zend_Service_Technorati_Weblog', $weblogs[0]);
    }
}
