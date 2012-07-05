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
 * @package    Zend_InputFilter
 * @subpackage UnitTest
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\InputFilter;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\InputFilter\Input;
use Zend\Filter;
use Zend\Validator;

class InputTest extends TestCase
{
    public function testConstructorRequiresAName()
    {
        $input = new Input('foo');
        $this->assertEquals('foo', $input->getName());
    }

    public function testInputHasEmptyFilterChainByDefault()
    {
        $input = new Input('foo');
        $filters = $input->getFilterChain();
        $this->assertInstanceOf('Zend\Filter\FilterChain', $filters);
        $this->assertEquals(0, count($filters));
    }

    public function testInputHasEmptyValidatorChainByDefault()
    {
        $input = new Input('foo');
        $validators = $input->getValidatorChain();
        $this->assertInstanceOf('Zend\Validator\ValidatorChain', $validators);
        $this->assertEquals(0, count($validators));
    }

    public function testCanInjectFilterChain()
    {
        $input = new Input('foo');
        $chain = new Filter\FilterChain();
        $input->setFilterChain($chain);
        $this->assertSame($chain, $input->getFilterChain());
    }

    public function testCanInjectValidatorChain()
    {
        $input = new Input('foo');
        $chain = new Validator\ValidatorChain();
        $input->setValidatorChain($chain);
        $this->assertSame($chain, $input->getValidatorChain());
    }

    public function testInputIsMarkedAsRequiredByDefault()
    {
        $input = new Input('foo');
        $this->assertTrue($input->isRequired());
    }

    public function testRequiredFlagIsMutable()
    {
        $input = new Input('foo');
        $input->setRequired(false);
        $this->assertFalse($input->isRequired());
    }

    public function testInputDoesNotAllowEmptyValuesByDefault()
    {
        $input = new Input('foo');
        $this->assertFalse($input->allowEmpty());
    }

    public function testAllowEmptyFlagIsMutable()
    {
        $input = new Input('foo');
        $input->setAllowEmpty(true);
        $this->assertTrue($input->allowEmpty());
    }

    public function testValueIsNullByDefault()
    {
        $input = new Input('foo');
        $this->assertNull($input->getValue());
    }

    public function testValueMayBeInjected()
    {
        $input = new Input('foo');
        $input->setValue('bar');
        $this->assertEquals('bar', $input->getValue());
    }

    public function testRetrievingValueFiltersTheValue()
    {
        $input  = new Input('foo');
        $input->setValue('bar');
        $filter = new Filter\StringToUpper();
        $input->getFilterChain()->attach($filter);
        $this->assertEquals('BAR', $input->getValue());
    }

    public function testCanRetrieveRawValue()
    {
        $input  = new Input('foo');
        $input->setValue('bar');
        $filter = new Filter\StringToUpper();
        $input->getFilterChain()->attach($filter);
        $this->assertEquals('bar', $input->getRawValue());
    }

    public function testIsValidReturnsFalseIfValidationChainFails()
    {
        $input  = new Input('foo');
        $input->setValue('bar');
        $validator = new Validator\Digits();
        $input->getValidatorChain()->addValidator($validator);
        $this->assertFalse($input->isValid());
    }

    public function testIsValidReturnsTrueIfValidationChainSucceeds()
    {
        $input  = new Input('foo');
        $input->setValue('123');
        $validator = new Validator\Digits();
        $input->getValidatorChain()->addValidator($validator);
        $this->assertTrue($input->isValid());
    }

    public function testValidationOperatesOnFilteredValue()
    {
        $input  = new Input('foo');
        $input->setValue(' 123 ');
        $filter = new Filter\StringTrim();
        $input->getFilterChain()->attach($filter);
        $validator = new Validator\Digits();
        $input->getValidatorChain()->addValidator($validator);
        $this->assertTrue($input->isValid());
    }

    public function testGetMessagesReturnsValidationMessages()
    {
        $input  = new Input('foo');
        $input->setValue('bar');
        $validator = new Validator\Digits();
        $input->getValidatorChain()->addValidator($validator);
        $this->assertFalse($input->isValid());
        $messages = $input->getMessages();
        $this->assertArrayHasKey(Validator\Digits::NOT_DIGITS, $messages);
    }

    public function testSpecifyingMessagesToInputReturnsThoseOnFailedValidation()
    {
        $input = new Input('foo');
        $input->setValue('bar');
        $validator = new Validator\Digits();
        $input->getValidatorChain()->addValidator($validator);
        $input->setErrorMessage('Please enter only digits');
        $this->assertFalse($input->isValid());
        $messages = $input->getMessages();
        $this->assertArrayNotHasKey(Validator\Digits::NOT_DIGITS, $messages);
        $this->assertContains('Please enter only digits', $messages);
    }

    public function testBreakOnFailureFlagIsOffByDefault()
    {
        $input = new Input('foo');
        $this->assertFalse($input->breakOnFailure());
    }

    public function testBreakOnFailureFlagIsMutable()
    {
        $input = new Input('foo');
        $input->setBreakOnFailure(true);
        $this->assertTrue($input->breakOnFailure());
    }

    public function testNotEmptyValidatorAddedWhenIsValidIsCalled()
    {
        $input = new Input('foo');
        $this->assertTrue($input->isRequired());
        $input->setValue('');
        $this->assertFalse($input->isValid());
        $messages = $input->getMessages();
        $this->assertArrayHasKey('isEmpty', $messages);
    }
}
