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

use Zend\Validator;


/**
 * @category   Zend
 * @package    Zend_Validator
 * @subpackage UnitTests
 * @group      Zend_Validator
 */
class StepTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Step object
     *
     * @var \Zend\Validator\Step
     */
    protected $_validator;

    /**
     * Creates a new Zend\Validator\Step object for each test method
     *
     * @return void
     */
    public function setUp()
    {
        $this->_validator = new Validator\Step();
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testBasic()
    {
        // By default, baseValue == 0 and step == 1
        $valuesExpected = array(
            array(1.00, true),
            array(0.00, true),
            array(2, true),
            array(3, true),
            array(2.1, false),
            array('2', true),
            array('1', true),
            array('1.2', false),
            array(1.01, false),
            array('not a scalar', false)
        );

        foreach ($valuesExpected as $element) {
            $this->assertEquals($element[1], $this->_validator->isValid($element[0]),
                'Test failed with ' . var_export($element, 1));
        }
    }

    public function testDecimalBaseValue()
    {
        $valuesExpected = array(
            array(1.1, false),
            array(0.1, true),
            array(2.1, true),
            array(3.1, false),
            array('2.1', true),
            array('1.1', false),
            array(1.11, false),
            array('not a scalar', false)
        );

        $validator = new Validator\Step(array(
            'baseValue' => 0.1,
            'step'      => 2
        ));

        foreach ($valuesExpected as $element) {
            $this->assertEquals($element[1], $validator->isValid($element[0]),
                'Test failed with ' . var_export($element, 1));
        }
    }

    public function testDecimalStep()
    {
        $valuesExpected = array(
            array(1.1, false),
            array(0.1, false),
            array(2.1, true),
            array(3.1, false),
            array(4.2, true),
            array(6.3, true),
            array(8.4, true),
            array(10.5, true),
            array(12.6, true),
            array(14.7, true),
            array(16.8, true),
            array(18.9, true),
            array(21.0, true),
            array('2.1', true),
            array('1.1', false),
            array(1.11, false),
            array('not a scalar', false)
        );

        $validator = new Validator\Step(array(
            'baseValue' => 0,
            'step'      => 2.1
        ));

        foreach ($valuesExpected as $element) {
            $this->assertEquals($element[1], $validator->isValid($element[0]),
                'Test failed with ' . var_export($element, 1));
        }
    }

    public function testDecimalStep2()
    {
        $valuesExpected = array(
            array(0.01, true),
            array(0.02, true),
            array(0.03, true),
            array(0.04, true),
            array(0.05, true),
            array(0.06, true),
            array(0.07, true),
            array(0.08, true),
            array(0.09, true),
            array(0.001, false),
            array(0.002, false),
            array(0.003, false),
            array(0.004, false),
            array(0.005, false),
            array(0.006, false),
            array(0.007, false),
            array(0.008, false),
            array(0.009, false)
        );

        $validator = new Validator\Step(array(
            'baseValue' => 0,
            'step'      => 0.01
        ));

        foreach ($valuesExpected as $element) {
            $this->assertEquals($element[1], $validator->isValid($element[0]),
                'Test failed with ' . var_export($element, 1));
        }
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
     * Ensures that set/getBaseValue() works
     */
    public function testCanSetBaseValue()
    {
        $this->_validator->setBaseValue(2);
        $this->assertEquals('2', $this->_validator->getBaseValue());
    }

    /**
     * Ensures that set/getStep() works
     */
    public function testCanSetStepValue()
    {
        $this->_validator->setStep(2);
        $this->assertEquals('2', $this->_validator->getStep());
    }

    public function testEqualsMessageTemplates()
    {
        $validator = new Validator\Step();
        $this->assertAttributeEquals($validator->getOption('messageTemplates'),
                                     'messageTemplates', $validator);
    }

    public function testSetStepFloat()
    {
        $step = 0.01;
        $this->_validator->setStep($step);
        $this->assertAttributeSame($step, 'step', $this->_validator);
    }

    public function testSetStepString()
    {
        $step = '0.01';
        $this->_validator->setStep($step);
        $this->assertAttributeSame((float) $step, 'step', $this->_validator);
    }
}
