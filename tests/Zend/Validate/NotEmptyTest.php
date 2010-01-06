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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
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
     * ZF-6708 introduces a change for validating integer 0; it is a valid
     * integer value. '0' is also valid.
     *
     * @group ZF-6708
     * @return void
     */
    public function testBasic()
    {
        $valuesExpected = array(
            array('word', true),
            array('', false),
            array('    ', false),
            array('  word  ', true),
            array('0', true),
            array(1, true),
            array(0, true),
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
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testOnlyBoolean()
    {
        $this->_validator->setType(Zend_Validate_NotEmpty::BOOLEAN);
        $this->assertFalse($this->_validator->isValid(false));
        $this->assertTrue($this->_validator->isValid(true));
        $this->assertTrue($this->_validator->isValid(0));
        $this->assertTrue($this->_validator->isValid(1));
        $this->assertTrue($this->_validator->isValid(0.0));
        $this->assertTrue($this->_validator->isValid(1.0));
        $this->assertTrue($this->_validator->isValid(''));
        $this->assertTrue($this->_validator->isValid('abc'));
        $this->assertTrue($this->_validator->isValid('0'));
        $this->assertTrue($this->_validator->isValid('1'));
        $this->assertTrue($this->_validator->isValid(array()));
        $this->assertTrue($this->_validator->isValid(array('xxx')));
        $this->assertTrue($this->_validator->isValid(null));
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testOnlyInteger()
    {
        $this->_validator->setType(Zend_Validate_NotEmpty::INTEGER);
        $this->assertTrue($this->_validator->isValid(false));
        $this->assertTrue($this->_validator->isValid(true));
        $this->assertFalse($this->_validator->isValid(0));
        $this->assertTrue($this->_validator->isValid(1));
        $this->assertTrue($this->_validator->isValid(0.0));
        $this->assertTrue($this->_validator->isValid(1.0));
        $this->assertTrue($this->_validator->isValid(''));
        $this->assertTrue($this->_validator->isValid('abc'));
        $this->assertTrue($this->_validator->isValid('0'));
        $this->assertTrue($this->_validator->isValid('1'));
        $this->assertTrue($this->_validator->isValid(array()));
        $this->assertTrue($this->_validator->isValid(array('xxx')));
        $this->assertTrue($this->_validator->isValid(null));
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testOnlyFloat()
    {
        $this->_validator->setType(Zend_Validate_NotEmpty::FLOAT);
        $this->assertTrue($this->_validator->isValid(false));
        $this->assertTrue($this->_validator->isValid(true));
        $this->assertTrue($this->_validator->isValid(0));
        $this->assertTrue($this->_validator->isValid(1));
        $this->assertFalse($this->_validator->isValid(0.0));
        $this->assertTrue($this->_validator->isValid(1.0));
        $this->assertTrue($this->_validator->isValid(''));
        $this->assertTrue($this->_validator->isValid('abc'));
        $this->assertTrue($this->_validator->isValid('0'));
        $this->assertTrue($this->_validator->isValid('1'));
        $this->assertTrue($this->_validator->isValid(array()));
        $this->assertTrue($this->_validator->isValid(array('xxx')));
        $this->assertTrue($this->_validator->isValid(null));
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testOnlyString()
    {
        $this->_validator->setType(Zend_Validate_NotEmpty::STRING);
        $this->assertTrue($this->_validator->isValid(false));
        $this->assertTrue($this->_validator->isValid(true));
        $this->assertTrue($this->_validator->isValid(0));
        $this->assertTrue($this->_validator->isValid(1));
        $this->assertTrue($this->_validator->isValid(0.0));
        $this->assertTrue($this->_validator->isValid(1.0));
        $this->assertFalse($this->_validator->isValid(''));
        $this->assertTrue($this->_validator->isValid('abc'));
        $this->assertTrue($this->_validator->isValid('0'));
        $this->assertTrue($this->_validator->isValid('1'));
        $this->assertTrue($this->_validator->isValid(array()));
        $this->assertTrue($this->_validator->isValid(array('xxx')));
        $this->assertTrue($this->_validator->isValid(null));
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testOnlyZero()
    {
        $this->_validator->setType(Zend_Validate_NotEmpty::ZERO);
        $this->assertTrue($this->_validator->isValid(false));
        $this->assertTrue($this->_validator->isValid(true));
        $this->assertTrue($this->_validator->isValid(0));
        $this->assertTrue($this->_validator->isValid(1));
        $this->assertTrue($this->_validator->isValid(0.0));
        $this->assertTrue($this->_validator->isValid(1.0));
        $this->assertTrue($this->_validator->isValid(''));
        $this->assertTrue($this->_validator->isValid('abc'));
        $this->assertFalse($this->_validator->isValid('0'));
        $this->assertTrue($this->_validator->isValid('1'));
        $this->assertTrue($this->_validator->isValid(array()));
        $this->assertTrue($this->_validator->isValid(array('xxx')));
        $this->assertTrue($this->_validator->isValid(null));
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testOnlyArray()
    {
        $this->_validator->setType(Zend_Validate_NotEmpty::EMPTY_ARRAY);
        $this->assertTrue($this->_validator->isValid(false));
        $this->assertTrue($this->_validator->isValid(true));
        $this->assertTrue($this->_validator->isValid(0));
        $this->assertTrue($this->_validator->isValid(1));
        $this->assertTrue($this->_validator->isValid(0.0));
        $this->assertTrue($this->_validator->isValid(1.0));
        $this->assertTrue($this->_validator->isValid(''));
        $this->assertTrue($this->_validator->isValid('abc'));
        $this->assertTrue($this->_validator->isValid('0'));
        $this->assertTrue($this->_validator->isValid('1'));
        $this->assertFalse($this->_validator->isValid(array()));
        $this->assertTrue($this->_validator->isValid(array('xxx')));
        $this->assertTrue($this->_validator->isValid(null));
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testOnlyNull()
    {
        $this->_validator->setType(Zend_Validate_NotEmpty::NULL);
        $this->assertTrue($this->_validator->isValid(false));
        $this->assertTrue($this->_validator->isValid(true));
        $this->assertTrue($this->_validator->isValid(0));
        $this->assertTrue($this->_validator->isValid(1));
        $this->assertTrue($this->_validator->isValid(0.0));
        $this->assertTrue($this->_validator->isValid(1.0));
        $this->assertTrue($this->_validator->isValid(''));
        $this->assertTrue($this->_validator->isValid('abc'));
        $this->assertTrue($this->_validator->isValid('0'));
        $this->assertTrue($this->_validator->isValid('1'));
        $this->assertTrue($this->_validator->isValid(array()));
        $this->assertTrue($this->_validator->isValid(array('xxx')));
        $this->assertFalse($this->_validator->isValid(null));
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testOnlyPHP()
    {
        $this->_validator->setType(Zend_Validate_NotEmpty::PHP);
        $this->assertFalse($this->_validator->isValid(false));
        $this->assertTrue($this->_validator->isValid(true));
        $this->assertFalse($this->_validator->isValid(0));
        $this->assertTrue($this->_validator->isValid(1));
        $this->assertFalse($this->_validator->isValid(0.0));
        $this->assertTrue($this->_validator->isValid(1.0));
        $this->assertFalse($this->_validator->isValid(''));
        $this->assertTrue($this->_validator->isValid('abc'));
        $this->assertFalse($this->_validator->isValid('0'));
        $this->assertTrue($this->_validator->isValid('1'));
        $this->assertFalse($this->_validator->isValid(array()));
        $this->assertTrue($this->_validator->isValid(array('xxx')));
        $this->assertFalse($this->_validator->isValid(null));
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testOnlySpace()
    {
        $this->_validator->setType(Zend_Validate_NotEmpty::SPACE);
        $this->assertTrue($this->_validator->isValid(false));
        $this->assertTrue($this->_validator->isValid(true));
        $this->assertTrue($this->_validator->isValid(0));
        $this->assertTrue($this->_validator->isValid(1));
        $this->assertTrue($this->_validator->isValid(0.0));
        $this->assertTrue($this->_validator->isValid(1.0));
        $this->assertTrue($this->_validator->isValid(''));
        $this->assertTrue($this->_validator->isValid('abc'));
        $this->assertTrue($this->_validator->isValid('0'));
        $this->assertTrue($this->_validator->isValid('1'));
        $this->assertTrue($this->_validator->isValid(array()));
        $this->assertTrue($this->_validator->isValid(array('xxx')));
        $this->assertTrue($this->_validator->isValid(null));
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testOnlyAll()
    {
        $this->_validator->setType(Zend_Validate_NotEmpty::ALL);
        $this->assertFalse($this->_validator->isValid(false));
        $this->assertTrue($this->_validator->isValid(true));
        $this->assertFalse($this->_validator->isValid(0));
        $this->assertTrue($this->_validator->isValid(1));
        $this->assertFalse($this->_validator->isValid(0.0));
        $this->assertTrue($this->_validator->isValid(1.0));
        $this->assertFalse($this->_validator->isValid(''));
        $this->assertTrue($this->_validator->isValid('abc'));
        $this->assertFalse($this->_validator->isValid('0'));
        $this->assertTrue($this->_validator->isValid('1'));
        $this->assertFalse($this->_validator->isValid(array()));
        $this->assertTrue($this->_validator->isValid(array('xxx')));
        $this->assertFalse($this->_validator->isValid(null));
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testArrayConstantNotation()
    {
        $filter = new Zend_Validate_NotEmpty(
            array(
                'type' => array(
                    Zend_Validate_NotEmpty::ZERO,
                    Zend_Validate_NotEmpty::STRING,
                    Zend_Validate_NotEmpty::BOOLEAN
                )
            )
        );

        $this->assertFalse($filter->isValid(false));
        $this->assertTrue($filter->isValid(true));
        $this->assertTrue($filter->isValid(0));
        $this->assertTrue($filter->isValid(1));
        $this->assertTrue($filter->isValid(0.0));
        $this->assertTrue($filter->isValid(1.0));
        $this->assertFalse($filter->isValid(''));
        $this->assertTrue($filter->isValid('abc'));
        $this->assertFalse($filter->isValid('0'));
        $this->assertTrue($filter->isValid('1'));
        $this->assertTrue($filter->isValid(array()));
        $this->assertTrue($filter->isValid(array('xxx')));
        $this->assertTrue($filter->isValid(null));
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testArrayConfigNotation()
    {
        $filter = new Zend_Validate_NotEmpty(
            array(
                'type' => array(
                    Zend_Validate_NotEmpty::ZERO,
                    Zend_Validate_NotEmpty::STRING,
                    Zend_Validate_NotEmpty::BOOLEAN),
                'test' => false
            )
        );

        $this->assertFalse($filter->isValid(false));
        $this->assertTrue($filter->isValid(true));
        $this->assertTrue($filter->isValid(0));
        $this->assertTrue($filter->isValid(1));
        $this->assertTrue($filter->isValid(0.0));
        $this->assertTrue($filter->isValid(1.0));
        $this->assertFalse($filter->isValid(''));
        $this->assertTrue($filter->isValid('abc'));
        $this->assertFalse($filter->isValid('0'));
        $this->assertTrue($filter->isValid('1'));
        $this->assertTrue($filter->isValid(array()));
        $this->assertTrue($filter->isValid(array('xxx')));
        $this->assertTrue($filter->isValid(null));
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testMultiConstantNotation()
    {
        $filter = new Zend_Validate_NotEmpty(
            Zend_Validate_NotEmpty::ZERO + Zend_Validate_NotEmpty::STRING + Zend_Validate_NotEmpty::BOOLEAN
        );

        $this->assertFalse($filter->isValid(false));
        $this->assertTrue($filter->isValid(true));
        $this->assertTrue($filter->isValid(0));
        $this->assertTrue($filter->isValid(1));
        $this->assertTrue($filter->isValid(0.0));
        $this->assertTrue($filter->isValid(1.0));
        $this->assertFalse($filter->isValid(''));
        $this->assertTrue($filter->isValid('abc'));
        $this->assertFalse($filter->isValid('0'));
        $this->assertTrue($filter->isValid('1'));
        $this->assertTrue($filter->isValid(array()));
        $this->assertTrue($filter->isValid(array('xxx')));
        $this->assertTrue($filter->isValid(null));
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testStringNotation()
    {
        $filter = new Zend_Validate_NotEmpty(
            array(
                'type' => array('zero', 'string', 'boolean')
            )
        );

        $this->assertFalse($filter->isValid(false));
        $this->assertTrue($filter->isValid(true));
        $this->assertTrue($filter->isValid(0));
        $this->assertTrue($filter->isValid(1));
        $this->assertTrue($filter->isValid(0.0));
        $this->assertTrue($filter->isValid(1.0));
        $this->assertFalse($filter->isValid(''));
        $this->assertTrue($filter->isValid('abc'));
        $this->assertFalse($filter->isValid('0'));
        $this->assertTrue($filter->isValid('1'));
        $this->assertTrue($filter->isValid(array()));
        $this->assertTrue($filter->isValid(array('xxx')));
        $this->assertTrue($filter->isValid(null));
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testSingleStringNotation()
    {
        $filter = new Zend_Validate_NotEmpty(
            'boolean'
        );

        $this->assertFalse($filter->isValid(false));
        $this->assertTrue($filter->isValid(true));
        $this->assertTrue($filter->isValid(0));
        $this->assertTrue($filter->isValid(1));
        $this->assertTrue($filter->isValid(0.0));
        $this->assertTrue($filter->isValid(1.0));
        $this->assertTrue($filter->isValid(''));
        $this->assertTrue($filter->isValid('abc'));
        $this->assertTrue($filter->isValid('0'));
        $this->assertTrue($filter->isValid('1'));
        $this->assertTrue($filter->isValid(array()));
        $this->assertTrue($filter->isValid(array('xxx')));
        $this->assertTrue($filter->isValid(null));
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testConfigObject()
    {
        require_once 'Zend/Config.php';
        $options = array('type' => 'all');
        $config  = new Zend_Config($options);

        $filter = new Zend_Validate_NotEmpty(
            $config
        );

        $this->assertFalse($filter->isValid(false));
        $this->assertTrue($filter->isValid(true));
        $this->assertFalse($filter->isValid(0));
        $this->assertTrue($filter->isValid(1));
        $this->assertFalse($filter->isValid(0.0));
        $this->assertTrue($filter->isValid(1.0));
        $this->assertFalse($filter->isValid(''));
        $this->assertTrue($filter->isValid('abc'));
        $this->assertFalse($filter->isValid('0'));
        $this->assertTrue($filter->isValid('1'));
        $this->assertFalse($filter->isValid(array()));
        $this->assertTrue($filter->isValid(array('xxx')));
        $this->assertFalse($filter->isValid(null));
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testSettingFalseType()
    {
        try {
            $this->_validator->setType(true);
            $this->fail();
        } catch (Zend_Exception $e) {
            $this->assertContains('Unknown', $e->getMessage());
        }
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testGetType()
    {
        $this->assertEquals(237, $this->_validator->getType());
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
