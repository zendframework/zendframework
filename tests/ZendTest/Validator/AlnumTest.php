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

/**
 * @namespace
 */
namespace ZendTest\Validator;
use Zend\Validator;

/**
 * Test helper
 */

/**
 * @see Zend_Validate_Alnum
 */


/**
 * @category   Zend
 * @package    Zend_Validate
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Validate
 */
class AlnumTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Zend_Validate_Alnum object
     *
     * @var Zend_Validate_Alnum
     */
    protected $_validator;

    /**
     * Creates a new Zend_Validate_Alnum object for each test method
     *
     * @return void
     */
    public function setUp()
    {
        $this->_validator = new Validator\Alnum();
    }

    /**
     * Ensures that the validator follows expected behavior for basic input values
     *
     * @return void
     */
    public function testExpectedResultsWithBasicInputValues()
    {
        $valuesExpected = array(
            'abc123'  => true,
            'abc 123' => false,
            'abcxyz'  => true,
            'AZ@#4.3' => false,
            'aBc123'  => true,
            ''        => false,
            ' '       => false,
            "\n"      => false,
            'foobar1' => true
            );
        foreach ($valuesExpected as $input => $result) {
            $this->assertEquals($result, $this->_validator->isValid($input));
        }
    }

    /**
     * Ensures that getMessages() returns expected initial value
     *
     * @return void
     */
    public function testMessagesEmptyInitially()
    {
        $this->assertEquals(array(), $this->_validator->getMessages());
    }

    /**
     * Ensures that the allowWhiteSpace option works as expected
     *
     * @return void
     */
    public function testOptionToAllowWhiteSpaceWithBasicInputValues()
    {
        $this->_validator->setAllowWhiteSpace(true);

        $valuesExpected = array(
            'abc123'  => true,
            'abc 123' => true,
            'abcxyz'  => true,
            'AZ@#4.3' => false,
            'aBc123'  => true,
            ''        => false,
            ' '       => true,
            "\n"      => true,
            " \t "    => true,
            'foobar1' => true
            );
        foreach ($valuesExpected as $input => $result) {
            $this->assertEquals(
                $result,
                $this->_validator->isValid($input),
                "Expected '$input' to be considered " . ($result ? '' : 'in') . "valid"
                );
        }
    }

    /**
     * @return void
     */
    public function testEmptyStringValueResultsInProperValidationFailureMessages()
    {
        $this->assertFalse($this->_validator->isValid(''));
        $messages = $this->_validator->getMessages();
        $arrayExpected = array(
            Validator\Alnum::STRING_EMPTY => '\'\' is an empty string'
            );
        $this->assertThat($messages, $this->identicalTo($arrayExpected));
    }

    /**
     * @return void
     * @deprecated Since 1.5.0
     */
    public function testEmptyStringValueResultsInProperValidationFailureErrors()
    {
        $this->assertFalse($this->_validator->isValid(''));
        $errors = $this->_validator->getErrors();
        $arrayExpected = array(
            Validator\Alnum::STRING_EMPTY
            );
        $this->assertThat($errors, $this->identicalTo($arrayExpected));
    }

    /**
     * @return void
     */
    public function testInvalidValueResultsInProperValidationFailureMessages()
    {
        $this->assertFalse($this->_validator->isValid('#'));
        $messages = $this->_validator->getMessages();
        $arrayExpected = array(
            Validator\Alnum::NOT_ALNUM => '\'#\' contains characters which are non alphabetic and no digits'
            );
        $this->assertThat($messages, $this->identicalTo($arrayExpected));
    }

    /**
     * @return void
     * @deprecated Since 1.5.0
     */
    public function testInvalidValueResultsInProperValidationFailureErrors()
    {
        $this->assertFalse($this->_validator->isValid('#'));
        $errors = $this->_validator->getErrors();
        $arrayExpected = array(
            Validator\Alnum::NOT_ALNUM
            );
        $this->assertThat($errors, $this->identicalTo($arrayExpected));
    }

    /**
     * @ZF-4352
     */
    public function testNonStringValidation()
    {
        $this->assertFalse($this->_validator->isValid(array(1 => 1)));
    }

    /**
     * @ZF-7475
     */
    public function testIntegerValidation()
    {
        $this->assertTrue($this->_validator->isValid(1));
    }
}
