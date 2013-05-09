<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_I18n
 */

namespace ZendTest\Validator;

use Zend\I18n\Validator\Alnum as AlnumValidator;

/**
 * @category   Zend
 * @package    Zend_Validator
 * @subpackage UnitTests
 * @group      Zend_Validator
 */
class AlnumTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AlnumValidator
     */
    protected $validator;

    /**
     * Creates a new Alnum object for each test method
     *
     * @return void
     */
    public function setUp()
    {
        $this->validator = new AlnumValidator();
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
            $this->assertEquals($result, $this->validator->isValid($input));
        }
    }

    /**
     * Ensures that getMessages() returns expected initial value
     *
     * @return void
     */
    public function testMessagesEmptyInitially()
    {
        $this->assertEquals(array(), $this->validator->getMessages());
    }

    /**
     * Ensures that the allowWhiteSpace option works as expected
     *
     * @return void
     */
    public function testOptionToAllowWhiteSpaceWithBasicInputValues()
    {
        $this->validator->setAllowWhiteSpace(true);

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
                $this->validator->isValid($input),
                "Expected '$input' to be considered " . ($result ? '' : 'in') . "valid"
                );
        }
    }

    /**
     * @return void
     */
    public function testEmptyStringValueResultsInProperValidationFailureMessages()
    {
        $this->assertFalse($this->validator->isValid(''));

        $messages = $this->validator->getMessages();
        $arrayExpected = array(
            AlnumValidator::STRING_EMPTY => 'The input is an empty string'
        );
        $this->assertThat($messages, $this->identicalTo($arrayExpected));
    }

    /**
     * @return void
     */
    public function testInvalidValueResultsInProperValidationFailureMessages()
    {
        $this->assertFalse($this->validator->isValid('#'));
        $messages = $this->validator->getMessages();
        $arrayExpected = array(
            AlnumValidator::NOT_ALNUM => 'The input contains characters which are non alphabetic and no digits'
        );
        $this->assertThat($messages, $this->identicalTo($arrayExpected));
    }

    /**
     * @ZF-4352
     */
    public function testNonStringValidation()
    {
        $this->assertFalse($this->validator->isValid(array(1 => 1)));
    }

    /**
     * @ZF-7475
     */
    public function testIntegerValidation()
    {
        $this->assertTrue($this->validator->isValid(1));
    }

    public function testEqualsMessageTemplates()
    {
        $validator = $this->validator;
        $this->assertAttributeEquals($validator->getOption('messageTemplates'),
                                     'messageTemplates', $validator);
    }
}
