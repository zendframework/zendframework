<?php
/**
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage UnitTests
 */


/**
 * Zend_Search_Lucene
 */
require_once 'Zend/Search/Lucene.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage UnitTests
 */
class Zend_Search_Lucene_SearchHighlightTest extends PHPUnit_Framework_TestCase
{
    /**
     * Wildcard pattern minimum preffix
     *
     * @var integer
     */
    protected $_wildcardMinPrefix;

    /**
     * Fuzzy search default preffix length
     *
     * @var integer
     */
    protected $_defaultPrefixLength;

    public function setUp()
    {
        $this->_wildcardMinPrefix = Zend_Search_Lucene_Search_Query_Wildcard::getMinPrefixLength();
        Zend_Search_Lucene_Search_Query_Wildcard::setMinPrefixLength(0);

        $this->_defaultPrefixLength = Zend_Search_Lucene_Search_Query_Fuzzy::getDefaultPrefixLength();
        Zend_Search_Lucene_Search_Query_Fuzzy::setDefaultPrefixLength(0);
    }

    public function tearDown()
    {
        Zend_Search_Lucene_Search_Query_Wildcard::setMinPrefixLength($this->_wildcardMinPrefix);
        Zend_Search_Lucene_Search_Query_Fuzzy::setDefaultPrefixLength($this->_defaultPrefixLength);
    }


    public function testHtmlFragmentHighlightMatches()
    {
        $query = Zend_Search_Lucene_Search_QueryParser::parse('title:"The Right Way" AND text:go');

        $highlightedHtmlFragment = $query->htmlFragmentHighlightMatches('Text highlighting using Zend_Search_Lucene is the right way to go!');

        $this->assertEquals($highlightedHtmlFragment,
                            'Text highlighting using Zend_Search_Lucene is <b style="color:black;background-color:#66ffff">the</b> <b style="color:black;background-color:#66ffff">right</b> <b style="color:black;background-color:#66ffff">way</b> to <b style="color:black;background-color:#ff66ff">go</b>!');
    }

//    public function testHtmlFragmentHighlightMatchesCyrillic()
//    {
//        $query = Zend_Search_Lucene_Search_QueryParser::parse('title:"некоторый текст" AND text:поехали');
//
//        $highlightedHtmlFragment = $query->htmlFragmentHighlightMatches('Подсвечиваем некоторый текст с использованием Zend_Search_Lucene. Поехали!');
//
//        $this->assertEquals($highlightedHtmlFragment,
//                            'Text highlighting using Zend_Search_Lucene is <b style="color:black;background-color:#66ffff">the</b> <b style="color:black;background-color:#66ffff">right</b> <b style="color:black;background-color:#66ffff">way</b> to <b style="color:black;background-color:#ff66ff">go</b>!');
//    }
//
//    public function testHtmlFragmentHighlightMatchesCyrillicWindows()
//    {
//        $query = Zend_Search_Lucene_Search_QueryParser::parse('title:"Некоторый текст" AND text:поехали');
//
//        $highlightedHtmlFragment =
//                $query->htmlFragmentHighlightMatches(iconv('UTF-8',
//                                                           'Windows-1251',
//                                                           'Подсвечиваем некоторый текст с использованием Zend_Search_Lucene. Поехали!'),
//                                                     'Windows-1251');
//
//        $this->assertEquals($highlightedHtmlFragment,
//                            'Text highlighting using Zend_Search_Lucene is <b style="color:black;background-color:#66ffff">the</b> <b style="color:black;background-color:#66ffff">right</b> <b style="color:black;background-color:#66ffff">way</b> to <b style="color:black;background-color:#ff66ff">go</b>!');
//    }

    public function testHighlightPhrasePlusTerm()
    {
        $query = Zend_Search_Lucene_Search_QueryParser::parse('title:"The Right Way" AND text:go');

        $html = '<HTML>'
                . '<HEAD><TITLE>Page title</TITLE></HEAD>'
                . '<BODY>'
                .   'Text highlighting using Zend_Search_Lucene is the right way to go!'
                . '</BODY>'
              . '</HTML>';

        $highlightedHTML = $query->highlightMatches($html);

        $this->assertTrue(strpos($highlightedHTML, '<b style="color:black;background-color:#66ffff">the</b>') !== false);
        $this->assertTrue(strpos($highlightedHTML, '<b style="color:black;background-color:#66ffff">right</b>') !== false);
        $this->assertTrue(strpos($highlightedHTML, '<b style="color:black;background-color:#66ffff">way</b>') !== false);
        $this->assertTrue(strpos($highlightedHTML, '<b style="color:black;background-color:#ff66ff">go</b>') !== false);
    }

    public function testHighlightMultitermWithProhibitedTerms()
    {
        $query = Zend_Search_Lucene_Search_QueryParser::parse('+text +highlighting -using -right +go');

        $html = '<HTML>'
                . '<HEAD><TITLE>Page title</TITLE></HEAD>'
                . '<BODY>'
                .   'Text highlighting using Zend_Search_Lucene is the right way to go!'
                . '</BODY>'
              . '</HTML>';

        $highlightedHTML = $query->highlightMatches($html);

        $this->assertTrue(strpos($highlightedHTML, '<b style="color:black;background-color:#66ffff">Text</b>') !== false);
        $this->assertTrue(strpos($highlightedHTML, '<b style="color:black;background-color:#ff66ff">highlighting</b>') !== false);
        $this->assertTrue(strpos($highlightedHTML, 'using Zend_Search_Lucene is the right way to') !== false);
        $this->assertTrue(strpos($highlightedHTML, '<b style="color:black;background-color:#ffff66">go</b>') !== false);
    }

    public function testHighlightWildcard1()
    {
        $query = Zend_Search_Lucene_Search_QueryParser::parse('te?t');

        $html = '<HTML>'
                . '<HEAD><TITLE>Page title</TITLE></HEAD>'
                . '<BODY>'
                .   'Test of text highlighting using wildcard query with question mark. Testing...'
                . '</BODY>'
              . '</HTML>';

        $highlightedHTML = $query->highlightMatches($html);

        $this->assertTrue(strpos($highlightedHTML, '<b style="color:black;background-color:#66ffff">Test</b>') !== false);
        $this->assertTrue(strpos($highlightedHTML, '<b style="color:black;background-color:#66ffff">text</b>') !== false);
        // Check that 'Testing' word is not highlighted
        $this->assertTrue(strpos($highlightedHTML, 'mark. Testing...') !== false);
    }

    public function testHighlightWildcard2()
    {
        $query = Zend_Search_Lucene_Search_QueryParser::parse('te?t*');

        $html = '<HTML>'
                . '<HEAD><TITLE>Page title</TITLE></HEAD>'
                . '<BODY>'
                .   'Test of text highlighting using wildcard query with question mark. Testing...'
                . '</BODY>'
              . '</HTML>';

        $highlightedHTML = $query->highlightMatches($html);

        $this->assertTrue(strpos($highlightedHTML, '<b style="color:black;background-color:#66ffff">Test</b>') !== false);
        $this->assertTrue(strpos($highlightedHTML, '<b style="color:black;background-color:#66ffff">text</b>') !== false);
        // Check that 'Testing' word is also highlighted
        $this->assertTrue(strpos($highlightedHTML, '<b style="color:black;background-color:#66ffff">Testing</b>') !== false);
    }

    public function testHighlightFuzzy1()
    {
        $query = Zend_Search_Lucene_Search_QueryParser::parse('test~');

        $html = '<HTML>'
                . '<HEAD><TITLE>Page title</TITLE></HEAD>'
                . '<BODY>'
                .   'Test of text fuzzy search terms highlighting. '
                .   'Words: test, text, latest, left, list, next, ...'
                . '</BODY>'
              . '</HTML>';

        $highlightedHTML = $query->highlightMatches($html);

        $this->assertTrue(strpos($highlightedHTML, '<b style="color:black;background-color:#66ffff">Test</b>') !== false);
        $this->assertTrue(strpos($highlightedHTML, '<b style="color:black;background-color:#66ffff">test</b>') !== false);
        $this->assertTrue(strpos($highlightedHTML, '<b style="color:black;background-color:#66ffff">text</b>') !== false);
        // Check that other words are not highlighted
        $this->assertTrue(strpos($highlightedHTML, 'latest, left, list, next, ...') !== false);
    }

    public function testHighlightFuzzy2()
    {
        $query = Zend_Search_Lucene_Search_QueryParser::parse('test~0.4');

        $html = '<HTML>'
                . '<HEAD><TITLE>Page title</TITLE></HEAD>'
                . '<BODY>'
                .   'Test of text fuzzy search terms highlighting. '
                .   'Words: test, text, latest, left, list, next, ...'
                . '</BODY>'
              . '</HTML>';

        $highlightedHTML = $query->highlightMatches($html);

        $this->assertTrue(strpos($highlightedHTML, '<b style="color:black;background-color:#66ffff">Test</b>') !== false);
        $this->assertTrue(strpos($highlightedHTML, '<b style="color:black;background-color:#66ffff">test</b>') !== false);
        // Check that other words are also highlighted
        $this->assertTrue(strpos($highlightedHTML, '<b style="color:black;background-color:#66ffff">text</b>') !== false);
        $this->assertTrue(strpos($highlightedHTML, '<b style="color:black;background-color:#66ffff">latest</b>') !== false);
        $this->assertTrue(strpos($highlightedHTML, '<b style="color:black;background-color:#66ffff">left</b>') !== false);
        $this->assertTrue(strpos($highlightedHTML, '<b style="color:black;background-color:#66ffff">list</b>') !== false);
        $this->assertTrue(strpos($highlightedHTML, '<b style="color:black;background-color:#66ffff">next</b>') !== false);
    }

    public function testHighlightRangeInclusive()
    {
        $query = Zend_Search_Lucene_Search_QueryParser::parse('[business TO by]');

        $html = '<HTML>'
                . '<HEAD><TITLE>Page title</TITLE></HEAD>'
                . '<BODY>'
                .   'Test of text using range query. '
                .   'It has to match "business", "by", "buss" and "but" words, but has to skip "bus"'
                . '</BODY>'
              . '</HTML>';

        $highlightedHTML = $query->highlightMatches($html);

        $this->assertTrue(strpos($highlightedHTML, '<b style="color:black;background-color:#66ffff">business</b>') !== false);
        $this->assertTrue(strpos($highlightedHTML, '<b style="color:black;background-color:#66ffff">by</b>') !== false);
        $this->assertTrue(strpos($highlightedHTML, '<b style="color:black;background-color:#66ffff">buss</b>') !== false);
        $this->assertTrue(strpos($highlightedHTML, '<b style="color:black;background-color:#66ffff">but</b>') !== false);
        // Check that "bus" word is skipped
        $this->assertTrue(strpos($highlightedHTML, 'has to skip "bus"') !== false);
    }

    public function testHighlightRangeNonInclusive()
    {
        $query = Zend_Search_Lucene_Search_QueryParser::parse('{business TO by}');

        $html = '<HTML>'
                . '<HEAD><TITLE>Page title</TITLE></HEAD>'
                . '<BODY>'
                .   'Test of text using range query. '
                .   'It has to match "buss" and "but" words, but has to skip "business", "by" and "bus"'
                . '</BODY>'
              . '</HTML>';

        $highlightedHTML = $query->highlightMatches($html);

        $this->assertTrue(strpos($highlightedHTML, '<b style="color:black;background-color:#66ffff">buss</b>') !== false);
        $this->assertTrue(strpos($highlightedHTML, '<b style="color:black;background-color:#66ffff">but</b>') !== false);
        // Check that "bus" word is skipped
        $this->assertTrue(strpos($highlightedHTML, 'has to skip "business", "by" and "bus"') !== false);
    }
}
