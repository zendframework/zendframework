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
 * @package    Zend_Search_Lucene
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Zend_Search_Lucene_Document
 */
require_once 'Zend/Search/Lucene/Document.php';

/**
 * Zend_Search_Lucene_Document_Docx
 */
require_once 'Zend/Search/Lucene/Document/Docx.php';

/**
 * Zend_Search_Lucene_Document_Pptx
 */
require_once 'Zend/Search/Lucene/Document/Pptx.php';

/**
 * Zend_Search_Lucene_Document_Xlsx
 */
require_once 'Zend/Search/Lucene/Document/Xlsx.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';

/**
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Search_Lucene
 */
class Zend_Search_Lucene_DocumentTest extends PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $document =  new Zend_Search_Lucene_Document();

        $this->assertEquals($document->boost, 1);
    }

    public function testFields()
    {
        $document =  new Zend_Search_Lucene_Document();

        $document->addField(Zend_Search_Lucene_Field::Text('title',      'Title'));
        $document->addField(Zend_Search_Lucene_Field::Text('annotation', 'Annotation'));
        $document->addField(Zend_Search_Lucene_Field::Text('body',       'Document body, document body, document body...'));

        $fieldnamesDiffArray = array_diff($document->getFieldNames(), array('title', 'annotation', 'body'));
        $this->assertTrue(is_array($fieldnamesDiffArray));
        $this->assertEquals(count($fieldnamesDiffArray), 0);

        $this->assertEquals($document->title,      'Title');
        $this->assertEquals($document->annotation, 'Annotation');
        $this->assertEquals($document->body,       'Document body, document body, document body...');

        $this->assertEquals($document->getField('title')->value,      'Title');
        $this->assertEquals($document->getField('annotation')->value, 'Annotation');
        $this->assertEquals($document->getField('body')->value,       'Document body, document body, document body...');

        $this->assertEquals($document->getFieldValue('title'),      'Title');
        $this->assertEquals($document->getFieldValue('annotation'), 'Annotation');
        $this->assertEquals($document->getFieldValue('body'),       'Document body, document body, document body...');


        if (PHP_OS == 'AIX') {
            return; // tests below here not valid on AIX
        }

        $wordsWithUmlautsIso88591 = iconv('UTF-8', 'ISO-8859-1', 'Words with umlauts: åãü...');
        $document->addField(Zend_Search_Lucene_Field::Text('description', $wordsWithUmlautsIso88591, 'ISO-8859-1'));
        $this->assertEquals($document->description, $wordsWithUmlautsIso88591);
        $this->assertEquals($document->getFieldUtf8Value('description'), 'Words with umlauts: åãü...');
    }

    public function testAddFieldMethodChaining()
    {
        $document =  new Zend_Search_Lucene_Document();
        $this->assertTrue($document->addField(Zend_Search_Lucene_Field::Text('title', 'Title')) instanceof Zend_Search_Lucene_Document);

        $document =  new Zend_Search_Lucene_Document();
        $document->addField(Zend_Search_Lucene_Field::Text('title',      'Title'))
                 ->addField(Zend_Search_Lucene_Field::Text('annotation', 'Annotation'))
                 ->addField(Zend_Search_Lucene_Field::Text('body',       'Document body, document body, document body...'));
    }


    public function testHtmlHighlighting()
    {
        $doc = Zend_Search_Lucene_Document_Html::loadHTML('<HTML><HEAD><TITLE>Page title</TITLE></HEAD><BODY>Document body.</BODY></HTML>');
        $this->assertTrue($doc instanceof Zend_Search_Lucene_Document_Html);

        $doc->highlight('document', '#66ffff');
        $this->assertTrue(strpos($doc->getHTML(), '<b style="color:black;background-color:#66ffff">Document</b> body.') !== false);
    }

    public function testHtmlExtendedHighlighting()
    {
        $doc = Zend_Search_Lucene_Document_Html::loadHTML('<HTML><HEAD><TITLE>Page title</TITLE></HEAD><BODY>Document body.</BODY></HTML>');
        $this->assertTrue($doc instanceof Zend_Search_Lucene_Document_Html);

        $doc->highlightExtended('document',
                                array('Zend_Search_Lucene_DocumentTest_DocHighlightingContainer',
                                      'extendedHighlightingCallback'),
                                array('style="color:black;background-color:#ff66ff"',
                                      '(!!!)'));
        $this->assertTrue(strpos($doc->getHTML(), '<b style="color:black;background-color:#ff66ff">Document</b>(!!!) body.') !== false);
    }

    public function testHtmlWordsHighlighting()
    {
        $doc = Zend_Search_Lucene_Document_Html::loadHTML('<HTML><HEAD><TITLE>Page title</TITLE></HEAD><BODY>Document body.</BODY></HTML>');
        $this->assertTrue($doc instanceof Zend_Search_Lucene_Document_Html);

        $doc->highlight(array('document', 'body'), '#66ffff');
        $highlightedHTML = $doc->getHTML();
        $this->assertTrue(strpos($highlightedHTML, '<b style="color:black;background-color:#66ffff">Document</b>') !== false);
        $this->assertTrue(strpos($highlightedHTML, '<b style="color:black;background-color:#66ffff">body</b>')     !== false);
    }

    public function testHtmlExtendedHighlightingCorrectWrongHtml()
    {
        $doc = Zend_Search_Lucene_Document_Html::loadHTML('<HTML><HEAD><TITLE>Page title</TITLE></HEAD><BODY>Document body.</BODY></HTML>');
        $this->assertTrue($doc instanceof Zend_Search_Lucene_Document_Html);

        $doc->highlightExtended('document',
                                array('Zend_Search_Lucene_DocumentTest_DocHighlightingContainer',
                                      'extendedHighlightingCallback'),
                                array('style="color:black;background-color:#ff66ff"',
                                      '<h3>(!!!)' /* Wrong HTML here, <h3> tag is not closed */));
        $this->assertTrue(strpos($doc->getHTML(), '<b style="color:black;background-color:#ff66ff">Document</b><h3>(!!!)</h3> body.') !== false);
    }

    public function testHtmlLinksProcessing()
    {
        $doc =  Zend_Search_Lucene_Document_Html::loadHTMLFile(dirname(__FILE__) . '/_indexSource/_files/contributing.documentation.html', true);
        $this->assertTrue($doc instanceof Zend_Search_Lucene_Document_Html);

        $this->assertTrue(array_values($doc->getHeaderLinks()) ==
                          array('index.html', 'contributing.html', 'contributing.bugs.html', 'contributing.wishlist.html'));
        $this->assertTrue(array_values($doc->getLinks()) ==
                          array('contributing.bugs.html',
                                'contributing.wishlist.html',
                                'developers.documentation.html',
                                'faq.translators-revision-tracking.html',
                                'index.html',
                                'contributing.html'));
    }

    public function testHtmlNoFollowLinks()
    {
        $html = '<HTML>'
                . '<HEAD><TITLE>Page title</TITLE></HEAD>'
                . '<BODY>'
                .   'Document body.'
                .   '<a href="link1.html">Link 1</a>.'
                .   '<a href="link2.html" rel="nofollow">Link 1</a>.'
                . '</BODY>'
              . '</HTML>';

        $oldNoFollowValue = Zend_Search_Lucene_Document_Html::getExcludeNoFollowLinks();

        Zend_Search_Lucene_Document_Html::setExcludeNoFollowLinks(false);
        $doc1 = Zend_Search_Lucene_Document_Html::loadHTML($html);
        $this->assertTrue($doc1 instanceof Zend_Search_Lucene_Document_Html);
        $this->assertTrue(array_values($doc1->getLinks()) == array('link1.html', 'link2.html'));

        Zend_Search_Lucene_Document_Html::setExcludeNoFollowLinks(true);
        $doc2 = Zend_Search_Lucene_Document_Html::loadHTML($html);
        $this->assertTrue($doc2 instanceof Zend_Search_Lucene_Document_Html);
        $this->assertTrue(array_values($doc2->getLinks()) == array('link1.html'));
    }

    public function testDocx()
    {
        if (!class_exists('ZipArchive')) {
            $this->markTestSkipped('ZipArchive class (Zip extension) is not loaded');
        }

        $docxDocument = Zend_Search_Lucene_Document_Docx::loadDocxFile(dirname(__FILE__) . '/_openXmlDocuments/test.docx', true);

        $this->assertTrue($docxDocument instanceof Zend_Search_Lucene_Document_Docx);
        $this->assertEquals($docxDocument->getFieldValue('title'), 'Test document');
        $this->assertEquals($docxDocument->getFieldValue('description'), 'This is a test document which can be used to demonstrate something.');
        $this->assertTrue($docxDocument->getFieldValue('body') != '');

        try {
            $docxDocument1 = Zend_Search_Lucene_Document_Docx::loadDocxFile(dirname(__FILE__) . '/_openXmlDocuments/dummy.docx', true);

            $this->fail('File not readable exception is expected.');
        } catch (Zend_Search_Lucene_Document_Exception $e) {
            if (strpos($e->getMessage(), 'is not readable') === false) {
                // Passthrough exception
                throw $e;
            }
        }
    }

    public function testPptx()
    {
        if (!class_exists('ZipArchive')) {
            $this->markTestSkipped('ZipArchive class (Zip extension) is not loaded');
        }

        $pptxDocument = Zend_Search_Lucene_Document_Pptx::loadPptxFile(dirname(__FILE__) . '/_openXmlDocuments/test.pptx', true);

        $this->assertTrue($pptxDocument instanceof Zend_Search_Lucene_Document_Pptx);
        $this->assertEquals($pptxDocument->getFieldValue('title'), 'Test document');
        $this->assertEquals($pptxDocument->getFieldValue('description'), 'This is a test document which can be used to demonstrate something.');
        $this->assertTrue($pptxDocument->getFieldValue('body') != '');
    }

    public function testXlsx()
    {
        if (!class_exists('ZipArchive')) {
            $this->markTestSkipped('ZipArchive class (Zip extension) is not loaded');
        }

        $xlsxDocument = Zend_Search_Lucene_Document_Xlsx::loadXlsxFile(dirname(__FILE__) . '/_openXmlDocuments/test.xlsx', true);

        $this->assertTrue($xlsxDocument instanceof Zend_Search_Lucene_Document_Xlsx);
        $this->assertEquals($xlsxDocument->getFieldValue('title'), 'Test document');
        $this->assertEquals($xlsxDocument->getFieldValue('description'), 'This is a test document which can be used to demonstrate something.');
        $this->assertTrue($xlsxDocument->getFieldValue('body') != '');
        $this->assertTrue( strpos($xlsxDocument->getFieldValue('body'), 'ipsum') !== false );
    }
}


class Zend_Search_Lucene_DocumentTest_DocHighlightingContainer {
    public static function extendedHighlightingCallback($stringToHighlight, $param1, $param2)
    {
        return '<b ' . $param1 . '>' . $stringToHighlight . '</b>' . $param2;
    }
}
