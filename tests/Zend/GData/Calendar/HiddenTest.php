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
 * @package    Zend_GData_Calendar
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\GData\Calendar;
use Zend\GData\Calendar\Extension;

/**
 * @category   Zend
 * @package    Zend_GData_Calendar
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_GData
 * @group      Zend_GData_Calendar
 */
class HiddenTest extends \PHPUnit_Framework_TestCase
{

    public function setUp() {
        $this->hiddenText = file_get_contents(
                'Zend/GData/Calendar/_files/HiddenElementSample1.xml',
                true);
        $this->hidden = new Extension\Hidden();
    }

    public function testEmptyHiddenShouldHaveNoExtensionElements() {
        $this->assertTrue(is_array($this->hidden->extensionElements));
        $this->assertTrue(count($this->hidden->extensionElements) == 0);
    }

    public function testEmptyHiddenShouldHaveNoExtensionAttributes() {
        $this->assertTrue(is_array($this->hidden->extensionAttributes));
        $this->assertTrue(count($this->hidden->extensionAttributes) == 0);
    }

    public function testSampleHiddenShouldHaveNoExtensionElements() {
        $this->hidden->transferFromXML($this->hiddenText);
        $this->assertTrue(is_array($this->hidden->extensionElements));
        $this->assertTrue(count($this->hidden->extensionElements) == 0);
    }

    public function testSampleHiddenShouldHaveNoExtensionAttributes() {
        $this->hidden->transferFromXML($this->hiddenText);
        $this->assertTrue(is_array($this->hidden->extensionAttributes));
        $this->assertTrue(count($this->hidden->extensionAttributes) == 0);
    }

    public function testNormalHiddenShouldHaveNoExtensionElements() {
        $this->hidden->value = true;
        $this->assertEquals($this->hidden->value, true);
        $this->assertEquals(count($this->hidden->extensionElements), 0);
        $newHidden = new Extension\Hidden();
        $newHidden->transferFromXML($this->hidden->saveXML());
        $this->assertEquals(count($newHidden->extensionElements), 0);
        $newHidden->extensionElements = array(
                new \Zend\GData\App\Extension\Element('foo', 'atom', null, 'bar'));
        $this->assertEquals(count($newHidden->extensionElements), 1);
        $this->assertEquals($newHidden->value, true);

        /* try constructing using magic factory */
        $cal = new \Zend\GData\Calendar();
        $newHidden2 = $cal->newHidden();
        $newHidden2->transferFromXML($newHidden->saveXML());
        $this->assertEquals(count($newHidden2->extensionElements), 1);
        $this->assertEquals($newHidden2->value, true);
    }

    public function testEmptyHiddenToAndFromStringShouldMatch() {
        $hiddenXml = $this->hidden->saveXML();
        $newHidden = new Extension\Hidden();
        $newHidden->transferFromXML($hiddenXml);
        $newHiddenXml = $newHidden->saveXML();
        $this->assertTrue($hiddenXml == $newHiddenXml);
    }

    public function testHiddenWithValueToAndFromStringShouldMatch() {
        $this->hidden->value = true;
        $hiddenXml = $this->hidden->saveXML();
        $newHidden = new Extension\Hidden();
        $newHidden->transferFromXML($hiddenXml);
        $newHiddenXml = $newHidden->saveXML();
        $this->assertTrue($hiddenXml == $newHiddenXml);
        $this->assertEquals(true, $newHidden->value);
    }

    public function testExtensionAttributes() {
        $extensionAttributes = $this->hidden->extensionAttributes;
        $extensionAttributes['foo1'] = array('name'=>'foo1', 'value'=>'bar');
        $extensionAttributes['foo2'] = array('name'=>'foo2', 'value'=>'rab');
        $this->hidden->extensionAttributes = $extensionAttributes;
        $this->assertEquals('bar', $this->hidden->extensionAttributes['foo1']['value']);
        $this->assertEquals('rab', $this->hidden->extensionAttributes['foo2']['value']);
        $hiddenXml = $this->hidden->saveXML();
        $newHidden = new Extension\Hidden();
        $newHidden->transferFromXML($hiddenXml);
        $this->assertEquals('bar', $newHidden->extensionAttributes['foo1']['value']);
        $this->assertEquals('rab', $newHidden->extensionAttributes['foo2']['value']);
    }

    public function testConvertFullHiddenToAndFromString() {
        $this->hidden->transferFromXML($this->hiddenText);
        $this->assertEquals($this->hidden->value, false);
    }

}
