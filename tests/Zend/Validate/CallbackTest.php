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
 * @package    Zend_Validate
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Validate_CallbackTest::main');
}

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../TestHelper.php';

/**
 * @see Zend_Validate_Callback
 */
require_once 'Zend/Validate/Callback.php';

/**
 * @category   Zend
 * @package    Zend_Validate
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Validate
 */
class Zend_Validate_CallbackTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs this test suite
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite('Zend_Validate_CallbackTest');
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testBasic()
    {
        $valid = new Zend_Validate_Callback(array($this, 'objectCallback'));
        $this->assertTrue($valid->isValid('test'));
    }

    public function testStaticCallback()
    {
        $valid = new Zend_Validate_Callback(
            array('Zend_Validate_CallbackTest', 'staticCallback')
        );
        $this->assertTrue($valid->isValid('test'));
    }

    public function testSettingDefaultOptionsAfterwards()
    {
        $valid = new Zend_Validate_Callback(array($this, 'objectCallback'));
        $valid->setOptions('options');
        $this->assertEquals(array('options'), $valid->getOptions());
        $this->assertTrue($valid->isValid('test'));
    }

    public function testSettingDefaultOptions()
    {
        $valid = new Zend_Validate_Callback(array('callback' => array($this, 'objectCallback'), 'options' => 'options'));
        $this->assertEquals(array('options'), $valid->getOptions());
        $this->assertTrue($valid->isValid('test'));
    }

    public function testGettingCallback()
    {
        $valid = new Zend_Validate_Callback(array($this, 'objectCallback'));
        $this->assertEquals(array($this, 'objectCallback'), $valid->getCallback());
    }

    public function testInvalidCallback()
    {
        $valid = new Zend_Validate_Callback(array($this, 'objectCallback'));
        try {
            $valid->setCallback('invalidcallback');
            $this->fail('Exception expected');
        } catch (Zend_Exception $e) {
            $this->assertContains('Invalid callback given', $e->getMessage());
        }
    }

    public function objectCallback($value)
    {
        return true;
    }

    public static function staticCallback($value)
    {
        return true;
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Validate_CallbackTest::main') {
    Zend_Validate_CallbackTest::main();
}
