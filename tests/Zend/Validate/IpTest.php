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
     * @ZF-4352
     */
    public function testNonStringValidation()
    {
        $this->assertFalse($this->_validator->isValid(array(1 => 1)));
    }
}
