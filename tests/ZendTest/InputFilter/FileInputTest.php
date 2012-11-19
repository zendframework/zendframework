<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_InputFilter
 */

namespace ZendTest\InputFilter;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\InputFilter\FileInput;
use Zend\Filter;
use Zend\Validator;

class FileInputTest extends TestCase
{
    public function testConstructorRequiresAName()
    {
        $input = new FileInput('foo');
        $this->assertEquals('foo', $input->getName());
    }

    public function testInputHasEmptyFilterChainByDefault()
    {
        $input = new FileInput('foo');
        $filters = $input->getFilterChain();
        $this->assertInstanceOf('Zend\Filter\FilterChain', $filters);
        $this->assertEquals(0, count($filters));
    }

    public function testInputHasEmptyValidatorChainByDefault()
    {
        $input = new FileInput('foo');
        $validators = $input->getValidatorChain();
        $this->assertInstanceOf('Zend\Validator\ValidatorChain', $validators);
        $this->assertEquals(0, count($validators));
    }

    public function testCanInjectFilterChain()
    {
        $input = new FileInput('foo');
        $chain = new Filter\FilterChain();
        $input->setFilterChain($chain);
        $this->assertSame($chain, $input->getFilterChain());
    }

    public function testCanInjectValidatorChain()
    {
        $input = new FileInput('foo');
        $chain = new Validator\ValidatorChain();
        $input->setValidatorChain($chain);
        $this->assertSame($chain, $input->getValidatorChain());
    }

    public function testInputIsMarkedAsRequiredByDefault()
    {
        $input = new FileInput('foo');
        $this->assertTrue($input->isRequired());
    }

    public function testRequiredFlagIsMutable()
    {
        $input = new FileInput('foo');
        $input->setRequired(false);
        $this->assertFalse($input->isRequired());
    }

    public function testInputDoesNotAllowEmptyValuesByDefault()
    {
        $input = new FileInput('foo');
        $this->assertFalse($input->allowEmpty());
    }

    public function testAllowEmptyFlagIsMutable()
    {
        $input = new FileInput('foo');
        $input->setAllowEmpty(true);
        $this->assertTrue($input->allowEmpty());
    }

    public function testValueIsNullByDefault()
    {
        $input = new FileInput('foo');
        $this->assertNull($input->getValue());
    }

    public function testValueMayBeInjected()
    {
        $input = new FileInput('foo');
        $input->setValue('bar');
        $this->assertEquals('bar', $input->getValue());
    }

    public function testRetrievingValueFiltersTheValueOnlyAfterValidating()
    {
        $input  = new FileInput('foo');
        $input->setValue('bar');
        $filter = new Filter\StringToUpper();
        $input->getFilterChain()->attach($filter);
        $this->assertEquals('bar', $input->getValue());
        $this->assertTrue($input->isValid());
        $this->assertEquals('BAR', $input->getValue());
    }

    public function testCanFilterArrayOfStrings()
    {
        $input  = new FileInput('foo');
        $values = array('foo', 'bar', 'baz');
        $input->setValue($values);
        $filter = new Filter\StringToUpper();
        $input->getFilterChain()->attach($filter);
        $this->assertEquals($values, $input->getValue());
        $this->assertTrue($input->isValid());
        $this->assertEquals(array('FOO', 'BAR', 'BAZ'), $input->getValue());
    }

    public function testCanFilterArrayOfFileData()
    {
        $input  = new FileInput('foo');
        $value  = array('tmp_name' => 'foo');
        $input->setValue($value);
        $filter = new Filter\StringToUpper();
        $input->getFilterChain()->attach($filter);
        $this->assertEquals('foo', $input->getValue());
        $this->assertTrue($input->isValid());
        $this->assertEquals('FOO', $input->getValue());
    }

    public function testCanFilterArrayOfMultiFileData()
    {
        $input  = new FileInput('foo');
        $values = array(
            array('tmp_name' => 'foo'),
            array('tmp_name' => 'bar'),
            array('tmp_name' => 'baz'),
        );
        $input->setValue($values);
        $filter = new Filter\StringToUpper();
        $input->getFilterChain()->attach($filter);
        $this->assertEquals(array('foo', 'bar', 'baz'), $input->getValue());
        $this->assertTrue($input->isValid());
        $this->assertEquals(array('FOO', 'BAR', 'BAZ'), $input->getValue());
    }

    public function testCanRetrieveRawValue()
    {
        $input  = new FileInput('foo');
        $input->setValue('bar');
        $filter = new Filter\StringToUpper();
        $input->getFilterChain()->attach($filter);
        $this->assertEquals('bar', $input->getRawValue());
    }

    public function testIsValidReturnsFalseIfValidationChainFails()
    {
        $input  = new FileInput('foo');
        $input->setValue('bar');
        $validator = new Validator\Digits();
        $input->getValidatorChain()->addValidator($validator);
        $this->assertFalse($input->isValid());
    }

    public function testIsValidReturnsTrueIfValidationChainSucceeds()
    {
        $input  = new FileInput('foo');
        $input->setValue('123');
        $validator = new Validator\Digits();
        $input->getValidatorChain()->addValidator($validator);
        $this->assertTrue($input->isValid());
    }

    public function testValidationOperatesBeforeFiltering()
    {
        $input  = new FileInput('foo');
        $input->setValue(' 123 ');
        $filter = new Filter\StringTrim();
        $input->getFilterChain()->attach($filter);
        $validator = new Validator\Digits();
        $input->getValidatorChain()->addValidator($validator);
        $this->assertFalse($input->isValid());
        $input->setValue('123');
        $this->assertTrue($input->isValid());
    }

    public function testGetMessagesReturnsValidationMessages()
    {
        $input  = new FileInput('foo');
        $input->setValue('bar');
        $validator = new Validator\Digits();
        $input->getValidatorChain()->addValidator($validator);
        $this->assertFalse($input->isValid());
        $messages = $input->getMessages();
        $this->assertArrayHasKey(Validator\Digits::NOT_DIGITS, $messages);
    }

    public function testSpecifyingMessagesToInputReturnsThoseOnFailedValidation()
    {
        $input = new FileInput('foo');
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
        $input = new FileInput('foo');
        $this->assertFalse($input->breakOnFailure());
    }

    public function testBreakOnFailureFlagIsMutable()
    {
        $input = new FileInput('foo');
        $input->setBreakOnFailure(true);
        $this->assertTrue($input->breakOnFailure());
    }

    public function testNotEmptyValidatorIsNotAddedWhenIsValidIsCalled()
    {
        $input = new FileInput('foo');
        $this->assertTrue($input->isRequired());
        $input->setValue('');
        $validatorChain = $input->getValidatorChain();
        $this->assertEquals(0, count($validatorChain->getValidators()));

        $this->assertTrue($input->isValid());
        $messages = $input->getMessages();
        $this->assertEquals(0, count($validatorChain->getValidators()));
    }

    public function testRequiredNotEmptyValidatorNotAddedWhenOneExists()
    {
        $input = new FileInput('foo');
        $this->assertTrue($input->isRequired());
        $input->setValue('');

        $notEmptyMock = $this->getMock('Zend\Validator\NotEmpty', array('isValid'));
        $notEmptyMock->expects($this->exactly(1))
                     ->method('isValid')
                     ->will($this->returnValue(false));

        $validatorChain = $input->getValidatorChain();
        $validatorChain->prependValidator($notEmptyMock);
        $this->assertFalse($input->isValid());

        $validators = $validatorChain->getValidators();
        $this->assertEquals(1, count($validators));
        $this->assertEquals($notEmptyMock, $validators[0]['instance']);
    }

    public function testMerge()
    {
        $input  = new FileInput('foo');
        $input->setValue(' 123 ');
        $filter = new Filter\StringTrim();
        $input->getFilterChain()->attach($filter);
        $validator = new Validator\Digits();
        $input->getValidatorChain()->addValidator($validator);

        $input2 = new FileInput('bar');
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
}
