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
 * @package    Zend_XmlRpc
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\XmlRpc;
use Zend\XmlRpc\Value;
use Zend\XmlRpc\Generator;
use Zend\Crypt\Math\BigInteger;
use Zend\Date;

/**
 * Test case for Zend_XmlRpc_Value
 *
 * @category   Zend
 * @package    Zend_XmlRpc
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_XmlRpc
 */
class ValueTest extends \PHPUnit_Framework_TestCase
{
    // Boolean

    public function testFactoryAutodetectsBoolean()
    {
        foreach (array(true, false) as $native) {
            $val = Value::getXmlRpcValue($native);
            $this->assertXmlRpcType('boolean', $val);
        }
    }

    public function testMarshalBooleanFromNative()
    {
        $native = true;
        $val = Value::getXmlRpcValue($native,
                                    Value::XMLRPC_TYPE_BOOLEAN);

        $this->assertXmlRpcType('boolean', $val);
        $this->assertSame($native, $val->getValue());
    }

    /**
     * @dataProvider ZendTest\XmlRpc\TestProvider::provideGenerators
     */
    public function testMarshalBooleanFromXmlRpc(Generator $generator)
    {
        Value::setGenerator($generator);
        $xml = '<value><boolean>1</boolean></value>';
        $val = Value::getXmlRpcValue($xml,
                                    Value::XML_STRING);

        $this->assertXmlRpcType('boolean', $val);
        $this->assertEquals('boolean', $val->getType());
        $this->assertSame(true, $val->getValue());
        $this->assertEquals($this->wrapXml($xml), $val->saveXml());
    }

    // Integer

    public function testFactoryAutodetectsInteger()
    {
        $val = Value::getXmlRpcValue(1);
        $this->assertXmlRpcType('integer', $val);
    }

    public function testMarshalIntegerFromNative()
    {
        $native = 1;
        $types = array(Value::XMLRPC_TYPE_I4,
                       Value::XMLRPC_TYPE_INTEGER);

        foreach ($types as $type) {
            $val = Value::getXmlRpcValue($native, $type);
            $this->assertXmlRpcType('integer', $val);
            $this->assertSame($native, $val->getValue());
        }
    }

    /**
     * @dataProvider ZendTest\XmlRpc\TestProvider::provideGenerators
     */
    public function testMarshalIntegerFromXmlRpc(Generator $generator)
    {
        Value::setGenerator($generator);

        $native = 1;
        $xmls = array("<value><int>$native</int></value>",
                      "<value><i4>$native</i4></value>");

        foreach ($xmls as $xml) {
            $val = Value::getXmlRpcValue($xml,
                                        Value::XML_STRING);
            $this->assertXmlRpcType('integer', $val);
            $this->assertEquals('int', $val->getType());
            $this->assertSame($native, $val->getValue());
            $this->assertEquals($this->wrapXml($xml), $val->saveXml());
        }
    }

    /**
     * @group ZF-3310
     */
    public function testMarshalI4FromOverlongNativeThrowsException()
    {
        $this->setExpectedException('Zend\XmlRpc\Exception\ValueException', 'Overlong integer given');
        $x = Value::getXmlRpcValue(PHP_INT_MAX + 5000, Value::XMLRPC_TYPE_I4);
        var_dump($x);
    }

    /**
     * @group ZF-3310
     */
    public function testMarshalIntegerFromOverlongNativeThrowsException()
    {
        $this->setExpectedException('Zend\XmlRpc\Exception\ValueException', 'Overlong integer given');
        Value::getXmlRpcValue(PHP_INT_MAX + 5000, Value::XMLRPC_TYPE_INTEGER);
    }

    // BigInteger

    /**
     * @group ZF-6445
     * @dataProvider ZendTest\XmlRpc\TestProvider::provideGenerators
     */
    public function testMarshalBigIntegerFromFromXmlRpc(Generator $generator)
    {
        Value::setGenerator($generator);
        $bigInt = (string)(PHP_INT_MAX + 1);
        $native = new BigInteger();
        $native->init($bigInt);

        $xmlStrings = array("<value><i8>$bigInt</i8></value>",
                            "<value><ex:i8 xmlns:ex=\"http://ws.apache.org/xmlrpc/namespaces/extensions\">$bigInt</ex:i8></value>");

        foreach ($xmlStrings as $xml) {
            $value = Value::getXmlRpcValue($xml, Value::XML_STRING);
            $this->assertEquals($native, $value->getValue());
            $this->assertEquals('i8', $value->getType());
            $this->assertEquals($this->wrapXml($xml), $value->saveXml());
        }
    }

    /**
     * @group ZF-6445
     */
    public function testMarshalBigIntegerFromNative()
    {
        $native = (string)(PHP_INT_MAX + 1);
        $types = array(Value::XMLRPC_TYPE_APACHEI8,
                       Value::XMLRPC_TYPE_I8);

        $bigInt = new BigInteger();
        $bigInt->init($native);

        foreach ($types as $type) {
            $value = Value::getXmlRpcValue($native, $type);
            $this->assertSame('i8', $value->getType());
            $this->assertEquals($bigInt, $value->getValue());
        }

        $value = Value::getXmlRpcValue($bigInt);
        $this->assertSame('i8', $value->getType());
        $this->assertEquals($bigInt, $value->getValue());
    }

    // Double

    public function testFactoryAutodetectsFloat()
    {
        $val = Value::getXmlRpcValue((float)1);
        $this->assertXmlRpcType('double', $val);
    }

    public function testMarshalDoubleFromNative()
    {
        $native = 1.1;
        $val = Value::getXmlRpcValue($native,
                                    Value::XMLRPC_TYPE_DOUBLE);

        $this->assertXmlRpcType('double', $val);
        $this->assertSame($native, $val->getValue());
    }

    /**
     * @dataProvider ZendTest\XmlRpc\TestProvider::provideGenerators
     */
    public function testMarshalDoubleFromXmlRpc(Generator $generator)
    {
        Value::setGenerator($generator);
        $native = 1.1;
        $xml = "<value><double>$native</double></value>";
        $val = Value::getXmlRpcValue($xml,
                                    Value::XML_STRING);

        $this->assertXmlRpcType('double', $val);
        $this->assertEquals('double', $val->getType());
        $this->assertSame($native, $val->getValue());
        $this->assertEquals($this->wrapXml($xml), $val->saveXml());
    }

    /**
     * @group ZF-7712
     * @dataProvider ZendTest\XmlRpc\TestProvider::provideGenerators
     */
    public function testMarshallingDoubleWithHigherPrecisionFromNative(Generator $generator)
    {
        Value::setGenerator($generator);
        if (ini_get('precision') < 7) {
            $this->markTestSkipped('precision is too low');
        }

        $native = 0.1234567;
        $value = Value::getXmlRpcValue($native, Value::XMLRPC_TYPE_DOUBLE);
        $this->assertXmlRpcType('double', $value);
        $this->assertSame($native, $value->getValue());
        $this->assertSame('<value><double>0.1234567</double></value>', trim($value->saveXml()));
    }

    /**
     * @group ZF-7712
     * @dataProvider ZendTest\XmlRpc\TestProvider::provideGenerators
     */
    public function testMarshallingDoubleWithHigherPrecisionFromNativeWithTrailingZeros(Generator $generator)
    {
        Value::setGenerator($generator);
        if (ini_get('precision') < 7) {
            $this->markTestSkipped('precision is too low');
        }
        $native = 0.1;
        $value = Value::getXmlRpcValue($native, Value::XMLRPC_TYPE_DOUBLE);
        $this->assertXmlRpcType('double', $value);
        $this->assertSame($native, $value->getValue());
        $this->assertSame('<value><double>0.1</double></value>', trim($value->saveXml()));
    }

    // String

    public function testFactoryAutodetectsString()
    {
        $val = Value::getXmlRpcValue('');
        $this->assertXmlRpcType('string', $val);
    }


    public function testMarshalStringFromNative()
    {
        $native = 'foo';
        $val = Value::getXmlRpcValue($native,
                                    Value::XMLRPC_TYPE_STRING);

        $this->assertXmlRpcType('string', $val);
        $this->assertSame($native, $val->getValue());
    }

    /**
     * @dataProvider ZendTest\XmlRpc\TestProvider::provideGenerators
     */
    public function testMarshalStringFromXmlRpc(Generator $generator)
    {
        Value::setGenerator($generator);
        $native = 'foo<>';
        $xml = "<value><string>foo&lt;&gt;</string></value>";
        $val = Value::getXmlRpcValue($xml,
                                    Value::XML_STRING);

        $this->assertXmlRpcType('string', $val);
        $this->assertEquals('string', $val->getType());
        $this->assertSame($native, $val->getValue());
        $this->assertEquals($this->wrapXml($xml), $val->saveXml());
    }

    /**
     * @dataProvider ZendTest\XmlRpc\TestProvider::provideGenerators
     */
    public function testMarshalStringFromDefault(Generator $generator)
    {
        Value::setGenerator($generator);
        $native = 'foo<br/>bar';
        $xml = "<string>foo&lt;br/&gt;bar</string>";
        $val = Value::getXmlRpcValue($xml,
                                    Value::XML_STRING);

        $this->assertXmlRpcType('string', $val);
        $this->assertEquals('string', $val->getType());
        $this->assertSame($native, $val->getValue());
        $this->assertEquals($this->wrapXml($xml), $val->saveXml());
    }

    //Nil

    public function testFactoryAutodetectsNil()
    {
        $val = Value::getXmlRpcValue(NULL);
        $this->assertXmlRpcType('nil', $val);
    }

    public function testMarshalNilFromNative()
    {
        $native = NULL;
        $types = array(Value::XMLRPC_TYPE_NIL,
                       Value::XMLRPC_TYPE_APACHENIL);
        foreach ($types as $type) {
            $value = Value::getXmlRpcValue($native, $type);

            $this->assertXmlRpcType('nil', $value);
            $this->assertSame($native, $value->getValue());
        }
    }

    /**
     * @dataProvider ZendTest\XmlRpc\TestProvider::provideGenerators
     */
    public function testMarshalNilFromXmlRpc(Generator $generator)
    {
        Value::setGenerator($generator);
        $xmls = array('<value><nil/></value>',
                     '<value><ex:nil xmlns:ex="http://ws.apache.org/xmlrpc/namespaces/extensions"/></value>');

        foreach ($xmls as $xml) {
            $val = Value::getXmlRpcValue($xml,
                                        Value::XML_STRING);
            $this->assertXmlRpcType('nil', $val);
            $this->assertEquals('nil', $val->getType());
            $this->assertSame(NULL, $val->getValue());
            $this->assertEquals($this->wrapXml($xml), $val->saveXml());
        }
    }

    // Array

    public function testFactoryAutodetectsArray()
    {
        $val = Value::getXmlRpcValue(array(0, 'foo'));
        $this->assertXmlRpcType('array', $val);
    }

    public function testMarshalArrayFromNative()
    {
        $native = array(0,1);
        $val = Value::getXmlRpcValue($native,
                                    Value::XMLRPC_TYPE_ARRAY);

        $this->assertXmlRpcType('array', $val);
        $this->assertSame($native, $val->getValue());
    }

    /**
     * @dataProvider ZendTest\XmlRpc\TestProvider::provideGenerators
     */
    public function testMarshalArrayFromXmlRpc(Generator $generator)
    {
        Value::setGenerator($generator);
        $native = array(0,1);
        $xml = '<value><array><data><value><int>0</int></value>'
             . '<value><int>1</int></value></data></array></value>';

        $val = Value::getXmlRpcValue($xml,
                                    Value::XML_STRING);

        $this->assertXmlRpcType('array', $val);
        $this->assertEquals('array', $val->getType());
        $this->assertSame($native, $val->getValue());
        $this->assertEquals($this->wrapXml($xml), $val->saveXml());
    }

    /**
     * @dataProvider ZendTest\XmlRpc\TestProvider::provideGenerators
     */
    public function testEmptyXmlRpcArrayResultsInEmptyArray(Generator $generator)
    {
        Value::setGenerator($generator);
        $native = array();
        $xml    = '<value><array><data/></array></value>';

        $val = Value::getXmlRpcValue($xml,
                                    Value::XML_STRING);

        $this->assertXmlRpcType('array', $val);
        $this->assertEquals('array', $val->getType());
        $this->assertSame($native, $val->getValue());

        $value = Value::getXmlRpcValue($xml, Value::XML_STRING);
        $this->assertXmlRpcType('array', $value);
        $this->assertEquals('array', $value->getType());
        $this->assertSame($native, $value->getValue());
    }

    /**
     * @dataProvider ZendTest\XmlRpc\TestProvider::provideGenerators
     */
    public function testArrayMustContainDataElement(Generator $generator)
    {
        Value::setGenerator($generator);
        $native = array();
        $xml    = '<value><array/></value>';

        $this->setExpectedException('Zend\XmlRpc\Exception\ValueException',
        	'Invalid XML for XML-RPC native array type: ARRAY tag must contain DATA tag'
        	);
        $val = Value::getXmlRpcValue($xml, Value::XML_STRING);
    }

    /**
     * @group ZF-5405
     * @dataProvider ZendTest\XmlRpc\TestProvider::provideGenerators
     */
    public function testMarshalNilInStructWrappedInArray(Generator $generator)
    {
        Value::setGenerator($generator);
        $expected = array(array('id' => '1', 'name' => 'vertebra, caudal', 'description' => null));
        $xml = '<value>'
             . '<array><data><value><struct><member><name>id</name><value><string>1</string></value></member>'
             . '<member><name>name</name><value><string>vertebra, caudal</string></value></member>'
             . '<member><name>description</name><value><nil/></value></member></struct></value></data></array>'
             . '</value>';
        $val = Value::getXmlRpcValue($xml, Value::XML_STRING);
        $this->assertSame($expected, $val->getValue());
    }

    // Struct

    public function testFactoryAutodetectsStruct()
    {
        $val = Value::getXmlRpcValue(array('foo' => 0));
        $this->assertXmlRpcType('struct', $val);
    }

    public function testFactoryAutodetectsStructFromObject()
    {
        $val = Value::getXmlRpcValue((object)array('foo' => 0));
        $this->assertXmlRpcType('struct', $val);
    }

    public function testMarshalStructFromNative()
    {
        $native = array('foo' => 0);
        $val = Value::getXmlRpcValue($native,
                                    Value::XMLRPC_TYPE_STRUCT);

        $this->assertXmlRpcType('struct', $val);
        $this->assertSame($native, $val->getValue());
    }

    /**
     * @dataProvider ZendTest\XmlRpc\TestProvider::provideGenerators
     */
    public function testMarshalStructFromXmlRpc(Generator $generator)
    {
        Value::setGenerator($generator);
        $native = array('foo' => 0, 'bar' => 'foo<>bar');
        $xml = '<value><struct><member><name>foo</name><value><int>0</int>'
             . '</value></member><member><name>bar</name><value><string>'
             . 'foo&lt;&gt;bar</string></value></member></struct></value>';

        $val = Value::getXmlRpcValue($xml,
                                    Value::XML_STRING);

        $this->assertXmlRpcType('struct', $val);
        $this->assertEquals('struct', $val->getType());
        $this->assertSame($native, $val->getValue());
        $this->assertEquals($this->wrapXml($xml), $val->saveXml());
    }

    /**
     * @dataProvider ZendTest\XmlRpc\TestProvider::provideGenerators
     */
    public function testMarshallingNestedStructFromXmlRpc(Generator $generator)
    {
        Value::setGenerator($generator);
        $native = array('foo' => array('bar' => '<br/>'));
        $xml = '<value><struct><member><name>foo</name><value><struct><member>'
             . '<name>bar</name><value><string>&lt;br/&gt;</string></value>'
             . '</member></struct></value></member></struct></value>';

        $val = Value::getXmlRpcValue($xml, Value::XML_STRING);

        $this->assertXmlRpcType('struct', $val);
        $this->assertEquals('struct', $val->getType());
        $this->assertSame($native, $val->getValue());
        $this->assertSame($this->wrapXml($xml), $val->saveXml());

        $val = Value::getXmlRpcValue($native);
        $this->assertSame(trim($xml), trim($val->saveXml()));
    }

    /**
     * @dataProvider ZendTest\XmlRpc\TestProvider::provideGenerators
     */
    public function testMarshallingStructWithMemberWithoutValue(Generator $generator)
    {
        Value::setGenerator($generator);
        $native = array('foo' => 0, 'bar' => 1);
        $xml = '<value><struct>'
             . '<member><name>foo</name><value><int>0</int></value></member>'
             . '<member><name>foo</name><bar/></member>'
             . '<member><name>bar</name><value><int>1</int></value></member>'
             . '</struct></value>';

        $val = Value::getXmlRpcValue($xml,
                                    Value::XML_STRING);

        $this->assertXmlRpcType('struct', $val);
        $this->assertEquals('struct', $val->getType());
        $this->assertSame($native, $val->getValue());
        $this->assertEquals($this->wrapXml($xml), $val->saveXml());
    }

    /**
     * @dataProvider ZendTest\XmlRpc\TestProvider::provideGenerators
     */
    public function testMarshallingStructWithMemberWithoutName(Generator $generator)
    {
        Value::setGenerator($generator);
        $native = array('foo' => 0, 'bar' => 1);
        $xml = '<value><struct>'
             . '<member><name>foo</name><value><int>0</int></value></member>'
             . '<member><value><string>foo</string></value></member>'
             . '<member><name>bar</name><value><int>1</int></value></member>'
             . '</struct></value>';

        $val = Value::getXmlRpcValue($xml,
                                    Value::XML_STRING);

        $this->assertXmlRpcType('struct', $val);
        $this->assertEquals('struct', $val->getType());
        $this->assertSame($native, $val->getValue());
        $this->assertEquals($this->wrapXml($xml), $val->saveXml());
    }

    /**
     * @group ZF-7639
     * @dataProvider ZendTest\XmlRpc\TestProvider::provideGenerators
     */
    public function testMarshalStructFromXmlRpcWithEntities(Generator $generator)
    {
        Value::setGenerator($generator);
        $native = array('&nbsp;' => 0);
        $xml = '<value><struct><member><name>&amp;nbsp;</name><value><int>0</int>'
             . '</value></member></struct></value>';
        $val = Value::getXmlRpcValue($xml, Value::XML_STRING);
        $this->assertXmlRpcType('struct', $val);
        $this->assertSame($native, $val->getValue());
        $this->assertSame($this->wrapXml($xml), $val->saveXml());
    }

    /**
     * @group ZF-3947
     * @dataProvider ZendTest\XmlRpc\TestProvider::provideGenerators
     */
    public function testMarshallingStructsWithEmptyValueFromXmlRpcShouldRetainKeys(Generator $generator)
    {
        Value::setGenerator($generator);
        $native = array('foo' => '');
        $xml = '<value><struct><member><name>foo</name>'
             . '<value><string/></value></member></struct></value>';

        $val = Value::getXmlRpcValue($xml,
                                    Value::XML_STRING);

        $this->assertXmlRpcType('struct', $val);
        $this->assertEquals('struct', $val->getType());
        $this->assertSame($native, $val->getValue());
        $this->assertEquals($this->wrapXml($xml), $val->saveXml());
    }

    /**
     * @dataProvider ZendTest\XmlRpc\TestProvider::provideGenerators
     */
    public function testMarshallingStructWithMultibyteValueFromXmlRpcRetainsMultibyteValue(Generator $generator)
    {
        Value::setGenerator($generator);
        $native = array('foo' => 'ß');
        $xmlDecl = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml = '<value><struct><member><name>foo</name><value><string>ß</string></value></member></struct></value>';

        $val = Value::getXmlRpcValue($xmlDecl . $xml,
                                    Value::XML_STRING);

        $this->assertXmlRpcType('struct', $val);
        $this->assertEquals('struct', $val->getType());
        $this->assertSame($native, $val->getValue());
        $this->assertEquals($this->wrapXml($xml), $val->saveXml());

        $val = Value::getXmlRpcValue($native, Value::XMLRPC_TYPE_STRUCT);
        $this->assertSame($native, $val->getValue());
        $this->assertSame(trim($xml), trim($val->saveXml()));
    }

    // DateTime

    public function testMarshalDateTimeFromNativeString()
    {
        $native = '1997-07-16T19:20+01:00';
        $val = Value::getXmlRpcValue($native,
                                    Value::XMLRPC_TYPE_DATETIME);

        $this->assertXmlRpcType('dateTime', $val);

        $expected = '1997-07-16T19:20+01:00';
        $this->assertSame(strtotime($native), strtotime($val->getValue()));
    }

    public function testMarshalDateTimeFromNativeStringProducesIsoOutput()
    {
        $native = '1997-07-16T19:20+01:00';
        $val = Value::getXmlRpcValue($native,
                                    Value::XMLRPC_TYPE_DATETIME);

        $this->assertXmlRpcType('dateTime', $val);

        $expected = date('c', strtotime($native));
        $expected = substr($expected, 0, strlen($expected) - 6);
        $expected = str_replace('-', '', $expected);
        $received = $val->getValue();
        $this->assertEquals($expected, $received);
    }

    public function testMarshalDateTimeFromInvalidString()
    {
        $this->setExpectedException('Zend\XmlRpc\Exception\ValueException', "Cannot convert given value 'foobarbaz' to a timestamp");
        Value::getXmlRpcValue('foobarbaz', Value::XMLRPC_TYPE_DATETIME);
    }

    public function testMarshalDateTimeFromNativeInteger()
    {
        $native = strtotime('1997-07-16T19:20+01:00');
        $val = Value::getXmlRpcValue($native,
                                    Value::XMLRPC_TYPE_DATETIME);

        $this->assertXmlRpcType('dateTime', $val);
        $this->assertSame($native, strtotime($val->getValue()));
    }

    /**
     * @dataProvider ZendTest\XmlRpc\TestProvider::provideGenerators
     */
    public function testMarshalDateTimeFromXmlRpc(Generator $generator)
    {
        Value::setGenerator($generator);
        $iso8601 = '1997-07-16T19:20+01:00';
        $xml = "<value><dateTime.iso8601>$iso8601</dateTime.iso8601></value>";

        $val = Value::getXmlRpcValue($xml,
                                    Value::XML_STRING);

        $this->assertXmlRpcType('dateTime', $val);
        $this->assertEquals('dateTime.iso8601', $val->getType());
        $this->assertSame(strtotime($iso8601), strtotime($val->getValue()));
        $this->assertEquals($this->wrapXml($xml), $val->saveXml());
    }

    /**
     * @dataProvider ZendTest\XmlRpc\TestProvider::provideGenerators
     * @group ZF-4249
     */
    public function testMarshalDateTimeFromFromZendDate(Generator $generator)
    {
        Value::setGenerator($generator);
        $date = new Date\Date(array('year' => 2039, 'month' => 4, 'day' => 18,
                                    'hour' => 13, 'minute' => 14, 'second' => 15));
        $dateString = '20390418T13:14:15';
        $xml = "<value><dateTime.iso8601>$dateString</dateTime.iso8601></value>";

        $val = Value::getXmlRpcValue($date, Value::XMLRPC_TYPE_DATETIME);
        $this->assertXmlRpcType('dateTime', $val);
        $this->assertEquals('dateTime.iso8601', $val->getType());
        $this->assertSame($dateString, $val->getValue());
        $this->assertEquals(trim($xml), trim($val->saveXml()));

    }

    /**
     * @dataProvider ZendTest\XmlRpc\TestProvider::provideGenerators
     * @group ZF-4249
     */
    public function testMarshalDateTimeFromZendDateAndAutodetectingType(Generator $generator)
    {
        Value::setGenerator($generator);
        $date = new Date\Date(array('year' => 2039, 'month' => 4, 'day' => 18,
                                    'hour' => 13, 'minute' => 14, 'second' => 15));
        $dateString = '20390418T13:14:15';
        $xml = "<value><dateTime.iso8601>$dateString</dateTime.iso8601></value>";

        $val = Value::getXmlRpcValue($date, Value::AUTO_DETECT_TYPE);
        $this->assertXmlRpcType('dateTime', $val);
        $this->assertEquals('dateTime.iso8601', $val->getType());
        $this->assertSame($dateString, $val->getValue());
        $this->assertEquals(trim($xml), trim($val->saveXml()));
    }

    /**
     * @dataProvider ZendTest\XmlRpc\TestProvider::provideGenerators
     * @group ZF-4249
     */
    public function testMarshalDateTimeFromFromDateTime(Generator $generator)
    {
        Value::setGenerator($generator);
        $dateString = '20390418T13:14:15';
        $date = new \DateTime($dateString);
        $dateString = '20390418T13:14:15';
        $xml = "<value><dateTime.iso8601>$dateString</dateTime.iso8601></value>";

        $val = Value::getXmlRpcValue($date, Value::XMLRPC_TYPE_DATETIME);
        $this->assertXmlRpcType('dateTime', $val);
        $this->assertEquals('dateTime.iso8601', $val->getType());
        $this->assertSame($dateString, $val->getValue());
        $this->assertEquals(trim($xml), trim($val->saveXml()));

    }

    /**
     * @dataProvider ZendTest\XmlRpc\TestProvider::provideGenerators
     * @group ZF-4249
     */
    public function testMarshalDateTimeFromDateTimeAndAutodetectingType(Generator $generator)
    {
        Value::setGenerator($generator);
        $dateString = '20390418T13:14:15';
        $date = new \DateTime($dateString);
        $xml = "<value><dateTime.iso8601>$dateString</dateTime.iso8601></value>";

        $val = Value::getXmlRpcValue($date, Value::AUTO_DETECT_TYPE);
        $this->assertXmlRpcType('dateTime', $val);
        $this->assertEquals('dateTime.iso8601', $val->getType());
        $this->assertSame($dateString, $val->getValue());
        $this->assertEquals(trim($xml), trim($val->saveXml()));
    }

    // Base64

    public function testMarshalBase64FromString()
    {
        $native = 'foo';
        $val = Value::getXmlRpcValue($native,
                                    Value::XMLRPC_TYPE_BASE64);

        $this->assertXmlRpcType('base64', $val);
        $this->assertSame($native, $val->getValue());
    }

    /**
     * @dataProvider ZendTest\XmlRpc\TestProvider::provideGenerators
     */
    public function testMarshalBase64FromXmlRpc(Generator $generator)
    {
        Value::setGenerator($generator);
        $native = 'foo';
        $xml = '<value><base64>' .base64_encode($native). '</base64></value>';

        $val = Value::getXmlRpcValue($xml,
                                    Value::XML_STRING);

        $this->assertXmlRpcType('base64', $val);
        $this->assertEquals('base64', $val->getType());
        $this->assertSame($native, $val->getValue());
        $this->assertEquals($this->wrapXml($xml), $val->saveXml());
    }

    public function testXmlRpcValueBase64GeneratedXmlContainsBase64EncodedText()
    {
        $native = 'foo';
        $val = Value::getXmlRpcValue($native,
                                    Value::XMLRPC_TYPE_BASE64);

        $this->assertXmlRpcType('base64', $val);
        $xml = $val->saveXml();
        $encoded = base64_encode($native);
        $this->assertContains($encoded, $xml);
    }

    /**
     * @group ZF-3862
     */
    public function testMarshalSerializedObjectAsBase64()
    {
        $o = new SerializableTestClass();
        $o->setProperty('foobar');
        $serialized = serialize($o);
        $val = Value::getXmlRpcValue($serialized,
                                    Value::XMLRPC_TYPE_BASE64);

        $this->assertXmlRpcType('base64', $val);
        $o2 = unserialize($val->getValue());
        $this->assertSame('foobar', $o2->getProperty());
    }

    public function testChangingExceptionResetsGeneratorObject()
    {
        $generator = Value::getGenerator();
        Value::setEncoding('UTF-8');
        $this->assertNotSame($generator, Value::getGenerator());
        $this->assertEquals($generator, Value::getGenerator());

        $generator = Value::getGenerator();
        Value::setEncoding('ISO-8859-1');
        $this->assertNotSame($generator, Value::getGenerator());
        $this->assertNotEquals($generator, Value::getGenerator());
    }

    // Exceptions

    public function testFactoryThrowsWhenInvalidTypeSpecified()
    {
        $this->setExpectedException('Zend\XmlRpc\Exception\ValueException', 'Given type is not a Zend\XmlRpc\Value constant');
        Value::getXmlRpcValue('', 'bad type here');
    }

    public function testPassingXmlRpcObjectReturnsTheSameObject()
    {
        $xmlRpcValue = new Value\String('foo');
        $this->assertSame($xmlRpcValue, Value::getXmlRpcValue($xmlRpcValue));
    }

    // Custom Assertions and Helper Methods

    public function assertXmlRpcType($type, $object)
    {
        if ($type == 'array') {
            $type = 'arrayValue';
        }
        $type = 'Zend\\XmlRpc\\Value\\' . ucfirst($type);
        $this->assertInstanceOf($type, $object);
    }

    public function wrapXml($xml)
    {
        return $xml . "\n";
    }
}

class SerializableTestClass
{
    protected $_property;
    public function setProperty($property)
    {
        $this->_property = $property;
    }

    public function getProperty()
    {
        return $this->_property;
    }
}
