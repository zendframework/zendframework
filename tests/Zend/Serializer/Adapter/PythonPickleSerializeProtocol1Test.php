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
 * @package    Zend_Serializer
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Serializer\Adapter;

use Zend\Serializer;

/**
 * @category   Zend
 * @package    Zend_Serializer
 * @subpackage UnitTests
 * @group      Zend_Serializer
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class PythonPickleSerializeProtocol1Test extends \PHPUnit_Framework_TestCase
{

    private $_adapter;

    public function setUp()
    {
        $this->_adapter = new Serializer\Adapter\PythonPickle(array('protocol' => 1));
    }

    public function tearDown()
    {
        $this->_adapter = null;
    }

    public function testSerializeNull()
   {
        $value    = null;
        $expected = 'N.';

        $data = $this->_adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testSerializeTrue()
    {
        $value    = true;
        $expected = "I01\r\n.";

        $data = $this->_adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testSerializeFalse()
    {
        $value    = false;
        $expected = "I00\r\n.";

        $data = $this->_adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testSerializeBinInt1()
    {
        $value    = 255;
        $expected = "K\xff.";

        $data = $this->_adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testSerializeBinInt2()
    {
        $value    = 256;
        $expected = "M\x00\x01.";

        $data = $this->_adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testSerializeBinInt()
    {
        $value    = -2;
        $expected = "J\xfe\xff\xff\xff.";

        $data = $this->_adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testSerializeBinFloat()
    {
        $value    = -12345.6789;
        $expected = "G\xc0\xc8\x1c\xd6\xe6\x31\xf8\xa1.";

        $data = $this->_adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testSerializeShortBinString()
    {
        $value    = 'test';
        $expected = "U\x04test"
                  . "q\x00.";

        $data = $this->_adapter->serialize($value);
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

        $data = $this->_adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

}
