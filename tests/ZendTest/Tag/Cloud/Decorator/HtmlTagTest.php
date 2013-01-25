<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Tag
 */

namespace ZendTest\Tag\Cloud\Decorator;

use	Zend\Tag;
use Zend\Tag\Cloud\Decorator;
use Zend\Tag\Cloud\Decorator\Exception\InvalidArgumentException;

/**
 * @category   Zend
 * @package    Zend_Tag
 * @subpackage UnitTests
 * @group      Zend_Tag
 * @group      Zend_Tag_Cloud
 */
class HtmlTagTest extends \PHPUnit_Framework_TestCase
{
    public function testDefaultOutput()
    {
        $decorator = new Decorator\HtmlTag();
        $expected  = array('<li><a href="http://first" style="font-size: 10px;">foo</a></li>',
                           '<li><a href="http://second" style="font-size: 13px;">bar</a></li>',
                           '<li><a href="http://third" style="font-size: 20px;">baz</a></li>');

        $this->assertEquals($decorator->render($this->_getTagList()), $expected);
    }

    public function testNestedTags()
    {
        $decorator = new Decorator\HtmlTag();
        $decorator->setHtmlTags(array('span' => array('class' => 'tag'), 'li'));
        $expected  = array('<li><span class="tag"><a href="http://first" style="font-size: 10px;">foo</a></span></li>',
                           '<li><span class="tag"><a href="http://second" style="font-size: 13px;">bar</a></span></li>',
                           '<li><span class="tag"><a href="http://third" style="font-size: 20px;">baz</a></span></li>');

        $this->assertEquals($decorator->render($this->_getTagList()), $expected);
    }

    public function testFontSizeSpread()
    {
        $decorator = new Decorator\HtmlTag();
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
        $decorator = new Decorator\HtmlTag();
        $decorator->setClassList(array('small', 'medium', 'large'));

        $expected  = array('<li><a href="http://first" class="small">foo</a></li>',
                           '<li><a href="http://second" class="medium">bar</a></li>',
                           '<li><a href="http://third" class="large">baz</a></li>');

        $this->assertEquals($decorator->render($this->_getTagList()), $expected);
    }

    public function testEmptyClassList()
    {
        $decorator = new Decorator\HtmlTag();

        $this->setExpectedException('Zend\Tag\Cloud\Decorator\Exception\InvalidArgumentException', 'Classlist is empty');
        $decorator->setClassList(array());
    }

    public function testInvalidClassList()
    {
        $decorator = new Decorator\HtmlTag();

        $this->setExpectedException('Zend\Tag\Cloud\Decorator\Exception\InvalidArgumentException', 'Classlist contains an invalid classname');
        $decorator->setClassList(array(array()));
    }

    public function testInvalidFontSizeUnit()
    {
        $decorator = new Decorator\HtmlTag();

        $this->setExpectedException('Zend\Tag\Cloud\Decorator\Exception\InvalidArgumentException', 'Invalid fontsize unit specified');
        $decorator->setFontSizeUnit('foo');
    }

    public function testInvalidMinFontSize()
    {
        $decorator = new Decorator\HtmlTag();

        $this->setExpectedException('Zend\Tag\Cloud\Decorator\Exception\InvalidArgumentException', 'Fontsize must be numeric');
        $decorator->setMinFontSize('foo');
    }

    public function testInvalidMaxFontSize()
    {
        $decorator = new Decorator\HtmlTag();

        $this->setExpectedException('Zend\Tag\Cloud\Decorator\Exception\InvalidArgumentException', 'Fontsize must be numeric');
        $decorator->setMaxFontSize('foo');
    }

    public function testConstructorWithArray()
    {
        $decorator = new Decorator\HtmlTag(array('minFontSize' => 5, 'maxFontSize' => 10, 'fontSizeUnit' => 'pt'));

        $this->assertEquals(5, $decorator->getMinFontSize());
        $this->assertEquals(10, $decorator->getMaxFontSize());
        $this->assertEquals('pt', $decorator->getFontSizeUnit());
    }

    public function testConstructorWithConfig()
    {
        $decorator = new Decorator\HtmlTag(new \Zend\Config\Config(array('minFontSize' => 5, 'maxFontSize' => 10, 'fontSizeUnit' => 'pt')));

        $this->assertEquals(5, $decorator->getMinFontSize());
        $this->assertEquals(10, $decorator->getMaxFontSize());
        $this->assertEquals('pt', $decorator->getFontSizeUnit());
    }

    public function testSetOptions()
    {
        $decorator = new Decorator\HtmlTag();
        $decorator->setOptions(array('minFontSize' => 5, 'maxFontSize' => 10, 'fontSizeUnit' => 'pt'));

        $this->assertEquals(5, $decorator->getMinFontSize());
        $this->assertEquals(10, $decorator->getMaxFontSize());
        $this->assertEquals('pt', $decorator->getFontSizeUnit());
    }

    public function testSkipOptions()
    {
        $decorator = new Decorator\HtmlTag(array('options' => 'foobar'));
        // In case would fail due to an error
    }

    protected function _getTagList()
    {
        $list   = new Tag\ItemList();
        $list[] = new Tag\Item(array('title' => 'foo', 'weight' => 1, 'params' => array('url' => 'http://first')));
        $list[] = new Tag\Item(array('title' => 'bar', 'weight' => 3, 'params' => array('url' => 'http://second')));
        $list[] = new Tag\Item(array('title' => 'baz', 'weight' => 10, 'params' => array('url' => 'http://third')));

        return $list;
    }

    public function getTags()
    {
        $tags = new Tag\ItemList();
        $tags[] = new Tag\Item(array(
            'title' => 'tag',
            'weight' => 1,
            'params' => array(
                'url' => 'http://testing',
            ),
        ));
        return $tags;
    }

    public function invalidHtmlElementProvider()
    {
        return array(
            array(array('_foo')),
            array(array('&foo')),
            array(array(' foo')),
            array(array(' foo')),
            array(array(
                '_foo' => array(),
            )),
        );
    }

    /**
     * @dataProvider invalidHtmlElementProvider
     */
    public function testInvalidElementNamesRaiseAnException($tags)
    {
        $decorator = new Decorator\HtmlTag();
        $decorator->setHTMLTags($tags);
        $this->setExpectedException('Zend\Tag\Exception\InvalidElementNameException');
        $decorator->render($this->getTags());
    }

    public function invalidAttributeProvider()
    {
        return array(
            array(array(
                'foo' => array(
                    '&bar' => 'baz',
                ),
            )),
            array(array(
                'foo' => array(
                    ':bar&baz' => 'bat',
                ),
            )),
            array(array(
                'foo' => array(
                    'bar/baz' => 'bat',
                ),
            )),
        );
    }

    /**
     * @dataProvider invalidAttributeProvider
     */
    public function testInvalidAttributesRaiseAnException($tags)
    {
        $decorator = new Decorator\HtmlTag();
        $decorator->setHTMLTags($tags);
        $this->setExpectedException('Zend\Tag\Exception\InvalidAttributeNameException');
        $decorator->render($this->getTags());
    }
}
