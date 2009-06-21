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
 * @see Zend_Service_Technorati_KeyInfoResult
 */
require_once 'Zend/Service/Technorati/KeyInfoResult.php';


/**
 * @category   Zend
 * @package    Zend_Service_Technorati
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_Technorati_KeyInfoResultTest extends Zend_Service_Technorati_TestCase
{
    const TEST_API_KEY = 'avalidapikey';

    public function setUp()
    {
        $this->dom = self::getTestFileContentAsDom('TestKeyInfoResult.xml');
    }
    
    public function testConstruct()
    {
        $this->_testConstruct('Zend_Service_Technorati_KeyInfoResult', array($this->dom, self::TEST_API_KEY));
    }
    
    public function testConstructThrowsExceptionWithInvalidDom() 
    {
        $this->_testConstructThrowsExceptionWithInvalidDom('Zend_Service_Technorati_KeyInfoResult', 'DOMDocument');
    }

    public function testKeyInfoResult()
    {
        $object = new Zend_Service_Technorati_KeyInfoResult($this->dom, self::TEST_API_KEY);

        $this->assertType('string', $object->getApiKey());
        $this->assertEquals(self::TEST_API_KEY, $object->getApiKey());
        $this->assertType('integer', $object->getApiQueries());
        $this->assertEquals(27, $object->getApiQueries());
        $this->assertType('integer', $object->getMaxQueries());
        $this->assertEquals(1500, $object->getMaxQueries());
    }

    public function testApiKeyIsNullByDefault()
    {
        $object = new Zend_Service_Technorati_KeyInfoResult($this->dom);
        $this->assertEquals(null, $object->getApiKey());
    }

    public function testSetGet()
    {
        $object = new Zend_Service_Technorati_KeyInfoResult($this->dom, self::TEST_API_KEY);

        $set = 'anewapikey';
        $get = $object->setApiKey($set)->getApiKey();
        $this->assertType('string', $get);
        $this->assertEquals($set, $get);
    }
}
