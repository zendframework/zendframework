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

/**
 * @namespace
 */
namespace ZendTest\Tag\Cloud\Decorator;

use Zend\Tag\Cloud\Decorator;

/**
 * @category   Zend
 * @package    Zend_Tag
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Tag
 * @group      Zend_Tag_Cloud
 */
class HTMLCloudTest extends \PHPUnit_Framework_TestCase
{
    public function testDefaultOutput()
    {
        $decorator = new Decorator\HTMLCloud();

        $this->assertEquals('<ul class="Zend_Tag_Cloud">foo bar</ul>', $decorator->render(array('foo', 'bar')));
    }

    public function testNestedTags()
    {
        $decorator = new Decorator\HTMLCloud();
        $decorator->setHTMLTags(array('span', 'div' => array('id' => 'tag-cloud')));

        $this->assertEquals('<div id="tag-cloud"><span>foo bar</span></div>', $decorator->render(array('foo', 'bar')));
    }

    public function testSeparator()
    {
        $decorator = new Decorator\HTMLCloud();
        $decorator->setSeparator('-');

        $this->assertEquals('<ul class="Zend_Tag_Cloud">foo-bar</ul>', $decorator->render(array('foo', 'bar')));
    }

    public function testConstructorWithArray()
    {
        $decorator = new Decorator\HTMLCloud(array('htmlTags' => array('div'), 'separator' => ' '));

        $this->assertEquals('<div>foo bar</div>', $decorator->render(array('foo', 'bar')));
    }

    public function testConstructorWithConfig()
    {
        $decorator = new Decorator\HTMLCloud(new \Zend\Config\Config(array('htmlTags' => array('div'), 'separator' => ' ')));

        $this->assertEquals('<div>foo bar</div>', $decorator->render(array('foo', 'bar')));
    }

    public function testSetOptions()
    {
        $decorator = new Decorator\HTMLCloud();
        $decorator->setOptions(array('htmlTags' => array('div'), 'separator' => ' '));

        $this->assertEquals('<div>foo bar</div>', $decorator->render(array('foo', 'bar')));
    }

    public function testSkipOptions()
    {
        $decorator = new Decorator\HTMLCloud(array('options' => 'foobar'));
        // In case would fail due to an error
    }
}

