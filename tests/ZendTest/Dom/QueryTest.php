<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Dom
 */

namespace ZendTest\Dom;

use Zend\Dom\Query;
use Zend\Dom\NodeList;
use Zend\Dom\Exception\ExceptionInterface as DOMException;

/**
 * Test class for Zend_Dom_Query.
 *
 * @category   Zend
 * @package    Zend_Dom
 * @subpackage UnitTests
 * @group      Zend_Dom
 */
class QueryTest extends \PHPUnit_Framework_TestCase
{
    public $html;
    public $query;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        $this->query = new Query();
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
        $this->query->setDocument($this->getHtml());
    }

    public function handleError($msg, $code = 0)
    {
        $this->error = $msg;
    }

    public function testConstructorShouldNotRequireArguments()
    {
        $query = new Query();
    }

    public function testConstructorShouldAcceptDocumentString()
    {
        $html  = $this->getHtml();
        $query = new Query($html);
        $this->assertSame($html, $query->getDocument());
    }

    public function testDocShouldBeNullByDefault()
    {
        $this->assertNull($this->query->getDocument());
    }

    public function testDocShouldBeNullByEmptyStringConstructor()
    {
        $emptyStr = "";
        $query = new Query($emptyStr);
        $this->assertNull($this->query->getDocument());
    }

    public function testDocShouldBeNullByEmptyStringSet()
    {
        $emptyStr = "";
        $this->query->setDocument($emptyStr);
        $this->assertNull($this->query->getDocument());
    }

    public function testDocTypeShouldBeNullByDefault()
    {
        $this->assertNull($this->query->getDocumentType());
    }

    public function testShouldAllowSettingDocument()
    {
        $this->testDocShouldBeNullByDefault();
        $this->loadHtml();
        $this->assertEquals($this->getHtml(), $this->query->getDocument());
    }

    public function testDocumentTypeShouldBeAutomaticallyDiscovered()
    {
        $this->loadHtml();
        $this->assertEquals(Query::DOC_XHTML, $this->query->getDocumentType());
        $this->query->setDocument('<?xml version="1.0"?><root></root>');
        $this->assertEquals(Query::DOC_XML, $this->query->getDocumentType());
        $this->query->setDocument('<html><body></body></html>');
        $this->assertEquals(Query::DOC_HTML, $this->query->getDocumentType());
    }

    public function testQueryingWithoutRegisteringDocumentShouldThrowException()
    {
        $this->setExpectedException('\Zend\Dom\Exception\RuntimeException', 'no document');
        $this->query->execute('.foo');
    }

    public function testQueryingInvalidDocumentShouldThrowException()
    {
        set_error_handler(array($this, 'handleError'));
        $this->query->setDocumentXml('some bogus string');
        try {
            $this->query->execute('.foo');
            restore_error_handler();
            $this->fail('Querying invalid document should throw exception');
        } catch (DOMException $e) {
            restore_error_handler();
            $this->assertContains('Error parsing', $e->getMessage());
        }
    }

    public function testQueryShouldReturnResultObject()
    {
        $this->loadHtml();
        $test = $this->query->execute('.foo');
        $this->assertTrue($test instanceof NodeList);
    }

    public function testResultShouldIndicateNumberOfFoundNodes()
    {
        $this->loadHtml();
        $result  = $this->query->execute('.foo');
        $message = 'Xpath: ' . $result->getXpathQuery() . "\n";
        $this->assertEquals(3, count($result), $message);
    }

    public function testResultShouldAllowIteratingOverFoundNodes()
    {
        $this->loadHtml();
        $result = $this->query->execute('.foo');
        $this->assertEquals(3, count($result));
        foreach ($result as $node) {
            $this->assertTrue($node instanceof \DOMNode, var_export($result, 1));
        }
    }

    public function testQueryShouldFindNodesWithMultipleClasses()
    {
        $this->loadHtml();
        $result = $this->query->execute('.footerblock .last');
        $this->assertEquals(1, count($result), $result->getXpathQuery());
    }

    public function testQueryShouldFindNodesWithArbitraryAttributeSelectorsExactly()
    {
        $this->loadHtml();
        $result = $this->query->execute('div[dojoType="FilteringSelect"]');
        $this->assertEquals(1, count($result), $result->getXpathQuery());
    }

    public function testQueryShouldFindNodesWithArbitraryAttributeSelectorsAsDiscreteWords()
    {
        $this->loadHtml();
        $result = $this->query->execute('li[dojoType~="bar"]');
        $this->assertEquals(2, count($result), $result->getXpathQuery());
    }

    public function testQueryShouldFindNodesWithArbitraryAttributeSelectorsAndAttributeValue()
    {
        $this->loadHtml();
        $result = $this->query->execute('li[dojoType*="bar"]');
        $this->assertEquals(2, count($result), $result->getXpathQuery());
    }

    public function testQueryXpathShouldAllowQueryingArbitraryUsingXpath()
    {
        $this->loadHtml();
        $result = $this->query->queryXpath('//li[contains(@dojotype, "bar")]');
        $this->assertEquals(2, count($result), $result->getXpathQuery());
    }

    public function testXpathPhpFunctionsShouldBeDisableByDefault()
    {
        $this->loadHtml();
        try {
            $this->query->queryXpath('//meta[php:functionString("strtolower", @http-equiv) = "content-type"]');
        } catch (\Exception $e) {
            return;
        }
        $this->assertFails('XPath PHPFunctions should be disable by default');
    }

    public function testXpathPhpFunctionsShouldBeEnableWithoutParameter()
    {
        $this->loadHtml();
        $this->query->registerXpathPhpFunctions();
        $result = $this->query->queryXpath('//meta[php:functionString("strtolower", @http-equiv) = "content-type"]');
        $this->assertEquals('content-type',
                            strtolower($result->current()->getAttribute('http-equiv')),
                            $result->getXpathQuery());
    }

    public function testXpathPhpFunctionsShouldBeNotCalledWhenSpecifiedFunction()
    {
        $this->loadHtml();
        try {
            $this->query->registerXpathPhpFunctions('stripos');
            $this->query->queryXpath('//meta[php:functionString("strtolower", @http-equiv) = "content-type"]');
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
        $this->query->setDocument($file);
        $this->query->execute('p');
        $errors = $this->query->getDocumentErrors();
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

        $this->query->setDocument($html);
        $results = $this->query->execute('input[type="hidden"][value="1"]');
        $this->assertEquals(2, count($results), $results->getXpathQuery());
        $results = $this->query->execute('input[value="1"][type~="hidden"]');
        $this->assertEquals(2, count($results), $results->getXpathQuery());
        $results = $this->query->execute('input[type="hidden"][value="0"]');
        $this->assertEquals(1, count($results));
    }

    /**
     * @group ZF-3938
     */
    public function testAllowsSpecifyingEncodingAtConstruction()
    {
        $doc = new Query($this->getHtml(), 'iso-8859-1');
        $this->assertEquals('iso-8859-1', $doc->getEncoding());
    }

    /**
     * @group ZF-3938
     */
    public function testAllowsSpecifyingEncodingWhenSettingDocument()
    {
        $this->query->setDocument($this->getHtml(), 'iso-8859-1');
        $this->assertEquals('iso-8859-1', $this->query->getEncoding());
    }

    /**
     * @group ZF-3938
     */
    public function testAllowsSpecifyingEncodingViaSetter()
    {
        $this->query->setEncoding('iso-8859-1');
        $this->assertEquals('iso-8859-1', $this->query->getEncoding());
    }

    /**
     * @group ZF-3938
     */
    public function testSpecifyingEncodingSetsEncodingOnDomDocument()
    {
        $this->query->setDocument($this->getHtml(), 'utf-8');
        $test = $this->query->execute('.foo');
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
        $this->query->setDocument($xhtmlWithXmlDecl, 'utf-8');
        $this->assertEquals(1, $this->query->execute('//p')->count());
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
        $this->query->setDocument($xhtmlWithXmlDecl, 'utf-8');
        $this->assertEquals(1, $this->query->execute('//p')->count());
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
        $this->query->setDocumentXml($xml);
        $this->setExpectedException("\Zend\Dom\Exception\RuntimeException");
        $this->query->queryXpath('/');
    }

    public function testOffsetExists()
    {
        $this->loadHtml();
        $results = $this->query->execute('input');

        $this->assertEquals(3, $results->count());
        $this->assertFalse($results->offsetExists(3));
        $this->assertTrue($results->offsetExists(2));
    }

    public function testOffsetGet()
    {
        $this->loadHtml();
        $results = $this->query->execute('input');

        $this->assertEquals(3, $results->count());
        $this->assertEquals('login', $results[2]->getAttribute('id'));
    }

    /**
     * @expectedException Zend\Dom\Exception\BadMethodCallException
     */
    public function testOffsetSet()
    {
        $this->loadHtml();
        $results = $this->query->execute('input');
        $this->assertEquals(3, $results->count());

        $results[0] = '<foobar />';
    }


    /**
     * @expectedException Zend\Dom\Exception\BadMethodCallException
     */
    public function testOffsetUnset()
    {
        $this->loadHtml();
        $results = $this->query->execute('input');
        $this->assertEquals(3, $results->count());

        unset($results[2]);
    }
}
