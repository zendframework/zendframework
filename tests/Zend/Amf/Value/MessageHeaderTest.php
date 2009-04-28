<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Amf_Value_MessageHeaderTest::main');
}

require_once dirname(__FILE__) . '/../../../TestHelper.php';
require_once 'Zend/Amf/Value/MessageHeader.php';

/**
 * Test case for Zend_Amf_Value_MessageHeader
 *
 * @package Zend_Amf
 * @subpackage UnitTests
 * @version $Id$
 */
class Zend_Amf_Value_MessageHeaderTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_Amf_Value_MessageHeaderTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function testConstructorShouldSetMessageHeaderName()
    {
        $messageHeader = new Zend_Amf_Value_MessageHeader('foo', true, 'content');
        $this->assertEquals('foo', $messageHeader->name);
    }

    public function testConstructorShouldSetMessageHeaderMustReadFlag()
    {
        $messageHeader = new Zend_Amf_Value_MessageHeader('foo', true, 'content');
        $this->assertTrue($messageHeader->mustRead);
        $messageHeader = new Zend_Amf_Value_MessageHeader('foo', false, 'content');
        $this->assertFalse($messageHeader->mustRead);
    }

    public function testConstructorShouldCastMessageHeaderMustReadFlagToBoolean()
    {
        $messageHeader = new Zend_Amf_Value_MessageHeader('foo', 'foo', 'content');
        $this->assertTrue($messageHeader->mustRead);
        $messageHeader = new Zend_Amf_Value_MessageHeader('foo', 0, 'content');
        $this->assertFalse($messageHeader->mustRead);
    }

    public function testConstructorShouldSetMessageHeaderDataUnmodified()
    {
        $data = new stdClass;
        $data->foo = 'bar';
        $data->bar = array('baz' => 'bat');
        $messageHeader = new Zend_Amf_Value_MessageHeader('foo', true, $data);
        $this->assertSame($data, $messageHeader->data);
    }

    public function testConstructorShouldNotSetLengthIfNotProvided()
    {
        $messageHeader = new Zend_Amf_Value_MessageHeader('foo', true, 'content');
        $this->assertNull($messageHeader->length);
    }

    public function testConstructorShouldCastLengthToInteger()
    {
        $messageHeader = new Zend_Amf_Value_MessageHeader('foo', 'foo', 'content', '2');
        $this->assertSame(2, $messageHeader->length);
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Amf_Value_MessageHeaderTest::main') {
    Zend_Amf_Value_MessageHeaderTest::main();
}
