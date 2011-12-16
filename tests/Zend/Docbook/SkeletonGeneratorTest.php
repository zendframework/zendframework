<?php

namespace ZendTest\Docbook;

use DOMDocument,
    DOMXPath,
    PHPUnit_Framework_TestCase as TestCase,
    Zend\Docbook\ClassParser,
    Zend\Docbook\SkeletonGenerator,
    Zend\Code\Reflection\ClassReflection;

class SkeletonGeneratorTest extends TestCase
{
    public function setUp()
    {
        $this->class     = new ClassReflection(new TestAsset\ParsedClass());
        $this->parser    = new ClassParser($this->class);
        $this->generator = new SkeletonGenerator($this->parser);
    }

    public function testGeneratorCreatesExpectedOutputStructure()
    {
        $docbook = $this->generator->generate();
        $dom     = new DOMDocument();
        $dom->loadXML($docbook);
        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace('docbook', 'http://docbook.org/ns/docbook');

        // Root node ID
        $this->assertContains('xml:id="zend-test.docbook.test-asset.parsed-class"', $docbook);

        // Intro section
        $nodes = $xpath->query('//docbook:section[@xml:id="zend-test.docbook.test-asset.parsed-class.intro"]');
        $this->assertInstanceOf('DOMNodeList', $nodes);
        $this->assertEquals(1, $nodes->length);
        $nodes = $xpath->query('//docbook:section[@xml:id="zend-test.docbook.test-asset.parsed-class.intro"]/docbook:info/docbook:title');
        $this->assertEquals(1, $nodes->length);
        $node = $nodes->item(0);
        $this->assertEquals('Overview', $node->nodeValue);

        // Quick Start section
        $nodes = $xpath->query('//docbook:section[@xml:id="zend-test.docbook.test-asset.parsed-class.quick-start"]');
        $this->assertInstanceOf('DOMNodeList', $nodes);
        $this->assertEquals(1, $nodes->length);
        $nodes = $xpath->query('//docbook:section[@xml:id="zend-test.docbook.test-asset.parsed-class.quick-start"]/docbook:info/docbook:title');
        $this->assertEquals(1, $nodes->length);
        $node = $nodes->item(0);
        $this->assertEquals('Quick Start', $node->nodeValue);

        // Options section
        $nodes = $xpath->query('//docbook:section[@xml:id="zend-test.docbook.test-asset.parsed-class.options"]');
        $this->assertInstanceOf('DOMNodeList', $nodes);
        $this->assertEquals(1, $nodes->length);
        $nodes = $xpath->query('//docbook:section[@xml:id="zend-test.docbook.test-asset.parsed-class.options"]/docbook:info/docbook:title');
        $this->assertEquals(1, $nodes->length);
        $node = $nodes->item(0);
        $this->assertEquals('Configuration Options', $node->nodeValue);

        // Examples section
        $nodes = $xpath->query('//docbook:section[@xml:id="zend-test.docbook.test-asset.parsed-class.examples"]');
        $this->assertInstanceOf('DOMNodeList', $nodes);
        $this->assertEquals(1, $nodes->length);
        $nodes = $xpath->query('//docbook:section[@xml:id="zend-test.docbook.test-asset.parsed-class.examples"]/docbook:info/docbook:title');
        $this->assertEquals(1, $nodes->length);
        $node = $nodes->item(0);
        $this->assertEquals('Examples', $node->nodeValue);

        // Methods section
        $nodes = $xpath->query('//docbook:section[@xml:id="zend-test.docbook.test-asset.parsed-class.methods"]');
        $this->assertInstanceOf('DOMNodeList', $nodes);
        $this->assertEquals(1, $nodes->length);
        $nodes = $xpath->query('//docbook:section[@xml:id="zend-test.docbook.test-asset.parsed-class.methods"]/docbook:info/docbook:title');
        $this->assertEquals(1, $nodes->length);
        $node = $nodes->item(0);
        $this->assertEquals('Available Methods', $node->nodeValue);
    }

    public function testGeneratesEntriesForEachPublicMethod()
    {
        $docbook = $this->generator->generate();
        $dom     = new DOMDocument();
        $dom->loadXML($docbook);
        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace('docbook', 'http://docbook.org/ns/docbook');

        $baseId  = 'zend-test.docbook.test-asset.parsed-class.methods';
        $methods = $this->parser->getMethods();

        // Get list of varlistentry items
        $nodes = $xpath->query('//docbook:section[@xml:id="' . $baseId . '"]/docbook:variablelist/docbook:varlistentry');

        $this->assertEquals(count($methods), $nodes->length);

        // Get varlistentry IDs, methodnames, and funcparams
        for ($i = 0; $i < $nodes->length; $i++) {
            $xml    = $dom->saveXML($nodes->item($i));
            $method = $methods[$i];

            $this->assertContains($method->getId(), $xml);

            $proto = $method->getPrototype();
            if (empty($proto)) {
                $this->assertContains('<funcparams/>', $xml);
            } else {
                $this->assertContains('<funcparams>' . $proto . '</funcparams>', $xml, $xml);
            }
            $this->assertContains($method->getReturnType(), $xml);

            $short = $method->getShortDescription();
            if (empty($short)) {
                $this->assertContains('<para/>', $xml);
            } else {
                $this->assertContains($short, $xml);
            }

            $long  = $method->getLongDescription();
            if (empty($long)) {
                $this->assertContains('<para/>', $xml);
            } else {
                $this->assertContains($long, $xml);
            }
        }
    }
}
