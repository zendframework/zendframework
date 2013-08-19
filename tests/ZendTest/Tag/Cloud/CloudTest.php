<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Tag
 */

namespace ZendTest\Tag\Cloud;

use Zend\Tag;
use Zend\Tag\Cloud;
use Zend\Tag\Cloud\DecoratorPluginManager;
use Zend\Tag\Exception\InvalidArgumentException;
use ZendTest\Tag\Cloud\TestAsset;

/**
 * @category   Zend
 * @package    Zend_Tag
 * @subpackage UnitTests
 * @group      Zend_Tag
 * @group      Zend_Tag_Cloud
 */
class CloudTest extends \PHPUnit_Framework_TestCase
{

    public function testGetAndSetItemList()
    {
        $cloud = $this->_getCloud();
        $this->assertTrue($cloud->getItemList() instanceof Tag\ItemList);

        $cloud->setItemList(new ItemListDummy);
        $this->assertTrue($cloud->getItemList() instanceof ItemListDummy);
    }

    public function testSetCloudDecoratorViaArray()
    {
        $cloud = $this->_getCloud();

        $cloud->setCloudDecorator(array('decorator' => 'CloudDummy', 'options' => array('foo' => 'bar')));
        $this->assertTrue($cloud->getCloudDecorator() instanceof TestAsset\CloudDummy);
        $this->assertEquals('bar', $cloud->getCloudDecorator()->getFoo());
    }

    public function testGetAndSetCloudDecorator()
    {
        $cloud = $this->_getCloud();
        $this->assertTrue($cloud->getCloudDecorator() instanceof \Zend\Tag\Cloud\Decorator\HtmlCloud);

        $cloud->setCloudDecorator(new TestAsset\CloudDummy());
        $this->assertTrue($cloud->getCloudDecorator() instanceof TestAsset\CloudDummy);
    }

    public function testSetInvalidCloudDecorator()
    {
        $cloud = $this->_getCloud();

        $this->setExpectedException('Zend\Tag\Exception\InvalidArgumentException', 'DecoratorInterface');
        $cloud->setCloudDecorator(new \stdClass());
    }

    public function testSetTagDecoratorViaArray()
    {
        $cloud = $this->_getCloud();

        $cloud->setTagDecorator(array('decorator' => 'TagDummy', 'options' => array('foo' => 'bar')));
        $this->assertTrue($cloud->getTagDecorator() instanceof TestAsset\TagDummy);
        $this->assertEquals('bar', $cloud->getTagDecorator()->getFoo());
    }

    public function testGetAndSetTagDecorator()
    {
        $cloud = $this->_getCloud();
        $this->assertTrue($cloud->getTagDecorator() instanceof \Zend\Tag\Cloud\Decorator\HtmlTag);

        $cloud->setTagDecorator(new TestAsset\TagDummy());
        $this->assertTrue($cloud->getTagDecorator() instanceof TestAsset\TagDummy);
    }

    public function testSetInvalidTagDecorator()
    {
        $cloud = $this->_getCloud();

        $this->setExpectedException('Zend\Tag\Exception\InvalidArgumentException', 'DecoratorInterface');
        $cloud->setTagDecorator(new \stdClass());
    }

    public function testSetDecoratorPluginManager()
    {
        $decorators = new DecoratorPluginManager();
        $cloud      = $this->_getCloud(array(), null);
        $cloud->setDecoratorPluginManager($decorators);
        $this->assertSame($decorators, $cloud->getDecoratorPluginManager());
    }

    public function testSetDecoratorPluginManagerViaOptions()
    {
        $decorators = new DecoratorPluginManager();
        $cloud      = $this->_getCloud(array('decoratorPluginManager' => $decorators), null);
        $this->assertSame($decorators, $cloud->getDecoratorPluginManager());
    }

    public function testAppendTagAsArray()
    {
        $cloud = $this->_getCloud();
        $list  = $cloud->getItemList();

        $cloud->appendTag(array('title' => 'foo', 'weight' => 1));

        $this->assertEquals('foo', $list[0]->getTitle());
    }

    public function testAppendTagAsItem()
    {
        $cloud = $this->_getCloud();
        $list  = $cloud->getItemList();

        $cloud->appendTag(new Tag\Item(array('title' => 'foo', 'weight' => 1)));

        $this->assertEquals('foo', $list[0]->getTitle());
    }

    public function testAppendInvalidTag()
    {
        $cloud = $this->_getCloud();

        $this->setExpectedException('Zend\Tag\Exception\InvalidArgumentException', 'TaggableInterface');
        $cloud->appendTag('foo');
    }

    public function testSetTagsAsArray()
    {
        $cloud = $this->_getCloud();
        $list  = $cloud->getItemList();

        $cloud->setTags(array(array('title' => 'foo', 'weight' => 1),
                              array('title' => 'bar', 'weight' => 2)));

        $this->assertEquals('foo', $list[0]->getTitle());
        $this->assertEquals('bar', $list[1]->getTitle());
    }

    public function testSetTagsAsItem()
    {
        $cloud = $this->_getCloud();
        $list  = $cloud->getItemList();

        $cloud->setTags(array(new Tag\Item(array('title' => 'foo', 'weight' => 1)),
                              new Tag\Item(array('title' => 'bar', 'weight' => 2))));

        $this->assertEquals('foo', $list[0]->getTitle());
        $this->assertEquals('bar', $list[1]->getTitle());
    }

    public function testSetTagsMixed()
    {
        $cloud = $this->_getCloud();
        $list  = $cloud->getItemList();

        $cloud->setTags(array(array('title' => 'foo', 'weight' => 1),
                              new Tag\Item(array('title' => 'bar', 'weight' => 2))));

        $this->assertEquals('foo', $list[0]->getTitle());
        $this->assertEquals('bar', $list[1]->getTitle());
    }

    public function testSetInvalidTags()
    {
        $cloud = $this->_getCloud();

        $this->setExpectedException('Zend\Tag\Exception\InvalidArgumentException', 'TaggableInterface');
        $cloud->setTags(array('foo'));
    }

    public function testConstructorWithArray()
    {
        $cloud = $this->_getCloud(array('tags' => array(array('title' => 'foo', 'weight' => 1))));
        $list  = $cloud->getItemList();

        $this->assertEquals('foo', $list[0]->getTitle());
    }

    public function testConstructorWithConfig()
    {
        $cloud = $this->_getCloud(new \Zend\Config\Config(array('tags' => array(array('title' => 'foo', 'weight' => 1)))));
        $list  = $cloud->getItemList();

        $this->assertEquals('foo', $list[0]->getTitle());
    }

    public function testSetOptions()
    {
        $cloud = $this->_getCloud();
        $cloud->setOptions(array('tags' => array(array('title' => 'foo', 'weight' => 1))));
        $list  = $cloud->getItemList();

        $this->assertEquals('foo', $list[0]->getTitle());
    }

    public function testSkipOptions()
    {
        $cloud = $this->_getCloud(array('options' => 'foobar'));
        // In case would fail due to an error
    }

    public function testRender()
    {
        $cloud    = $this->_getCloud(array('tags' => array(array('title' => 'foo', 'weight' => 1), array('title' => 'bar', 'weight' => 3))));
        $expected = '<ul class="Zend&#x5C;Tag&#x5C;Cloud">'
                  . '<li><a href="" style="font-size: 10px;">foo</a></li> '
                  . '<li><a href="" style="font-size: 20px;">bar</a></li>'
                  . '</ul>';
        $this->assertEquals($expected, $cloud->render());
    }

    public function testRenderEmptyCloud()
    {
        $cloud = $this->_getCloud();
        $this->assertEquals('', $cloud->render());
    }

    public function testRenderViaToString()
    {
        $cloud = $this->_getCloud(array('tags' => array(array('title' => 'foo', 'weight' => 1), array('title' => 'bar', 'weight' => 3))));
        $expected = '<ul class="Zend&#x5C;Tag&#x5C;Cloud">'
                  . '<li><a href="" style="font-size: 10px;">foo</a></li> '
                  . '<li><a href="" style="font-size: 20px;">bar</a></li>'
                  . '</ul>';
        $this->assertEquals($expected, (string) $cloud);
    }

    protected function _getCloud($options = null, $setDecoratorPluginManager = true)
    {
        $cloud = new Tag\Cloud($options);

        if ($setDecoratorPluginManager) {
            $decorators = $cloud->getDecoratorPluginManager();
            $decorators->setInvokableClass('clouddummy',  'ZendTest\Tag\Cloud\TestAsset\CloudDummy');
            $decorators->setInvokableClass('clouddummy1', 'ZendTest\Tag\Cloud\TestAsset\CloudDummy1');
            $decorators->setInvokableClass('clouddummy2', 'ZendTest\Tag\Cloud\TestAsset\CloudDummy2');
            $decorators->setInvokableClass('tagdummy',    'ZendTest\Tag\Cloud\TestAsset\TagDummy');
        }

        return $cloud;
    }
}

class ItemListDummy extends Tag\ItemList {}
