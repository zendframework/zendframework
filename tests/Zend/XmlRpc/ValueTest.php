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
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version $Id$
 */
require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'Zend/XmlRpc/Value.php';
require_once 'Zend/XmlRpc/Value/Scalar.php';
require_once 'Zend/XmlRpc/Value/BigInteger.php';
require_once 'Zend/XmlRpc/Value/Collection.php';
require_once 'Zend/XmlRpc/Value/Array.php';
require_once 'Zend/XmlRpc/Value/Base64.php';
require_once 'Zend/XmlRpc/Value/Boolean.php';
require_once 'Zend/XmlRpc/Value/DateTime.php';
require_once 'Zend/XmlRpc/Value/Double.php';
require_once 'Zend/XmlRpc/Value/Integer.php';
require_once 'Zend/XmlRpc/Value/String.php';
require_once 'Zend/XmlRpc/Value/Nil.php';
require_once 'Zend/XmlRpc/Value/Struct.php';
require_once 'Zend/Crypt/Math/BigInteger.php';
require_once 'Zend/XmlRpc/Generator/DOMDocument.php';
require_once 'Zend/XmlRpc/Generator/XMLWriter.php';
require_once 'Zend/XmlRpc/TestProvider.php';
require_once 'Zend/Date.php';

/**
 * Test case for Zend_XmlRpc_Value
 *
 * @category   Zend
 * @package    Zend_XmlRpc
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_XmlRpc
 */
class Zend_XmlRpc_ValueTest extends PHPUnit_Framework_TestCase
{
    // Boolean

    public function testFactoryAutodetectsBoolean()
    {
        foreach (array(true, false) as $native) {
            $val = Zend_XmlRpc_Value::getXmlRpcValue($native);
            $this->assertXmlRpcType('boolean', $val);
        }
    }

    public function testMarshalBooleanFromNative()
    {
        $native = true;
        $val = Zend_XmlRpc_Value::getXmlRpcValue($native,
                                    Zend_XmlRpc_Value::XMLRPC_TYPE_BOOLEAN);

        $this->assertXmlRpcType('boolean', $val);
        $this->assertSame($native, $val->getValue());
    }

    /**
     * @dataProvider Zend_XmlRpc_TestProvider::provideGenerators
     */
    public function testMarshalBooleanFromXmlRpc(Zend_XmlRpc_Generator_Abstract $generator)
    {
        Zend_XmlRpc_Value::setGenerator($generator);
        $xml = '<value><boolean>1</boolean></value>';
        $val = Zend_XmlRpc_Value::getXmlRpcValue($xml,
                                    Zend_XmlRpc_Value::XML_STRING);

        $this->assertXmlRpcType('boolean', $val);
        $this->assertEquals('boolean', $val->getType());
        $this->assertSame(true, $val->getValue());
        $this->assertEquals($this->wrapXml($xml), $val->saveXml());
    }

    // Integer

    public function testFactoryAutodetectsInteger()
    {
        $val = Zend_XmlRpc_Value::getXmlRpcValue(1);
        $this->assertXmlRpcType('integer', $val);
    }

    public function testMarshalIntegerFromNative()
    {
        $native = 1;
        $types = array(Zend_XmlRpc_Value::XMLRPC_TYPE_I4,
                       Zend_XmlRpc_Value::XMLRPC_TYPE_INTEGER);

        foreach ($types as $type) {
            $val = Zend_XmlRpc_Value::getXmlRpcValue($native, $type);
            $this->assertXmlRpcType('integer', $val);
            $this->assertSame($native, $val->getValue());
        }
    }

    /**
     * @dataProvider Zend_XmlRpc_TestProvider::provideGenerators
     */
    public function testMarshalIntegerFromXmlRpc(Zend_XmlRpc_Generator_Abstract $generator)
    {
        Zend_XmlRpc_Value::setGenerator($generator);

        $native = 1;
        $xmls = array("<value><int>$native</int></value>",
                      "<value><i4>$native</i4></value>");

        foreach ($xmls as $xml) {
            $val = Zend_XmlRpc_Value::getXmlRpcValue($xml,
                                        Zend_XmlRpc_Value::XML_STRING);
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
        $this->setExpectedException('Zend_XmlRpc_Value_Exception', 'Overlong integer given');
        Zend_XmlRpc_Value::getXmlRpcValue(PHP_INT_MAX + 1, Zend_XmlRpc_Value::XMLRPC_TYPE_I4);
    }

    /**
     * @group ZF-3310
     */
    public function testMarshalIntegerFromOverlongNativeThrowsException()
    {
        $this->setExpectedException('Zend_XmlRpc_Value_Exception', 'Overlong integer given');
        Zend_XmlRpc_Value::getXmlRpcValue(PHP_INT_MAX + 1, Zend_XmlRpc_Value::XMLRPC_TYPE_INTEGER);
    }

    // BigInteger

    /**
     * @group ZF-6445
     * @dataProvider Zend_XmlRpc_TestProvider::provideGenerators
     */
    public function testMarshalBigIntegerFromFromXmlRpc(Zend_XmlRpc_Generator_Abstract $generator)
    {
        Zend_XmlRpc_Value::setGenerator($generator);
        $bigInt = (string)(PHP_INT_MAX + 1);
        $native = new Zend_Crypt_Math_BigInteger();
        $native->init($bigInt);

        $xmlStrings = array("<value><i8>$bigInt</i8></value>",
                            "<value><ex:i8 xmlns:ex=\"http://ws.apache.org/xmlrpc/namespaces/extensions\">$bigInt</ex:i8></value>");

        foreach ($xmlStrings as $xml) {
            $value = Zend_XmlRpc_Value::getXmlRpcValue($xml, Zend_XmlRpc_Value::XML_STRING);
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
        $types = array(Zend_XmlRpc_Value::XMLRPC_TYPE_APACHEI8,
                       Zend_XmlRpc_Value::XMLRPC_TYPE_I8);

        $bigInt = new Zend_Crypt_Math_BigInteger();
        $bigInt->init($native);

        foreach ($types as $type) {
            $value = Zend_XmlRpc_Value::getXmlRpcValue($native, $type);
            $this->assertSame('i8', $value->getType());
            $this->assertEquals($bigInt, $value->getValue());
        }

        $value = Zend_XmlRpc_Value::getXmlRpcValue($bigInt);
        $this->assertSame('i8', $value->getType());
        $this->assertEquals($bigInt, $value->getValue());
    }

    // Double

    public function testFactoryAutodetectsFloat()
    {
        $val = Zend_XmlRpc_Value::getXmlRpcValue((float)1);
        $this->assertXmlRpcType('double', $val);
    }

    public function testMarshalDoubleFromNative()
    {
        $native = 1.1;
        $val = Zend_XmlRpc_Value::getXmlRpcValue($native,
                                    Zend_XmlRpc_Value::XMLRPC_TYPE_DOUBLE);

        $this->assertXmlRpcType('double', $val);
        $this->assertSame($native, $val->getValue());
    }

    /**
     * @dataProvider Zend_XmlRpc_TestProvider::provideGenerators
     */
    public function testMarshalDoubleFromXmlRpc(Zend_XmlRpc_Generator_Abstract $generator)
    {
        Zend_XmlRpc_Value::setGenerator($generator);
        $native = 1.1;
        $xml = "<value><double>$native</double></value>";
        $val = Zend_XmlRpc_Value::getXmlRpcValue($xml,
                                    Zend_XmlRpc_Value::XML_STRING);

        $this->assertXmlRpcType('double', $val);
        $this->assertEquals('double', $val->getType());
        $this->assertSame($native, $val->getValue());
        $this->assertEquals($this->wrapXml($xml), $val->saveXml());
    }

    /**
     * @group ZF-7712
     * @dataProvider Zend_XmlRpc_TestProvider::provideGenerators
     */
    public function testMarshallingDoubleWithHigherPrecisionFromNative(Zend_XmlRpc_Generator_Abstract $generator)
    {
        Zend_XmlRpc_Value::setGenerator($generator);
        if (ini_get('precision') < 7) {
            $this->markTestSkipped('precision is too low');
        }

        $native = 0.1234567;
        $value = Zend_XmlRpc_Value::getXmlRpcValue($native, Zend_XmlRpc_Value::XMLRPC_TYPE_DOUBLE);
        $this->assertXmlRpcType('double', $value);
        $this->assertSame($native, $value->getValue());
        $this->assertSame('<value><double>0.1234567</double></value>', trim($value->saveXml()));
    }

    /**
     * @group ZF-7712
     * @dataProvider Zend_XmlRpc_TestProvider::provideGenerators
     */
    public function testMarshallingDoubleWithHigherPrecisionFromNativeWithTrailingZeros(Zend_XmlRpc_Generator_Abstract $generator)
    {
        Zend_XmlRpc_Value::setGenerator($generator);
        if (ini_get('precision') < 7) {
            $this->markTestSkipped('precision is too low');
        }
        $native = 0.1;
        $value = Zend_XmlRpc_Value::getXmlRpcValue($native, Zend_XmlRpc_Value::XMLRPC_TYPE_DOUBLE);
        $this->assertXmlRpcType('double', $value);
        $this->assertSame($native, $value->getValue());
        $this->assertSame('<value><double>0.1</double></value>', trim($value->saveXml()));
    }

    // String

    public function testFactoryAutodetectsString()
    {
        $val = Zend_XmlRpc_Value::getXmlRpcValue('');
        $this->assertXmlRpcType('string', $val);
    }


    public function testMarshalStringFromNative()
    {
        $native = 'foo';
        $val = Zend_XmlRpc_Value::getXmlRpcValue($native,
                                    Zend_XmlRpc_Value::XMLRPC_TYPE_STRING);

        $this->assertXmlRpcType('string', $val);
        $this->assertSame($native, $val->getValue());
    }

    /**
     * @dataProvider Zend_XmlRpc_TestProvider::provideGenerators
     */
    public function testMarshalStringFromXmlRpc(Zend_XmlRpc_Generator_Abstract $generator)
    {
        Zend_XmlRpc_Value::setGenerator($generator);
        $native = 'foo';
        $xml = "<value><string>$native</string></value>";
        $val = Zend_XmlRpc_Value::getXmlRpcValue($xml,
                                    Zend_XmlRpc_Value::XML_STRING);

        $this->assertXmlRpcType('string', $val);
        $this->assertEquals('string', $val->getType());
        $this->assertSame($native, $val->getValue());
        $this->assertEquals($this->wrapXml($xml), $val->saveXml());
    }

    /**
     * @dataProvider Zend_XmlRpc_TestProvider::provideGenerators
     */
    public function testMarshalStringFromDefault(Zend_XmlRpc_Generator_Abstract $generator)
    {
        Zend_XmlRpc_Value::setGenerator($generator);
        $native = 'foo';
        $xml = "<string>$native</string>";
        $val = Zend_XmlRpc_Value::getXmlRpcValue($xml,
                                    Zend_XmlRpc_Value::XML_STRING);

        $this->assertXmlRpcType('string', $val);
        $this->assertEquals('string', $val->getType());
        $this->assertSame($native, $val->getValue());
        $this->assertEquals($this->wrapXml($xml), $val->saveXml());
    }

    //Nil

    public function testFactoryAutodetectsNil()
    {
        $val = Zend_XmlRpc_Value::getXmlRpcValue(NULL);
        $this->assertXmlRpcType('nil', $val);
    }

    public function testMarshalNilFromNative()
    {
        $native = NULL;
        $types = array(Zend_XmlRpc_Value::XMLRPC_TYPE_NIL,
                       Zend_XmlRpc_Value::XMLRPC_TYPE_APACHENIL);
        foreach ($types as $type) {
            $value = Zend_XmlRpc_Value::getXmlRpcValue($native, $type);

            $this->assertXmlRpcType('nil', $value);
            $this->assertSame($native, $value->getValue());
        }
    }

    /**
     * @dataProvider Zend_XmlRpc_TestProvider::provideGenerators
     */
    public function testMarshalNilFromXmlRpc(Zend_XmlRpc_Generator_Abstract $generator)
    {
        Zend_XmlRpc_Value::setGenerator($generator);
        $xmls = array('<value><nil/></value>',
                     '<value><ex:nil xmlns:ex="http://ws.apache.org/xmlrpc/namespaces/extensions"/></value>');

        foreach ($xmls as $xml) {
            $val = Zend_XmlRpc_Value::getXmlRpcValue($xml,
                                        Zend_XmlRpc_Value::XML_STRING);
            $this->assertXmlRpcType('nil', $val);
            $this->assertEquals('nil', $val->getType());
            $this->assertSame(NULL, $val->getValue());
            $this->assertEquals($this->wrapXml($xml), $val->saveXml());
        }
    }

    // Array

    public function testFactoryAutodetectsArray()
    {
        $val = Zend_XmlRpc_Value::getXmlRpcValue(array(0, 'foo'));
        $this->assertXmlRpcType('array', $val);
    }

    public function testMarshalArrayFromNative()
    {
        $native = array(0,1);
        $val = Zend_XmlRpc_Value::getXmlRpcValue($native,
                                    Zend_XmlRpc_Value::XMLRPC_TYPE_ARRAY);

        $this->assertXmlRpcType('array', $val);
        $this->assertSame($native, $val->getValue());
    }

    /**
     * @dataProvider Zend_XmlRpc_TestProvider::provideGenerators
     */
    public function testMarshalArrayFromXmlRpc(Zend_XmlRpc_Generator_Abstract $generator)
    {
        Zend_XmlRpc_Value::setGenerator($generator);
        $native = array(0,1);
        $xml = '<value><array><data><value><int>0</int></value>'
             . '<value><int>1</int></value></data></array></value>';

        $val = Zend_XmlRpc_Value::getXmlRpcValue($xml,
                                    Zend_XmlRpc_Value::XML_STRING);

        $this->assertXmlRpcType('array', $val);
        $this->assertEquals('array', $val->getType());
        $this->assertSame($native, $val->getValue());
        $this->assertEquals($this->wrapXml($xml), $val->saveXml());
    }

    /**
     * @dataProvider Zend_XmlRpc_TestProvider::provideGenerators
     */
    public function testEmptyXmlRpcArrayResultsInEmptyArray(Zend_XmlRpc_Generator_Abstract $generator)
    {
        Zend_XmlRpc_Value::setGenerator($generator);
        $native = array();
        $xml    = '<value><array><data/></array></value>';

        $val = Zend_XmlRpc_Value::getXmlRpcValue($xml,
                                    Zend_XmlRpc_Value::XML_STRING);

        $this->assertXmlRpcType('array', $val);
        $this->assertEquals('array', $val->getType());
        $this->assertSame($native, $val->getValue());

        $value = Zend_XmlRpc_Value::getXmlRpcValue($xml, Zend_XmlRpc_Value::XML_STRING);
        $this->assertXmlRpcType('array', $value);
        $this->assertEquals('array', $value->getType());
        $this->assertSame($native, $value->getValue());
    }

    /**
     * @dataProvider Zend_XmlRpc_TestProvider::provideGenerators
     */
    public function testArrayMustContainDataElement(Zend_XmlRpc_Generator_Abstract $generator)
    {
        Zend_XmlRpc_Value::setGenerator($generator);
        $native = array();
        $xml    = '<value><array/></value>';

        $this->setExpectedException('Zend_XmlRpc_Value_Exception',
            'Invalid XML for XML-RPC native array type: ARRAY tag must contain DATA tag');
        $val = Zend_XmlRpc_Value::getXmlRpcValue($xml,
                                    Zend_XmlRpc_Value::XML_STRING);
    }

    /**
     * @group ZF-5405
     * @dataProvider Zend_XmlRpc_TestProvider::provideGenerators
     */
    public function testMarshalNilInStructWrappedInArray(Zend_XmlRpc_Generator_Abstract $generator)
    {
        Zend_XmlRpc_Value::setGenerator($generator);
        $expected = array(array('id' => '1', 'name' => 'vertebra, caudal', 'description' => null));
        $xml = '<value>'
             . '<array><data><value><struct><member><name>id</name><value><string>1</string></value></member>'
             . '<member><name>name</name><value><string>vertebra, caudal</string></value></member>'
             . '<member><name>description</name><value><nil/></value></member></struct></value></data></array>'
             . '</value>';
        $val = Zend_XmlRpc_Value::getXmlRpcValue($xml, Zend_XmlRpc_Value::XML_STRING);
        $this->assertSame($expected, $val->getValue());
    }

    // Struct

    public function testFactoryAutodetectsStruct()
    {
        $val = Zend_XmlRpc_Value::getXmlRpcValue(array('foo' => 0));
        $this->assertXmlRpcType('struct', $val);
    }

    public function testFactoryAutodetectsStructFromObject()
    {
        $val = Zend_XmlRpc_Value::getXmlRpcValue((object)array('foo' => 0));
        $this->assertXmlRpcType('struct', $val);
    }

    public function testMarshalStructFromNative()
    {
        $native = array('foo' => 0);
        $val = Zend_XmlRpc_Value::getXmlRpcValue($native,
                                    Zend_XmlRpc_Value::XMLRPC_TYPE_STRUCT);

        $this->assertXmlRpcType('struct', $val);
        $this->assertSame($native, $val->getValue());
    }

    /**
     * @dataProvider Zend_XmlRpc_TestProvider::provideGenerators
     */
    public function testMarshalStructFromXmlRpc(Zend_XmlRpc_Generator_Abstract $generator)
    {
        Zend_XmlRpc_Value::setGenerator($generator);
        $native = array('foo' => 0);
        $xml = '<value><struct><member><name>foo</name><value><int>0</int>'
             . '</value></member></struct></value>';

        $val = Zend_XmlRpc_Value::getXmlRpcValue($xml,
                                    Zend_XmlRpc_Value::XML_STRING);

        $this->assertXmlRpcType('struct', $val);
        $this->assertEquals('struct', $val->getType());
        $this->assertSame($native, $val->getValue());
        $this->assertEquals($this->wrapXml($xml), $val->saveXml());
    }

    /**
     * @dataProvider Zend_XmlRpc_TestProvider::provideGenerators
     */
    public function testMarshallingStructWithMemberWithoutValue(Zend_XmlRpc_Generator_Abstract $generator)
    {
        Zend_XmlRpc_Value::setGenerator($generator);
        $native = array('foo' => 0, 'bar' => 1);
        $xml = '<value><struct>'
             . '<member><name>foo</name><value><int>0</int></value></member>'
             . '<member><name>foo</name><bar/></member>'
             . '<member><name>bar</name><value><int>1</int></value></member>'
             . '</struct></value>';

        $val = Zend_XmlRpc_Value::getXmlRpcValue($xml,
                                    Zend_XmlRpc_Value::XML_STRING);

        $this->assertXmlRpcType('struct', $val);
        $this->assertEquals('struct', $val->getType());
        $this->assertSame($native, $val->getValue());
        $this->assertEquals($this->wrapXml($xml), $val->saveXml());
    }

    /**
     * @dataProvider Zend_XmlRpc_TestProvider::provideGenerators
     */
    public function testMarshallingStructWithMemberWithoutName(Zend_XmlRpc_Generator_Abstract $generator)
    {
        Zend_XmlRpc_Value::setGenerator($generator);
        $native = array('foo' => 0, 'bar' => 1);
        $xml = '<value><struct>'
             . '<member><name>foo</name><value><int>0</int></value></member>'
             . '<member><value><string>foo</string></value></member>'
             . '<member><name>bar</name><value><int>1</int></value></member>'
             . '</struct></value>';

        $val = Zend_XmlRpc_Value::getXmlRpcValue($xml,
                                    Zend_XmlRpc_Value::XML_STRING);

        $this->assertXmlRpcType('struct', $val);
        $this->assertEquals('struct', $val->getType());
        $this->assertSame($native, $val->getValue());
        $this->assertEquals($this->wrapXml($xml), $val->saveXml());
    }

    /**
     * @group ZF-7639
     * @dataProvider Zend_XmlRpc_TestProvider::provideGenerators
     */
    public function testMarshalStructFromXmlRpcWithEntities(Zend_XmlRpc_Generator_Abstract $generator)
    {
        Zend_XmlRpc_Value::setGenerator($generator);
        $native = array('&nbsp;' => 0);
        $xml = '<value><struct><member><name>&amp;nbsp;</name><value><int>0</int>'
             . '</value></member></struct></value>';
        $val = Zend_XmlRpc_Value::getXmlRpcValue($xml, Zend_XmlRpc_Value::XML_STRING);
        $this->assertXmlRpcType('struct', $val);
        $this->assertSame($native, $val->getValue());
        $this->assertSame($this->wrapXml($xml), $val->saveXml());
    }

    /**
     * @group ZF-3947
     * @dataProvider Zend_XmlRpc_TestProvider::provideGenerators
     */
    public function testMarshallingStructsWithEmptyValueFromXmlRpcShouldRetainKeys(Zend_XmlRpc_Generator_Abstract $generator)
    {
        Zend_XmlRpc_Value::setGenerator($generator);
        $native = array('foo' => '');
        $xml = '<value><struct><member><name>foo</name>'
             . '<value><string/></value></member></struct></value>';

        $val = Zend_XmlRpc_Value::getXmlRpcValue($xml,
                                    Zend_XmlRpc_Value::XML_STRING);

        $this->assertXmlRpcType('struct', $val);
        $this->assertEquals('struct', $val->getType());
        $this->assertSame($native, $val->getValue());
        $this->assertEquals($this->wrapXml($xml), $val->saveXml());
    }

    /**
     * @dataProvider Zend_XmlRpc_TestProvider::provideGenerators
     */
    public function testMarshallingStructWithMultibyteValueFromXmlRpcRetainsMultibyteValue(Zend_XmlRpc_Generator_Abstract $generator)
    {
        Zend_XmlRpc_Value::setGenerator($generator);
        $native = array('foo' => 'ß');
        $xmlDecl = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml = '<value><struct><member><name>foo</name><value><string>ß</string></value></member></struct></value>';

        $val = Zend_XmlRpc_Value::getXmlRpcValue($xmlDecl . $xml,
                                    Zend_XmlRpc_Value::XML_STRING);

        $this->assertXmlRpcType('struct', $val);
        $this->assertEquals('struct', $val->getType());
        $this->assertSame($native, $val->getValue());
        $this->assertEquals($this->wrapXml($xml), $val->saveXml());

        $val = Zend_XmlRpc_Value::getXmlRpcValue($native, Zend_XmlRpc_Value::XMLRPC_TYPE_STRUCT);
        $this->assertSame($native, $val->getValue());
        $this->assertSame(trim($xml), trim($val->saveXml()));
    }

    // DateTime

    public function testMarshalDateTimeFromNativeString()
    {
        $native = '1997-07-16T19:20+01:00';
        $val = Zend_XmlRpc_Value::getXmlRpcValue($native,
                                    Zend_XmlRpc_Value::XMLRPC_TYPE_DATETIME);

        $this->assertXmlRpcType('dateTime', $val);

        $expected = '1997-07-16T19:20+01:00';
        $this->assertSame(strtotime($native), strtotime($val->getValue()));
    }

    public function testMarshalDateTimeFromNativeStringProducesIsoOutput()
    {
        $native = '1997-07-16T19:20+01:00';
        $val = Zend_XmlRpc_Value::getXmlRpcValue($native,
                                    Zend_XmlRpc_Value::XMLRPC_TYPE_DATETIME);

        $this->assertXmlRpcType('dateTime', $val);

        $expected = date('c', strtotime($native));
        $expected = substr($expected, 0, strlen($expected) - 6);
        $expected = str_replace('-', '', $expected);
        $received = $val->getValue();
        $this->assertEquals($expected, $received);
    }

    public function testMarshalDateTimeFromInvalidString()
    {
        $this->setExpectedException('Zend_XmlRpc_Value_Exception',
            "Cannot convert given value 'foobarbaz' to a timestamp");
        Zend_XmlRpc_Value::getXmlRpcValue('foobarbaz', Zend_XmlRpc_Value::XMLRPC_TYPE_DATETIME);
    }

    public function testMarshalDateTimeFromNativeInteger()
    {
        $native = strtotime('1997-07-16T19:20+01:00');
        $val = Zend_XmlRpc_Value::getXmlRpcValue($native,
                                    Zend_XmlRpc_Value::XMLRPC_TYPE_DATETIME);

        $this->assertXmlRpcType('dateTime', $val);
        $this->assertSame($native, strtotime($val->getValue()));
    }

    /**
     * @dataProvider Zend_XmlRpc_TestProvider::provideGenerators
     */
    public function testMarshalDateTimeFromXmlRpc(Zend_XmlRpc_Generator_Abstract $generator)
    {
        Zend_XmlRpc_Value::setGenerator($generator);
        $iso8601 = '1997-07-16T19:20+01:00';
        $xml = "<value><dateTime.iso8601>$iso8601</dateTime.iso8601></value>";

        $val = Zend_XmlRpc_Value::getXmlRpcValue($xml,
                                    Zend_XmlRpc_Value::XML_STRING);

        $this->assertXmlRpcType('dateTime', $val);
        $this->assertEquals('dateTime.iso8601', $val->getType());
        $this->assertSame(strtotime($iso8601), strtotime($val->getValue()));
        $this->assertEquals($this->wrapXml($xml), $val->saveXml());
    }

    /**
     * @dataProvider Zend_XmlRpc_TestProvider::provideGenerators
     * @group ZF-4249
     */
    public function testMarshalDateTimeFromFromZendDate(Zend_XmlRpc_Generator_Abstract $generator)
    {
        Zend_XmlRpc_Value::setGenerator($generator);
        $date = new Zend_Date(array('year' => 2039, 'month' => 4, 'day' => 18,
                                    'hour' => 13, 'minute' => 14, 'second' => 15));
        $dateString = '20390418T13:14:15';
        $xml = "<value><dateTime.iso8601>$dateString</dateTime.iso8601></value>";

        $val = Zend_XmlRpc_Value::getXmlRpcValue($date, Zend_XmlRpc_Value::XMLRPC_TYPE_DATETIME);
        $this->assertXmlRpcType('dateTime', $val);
        $this->assertEquals('dateTime.iso8601', $val->getType());
        $this->assertSame($dateString, $val->getValue());
        $this->assertEquals(trim($xml), trim($val->saveXml()));

    }

    /**
     * @dataProvider Zend_XmlRpc_TestProvider::provideGenerators
     * @group ZF-4249
     */
    public function testMarshalDateTimeFromZendDateAndAutodetectingType(Zend_XmlRpc_Generator_Abstract $generator)
    {
        Zend_XmlRpc_Value::setGenerator($generator);
        $date = new Zend_Date(array('year' => 2039, 'month' => 4, 'day' => 18,
                                    'hour' => 13, 'minute' => 14, 'second' => 15));
        $dateString = '20390418T13:14:15';
        $xml = "<value><dateTime.iso8601>$dateString</dateTime.iso8601></value>";

        $val = Zend_XmlRpc_Value::getXmlRpcValue($date, Zend_XmlRpc_Value::AUTO_DETECT_TYPE);
        $this->assertXmlRpcType('dateTime', $val);
        $this->assertEquals('dateTime.iso8601', $val->getType());
        $this->assertSame($dateString, $val->getValue());
        $this->assertEquals(trim($xml), trim($val->saveXml()));
    }

    /**
     * @dataProvider Zend_XmlRpc_TestProvider::provideGenerators
     * @group ZF-4249
     */
    public function testMarshalDateTimeFromFromDateTime(Zend_XmlRpc_Generator_Abstract $generator)
    {
        Zend_XmlRpc_Value::setGenerator($generator);
        $dateString = '20390418T13:14:15';
        $date = new DateTime($dateString);
        $dateString = '20390418T13:14:15';
        $xml = "<value><dateTime.iso8601>$dateString</dateTime.iso8601></value>";

        $val = Zend_XmlRpc_Value::getXmlRpcValue($date, Zend_XmlRpc_Value::XMLRPC_TYPE_DATETIME);
        $this->assertXmlRpcType('dateTime', $val);
        $this->assertEquals('dateTime.iso8601', $val->getType());
        $this->assertSame($dateString, $val->getValue());
        $this->assertEquals(trim($xml), trim($val->saveXml()));

    }

    /**
     * @dataProvider Zend_XmlRpc_TestProvider::provideGenerators
     * @group ZF-4249
     */
    public function testMarshalDateTimeFromDateTimeAndAutodetectingType(Zend_XmlRpc_Generator_Abstract $generator)
    {
        Zend_XmlRpc_Value::setGenerator($generator);
        $dateString = '20390418T13:14:15';
        $date = new DateTime($dateString);
        $xml = "<value><dateTime.iso8601>$dateString</dateTime.iso8601></value>";

        $val = Zend_XmlRpc_Value::getXmlRpcValue($date, Zend_XmlRpc_Value::AUTO_DETECT_TYPE);
        $this->assertXmlRpcType('dateTime', $val);
        $this->assertEquals('dateTime.iso8601', $val->getType());
        $this->assertSame($dateString, $val->getValue());
        $this->assertEquals(trim($xml), trim($val->saveXml()));
    }

    // Base64

    public function testMarshalBase64FromString()
    {
        $native = 'foo';
        $val = Zend_XmlRpc_Value::getXmlRpcValue($native,
                                    Zend_XmlRpc_Value::XMLRPC_TYPE_BASE64);

        $this->assertXmlRpcType('base64', $val);
        $this->assertSame($native, $val->getValue());
    }

    /**
     * @dataProvider Zend_XmlRpc_TestProvider::provideGenerators
     */
    public function testMarshalBase64FromXmlRpc(Zend_XmlRpc_Generator_Abstract $generator)
    {
        Zend_XmlRpc_Value::setGenerator($generator);
        $native = 'foo';
        $xml = '<value><base64>' .base64_encode($native). '</base64></value>';

        $val = Zend_XmlRpc_Value::getXmlRpcValue($xml,
                                    Zend_XmlRpc_Value::XML_STRING);

        $this->assertXmlRpcType('base64', $val);
        $this->assertEquals('base64', $val->getType());
        $this->assertSame($native, $val->getValue());
        $this->assertEquals($this->wrapXml($xml), $val->saveXml());
    }

    public function testXmlRpcValueBase64GeneratedXmlContainsBase64EncodedText()
    {
        $native = 'foo';
        $val = Zend_XmlRpc_Value::getXmlRpcValue($native,
                                    Zend_XmlRpc_Value::XMLRPC_TYPE_BASE64);

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
        $o = new Zend_XmlRpc_SerializableTestClass();
        $o->setProperty('foobar');
        $serialized = serialize($o);
        $val = Zend_XmlRpc_Value::getXmlRpcValue($serialized,
                                    Zend_XmlRpc_Value::XMLRPC_TYPE_BASE64);

        $this->assertXmlRpcType('base64', $val);
        $o2 = unserialize($val->getValue());
        $this->assertSame('foobar', $o2->getProperty());
    }

    public function testChangingExceptionResetsGeneratorObject()
    {
        $generator = Zend_XmlRpc_Value::getGenerator();
        Zend_XmlRpc_Value::setEncoding('UTF-8');
        $this->assertNotSame($generator, Zend_XmlRpc_Value::getGenerator());
        $this->assertEquals($generator, Zend_XmlRpc_Value::getGenerator());

        $generator = Zend_XmlRpc_Value::getGenerator();
        Zend_XmlRpc_Value::setEncoding('ISO-8859-1');
        $this->assertNotSame($generator, Zend_XmlRpc_Value::getGenerator());
        $this->assertNotEquals($generator, Zend_XmlRpc_Value::getGenerator());
    }

    // Exceptions

    public function testFactoryThrowsWhenInvalidTypeSpecified()
    {
        try {
            Zend_XmlRpc_Value::getXmlRpcValue('', 'bad type here');
            $this->fail();
        } catch (Exception $e) {
            $this->assertRegexp('/given type is not/i', $e->getMessage());
        }
    }

    public function testPassingXmlRpcObjectReturnsTheSameObject()
    {
        $xmlRpcValue = new Zend_XmlRpc_Value_String('foo');
        $this->assertSame($xmlRpcValue, Zend_XmlRpc_Value::getXmlRpcValue($xmlRpcValue));
    }

    // Custom Assertions and Helper Methods

    public function assertXmlRpcType($type, $object)
    {
        $type = 'Zend_XmlRpc_Value_' . ucfirst($type);
        $this->assertType($type, $object);
    }

    public function wrapXml($xml)
    {
        return $xml . "\n";
    }
}

class Zend_XmlRpc_SerializableTestClass
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

// Call Zend_XmlRpc_ValueTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_XmlRpc_ValueTest::main") {
    Zend_XmlRpc_ValueTest::main();
}
