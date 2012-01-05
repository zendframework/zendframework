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
 * @package    Zend_GData_GApps
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\GData\GApps;
use Zend\GData\GApps\Extension;

/**
 * @category   Zend
 * @package    Zend_GData_GApps
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_GData
 * @group      Zend_GData_GApps
 */
class EmailListTest extends \PHPUnit_Framework_TestCase
{

    public function setUp() {
        $this->emailListText = file_get_contents(
                'Zend/GData/GApps/_files/EmailListElementSample1.xml',
                true);
        $this->emailList = new Extension\EmailList();
    }

    public function testEmptyEmailListShouldHaveNoExtensionElements() {
        $this->assertTrue(is_array($this->emailList->extensionElements));
        $this->assertTrue(count($this->emailList->extensionElements) == 0);
    }

    public function testEmptyEmailListShouldHaveNoExtensionAttributes() {
        $this->assertTrue(is_array($this->emailList->extensionAttributes));
        $this->assertTrue(count($this->emailList->extensionAttributes) == 0);
    }

    public function testSampleEmailListShouldHaveNoExtensionElements() {
        $this->emailList->transferFromXML($this->emailListText);
        $this->assertTrue(is_array($this->emailList->extensionElements));
        $this->assertTrue(count($this->emailList->extensionElements) == 0);
    }

    public function testSampleEmailListShouldHaveNoExtensionAttributes() {
        $this->emailList->transferFromXML($this->emailListText);
        $this->assertTrue(is_array($this->emailList->extensionAttributes));
        $this->assertTrue(count($this->emailList->extensionAttributes) == 0);
    }

    public function testNormalEmailListShouldHaveNoExtensionElements() {
        $this->emailList->name = "test-name";

        $this->assertEquals("test-name", $this->emailList->name);

        $this->assertEquals(0, count($this->emailList->extensionElements));
        $newEmailList = new Extension\EmailList();
        $newEmailList->transferFromXML($this->emailList->saveXML());
        $this->assertEquals(0, count($newEmailList->extensionElements));
        $newEmailList->extensionElements = array(
                new \Zend\GData\App\Extension\Element('foo', 'atom', null, 'bar'));
        $this->assertEquals(1, count($newEmailList->extensionElements));
        $this->assertEquals("test-name", $newEmailList->name);

        /* try constructing using magic factory */
        $gdata = new \Zend\GData\GApps();
        $newEmailList2 = $gdata->newEmailList();
        $newEmailList2->transferFromXML($newEmailList->saveXML());
        $this->assertEquals(1, count($newEmailList2->extensionElements));
        $this->assertEquals("test-name", $newEmailList2->name);
    }

    public function testEmptyEmailListToAndFromStringShouldMatch() {
        $emailListXml = $this->emailList->saveXML();
        $newEmailList = new Extension\EmailList();
        $newEmailList->transferFromXML($emailListXml);
        $newEmailListXml = $newEmailList->saveXML();
        $this->assertTrue($emailListXml == $newEmailListXml);
    }

    public function testEmailListWithValueToAndFromStringShouldMatch() {
        $this->emailList->name = "test-name";
        $emailListXml = $this->emailList->saveXML();
        $newEmailList = new Extension\EmailList();
        $newEmailList->transferFromXML($emailListXml);
        $newEmailListXml = $newEmailList->saveXML();
        $this->assertTrue($emailListXml == $newEmailListXml);
        $this->assertEquals("test-name", $this->emailList->name);
    }

    public function testExtensionAttributes() {
        $extensionAttributes = $this->emailList->extensionAttributes;
        $extensionAttributes['foo1'] = array('name'=>'foo1', 'value'=>'bar');
        $extensionAttributes['foo2'] = array('name'=>'foo2', 'value'=>'rab');
        $this->emailList->extensionAttributes = $extensionAttributes;
        $this->assertEquals('bar', $this->emailList->extensionAttributes['foo1']['value']);
        $this->assertEquals('rab', $this->emailList->extensionAttributes['foo2']['value']);
        $emailListXml = $this->emailList->saveXML();
        $newEmailList = new Extension\EmailList();
        $newEmailList->transferFromXML($emailListXml);
        $this->assertEquals('bar', $newEmailList->extensionAttributes['foo1']['value']);
        $this->assertEquals('rab', $newEmailList->extensionAttributes['foo2']['value']);
    }

    public function testConvertFullEmailListToAndFromString() {
        $this->emailList->transferFromXML($this->emailListText);
        $this->assertEquals("us-sales", $this->emailList->name);
    }

}
