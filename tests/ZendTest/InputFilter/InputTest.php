<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\InputFilter;

use PHPUnit_Framework_TestCase as TestCase;
use RuntimeException;
use Zend\Filter;
use Zend\InputFilter\Input;
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

    public function emptyValuesProvider()
    {
        return array(
            array(null),
            array(''),
            array(array()),
        );
    }

    /**
     * @dataProvider emptyValuesProvider
     */
    public function testValidatorSkippedIfValueIsEmptyAndAllowedAndNotContinue($emptyValue)
    {
        $validator = function () {
            return false;
        };
        $this->input->setAllowEmpty(true)
            ->setContinueIfEmpty(false)
            ->setValue($emptyValue)
            ->getValidatorChain()->attach(new Validator\Callback($validator));

        $this->assertTrue($this->input->isValid());
    }

    /**
     * @dataProvider emptyValuesProvider
     */
    public function testAllowEmptyOptionSet($emptyValue)
    {
        $this->input->setAllowEmpty(true);
        $this->input->setValue($emptyValue);
        $this->assertTrue($this->input->isValid());
    }

    /**
     * @dataProvider emptyValuesProvider
     */
    public function testAllowEmptyOptionNotSet($emptyValue)
    {
        $this->input->setAllowEmpty(false);
        $this->input->setValue($emptyValue);
        $this->assertFalse($this->input->isValid());
    }

    /**
     * @dataProvider emptyValuesProvider
     */
    public function testValidatorInvokedIfValueIsEmptyAndAllowedAndContinue($emptyValue)
    {
        $message = 'failure by explicit validator';
        $validator = new Validator\Callback(function ($value) {
            return false;
        });
        $validator->setMessage($message);
        $this->input->setAllowEmpty(true)
                    ->setContinueIfEmpty(true)
                    ->setValue($emptyValue)
                    ->getValidatorChain()->attach($validator);
        $this->assertFalse($this->input->isValid());
        // Test reason for validation failure; ensures that failure was not
        // caused by accidentally injected NotEmpty validator
        $this->assertEquals(array('callbackValue' => $message), $this->input->getMessages());
    }

    public function testNotAllowEmptyWithFilterConvertsNonemptyToEmptyIsNotValid()
    {
        $this->input->setValue('nonempty')
                    ->getFilterChain()->attach(new Filter\Callback(function () {
                        return '';
                    }));
        $this->assertFalse($this->input->isValid());
    }

    public function testNotAllowEmptyWithFilterConvertsEmptyToNonEmptyIsValid()
    {
        $this->input->setValue('')
                    ->getFilterChain()->attach(new Filter\Callback(function () {
                        return 'nonempty';
                    }));
        $this->assertTrue($this->input->isValid());
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

    public function dataFallbackValue()
    {
        return array(
            array(
                'fallbackValue' => null
            ),
            array(
                'fallbackValue' => ''
            ),
            array(
                'fallbackValue' => 'some value'
            ),
        );
    }

    /**
     * @dataProvider dataFallbackValue
     */
    public function testFallbackValue($fallbackValue)
    {
        $this->input->setFallbackValue($fallbackValue);
        $validator = new Validator\Date();
        $this->input->getValidatorChain()->attach($validator);
        $this->input->setValue('123'); // not a date

        $this->assertTrue($this->input->isValid());
        $this->assertEmpty($this->input->getMessages());
        $this->assertSame($fallbackValue, $this->input->getValue());
    }

    public function testMergeRetainsContinueIfEmptyFlag()
    {
        $input = new Input('foo');
        $input->setContinueIfEmpty(true);

        $input2 = new Input('bar');
        $input2->merge($input);
        $this->assertTrue($input2->continueIfEmpty());
    }

    public function testMergeRetainsAllowEmptyFlag()
    {
        $input = new Input('foo');
        $input->setRequired(true);
        $input->setAllowEmpty(true);

        $input2 = new Input('bar');
        $input2->setRequired(true);
        $input2->setAllowEmpty(false);
        $input2->merge($input);

        $this->assertTrue($input2->isRequired());
        $this->assertTrue($input2->allowEmpty());
    }

    /**
     * @group 7445
     */
    public function testInputIsValidWhenUsingSetRequiredAtStart()
    {
        $input = new Input();
        $input->setName('foo')
              ->setRequired(false)
              ->setAllowEmpty(false)
              ->setContinueIfEmpty(false);

        $this->assertTrue($input->isValid());
    }

    /**
     * @group 7445
     */
    public function testInputIsValidWhenUsingSetRequiredAtEnd()
    {
        $input = new Input();
        $input->setName('foo')
              ->setAllowEmpty(false)
              ->setContinueIfEmpty(false)
              ->setRequired(false);

        $this->assertTrue($input->isValid());
    }

    public function whenRequiredAndAllowEmptyAndNotContinueIfEmptyValidatorsAreNotRun()
    {
        $validator = new Validator\Callback(function ($value) {
            throw new RuntimeException('Validator executed when it should not be');
        });

        $requiredFirst = new Input('foo');
        $requiredFirst->setRequired(true)
            ->setAllowEmpty(true)
            ->setContinueIfEmpty(false)
            ->getValidatorChain()->attach($validator);

        $requiredLast = new Input('foo');
        $requiredLast->setAllowEmpty(true)
            ->setContinueIfEmpty(false)
            ->setRequired(true)
            ->getValidatorChain()->attach($validator);

        return array(
            'required-first-null'  => array($requiredFirst, null),
            'required-last-null'   => array($requiredLast, null),
            'required-first-empty' => array($requiredFirst, ''),
            'required-last-empty'  => array($requiredLast, ''),
            'required-first-array' => array($requiredFirst, array()),
            'required-last-array'  => array($requiredLast, array()),
        );
    }

    /**
     * @group 7448
     * @dataProvider whenRequiredAndAllowEmptyAndNotContinueIfEmptyValidatorsAreNotRun
     */
    public function testWhenRequiredAndAllowEmptyAndNotContinueIfEmptyValidatorsAreNotRun($input, $value)
    {
        $input->setValue($value);
        $this->assertTrue($input->isValid());
    }

    public function whenRequiredAndAllowEmptyAndContinueIfEmptyValidatorsAreRun()
    {
        $alwaysInvalid = new Validator\Callback(function ($value) {
            if (! empty($value)) {
                throw new RuntimeException('Unexpected non-empty value provided to validate');
            }
            return false;
        });

        $emptyIsValid = new Validator\Callback(function ($value) {
            if (! empty($value)) {
                throw new RuntimeException('Unexpected non-empty value provided to validate');
            }
            return true;
        });

        $requiredFirstInvalid = new Input('foo');
        $requiredFirstValid   = new Input('foo');
        foreach (array($requiredFirstValid, $requiredFirstInvalid) as $input) {
            $input->setRequired(true)
                ->setAllowEmpty(true)
                ->setContinueIfEmpty(true);
        }

        $requiredLastInvalid = new Input('foo');
        $requiredLastValid   = new Input('foo');
        foreach (array($requiredLastValid, $requiredLastInvalid) as $input) {
            $input->setAllowEmpty(true)
                ->setContinueIfEmpty(true)
                ->setRequired(true);
        }

        foreach (array($requiredFirstValid, $requiredLastValid) as $input) {
            $input->getValidatorChain()->attach($emptyIsValid);
        }

        foreach (array($requiredFirstInvalid, $requiredLastInvalid) as $input) {
            $input->getValidatorChain()->attach($alwaysInvalid);
        }

        return array(
            'required-first-null-valid'    => array($requiredFirstValid, null, 'assertTrue'),
            'required-first-null-invalid'  => array($requiredFirstInvalid, null, 'assertFalse'),
            'required-last-null-valid'     => array($requiredLastValid, null, 'assertTrue'),
            'required-last-null-invalid'   => array($requiredLastInvalid, null, 'assertFalse'),
            'required-first-empty-valid'   => array($requiredFirstValid, '', 'assertTrue'),
            'required-first-empty-invalid' => array($requiredFirstInvalid, '', 'assertFalse'),
            'required-last-empty-valid'    => array($requiredLastValid, '', 'assertTrue'),
            'required-last-empty-invalid'  => array($requiredLastInvalid, '', 'assertFalse'),
            'required-first-array-valid'   => array($requiredFirstValid, array(), 'assertTrue'),
            'required-first-array-invalid' => array($requiredFirstInvalid, array(), 'assertFalse'),
            'required-last-array-valid'    => array($requiredLastValid, array(), 'assertTrue'),
            'required-last-array-invalid'  => array($requiredLastInvalid, array(), 'assertFalse'),
        );
    }

    /**
     * @group 7448
     * @dataProvider whenRequiredAndAllowEmptyAndContinueIfEmptyValidatorsAreRun
     */
    public function testWhenRequiredAndAllowEmptyAndContinueIfEmptyValidatorsAreRun($input, $value, $assertion)
    {
        $input->setValue($value);
        $this->{$assertion}($input->isValid());
    }

    public function whenRequiredAndNotAllowEmptyAndNotContinueIfEmptyValidatorsAreNotRun()
    {
        $validator = new Validator\Callback(function ($value) {
            throw new RuntimeException('Validator executed when it should not be');
        });

        $requiredFirst = new Input('foo');
        $requiredFirst->setRequired(true)
            ->setAllowEmpty(false)
            ->setContinueIfEmpty(false)
            ->getValidatorChain()->attach($validator);

        $requiredLast = new Input('foo');
        $requiredLast->setAllowEmpty(false)
            ->setContinueIfEmpty(false)
            ->setRequired(true)
            ->getValidatorChain()->attach($validator);

        return array(
            'required-first-null'  => array($requiredFirst, null),
            'required-last-null'   => array($requiredLast, null),
            'required-first-empty' => array($requiredFirst, ''),
            'required-last-empty'  => array($requiredLast, ''),
            'required-first-array' => array($requiredFirst, array()),
            'required-last-array'  => array($requiredLast, array()),
        );
    }

    /**
     * @group 7448
     * @dataProvider whenRequiredAndNotAllowEmptyAndNotContinueIfEmptyValidatorsAreNotRun
     */
    public function testWhenRequiredAndNotAllowEmptyAndNotContinueIfEmptyValidatorsAreNotRun($input, $value)
    {
        $input->setValue($value);
        $this->assertFalse($input->isValid());
    }

    public function whenRequiredAndNotAllowEmptyAndContinueIfEmptyValidatorsAreRun()
    {
        $alwaysInvalid = new Validator\Callback(function ($value) {
            if (! empty($value)) {
                throw new RuntimeException('Unexpected non-empty value provided to validate');
            }
            return false;
        });

        $emptyIsValid = new Validator\Callback(function ($value) {
            if (! empty($value)) {
                throw new RuntimeException('Unexpected non-empty value provided to validate');
            }
            return true;
        });

        $requiredFirstInvalid = new Input('foo');
        $requiredFirstValid   = new Input('foo');
        foreach (array($requiredFirstValid, $requiredFirstInvalid) as $input) {
            $input->setRequired(true)
                ->setAllowEmpty(false)
                ->setContinueIfEmpty(true);
        }

        $requiredLastInvalid = new Input('foo');
        $requiredLastValid   = new Input('foo');
        foreach (array($requiredLastValid, $requiredLastInvalid) as $input) {
            $input->setAllowEmpty(false)
                ->setContinueIfEmpty(true)
                ->setRequired(true);
        }

        foreach (array($requiredFirstValid, $requiredLastValid) as $input) {
            $input->getValidatorChain()->attach($emptyIsValid);
        }

        foreach (array($requiredFirstInvalid, $requiredLastInvalid) as $input) {
            $input->getValidatorChain()->attach($alwaysInvalid);
        }

        return array(
            'required-first-null-valid'    => array($requiredFirstValid, null, 'assertTrue'),
            'required-first-null-invalid'  => array($requiredFirstInvalid, null, 'assertFalse'),
            'required-last-null-valid'     => array($requiredLastValid, null, 'assertTrue'),
            'required-last-null-invalid'   => array($requiredLastInvalid, null, 'assertFalse'),
            'required-first-empty-valid'   => array($requiredFirstValid, '', 'assertTrue'),
            'required-first-empty-invalid' => array($requiredFirstInvalid, '', 'assertFalse'),
            'required-last-empty-valid'    => array($requiredLastValid, '', 'assertTrue'),
            'required-last-empty-invalid'  => array($requiredLastInvalid, '', 'assertFalse'),
            'required-first-array-valid'   => array($requiredFirstValid, array(), 'assertTrue'),
            'required-first-array-invalid' => array($requiredFirstInvalid, array(), 'assertFalse'),
            'required-last-array-valid'    => array($requiredLastValid, array(), 'assertTrue'),
            'required-last-array-invalid'  => array($requiredLastInvalid, array(), 'assertFalse'),
        );
    }

    /**
     * @group 7448
     * @dataProvider whenRequiredAndNotAllowEmptyAndContinueIfEmptyValidatorsAreRun
     */
    public function testWhenRequiredAndNotAllowEmptyAndContinueIfEmptyValidatorsAreRun($input, $value, $assertion)
    {
        $input->setValue($value);
        $this->{$assertion}($input->isValid());
    }

    public function whenNotRequiredAndAllowEmptyAndNotContinueIfEmptyValidatorsAreNotRun()
    {
        $validator = new Validator\Callback(function ($value) {
            throw new RuntimeException('Validator executed when it should not be');
        });

        $requiredFirst = new Input('foo');
        $requiredFirst->setRequired(false)
            ->setAllowEmpty(true)
            ->setContinueIfEmpty(false)
            ->getValidatorChain()->attach($validator);

        $requiredLast = new Input('foo');
        $requiredLast->setAllowEmpty(true)
            ->setContinueIfEmpty(false)
            ->setRequired(false)
            ->getValidatorChain()->attach($validator);

        return array(
            'required-first-null'  => array($requiredFirst, null),
            'required-last-null'   => array($requiredLast, null),
            'required-first-empty' => array($requiredFirst, ''),
            'required-last-empty'  => array($requiredLast, ''),
            'required-first-array' => array($requiredFirst, array()),
            'required-last-array'  => array($requiredLast, array()),
        );
    }

    /**
     * @group 7448
     * @dataProvider whenNotRequiredAndAllowEmptyAndNotContinueIfEmptyValidatorsAreNotRun
     */
    public function testWhenNotRequiredAndAllowEmptyAndNotContinueIfEmptyValidatorsAreNotRun($input, $value)
    {
        $input->setValue($value);
        $this->assertTrue($input->isValid());
    }

    public function whenNotRequiredAndNotAllowEmptyAndNotContinueIfEmptyValidatorsAreNotRun()
    {
        $validator = new Validator\Callback(function ($value) {
            throw new RuntimeException('Validator executed when it should not be');
        });

        $requiredFirst = new Input('foo');
        $requiredFirst->setRequired(false)
            ->setAllowEmpty(false)
            ->setContinueIfEmpty(false)
            ->getValidatorChain()->attach($validator);

        $requiredLast = new Input('foo');
        $requiredLast->setAllowEmpty(false)
            ->setContinueIfEmpty(false)
            ->setRequired(false)
            ->getValidatorChain()->attach($validator);

        return array(
            'required-first-null'  => array($requiredFirst, null),
            'required-last-null'   => array($requiredLast, null),
            'required-first-empty' => array($requiredFirst, ''),
            'required-last-empty'  => array($requiredLast, ''),
            'required-first-array' => array($requiredFirst, array()),
            'required-last-array'  => array($requiredLast, array()),
        );
    }

    /**
     * @group 7448
     * @dataProvider whenNotRequiredAndNotAllowEmptyAndNotContinueIfEmptyValidatorsAreNotRun
     */
    public function testWhenNotRequiredAndNotAllowEmptyAndNotContinueIfEmptyValidatorsAreNotRun($input, $value)
    {
        $input->setValue($value);
        $this->assertTrue($input->isValid());
    }

    public function whenNotRequiredAndAllowEmptyAndContinueIfEmptyValidatorsAreRun()
    {
        $alwaysInvalid = new Validator\Callback(function ($value) {
            if (! empty($value)) {
                throw new RuntimeException('Unexpected non-empty value provided to validate');
            }
            return false;
        });

        $emptyIsValid = new Validator\Callback(function ($value) {
            if (! empty($value)) {
                throw new RuntimeException('Unexpected non-empty value provided to validate');
            }
            return true;
        });

        $requiredFirstInvalid = new Input('foo');
        $requiredFirstValid   = new Input('foo');
        foreach (array($requiredFirstValid, $requiredFirstInvalid) as $input) {
            $input->setRequired(false)
                ->setAllowEmpty(true)
                ->setContinueIfEmpty(true);
        }

        $requiredLastInvalid = new Input('foo');
        $requiredLastValid   = new Input('foo');
        foreach (array($requiredLastValid, $requiredLastInvalid) as $input) {
            $input->setAllowEmpty(true)
                ->setContinueIfEmpty(true)
                ->setRequired(false);
        }

        foreach (array($requiredFirstValid, $requiredLastValid) as $input) {
            $input->getValidatorChain()->attach($emptyIsValid);
        }

        foreach (array($requiredFirstInvalid, $requiredLastInvalid) as $input) {
            $input->getValidatorChain()->attach($alwaysInvalid);
        }

        return array(
            'required-first-null-valid'    => array($requiredFirstValid, null, 'assertTrue'),
            'required-first-null-invalid'  => array($requiredFirstInvalid, null, 'assertFalse'),
            'required-last-null-valid'     => array($requiredLastValid, null, 'assertTrue'),
            'required-last-null-invalid'   => array($requiredLastInvalid, null, 'assertFalse'),
            'required-first-empty-valid'   => array($requiredFirstValid, '', 'assertTrue'),
            'required-first-empty-invalid' => array($requiredFirstInvalid, '', 'assertFalse'),
            'required-last-empty-valid'    => array($requiredLastValid, '', 'assertTrue'),
            'required-last-empty-invalid'  => array($requiredLastInvalid, '', 'assertFalse'),
            'required-first-array-valid'   => array($requiredFirstValid, array(), 'assertTrue'),
            'required-first-array-invalid' => array($requiredFirstInvalid, array(), 'assertFalse'),
            'required-last-array-valid'    => array($requiredLastValid, array(), 'assertTrue'),
            'required-last-array-invalid'  => array($requiredLastInvalid, array(), 'assertFalse'),
        );
    }

    /**
     * @group 7448
     * @dataProvider whenNotRequiredAndAllowEmptyAndContinueIfEmptyValidatorsAreRun
     */
    public function testWhenNotRequiredAndAllowEmptyAndContinueIfEmptyValidatorsAreRun($input, $value, $assertion)
    {
        $input->setValue($value);
        $this->{$assertion}($input->isValid());
    }

    public function whenNotRequiredAndNotAllowEmptyAndContinueIfEmptyValidatorsAreRun()
    {
        $alwaysInvalid = new Validator\Callback(function ($value) {
            if (! empty($value)) {
                throw new RuntimeException('Unexpected non-empty value provided to validate');
            }
            return false;
        });

        $emptyIsValid = new Validator\Callback(function ($value) {
            if (! empty($value)) {
                throw new RuntimeException('Unexpected non-empty value provided to validate');
            }
            return true;
        });

        $requiredFirstInvalid = new Input('foo');
        $requiredFirstValid   = new Input('foo');
        foreach (array($requiredFirstValid, $requiredFirstInvalid) as $input) {
            $input->setRequired(false)
                ->setAllowEmpty(false)
                ->setContinueIfEmpty(true);
        }

        $requiredLastInvalid = new Input('foo');
        $requiredLastValid   = new Input('foo');
        foreach (array($requiredLastValid, $requiredLastInvalid) as $input) {
            $input->setAllowEmpty(false)
                ->setContinueIfEmpty(true)
                ->setRequired(false);
        }

        foreach (array($requiredFirstValid, $requiredLastValid) as $input) {
            $input->getValidatorChain()->attach($emptyIsValid);
        }

        foreach (array($requiredFirstInvalid, $requiredLastInvalid) as $input) {
            $input->getValidatorChain()->attach($alwaysInvalid);
        }

        return array(
            'required-first-null-valid'    => array($requiredFirstValid, null, 'assertTrue'),
            'required-first-null-invalid'  => array($requiredFirstInvalid, null, 'assertFalse'),
            'required-last-null-valid'     => array($requiredLastValid, null, 'assertTrue'),
            'required-last-null-invalid'   => array($requiredLastInvalid, null, 'assertFalse'),
            'required-first-empty-valid'   => array($requiredFirstValid, '', 'assertTrue'),
            'required-first-empty-invalid' => array($requiredFirstInvalid, '', 'assertFalse'),
            'required-last-empty-valid'    => array($requiredLastValid, '', 'assertTrue'),
            'required-last-empty-invalid'  => array($requiredLastInvalid, '', 'assertFalse'),
            'required-first-array-valid'   => array($requiredFirstValid, array(), 'assertTrue'),
            'required-first-array-invalid' => array($requiredFirstInvalid, array(), 'assertFalse'),
            'required-last-array-valid'    => array($requiredLastValid, array(), 'assertTrue'),
            'required-last-array-invalid'  => array($requiredLastInvalid, array(), 'assertFalse'),
        );
    }

    /**
     * @group 7448
     * @dataProvider whenNotRequiredAndNotAllowEmptyAndContinueIfEmptyValidatorsAreRun
     */
    public function testWhenNotRequiredAndNotAllowEmptyAndContinueIfEmptyValidatorsAreRun($input, $value, $assertion)
    {
        $input->setValue($value);
        $this->{$assertion}($input->isValid());
    }
}
