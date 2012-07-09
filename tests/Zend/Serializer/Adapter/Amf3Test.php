<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Serializer
 */

namespace ZendTest\Serializer\Adapter;

use Zend\Serializer;

/**
 * @category   Zend
 * @package    Zend_Serializer
 * @subpackage UnitTests
 * @group      Zend_Serializer
 */
class Amf3Test extends \PHPUnit_Framework_TestCase
{

    private $_adapter;

    public function setUp()
    {
        $this->_adapter = new Serializer\Adapter\Amf3();
    }

    public function tearDown()
    {
        $this->_adapter = null;
    }

    /**
     * Simple test to serialize a value using Zend_Amf_Parser_Amf3_Serializer
     * -> This only tests the usage of Zend_Amf @see Zend_Amf_AllTests
     */
    public function testSerialize()
    {
        $value    = true;
        $expected = "\x03"; // Amf3 -> true

        $data = $this->_adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    /**
     * Simple test to unserialize a value using Zend_Amf_Parser_Amf3_Deserializer
     * -> This only tests the usage of Zend_Amf @see Zend_Amf_AllTests
     */
    public function testUnserialize()
    {
        $expected   = true;
        $value      = "\x03"; // Amf3 -> true

        $data = $this->_adapter->unserialize($value);
        $this->assertEquals($expected, $data);
    }

}
