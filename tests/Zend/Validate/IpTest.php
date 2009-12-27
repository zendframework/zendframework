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

    public function testOnlyIpv4()
    {
        $this->_validator->setOptions(array('allowipv6' => false));
        $this->assertTrue($this->_validator->isValid('1.2.3.4'));
        $this->assertFalse($this->_validator->isValid('a:b:c:d:e::1.2.3.4'));
    }

    public function testOnlyIpv6()
    {
        $this->_validator->setOptions(array('allowipv4' => false));
        $this->assertFalse($this->_validator->isValid('1.2.3.4'));
        $this->assertTrue($this->_validator->isValid('a:b:c:d:e::1.2.3.4'));
    }

    public function testNoValidation()
    {
        try {
            $this->_validator->setOptions(array('allowipv4' => false, 'allowipv6' => false));
            $this->fail();
        } catch (Zend_Validate_Exception $e) {
            $this->assertContains('Nothing to validate', $e->getMessage());
        }
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
     * @see ZF-8253
     */
    public function testIPv6addresses()
    {
        $IPs = array(
            '2001:0db8:0000:0000:0000:0000:1428:57ab'   => true,
            '2001:0DB8:0000:0000:0000:0000:1428:57AB'   => true,
            '2001:00db8:0000:0000:0000:0000:1428:57ab'  => false,
            '2001:0db8:xxxx:0000:0000:0000:1428:57ab'   => false,

            '2001:db8::1428:57ab'   => true,
            '2001:db8::1428::57ab'  => false,
            '2001:dx0::1234'        => false,
            '2001:db0::12345'       => false,

            ''                      => false,
            ':'                     => false,
            '::'                    => true,
            ':::'                   => false,
            '::::'                  => false,
            '::1'                   => true,
            ':::1'                  => false,

            '::1.2.3.4'             => true,
            '::127.0.0.1'           => true,
            '::256.0.0.1'           => false,
            '::01.02.03.04'         => false,
            'a:b:c::1.2.3.4'        => true,
            'a:b:c:d::1.2.3.4'      => true,
            'a:b:c:d:e::1.2.3.4'    => true,
            'a:b:c:d:e:f:1.2.3.4'   => true,
            'a:b:c:d:e:f:1.256.3.4' => false,
            'a:b:c:d:e:f::1.2.3.4'  => false,

            'a:b:c:d:e:f:0:1:2'     => false,
            'a:b:c:d:e:f:0:1'       => true,
            'a::b:c:d:e:f:0:1'      => false,
            'a::c:d:e:f:0:1'        => true,
            'a::d:e:f:0:1'          => true,
            'a::e:f:0:1'            => true,
            'a::f:0:1'              => true,
            'a::0:1'                => true,
            'a::1'                  => true,
            'a::'                   => true,

            '::0:1:a:b:c:d:e:f'     => false,
            '::0:a:b:c:d:e:f'       => true,
            '::a:b:c:d:e:f'         => true,
            '::b:c:d:e:f'           => true,
            '::c:d:e:f'             => true,
            '::d:e:f'               => true,
            '::e:f'                 => true,
            '::f'                   => true,

            '0:1:a:b:c:d:e:f::'     => false,
            '0:a:b:c:d:e:f::'       => true,
            'a:b:c:d:e:f::'         => true,
            'b:c:d:e:f::'           => true,
            'c:d:e:f::'             => true,
            'd:e:f::'               => true,
            'e:f::'                 => true,
            'f::'                   => true,

            'a:b:::e:f'             => false,
            '::a:'                  => false,
            '::a::'                 => false,
            ':a::b'                 => false,
            'a::b:'                 => false,
            '::a:b::c'              => false,
            'abcde::f'              => false,

            ':10.0.0.1'             => false,
            '0:0:0:255.255.255.255' => false,
            '1fff::a88:85a3::172.31.128.1' => false,

            'a:b:c:d:e:f:0::1'      => false,
            'a:b:c:d:e:f:0::'       => true,
            'a:b:c:d:e:f::0'        => true,
            'a:b:c:d:e:f::'         => true,

            'total gibberish'       => false
        );

        foreach($IPs as $ip => $expectedOutcome) {
            if($expectedOutcome) {
                $this->assertTrue($this->_validator->isValid($ip));
            } else {
                $this->assertFalse($this->_validator->isValid($ip));
            }
        }

    }

    /**
     * @ZF-4352
     */
    public function testNonStringValidation()
    {
        $this->assertFalse($this->_validator->isValid(array(1 => 1)));
    }

    /**
     * @ZF-8640
     */
    public function testNonNewlineValidation()
    {
        $this->assertFalse($this->_validator->isValid("::C0A8:2\n"));
    }
}
