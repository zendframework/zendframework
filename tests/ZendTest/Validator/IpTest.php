<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Validator
 */

namespace ZendTest\Validator;

use Zend\Validator\Ip;

/**
 * @category   Zend
 * @package    Zend_Validator
 * @subpackage UnitTests
 * @group      Zend_Validator
 */
class IpTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Ip
     */
    protected $validator;

    /**
     * The list with the options supported.
     * By default all options are disabled.
     *
     * @var array
     */
    protected $options;

    /**
     * Creates a new IP Validator for each test
     *
     * @return void
     */
    public function setUp()
    {
        $this->validator = new Ip();
        $this->options   = array(
            'allowipv4'      => false,
            'allowipv6'      => false,
            'allowipvfuture' => false,
            'allowliteral' => false,
        );
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testBasic()
    {
        $this->assertTrue($this->validator->isValid('1.2.3.4'));
        $this->assertTrue($this->validator->isValid('10.0.0.1'));
        $this->assertTrue($this->validator->isValid('255.255.255.255'));

        $this->assertFalse($this->validator->isValid('0.0.0.256'));
        $this->assertFalse($this->validator->isValid('1.2.3.4.5'));
    }

    public function testZeroIpForZF2420()
    {
        $this->assertTrue($this->validator->isValid('0.0.0.0'));
    }


    public function testOnlyIpv4()
    {
        $this->options['allowipv4'] = true;
        $this->validator->setOptions($this->options);
        $this->assertTrue($this->validator->isValid('1.2.3.4'));
        $this->assertFalse($this->validator->isValid('a:b:c:d:e::1.2.3.4'));
        $this->assertFalse($this->validator->isValid('v1.09azAZ-._~!$&\'()*+,;='));
    }

    public function testOnlyIpv6()
    {
        $this->options['allowipv6'] = true;
        $this->validator->setOptions($this->options);
        $this->assertFalse($this->validator->isValid('1.2.3.4'));
        $this->assertTrue($this->validator->isValid('a:b:c:d:e::1.2.3.4'));
        $this->assertFalse($this->validator->isValid('v1.09azAZ-._~!$&\'()*+,;='));
    }

    public function testOnlyIpvfuture()
    {
        $this->options['allowipvfuture'] = true;
        $this->validator->setOptions($this->options);
        $this->assertFalse($this->validator->isValid('1.2.3.4'));
        $this->assertFalse($this->validator->isValid('a:b:c:d:e::1.2.3.4'));
        $this->assertTrue($this->validator->isValid("v1.09azAZ-._~!$&'()*+,;=:"));
    }

    public function testLiteral()
    {
        $this->options   = array(
            'allowipv4'      => true,
            'allowipv6'      => true,
            'allowipvfuture' => true,
            'allowliteral'   => true,
        );
        $this->validator->setOptions($this->options);

        $this->assertFalse($this->validator->isValid('[1.2.3.4]'));
        $this->assertTrue($this->validator->isValid('[a:b:c:d:e::1.2.3.4]'));
        $this->assertFalse($this->validator->isValid('[[a:b:c:d:e::1.2.3.4]]'));
        $this->assertFalse($this->validator->isValid('[[a:b:c:d:e::1.2.3.4]'));
        $this->assertFalse($this->validator->isValid('[[a:b:c:d:e::1.2.3.4'));
        $this->assertFalse($this->validator->isValid('[a:b:c:d:e::1.2.3.4]]'));
        $this->assertFalse($this->validator->isValid('a:b:c:d:e::1.2.3.4]]'));
        $this->assertTrue($this->validator->isValid("[v1.ZZ:ZZ]"));
    }

    /**
     * Versions 4 and 6 are not allowed in IPvFuture
     *
     * @depends testOnlyIpvfuture
     */
    public function testVersionsAllowedIpvfuture()
    {
        $this->options['allowipvfuture'] = true;
        $this->validator->setOptions($this->options);
        $this->assertTrue($this->validator->isValid('v1.A', 'IPvFuture: Version 1 disallowed'));
        $this->assertTrue($this->validator->isValid('vD.A', 'IPvFuture: Version D disallowed'));
        $this->assertTrue($this->validator->isValid('v46.A', 'IPvFuture: Version 46 disallowed'));

        $this->assertFalse($this->validator->isValid('v4.A', 'IPvFuture: Version 4 allowed'));
        $this->assertFalse($this->validator->isValid('v6.A', 'IPvFuture: Version 6 allowed'));
    }

    public function testNoValidation()
    {
        $this->setExpectedException('Zend\Validator\Exception\InvalidArgumentException', 'Nothing to validate');
        $this->validator->setOptions($this->options);
    }

    public function testInvalidIpForZF4809()
    {
        $this->assertFalse($this->validator->isValid('1.2.333'));
    }

    public function testInvalidIpForZF3435()
    {
        $this->assertFalse($this->validator->isValid('192.168.0.2 adfs'));
    }

    /**
     * @group ZF-2694
     * @group ZF-8253
     */
    public function testIPv6addresses()
    {
        $ips = array(
            '2001:0db8:0000:0000:0000:0000:1428:57ab'      => true,
            '2001:0DB8:0000:0000:0000:0000:1428:57AB'      => true,
            '[2001:0DB8:0000:0000:0000:0000:1428:57AB]'    => true,
            '2001:00db8:0000:0000:0000:0000:1428:57ab'     => false,
            '2001:0db8:xxxx:0000:0000:0000:1428:57ab'      => false,
            '2001:0DB8:0000:0000:0000:0000:1428:57AB:90'   => false,
            '[2001:0DB8:0000:0000:0000:0000:1428:57AB]:90' => false,

            '2001:db8::1428:57ab'                          => true,
            '2001:db8::1428::57ab'                         => false,
            '2001:dx0::1234'                               => false,
            '2001:db0::12345'                              => false,

            ''                                             => false,
            ':'                                            => false,
            '::'                                           => true,
            ':::'                                          => false,
            '::::'                                         => false,
            '::1'                                          => true,
            ':::1'                                         => false,

            '[::1.2.3.4]'                                  => true,
            '::1.2.3.4'                                    => true,
            '::127.0.0.1'                                  => true,
            '::256.0.0.1'                                  => false,
            '::01.02.03.04'                                => true,
            // according to RFC this can be interpreted as hex notation IPv4
            'a:b:c::1.2.3.4'                               => true,
            'a:b:c:d::1.2.3.4'                             => true,
            'a:b:c:d:e::1.2.3.4'                           => true,
            'a:b:c:d:e:f:1.2.3.4'                          => true,
            'a:b:c:d:e:f:1.256.3.4'                        => false,
            'a:b:c:d:e:f::1.2.3.4'                         => false,

            'a:b:c:d:e:f:0:1:2'                            => false,
            'a:b:c:d:e:f:0:1'                              => true,
            'a::b:c:d:e:f:0:1'                             => false,
            'a::c:d:e:f:0:1'                               => true,
            'a::d:e:f:0:1'                                 => true,
            'a::e:f:0:1'                                   => true,
            'a::f:0:1'                                     => true,
            'a::0:1'                                       => true,
            'a::1'                                         => true,
            'a::'                                          => true,

            '::0:1:a:b:c:d:e:f'                            => false,
            '::0:a:b:c:d:e:f'                              => true,
            '::a:b:c:d:e:f'                                => true,
            '::b:c:d:e:f'                                  => true,
            '::c:d:e:f'                                    => true,
            '::d:e:f'                                      => true,
            '::e:f'                                        => true,
            '::f'                                          => true,

            '0:1:a:b:c:d:e:f::'                            => false,
            '0:a:b:c:d:e:f::'                              => true,
            'a:b:c:d:e:f::'                                => true,
            'b:c:d:e:f::'                                  => true,
            'c:d:e:f::'                                    => true,
            'd:e:f::'                                      => true,
            'e:f::'                                        => true,
            'f::'                                          => true,

            'a:b:::e:f'                                    => false,
            '::a:'                                         => false,
            '::a::'                                        => false,
            ':a::b'                                        => false,
            'a::b:'                                        => false,
            '::a:b::c'                                     => false,
            'abcde::f'                                     => false,

            ':10.0.0.1'                                    => false,
            '0:0:0:255.255.255.255'                        => false,
            '1fff::a88:85a3::172.31.128.1'                 => false,

            'a:b:c:d:e:f:0::1'                             => false,
            'a:b:c:d:e:f:0::'                              => true,
            'a:b:c:d:e:f::0'                               => true,

            'total gibberish'                              => false
        );

        foreach ($ips as $ip => $expectedOutcome) {
            if ($expectedOutcome) {
                $this->assertTrue($this->validator->isValid($ip), $ip . ' failed validation (expects true)');
            } else {
                $this->assertFalse($this->validator->isValid($ip), $ip . ' failed validation (expects false)');
            }
        }

    }

    /**
     * @ZF-4352
     */
    public function testNonStringValidation()
    {
        $this->assertFalse($this->validator->isValid(array(1 => 1)));
    }

    /**
     * @ZF-8640
     */
    public function testNonNewlineValidation()
    {
        $this->assertFalse($this->validator->isValid("::C0A8:2\n"));
    }

    /**
     * @group ZF-10621
     */
    public function testIPv4AddressNotations()
    {
        $ips = array(
            // binary notation
            '00000001.00000010.00000011.00000100'    => true,
            '10000000.02000000.00000000.00000001'    => false,
            '10000000.02000000.00000000.00000001:80' => false,

            // octal notation (always seen as integer!)
            '001.002.003.004'                        => true,
            '009.008.007.006'                        => true,
            '0a0.100.001.010'                        => false,
            '0a0.100.001.010:80'                     => false,

            // hex notation
            '01.02.03.04'                            => true,
            'a0.b0.c0.d0'                            => true,
            'g0.00.00.00'                            => false,
            'g0.00.00.00:80'                         => false,
        );

        foreach ($ips as $ip => $expectedOutcome) {
            if ($expectedOutcome) {
                $this->assertTrue($this->validator->isValid($ip), $ip . ' failed validation (expects true)');
            } else {
                $this->assertFalse($this->validator->isValid($ip), $ip . ' failed validation (expects false)');
            }
        }
    }

    /**
     * @dataProvider iPvFutureAddressesProvider
     */
    public function testIPvFutureAddresses($ip, $expected)
    {
        $this->options['allowipvfuture'] = true;
        $this->options['allowliteral'] = true;
        $this->validator->setOptions($this->options);
        $this->assertEquals($expected, $this->validator->isValid($ip));
    }

    public function iPvFutureAddressesProvider()
    {
        return array(
            array("[v1.09azAZ-._~!$&'()*+,;=:]:80", false),
            array("[v1.09azAZ-._~!$&'()*+,;=:]", true),
            array("[v1.09azAZ-._~!$&'()*+,;=:", false),
            array("v1.09azAZ-._~!$&'()*+,;=:]", false),
            array("v1.09azAZ-._~!$&'()*+,;=:", true),
            array("v1.09azAZ-._~!$&'()*+,;=", true),
            array("v1.09azAZ-._~!$&'()*+,;", true),
            array("v1.09azAZ-._~!$&'()*+,", true),
            array("v1.09azAZ-._~!$&'()*+", true),
            array("v1.09azAZ-._~!$&'()*", true),
            array("v1.09azAZ-._~!$&'()", true),
            array("v1.09azAZ-._~!$&'(", true),
            array("v1.09azAZ-._~!$&'", true),
            array("v1.09azAZ-._~!$&", true),
            array("v1.09azAZ-._~!$", true),
            array("v1.09azAZ-._~!", true),
            array("v1.09azAZ-._~", true),
            array("v1.09azAZ-._", true),
            array("v1.09azAZ-.", true),
            array("v1.09azAZ-", true),
            array("v1.09azAZ", true),
            array("v1.09azA", true),
            array("v1.09az", true),
            array("v1.09a", true),
            array("v1.09", true),
            array("v1.0", true),
            array("v1.", false),
            array("v1", false),
            array("v", false),
            array("", false),
            array("vFF.Z", true),
            array("vFG./", false),
            array("v1./", false),
            array("v1.?", false),
            array("v1.#", false),
            array("v1.[", false),
            array("v1.]", false),
            array("v1.@", false),
        );
    }

    /**
     * Ensures that getMessages() returns expected default value
     *
     * @return void
     */
    public function testGetMessages()
    {
        $this->assertEquals(array(), $this->validator->getMessages());
    }

    public function testEqualsMessageTemplates()
    {
        $validator = $this->validator;
        $this->assertAttributeEquals($validator->getOption('messageTemplates'),
                                     'messageTemplates', $validator);
    }
}
