<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_DocBook
 */

namespace ZendTest\DocBook;

use DOMDocument;
use DOMXPath;
use PHPUnit_Framework_TestCase as TestCase;
use Zend\DocBook\ClassParser;
use Zend\DocBook\SkeletonGenerator;
use Zend\Code\Reflection\ClassReflection;

/**
 * @category   Zend
 * @package    Zend_DocBook
 * @subpackage UnitTests
 */
class SkeletonGeneratorTest extends TestCase
{
    /** @var ClassReflection */
    public $class;
    /** @var ClassParser */
    public $parser;
    /** @var SkeletonGenerator */
    public $generator;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        $this->class     = new ClassReflection(new TestAsset\ParsedClass());
        $this->parser    = new ClassParser($this->class);
        $this->generator = new SkeletonGenerator($this->parser);
    }

    public function testGeneratorCreatesExpectedOutputStructure()
    {
        $docBook = $this->generator->generate();
        $dom     = new DOMDocument();
        $dom->loadXML($docBook);
        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace('docbook', 'http://docbook.org/ns/docbook');

        // Root node ID
        $this->assertContains('xml:id="zend-test.doc-book.test-asset.parsed-class"', $docBook);

        // Intro section
        $nodes = $xpath->query('//docbook:section[@xml:id="zend-test.doc-book.test-asset.parsed-class.intro"]');
        $this->assertInstanceOf('DOMNodeList', $nodes);
        $this->assertEquals(1, $nodes->length);
        $nodes = $xpath->query('//docbook:section[@xml:id="zend-test.doc-book.test-asset.parsed-class.intro"]/docbook:info/docbook:title');
        $this->assertEquals(1, $nodes->length);
        $node = $nodes->item(0);
        $this->assertEquals('Overview', $node->nodeValue);

        // Quick Start section
        $nodes = $xpath->query('//docbook:section[@xml:id="zend-test.doc-book.test-asset.parsed-class.quick-start"]');
        $this->assertInstanceOf('DOMNodeList', $nodes);
        $this->assertEquals(1, $nodes->length);
        $nodes = $xpath->query('//docbook:section[@xml:id="zend-test.doc-book.test-asset.parsed-class.quick-start"]/docbook:info/docbook:title');
        $this->assertEquals(1, $nodes->length);
        $node = $nodes->item(0);
        $this->assertEquals('Quick Start', $node->nodeValue);

        // Options section
        $nodes = $xpath->query('//docbook:section[@xml:id="zend-test.doc-book.test-asset.parsed-class.options"]');
        $this->assertInstanceOf('DOMNodeList', $nodes);
        $this->assertEquals(1, $nodes->length);
        $nodes = $xpath->query('//docbook:section[@xml:id="zend-test.doc-book.test-asset.parsed-class.options"]/docbook:info/docbook:title');
        $this->assertEquals(1, $nodes->length);
        $node = $nodes->item(0);
        $this->assertEquals('Configuration Options', $node->nodeValue);

        // Examples section
        $nodes = $xpath->query('//docbook:section[@xml:id="zend-test.doc-book.test-asset.parsed-class.examples"]');
        $this->assertInstanceOf('DOMNodeList', $nodes);
        $this->assertEquals(1, $nodes->length);
        $nodes = $xpath->query('//docbook:section[@xml:id="zend-test.doc-book.test-asset.parsed-class.examples"]/docbook:info/docbook:title');
        $this->assertEquals(1, $nodes->length);
        $node = $nodes->item(0);
        $this->assertEquals('Examples', $node->nodeValue);

        // Methods section
        $nodes = $xpath->query('//docbook:section[@xml:id="zend-test.doc-book.test-asset.parsed-class.methods"]');
        $this->assertInstanceOf('DOMNodeList', $nodes);
        $this->assertEquals(1, $nodes->length);
        $nodes = $xpath->query('//docbook:section[@xml:id="zend-test.doc-book.test-asset.parsed-class.methods"]/docbook:info/docbook:title');
        $this->assertEquals(1, $nodes->length);
        $node = $nodes->item(0);
        $this->assertEquals('Available Methods', $node->nodeValue);
    }

    public function testGeneratesEntriesForEachPublicMethod()
    {
        $docBook = $this->generator->generate();
        $dom     = new DOMDocument();
        $dom->loadXML($docBook);
        $xpath = new DOMXPath($dom);
        $xpath->registerNamespace('docbook', 'http://docbook.org/ns/docbook');

        $baseId  = 'zend-test.doc-book.test-asset.parsed-class.methods';
        $methods = $this->parser->getMethods();

        // Get list of varlistentry items
        $nodes = $xpath->query(
            '//docbook:section[@xml:id="' . $baseId . '"]/docbook:variablelist/docbook:varlistentry');

        $this->assertEquals(count($methods), $nodes->length);

        // Get varlistentry IDs, methodnames, and funcparams
        for ($i = 0; $i < $nodes->length; $i++) {
            $xml    = $dom->saveXML($nodes->item($i));
            $method = $methods[$i];

            $this->assertContains($method->getId(), $xml);

            $prototype = $method->getPrototype();
            if (empty($prototype)) {
                $this->assertContains('<funcparams/>', $xml);
            } else {
                $this->assertContains('<funcparams>' . $prototype . '</funcparams>', $xml, $xml);
            }
            $this->assertContains($method->getReturnType(), $xml);

            $short = $method->getShortDescription();
            if (empty($short)) {
                $this->assertContains('<para/>', $xml);
            } else {
                $this->assertContains($short, $xml);
            }

            $long = $method->getLongDescription();
            if (!empty($long)) {
                $this->assertContains($long, $xml);
            }
        }
    }
}
