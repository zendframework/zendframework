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
 * @copyright  Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Form\Element;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Form\Element\Checkbox as CheckboxElement;

class CheckboxTest extends TestCase
{
    public function testProvidesValidDefaultValues()
    {
        $element = new CheckboxElement();
        $this->assertEquals('1', $element->getCheckedValue());
        $this->assertEquals('0', $element->getUncheckedValue());
    }

    public function testProvidesInputSpecificationThatIncludesValidatorsBasedOnAttributes()
    {
        $element = new CheckboxElement();

        $inputSpec = $element->getInputSpecification();
        $this->assertArrayHasKey('validators', $inputSpec);
        $this->assertInternalType('array', $inputSpec['validators']);

        $expectedClasses = array(
            'Zend\Validator\InArray'
        );
        foreach ($inputSpec['validators'] as $validator) {
            $class = get_class($validator);
            $this->assertTrue(in_array($class, $expectedClasses), $class);
            switch ($class) {
                case 'Zend\Validator\InArray':
                    $this->assertEquals(array($element->getCheckedValue(), $element->getUncheckedValue()), $validator->getHaystack());
                    break;
                default:
                    break;
            }
        }
    }

    public function testIsChecked()
    {
        $element = new CheckboxElement();
        $this->assertEquals(false, $element->isChecked());
    }

    public function testSetAttributeValue()
    {
        $element = new CheckboxElement();
        $this->assertEquals(false, $element->isChecked());

        $element->setAttribute('value', 123);
        $this->assertEquals(false, $element->isChecked());

        $element->setAttribute('value', true);
        $this->assertEquals(true, $element->isChecked());
    }

    public function testIntegerCheckedValue()
    {
        $element = new CheckboxElement();
        $element->setCheckedValue(123);

        $this->assertEquals(false, $element->isChecked());

        $element->setAttribute('value', 123);
        $this->assertEquals(true, $element->isChecked());
    }

    public function testSetChecked()
    {
        $element = new CheckboxElement();
        $this->assertEquals(false, $element->isChecked());

        $element->setChecked(true);
        $this->assertEquals(true, $element->isChecked());

        $element->setChecked(false);
        $this->assertEquals(false, $element->isChecked());
    }

    public function testCheckWithCheckedValue()
    {
        $element = new CheckboxElement();
        $this->assertEquals(false, $element->isChecked());

        $element->setValue($element->getCheckedValue());
        $this->assertEquals(true, $element->isChecked());
    }

    public function testSetOptions()
    {
        $element = new CheckboxElement();
        $element->setOptions(array(
                                  'use_hidden_element' => true,
                                  'unchecked_value' => 'foo',
                                  'checked_value' => 'bar',
                             ));
        $this->assertEquals(true, $element->getOption('use_hidden_element'));
        $this->assertEquals('foo', $element->getOption('unchecked_value'));
        $this->assertEquals('bar', $element->getOption('checked_value'));
    }
}
