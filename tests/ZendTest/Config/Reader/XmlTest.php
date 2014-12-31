<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Config\Reader;

use ReflectionProperty;
use XMLReader;
use Zend\Config\Reader\Xml;

/**
 * @group      Zend_Config
 *
 * @covers \Zend\Config\Reader\Xml
 */
class XmlTest extends AbstractReaderTestCase
{
    public function setUp()
    {
        $this->reader = new Xml();
    }

    public function tearDown()
    {
        restore_error_handler();
    }

    /**
     * getTestAssetPath(): defined by AbstractReaderTestCase.
     *
     * @see    AbstractReaderTestCase::getTestAssetPath()
     * @return string
     */
    protected function getTestAssetPath($name)
    {
        return __DIR__ . '/TestAssets/Xml/' . $name . '.xml';
    }

    public function testInvalidXmlFile()
    {
        $this->reader = new Xml();
        $this->setExpectedException('Zend\Config\Exception\RuntimeException');
        $arrayXml = $this->reader->fromFile($this->getTestAssetPath('invalid'));
    }

    public function testFromString()
    {
        $xml = <<<ECS
<?xml version="1.0" encoding="UTF-8"?>
<zend-config>
    <test>foo</test>
    <bar>baz</bar>
    <bar>foo</bar>
</zend-config>

ECS;

        $arrayXml= $this->reader->fromString($xml);
        $this->assertEquals($arrayXml['test'], 'foo');
        $this->assertEquals($arrayXml['bar'][0], 'baz');
        $this->assertEquals($arrayXml['bar'][1], 'foo');
    }

    public function testInvalidString()
    {
        $xml = <<<ECS
<?xml version="1.0" encoding="UTF-8"?>
<zend-config>
    <bar>baz</baz>
</zend-config>

ECS;
        $this->setExpectedException('Zend\Config\Exception\RuntimeException');
        $this->reader->fromString($xml);
    }

    public function testZF300_MultipleKeysOfTheSameName()
    {
        $config = $this->reader->fromFile($this->getTestAssetPath('array'));

        $this->assertEquals('2a', $config['one']['two'][0]);
        $this->assertEquals('2b', $config['one']['two'][1]);
        $this->assertEquals('4', $config['three']['four'][1]);
        $this->assertEquals('5', $config['three']['four'][0]['five']);
    }

    public function testZF300_ArraysWithMultipleChildren()
    {
        $config = $this->reader->fromFile($this->getTestAssetPath('array'));

        $this->assertEquals('1', $config['six']['seven'][0]['eight']);
        $this->assertEquals('2', $config['six']['seven'][1]['eight']);
        $this->assertEquals('3', $config['six']['seven'][2]['eight']);
        $this->assertEquals('1', $config['six']['seven'][0]['nine']);
        $this->assertEquals('2', $config['six']['seven'][1]['nine']);
        $this->assertEquals('3', $config['six']['seven'][2]['nine']);
    }

    /**
     * @group zf6279
     */
    public function testElementWithBothAttributesAndAStringValueIsProcessedCorrectly()
    {
        $this->reader = new Xml();
        $arrayXml = $this->reader->fromFile($this->getTestAssetPath('attributes'));
        $this->assertArrayHasKey('one', $arrayXml);
        $this->assertInternalType('array', $arrayXml['one']);
        
        // No attribute + text value == string
        $this->assertArrayHasKey(0, $arrayXml['one']);
        $this->assertEquals('bazbat', $arrayXml['one'][0]);
        
        // Attribute(s) + text value == array
        $this->assertArrayHasKey(1, $arrayXml['one']);
        $this->assertInternalType('array', $arrayXml['one'][1]);
        // Attributes stored in named array keys
        $this->assertArrayHasKey('foo', $arrayXml['one'][1]);
        $this->assertEquals('bar', $arrayXml['one'][1]['foo']);
        // Element value stored in special key '_'
        $this->assertArrayHasKey('_', $arrayXml['one'][1]);
        $this->assertEquals('bazbat', $arrayXml['one'][1]['_']);
    }

    /**
     * @group 6761
     * @group 6730
     */
    public function testReadNonExistingFilesWillFailWithException()
    {
        $configReader = new Xml();

        $this->setExpectedException('Zend\Config\Exception\RuntimeException');

        $configReader->fromFile(sys_get_temp_dir() . '/path/that/does/not/exist');
    }

    /**
     * @group 6761
     * @group 6730
     */
    public function testCloseWhenCallFromFileReaderGetInvalid()
    {
        $configReader = new Xml();

        $configReader->fromFile($this->getTestAssetPath('attributes'));

        $xmlReader = $this->getInternalXmlReader($configReader);

        $this->setExpectedException('PHPUnit_Framework_Error_Warning');
        $xmlReader->setParserProperty(XMLReader::VALIDATE, true);
    }

    /**
     * @group 6761
     * @group 6730
     */
    public function testCloseWhenCallFromStringReaderGetInvalid()
    {
        $xml = <<<ECS
<?xml version="1.0" encoding="UTF-8"?>
<zend-config>
    <test>foo</test>
    <bar>baz</bar>
    <bar>foo</bar>
</zend-config>

ECS;

        $configReader = new Xml();

        $configReader->fromString($xml);

        $xmlReader = $this->getInternalXmlReader($configReader);

        $this->setExpectedException('PHPUnit_Framework_Error_Warning');
        $xmlReader->setParserProperty(XMLReader::VALIDATE, true);
    }

    /**
     * Reads the internal XML reader from a given Xml config reader
     *
     * @param Xml $xml
     *
     * @return XMLReader
     */
    private function getInternalXmlReader(Xml $xml)
    {
        $reflectionReader = new ReflectionProperty('Zend\Config\Reader\Xml', 'reader');

        $reflectionReader->setAccessible(true);

        $xmlReader = $reflectionReader->getValue($xml);

        $this->assertInstanceOf('XMLReader', $xmlReader);

        return $xmlReader;
    }
}
