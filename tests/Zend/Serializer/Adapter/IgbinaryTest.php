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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @category   Zend
 * @package    Zend_Serializer
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Serializer_Adapter_IgbinaryTest extends PHPUnit_Framework_TestCase
{

    private $_adapter;

    public function setUp()
    {
        if (!constant('TESTS_ZEND_SERIALIZER_ADAPTER_IGBINARY_ENABLED')) {
            $this->markTestSkipped('Zend_Serializer IgBinary tests are not enabled');
        }
        $this->_adapter = new Zend_Serializer_Adapter_Igbinary();
    }

    public function tearDown()
    {
        $this->_adapter = null;
    }

    public function testSerializeString()
    {
        $value    = 'test';
        $expected = igbinary_serialize($value);

        $data = $this->_adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testSerializeFalse()
    {
        $value    = false;
        $expected = igbinary_serialize($value);

        $data = $this->_adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testSerializeNull()
    {
        $value    = null;
        $expected = igbinary_serialize($value);

        $data = $this->_adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testSerializeNumeric()
    {
        $value    = 100;
        $expected = igbinary_serialize($value);

        $data = $this->_adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testSerializeObject()
    {
        $value    = new stdClass();
        $expected = igbinary_serialize($value);

        $data = $this->_adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testUnserializeString()
    {
        $expected = 'test';
        $value    = igbinary_serialize($expected);

        $data = $this->_adapter->unserialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testUnserializeFalse()
    {
        $expected = false;
        $value    = igbinary_serialize($expected);

        $data = $this->_adapter->unserialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testUnserializeNull()
    {
        $expected = null;
        $value    = igbinary_serialize($expected);

        $data = $this->_adapter->unserialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testUnserializeNumeric()
    {
        $expected = 100;
        $value    = igbinary_serialize($expected);

        $data = $this->_adapter->unserialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testUnserializeObject()
    {
        $expected = new stdClass();
        $value    = igbinary_serialize($expected);

        $data = $this->_adapter->unserialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testUnserialzeInvalid()
    {
        $value = "\0\1\r\n";
        $this->setExpectedException('Zend_Serializer_Exception');
        $this->_adapter->unserialize($value);
    }

}

