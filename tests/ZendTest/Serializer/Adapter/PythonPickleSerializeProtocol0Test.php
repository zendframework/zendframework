<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
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
class PythonPickleSerializeProtocol0Test extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Serializer\Adapter\PythonPickle
     */
    private $adapter;

    public function setUp()
    {
        $options = new Serializer\Adapter\PythonPickleOptions(array(
            'protocol' => 0
        ));
        $this->adapter = new Serializer\Adapter\PythonPickle($options);
    }

    public function tearDown()
    {
        $this->adapter = null;
    }

    public function testSerializeNull()
    {
        $value      = null;
        $expected   = 'N.';

        $data = $this->adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testSerializeTrue()
    {
        $value      = true;
        $expected   = "I01\r\n.";

        $data = $this->adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testSerializeFalse()
    {
        $value      = false;
        $expected   = "I00\r\n.";

        $data = $this->adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testSerializeInt()
    {
        $value      = -12345;
        $expected   = "I-12345\r\n.";

        $data = $this->adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testSerializeFloat()
    {
        $value      = -12345.6789;
        $expected   = "F-12345.6789\r\n.";

        $data = $this->adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testSerializeString()
    {
        $value      = 'test';
        $expected   = "S'test'\r\np0\r\n.";

        $data = $this->adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testSerializeStringWithSpecialChars()
    {
        $value      = "\x00\x01\x02\x03\x04\x05\x06\x07\x08\x09\x0a\x0b\x0c\x0d\x0e\x0f"
                    . "\x10\x11\x12\x13\x14\x15\x16\x17\x18\x19\x1a\x1b\x1c\x1d\x1e\x1f"
                    . "\xff\\\"'";
        $expected   = "S'\\x00\\x01\\x02\\x03\\x04\\x05\\x06\\x07\\x08\\t\\n\\x0b\\x0c\\r\\x0e\\x0f"
                    . "\\x10\\x11\\x12\\x13\\x14\\x15\\x16\\x17\\x18\\x19\\x1a\\x1b\\x1c\\x1d\\x1e\\x1f"
                    . "\\xff\\\\\"\\''\r\n"
                    . "p0\r\n.";

        $data = $this->adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testSerializeArrayList()
    {
        $value      = array('1', '2', 'test');
        $expected   = "(lp0\r\n"
                    . "S'1'\r\n"
                    . "p1\r\n"
                    . "aS'2'\r\n"
                    . "p2\r\n"
                    . "aS'test'\r\n"
                    . "p3\r\n"
                    . "a.";

        $data = $this->adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testSerializeSplFixedArray()
    {
        $value = new \SplFixedArray(3);
        $value[0] = '1';
        $value[1] = '2';
        $value[2] = 'test';

        $expected   = "(lp0\r\n"
                    . "S'1'\r\n"
                    . "p1\r\n"
                    . "aS'2'\r\n"
                    . "p2\r\n"
                    . "aS'test'\r\n"
                    . "p3\r\n"
                    . "a.";

        $data = $this->adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testSerializeArrayDict()
    {
        $value    = array('1', '2', 'three' => 'test');
        $expected = "(dp0\r\n"
                  . "I0\r\n"
                  . "S'1'\r\n"
                  . "p1\r\n"
                  . "sI1\r\n"
                  . "S'2'\r\n"
                  . "p2\r\n"
                  . "sS'three'\r\n"
                  . "p3\r\n"
                  . "S'test'\r\n"
                  . "p4\r\n"
                  . "s.";

        $data = $this->adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testSerializeObject()
    {
        $value = new \stdClass();
        $value->test  = 'test';
        $value->test2 = 2;
        $expected = "(dp0\r\n"
                  . "S'test'\r\n"
                  . "p1\r\n"
                  . "g1\r\n"
                  . "sS'test2'\r\n"
                  . "p2\r\n"
                  . "I2\r\n"
                  . "s.";

        $data = $this->adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

}
