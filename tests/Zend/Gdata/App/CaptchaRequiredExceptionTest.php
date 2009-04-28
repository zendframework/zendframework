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
 * @package      Zend_Gdata_App
 * @subpackage UnitTests
 * @copyright    Copyright (c) 2006 Zend Technologies USA Inc. (http://www.zend.com);
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

 require_once 'Zend/Gdata/App/CaptchaRequiredException.php';
 
 /**
 * @package Zend_Gdata_App
 * @subpackage UnitTests
 */
class Zend_Gdata_App_CaptchaRequiredExceptionTest extends PHPUnit_Framework_TestCase
{

    public function setUp() {
        $this->exampleException = new Zend_Gdata_App_CaptchaRequiredException('testtoken', 'Captcha?ctoken=testtoken');
    }
      
    public function testExceptionContainsValidInformation() {
        $this->assertEquals('testtoken', $this->exampleException->getCaptchaToken());
        $this->assertEquals('https://www.google.com/accounts/Captcha?ctoken=testtoken', $this->exampleException->getCaptchaUrl());
    }
    
    public function testExceptionIsThrowable() {
        $caught = false;
        try {
            throw $this->exampleException;
        }
        catch(Zend_Gdata_App_CaptchaRequiredException $e) {
            $caught = true;
        }
        
        $this->assertTrue($caught);
    }

}
