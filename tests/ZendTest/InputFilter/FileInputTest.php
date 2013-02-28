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
use Zend\InputFilter\FileInput;
use Zend\Filter;
use Zend\Validator;

class FileInputTest extends TestCase
{
    public function setUp()
    {
        $this->input = new FileInput('foo');
        // Upload validator does not work in CLI test environment, disable
        $this->input->setAutoPrependUploadValidator(false);
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

    public function testValueIsNullByDefault()
    {
        $this->assertNull($this->input->getValue());
    }

    public function testValueMayBeInjected()
    {
        $value = array('tmp_name' => 'bar');
        $this->input->setValue($value);
        $this->assertEquals($value, $this->input->getValue());
    }

    public function testRetrievingValueFiltersTheValueOnlyAfterValidating()
    {
        $value = array('tmp_name' => 'bar');
        $this->input->setValue($value);

        $newValue = array('tmp_name' => 'foo');
        $filterMock = $this->getMockBuilder('Zend\Filter\File\Rename')
            ->disableOriginalConstructor()
            ->getMock();
        $filterMock->expects($this->any())
            ->method('filter')
            ->will($this->returnValue($newValue));

        // Why not attach mocked filter directly?
        // No worky without wrapping in a callback.
        // Missing something in mock setup?
        $this->input->getFilterChain()->attach(
            function ($value) use ($filterMock) {
                return $filterMock->filter($value);
            }
        );

        $this->assertEquals($value, $this->input->getValue());
        $this->assertTrue($this->input->isValid());
        $this->assertEquals($newValue, $this->input->getValue());
    }

    public function testCanFilterArrayOfMultiFileData()
    {
        $values = array(
            array('tmp_name' => 'foo'),
            array('tmp_name' => 'bar'),
            array('tmp_name' => 'baz'),
        );
        $this->input->setValue($values);

        $newValue = array('tmp_name' => 'new');
        $filterMock = $this->getMockBuilder('Zend\Filter\File\Rename')
            ->disableOriginalConstructor()
            ->getMock();
        $filterMock->expects($this->any())
            ->method('filter')
            ->will($this->returnValue($newValue));

        // Why not attach mocked filter directly?
        // No worky without wrapping in a callback.
        // Missing something in mock setup?
        $this->input->getFilterChain()->attach(
            function ($value) use ($filterMock) {
                return $filterMock->filter($value);
            }
        );

        $this->assertEquals($values, $this->input->getValue());
        $this->assertTrue($this->input->isValid());
        $this->assertEquals(
            array($newValue, $newValue, $newValue),
            $this->input->getValue()
        );
    }

    public function testCanRetrieveRawValue()
    {
        $value = array('tmp_name' => 'bar');
        $this->input->setValue($value);
        $filter = new Filter\StringToUpper();
        $this->input->getFilterChain()->attach($filter);
        $this->assertEquals($value, $this->input->getRawValue());
    }

    public function testIsValidReturnsFalseIfValidationChainFails()
    {
        $this->input->setValue(array('tmp_name' => 'bar'));
        $validator = new Validator\Digits();
        $this->input->getValidatorChain()->attach($validator);
        $this->assertFalse($this->input->isValid());
    }

    public function testIsValidReturnsTrueIfValidationChainSucceeds()
    {
        $this->input->setValue(array('tmp_name' => 'bar'));
        $validator = new Validator\NotEmpty();
        $this->input->getValidatorChain()->attach($validator);
        $this->assertTrue($this->input->isValid());
    }

    public function testValidationOperatesBeforeFiltering()
    {
        $badValue = array(
            'tmp_name' => ' ' . __FILE__ . ' ',
            'name'     => 'foo',
            'size'     => 1,
            'error'    => 0,
        );
        $this->input->setValue($badValue);

        $filteredValue = array('tmp_name' => 'new');
        $filterMock = $this->getMockBuilder('Zend\Filter\File\Rename')
            ->disableOriginalConstructor()
            ->getMock();
        $filterMock->expects($this->any())
            ->method('filter')
            ->will($this->returnValue($filteredValue));

        // Why not attach mocked filter directly?
        // No worky without wrapping in a callback.
        // Missing something in mock setup?
        $this->input->getFilterChain()->attach(
            function ($value) use ($filterMock) {
                return $filterMock->filter($value);
            }
        );

        $validator = new Validator\File\Exists();
        $this->input->getValidatorChain()->attach($validator);
        $this->assertFalse($this->input->isValid());
        $this->assertEquals($badValue, $this->input->getValue());

        $goodValue = array(
            'tmp_name' => __FILE__,
            'name'     => 'foo',
            'size'     => 1,
            'error'    => 0,
        );
        $this->input->setValue($goodValue);
        $this->assertTrue($this->input->isValid());
        $this->assertEquals($filteredValue, $this->input->getValue());
    }

    public function testGetMessagesReturnsValidationMessages()
    {
        $this->input->setAutoPrependUploadValidator(true);
        $this->input->setValue(array(
            'tmp_name' => __FILE__,
            'name'     => 'foo',
            'size'     => 1,
            'error'    => 0,
        ));
        $this->assertFalse($this->input->isValid());
        $messages = $this->input->getMessages();
        $this->assertArrayHasKey(Validator\File\UploadFile::ATTACK, $messages);
    }

    public function testCanValidateArrayOfMultiFileData()
    {
        $values = array(
            array(
                'tmp_name' => __FILE__,
                'name'     => 'foo',
            ),
            array(
                'tmp_name' => __FILE__,
                'name'     => 'bar',
            ),
            array(
                'tmp_name' => __FILE__,
                'name'     => 'baz',
            ),
        );
        $this->input->setValue($values);
        $validator = new Validator\File\Exists();
        $this->input->getValidatorChain()->attach($validator);
        $this->assertTrue($this->input->isValid());

        // Negative test
        $values[1]['tmp_name'] = 'file-not-found';
        $this->input->setValue($values);
        $this->assertFalse($this->input->isValid());
    }

    public function testSpecifyingMessagesToInputReturnsThoseOnFailedValidation()
    {
        $this->input->setValue(array('tmp_name' => 'bar'));
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

    public function testAutoPrependUploadValidatorIsOnByDefault()
    {
        $input = new FileInput('foo');
        $this->assertTrue($input->getAutoPrependUploadValidator());
    }

    public function testUploadValidatorIsAddedWhenIsValidIsCalled()
    {
        $this->input->setAutoPrependUploadValidator(true);
        $this->assertTrue($this->input->getAutoPrependUploadValidator());
        $this->assertTrue($this->input->isRequired());
        $this->input->setValue(array(
            'tmp_name' => __FILE__,
            'name'     => 'foo',
            'size'     => 1,
            'error'    => 0,
        ));
        $validatorChain = $this->input->getValidatorChain();
        $this->assertEquals(0, count($validatorChain->getValidators()));

        $this->assertFalse($this->input->isValid());
        $validators = $validatorChain->getValidators();
        $this->assertEquals(1, count($validators));
        $this->assertInstanceOf('Zend\Validator\File\UploadFile', $validators[0]['instance']);
    }

    public function testUploadValidatorIsNotAddedWhenIsValidIsCalled()
    {
        $this->assertFalse($this->input->getAutoPrependUploadValidator());
        $this->assertTrue($this->input->isRequired());
        $this->input->setValue(array('tmp_name' => 'bar'));
        $validatorChain = $this->input->getValidatorChain();
        $this->assertEquals(0, count($validatorChain->getValidators()));

        $this->assertTrue($this->input->isValid());
        $this->assertEquals(0, count($validatorChain->getValidators()));
    }

    public function testRequiredUploadValidatorValidatorNotAddedWhenOneExists()
    {
        $this->input->setAutoPrependUploadValidator(true);
        $this->assertTrue($this->input->getAutoPrependUploadValidator());
        $this->assertTrue($this->input->isRequired());
        $this->input->setValue(array('tmp_name' => 'bar'));

        $uploadMock = $this->getMock('Zend\Validator\File\UploadFile', array('isValid'));
        $uploadMock->expects($this->exactly(1))
                     ->method('isValid')
                     ->will($this->returnValue(true));

        $validatorChain = $this->input->getValidatorChain();
        $validatorChain->prependValidator($uploadMock);
        $this->assertTrue($this->input->isValid());

        $validators = $validatorChain->getValidators();
        $this->assertEquals(1, count($validators));
        $this->assertEquals($uploadMock, $validators[0]['instance']);
    }

    public function testValidationsRunWithoutFileArrayDueToAjaxPost()
    {
        $this->input->setAutoPrependUploadValidator(true);
        $this->assertTrue($this->input->getAutoPrependUploadValidator());
        $this->assertTrue($this->input->isRequired());
        $this->input->setValue('');

        $uploadMock = $this->getMock('Zend\Validator\File\UploadFile', array('isValid'));
        $uploadMock->expects($this->exactly(1))
            ->method('isValid')
            ->will($this->returnValue(false));

        $validatorChain = $this->input->getValidatorChain();
        $validatorChain->prependValidator($uploadMock);
        $this->assertFalse($this->input->isValid());

        $validators = $validatorChain->getValidators();
        $this->assertEquals(1, count($validators));
        $this->assertEquals($uploadMock, $validators[0]['instance']);
    }

    public function testMerge()
    {
        $value  = array('tmp_name' => 'bar');

        $input  = new FileInput('foo');
        $input->setAutoPrependUploadValidator(false);
        $input->setValue($value);
        $filter = new Filter\StringTrim();
        $input->getFilterChain()->attach($filter);
        $validator = new Validator\Digits();
        $input->getValidatorChain()->attach($validator);

        $input2 = new FileInput('bar');
        $input2->merge($input);
        $validatorChain = $input->getValidatorChain();
        $filterChain    = $input->getFilterChain();

        $this->assertFalse($input2->getAutoPrependUploadValidator());
        $this->assertEquals($value, $input2->getRawValue());
        $this->assertEquals(1, $validatorChain->count());
        $this->assertEquals(1, $filterChain->count());

        $validators = $validatorChain->getValidators();
        $this->assertInstanceOf('Zend\Validator\Digits', $validators[0]['instance']);

        $filters = $filterChain->getFilters()->toArray();
        $this->assertInstanceOf('Zend\Filter\StringTrim', $filters[0]);
    }
}
