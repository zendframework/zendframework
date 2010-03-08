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
 * @package    Zend_Tag
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Tag_Cloud_Decorator_HtmlTagTest::main');
}

/**
 * Test helper
 */


/**
 * @category   Zend
 * @package    Zend_Tag
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Tag
 * @group      Zend_Tag_Cloud
 */
class Zend_Tag_Cloud_Decorator_HtmlTagTest extends PHPUnit_Framework_TestCase
{
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite(__CLASS__);
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function testDefaultOutput()
    {
        $decorator = new Zend_Tag_Cloud_Decorator_HtmlTag();
        $expected  = array('<li><a href="http://first" style="font-size: 10px;">foo</a></li>',
                           '<li><a href="http://second" style="font-size: 13px;">bar</a></li>',
                           '<li><a href="http://third" style="font-size: 20px;">baz</a></li>');

        $this->assertEquals($decorator->render($this->_getTagList()), $expected);
    }

    public function testNestedTags()
    {
        $decorator = new Zend_Tag_Cloud_Decorator_HtmlTag();
        $decorator->setHtmlTags(array('span' => array('class' => 'tag'), 'li'));
        $expected  = array('<li><span class="tag"><a href="http://first" style="font-size: 10px;">foo</a></span></li>',
                           '<li><span class="tag"><a href="http://second" style="font-size: 13px;">bar</a></span></li>',
                           '<li><span class="tag"><a href="http://third" style="font-size: 20px;">baz</a></span></li>');

        $this->assertEquals($decorator->render($this->_getTagList()), $expected);
    }

    public function testFontSizeSpread()
    {
        $decorator = new Zend_Tag_Cloud_Decorator_HtmlTag();
        $decorator->setFontSizeUnit('pt')
                  ->setMinFontSize(5)
                  ->setMaxFontSize(50);

        $expected  = array('<li><a href="http://first" style="font-size: 5pt;">foo</a></li>',
                           '<li><a href="http://second" style="font-size: 15pt;">bar</a></li>',
                           '<li><a href="http://third" style="font-size: 50pt;">baz</a></li>');

        $this->assertEquals($decorator->render($this->_getTagList()), $expected);
    }

    public function testClassListSpread()
    {
        $decorator = new Zend_Tag_Cloud_Decorator_HtmlTag();
        $decorator->setClassList(array('small', 'medium', 'large'));

        $expected  = array('<li><a href="http://first" class="small">foo</a></li>',
                           '<li><a href="http://second" class="medium">bar</a></li>',
                           '<li><a href="http://third" class="large">baz</a></li>');

        $this->assertEquals($decorator->render($this->_getTagList()), $expected);
    }

    public function testEmptyClassList()
    {
        $decorator = new Zend_Tag_Cloud_Decorator_HtmlTag();

        try {
            $decorator->setClassList(array());
            $this->fail('An expected Zend_Tag_Cloud_Decorator_Exception was not raised');
        } catch (Zend_Tag_Cloud_Decorator_Exception $e) {
            $this->assertEquals($e->getMessage(), 'Classlist is empty');
        }
    }

    public function testInvalidClassList()
    {
        $decorator = new Zend_Tag_Cloud_Decorator_HtmlTag();

        try {
            $decorator->setClassList(array(array()));
            $this->fail('An expected Zend_Tag_Cloud_Decorator_Exception was not raised');
        } catch (Zend_Tag_Cloud_Decorator_Exception $e) {
            $this->assertEquals($e->getMessage(), 'Classlist contains an invalid classname');
        }
    }

    public function testInvalidFontSizeUnit()
    {
        $decorator = new Zend_Tag_Cloud_Decorator_HtmlTag();

        try {
            $decorator->setFontSizeUnit('foo');
            $this->fail('An expected Zend_Tag_Cloud_Decorator_Exception was not raised');
        } catch (Zend_Tag_Cloud_Decorator_Exception $e) {
            $this->assertEquals($e->getMessage(), 'Invalid fontsize unit specified');
        }
    }

    public function testInvalidMinFontSize()
    {
        $decorator = new Zend_Tag_Cloud_Decorator_HtmlTag();

        try {
            $decorator->setMinFontSize('foo');
            $this->fail('An expected Zend_Tag_Cloud_Decorator_Exception was not raised');
        } catch (Zend_Tag_Cloud_Decorator_Exception $e) {
            $this->assertEquals($e->getMessage(), 'Fontsize must be numeric');
        }
    }

    public function testInvalidMaxFontSize()
    {
        $decorator = new Zend_Tag_Cloud_Decorator_HtmlTag();

        try {
            $decorator->setMaxFontSize('foo');
            $this->fail('An expected Zend_Tag_Cloud_Decorator_Exception was not raised');
        } catch (Zend_Tag_Cloud_Decorator_Exception $e) {
            $this->assertEquals($e->getMessage(), 'Fontsize must be numeric');
        }
    }

    public function testConstructorWithArray()
    {
        $decorator = new Zend_Tag_Cloud_Decorator_HtmlTag(array('minFontSize' => 5, 'maxFontSize' => 10, 'fontSizeUnit' => 'pt'));

        $this->assertEquals(5, $decorator->getMinFontSize());
        $this->assertEquals(10, $decorator->getMaxFontSize());
        $this->assertEquals('pt', $decorator->getFontSizeUnit());
    }

    public function testConstructorWithConfig()
    {
        $decorator = new Zend_Tag_Cloud_Decorator_HtmlTag(new Zend_Config(array('minFontSize' => 5, 'maxFontSize' => 10, 'fontSizeUnit' => 'pt')));

        $this->assertEquals(5, $decorator->getMinFontSize());
        $this->assertEquals(10, $decorator->getMaxFontSize());
        $this->assertEquals('pt', $decorator->getFontSizeUnit());
    }

    public function testSetOptions()
    {
        $decorator = new Zend_Tag_Cloud_Decorator_HtmlTag();
        $decorator->setOptions(array('minFontSize' => 5, 'maxFontSize' => 10, 'fontSizeUnit' => 'pt'));

        $this->assertEquals(5, $decorator->getMinFontSize());
        $this->assertEquals(10, $decorator->getMaxFontSize());
        $this->assertEquals('pt', $decorator->getFontSizeUnit());
    }

    public function testSkipOptions()
    {
        $decorator = new Zend_Tag_Cloud_Decorator_HtmlTag(array('options' => 'foobar'));
        // In case would fail due to an error
    }

    protected function _getTagList()
    {
        $list   = new Zend_Tag_ItemList();
        $list[] = new Zend_Tag_Item(array('title' => 'foo', 'weight' => 1, 'params' => array('url' => 'http://first')));
        $list[] = new Zend_Tag_Item(array('title' => 'bar', 'weight' => 3, 'params' => array('url' => 'http://second')));
        $list[] = new Zend_Tag_Item(array('title' => 'baz', 'weight' => 10, 'params' => array('url' => 'http://third')));

        return $list;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Tag_Cloud_Decorator_HtmlTagTest::main') {
    Zend_Tag_Cloud_Decorator_HtmlTagTest::main();
}
