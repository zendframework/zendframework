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

// Call Zend_XmlRpc_ValueTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    require_once dirname(__FILE__) . '/../../TestHelper.php';
    define("PHPUnit_MAIN_METHOD", "Zend_XmlRpc_ValueTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'Zend/XmlRpc/Value.php';
require_once 'Zend/XmlRpc/Value/Scalar.php';
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
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("Zend_XmlRpc_ValueTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

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

    public function testMarshalBooleanFromXmlRpc()
    {
        $xml = '<value><boolean>1</boolean></value>';
        $val = Zend_XmlRpc_Value::getXmlRpcValue($xml, 
                                    Zend_XmlRpc_Value::XML_STRING);

        $this->assertXmlRpcType('boolean', $val);
        $this->assertEquals('boolean', $val->getType());
        $this->assertSame(true, $val->getValue());
        $this->assertType('DomElement', $val->getAsDOM());
        $this->assertEquals($this->wrapXml($xml), $val->saveXML());
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

    public function testMarshalIntegerFromXmlRpc()
    {
        $native = 1;
        $xmls = array("<value><int>$native</int></value>",
                      "<value><i4>$native</i4></value>");

        foreach ($xmls as $xml) {
            $val = Zend_XmlRpc_Value::getXmlRpcValue($xml, 
                                        Zend_XmlRpc_Value::XML_STRING);

            $this->assertXmlRpcType('integer', $val);
            $this->assertEquals('int', $val->getType());
            $this->assertSame($native, $val->getValue());
            $this->assertType('DomElement', $val->getAsDOM());
            $this->assertEquals($this->wrapXml($xml), $val->saveXML());
        }
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
    
    public function testMarshalDoubleFromXmlRpc()
    {
        $native = 1.1;
        $xml = "<value><double>$native</double></value>";
        $val = Zend_XmlRpc_Value::getXmlRpcValue($xml, 
                                    Zend_XmlRpc_Value::XML_STRING);

        $this->assertXmlRpcType('double', $val);
        $this->assertEquals('double', $val->getType());
        $this->assertSame($native, $val->getValue());
        $this->assertType('DomElement', $val->getAsDOM());
        $this->assertEquals($this->wrapXml($xml), $val->saveXML());        
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

    public function testMarshalStringFromXmlRpc()
    {
        $native = 'foo';
        $xml = "<value><string>$native</string></value>";
        $val = Zend_XmlRpc_Value::getXmlRpcValue($xml, 
                                    Zend_XmlRpc_Value::XML_STRING);

        $this->assertXmlRpcType('string', $val);
        $this->assertEquals('string', $val->getType());
        $this->assertSame($native, $val->getValue());
        $this->assertType('DomElement', $val->getAsDOM());
        $this->assertEquals($this->wrapXml($xml), $val->saveXML());        
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
        $val = Zend_XmlRpc_Value::getXmlRpcValue($native, 
                                    Zend_XmlRpc_Value::XMLRPC_TYPE_NIL);

        $this->assertXmlRpcType('nil', $val);
        $this->assertSame($native, $val->getValue());
    }

    public function testMarshalNilFromXmlRpc()
    {
        $xml = '<value><nil/></value>';
        $val = Zend_XmlRpc_Value::getXmlRpcValue($xml, 
                                    Zend_XmlRpc_Value::XML_STRING);

        $this->assertXmlRpcType('nil', $val);
        $this->assertEquals('nil', $val->getType());
        $this->assertSame(NULL, $val->getValue());
        $this->assertType('DomElement', $val->getAsDOM());
        $this->assertEquals($this->wrapXml($xml), $val->saveXML());
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
    
    public function testMarshalArrayFromXmlRpc()
    {
        $native = array(0,1);
        $xml = '<value><array><data><value><int>0</int></value>'
             . '<value><int>1</int></value></data></array></value>';

        $val = Zend_XmlRpc_Value::getXmlRpcValue($xml, 
                                    Zend_XmlRpc_Value::XML_STRING);

        $this->assertXmlRpcType('array', $val);
        $this->assertEquals('array', $val->getType());
        $this->assertSame($native, $val->getValue());
        $this->assertType('DomElement', $val->getAsDOM());
        $this->assertEquals($this->wrapXml($xml), $val->saveXML());   
    }

    public function testEmptyXmlRpcArrayResultsInEmptyArray()
    {
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

    public function testMarshalStructFromXmlRpc()
    {
        $native = array('foo' => 0);
        $xml = '<value><struct><member><name>foo</name><value><int>0</int>'
             . '</value></member></struct></value>';

        $val = Zend_XmlRpc_Value::getXmlRpcValue($xml, 
                                    Zend_XmlRpc_Value::XML_STRING);

        $this->assertXmlRpcType('struct', $val);
        $this->assertEquals('struct', $val->getType());
        $this->assertSame($native, $val->getValue());
        $this->assertType('DomElement', $val->getAsDOM());
        $this->assertEquals($this->wrapXml($xml), $val->saveXML());         
    }

    /**
     * @group ZF-7639
     */
    public function testMarshalStructFromXmlRpcWithEntities()
    {
        $native = array('&nbsp;' => 0);
        $xml = '<value><struct><member><name>&amp;nbsp;</name><value><int>0</int>'
             . '</value></member></struct></value>';
        $val = Zend_XmlRpc_Value::getXmlRpcValue($xml, Zend_XmlRpc_Value::XML_STRING);
        $this->assertXmlRpcType('struct', $val);
        $this->assertSame($native, $val->getValue());
        $this->assertSame($this->wrapXml($xml), $val->saveXML());
    }

    /**
     * @group ZF-3947
     */
    public function testMarshallingStructsWithEmptyValueFromXmlRpcShouldRetainKeys()
    {
        $native = array('foo' => '');
        $xml = '<value><struct><member><name>foo</name>'
             . '<value/></member></struct></value>';

        $val = Zend_XmlRpc_Value::getXmlRpcValue($xml, 
                                    Zend_XmlRpc_Value::XML_STRING);

        $this->assertXmlRpcType('struct', $val);
        $this->assertEquals('struct', $val->getType());
        $this->assertSame($native, $val->getValue());
        $this->assertType('DomElement', $val->getAsDOM());
        $this->assertEquals($this->wrapXml($xml), $val->saveXML());         
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

    public function testMarshalDateTimeFromNativeInteger()
    {   
        $native = strtotime('1997-07-16T19:20+01:00');
        $val = Zend_XmlRpc_Value::getXmlRpcValue($native,
                                    Zend_XmlRpc_Value::XMLRPC_TYPE_DATETIME);
                    
        $this->assertXmlRpcType('dateTime', $val);
        $this->assertSame($native, strtotime($val->getValue())); 
    }

    public function testMarshalDateTimeFromXmlRpc()
    {
        $iso8601 = '1997-07-16T19:20+01:00';
        $xml = "<value><dateTime.iso8601>$iso8601</dateTime.iso8601></value>";
        
        $val = Zend_XmlRpc_Value::getXmlRpcValue($xml, 
                                    Zend_XmlRpc_Value::XML_STRING);

        $this->assertXmlRpcType('dateTime', $val);
        $this->assertEquals('dateTime.iso8601', $val->getType());
        $this->assertSame(strtotime($iso8601), strtotime($val->getValue()));
        $this->assertType('DomElement', $val->getAsDOM());
        $this->assertEquals($this->wrapXml($xml), $val->saveXML());                    
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
    
    public function testMarshalBase64FromXmlRpc()
    {
        $native = 'foo';
        $xml = '<value><base64>' .base64_encode($native). '</base64></value>';
        
        $val = Zend_XmlRpc_Value::getXmlRpcValue($xml, 
                                    Zend_XmlRpc_Value::XML_STRING);

        $this->assertXmlRpcType('base64', $val);
        $this->assertEquals('base64', $val->getType());
        $this->assertSame($native, $val->getValue());
        $this->assertType('DomElement', $val->getAsDOM());
        $this->assertEquals($this->wrapXml($xml), $val->saveXML());                    
    }    

    public function testXmlRpcValueBase64GeneratedXmlContainsBase64EncodedText()
    {
        $native = 'foo';
        $val = Zend_XmlRpc_Value::getXmlRpcValue($native,
                                    Zend_XmlRpc_Value::XMLRPC_TYPE_BASE64);
                    
        $this->assertXmlRpcType('base64', $val);
        $xml = $val->saveXML();
        $encoded = base64_encode($native);
        $this->assertContains($encoded, $xml);
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

    // Custom Assertions and Helper Methods

    public function assertXmlRpcType($type, $object)
    {
        $type = 'Zend_XmlRpc_Value_' . ucfirst($type);
        $this->assertType($type, $object);
    }
    
    public function wrapXml($xml)
    {
        return "<?xml version=\"1.0\"?>\n$xml\n";
    }
}

// Call Zend_XmlRpc_ValueTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_XmlRpc_ValueTest::main") {
    Zend_XmlRpc_ValueTest::main();
}
