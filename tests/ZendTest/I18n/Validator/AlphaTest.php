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

use Zend\I18n\Validator\Alpha as AlphaValidator;

/**
 * @category   Zend
 * @package    Zend_Validator
 * @subpackage UnitTests
 * @group      Zend_Validator
 */
class AlphaTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AlphaValidator
     */
    protected $validator;

    public function setUp()
    {
        $this->validator = new AlphaValidator();
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testBasic()
    {
        $valuesExpected = array(
            'abc123'  => false,
            'abc 123' => false,
            'abcxyz'  => true,
            'AZ@#4.3' => false,
            'aBc123'  => false,
            'aBcDeF'  => true,
            ''        => false,
            ' '       => false,
            "\n"      => false
            );
        foreach ($valuesExpected as $input => $result) {
            $this->assertEquals($result, $this->validator->isValid($input));
        }
    }

    /**
     * Ensures that getMessages() returns expected default value
     *
     * @return void
     */
    public function testGetMessages()
    {
        $this->assertEquals(array(), $this->validator->getMessages());
    }

    /**
     * Ensures that the allowWhiteSpace option works as expected
     *
     * @return void
     */
    public function testAllowWhiteSpace()
    {
        $this->validator->setAllowWhiteSpace(true);

        $valuesExpected = array(
            'abc123'  => false,
            'abc 123' => false,
            'abcxyz'  => true,
            'AZ@#4.3' => false,
            'aBc123'  => false,
            'aBcDeF'  => true,
            ''        => false,
            ' '       => true,
            "\n"      => true,
            " \t "    => true,
            "a\tb c"  => true
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
     * @ZF-4352
     */
    public function testNonStringValidation()
    {
        $this->assertFalse($this->validator->isValid(array(1 => 1)));
    }

    public function testEqualsMessageTemplates()
    {
        $validator = $this->validator;
        $this->assertAttributeEquals($validator->getOption('messageTemplates'),
                                     'messageTemplates', $validator);
    }
}
