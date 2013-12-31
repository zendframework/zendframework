<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Config\Writer;

use Zend\Config\Writer\PhpArray;
use Zend\Config\Config;
use ZendTest\Config\Writer\TestAssets\PhpReader;

/**
 * @group      Zend_Config
 */
class PhpArrayTest extends AbstractWriterTestCase
{
    protected $_tempName;

    public function setUp()
    {
        $this->writer = new PhpArray();
        $this->reader = new PhpReader();
    }

    /**
     * @group ZF-8234
     */
    public function testRender()
    {
        $config = new Config(array(
            'test' => 'foo',
            'bar' => array(0 => 'baz', 1 => 'foo'),
            'emptyArray' => array(),
            'object' => (object) array('foo' => 'bar'),
            'integer' => 123,
            'boolean' => false,
            'null' => null,
        ));

        $configString = $this->writer->toString($config);

        // build string line by line as we are trailing-whitespace sensitive.
        $expected = "<?php\n";
        $expected .= "return array(\n";
        $expected .= "    'test' => 'foo',\n";
        $expected .= "    'bar' => array(\n";
        $expected .= "        0 => 'baz',\n";
        $expected .= "        1 => 'foo',\n";
        $expected .= "    ),\n";
        $expected .= "    'emptyArray' => array(),\n";
        $expected .= "    'object' => stdClass::__set_state(array(\n";
        $expected .= "   'foo' => 'bar',\n";
        $expected .= ")),\n";
        $expected .= "    'integer' => 123,\n";
        $expected .= "    'boolean' => false,\n";
        $expected .= "    'null' => null,\n";
        $expected .= ");\n";

        $this->assertEquals($expected, $configString);
    }

    public function testRenderWithBracketArraySyntax()
    {
        $config = new Config(array('test' => 'foo', 'bar' => array(0 => 'baz', 1 => 'foo'), 'emptyArray' => array()));

        $this->writer->setUseBracketArraySyntax(true);
        $configString = $this->writer->toString($config);

        // build string line by line as we are trailing-whitespace sensitive.
        $expected = "<?php\n";
        $expected .= "return [\n";
        $expected .= "    'test' => 'foo',\n";
        $expected .= "    'bar' => [\n";
        $expected .= "        0 => 'baz',\n";
        $expected .= "        1 => 'foo',\n";
        $expected .= "    ],\n";
        $expected .= "    'emptyArray' => [],\n";
        $expected .= "];\n";

        $this->assertEquals($expected, $configString);
    }

    public function testSetUseBracketArraySyntaxReturnsFluentInterface()
    {
        $this->assertSame($this->writer, $this->writer->setUseBracketArraySyntax(true));
    }
}
