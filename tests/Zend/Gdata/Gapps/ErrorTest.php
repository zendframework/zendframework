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
 * @package      Zend_Gdata_Gapps
 * @subpackage   UnitTests
 * @copyright    Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com);
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

require_once 'Zend/Gdata/Gapps/Error.php';
require_once 'Zend/Gdata/Gapps.php';

/**
 * @package Zend_Gdata
 * @subpackage UnitTests
 */
class Zend_Gdata_Gapps_ErrorTest extends PHPUnit_Framework_TestCase
{

    public function setUp() {
        $this->error = new Zend_Gdata_Gapps_Error();
    }

    public function testCanSetAndGetErrorCodeUsingConstant() {
        $this->error->setErrorCode(
            Zend_Gdata_Gapps_Error::INVALID_EMAIL_ADDRESS);
        $this->assertEquals(Zend_Gdata_Gapps_Error::INVALID_EMAIL_ADDRESS,
            $this->error->getErrorCode());
    }

    public function testCanSetAndGetErrorCodeUsingInteger() {
        $this->error->setErrorCode(123);
        $this->assertEquals(123, $this->error->getErrorCode());
    }

   public function testCanSetAndGetReason() {
        $text = "The foo is missing a bar.";
        $this->error->setReason($text);
        $this->assertEquals($text, $this->error->getReason());
    }

    public function testCanSetAndGetInvalidInput() {
         $text = "for___baz";
         $this->error->setInvalidInput($text);
         $this->assertEquals($text, $this->error->getInvalidInput());
    }

    public function testContstructorAllowsSettingAllVariables() {
        $this->error = new Zend_Gdata_Gapps_Error(
            Zend_Gdata_Gapps_Error::USER_DELETED_RECENTLY,
            "foo", "bar");
        $this->assertEquals(Zend_Gdata_Gapps_Error::USER_DELETED_RECENTLY,
            $this->error->getErrorCode());
        $this->assertEquals("foo", $this->error->getReason());
        $this->assertEquals("bar", $this->error->getInvalidInput());
    }

    public function testToStringProvidesHelpfulMessage() {
        $this->error->setErrorCode(Zend_Gdata_Gapps_Error::USER_SUSPENDED);
        $this->error->setReason("The foo is missing a bar.");
        $this->error->setInvalidInput("for___baz");
        $this->assertEquals("Error 1101: The foo is missing a bar.\n\tInvalid Input: \"for___baz\"", $this->error->__toString());

        $this->error->setErrorCode(Zend_Gdata_Gapps_Error::UNKNOWN_ERROR);
        $this->error->setReason("Unknown error.");
        $this->error->setInvalidInput("blah");
        $this->assertEquals("Error 1000: Unknown error.\n\tInvalid Input: \"blah\"", $this->error->__toString());
    }

}
