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
 * @see Zend_Validator_LessThan
 */


/**
 * @category   Zend
 * @package    Zend_Validator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Validator
 */
class LessThanTest extends \PHPUnit_Framework_TestCase
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
         *      - maximum
         *      - expected validation result
         *      - array of test input values
         */
        $valuesExpected = array(
            array(100, true, array(-1, 0, 0.01, 1, 99.999)),
            array(100, false, array(100, 100.0, 100.01)),
            array('a', false, array('a', 'b', 'c', 'd')),
            array('z', true, array('x', 'y')),
            array(array('max' => 100, 'inclusive' => true), true, array(-1, 0, 0.01, 1, 99.999, 100, 100.0)),
            array(array('max' => 100, 'inclusive' => true), false, array(100.01)),
            array(array('max' => 100, 'inclusive' => false), true, array(-1, 0, 0.01, 1, 99.999)),
            array(array('max' => 100, 'inclusive' => false), false, array(100, 100.0, 100.01))
        );

        foreach ($valuesExpected as $element) {
            $validator = new Validator\LessThan($element[0]);
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
        $validator = new Validator\LessThan(10);
        $this->assertEquals(array(), $validator->getMessages());
    }

    /**
     * Ensures that getMax() returns expected value
     *
     * @return void
     */
    public function testGetMax()
    {
        $validator = new Validator\LessThan(10);
        $this->assertEquals(10, $validator->getMax());
    }

    /**
     * Ensures that getInclusive() returns expected default value
     *
     * @return void
     */
    public function testGetInclusive()
    {
        $validator = new Validator\LessThan(10);
        $this->assertEquals(false, $validator->getInclusive());
    }
    
    public function testEqualsMessageTemplates()
    {
        $validator = new Validator\LessThan(10);
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
        $validator = new Validator\LessThan(10);
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
