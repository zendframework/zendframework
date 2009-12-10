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
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version $Id: ValueTest.php 18442 2009-09-30 13:17:48Z lars $
 */

// Call Zend_XmlRpc_ValueTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    require_once dirname(__FILE__) . '/../../TestHelper.php';
    define("PHPUnit_MAIN_METHOD", "Zend_XmlRpc_ValueTest::main");
}

require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";
require_once 'Zend/XmlRpc/Generator/DOMDocument.php';
require_once 'Zend/XmlRpc/Generator/XMLWriter.php';
require_once 'Zend/XmlRpc/TestProvider.php';

/**
 * Test case for Zend_XmlRpc_Generator_*
 *
 * @category   Zend
 * @package    Zend_XmlRpc
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_XmlRpc
 */
class Zend_XmlRpc_GeneratorTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider Zend_XmlRpc_TestProvider::provideGenerators
     */
    public function testCreatingSingleElement(Zend_XmlRpc_Generator_Abstract $generator)
    {
        $generator->startElement('element');
        $generator->endElement('element');
        $this->assertXml('<element/>', $generator);
    }

    /**
     * @dataProvider Zend_XmlRpc_TestProvider::provideGenerators
     */
    public function testCreatingSingleElementWithValue(Zend_XmlRpc_Generator_Abstract $generator)
    {
        $generator->startElement('element', 'value');
        $generator->endElement('element');
        $this->assertXml('<element>value</element>', $generator);
    }

    /**
     * @dataProvider Zend_XmlRpc_TestProvider::provideGenerators
     */
    public function testCreatingComplexXmlDocument(Zend_XmlRpc_Generator_Abstract $generator)
    {
        $generator->startElement('root');
        $generator->startElement('children');
        $generator->startElement('child', 'child1')->endElement('child');
        $generator->startElement('child', 'child2')->endElement('child');
        $generator->endElement('children');
        $generator->endElement('root');
        $this->assertXml(
            '<root><children><child>child1</child><child>child2</child></children></root>',
            $generator
        );
    }

    /**
     * @dataProvider Zend_XmlRpc_TestProvider::provideGenerators
     */
    public function testFlushingGeneratorFlushesEverything(Zend_XmlRpc_Generator_Abstract $generator)
    {
        $generator->startElement('test')->endElement('test');
        $this->assertXml('<test/>', $generator);
        $this->assertContains('<test/>', $generator->flush());
        $this->assertSame('', (string)$generator);
    }

    /**
     * @dataProvider Zend_XmlRpc_TestProvider::provideGenerators
     */
    public function testSpecialCharsAreEncoded(Zend_XmlRpc_Generator_Abstract $generator)
    {
        $generator->startElement('element', '<>&"\'€')->endElement('element');
        $variant1 = '<element>&lt;&gt;&amp;"\'€</element>';
        $variant2 = '<element>&lt;&gt;&amp;&quot;\'€</element>';
        try {
            $this->assertXml($variant1, $generator);
        } catch (PHPUnit_Framework_ExpectationFailedException $e) {
            $this->assertXml($variant2, $generator);
        }
    }

    /**
     * @dataProvider Zend_XmlRpc_TestProvider::provideGeneratorsWithAlternateEncodings
     */
    public function testDifferentEncodings(Zend_XmlRpc_Generator_Abstract $generator)
    {
        $generator->startElement('element', '€')->endElement('element');
        $this->assertXml('<element>&#8364;</element>', $generator);
    }

    /**
     * @dataProvider Zend_XmlRpc_TestProvider::provideGenerators
     */
    public function testFluentInterfacesProvided(Zend_XmlRpc_Generator_Abstract $generator)
    {
        $this->assertSame($generator, $generator->startElement('foo'));
        $this->assertSame($generator, $generator->endElement('foo'));
    }

    public function assertXml($expected, $actual)
    {
        $expected = trim($expected);
        $this->assertSame($expected, trim($actual));
        $xmlDecl = '<?xml version="1.0" encoding="' . $actual->getEncoding() . '"?>' . "\n";
        $this->assertSame($xmlDecl . $expected, trim($actual->saveXml()));
    }
}
