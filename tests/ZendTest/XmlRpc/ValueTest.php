<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_XmlRpc
 */

namespace ZendTest\XmlRpc;

use stdClass;
use DateTime;
use Zend\XmlRpc\AbstractValue;
use Zend\XmlRpc\Value;
use Zend\XmlRpc\Generator\GeneratorInterface as Generator;

/**
 * Test case for Value
 *
 * @category   Zend
 * @package    Zend_XmlRpc
 * @subpackage UnitTests
 * @group      Zend_XmlRpc
 */
class ValueTest extends \PHPUnit_Framework_TestCase
{
    public $xmlRpcDateFormat = 'Ymd\\TH:i:s';

    // Boolean

    public function testFactoryAutodetectsBoolean()
    {
        foreach (array(true, false) as $native) {
            $val = AbstractValue::getXmlRpcValue($native);
            $this->assertXmlRpcType('boolean', $val);
        }
    }

    public function testMarshalBooleanFromNative()
    {
        $native = true;
        $val = AbstractValue::getXmlRpcValue($native,
                                    AbstractValue::XMLRPC_TYPE_BOOLEAN);

        $this->assertXmlRpcType('boolean', $val);
        $this->assertSame($native, $val->getValue());
    }

    /**
     * @dataProvider ZendTest\XmlRpc\TestProvider::provideGenerators
     */
    public function testMarshalBooleanFromXmlRpc(Generator $generator)
    {
        AbstractValue::setGenerator($generator);
        $xml = '<value><boolean>1</boolean></value>';
        $val = AbstractValue::getXmlRpcValue($xml,
                                    AbstractValue::XML_STRING);

        $this->assertXmlRpcType('boolean', $val);
        $this->assertEquals('boolean', $val->getType());
        $this->assertSame(true, $val->getValue());
        $this->assertEquals($this->wrapXml($xml), $val->saveXml());
    }

    // Integer

    public function testFactoryAutodetectsInteger()
    {
        $val = AbstractValue::getXmlRpcValue(1);
        $this->assertXmlRpcType('integer', $val);
    }

    public function testMarshalIntegerFromNative()
    {
        $native = 1;
        $types = array(AbstractValue::XMLRPC_TYPE_I4,
                       AbstractValue::XMLRPC_TYPE_INTEGER);

        foreach ($types as $type) {
            $val = AbstractValue::getXmlRpcValue($native, $type);
            $this->assertXmlRpcType('integer', $val);
            $this->assertSame($native, $val->getValue());
        }
    }

    /**
     * @dataProvider ZendTest\XmlRpc\TestProvider::provideGenerators
     */
    public function testMarshalIntegerFromXmlRpc(Generator $generator)
    {
        AbstractValue::setGenerator($generator);

        $native = 1;
        $xmls = array("<value><int>$native</int></value>",
                      "<value><i4>$native</i4></value>");

        foreach ($xmls as $xml) {
            $val = AbstractValue::getXmlRpcValue($xml,
                                        AbstractValue::XML_STRING);
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
        $x = AbstractValue::getXmlRpcValue(PHP_INT_MAX + 5000, AbstractValue::XMLRPC_TYPE_I4);
        var_dump($x);
    }

    /**
     * @group ZF-3310
     */
    public function testMarshalIntegerFromOverlongNativeThrowsException()
    {
        $this->setExpectedException('Zend\XmlRpc\Exception\ValueException', 'Overlong integer given');
        AbstractValue::getXmlRpcValue(PHP_INT_MAX + 5000, AbstractValue::XMLRPC_TYPE_INTEGER);
    }

    // Double

    public function testFactoryAutodetectsFloat()
    {
        $val = AbstractValue::getXmlRpcValue((float)1);
        $this->assertXmlRpcType('double', $val);
    }

    public function testMarshalDoubleFromNative()
    {
        $native = 1.1;
        $val = AbstractValue::getXmlRpcValue($native,
                                    AbstractValue::XMLRPC_TYPE_DOUBLE);

        $this->assertXmlRpcType('double', $val);
        $this->assertSame($native, $val->getValue());
    }

    /**
     * @dataProvider ZendTest\XmlRpc\TestProvider::provideGenerators
     */
    public function testMarshalDoubleFromXmlRpc(Generator $generator)
    {
        AbstractValue::setGenerator($generator);
        $native = 1.1;
        $xml = "<value><double>$native</double></value>";
        $val = AbstractValue::getXmlRpcValue($xml,
                                    AbstractValue::XML_STRING);

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
        AbstractValue::setGenerator($generator);
        if (ini_get('precision') < 7) {
            $this->markTestSkipped('precision is too low');
        }

        $native = 0.1234567;
        $value = AbstractValue::getXmlRpcValue($native, AbstractValue::XMLRPC_TYPE_DOUBLE);
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
        AbstractValue::setGenerator($generator);
        if (ini_get('precision') < 7) {
            $this->markTestSkipped('precision is too low');
        }
        $native = 0.1;
        $value = AbstractValue::getXmlRpcValue($native, AbstractValue::XMLRPC_TYPE_DOUBLE);
        $this->assertXmlRpcType('double', $value);
        $this->assertSame($native, $value->getValue());
        $this->assertSame('<value><double>0.1</double></value>', trim($value->saveXml()));
    }

    // String

    public function testFactoryAutodetectsString()
    {
        $val = AbstractValue::getXmlRpcValue('');
        $this->assertXmlRpcType('string', $val);
    }


    public function testMarshalStringFromNative()
    {
        $native = 'foo';
        $val = AbstractValue::getXmlRpcValue($native,
                                    AbstractValue::XMLRPC_TYPE_STRING);

        $this->assertXmlRpcType('string', $val);
        $this->assertSame($native, $val->getValue());
    }

    /**
     * @dataProvider ZendTest\XmlRpc\TestProvider::provideGenerators
     */
    public function testMarshalStringFromXmlRpc(Generator $generator)
    {
        AbstractValue::setGenerator($generator);
        $native = 'foo<>';
        $xml = "<value><string>foo&lt;&gt;</string></value>";
        $val = AbstractValue::getXmlRpcValue($xml,
                                    AbstractValue::XML_STRING);

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
        AbstractValue::setGenerator($generator);
        $native = 'foo<br/>bar';
        $xml = "<string>foo&lt;br/&gt;bar</string>";
        $val = AbstractValue::getXmlRpcValue($xml,
                                    AbstractValue::XML_STRING);

        $this->assertXmlRpcType('string', $val);
        $this->assertEquals('string', $val->getType());
        $this->assertSame($native, $val->getValue());
        $this->assertEquals($this->wrapXml($xml), $val->saveXml());
    }

    //Nil

    public function testFactoryAutodetectsNil()
    {
        $val = AbstractValue::getXmlRpcValue(NULL);
        $this->assertXmlRpcType('nil', $val);
    }

    public function testMarshalNilFromNative()
    {
        $native = NULL;
        $types = array(AbstractValue::XMLRPC_TYPE_NIL,
                       AbstractValue::XMLRPC_TYPE_APACHENIL);
        foreach ($types as $type) {
            $value = AbstractValue::getXmlRpcValue($native, $type);

            $this->assertXmlRpcType('nil', $value);
            $this->assertSame($native, $value->getValue());
        }
    }

    /**
     * @dataProvider ZendTest\XmlRpc\TestProvider::provideGenerators
     */
    public function testMarshalNilFromXmlRpc(Generator $generator)
    {
        AbstractValue::setGenerator($generator);
        $xmls = array('<value><nil/></value>',
                     '<value><ex:nil xmlns:ex="http://ws.apache.org/xmlrpc/namespaces/extensions"/></value>');

        foreach ($xmls as $xml) {
            $val = AbstractValue::getXmlRpcValue($xml,
                                        AbstractValue::XML_STRING);
            $this->assertXmlRpcType('nil', $val);
            $this->assertEquals('nil', $val->getType());
            $this->assertSame(NULL, $val->getValue());
            $this->assertEquals($this->wrapXml($xml), $val->saveXml());
        }
    }

    // Array

    public function testFactoryAutodetectsArray()
    {
        $val = AbstractValue::getXmlRpcValue(array(0, 'foo'));
        $this->assertXmlRpcType('array', $val);
    }

    public function testMarshalArrayFromNative()
    {
        $native = array(0,1);
        $val = AbstractValue::getXmlRpcValue($native,
                                    AbstractValue::XMLRPC_TYPE_ARRAY);

        $this->assertXmlRpcType('array', $val);
        $this->assertSame($native, $val->getValue());
    }

    /**
     * @dataProvider ZendTest\XmlRpc\TestProvider::provideGenerators
     */
    public function testMarshalArrayFromXmlRpc(Generator $generator)
    {
        AbstractValue::setGenerator($generator);
        $native = array(0,1);
        $xml = '<value><array><data><value><int>0</int></value>'
             . '<value><int>1</int></value></data></array></value>';

        $val = AbstractValue::getXmlRpcValue($xml,
                                    AbstractValue::XML_STRING);

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
        AbstractValue::setGenerator($generator);
        $native = array();
        $xml    = '<value><array><data/></array></value>';

        $val = AbstractValue::getXmlRpcValue($xml,
                                    AbstractValue::XML_STRING);

        $this->assertXmlRpcType('array', $val);
        $this->assertEquals('array', $val->getType());
        $this->assertSame($native, $val->getValue());

        $value = AbstractValue::getXmlRpcValue($xml, AbstractValue::XML_STRING);
        $this->assertXmlRpcType('array', $value);
        $this->assertEquals('array', $value->getType());
        $this->assertSame($native, $value->getValue());
    }

    /**
     * @dataProvider ZendTest\XmlRpc\TestProvider::provideGenerators
     */
    public function testArrayMustContainDataElement(Generator $generator)
    {
        AbstractValue::setGenerator($generator);
        $native = array();
        $xml    = '<value><array/></value>';

        $this->setExpectedException('Zend\XmlRpc\Exception\ValueException',
            'Invalid XML for XML-RPC native array type: ARRAY tag must contain DATA tag'
            );
        $val = AbstractValue::getXmlRpcValue($xml, AbstractValue::XML_STRING);
    }

    /**
     * @group ZF-5405
     * @dataProvider ZendTest\XmlRpc\TestProvider::provideGenerators
     */
    public function testMarshalNilInStructWrappedInArray(Generator $generator)
    {
        if (version_compare(phpversion(), '5.4', '>=')) {
            // SimpleXMLElement don't understand NIL
            $this->markTestIncomplete('Code to test is not compatible with PHP 5.4');
        }

        AbstractValue::setGenerator($generator);
        $expected = array(array('id' => '1', 'name' => 'vertebra, caudal', 'description' => null));
        $xml = '<value>'
             . '<array><data><value><struct><member><name>id</name><value><string>1</string></value></member>'
             . '<member><name>name</name><value><string>vertebra, caudal</string></value></member>'
             . '<member><name>description</name><value><nil/></value></member></struct></value></data></array>'
             . '</value>';
        $val = AbstractValue::getXmlRpcValue($xml, AbstractValue::XML_STRING);
        $this->assertSame($expected, $val->getValue());
    }

    // Struct

    public function testFactoryAutodetectsStruct()
    {
        $val = AbstractValue::getXmlRpcValue(array('foo' => 0));
        $this->assertXmlRpcType('struct', $val);
    }

    public function testFactoryAutodetectsStructFromObject()
    {
        $val = AbstractValue::getXmlRpcValue((object)array('foo' => 0));
        $this->assertXmlRpcType('struct', $val);
    }

    public function testMarshalStructFromNative()
    {
        $native = array('foo' => 0);
        $val = AbstractValue::getXmlRpcValue($native,
                                    AbstractValue::XMLRPC_TYPE_STRUCT);

        $this->assertXmlRpcType('struct', $val);
        $this->assertSame($native, $val->getValue());
    }

    /**
     * @dataProvider ZendTest\XmlRpc\TestProvider::provideGenerators
     */
    public function testMarshalStructFromXmlRpc(Generator $generator)
    {
        AbstractValue::setGenerator($generator);
        $native = array('foo' => 0, 'bar' => 'foo<>bar');
        $xml = '<value><struct><member><name>foo</name><value><int>0</int>'
             . '</value></member><member><name>bar</name><value><string>'
             . 'foo&lt;&gt;bar</string></value></member></struct></value>';

        $val = AbstractValue::getXmlRpcValue($xml,
                                    AbstractValue::XML_STRING);

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
        AbstractValue::setGenerator($generator);
        $native = array('foo' => array('bar' => '<br/>'));
        $xml = '<value><struct><member><name>foo</name><value><struct><member>'
             . '<name>bar</name><value><string>&lt;br/&gt;</string></value>'
             . '</member></struct></value></member></struct></value>';

        $val = AbstractValue::getXmlRpcValue($xml, AbstractValue::XML_STRING);

        $this->assertXmlRpcType('struct', $val);
        $this->assertEquals('struct', $val->getType());
        $this->assertSame($native, $val->getValue());
        $this->assertSame($this->wrapXml($xml), $val->saveXml());

        $val = AbstractValue::getXmlRpcValue($native);
        $this->assertSame(trim($xml), trim($val->saveXml()));
    }

    /**
     * @dataProvider ZendTest\XmlRpc\TestProvider::provideGenerators
     */
    public function testMarshallingStructWithMemberWithoutValue(Generator $generator)
    {
        AbstractValue::setGenerator($generator);
        $native = array('foo' => 0, 'bar' => 1);
        $xml = '<value><struct>'
             . '<member><name>foo</name><value><int>0</int></value></member>'
             . '<member><name>foo</name><bar/></member>'
             . '<member><name>bar</name><value><int>1</int></value></member>'
             . '</struct></value>';

        $val = AbstractValue::getXmlRpcValue($xml,
                                    AbstractValue::XML_STRING);

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
        AbstractValue::setGenerator($generator);
        $native = array('foo' => 0, 'bar' => 1);
        $xml = '<value><struct>'
             . '<member><name>foo</name><value><int>0</int></value></member>'
             . '<member><value><string>foo</string></value></member>'
             . '<member><name>bar</name><value><int>1</int></value></member>'
             . '</struct></value>';

        $val = AbstractValue::getXmlRpcValue($xml,
                                    AbstractValue::XML_STRING);

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
        AbstractValue::setGenerator($generator);
        $native = array('&nbsp;' => 0);
        $xml = '<value><struct><member><name>&amp;nbsp;</name><value><int>0</int>'
             . '</value></member></struct></value>';
        $val = AbstractValue::getXmlRpcValue($xml, AbstractValue::XML_STRING);
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
        AbstractValue::setGenerator($generator);
        $native = array('foo' => '');
        $xml = '<value><struct><member><name>foo</name>'
             . '<value><string/></value></member></struct></value>';

        $val = AbstractValue::getXmlRpcValue($xml,
                                    AbstractValue::XML_STRING);

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
        AbstractValue::setGenerator($generator);
        $native = array('foo' => 'ß');
        $xmlDecl = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml = '<value><struct><member><name>foo</name><value><string>ß</string></value></member></struct></value>';

        $val = AbstractValue::getXmlRpcValue($xmlDecl . $xml,
                                    AbstractValue::XML_STRING);

        $this->assertXmlRpcType('struct', $val);
        $this->assertEquals('struct', $val->getType());
        $this->assertSame($native, $val->getValue());
        $this->assertEquals($this->wrapXml($xml), $val->saveXml());

        $val = AbstractValue::getXmlRpcValue($native, AbstractValue::XMLRPC_TYPE_STRUCT);
        $this->assertSame($native, $val->getValue());
        $this->assertSame(trim($xml), trim($val->saveXml()));
    }

    // DateTime

    public function testMarshalDateTimeFromNativeString()
    {
        $native = '1997-07-16T19:20+01:00';
        $val = AbstractValue::getXmlRpcValue($native,
                                    AbstractValue::XMLRPC_TYPE_DATETIME);

        $this->assertXmlRpcType('dateTime', $val);

        $expected = new DateTime($native);
        $this->assertSame($expected->format($this->xmlRpcDateFormat), $val->getValue());
    }

    public function testMarshalDateTimeFromNativeStringProducesIsoOutput()
    {
        $native = '1997-07-16T19:20+01:00';
        $val = AbstractValue::getXmlRpcValue($native,
                                    AbstractValue::XMLRPC_TYPE_DATETIME);

        $this->assertXmlRpcType('dateTime', $val);

        $expected = new DateTime($native);
        $received = $val->getValue();
        $this->assertEquals($expected->format($this->xmlRpcDateFormat), $received);
    }

    public function testMarshalDateTimeFromInvalidString()
    {
        $this->setExpectedException('Zend\XmlRpc\Exception\ValueException',
                                    "The timezone could not be found in the database");
        AbstractValue::getXmlRpcValue('foobarbaz', AbstractValue::XMLRPC_TYPE_DATETIME);
    }

    public function testMarshalDateTimeFromNativeInteger()
    {
        $native = strtotime('1997-07-16T19:20+01:00');
        $val = AbstractValue::getXmlRpcValue($native,
                                    AbstractValue::XMLRPC_TYPE_DATETIME);

        $this->assertXmlRpcType('dateTime', $val);
        $this->assertSame($native, strtotime($val->getValue()));
    }

    /**
     * @group ZF-11588
     */
    public function testMarshalDateTimeBeyondUnixEpochFromNativeStringPassedToConstructor()
    {
        $native = '2040-01-01T00:00:00';
        $value  = new Value\DateTime($native);
        $expected = new DateTime($native);
        $this->assertSame($expected->format($this->xmlRpcDateFormat), $value->getValue());
    }

    /**
     * @dataProvider ZendTest\XmlRpc\TestProvider::provideGenerators
     */
    public function testMarshalDateTimeFromXmlRpc(Generator $generator)
    {
        AbstractValue::setGenerator($generator);
        $iso8601 = '1997-07-16T19:20+01:00';
        $xml = "<value><dateTime.iso8601>$iso8601</dateTime.iso8601></value>";

        $val = AbstractValue::getXmlRpcValue($xml,
                                    AbstractValue::XML_STRING);

        $this->assertXmlRpcType('dateTime', $val);
        $this->assertEquals('dateTime.iso8601', $val->getType());
        $expected = new DateTime($iso8601);
        $this->assertSame($expected->format($this->xmlRpcDateFormat), $val->getValue());
        $this->assertEquals($this->wrapXml($xml), $val->saveXml());
    }

    /**
     * @dataProvider ZendTest\XmlRpc\TestProvider::provideGenerators
     * @group ZF-4249
     */
    public function testMarshalDateTimeFromFromDateTime(Generator $generator)
    {
        AbstractValue::setGenerator($generator);
        $dateString = '20390418T13:14:15';
        $date = new DateTime($dateString);
        $dateString = '20390418T13:14:15';
        $xml = "<value><dateTime.iso8601>$dateString</dateTime.iso8601></value>";

        $val = AbstractValue::getXmlRpcValue($date, AbstractValue::XMLRPC_TYPE_DATETIME);
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
        AbstractValue::setGenerator($generator);
        $dateString = '20390418T13:14:15';
        $date = new \DateTime($dateString);
        $xml = "<value><dateTime.iso8601>$dateString</dateTime.iso8601></value>";

        $val = AbstractValue::getXmlRpcValue($date, AbstractValue::AUTO_DETECT_TYPE);
        $this->assertXmlRpcType('dateTime', $val);
        $this->assertEquals('dateTime.iso8601', $val->getType());
        $this->assertSame($dateString, $val->getValue());
        $this->assertEquals(trim($xml), trim($val->saveXml()));
    }

    /**
     * @group ZF-10776
     */
    public function testGetValueDatetime()
    {
        $expectedValue = '20100101T00:00:00';
        $phpDatetime     = new DateTime('20100101T00:00:00');
        $phpDateNative   = '20100101T00:00:00';

        $xmlRpcValueDateTime = new Value\DateTime($phpDatetime);
        $this->assertEquals($expectedValue, $xmlRpcValueDateTime->getValue());

        $xmlRpcValueDateTime = new Value\DateTime($phpDateNative);
        $this->assertEquals($expectedValue, $xmlRpcValueDateTime->getValue());
    }

    // Base64

    public function testMarshalBase64FromString()
    {
        $native = 'foo';
        $val = AbstractValue::getXmlRpcValue($native,
                                    AbstractValue::XMLRPC_TYPE_BASE64);

        $this->assertXmlRpcType('base64', $val);
        $this->assertSame($native, $val->getValue());
    }

    /**
     * @dataProvider ZendTest\XmlRpc\TestProvider::provideGenerators
     */
    public function testMarshalBase64FromXmlRpc(Generator $generator)
    {
        AbstractValue::setGenerator($generator);
        $native = 'foo';
        $xml = '<value><base64>' .base64_encode($native). '</base64></value>';

        $val = AbstractValue::getXmlRpcValue($xml,
                                    AbstractValue::XML_STRING);

        $this->assertXmlRpcType('base64', $val);
        $this->assertEquals('base64', $val->getType());
        $this->assertSame($native, $val->getValue());
        $this->assertEquals($this->wrapXml($xml), $val->saveXml());
    }

    public function testXmlRpcValueBase64GeneratedXmlContainsBase64EncodedText()
    {
        $native = 'foo';
        $val = AbstractValue::getXmlRpcValue($native,
                                    AbstractValue::XMLRPC_TYPE_BASE64);

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
        $val = AbstractValue::getXmlRpcValue($serialized,
                                    AbstractValue::XMLRPC_TYPE_BASE64);

        $this->assertXmlRpcType('base64', $val);
        $o2 = unserialize($val->getValue());
        $this->assertSame('foobar', $o2->getProperty());
    }

    public function testChangingExceptionResetsGeneratorObject()
    {
        $generator = AbstractValue::getGenerator();
        AbstractValue::setEncoding('UTF-8');
        $this->assertNotSame($generator, AbstractValue::getGenerator());
        $this->assertEquals($generator, AbstractValue::getGenerator());

        $generator = AbstractValue::getGenerator();
        AbstractValue::setEncoding('ISO-8859-1');
        $this->assertNotSame($generator, AbstractValue::getGenerator());
        $this->assertNotEquals($generator, AbstractValue::getGenerator());
    }

    // Exceptions

    public function testFactoryThrowsWhenInvalidTypeSpecified()
    {
        $this->setExpectedException('Zend\XmlRpc\Exception\ValueException', 'Given type is not a Zend\XmlRpc\AbstractValue constant');
        AbstractValue::getXmlRpcValue('', 'bad type here');
    }

    public function testPassingXmlRpcObjectReturnsTheSameObject()
    {
        $xmlRpcValue = new Value\String('foo');
        $this->assertSame($xmlRpcValue, AbstractValue::getXmlRpcValue($xmlRpcValue));
    }


    public function testGetXmlRpcTypeByValue()
    {
        $this->assertSame(
            AbstractValue::XMLRPC_TYPE_NIL,
            AbstractValue::getXmlRpcTypeByValue(new Value\Nil)
        );

        $this->assertEquals(
            AbstractValue::XMLRPC_TYPE_DATETIME,
            AbstractValue::getXmlRpcTypeByValue(new DateTime)
        );

        $this->assertEquals(
            AbstractValue::XMLRPC_TYPE_STRUCT,
            AbstractValue::getXmlRpcTypeByValue(array('foo' => 'bar'))
        );

        $object = new stdClass;
        $object->foo = 'bar';

        $this->assertEquals(
            AbstractValue::XMLRPC_TYPE_STRUCT,
            AbstractValue::getXmlRpcTypeByValue($object)
        );

        $this->assertEquals(
            AbstractValue::XMLRPC_TYPE_ARRAY,
            AbstractValue::getXmlRpcTypeByValue(new stdClass)
        );

        $this->assertEquals(
            AbstractValue::XMLRPC_TYPE_ARRAY,
            AbstractValue::getXmlRpcTypeByValue(array(1, 3, 3, 7))
        );

        $this->assertEquals(
            AbstractValue::XMLRPC_TYPE_INTEGER,
            AbstractValue::getXmlRpcTypeByValue(42)
        );

        $this->assertEquals(
            AbstractValue::XMLRPC_TYPE_DOUBLE,
            AbstractValue::getXmlRpcTypeByValue(13.37)
        );

        $this->assertEquals(
            AbstractValue::XMLRPC_TYPE_BOOLEAN,
            AbstractValue::getXmlRpcTypeByValue(true)
        );

        $this->assertEquals(
            AbstractValue::XMLRPC_TYPE_BOOLEAN,
            AbstractValue::getXmlRpcTypeByValue(false)
        );

        $this->assertEquals(
            AbstractValue::XMLRPC_TYPE_NIL,
            AbstractValue::getXmlRpcTypeByValue(null)
        );

        $this->assertEquals(
            AbstractValue::XMLRPC_TYPE_STRING,
            AbstractValue::getXmlRpcTypeByValue('Zend Framework')
        );
    }

    public function testGetXmlRpcTypeByValueThrowsExceptionOnInvalidValue()
    {
        $this->setExpectedException('Zend\XmlRpc\Exception\InvalidArgumentException');
        AbstractValue::getXmlRpcTypeByValue(fopen(__FILE__, 'r'));
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
