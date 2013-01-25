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
class PythonPickleSerializeProtocol1Test extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Serializer\Adapter\PythonPickle
     */
    private $adapter;

    public function setUp()
    {
        $options = new Serializer\Adapter\PythonPickleOptions(array(
            'protocol' => 1
        ));
        $this->adapter = new Serializer\Adapter\PythonPickle($options);
    }

    public function tearDown()
    {
        $this->adapter = null;
    }

    public function testSerializeNull()
   {
        $value    = null;
        $expected = 'N.';

        $data = $this->adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testSerializeTrue()
    {
        $value    = true;
        $expected = "I01\r\n.";

        $data = $this->adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testSerializeFalse()
    {
        $value    = false;
        $expected = "I00\r\n.";

        $data = $this->adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testSerializeBinInt1()
    {
        $value    = 255;
        $expected = "K\xff.";

        $data = $this->adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testSerializeBinInt2()
    {
        $value    = 256;
        $expected = "M\x00\x01.";

        $data = $this->adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testSerializeBinInt()
    {
        $value    = -2;
        $expected = "J\xfe\xff\xff\xff.";

        $data = $this->adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testSerializeBinFloat()
    {
        $value    = -12345.6789;
        $expected = "G\xc0\xc8\x1c\xd6\xe6\x31\xf8\xa1.";

        $data = $this->adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testSerializeShortBinString()
    {
        $value    = 'test';
        $expected = "U\x04test"
                  . "q\x00.";

        $data = $this->adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testSerializeBinString()
    {
        $value    = "0123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789"
                  . "0123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789"
                  . "01234567890123456789012345678901234567890123456789012345";
        $expected = "T\x00\x01\x00\x00"
                  . "0123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789"
                  . "0123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789"
                  . "01234567890123456789012345678901234567890123456789012345"
                  . "q\x00.";

        $data = $this->adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

}
