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
require_once dirname(__FILE__) . DIRECTORY_SEPARATOR . 'TestCase.php';

/**
 * @see Zend_Service_Technorati_Utils
 */
require_once "Zend/Service/Technorati/Utils.php";


/**
 * @category   Zend
 * @package    Zend_Service_Technorati
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_Technorati_UtilsTest extends Zend_Service_Technorati_TestCase
{
    /**
     * @return void
     */
    public function testSetUriHttpInputNullReturnsNull()
    {
        $this->assertNull(Zend_Service_Technorati_Utils::normalizeUriHttp(null));
    }

    /**
     * @return void
     */
    public function testSetUriHttpInputInvalidSchemeFtpThrowsException()
    {
        $scheme             = 'ftp';
        $inputInvalidScheme = "$scheme://example.com";
        try {
            Zend_Service_Technorati_Utils::normalizeUriHttp($inputInvalidScheme);
            $this->fail('Expected Zend_Service_Technorati_Exception not thrown');
        } catch (Zend_Service_Technorati_Exception $e) {
            $this->assertContains($scheme, $e->getMessage());
        }
    }

    /**
     * @return void
     */
    public function testSetDateInputNullReturnsNull()
    {
        $this->assertNull(Zend_Service_Technorati_Utils::normalizeDate(null));
    }

    /**
     * @return void
     */
    public function testSetDateInputDateInstanceReturnsInstance()
    {
        $date   = new Zend_Date('2007-11-11 08:47:26 GMT');
        $result = Zend_Service_Technorati_Utils::normalizeDate($date);
        
        $this->assertType('Zend_Date', $result);
        $this->assertEquals($date, $result);
    }

    /**
     * @return void
     */
    public function testSetDateInputInvalidThrowsException()
    {
        $inputInvalid = "2007foo";
        try {
            Zend_Service_Technorati_Utils::normalizeDate($inputInvalid);
            $this->fail('Expected Zend_Service_Technorati_Exception not thrown');
        } catch (Zend_Service_Technorati_Exception $e) {
            $this->assertContains($inputInvalid, $e->getMessage());
        }
    }
}
