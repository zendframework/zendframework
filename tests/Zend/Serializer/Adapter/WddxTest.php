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

use Zend\Serializer,
    Zend\Serializer\Exception\ExtensionNotLoadedException;

/**
 * @category   Zend
 * @package    Zend_Serializer
 * @subpackage UnitTests
 * @group      Zend_Serializer
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class WddxTest extends \PHPUnit_Framework_TestCase
{

    private $_adapter;

    public function setUp()
    {
        if (!extension_loaded('wddx')) {
            try {
                new Serializer\Adapter\Wddx();
                $this->fail("Zend\\Serializer\\Adapter\\Wddx needs missing ext/wddx but did't throw exception");
            } catch (ExtensionNotLoadedException $e) {}
            $this->markTestSkipped('Zend\\Serializer\\Adapter\\Wddx needs ext/wddx');
        }
        $this->_adapter = new \Zend\Serializer\Adapter\Wddx();
    }

    public function tearDown()
    {
        $this->_adapter = null;
    }

    public function testSerializeString()
    {
        $value    = 'test';
        $expected = '<wddxPacket version=\'1.0\'><header/>'
                  . '<data><string>test</string></data></wddxPacket>';

        $data = $this->_adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testSerializeStringWithComment()
    {
        $value    = 'test';
        $expected = '<wddxPacket version=\'1.0\'><header><comment>a test comment</comment></header>'
                  . '<data><string>test</string></data></wddxPacket>';

        $data = $this->_adapter->serialize($value, array('comment' => 'a test comment'));
        $this->assertEquals($expected, $data);
    }

    public function testSerializeFalse()
    {
        $value    = false;
        $expected = '<wddxPacket version=\'1.0\'><header/>'
                  . '<data><boolean value=\'false\'/></data></wddxPacket>';

        $data = $this->_adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testSerializeTrue()
    {
        $value    = true;
        $expected = '<wddxPacket version=\'1.0\'><header/>'
                  . '<data><boolean value=\'true\'/></data></wddxPacket>';

        $data = $this->_adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testSerializeNull()
    {
        $value    = null;
        $expected = '<wddxPacket version=\'1.0\'><header/>'
                  . '<data><null/></data></wddxPacket>';

        $data = $this->_adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testSerializeNumeric()
    {
        $value    = 100;
        $expected = '<wddxPacket version=\'1.0\'><header/>'
                  . '<data><number>100</number></data></wddxPacket>';

        $data = $this->_adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testSerializeObject()
    {
        $value = new \stdClass();
        $value->test = "test";
        $expected = '<wddxPacket version=\'1.0\'><header/>'
                  . '<data><struct>'
                  . '<var name=\'php_class_name\'><string>stdClass</string></var>'
                  . '<var name=\'test\'><string>test</string></var>'
                  . '</struct></data></wddxPacket>';

        $data = $this->_adapter->serialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testUnserializeString()
    {
        $value    = '<wddxPacket version=\'1.0\'><header/>'
                  . '<data><string>test</string></data></wddxPacket>';
        $expected = 'test';

        $data = $this->_adapter->unserialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testUnserializeFalse()
    {
        $value    = '<wddxPacket version=\'1.0\'><header/>'
                  . '<data><boolean value=\'false\'/></data></wddxPacket>';
        $expected = false;

        $data = $this->_adapter->unserialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testUnserializeTrue()
    {
        $value    = '<wddxPacket version=\'1.0\'><header/>'
                  . '<data><boolean value=\'true\'/></data></wddxPacket>';
        $expected = true;

        $data = $this->_adapter->unserialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testUnserializeNull1()
    {
        $value    = '<wddxPacket version=\'1.0\'><header/>'
                  . '<data><null/></data></wddxPacket>';
        $expected = null;

        $data = $this->_adapter->unserialize($value);
        $this->assertEquals($expected, $data);
    }

    /**
     * test to unserialize a valid null value by an valid wddx
     * but with some differenzes to the null cenerated by php
     * -> the invalid check have to success for all valid wddx null
     */
    public function testUnserializeNull2()
    {
        $value    = '<wddxPacket version=\'1.0\'><header/>' . "\n"
                  . '<data><null/></data></wddxPacket>';
        $expected = null;

        $data = $this->_adapter->unserialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testUnserializeNumeric()
    {
        $value    = '<wddxPacket version=\'1.0\'><header/>'
                  . '<data><number>100</number></data></wddxPacket>';
        $expected = 100;

        $data = $this->_adapter->unserialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testUnserializeObject()
    {
        $value    = '<wddxPacket version=\'1.0\'><header/>'
                  . '<data><struct>'
                  . '<var name=\'php_class_name\'><string>stdClass</string></var>'
                  . '<var name=\'test\'><string>test</string></var>'
                  . '</struct></data></wddxPacket>';
        $expected = new \stdClass();
        $expected->test = 'test';

        $data = $this->_adapter->unserialize($value);
        $this->assertEquals($expected, $data);
    }

    public function testUnserialzeInvalidXml()
    {
        if (!class_exists('SimpleXMLElement', false)) {
            $this->markTestSkipped('Skipped by missing ext/simplexml');
        }

        $value = 'not a serialized string';
        $this->setExpectedException(
            'Zend\Serializer\Exception\RuntimeException',
            'String could not be parsed as XML'
        );
        $this->_adapter->unserialize($value);
    }

    public function testUnserialzeInvalidWddx()
    {
        if (!class_exists('SimpleXMLElement', false)) {
            $this->markTestSkipped('Skipped by missing ext/simplexml');
        }

        $value = '<wddxPacket version=\'1.0\'><header /></wddxPacket>';
        $this->setExpectedException(
            'Zend\Serializer\Exception\RuntimeException',
            'Invalid wddx'
        );
        $this->_adapter->unserialize($value);
    }

}
