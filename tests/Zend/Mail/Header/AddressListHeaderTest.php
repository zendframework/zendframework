<?php

namespace ZendTest\Mail\Header;

use PHPUnit_Framework_TestCase as TestCase,
    Zend\Mail\Address,
    Zend\Mail\AddressList,
    Zend\Mail\Header\AbstractAddressList,
    Zend\Mail\Header\Bcc,
    Zend\Mail\Header\Cc,
    Zend\Mail\Header\From,
    Zend\Mail\Header\ReplyTo,
    Zend\Mail\Header\To;

class AddressListHeaderTest extends TestCase
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
        return 'ZF DevTeam <zf-devteam@zend.com>, <zf-contributors@lists.zend.com>, ZF Announce List <fw-announce@lists.zend.com>';
    }

    /**
     * @dataProvider getHeaderInstances
     */
    public function testStringRepresentationIncludesHeaderAndFieldValue($header, $type)
    {
        $this->populateAddressList($header->getAddressList());
        $expected = sprintf("%s: %s\r\n", $type, $this->getExpectedFieldValue());
        $this->assertEquals($expected, $header->toString());
    }
}
