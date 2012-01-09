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
 * @package    Zend_Validator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Validator;
use Zend\Validator,
    ReflectionClass;

/**
 * Test helper
 */

/**
 * @see Zend_Validator_Regex
 */


/**
 * @category   Zend
 * @package    Zend_Validator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
            $validator = new Validator\Regex($element[0]);
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
        $validator = new Validator\Regex('/./');
        $this->assertEquals(array(), $validator->getMessages());
    }

    /**
     * Ensures that getPattern() returns expected value
     *
     * @return void
     */
    public function testGetPattern()
    {
        $validator = new Validator\Regex('/./');
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
        $validator = new Validator\Regex('/');
    }

    /**
     * @ZF-4352
     */
    public function testNonStringValidation()
    {
        $validator = new Validator\Regex('/./');
        $this->assertFalse($validator->isValid(array(1 => 1)));
    }

    /**
     * @ZF-11863
     */
    public function testSpecialCharValidation()
    {
        /**
         * The elements of each array are, in order:
         *      - pattern
         *      - expected validation result
         *      - array of test input values
         */
        $valuesExpected = array(
            array('/^[[:alpha:]\']+$/iu', true, array('test', 'òèùtestòò', 'testà', 'teààst', 'ààòòìùéé', 'èùòìiieeà')),
            array('/^[[:alpha:]\']+$/iu', false, array('test99'))
            );
        foreach ($valuesExpected as $element) {
            $validator = new Validator\Regex($element[0]);
            foreach ($element[2] as $input) {
                $this->assertEquals($element[1], $validator->isValid($input));
            }
        }
    }
    
    public function testEqualsMessageTemplates()
    {
        $validator = new Validator\Regex('//');
        $reflection = new ReflectionClass($validator);
        
        if(!$reflection->hasProperty('_messageTemplates')) {
            return;
        }
        
        $property = $reflection->getProperty('_messageTemplates');
        $property->setAccessible(true);

        $this->assertEquals(
            $property->getValue($validator),
            $validator->getOption('messageTemplates')
        );
    }
    
    public function testEqualsMessageVariables()
    {
        $validator = new Validator\Regex('//');
        $reflection = new ReflectionClass($validator);
        
        if(!$reflection->hasProperty('_messageVariables')) {
            return;
        }
        
        $property = $reflection->getProperty('_messageVariables');
        $property->setAccessible(true);

        $this->assertEquals(
            $property->getValue($validator),
            $validator->getOption('messageVariables')
        );
    }
}
