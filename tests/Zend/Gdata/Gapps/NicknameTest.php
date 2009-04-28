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
 * @category     Zend
 * @package      Zend_Gdata
 * @subpackage   UnitTests
 * @copyright    Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com);
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

require_once 'Zend/Gdata/Gapps/Extension/Nickname.php';
require_once 'Zend/Gdata.php';

/**
 * @package    Zend_Gdata
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Gdata_Gapps_NicknameTest extends PHPUnit_Framework_TestCase
{

    public function setUp() {
        $this->nicknameText = file_get_contents(
                'Zend/Gdata/Gapps/_files/NicknameElementSample1.xml',
                true);
        $this->nickname = new Zend_Gdata_Gapps_Extension_Nickname();
    }

    public function testEmptyNicknameShouldHaveNoExtensionElements() {
        $this->assertTrue(is_array($this->nickname->extensionElements));
        $this->assertTrue(count($this->nickname->extensionElements) == 0);
    }

    public function testEmptyNicknameShouldHaveNoExtensionAttributes() {
        $this->assertTrue(is_array($this->nickname->extensionAttributes));
        $this->assertTrue(count($this->nickname->extensionAttributes) == 0);
    }

    public function testSampleNicknameShouldHaveNoExtensionElements() {
        $this->nickname->transferFromXML($this->nicknameText);
        $this->assertTrue(is_array($this->nickname->extensionElements));
        $this->assertTrue(count($this->nickname->extensionElements) == 0);
    }

    public function testSampleNicknameShouldHaveNoExtensionAttributes() {
        $this->nickname->transferFromXML($this->nicknameText);
        $this->assertTrue(is_array($this->nickname->extensionAttributes));
        $this->assertTrue(count($this->nickname->extensionAttributes) == 0);
    }

    public function testNormalNicknameShouldHaveNoExtensionElements() {
        $this->nickname->name = "Trogdor";

        $this->assertEquals("Trogdor", $this->nickname->name);

        $this->assertEquals(0, count($this->nickname->extensionElements));
        $newNickname = new Zend_Gdata_Gapps_Extension_Nickname();
        $newNickname->transferFromXML($this->nickname->saveXML());
        $this->assertEquals(0, count($newNickname->extensionElements));
        $newNickname->extensionElements = array(
                new Zend_Gdata_App_Extension_Element('foo', 'atom', null, 'bar'));
        $this->assertEquals(1, count($newNickname->extensionElements));
        $this->assertEquals("Trogdor", $newNickname->name);

        /* try constructing using magic factory */
        $gdata = new Zend_Gdata_Gapps();
        $newNickname2 = $gdata->newNickname();
        $newNickname2->transferFromXML($newNickname->saveXML());
        $this->assertEquals(1, count($newNickname2->extensionElements));
        $this->assertEquals("Trogdor", $newNickname2->name);
    }

    public function testEmptyNicknameToAndFromStringShouldMatch() {
        $nicknameXml = $this->nickname->saveXML();
        $newNickname = new Zend_Gdata_Gapps_Extension_Nickname();
        $newNickname->transferFromXML($nicknameXml);
        $newNicknameXml = $newNickname->saveXML();
        $this->assertTrue($nicknameXml == $newNicknameXml);
    }

    public function testNicknameWithValueToAndFromStringShouldMatch() {
        $this->nickname->name = "Trogdor";
        $nicknameXml = $this->nickname->saveXML();
        $newNickname = new Zend_Gdata_Gapps_Extension_Nickname();
        $newNickname->transferFromXML($nicknameXml);
        $newNicknameXml = $newNickname->saveXML();
        $this->assertTrue($nicknameXml == $newNicknameXml);
        $this->assertEquals("Trogdor", $this->nickname->name);
    }

    public function testExtensionAttributes() {
        $extensionAttributes = $this->nickname->extensionAttributes;
        $extensionAttributes['foo1'] = array('name'=>'foo1', 'value'=>'bar');
        $extensionAttributes['foo2'] = array('name'=>'foo2', 'value'=>'rab');
        $this->nickname->extensionAttributes = $extensionAttributes;
        $this->assertEquals('bar', $this->nickname->extensionAttributes['foo1']['value']);
        $this->assertEquals('rab', $this->nickname->extensionAttributes['foo2']['value']);
        $nicknameXml = $this->nickname->saveXML();
        $newNickname = new Zend_Gdata_Gapps_Extension_Nickname();
        $newNickname->transferFromXML($nicknameXml);
        $this->assertEquals('bar', $newNickname->extensionAttributes['foo1']['value']);
        $this->assertEquals('rab', $newNickname->extensionAttributes['foo2']['value']);
    }

    public function testConvertFullNicknameToAndFromString() {
        $this->nickname->transferFromXML($this->nicknameText);
        $this->assertEquals("Jones", $this->nickname->name);
    }

}
