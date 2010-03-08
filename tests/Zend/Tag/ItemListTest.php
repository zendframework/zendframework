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
    define('PHPUnit_MAIN_METHOD', 'Zend_Tag_ItemListTest::main');
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
 */
class Zend_Tag_ItemListTest extends PHPUnit_Framework_TestCase
{
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite(__CLASS__);
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function testArrayAccessAndCount()
    {
        $list = new Zend_Tag_ItemList();

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
        $list = new Zend_Tag_ItemList();

        $values = array('foo', 'bar', 'baz');
        foreach ($values as $value) {
            $list[] = $this->_getItem($value);
        }

        foreach ($list as $key => $item) {
            $this->assertEquals($item->getTitle(), $values[$key]);
        }

        $list->seek(2);
        $this->assertEquals($list->current()->getTitle(), $values[2]);

        try {
            $list->seek(3);
            $this->fail('An expected OutOfBoundsException was not raised');
        } catch (OutOfBoundsException $e) {
            $this->assertEquals($e->getMessage(), 'Invalid seek position');
        }
    }

    public function testInvalidItem()
    {
        $list = new Zend_Tag_ItemList();

        try {
            $list[] = 'test';
            $this->fail('An expected Zend_Tag_Exception was not raised');
        } catch (Zend_Tag_Exception $e) {
            $this->assertEquals($e->getMessage(), 'Item must implement Zend_Tag_Taggable');
        }
    }

    public function testSpreadWeightValues()
    {
        $list = new Zend_Tag_ItemList();

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
        $list = new Zend_Tag_ItemList();

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
        $list = new Zend_Tag_ItemList();

        try {
            $list->spreadWeightValues(array());
            $this->fail('An expected Zend_Tag_Exception was not raised');
        } catch (Zend_Tag_Exception $e) {
            $this->assertEquals($e->getMessage(), 'Value list may not be empty');
        }
    }

    protected function _getItem($title = 'foo', $weight = 1)
    {
        return new Zend_Tag_Item(array('title' => $title, 'weight' => $weight));
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Tag_ItemListTest::main') {
    Zend_Tag_ItemListTest::main();
}
