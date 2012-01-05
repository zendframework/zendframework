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
 * @package    Zend_Validator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Validator;

use Zend\Validator,
    ReflectionClass;

/**
 * Test helper
 */

/**
 * @see Zend_Validator_Ip
 */

/**
 * @category   Zend
 * @package    Zend_Validator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Validator
 */
class IpTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Zend_Validator_Ip object
     *
     * @var Zend_Validator_Ip
     */
    protected $_validator;

    /**
     * Creates a new Zend_Validator_Ip object for each test method
     *
     * @return void
     */
    public function setUp()
    {
        $this->_validator = new \Zend\Validator\Ip();
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
        $this->setExpectedException('Zend\Validator\Exception\InvalidArgumentException', 'Nothing to validate');
        $this->_validator->setOptions(array('allowipv4' => false, 'allowipv6' => false));
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
     * @group ZF-2694
     * @group ZF-8253
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
            '::01.02.03.04'         => true, // according to RFC this can be interpreted as hex notation IpV4
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
                $this->assertTrue($this->_validator->isValid($ip), $ip . " failed validation");
            } else {
                $this->assertFalse($this->_validator->isValid($ip), $ip . " failed validation");
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

    /**
     * @group ZF-10621
     */
    public function testIPv4addressnotations()
    {
        $IPs = array(
            // binary notation
            '00000001.00000010.00000011.00000100' => true,
            '10000000.02000000.00000000.00000001' => false,

            // octal notation (always seen as integer!)
            '001.002.003.004' => true,
            '009.008.007.006' => true,
            '0a0.100.001.010' => false,

            // hex notation
            '01.02.03.04' => true,
            'a0.b0.c0.d0' => true,
            'g0.00.00.00' => false
        );

        foreach($IPs as $ip => $expectedOutcome) {
            if($expectedOutcome) {
                $this->assertTrue($this->_validator->isValid($ip), $ip . " failed validation");
            } else {
                $this->assertFalse($this->_validator->isValid($ip), $ip . " failed validation");
            }
        }

    }
    
    public function testEqualsMessageTemplates()
    {
        $validator = $this->_validator;
        $reflection = new ReflectionClass($validator);
        
        if(!$reflection->hasProperty('_messageTemplates')) {
            return;
        }
        
        $property = $reflection->getProperty('_messageTemplates');
        $property->setAccessible(true);

        $this->assertEquals(
            $property->getValue($validator),
            $validator->getOption('messageTemplates')
        );
    }
    
    public function testEqualsMessageVariables()
    {
        $validator = $this->_validator;
        $reflection = new ReflectionClass($validator);
        
        if(!$reflection->hasProperty('_messageVariables')) {
            return;
        }
        
        $property = $reflection->getProperty('_messageVariables');
        $property->setAccessible(true);

        $this->assertEquals(
            $property->getValue($validator),
            $validator->getOption('messageVariables')
        );
    }
}
