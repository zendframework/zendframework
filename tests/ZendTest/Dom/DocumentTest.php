<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Dom;

use Zend\Dom\Document;
use Zend\Dom\NodeList;
use Zend\Dom\Exception\ExceptionInterface as DOMException;

/**
 * Test class for Zend\Dom\Document.
 *
 * @group      Zend_Dom
 */
class DocumentTest extends \PHPUnit_Framework_TestCase
{
    public $html;
    public $document;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        $this->document = new Document();
    }

    public function getHtml()
    {
        if (null === $this->html) {
            $this->html  = file_get_contents(__DIR__ . '/_files/sample.xhtml');
        }
        return $this->html;
    }

    public function loadHtml()
    {
        $this->document->setStringDocument($this->getHtml());
    }

    public function handleError($msg, $code = 0)
    {
        $this->error = $msg;
    }

    public function testConstructorShouldNotRequireArguments()
    {
        $document = new Document();
    }

    public function testConstructorShouldAcceptDocumentString()
    {
        $html  = $this->getHtml();
        $document = new Document($html);
        $this->assertSame($html, $document->getStringDocument());
    }

    public function testDocShouldBeNullByDefault()
    {
        $this->assertNull($this->document->getStringDocument());
    }

    public function testDomDocShouldRaiseExceptionByDefault()
    {
        $this->setExpectedException('\Zend\Dom\Exception\RuntimeException', 'no document');
        $this->document->getDomDocument();
    }

    public function testDocShouldBeNullByEmptyStringConstructor()
    {
        $emptyStr = '';
        $document = new Document($emptyStr);
        $this->assertNull($this->document->getStringDocument());
    }

    public function testDocShouldBeNullByEmptyStringSet()
    {
        $emptyStr = '';
        $this->document->setStringDocument($emptyStr);
        $this->assertNull($this->document->getStringDocument());
    }

    public function testDocTypeShouldBeNullByDefault()
    {
        $this->assertNull($this->document->getType());
    }

    public function testDocEncodingShouldBeNullByDefault()
    {
        $this->assertNull($this->document->getEncoding());
    }

    public function testShouldAllowSettingDocument()
    {
        $this->testDocShouldBeNullByDefault();
        $this->loadHtml();
        $this->assertEquals($this->getHtml(), $this->document->getStringDocument());
    }

    public function testDocumentTypeShouldBeAutomaticallyDiscovered()
    {
        $this->loadHtml();
        $this->assertEquals(Document::DOC_XHTML, $this->document->getType());
        $this->document->setStringDocument('<?xml version="1.0"?><root></root>');
        $this->assertEquals(Document::DOC_XML, $this->document->getType());
        $this->document->setStringDocument('<html><body></body></html>');
        $this->assertEquals(Document::DOC_HTML, $this->document->getType());
    }

    public function testQueryingWithoutRegisteringDocumentShouldThrowException()
    {
        $this->setExpectedException('\Zend\Dom\Exception\RuntimeException', 'no document');
        $this->document->queryCss('.foo');
    }

    public function testQueryingInvalidDocumentShouldThrowException()
    {
        set_error_handler(array($this, 'handleError'));
        $this->document->setStringDocument('some bogus string', Document::DOC_XML);
        try {
            $this->document->queryCss('.foo');
            restore_error_handler();
            $this->fail('Querying invalid document should throw exception');
        } catch (DOMException $e) {
            restore_error_handler();
            $this->assertContains('Error parsing', $e->getMessage());
        }
    }

    public function testgetDomMethodShouldReturnDomDocumentWithStringDocumentInConstructor()
    {
        $html  = $this->getHtml();
        $document = new Document($html);
        $this->assertTrue($document->getDomDocument() instanceof \DOMDocument);
    }

    public function testgetDomMethodShouldReturnDomDocumentWithStringDocumentSetFromMethod()
    {
        $this->loadHtml();
        $this->assertTrue($this->document->getDomDocument() instanceof \DOMDocument);
    }

    public function testSettingNewDocumentResetsAllPreviousDocumentAttributes()
    {
        $document    = new Document();
        $document->setStringDocument($this->getHtml(), Document::DOC_XHTML, 'ISO-8859-1');
        $oldDocument = clone $document;
        $document->setStringDocument('<html><body><p>test</p></body></html>', Document::DOC_HTML, 'UTF-8');
        $this->assertNotEquals($document->getStringDocument(), $oldDocument->getStringDocument());
        $this->assertNotEquals($document->getType(), $oldDocument->getType());
        $this->assertNotEquals($document->getEncoding(), $oldDocument->getEncoding());
    }

    public function testSettingNewEmptyDocumentsResetsAllPreviousDocumentAttributes()
    {
        $document    = new Document();
        $document->setStringDocument($this->getHtml(), Document::DOC_XHTML, 'ISO-8859-1');
        $oldDocument = clone $document;
        $document->setStringDocument(null, Document::DOC_HTML, 'UTF-8');
        $this->assertNotEquals($document->getStringDocument(), $oldDocument->getStringDocument());
        $this->assertNotEquals($document->getType(), $oldDocument->getType());
        $this->assertNotEquals($document->getEncoding(), $oldDocument->getEncoding());
    }

    public function testQueryShouldReturnResultObject()
    {
        $this->loadHtml();
        $test = $this->document->queryCss('.foo');
        $this->assertTrue($test instanceof NodeList);
    }

    public function testResultShouldIndicateNumberOfFoundNodes()
    {
        $this->loadHtml();
        $result  = $this->document->queryCss('.foo');
        $message = 'Xpath: ' . $result->getXpathQuery() . "\n";
        $this->assertEquals(3, count($result), $message);
    }

    public function testResultShouldAllowIteratingOverFoundNodes()
    {
        $this->loadHtml();
        $result = $this->document->queryCss('.foo');
        $this->assertEquals(3, count($result));
        foreach ($result as $node) {
            $this->assertTrue($node instanceof \DOMNode, var_export($result, 1));
        }
    }

    public function testQueryShouldFindNodesWithMultipleClasses()
    {
        $this->loadHtml();
        $result = $this->document->queryCss('.footerblock .last');
        $this->assertEquals(1, count($result), $result->getXpathQuery());
    }

    public function testQueryShouldFindNodesWithArbitraryAttributeSelectorsExactly()
    {
        $this->loadHtml();
        $result = $this->document->queryCss('div[dojoType="FilteringSelect"]');
        $this->assertEquals(1, count($result), $result->getXpathQuery());
    }

    public function testQueryShouldFindNodesWithArbitraryAttributeSelectorsAsDiscreteWords()
    {
        $this->loadHtml();
        $result = $this->document->queryCss('li[dojoType~="bar"]');
        $this->assertEquals(2, count($result), $result->getXpathQuery());
    }

    public function testQueryShouldFindNodesWithArbitraryAttributeSelectorsAndAttributeValue()
    {
        $this->loadHtml();
        $result = $this->document->queryCss('li[dojoType*="bar"]');
        $this->assertEquals(2, count($result), $result->getXpathQuery());
    }

    public function testQueryXpathShouldAllowQueryingArbitraryUsingXpath()
    {
        $this->loadHtml();
        $result = $this->document->queryXpath('//li[contains(@dojotype, "bar")]');
        $this->assertEquals(2, count($result), $result->getXpathQuery());
    }

    public function testXpathPhpFunctionsShouldBeDisabledByDefault()
    {
        $this->loadHtml();
        try {
            $this->document->queryXpath('//meta[php:functionString("strtolower", @http-equiv) = "content-type"]');
        } catch (\Exception $e) {
            return;
        }
        $this->assertFails('XPath PHPFunctions should be disabled by default');
    }

    public function testXpathPhpFunctionsShouldBeEnabledWithoutParameter()
    {
        $this->loadHtml();
        $this->document->registerXpathPhpFunctions();
        $result = $this->document->queryXpath('//meta[php:functionString("strtolower", @http-equiv) = "content-type"]');
        $this->assertEquals(
            'content-type',
            strtolower($result->current()->getAttribute('http-equiv')),
            $result->getXpathQuery()
        );
    }

    public function testXpathPhpFunctionsShouldBeNotCalledWhenSpecifiedFunction()
    {
        $this->loadHtml();
        try {
            $this->document->registerXpathPhpFunctions('stripos');
            $this->document->queryXpath('//meta[php:functionString("strtolower", @http-equiv) = "content-type"]');
        } catch (\Exception $e) {
            // $e->getMessage() - Not allowed to call handler 'strtolower()
            return;
        }
        $this->assertFails('Not allowed to call handler strtolower()');
    }

    /**
     * @group ZF-9243
     */
    public function testLoadingDocumentWithErrorsShouldNotRaisePhpErrors()
    {
        $file = file_get_contents(__DIR__ . '/_files/bad-sample.html');
        $this->document->setStringDocument($file);
        $this->document->queryCss('p');
        $errors = $this->document->getErrors();
        $this->assertTrue(is_array($errors));
        $this->assertTrue(0 < count($errors));
    }

    /**
     * @group ZF-9765
     */
    public function testCssSelectorShouldFindNodesWhenMatchingMultipleAttributes()
    {
        $html = <<<EOF
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<body>
  <form action="#" method="get">
    <input type="hidden" name="foo" value="1" id="foo"/>
    <input type="hidden" name="bar" value="0" id="bar"/>
    <input type="hidden" name="baz" value="1" id="baz"/>
  </form>
</body>
</html>
EOF;

        $this->document->setStringDocument($html);
        $results = $this->document->queryCss('input[type="hidden"][value="1"]');
        $this->assertEquals(2, count($results), $results->getXpathQuery());
        $results = $this->document->queryCss('input[value="1"][type~="hidden"]');
        $this->assertEquals(2, count($results), $results->getXpathQuery());
        $results = $this->document->queryCss('input[type="hidden"][value="0"]');
        $this->assertEquals(1, count($results));
    }

    /**
     * @group ZF-3938
     */
    public function testAllowsSpecifyingEncodingAtConstruction()
    {
        $doc = new Document($this->getHtml(), 'iso-8859-1');
        $this->assertEquals('iso-8859-1', $doc->getEncoding());
    }

    /**
     * @group ZF-3938
     */
    public function testAllowsSpecifyingEncodingWhenSettingDocument()
    {
        $this->document->setStringDocument($this->getHtml(), null, 'iso-8859-1');
        $this->assertEquals('iso-8859-1', $this->document->getEncoding());
    }

    /**
     * @group ZF-3938
     */
    public function testAllowsSpecifyingEncodingViaSetter()
    {
        $this->document->setEncoding('iso-8859-1');
        $this->assertEquals('iso-8859-1', $this->document->getEncoding());
    }

    /**
     * @group ZF-3938
     */
    public function testSpecifyingEncodingSetsEncodingOnDomDocument()
    {
        $this->document->setStringDocument($this->getHtml(), 'utf-8');
        $test = $this->document->queryCss('.foo');
        $this->assertInstanceof('\\Zend\\Dom\\NodeList', $test);
        $doc  = $test->getDocument();
        $this->assertInstanceof('\\DOMDocument', $doc);
        $this->assertEquals('utf-8', $doc->encoding);
    }

    /**
     * @group ZF-11376
     */
    public function testXhtmlDocumentWithXmlDeclaration()
    {
        $xhtmlWithXmlDecl = <<<EOB
<?xml version="1.0" encoding="UTF-8" ?>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head><title /></head>
    <body><p>Test paragraph.</p></body>
</html>
EOB;
        $this->document->setStringDocument($xhtmlWithXmlDecl, 'utf-8');
        $this->assertEquals(1, $this->document->queryCss('//p')->count());
    }

    /**
     * @group ZF-12106
     */
    public function testXhtmlDocumentWithXmlAndDoctypeDeclaration()
    {
        $xhtmlWithXmlDecl = <<<EOB
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html
     PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <title>Virtual Library</title>
  </head>
  <body>
    <p>Moved to <a href="http://example.org/">example.org</a>.</p>
  </body>
</html>
EOB;
        $this->document->setStringDocument($xhtmlWithXmlDecl, 'utf-8');
        $this->assertEquals(1, $this->document->queryCss('//p')->count());
    }

    public function testLoadingXmlContainingDoctypeShouldFailToPreventXxeAndXeeAttacks()
    {
        $xml = <<<XML
<?xml version="1.0"?>
<!DOCTYPE results [<!ENTITY harmless "completely harmless">]>
<results>
    <result>This result is &harmless;</result>
</results>
XML;
        $this->document->setStringDocument($xml);
        $this->setExpectedException("\Zend\Dom\Exception\RuntimeException");
        $this->document->queryXpath('/');
    }

    public function testOffsetExists()
    {
        $this->loadHtml();
        $results = $this->document->queryCss('input');

        $this->assertEquals(3, $results->count());
        $this->assertFalse($results->offsetExists(3));
        $this->assertTrue($results->offsetExists(2));
    }

    public function testOffsetGet()
    {
        $this->loadHtml();
        $results = $this->document->queryCss('input');

        $this->assertEquals(3, $results->count());
        $this->assertEquals('login', $results[2]->getAttribute('id'));
    }

    /**
     * @expectedException Zend\Dom\Exception\BadMethodCallException
     */
    public function testOffsetSet()
    {
        $this->loadHtml();
        $results = $this->document->queryCss('input');
        $this->assertEquals(3, $results->count());

        $results[0] = '<foobar />';
    }


    /**
     * @expectedException Zend\Dom\Exception\BadMethodCallException
     */
    public function testOffsetUnset()
    {
        $this->loadHtml();
        $results = $this->document->queryCss('input');
        $this->assertEquals(3, $results->count());

        unset($results[2]);
    }
}
