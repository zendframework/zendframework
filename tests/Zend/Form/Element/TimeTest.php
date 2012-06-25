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
 * @package    Zend_Form
 * @subpackage UnitTest
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Form\Element;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Form\Element\Time as TimeElement;
use Zend\Form\Factory;

class TimeTest extends TestCase
{
    public function setUp()
    {
        $this->element = new TimeElement('foo');
        parent::setUp();
    }

    public function getAttributesAndValidatorsDataProvider()
    {
        return array(
            array(
                array('min' => true, 'max' => true, 'step' => true),
                array(
                    'Zend\Validator\Date',
                    'Zend\Validator\GreaterThan',
                    'Zend\Validator\LessThan',
                    'Zend\Validator\DateStep'
                ),
            ),
            array(
                array('max' => true, 'step' => true),
                array(
                    'Zend\Validator\Date',
                    'Zend\Validator\LessThan',
                    'Zend\Validator\DateStep'
                ),
            ),
            array(
                array('min' => true, 'step' => true),
                array(
                    'Zend\Validator\Date',
                    'Zend\Validator\GreaterThan',
                    'Zend\Validator\DateStep'
                ),
            ),
            array(
                array('min' => true, 'max' => true, 'step' => 'any'),
                array(
                    'Zend\Validator\Date',
                    'Zend\Validator\GreaterThan',
                    'Zend\Validator\LessThan',
                ),
            ),
            array(
                array(),
                array(
                    'Zend\Validator\Date',
                    'Zend\Validator\DateStep'
                ),
            ),
        );
    }

    /**
     * @dataProvider getAttributesAndValidatorsDataProvider
     */
    public function testLazyLoadsValidatorsByDefault($attributes, $validatorClasses)
    {
        $this->element->setAttributes($attributes);
        $validators = $this->element->getValidators();
        foreach ($validators as $i => $validator) {
            $this->assertInstanceOf($validatorClasses[$i], $validator);
        }
    }

    public function testCanInjectValidator()
    {
        $validator  = $this->getMock('Zend\Validator\ValidatorInterface');
        $this->element->setValidators(array($validator));
        $validators = $this->element->getValidators();
        $this->assertSame($validator, array_shift($validators));
    }

    public function testProvidesInputSpecificationThatIncludesValidator()
    {
        $validator = $this->getMock('Zend\Validator\ValidatorInterface');
        $this->element->setValidators(array($validator));

        $inputSpec = $this->element->getInputSpecification();
        $this->assertArrayHasKey('validators', $inputSpec);
        $this->assertInternalType('array', $inputSpec['validators']);
        $test = array_shift($inputSpec['validators']);
        $this->assertSame($validator, $test);
    }
}
