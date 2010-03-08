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
    define('PHPUnit_MAIN_METHOD', 'Zend_Tag_ItemTest::main');
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
class Zend_Tag_ItemTest extends PHPUnit_Framework_TestCase
{
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite(__CLASS__);
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function testConstuctor()
    {
        $tag = new Zend_Tag_Item(array(
            'title' => 'foo',
            'weight' => 10,
            'params' => array(
                'bar' => 'baz'
            )
        ));

        $this->assertEquals('foo', $tag->getTitle());
        $this->assertEquals(10, $tag->getWeight());
        $this->assertEquals('baz', $tag->getParam('bar'));
    }

    public function testSetOptions()
    {
        $tag = new Zend_Tag_Item(array('title' => 'foo', 'weight' => 1));
        $tag->setOptions(array(
            'title' => 'bar',
            'weight' => 10,
            'params' => array(
                'bar' => 'baz'
            )
        ));

        $this->assertEquals('bar', $tag->getTitle());
        $this->assertEquals(10, $tag->getWeight());
        $this->assertEquals('baz', $tag->getParam('bar'));
    }

    public function testSetParam()
    {
        $tag = new Zend_Tag_Item(array('title' => 'foo', 'weight' => 1));
        $tag->setParam('bar', 'baz');

        $this->assertEquals('baz', $tag->getParam('bar'));
    }

    public function testSetTitle()
    {
        $tag = new Zend_Tag_Item(array('title' => 'foo', 'weight' => 1));
        $tag->setTitle('baz');

        $this->assertEquals('baz', $tag->getTitle());
    }

    public function testInvalidTitle()
    {
        try {
            $tag = new Zend_Tag_Item(array('title' => 10, 'weight' => 1));
            $this->fail('An expected Zend_Tag_Exception was not raised');
        } catch (Zend_Tag_Exception $e) {
            $this->assertEquals($e->getMessage(), 'Title must be a string');
        }
    }

    public function testSetWeight()
    {
        $tag = new Zend_Tag_Item(array('title' => 'foo', 'weight' => 1));
        $tag->setWeight('10');

        $this->assertEquals(10.0, $tag->getWeight());
        $this->assertTrue(is_float($tag->getWeight()));
    }

    public function testInvalidWeight()
    {
        try {
            $tag = new Zend_Tag_Item(array('title' => 'foo', 'weight' => 'foobar'));
            $this->fail('An expected Zend_Tag_Exception was not raised');
        } catch (Zend_Tag_Exception $e) {
            $this->assertEquals($e->getMessage(), 'Weight must be numeric');
        }
    }

    public function testSkipOptions()
    {
        $tag = new Zend_Tag_Item(array('title' => 'foo', 'weight' => 1, 'param' => 'foobar'));
        // In case would fail due to an error
    }

    public function testInvalidOptions()
    {
        try {
            $tag = new Zend_Tag_Item('test');
            $this->fail('An expected Zend_Tag_Exception was not raised');
        } catch (Zend_Tag_Exception $e) {
            $this->assertEquals($e->getMessage(), 'Invalid options provided to constructor');
        }
    }

    public function testMissingTitle()
    {
        try {
            $tag = new Zend_Tag_Item(array('weight' => 1));
            $this->fail('An expected Zend_Tag_Exception was not raised');
        } catch (Zend_Tag_Exception $e) {
            $this->assertEquals($e->getMessage(), 'Title was not set');
        }
    }

    public function testMissingWeight()
    {
        try {
            $tag = new Zend_Tag_Item(array('title' => 'foo'));
            $this->fail('An expected Zend_Tag_Exception was not raised');
        } catch (Zend_Tag_Exception $e) {
            $this->assertEquals($e->getMessage(), 'Weight was not set');
        }
    }

    public function testConfigOptions()
    {
        $tag = new Zend_Tag_Item(new Zend_Config(array('title' => 'foo', 'weight' => 1)));

        $this->assertEquals($tag->getTitle(), 'foo');
        $this->assertEquals($tag->getWeight(), 1);
    }

    public function testGetNonSetParam()
    {
        $tag = new Zend_Tag_Item(array('title' => 'foo', 'weight' => 1));

        $this->assertNull($tag->getParam('foo'));
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Tag_ItemTest::main') {
    Zend_Tag_ItemTest::main();
}
