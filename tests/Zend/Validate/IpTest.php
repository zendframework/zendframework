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
 * @package    Zend_Validate
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../TestHelper.php';

/**
 * @see Zend_Validate_Ip
 */
require_once 'Zend/Validate/Ip.php';

/**
 * @category   Zend
 * @package    Zend_Validate
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Validate
 */
class Zend_Validate_IpTest extends PHPUnit_Framework_TestCase
{
    /**
     * Zend_Validate_Ip object
     *
     * @var Zend_Validate_Ip
     */
    protected $_validator;

    /**
     * Creates a new Zend_Validate_Ip object for each test method
     *
     * @return void
     */
    public function setUp()
    {
        $this->_validator = new Zend_Validate_Ip();
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testBasic()
    {
        $this->assertTrue($this->_validator->isValid('1.2.3.4'));
        $this->assertTrue($this->_validator->isValid('10.0.0.1'));
        $this->assertTrue($this->_validator->isValid('255.255.255.255'));

        $this->assertFalse($this->_validator->isValid('0.0.0.256'));
        $this->assertFalse($this->_validator->isValid('1.2.3.4.5'));
    }

    public function testZeroIpForZF2420()
    {
        $this->assertTrue($this->_validator->isValid('0.0.0.0'));
    }

    /**
     * Ensures that getMessages() returns expected default value
     *
     * @return void
     */
    public function testGetMessages()
    {
        $this->assertEquals(array(), $this->_validator->getMessages());
    }

    public function testInvalidIpForZF4809()
    {
        $this->assertFalse($this->_validator->isValid('1.2.333'));
    }

    public function testInvalidIpForZF3435()
    {
        $this->assertFalse($this->_validator->isValid('192.168.0.2 adfs'));
    }

    /**
     * @see ZF-2694
     */
    public function testIPv6addresses()
    {
        if (!function_exists('inet_pton')) {
            $this->markTestIncomplete('No IPv6 support within this PHP release');
        }

        $this->assertTrue($this->_validator->isValid('::127.0.0.1'));
    }
    
    /**
     * @see ZF-8253
     */
    public function testMoreIPv6Addresses()
    {
        $ips = array(
                     '2001:0db8:0000:0000:0000:0000:1428:57ab',
                     '2001:0DB8:0000:0000:0000:0000:1428:57AB',
                     'a:b:c::1.2.3.4',
                     'a:b:c:d::1.2.3.4',
                     'a:b:c:d:e:f:1.2.3.4',
                     'a::c:d:e:f:0:1',
                     'a::0:1',
                     '::e:f',
                     'a:b:c:d:e:f::0');
        foreach($ips as $ip) {
            $this->assertTrue($this->_validator->isValid($ip));
        }
    }
    
    /**
     * @see ZF-8253
     * @see http://bugs.php.net/bug.php?id=50117
     */
    public function testMoreIPv6AddressesPHPdidWrong()
    {
        $validIps = array('a:b:c:d:e::1.2.3.4',
                          '::0:a:b:c:d:e:f',
                          '0:a:b:c:d:e:f::');
        $invalidIps = array('::01.02.03.04',
                            '0:0:0:255.255.255.255');
        
        foreach($validIps as $ip) {
            $this->assertTrue($this->_validator->isValid($ip));
        }
        
        foreach($invalidIps as $ip) {
            $this->assertFalse($this->_validator->isValid($ip));
        }
    }
    

    /**
     * @ZF-4352
     */
    public function testNonStringValidation()
    {
        $this->assertFalse($this->_validator->isValid(array(1 => 1)));
    }
}
