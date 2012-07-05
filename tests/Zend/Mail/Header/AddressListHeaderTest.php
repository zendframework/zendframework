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
 * @package    Zend_Mail
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Mail\Header;

use Zend\Mail\Address,
    Zend\Mail\AddressList,
    Zend\Mail\Header\AbstractAddressList,
    Zend\Mail\Header\Bcc,
    Zend\Mail\Header\Cc,
    Zend\Mail\Header\From,
    Zend\Mail\Header\ReplyTo,
    Zend\Mail\Header\To;

/**
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Mail
 */
class AddressListHeaderTest extends \PHPUnit_Framework_TestCase
{
    public static function getHeaderInstances()
    {
        return array(
            array(new Bcc(), 'Bcc'),
            array(new Cc(), 'Cc'),
            array(new From(), 'From'),
            array(new ReplyTo(), 'Reply-To'),
            array(new To(), 'To'),
        );
    }

    /**
     * @dataProvider getHeaderInstances
     */
    public function testConcreteHeadersExtendAbstractAddressListHeader($header)
    {
        $this->assertInstanceOf('Zend\Mail\Header\AbstractAddressList', $header);
    }

    /**
     * @dataProvider getHeaderInstances
     */
    public function testConcreteHeaderFieldNamesAreDiscrete($header, $type)
    {
        $this->assertEquals($type, $header->getFieldName());
    }

    /**
     * @dataProvider getHeaderInstances
     */
    public function testConcreteHeadersComposeAddressLists($header)
    {
        $list = $header->getAddressList();
        $this->assertInstanceOf('Zend\Mail\AddressList', $list);
    }

    public function testFieldValueIsEmptyByDefault()
    {
        $header = new To();
        $this->assertEquals('', $header->getFieldValue());
    }

    public function testFieldValueIsCreatedFromAddressList()
    {
        $header = new To();
        $list   = $header->getAddressList();
        $this->populateAddressList($list);
        $expected = $this->getExpectedFieldValue();
        $this->assertEquals($expected, $header->getFieldValue());
    }

    public function populateAddressList(AddressList $list)
    {
        $address = new Address('zf-devteam@zend.com', 'ZF DevTeam');
        $list->add($address);
        $list->add('zf-contributors@lists.zend.com');
        $list->add('fw-announce@lists.zend.com', 'ZF Announce List');
    }

    public function getExpectedFieldValue()
    {
        return "ZF DevTeam <zf-devteam@zend.com>,\r\n zf-contributors@lists.zend.com,\r\n ZF Announce List <fw-announce@lists.zend.com>";
    }

    /**
     * @dataProvider getHeaderInstances
     */
    public function testStringRepresentationIncludesHeaderAndFieldValue($header, $type)
    {
        $this->populateAddressList($header->getAddressList());
        $expected = sprintf('%s: %s', $type, $this->getExpectedFieldValue());
        $this->assertEquals($expected, $header->toString());
    }

    public function getStringHeaders()
    {
        $value = $this->getExpectedFieldValue();
        return array(
            array('Cc: ' . $value, 'Zend\Mail\Header\Cc'),
            array('Bcc: ' . $value, 'Zend\Mail\Header\Bcc'),
            array('From: ' . $value, 'Zend\Mail\Header\From'),
            array('Reply-To: ' . $value, 'Zend\Mail\Header\ReplyTo'),
            array('To: ' . $value, 'Zend\Mail\Header\To'),
        );
    }

    /**
     * @dataProvider getStringHeaders
     */
    public function testDeserializationFromString($headerLine, $class)
    {
        $callback = sprintf('%s::fromString', $class);
        $header   = call_user_func($callback, $headerLine);
        $this->assertInstanceOf($class, $header);
        $list = $header->getAddressList();
        $this->assertEquals(3, count($list));
        $this->assertTrue($list->has('zf-devteam@zend.com'));
        $this->assertTrue($list->has('zf-contributors@lists.zend.com'));
        $this->assertTrue($list->has('fw-announce@lists.zend.com'));
        $address = $list->get('zf-devteam@zend.com');
        $this->assertEquals('ZF DevTeam', $address->getName());
        $address = $list->get('zf-contributors@lists.zend.com');
        $this->assertNull($address->getName());
        $address = $list->get('fw-announce@lists.zend.com');
        $this->assertEquals('ZF Announce List', $address->getName());
    }
}
