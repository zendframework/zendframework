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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Tag;

use Zend\Tag,
	Zend\Tag\Exception\InvalidArgumentException,
	Zend\Tag\Exception\OutOfBoundsException;

/**
 * @category   Zend
 * @package    Zend_Tag
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Tag
 */
class ItemListTest extends \PHPUnit_Framework_TestCase
{
    public function testArrayAccessAndCount()
    {
        $list = new Tag\ItemList();

        $list[] = $this->_getItem('foo');
        $list[] = $this->_getItem('bar');
        $list[] = $this->_getItem('baz');
        $this->assertEquals(count($list), 3);

        unset($list[2]);
        $this->assertEquals(count($list), 2);

        $list[5] = $this->_getItem('bat');
        $this->assertTrue(isset($list[5]));

        $this->assertEquals($list[1]->getTitle(), 'bar');
    }

    public function testSeekableIterator()
    {
        $list = new Tag\ItemList();

        $values = array('foo', 'bar', 'baz');
        foreach ($values as $value) {
            $list[] = $this->_getItem($value);
        }

        foreach ($list as $key => $item) {
            $this->assertEquals($item->getTitle(), $values[$key]);
        }

        $list->seek(2);
        $this->assertEquals($list->current()->getTitle(), $values[2]);
    }
    
    public function testSeektableIteratorThrowsBoundsException()
    {
        $list = new Tag\ItemList();

        $values = array('foo', 'bar', 'baz');
        foreach ($values as $value) {
            $list[] = $this->_getItem($value);
        }
        $list->seek(2);
        
        $this->setExpectedException('Zend\Tag\Exception\OutOfBoundsException', 'Invalid seek position');
        $list->seek(3);
    }

    public function testInvalidItem()
    {
        $list = new Tag\ItemList();

        $this->setExpectedException('\Zend\Tag\Exception\OutOfBoundsException', 'Item must implement Zend\Tag\Taggable');
        $list[] = 'test';
    }

    public function testSpreadWeightValues()
    {
        $list = new Tag\ItemList();

        $list[] = $this->_getItem('foo', 1);
        $list[] = $this->_getItem('bar', 5);
        $list[] = $this->_getItem('baz', 50);

        $list->spreadWeightValues(array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10));

        $weightValues = array();
        foreach ($list as $item) {
            $weightValues[] = $item->getParam('weightValue');
        }

        $expectedWeightValues = array(1, 2, 10);

        $this->assertEquals($weightValues, $expectedWeightValues);
    }

    public function testSpreadWeightValuesWithSingleValue()
    {
        $list = new Tag\ItemList();

        $list[] = $this->_getItem('foo', 1);
        $list[] = $this->_getItem('bar', 5);
        $list[] = $this->_getItem('baz', 50);

        $list->spreadWeightValues(array('foobar'));

        $weightValues = array();
        foreach ($list as $item) {
            $weightValues[] = $item->getParam('weightValue');
        }

        $expectedWeightValues = array('foobar', 'foobar', 'foobar');

        $this->assertEquals($weightValues, $expectedWeightValues);
    }

    public function testSpreadWeightValuesWithEmptyValuesArray()
    {
        $list = new Tag\ItemList();

        $this->setExpectedException('Zend\Tag\Exception\InvalidArgumentException', 'Value list may not be empty');
        $list->spreadWeightValues(array());
    }

    protected function _getItem($title = 'foo', $weight = 1)
    {
        return new Tag\Item(array('title' => $title, 'weight' => $weight));
    }
}
