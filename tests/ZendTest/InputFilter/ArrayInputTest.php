<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_InputFilter
 */

namespace ZendTest\InputFilter;

use Zend\InputFilter\ArrayInput;
use Zend\Filter;
use Zend\Validator;

class ArrayInputTest extends InputTest
{
    public function setUp()
    {
        $this->input = new ArrayInput('foo');
    }

    public function testValueIsNullByDefault()
    {
        $this->markTestSkipped('Test is not enabled in ArrayInputTest');
    }

    public function testValueIsEmptyArrayByDefault()
    {
        $this->assertCount(0, $this->input->getValue());
    }

    public function testNotArrayValueCannotBeInjected()
    {
        $this->setExpectedException('Zend\InputFilter\Exception\InvalidArgumentException');
        $this->input->setValue('bar');
    }

    public function testValueMayBeInjected()
    {
        $this->input->setValue(array('bar'));
        $this->assertEquals(array('bar'), $this->input->getValue());
    }

    public function testRetrievingValueFiltersTheValue()
    {
        $this->input->setValue(array('bar'));
        $filter = new Filter\StringToUpper();
        $this->input->getFilterChain()->attach($filter);
        $this->assertEquals(array('BAR'), $this->input->getValue());
    }

    public function testCanRetrieveRawValue()
    {
        $this->input->setValue(array('bar'));
        $filter = new Filter\StringToUpper();
        $this->input->getFilterChain()->attach($filter);
        $this->assertEquals(array('bar'), $this->input->getRawValue());
    }

    public function testIsValidReturnsFalseIfValidationChainFails()
    {
        $this->input->setValue(array('123', 'bar'));
        $validator = new Validator\Digits();
        $this->input->getValidatorChain()->attach($validator);
        $this->assertFalse($this->input->isValid());
    }

    public function testIsValidReturnsTrueIfValidationChainSucceeds()
    {
        $this->input->setValue(array('123', '123'));
        $validator = new Validator\Digits();
        $this->input->getValidatorChain()->attach($validator);
        $this->assertTrue($this->input->isValid());
    }

    public function testValidationOperatesOnFilteredValue()
    {
        $this->input->setValue(array(' 123 ', '  123'));
        $filter = new Filter\StringTrim();
        $this->input->getFilterChain()->attach($filter);
        $validator = new Validator\Digits();
        $this->input->getValidatorChain()->attach($validator);
        $this->assertTrue($this->input->isValid());
    }

    public function testGetMessagesReturnsValidationMessages()
    {
        $this->input->setValue(array('bar'));
        $validator = new Validator\Digits();
        $this->input->getValidatorChain()->attach($validator);
        $this->assertFalse($this->input->isValid());
        $messages = $this->input->getMessages();
        $this->assertArrayHasKey(Validator\Digits::NOT_DIGITS, $messages);
    }

    public function testSpecifyingMessagesToInputReturnsThoseOnFailedValidation()
    {
        $this->input->setValue(array('bar'));
        $validator = new Validator\Digits();
        $this->input->getValidatorChain()->attach($validator);
        $this->input->setErrorMessage('Please enter only digits');
        $this->assertFalse($this->input->isValid());
        $messages = $this->input->getMessages();
        $this->assertArrayNotHasKey(Validator\Digits::NOT_DIGITS, $messages);
        $this->assertContains('Please enter only digits', $messages);
    }

    public function testNotEmptyValidatorAddedWhenIsValidIsCalled()
    {
        $this->assertTrue($this->input->isRequired());
        $this->input->setValue(array('bar', ''));
        $validatorChain = $this->input->getValidatorChain();
        $this->assertEquals(0, count($validatorChain->getValidators()));

        $this->assertFalse($this->input->isValid());
        $messages = $this->input->getMessages();
        $this->assertArrayHasKey('isEmpty', $messages);
        $this->assertEquals(1, count($validatorChain->getValidators()));

        // Assert that NotEmpty validator wasn't added again
        $this->assertFalse($this->input->isValid());
        $this->assertEquals(1, count($validatorChain->getValidators()));
    }

    public function testRequiredNotEmptyValidatorNotAddedWhenOneExists()
    {
        $this->assertTrue($this->input->isRequired());
        $this->input->setValue(array('bar', ''));

        $notEmptyMock = $this->getMock('Zend\Validator\NotEmpty', array('isValid'));
        $notEmptyMock->expects($this->exactly(1))
            ->method('isValid')
            ->will($this->returnValue(false));

        $validatorChain = $this->input->getValidatorChain();
        $validatorChain->prependValidator($notEmptyMock);
        $this->assertFalse($this->input->isValid());

        $validators = $validatorChain->getValidators();
        $this->assertEquals(1, count($validators));
        $this->assertEquals($notEmptyMock, $validators[0]['instance']);
    }

    public function testMerge()
    {
        $input = new ArrayInput('foo');
        $input->setValue(array(' 123 '));
        $filter = new Filter\StringTrim();
        $input->getFilterChain()->attach($filter);
        $validator = new Validator\Digits();
        $input->getValidatorChain()->attach($validator);

        $input2 = new ArrayInput('bar');
        $input2->merge($input);
        $validatorChain = $input->getValidatorChain();
        $filterChain    = $input->getFilterChain();

        $this->assertEquals(array(' 123 '), $input2->getRawValue());
        $this->assertEquals(1, $validatorChain->count());
        $this->assertEquals(1, $filterChain->count());

        $validators = $validatorChain->getValidators();
        $this->assertInstanceOf('Zend\Validator\Digits', $validators[0]['instance']);

        $filters = $filterChain->getFilters()->toArray();
        $this->assertInstanceOf('Zend\Filter\StringTrim', $filters[0]);
    }

    public function testDoNotInjectNotEmptyValidatorIfAnywhereInChain()
    {
        $this->assertTrue($this->input->isRequired());
        $this->input->setValue(array('bar', ''));

        $notEmptyMock = $this->getMock('Zend\Validator\NotEmpty', array('isValid'));
        $notEmptyMock->expects($this->exactly(1))
            ->method('isValid')
            ->will($this->returnValue(false));

        $validatorChain = $this->input->getValidatorChain();
        $validatorChain->attach(new Validator\Digits());
        $validatorChain->attach($notEmptyMock);
        $this->assertFalse($this->input->isValid());

        $validators = $validatorChain->getValidators();
        $this->assertEquals(2, count($validators));
        $this->assertEquals($notEmptyMock, $validators[1]['instance']);
    }
}
