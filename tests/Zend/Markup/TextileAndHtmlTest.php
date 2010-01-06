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
 * @package    Zend_Markup
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

require_once dirname(dirname(dirname(__FILE__))) . '/TestHelper.php';

if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Markup_TextileAndHtmlTest::main");
}

require_once 'Zend/Markup.php';

/**
 * Test class for Zend_Markup_Renderer_Html and Zend_Markup_Parser_Textile
 *
 * @category   Zend
 * @package    Zend_Markup
 * @subpackage UnitTests
 * @group      Zend_Markup
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Markup_TextileAndHtmlTest extends PHPUnit_Framework_TestCase
{

    /**
     * Zend_Markup_Renderer_RendererAbstract instance
     *
     * @var Zend_Markup_Renderer_RendererAbstract
     */
    protected $_markup;


    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("Zend_Markup_MarkupTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        $this->_markup = Zend_Markup::factory('Textile', 'html');
    }

    /**
     * Tears down the fixture
     * This method is called after a test is executed.
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->_markup);
    }


    public function testHtmlTags()
    {
    	$m = $this->_markup;

        $this->assertEquals('<p><strong>foo</strong></p>', $m->render('*foo*'));
        $this->assertEquals('<p><strong>foo</strong></p>', $m->render('**foo**'));
        $this->assertEquals('<p><em>foo</em></p>', $m->render('_foo_'));
        $this->assertEquals('<p><em>foo</em></p>', $m->render('__foo__'));
        $this->assertEquals('<p><cite>foo</cite></p>', $m->render('??foo??'));
        $this->assertEquals('<p><del>foo</del></p>', $m->render('-foo-'));
        $this->assertEquals('<p><ins>foo</ins></p>', $m->render('+foo+'));
        $this->assertEquals('<p><sup>foo</sup></p>', $m->render('^foo^'));
        $this->assertEquals('<p><sub>foo</sub></p>', $m->render('~foo~'));
        $this->assertEquals('<p><span>foo</span></p>', $m->render('%foo%'));
        $this->assertEquals('<p><acronym title="Teh Zend Framework">TZF</acronym></p>',
                            $m->render('TZF(Teh Zend Framework)'));
        $this->assertEquals('<p><a href="http://framework.zend.com/">Zend Framework</a></p>',
                            $m->render('"Zend Framework":http://framework.zend.com/'));
        $this->assertEquals('<p><h1>foobar</h1></p>',
                            $m->render('h1. foobar'));
        $this->assertEquals('<p><img src="http://framework.zend.com/images/logo.gif" alt="logo" /></p>',
                            $m->render('!http://framework.zend.com/images/logo.gif!'));

        $value    = "# Zend Framework\n# Unit Tests";
        $expected = '<p><ol style="list-style-type: decimal"><li>Zend Framework</li><li>Unit Tests</li></ol></p>';
        $this->assertEquals($expected, $m->render($value));

        $value    = "* Zend Framework\n* Foo Bar";
        $expected = '<p><ul><li>Zend Framework</li><li>Foo Bar</li></ul></p>';
        $this->assertEquals($expected, $m->render($value));
    }

    public function testSimpleAttributes()
    {
        $m = $this->_markup;

        $this->assertEquals('<p><strong class="zend">foo</strong></p>', $m->render('*(zend)foo*'));
        $this->assertEquals('<p><strong id="zend">foo</strong></p>', $m->render('*(#zend)foo*'));
        $this->assertEquals('<p><strong id="framework" class="zend">foo</strong></p>',
                            $m->render('*(zend#framework)foo*'));

        $this->assertEquals('<p><strong style="color:green;">foo</strong></p>', $m->render('*{color:green;}foo*'));
        $this->assertEquals('<p><strong lang="en">foo</strong></p>', $m->render('*[en]foo*'));
    }

    public function testBlockAttributes()
    {
        $m = $this->_markup;

        $this->assertEquals('<p class="zend">foo</p>', $m->render('p(zend). foo'));
        $this->assertEquals('<p id="zend">foo</p>', $m->render('p(#zend). foo'));
        $this->assertEquals('<p id="framework" class="zend">foo</p>', $m->render('p(zend#framework). foo'));

        $this->assertEquals('<p style="color:green;">foo</p>', $m->render('p{color:green;}. foo'));
        $this->assertEquals('<p lang="en">foo</p>', $m->render('p[en]. foo'));

        $this->assertEquals('<p style="text-align: right;">foo</p>', $m->render('p>. foo'));
        $this->assertEquals('<p style="text-align: left;">foo</p>', $m->render('p<. foo'));
        $this->assertEquals('<p style="text-align: justify;">foo</p>', $m->render('p<>. foo'));
        $this->assertEquals('<p style="text-align: center;">foo</p>', $m->render('p=. foo'));
    }

    public function testNewlines()
    {
        $this->assertEquals("<p>foo</p><p>bar<br />\nbaz</p>", $this->_markup->render("foo\n\nbar\nbaz"));
        $this->assertEquals("<p>foo</p><p style=\"color:green;\">bar<br />\nbaz</p>",
                            $this->_markup->render("foo\n\np{color:green}. bar\nbaz"));
        $this->assertEquals("<p>foo</p><p>pahbarbaz</p>",
                            $this->_markup->render("foo\n\npahbarbaz"));
    }

    public function testAttributeNotEndingDoesNotThrowNotice()
    {
        $m = $this->_markup;

        $this->assertEquals("<p><strong>[</strong></p>", $m->render('*['));
        $this->assertEquals("<p><strong>{</strong></p>", $m->render('*{'));
        $this->assertEquals("<p><strong>(</strong></p>", $m->render('*('));
    }

    public function testTagOnEofDoesNotThrowNotice()
    {
        $m = $this->_markup;
        $this->assertEquals("<p></p>", $m->render('!'));
        $this->assertEquals("<p>*</p>", $m->render('*'));
    }

    public function testAcronymOnEofDoesNotThrowNotice()
    {
        $this->assertEquals('<p>ZFC(</p>', $this->_markup->render('ZFC('));
    }


}

// Call Zend_Markup_BbcodeTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Markup_TextileAndHtmlTest::main") {
    Zend_Markup_TextileAndHtmlTest::main();
}
