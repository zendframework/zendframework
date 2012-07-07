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

namespace ZendTest\Validator;

use Zend\Validator;


/**
 * @category   Zend
 * @package    Zend_Validator
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
}
