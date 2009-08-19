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

// Call Zend_Validate_NotEmptyTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Validate_NotEmptyTest::main");
}

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../TestHelper.php';

/**
 * @see Zend_Validate_NotEmpty
 */
require_once 'Zend/Validate/NotEmpty.php';


/**
 * @category   Zend
 * @package    Zend_Validate
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Validate
 */
class Zend_Validate_NotEmptyTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_Validate_NotEmptyTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Zend_Validate_NotEmpty object
     *
     * @var Zend_Validate_NotEmpty
     */
    protected $_validator;

    /**
     * Creates a new Zend_Validate_NotEmpty object for each test method
     *
     * @return void
     */
    public function setUp()
    {
        $this->_validator = new Zend_Validate_NotEmpty();
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testBasic()
    {
        $valuesExpected = array(
            array('word', true),
            array('', false),
            array('    ', false),
            array('  word  ', true),
            array(1, true),
            array(0, false),
            array(true, true),
            array(false, false),
            array(null, false),
            array(array(), false),
            array(array(5), true),
        );
        foreach ($valuesExpected as $i => $element) {
            $this->assertEquals($element[1], $this->_validator->isValid($element[0]),
                "Failed test #$i");
        }
    }

    /**
     * @see ZF-3236
     */
    public function testStringWithZeroShouldNotBeTreatedAsEmpty()
    {
        $this->assertTrue($this->_validator->isValid('0'));
    }

    /**
     * Ensures that getMessages() returns expected default value
     *
     * @return void
     */
    public function testGetMessages()
    {
        $this->assertEquals(array(), $this->_validator->getMessages());
    }

    /**
     * @ZF-4352
     */
    public function testNonStringValidation()
    {
        $v2 = new Zend_Validate_NotEmpty();
        $this->assertFalse($this->_validator->isValid($v2));
    }
}

// Call Zend_Validate_NotEmptyTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Validate_NotEmptyTest::main") {
    Zend_Validate_NotEmptyTest::main();
}
