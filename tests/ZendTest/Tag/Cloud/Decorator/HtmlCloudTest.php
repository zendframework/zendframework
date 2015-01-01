<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Tag\Cloud\Decorator;

use Zend\Tag\Cloud\Decorator;

/**
 * @group      Zend_Tag
 * @group      Zend_Tag_Cloud
 */
class HtmlCloudTest extends \PHPUnit_Framework_TestCase
{
    public function testDefaultOutput()
    {
        $decorator = new Decorator\HtmlCloud();

        $this->assertEquals(
            '<ul class="zend-tag-cloud">foo bar</ul>',
            $decorator->render(
                array(
                     'foo',
                     'bar'
                )
            )
        );
    }

    public function testNestedTags()
    {
        $decorator = new Decorator\HtmlCloud();
        $decorator->setHtmlTags(
            array(
                 'span',
                 'div' => array('id' => 'tag-cloud')
            )
        );

        $this->assertEquals(
            '<div id="tag-cloud"><span>foo bar</span></div>',
            $decorator->render(
                array(
                     'foo',
                     'bar'
                )
            )
        );
    }

    public function testSeparator()
    {
        $decorator = new Decorator\HtmlCloud();
        $decorator->setSeparator('-');

        $this->assertEquals(
            '<ul class="zend-tag-cloud">foo-bar</ul>',
            $decorator->render(
                array(
                     'foo',
                     'bar'
                )
            )
        );
    }

    public function testConstructorWithArray()
    {
        $decorator = new Decorator\HtmlCloud(array(
                                                  'htmlTags'  => array('div'),
                                                  'separator' => ' '
                                             ));

        $this->assertEquals(
            '<div>foo bar</div>', $decorator->render(
                array(
                     'foo',
                     'bar'
                )
            )
        );
    }

    public function testConstructorWithConfig()
    {
        $decorator = new Decorator\HtmlCloud(
            new \Zend\Config\Config(
                array(
                     'htmlTags'  => array('div'),
                     'separator' => ' '
                )
            )
        );

        $this->assertEquals(
            '<div>foo bar</div>', $decorator->render(
                array(
                     'foo',
                     'bar'
                )
            )
        );
    }

    public function testSetOptions()
    {
        $decorator = new Decorator\HtmlCloud();
        $decorator->setOptions(
            array(
                 'htmlTags'  => array('div'),
                 'separator' => ' '
            )
        );

        $this->assertEquals(
            '<div>foo bar</div>', $decorator->render(
                array(
                     'foo',
                     'bar'
                )
            )
        );
    }

    public function testSkipOptions()
    {
        $decorator = new Decorator\HtmlCloud(array('options' => 'foobar'));
        // In case would fail due to an error
    }

    public function invalidHtmlTagProvider()
    {
        return array(
            array(array('_foo')),
            array(array('&foo')),
            array(array(' foo')),
            array(array(' foo')),
            array(
                array(
                    '_foo' => array(),
                )
            ),
        );
    }

    /**
     * @dataProvider invalidHtmlTagProvider
     */
    public function testInvalidHtmlTagsRaiseAnException($tags)
    {
        $decorator = new Decorator\HtmlCloud();
        $decorator->setHTMLTags($tags);
        $this->setExpectedException(
            'Zend\Tag\Exception\InvalidElementNameException'
        );
        $decorator->render(array());
    }

    public function invalidAttributeProvider()
    {
        return array(
            array(
                array(
                    'foo' => array(
                        '&bar' => 'baz',
                    ),
                )
            ),
            array(
                array(
                    'foo' => array(
                        ':bar&baz' => 'bat',
                    ),
                )
            ),
            array(
                array(
                    'foo' => array(
                        'bar/baz' => 'bat',
                    ),
                )
            ),
        );
    }

    /**
     * @dataProvider invalidAttributeProvider
     */
    public function testInvalidAttributeNamesRaiseAnException($tags)
    {
        $decorator = new Decorator\HtmlCloud();
        $decorator->setHTMLTags($tags);
        $this->setExpectedException(
            'Zend\Tag\Exception\InvalidAttributeNameException'
        );
        $decorator->render(array());
    }
}
