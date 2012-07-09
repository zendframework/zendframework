<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Tag
 */

namespace ZendTest\Tag\Cloud\Decorator;

use Zend\Tag\Cloud\Decorator;

/**
 * @category   Zend
 * @package    Zend_Tag
 * @subpackage UnitTests
 * @group      Zend_Tag
 * @group      Zend_Tag_Cloud
 */
class HtmlCloudTest extends \PHPUnit_Framework_TestCase
{
    public function testDefaultOutput()
    {
        $decorator = new Decorator\HtmlCloud();

        $this->assertEquals('<ul class="Zend\Tag\Cloud">foo bar</ul>', $decorator->render(array('foo', 'bar')));
    }

    public function testNestedTags()
    {
        $decorator = new Decorator\HtmlCloud();
        $decorator->setHtmlTags(array('span', 'div' => array('id' => 'tag-cloud')));

        $this->assertEquals('<div id="tag-cloud"><span>foo bar</span></div>', $decorator->render(array('foo', 'bar')));
    }

    public function testSeparator()
    {
        $decorator = new Decorator\HtmlCloud();
        $decorator->setSeparator('-');

        $this->assertEquals('<ul class="Zend\Tag\Cloud">foo-bar</ul>', $decorator->render(array('foo', 'bar')));
    }

    public function testConstructorWithArray()
    {
        $decorator = new Decorator\HtmlCloud(array('htmlTags' => array('div'), 'separator' => ' '));

        $this->assertEquals('<div>foo bar</div>', $decorator->render(array('foo', 'bar')));
    }

    public function testConstructorWithConfig()
    {
        $decorator = new Decorator\HtmlCloud(new \Zend\Config\Config(array('htmlTags' => array('div'), 'separator' => ' ')));

        $this->assertEquals('<div>foo bar</div>', $decorator->render(array('foo', 'bar')));
    }

    public function testSetOptions()
    {
        $decorator = new Decorator\HtmlCloud();
        $decorator->setOptions(array('htmlTags' => array('div'), 'separator' => ' '));

        $this->assertEquals('<div>foo bar</div>', $decorator->render(array('foo', 'bar')));
    }

    public function testSkipOptions()
    {
        $decorator = new Decorator\HtmlCloud(array('options' => 'foobar'));
        // In case would fail due to an error
    }
}

