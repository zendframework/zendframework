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

use PHPUnit_Framework_TestCase as TestCase;
use Zend\InputFilter\Input;
use Zend\Filter;
use Zend\Validator;

class InputTest extends TestCase
{
    /**
     * @var Input
     */
    protected $input;

    public function setUp()
    {
        $this->input = new Input('foo');
    }

    public function testConstructorRequiresAName()
    {
        $this->assertEquals('foo', $this->input->getName());
    }

    public function testInputHasEmptyFilterChainByDefault()
    {
        $filters = $this->input->getFilterChain();
        $this->assertInstanceOf('Zend\Filter\FilterChain', $filters);
        $this->assertEquals(0, count($filters));
    }

    public function testInputHasEmptyValidatorChainByDefault()
    {
        $validators = $this->input->getValidatorChain();
        $this->assertInstanceOf('Zend\Validator\ValidatorChain', $validators);
        $this->assertEquals(0, count($validators));
    }

    public function testCanInjectFilterChain()
    {
        $chain = new Filter\FilterChain();
        $this->input->setFilterChain($chain);
        $this->assertSame($chain, $this->input->getFilterChain());
    }

    public function testCanInjectValidatorChain()
    {
        $chain = new Validator\ValidatorChain();
        $this->input->setValidatorChain($chain);
        $this->assertSame($chain, $this->input->getValidatorChain());
    }

    public function testInputIsMarkedAsRequiredByDefault()
    {
        $this->assertTrue($this->input->isRequired());
    }

    public function testRequiredFlagIsMutable()
    {
        $this->input->setRequired(false);
        $this->assertFalse($this->input->isRequired());
    }

    public function testInputDoesNotAllowEmptyValuesByDefault()
    {
        $this->assertFalse($this->input->allowEmpty());
    }

    public function testAllowEmptyFlagIsMutable()
    {
        $this->input->setAllowEmpty(true);
        $this->assertTrue($this->input->allowEmpty());
    }

    public function testContinueIfEmptyFlagIsFalseByDefault()
    {
        $input = new Input('foo');
        $this->assertFalse($input->continueIfEmpty());
    }

    public function testContinueIfEmptyFlagIsMutable()
    {
        $input = new Input('foo');
        $input->setContinueIfEmpty(true);
        $this->assertTrue($input->continueIfEmpty());
    }

    public function testNotEmptyValidatorNotInjectedIfContinueIfEmptyIsTrue()
    {
        $input = new Input('foo');
        $input->setContinueIfEmpty(true);
        $input->setValue('');
        $input->isValid();
        $validators = $input->getValidatorChain()
                                ->getValidators();
        $this->assertTrue(0 == count($validators));
    }

    public function testValueIsNullByDefault()
    {
        $this->assertNull($this->input->getValue());
    }

    public function testValueMayBeInjected()
    {
        $this->input->setValue('bar');
        $this->assertEquals('bar', $this->input->getValue());
    }

    public function testRetrievingValueFiltersTheValue()
    {
        $this->input->setValue('bar');
        $filter = new Filter\StringToUpper();
        $this->input->getFilterChain()->attach($filter);
        $this->assertEquals('BAR', $this->input->getValue());
    }

    public function testCanRetrieveRawValue()
    {
        $this->input->setValue('bar');
        $filter = new Filter\StringToUpper();
        $this->input->getFilterChain()->attach($filter);
        $this->assertEquals('bar', $this->input->getRawValue());
    }

    public function testIsValidReturnsFalseIfValidationChainFails()
    {
        $this->input->setValue('bar');
        $validator = new Validator\Digits();
        $this->input->getValidatorChain()->attach($validator);
        $this->assertFalse($this->input->isValid());
    }

    public function testIsValidReturnsTrueIfValidationChainSucceeds()
    {
        $this->input->setValue('123');
        $validator = new Validator\Digits();
        $this->input->getValidatorChain()->attach($validator);
        $this->assertTrue($this->input->isValid());
    }

    public function testValidationOperatesOnFilteredValue()
    {
        $this->input->setValue(' 123 ');
        $filter = new Filter\StringTrim();
        $this->input->getFilterChain()->attach($filter);
        $validator = new Validator\Digits();
        $this->input->getValidatorChain()->attach($validator);
        $this->assertTrue($this->input->isValid());
    }

    public function testGetMessagesReturnsValidationMessages()
    {
        $this->input->setValue('bar');
        $validator = new Validator\Digits();
        $this->input->getValidatorChain()->attach($validator);
        $this->assertFalse($this->input->isValid());
        $messages = $this->input->getMessages();
        $this->assertArrayHasKey(Validator\Digits::NOT_DIGITS, $messages);
    }

    public function testSpecifyingMessagesToInputReturnsThoseOnFailedValidation()
    {
        $this->input->setValue('bar');
        $validator = new Validator\Digits();
        $this->input->getValidatorChain()->attach($validator);
        $this->input->setErrorMessage('Please enter only digits');
        $this->assertFalse($this->input->isValid());
        $messages = $this->input->getMessages();
        $this->assertArrayNotHasKey(Validator\Digits::NOT_DIGITS, $messages);
        $this->assertContains('Please enter only digits', $messages);
    }

    public function testBreakOnFailureFlagIsOffByDefault()
    {
        $this->assertFalse($this->input->breakOnFailure());
    }

    public function testBreakOnFailureFlagIsMutable()
    {
        $this->input->setBreakOnFailure(true);
        $this->assertTrue($this->input->breakOnFailure());
    }

    public function testNotEmptyValidatorAddedWhenIsValidIsCalled()
    {
        $this->assertTrue($this->input->isRequired());
        $this->input->setValue('');
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
        $this->input->setValue('');

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
        $input = new Input('foo');
        $input->setValue(' 123 ');
        $filter = new Filter\StringTrim();
        $input->getFilterChain()->attach($filter);
        $validator = new Validator\Digits();
        $input->getValidatorChain()->attach($validator);

        $input2 = new Input('bar');
        $input2->merge($input);
        $validatorChain = $input->getValidatorChain();
        $filterChain    = $input->getFilterChain();

        $this->assertEquals(' 123 ', $input2->getRawValue());
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
        $this->input->setValue('');

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
