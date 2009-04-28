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
 * @control     Zend
 * @package      Zend_Gdata_App
 * @subpackage UnitTests
 * @copyright    Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com);
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

require_once 'Zend/Gdata/App/Extension/Control.php';
require_once 'Zend/Gdata/App/Extension/Draft.php';
require_once 'Zend/Gdata/App.php';

/**
 * @package Zend_Gdata_App
 * @subpackage UnitTests
 */
class Zend_Gdata_App_ControlTest extends PHPUnit_Framework_TestCase
{

    public function setUp() {
        $this->controlText = file_get_contents(
                'Zend/Gdata/App/_files/ControlElementSample1.xml',
                true);
        $this->control = new Zend_Gdata_App_Extension_Control();
    }
      
    public function testEmptyControlShouldHaveEmptyExtensionsList() {
        $this->assertTrue(is_array($this->control->extensionElements));
        $this->assertTrue(count($this->control->extensionElements) == 0);
    }
      
    public function testEmptyControlToAndFromStringShouldMatch() {
        $controlXml = $this->control->saveXML();
        $newControl = new Zend_Gdata_App_Extension_Control();
        $newControl->transferFromXML($controlXml);
        $newControlXml = $newControl->saveXML();
        $this->assertTrue($controlXml == $newControlXml);
    }

    public function testControlWithDraftToAndFromStringShouldMatch() {
        $draft = new Zend_Gdata_App_Extension_Draft('yes');
        $this->control->draft = $draft;
        $controlXml = $this->control->saveXML();
        $newControl = new Zend_Gdata_App_Extension_Control();
        $newControl->transferFromXML($controlXml);
        $newControlXml = $newControl->saveXML();
        $this->assertEquals($newControlXml, $controlXml);
        $this->assertEquals('yes', $newControl->draft->text);
    }

    public function testConvertControlWithDraftToAndFromString() {
        $this->control->transferFromXML($this->controlText);
        $this->assertEquals('yes', $this->control->draft->text);
    }

}
