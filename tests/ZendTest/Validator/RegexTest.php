<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Validator
 */

namespace ZendTest\Validator;

use Zend\Validator\Regex;

/**
 * @category   Zend
 * @package    Zend_Validator
 * @subpackage UnitTests
 * @group      Zend_Validator
 */
class RegexTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testBasic()
    {
        /**
         * The elements of each array are, in order:
         *      - pattern
         *      - expected validation result
         *      - array of test input values
         */
        $valuesExpected = array(
            array('/[a-z]/', true, array('abc123', 'foo', 'a', 'z')),
            array('/[a-z]/', false, array('123', 'A'))
            );
        foreach ($valuesExpected as $element) {
            $validator = new Regex($element[0]);
            foreach ($element[2] as $input) {
                $this->assertEquals($element[1], $validator->isValid($input));
            }
        }
    }

    /**
     * Ensures that getMessages() returns expected default value
     *
     * @return void
     */
    public function testGetMessages()
    {
        $validator = new Regex('/./');
        $this->assertEquals(array(), $validator->getMessages());
    }

    /**
     * Ensures that getPattern() returns expected value
     *
     * @return void
     */
    public function testGetPattern()
    {
        $validator = new Regex('/./');
        $this->assertEquals('/./', $validator->getPattern());
    }

    /**
     * Ensures that a bad pattern results in a thrown exception upon isValid() call
     *
     * @return void
     */
    public function testBadPattern()
    {
        $this->setExpectedException('Zend\Validator\Exception\InvalidArgumentException', 'Internal error parsing');
        $validator = new Regex('/');
    }

    /**
     * @ZF-4352
     */
    public function testNonStringValidation()
    {
        $validator = new Regex('/./');
        $this->assertFalse($validator->isValid(array(1 => 1)));
    }

    /**
     * @ZF-11863
     * @dataProvider specialCharValidationProvider
     */
    public function testSpecialCharValidation($expected, $input)
    {
        // Locale changed due a bug with PHP versions lower than 5.3.4 (https://bugs.php.net/bug.php?id=52971)
        //setlocale(LC_ALL, 'Spanish_Spain', 'es_ES', 'es_ES.utf-8');
        if (version_compare(PHP_VERSION, '5.3.4', '<')) {
            $this->markTestIncomplete( // Skipped because Travis-CI PHP 5.3.3 don't allow set the locale
                "Test skipped because the PHP version is lower than 5.3.4 or the environment don't support quoted characters");
        }
        $validator = new Regex('/^[[:alpha:]\']+$/iu');
        $this->assertEquals($expected, $validator->isValid($input),
                            'Reason: ' . implode('', $validator->getMessages()));
    }

    /**
     * The elements of each array are, in order:
     *      - expected validation result
     *      - test input value
     */
    public function specialCharValidationProvider()
    {
        return array(
            array(true, 'test'),
            array(true, 'òèùtestòò'),
            array(true, 'testà'),
            array(true, 'teààst'),
            array(true, 'ààòòìùéé'),
            array(true, 'èùòìiieeà'),
            array(false, 'test99'),
        );
    }

    public function testEqualsMessageTemplates()
    {
        $validator = new Regex('//');
        $this->assertAttributeEquals($validator->getOption('messageTemplates'),
                                     'messageTemplates', $validator);
    }

    public function testEqualsMessageVariables()
    {
        $validator = new Regex('//');
        $this->assertAttributeEquals($validator->getOption('messageVariables'),
                                     'messageVariables', $validator);
    }
}
