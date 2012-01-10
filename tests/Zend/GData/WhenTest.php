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
 * @package    Zend_GData
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\GData;
use Zend\GData\Extension;

/**
 * @category   Zend
 * @package    Zend_GData
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_GData
 */
class WhenTest extends \PHPUnit_Framework_TestCase
{

    public function setUp() {
        $this->whenText = file_get_contents(
                'Zend/GData/_files/WhenElementSample1.xml',
                true);
        $this->when = new Extension\When();
    }

    public function testEmptyWhenShouldHaveNoExtensionElements() {
        $this->assertTrue(is_array($this->when->extensionElements));
        $this->assertTrue(count($this->when->extensionElements) == 0);
    }

    public function testEmptyWhenShouldHaveNoExtensionAttributes() {
        $this->assertTrue(is_array($this->when->extensionAttributes));
        $this->assertTrue(count($this->when->extensionAttributes) == 0);
    }

    public function testSampleWhenShouldHaveNoExtensionElements() {
        $this->when->transferFromXML($this->whenText);
        $this->assertTrue(is_array($this->when->extensionElements));
        $this->assertTrue(count($this->when->extensionElements) == 0);
    }

    public function testSampleWhenShouldHaveNoExtensionAttributes() {
        $this->when->transferFromXML($this->whenText);
        $this->assertTrue(is_array($this->when->extensionAttributes));
        $this->assertTrue(count($this->when->extensionAttributes) == 0);
    }

    public function testNormalWhenShouldHaveNoExtensionElements() {
        $this->when->valueString = "Later";
        $this->when->endTime = "2007-06-21T21:31:56-07:00";
        $this->when->startTime = "2007-06-19T05:42:19-06:00";

        $this->assertEquals("Later", $this->when->valueString);
        $this->assertEquals("2007-06-21T21:31:56-07:00", $this->when->endTime);
        $this->assertEquals("2007-06-19T05:42:19-06:00", $this->when->startTime);

        $this->assertEquals(0, count($this->when->extensionElements));
        $newWhen = new Extension\When();
        $newWhen->transferFromXML($this->when->saveXML());
        $this->assertEquals(0, count($newWhen->extensionElements));
        $newWhen->extensionElements = array(
                new \Zend\GData\App\Extension\Element('foo', 'atom', null, 'bar'));
        $this->assertEquals(1, count($newWhen->extensionElements));
        $this->assertEquals("Later", $newWhen->valueString);
        $this->assertEquals("2007-06-21T21:31:56-07:00", $newWhen->endTime);
        $this->assertEquals("2007-06-19T05:42:19-06:00", $newWhen->startTime);

        /* try constructing using magic factory */
        $gdata = new \Zend\GData\GData();
        $newWhen2 = $gdata->newWhen();
        $newWhen2->transferFromXML($newWhen->saveXML());
        $this->assertEquals(1, count($newWhen2->extensionElements));
        $this->assertEquals("Later", $newWhen2->valueString);
        $this->assertEquals("2007-06-21T21:31:56-07:00", $newWhen2->endTime);
        $this->assertEquals("2007-06-19T05:42:19-06:00", $newWhen2->startTime);
    }

    public function testEmptyWhenToAndFromStringShouldMatch() {
        $whenXml = $this->when->saveXML();
        $newWhen = new Extension\When();
        $newWhen->transferFromXML($whenXml);
        $newWhenXml = $newWhen->saveXML();
        $this->assertTrue($whenXml == $newWhenXml);
    }

    public function testWhenWithValueToAndFromStringShouldMatch() {
        $this->when->valueString = "Later";
        $this->when->endTime = "2007-06-21T21:31:56-07:00";
        $this->when->startTime = "2007-06-19T05:42:19-06:00";
        $whenXml = $this->when->saveXML();
        $newWhen = new Extension\When();
        $newWhen->transferFromXML($whenXml);
        $newWhenXml = $newWhen->saveXML();
        $this->assertTrue($whenXml == $newWhenXml);
        $this->assertEquals("Later", $this->when->valueString);
        $this->assertEquals("2007-06-21T21:31:56-07:00", $this->when->endTime);
        $this->assertEquals("2007-06-19T05:42:19-06:00", $this->when->startTime);
    }

    public function testExtensionAttributes() {
        $extensionAttributes = $this->when->extensionAttributes;
        $extensionAttributes['foo1'] = array('name'=>'foo1', 'value'=>'bar');
        $extensionAttributes['foo2'] = array('name'=>'foo2', 'value'=>'rab');
        $this->when->extensionAttributes = $extensionAttributes;
        $this->assertEquals('bar', $this->when->extensionAttributes['foo1']['value']);
        $this->assertEquals('rab', $this->when->extensionAttributes['foo2']['value']);
        $whenXml = $this->when->saveXML();
        $newWhen = new Extension\When();
        $newWhen->transferFromXML($whenXml);
        $this->assertEquals('bar', $newWhen->extensionAttributes['foo1']['value']);
        $this->assertEquals('rab', $newWhen->extensionAttributes['foo2']['value']);
    }

    public function testConvertFullWhenToAndFromString() {
        $this->when->transferFromXML($this->whenText);
        $this->assertEquals("Tomorrow @ 5 PM", $this->when->valueString);
        $this->assertEquals("2005-06-06T18:00:00-08:00", $this->when->endTime);
        $this->assertEquals("2005-06-06T17:00:00-08:00", $this->when->startTime);
    }

    public function testToStringCanReturnValueString() {
        $this->when->transferFromXML($this->whenText);
        $this->assertEquals('Tomorrow @ 5 PM', $this->when->__toString());
    }

}
