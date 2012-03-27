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
 * @package    Zend_XmlRpc
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\XmlRpc;

use Zend\XmlRpc\Generator;

/**
 * Test case for Zend_XmlRpc_Generator_*
 *
 * @category   Zend
 * @package    Zend_XmlRpc
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_XmlRpc
 */
class GeneratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider ZendTest\XmlRpc\TestProvider::provideGenerators
     */
    public function testCreatingSingleElement(Generator $generator)
    {
        $generator->openElement('element');
        $generator->closeElement('element');
        $this->assertXml('<element/>', $generator);
    }

    /**
     * @dataProvider ZendTest\XmlRpc\TestProvider::provideGenerators
     */
    public function testCreatingSingleElementWithValue(Generator $generator)
    {
        $generator->openElement('element', 'value');
        $generator->closeElement('element');
        $this->assertXml('<element>value</element>', $generator);
    }

    /**
     * @dataProvider ZendTest\XmlRpc\TestProvider::provideGenerators
     */
    public function testCreatingComplexXmlDocument(Generator $generator)
    {
        $generator->openElement('root');
        $generator->openElement('children');
        $generator->openElement('child', 'child1')->closeElement('child');
        $generator->openElement('child', 'child2')->closeElement('child');
        $generator->closeElement('children');
        $generator->closeElement('root');
        $this->assertXml(
            '<root><children><child>child1</child><child>child2</child></children></root>',
            $generator
        );
    }

    /**
     * @dataProvider ZendTest\XmlRpc\TestProvider::provideGenerators
     */
    public function testFlushingGeneratorFlushesEverything(Generator $generator)
    {
        $generator->openElement('test')->closeElement('test');
        $this->assertXml('<test/>', $generator);
        $this->assertContains('<test/>', $generator->flush());
        $this->assertSame('', (string)$generator);
    }

    /**
     * @dataProvider ZendTest\XmlRpc\TestProvider::provideGenerators
     */
    public function testSpecialCharsAreEncoded(Generator $generator)
    {
        $generator->openElement('element', '<>&"\'€')->closeElement('element');
        $variant1 = '<element>&lt;&gt;&amp;"\'€</element>';
        $variant2 = '<element>&lt;&gt;&amp;&quot;\'€</element>';
        try {
            $this->assertXml($variant1, $generator);
        } catch (\PHPUnit_Framework_ExpectationFailedException $e) {
            $this->assertXml($variant2, $generator);
        }
    }

    /**
     * @dataProvider ZendTest\XmlRpc\TestProvider::provideGeneratorsWithAlternateEncodings
     */
    public function testDifferentEncodings(Generator $generator)
    {
        $generator->openElement('element', '€')->closeElement('element');
        $this->assertXml('<element>&#8364;</element>', $generator);
    }

    /**
     * @dataProvider ZendTest\XmlRpc\TestProvider::provideGenerators
     */
    public function testFluentInterfacesProvided(Generator $generator)
    {
        $this->assertSame($generator, $generator->openElement('foo'));
        $this->assertSame($generator, $generator->closeElement('foo'));
    }

    public function assertXml($expected, $actual)
    {
        $expected = trim($expected);
        $this->assertSame($expected, trim($actual));
        $xmlDecl = '<?xml version="1.0" encoding="' . $actual->getEncoding() . '"?>' . "\n";
        $this->assertSame($xmlDecl . $expected, trim($actual->saveXml()));
    }
}
