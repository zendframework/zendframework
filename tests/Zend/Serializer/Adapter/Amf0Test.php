<?php
/**
 * @package    Zend_Cache
 * @subpackage UnitTests
 */

/** @see Zend_Serializer_Adapter_Amf0 */
require_once 'Zend/Serializer/Adapter/Amf0.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @package    Zend_Serializer
 * @subpackage UnitTests
 */
class Zend_Serializer_Adapter_Amf0Test extends PHPUnit_Framework_TestCase
{

    private $_adapter;

    public function setUp()
    {
        $this->_adapter = new Zend_Serializer_Adapter_Amf0();
    }

    public function tearDown()
    {
        $this->_adapter = null;
    }

    /**
     * Simple test to serialize a value using Zend_Amf_Parser_Amf0_Serializer
     * -> This only tests the usage of Zend_Amf @see Zend_Amf_AllTests
     */
    public function testSerialize() {
        $value    = true;
        $expected = "\x01\x01"; // Amf0 -> true

        $data = $this->_adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    /**
     * Simple test to serialize a value using Zend_Amf_Parser_Amf0_Deserializer
     * -> This only tests the usage of Zend_Amf @see Zend_Amf_AllTests
     */
    public function testUnserialize() {
        $expected   = true;
        $value      = "\x01\x01"; // Amf0 -> true

        $data = $this->_adapter->unserialize($value);
        $this->assertEquals($expected, $data);
    }

}
