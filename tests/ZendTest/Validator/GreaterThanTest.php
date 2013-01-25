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

use Zend\Validator\GreaterThan;

/**
 * @category   Zend
 * @package    Zend_Validator
 * @subpackage UnitTests
 * @group      Zend_Validator
 */
class GreaterThanTest extends \PHPUnit_Framework_TestCase
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
         *      - minimum
         *      - expected validation result
         *      - array of test input values
         */
        $valuesExpected = array(
            array(0, true, array(0.01, 1, 100)),
            array(0, false, array(0, 0.00, -0.01, -1, -100)),
            array('a', true, array('b', 'c', 'd')),
            array('z', false, array('x', 'y', 'z')),
            array(array('min' => 0, 'inclusive' => true), true, array(0, 0.00, 0.01, 1, 100)),
            array(array('min' => 0, 'inclusive' => true), false, array(-0.01, -1, -100)),
            array(array('min' => 0, 'inclusive' => false), true, array(0.01, 1, 100)),
            array(array('min' => 0, 'inclusive' => false), false, array(0, 0.00, -0.01, -1, -100))
        );

        foreach ($valuesExpected as $element) {
            $validator = new GreaterThan($element[0]);
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
        $validator = new GreaterThan(10);
        $this->assertEquals(array(), $validator->getMessages());
    }

    /**
     * Ensures that getMin() returns expected value
     *
     * @return void
     */
    public function testGetMin()
    {
        $validator = new GreaterThan(10);
        $this->assertEquals(10, $validator->getMin());
    }

    /**
     * Ensures that getInclusive() returns expected default value
     *
     * @return void
     */
    public function testGetInclusive()
    {
        $validator = new GreaterThan(10);
        $this->assertEquals(false, $validator->getInclusive());
    }

    public function testEqualsMessageTemplates()
    {
        $validator = new GreaterThan(1);
        $this->assertAttributeEquals($validator->getOption('messageTemplates'),
                                     'messageTemplates', $validator);
    }

    public function testEqualsMessageVariables()
    {
        $validator = new GreaterThan(1);
        $this->assertAttributeEquals($validator->getOption('messageVariables'),
                                     'messageVariables', $validator);
    }
}
