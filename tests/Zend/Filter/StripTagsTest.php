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
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

// Call Zend_Filter_StripTagsTest::main() if this source file is executed directly.
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Filter_StripTagsTest::main');
}

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../TestHelper.php';

/**
 * @see Zend_Filter_StripTags
 */
require_once 'Zend/Filter/StripTags.php';


/**
 * @category   Zend
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Filter
 */
class Zend_Filter_StripTagsTest extends PHPUnit_Framework_TestCase
{
    /**
     * Zend_Filter_StripTags object
     *
     * @var Zend_Filter_StripTags
     */
    protected $_filter;

    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite(__CLASS__);
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Creates a new Zend_Filter_StripTags object for each test method
     *
     * @return void
     */
    public function setUp()
    {
        $this->_filter = new Zend_Filter_StripTags();
    }

    /**
     * Ensures that getTagsAllowed() returns expected default value
     *
     * @return void
     */
    public function testGetTagsAllowed()
    {
        $this->assertEquals(array(), $this->_filter->getTagsAllowed());
    }

    /**
     * Ensures that setTagsAllowed() follows expected behavior when provided a single tag
     *
     * @return void
     */
    public function testSetTagsAllowedString()
    {
        $this->_filter->setTagsAllowed('b');
        $this->assertEquals(array('b' => array()), $this->_filter->getTagsAllowed());
    }

    /**
     * Ensures that setTagsAllowed() follows expected behavior when provided an array of tags
     *
     * @return void
     */
    public function testSetTagsAllowedArray()
    {
        $tagsAllowed = array(
            'b',
            'a'   => 'href',
            'div' => array('id', 'class')
            );
        $this->_filter->setTagsAllowed($tagsAllowed);
        $tagsAllowedExpected = array(
            'b'   => array(),
            'a'   => array('href' => null),
            'div' => array('id' => null, 'class' => null)
            );
        $this->assertEquals($tagsAllowedExpected, $this->_filter->getTagsAllowed());
    }

    /**
     * Ensures that getAttributesAllowed() returns expected default value
     *
     * @return void
     */
    public function testGetAttributesAllowed()
    {
        $this->assertEquals(array(), $this->_filter->getAttributesAllowed());
    }

    /**
     * Ensures that setAttributesAllowed() follows expected behavior when provided a single attribute
     *
     * @return void
     */
    public function testSetAttributesAllowedString()
    {
        $this->_filter->setAttributesAllowed('class');
        $this->assertEquals(array('class' => null), $this->_filter->getAttributesAllowed());
    }

    /**
     * Ensures that setAttributesAllowed() follows expected behavior when provided an array of attributes
     *
     * @return void
     */
    public function testSetAttributesAllowedArray()
    {
        $attributesAllowed = array(
            'clAss',
            4    => 'inT',
            'ok' => 'String',
            null
            );
        $this->_filter->setAttributesAllowed($attributesAllowed);
        $attributesAllowedExpected = array(
            'class'  => null,
            'int'    => null,
            'string' => null
            );
        $this->assertEquals($attributesAllowedExpected, $this->_filter->getAttributesAllowed());
    }

    /**
     * Ensures that a single unclosed tag is stripped in its entirety
     *
     * @return void
     */
    public function testFilterTagUnclosed1()
    {
        $input    = '<a href="http://example.com" Some Text';
        $expected = '';
        $this->assertEquals($expected, $this->_filter->filter($input));
    }

    /**
     * Ensures that a single tag is stripped
     *
     * @return void
     */
    public function testFilterTag1()
    {
        $input    = '<a href="example.com">foo</a>';
        $expected = 'foo';
        $this->assertEquals($expected, $this->_filter->filter($input));
    }

    /**
     * Ensures that singly nested tags are stripped
     *
     * @return void
     */
    public function testFilterTagNest1()
    {
        $input    = '<a href="example.com"><b>foo</b></a>';
        $expected = 'foo';
        $this->assertEquals($expected, $this->_filter->filter($input));
    }

    /**
     * Ensures that two successive tags are stripped
     *
     * @return void
     */
    public function testFilterTag2()
    {
        $input    = '<a href="example.com">foo</a><b>bar</b>';
        $expected = 'foobar';
        $this->assertEquals($expected, $this->_filter->filter($input));
    }

    /**
     * Ensures that an allowed tag is returned as lowercase and with backward-compatible XHTML ending, where supplied
     *
     * @return void
     */
    public function testFilterTagAllowedBackwardCompatible()
    {
        $input    = '<BR><Br><bR><br/><br  /><br / ></br></bR>';
        $expected = '<br><br><br><br /><br /><br></br></br>';
        $this->_filter->setTagsAllowed('br');
        $this->assertEquals($expected, $this->_filter->filter($input));
    }

    /**
     * Ensures that any greater-than symbols '>' are removed from text preceding a tag
     *
     * @return void
     */
    public function testFilterTagPrefixGt()
    {
        $input    = '2 > 1 === true<br/>';
        $expected = '2  1 === true';
        $this->assertEquals($expected, $this->_filter->filter($input));
    }

    /**
     * Ensures that any greater-than symbols '>' are removed from text having no tags
     *
     * @return void
     */
    public function testFilterGt()
    {
        $input    = '2 > 1 === true ==> $object->property';
        $expected = '2  1 === true == $object-property';
        $this->assertEquals($expected, $this->_filter->filter($input));
    }

    /**
     * Ensures that any greater-than symbols '>' are removed from text wrapping a tag
     *
     * @return void
     */
    public function testFilterTagWrappedGt()
    {
        $input    = '2 > 1 === true <==> $object->property';
        $expected = '2  1 === true  $object-property';
        $this->assertEquals($expected, $this->_filter->filter($input));
    }

    /**
     * Ensures that an attribute for an allowed tag is stripped
     *
     * @return void
     */
    public function testFilterTagAllowedAttribute()
    {
        $tagsAllowed = 'img';
        $this->_filter->setTagsAllowed($tagsAllowed);
        $input    = '<IMG alt="foo" />';
        $expected = '<img />';
        $this->assertEquals($expected, $this->_filter->filter($input));
    }

    /**
     * Ensures that an allowed tag with an allowed attribute is filtered as expected
     *
     * @return void
     */
    public function testFilterTagAllowedAttributeAllowed()
    {
        $tagsAllowed = array(
            'img' => 'alt'
            );
        $this->_filter->setTagsAllowed($tagsAllowed);
        $input    = '<IMG ALT="FOO" />';
        $expected = '<img alt="FOO" />';
        $this->assertEquals($expected, $this->_filter->filter($input));
    }

    /**
     * Ensures expected behavior when a greater-than symbol '>' appears in an allowed attribute's value
     *
     * Currently this is not unsupported; these symbols should be escaped when used in an attribute value.
     *
     * @return void
     */
    public function testFilterTagAllowedAttributeAllowedGt()
    {
        $tagsAllowed = array(
            'img' => 'alt'
            );
        $this->_filter->setTagsAllowed($tagsAllowed);
        $input    = '<img alt="$object->property" />';
        $expected = '<img>property" /';
        $this->assertEquals($expected, $this->_filter->filter($input));
    }

    /**
     * Ensures expected behavior when an escaped greater-than symbol '>' appears in an allowed attribute's value
     *
     * @return void
     */
    public function testFilterTagAllowedAttributeAllowedGtEscaped()
    {
        $tagsAllowed = array(
            'img' => 'alt'
            );
        $this->_filter->setTagsAllowed($tagsAllowed);
        $input    = '<img alt="$object-&gt;property" />';
        $expected = '<img alt="$object-&gt;property" />';
        $this->assertEquals($expected, $this->_filter->filter($input));
    }

    /**
     * Ensures that an unterminated attribute value does not affect other attributes but causes the corresponding
     * attribute to be removed in its entirety.
     *
     * @return void
     */
    public function testFilterTagAllowedAttributeAllowedValueUnclosed()
    {
        $tagsAllowed = array(
            'img' => array('alt', 'height', 'src', 'width')
            );
        $this->_filter->setTagsAllowed($tagsAllowed);
        $input    = '<img src="image.png" alt="square height="100" width="100" />';
        $expected = '<img src="image.png" alt="square height=" width="100" />';
        $this->assertEquals($expected, $this->_filter->filter($input));
    }

    /**
     * Ensures that an allowed attribute having no value is removed (XHTML disallows attributes with no values)
     *
     * @return void
     */
    public function testFilterTagAllowedAttributeAllowedValueMissing()
    {
        $tagsAllowed = array(
            'input' => array('checked', 'name', 'type')
            );
        $this->_filter->setTagsAllowed($tagsAllowed);
        $input    = '<input name="foo" type="checkbox" checked />';
        $expected = '<input name="foo" type="checkbox" />';
        $this->assertEquals($expected, $this->_filter->filter($input));
    }

    /**
     * Ensures that the filter works properly for the data reported on fw-general on 2007-05-26
     *
     * @see    http://www.nabble.com/question-about-tag-filter-p10813688s16154.html
     * @return void
     */
    public function testFilter20070526()
    {
        $tagsAllowed = array(
            'object' => array('width', 'height'),
            'param'  => array('name', 'value'),
            'embed'  => array('src', 'type', 'wmode', 'width', 'height'),
            );
        $this->_filter->setTagsAllowed($tagsAllowed);
        $input = '<object width="425" height="350"><param name="movie" value="http://www.example.com/path/to/movie">'
               . '</param><param name="wmode" value="transparent"></param><embed '
               . 'src="http://www.example.com/path/to/movie" type="application/x-shockwave-flash" '
               . 'wmode="transparent" width="425" height="350"></embed></object>';
        $expected = '<object width="425" height="350"><param name="movie" value="http://www.example.com/path/to/movie">'
               . '</param><param name="wmode" value="transparent"></param><embed '
               . 'src="http://www.example.com/path/to/movie" type="application/x-shockwave-flash" '
               . 'wmode="transparent" width="425" height="350"></embed></object>';
        $this->assertEquals($expected, $this->_filter->filter($input));
    }

    /**
     * Ensures that a comment is stripped
     *
     * @return void
     */
    public function testFilterComment()
    {
        $input    = '<!-- a comment -->';
        $expected = '';
        $this->assertEquals($expected, $this->_filter->filter($input));
    }

    /**
     * Ensures that a comment wrapped with other strings is stripped
     *
     * @return void
     */
    public function testFilterCommentWrapped()
    {
        $input    = 'foo<!-- a comment -->bar';
        $expected = 'foobar';
        $this->assertEquals($expected, $this->_filter->filter($input));
    }

    /**
     * Ensures that a comment is not removed when comments are allowed
     *
     * @return void
     */
    public function testFilterCommentAllowed()
    {
        $input    = '<!-- a comment -->';
        $expected = '<!-- a comment -->';
        $this->_filter->setCommentsAllowed(true);
        $this->assertEquals($expected, $this->_filter->filter($input));
    }

    /**
     * Ensures that a comment containing tags is untouched when comments are allowed
     *
     * @return void
     */
    public function testFilterCommentAllowedContainsTags()
    {
        $input    = '<!-- a comment <br /> <h1>SuperLarge</h1> -->';
        $expected = '<!-- a comment <br /> <h1>SuperLarge</h1> -->';
        $this->_filter->setCommentsAllowed(true);
        $this->assertEquals($expected, $this->_filter->filter($input));
    }

    /**
     * Ensures expected behavior when comments are allowed and a comment contains tags and linebreaks
     *
     * @return void
     */
    public function testFilterCommentsAllowedContainsTagsLinebreaks()
    {
        $input    = "<br> test <p> text </p> with <!-- comments --> and <!-- hidd\n\nen <br> -->";
        $expected = " test  text  with <!-- comments --> and <!-- hidd\n\nen <br> -->";
        $this->_filter->setCommentsAllowed(true);
        $this->assertEquals($expected, $this->_filter->filter($input));
    }

    /**
     * Ensures expected behavior when comments are allowed but nested
     *
     * @return void
     */
    public function testFilterCommentsAllowedNested()
    {
        $input    = '<a> <!-- <b> <!-- <c> --> <d> --> <e>';
        $expected = ' <!-- <b> <!-- <c> -->  -- ';
        $this->_filter->setCommentsAllowed(true);
        $this->assertEquals($expected, $this->_filter->filter($input));
    }

    /**
     * Ensures that space is allowed between the double-hyphen '--' and the ending delimiter '>'
     *
     * @see    http://www.w3.org/TR/1999/REC-html401-19991224/intro/sgmltut.html#h-3.2.4
     * @return void
     */
    public function testFilterCommentsAllowedDelimiterEndingWhiteSpace()
    {
        $input    = '<a> <!-- <b> --  > <c>';
        $expected = ' <!-- <b> --  > ';
        $this->_filter->setCommentsAllowed(true);
        $this->assertEquals($expected, $this->_filter->filter($input));
    }

    /**
     * Ensures that a closing angle bracket in an allowed attribute does not break the parser
     *
     * @return void
     * @link   http://framework.zend.com/issues/browse/ZF-3278
     */
    public function testClosingAngleBracketInAllowedAttributeValue()
    {
        $tagsAllowed = array(
            'a' => 'href'
            );
        $this->_filter->setTagsAllowed($tagsAllowed);
        $input    = '<a href="Some &gt; Text">';
        $expected = '<a href="Some &gt; Text">';
        $this->assertEquals($expected, $this->_filter->filter($input));
    }

    /**
     * Ensures that an allowed attribute's value may end with an equals sign '='
     *
     * @group ZF-3293
     * @group ZF-5983
     */
    public function testAllowedAttributeValueMayEndWithEquals()
    {
        $tagsAllowed = array(
            'element' => 'attribute'
        );
        $this->_filter->setTagsAllowed($tagsAllowed);
        $input = '<element attribute="a=">contents</element>';
        $this->assertEquals($input, $this->_filter->filter($input));
    }

    /**
     * @group ZF-5983
     */
    public function testDisallowedAttributesSplitOverMultipleLinesShouldBeStripped()
    {
        $tagsAllowed = array('a' => 'href');
        $this->_filter->setTagsAllowed($tagsAllowed);
        $input = '<a href="http://framework.zend.com/issues" onclick
=
    "alert(&quot;Gotcha&quot;); return false;">http://framework.zend.com/issues</a>';
        $filtered = $this->_filter->filter($input);
        $this->assertNotContains('onclick', $filtered);
    }
}

// Call Zend_Filter_StripTagsTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD === 'Zend_Filter_StripTagsTest::main') {
    Zend_Filter_StripTagsTest::main();
}
