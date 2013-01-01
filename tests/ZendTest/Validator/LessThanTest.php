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

use Zend\Validator\LessThan;

/**
 * @category   Zend
 * @package    Zend_Validator
 * @subpackage UnitTests
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
            $validator = new LessThan($element[0]);
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
        $validator = new LessThan(10);
        $this->assertEquals(array(), $validator->getMessages());
    }

    /**
     * Ensures that getMax() returns expected value
     *
     * @return void
     */
    public function testGetMax()
    {
        $validator = new LessThan(10);
        $this->assertEquals(10, $validator->getMax());
    }

    /**
     * Ensures that getInclusive() returns expected default value
     *
     * @return void
     */
    public function testGetInclusive()
    {
        $validator = new LessThan(10);
        $this->assertEquals(false, $validator->getInclusive());
    }

    public function testEqualsMessageTemplates()
    {
        $validator = new LessThan(10);
        $this->assertAttributeEquals($validator->getOption('messageTemplates'),
                                     'messageTemplates', $validator);
    }

    public function testEqualsMessageVariables()
    {
        $validator = new LessThan(10);
        $this->assertAttributeEquals($validator->getOption('messageVariables'),
                                     'messageVariables', $validator);
    }
}
