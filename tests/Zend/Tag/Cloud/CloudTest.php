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
namespace ZendTest\Tag\Cloud;
use Zend\Tag;
use Zend\Tag\Cloud;
use Zend\Loader\PluginLoader;
use ZendTest\Tag\Cloud\TestAsset;

/**
 * @category   Zend
 * @package    Zend_Tag
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
        $this->assertTrue($cloud->getCloudDecorator() instanceof \Zend\Tag\Cloud\Decorator\HTMLCloud);

        $cloud->setCloudDecorator(new TestAsset\CloudDummy());
        $this->assertTrue($cloud->getCloudDecorator() instanceof TestAsset\CloudDummy);
    }

    public function testSetInvalidCloudDecorator()
    {
        $cloud = $this->_getCloud();

        try {
            $cloud->setCloudDecorator(new \stdClass());
            $this->fail('An expected Zend_Tag_Cloud_Exception was not raised');
        } catch (Cloud\Exception $e) {
            $this->assertEquals('Decorator is no instance of Zend_Tag_Cloud_Decorator_Cloud', $e->getMessage());
        }
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
        $this->assertTrue($cloud->getTagDecorator() instanceof \Zend\Tag\Cloud\Decorator\HTMLTag);

        $cloud->setTagDecorator(new TestAsset\TagDummy());
        $this->assertTrue($cloud->getTagDecorator() instanceof TestAsset\TagDummy);
    }

    public function testSetInvalidTagDecorator()
    {
        $cloud = $this->_getCloud();

        try {
            $cloud->setTagDecorator(new \stdClass());
            $this->fail('An expected Zend_Tag_Cloud_Exception was not raised');
        } catch (Cloud\Exception $e) {
            $this->assertEquals('Decorator is no instance of Zend_Tag_Cloud_Decorator_Tag', $e->getMessage());
        }
    }

    public function testSetPrefixPathViaOptions()
    {
        $cloud = $this->_getCloud(array(
            'prefixPath' => array(
                'prefix' => 'ZendTest\Tag\Cloud\TestAsset',
                'path' => __DIR__ . '/TestAsset'
            ),
            'cloudDecorator' => array(
                'decorator' => 'CloudDummy1',
                'options'   => array(
                    'foo' => 'bar'
                )
            )
        ), false);

        $this->assertTrue($cloud->getCloudDecorator() instanceof TestAsset\CloudDummy1);
        $this->assertEquals('bar', $cloud->getCloudDecorator()->getFoo());
    }

    public function testSetPrefixPathsViaOptions()
    {
        $cloud = $this->_getCloud(array(
            'prefixPath' => array(
                array(
                    'prefix' => 'ZendTest\Tag\Cloud\TestAsset',
                    'path' => __DIR__ . '/TestAsset'
                )
            ),
            'cloudDecorator' => array(
                'decorator' => 'CloudDummy2',
                'options'   => array(
                    'foo' => 'bar'
                )
            )
        ), false);

        $this->assertTrue($cloud->getCloudDecorator() instanceof TestAsset\CloudDummy2);
        $this->assertEquals('bar', $cloud->getCloudDecorator()->getFoo());
    }

    public function testSetPrefixPathsSkip()
    {
        $cloud = $this->_getCloud(array(
            'prefixPath' => array(
                array(
                    'prefix' => 'foobar',
                )
            ),
        ), false);

        $this->assertEquals(1, count($cloud->getPluginLoader()->getPaths()));
    }

    public function testSetPluginLoader()
    {
        $loader = new PluginLoader(array('foo_' => 'bar/'));
        $cloud  = $this->_getCloud(array(), null);
        $cloud->setPluginLoader($loader);
        $paths  = $cloud->getPluginLoader()->getPaths();

        $this->assertEquals('bar/', $paths['foo_\\'][0]); // this might be an issue
    }

    public function testSetPluginLoaderViaOptions()
    {
        $loader = new PluginLoader(array('foo_' => 'bar/'));
        $cloud  = $this->_getCloud(array('pluginLoader' => $loader), null);
        $paths  = $cloud->getPluginLoader()->getPaths();

        $this->assertEquals('bar/', $paths['foo_\\'][0]); // this might be an issue
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

        try {
            $cloud->appendTag('foo');
            $this->fail('An expected Zend_Tag_Cloud_Exception was not raised');
        } catch (Cloud\Exception $e) {
            $this->assertEquals('Tag must be an instance of Zend_Tag_Taggable or an array', $e->getMessage());
        }
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

        try {
            $cloud->setTags(array('foo'));
            $this->fail('An expected Zend_Tag_Cloud_Exception was not raised');
        } catch (Cloud\Exception $e) {
            $this->assertEquals('Tag must be an instance of Zend_Tag_Taggable or an array', $e->getMessage());
        }
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
        $expected = '<ul class="Zend_Tag_Cloud">'
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
        $expected = '<ul class="Zend_Tag_Cloud">'
                  . '<li><a href="" style="font-size: 10px;">foo</a></li> '
                  . '<li><a href="" style="font-size: 20px;">bar</a></li>'
                  . '</ul>';
        $this->assertEquals($expected, (string) $cloud);
    }

    protected function _getCloud($options = null, $setPluginLoader = true)
    {
        $cloud = new Tag\Cloud($options);

        if ($setPluginLoader) {
            $cloud->getPluginLoader()->addPrefixPath('ZendTest\Tag\Cloud\TestAsset', __DIR__ . '/TestAsset');
        }

        return $cloud;
    }
}

class ItemListDummy extends Tag\ItemList {}
