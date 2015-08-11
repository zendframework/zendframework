<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\InputFilter;

use PHPUnit_Framework_MockObject_MockObject as MockObject;
use PHPUnit_Framework_TestCase as TestCase;
use stdClass;
use Zend\Filter\FilterChain;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputInterface;
use Zend\Validator\NotEmpty as NotEmptyValidator;
use Zend\Validator\ValidatorChain;
use Zend\Validator\ValidatorInterface;

/**
 * @covers Zend\InputFilter\Input
 */
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

    public function assertRequiredValidationErrorMessage(Input $input, $message = '')
    {
        $message  = $message ?: 'Expected failure message for required input';
        $message .= ';';

        $messages = $input->getMessages();
        $this->assertInternalType('array', $messages, $message . ' non-array messages array');

        $notEmpty         = new NotEmptyValidator();
        $messageTemplates = $notEmpty->getOption('messageTemplates');
        $this->assertSame(array(
            NotEmptyValidator::IS_EMPTY => $messageTemplates[NotEmptyValidator::IS_EMPTY],
        ), $messages, $message . ' missing NotEmpty::IS_EMPTY key and/or contains additional messages');
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
        $chain = $this->createFilterChainMock();
        $this->input->setFilterChain($chain);
        $this->assertSame($chain, $this->input->getFilterChain());
    }

    public function testCanInjectValidatorChain()
    {
        $chain = $this->createValidatorChainMock();
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
        $input = $this->input;
        $this->assertFalse($input->continueIfEmpty());
    }

    public function testContinueIfEmptyFlagIsMutable()
    {
        $input = $this->input;
        $input->setContinueIfEmpty(true);
        $this->assertTrue($input->continueIfEmpty());
    }

    /**
     * @dataProvider setValueProvider
     */
    public function testSetFallbackValue($fallbackValue)
    {
        $input = $this->input;

        $return = $input->setFallbackValue($fallbackValue);
        $this->assertSame($input, $return, 'setFallbackValue() must return it self');

        $this->assertEquals($fallbackValue, $input->getFallbackValue(), 'getFallbackValue() value not match');
        $this->assertEquals(true, $input->hasFallback(), 'hasFallback() value not match');
    }

    /**
     * @dataProvider fallbackValueVsIsValidProvider
     */
    public function testFallbackValueVsIsValidRules($required, $fallbackValue, $originalValue, $isValid, $expectedValue)
    {
        $input = $this->input;
        $input->setContinueIfEmpty(true);

        $input->setRequired($required);
        $input->setValidatorChain($this->createValidatorChainMock($isValid, $originalValue));
        $input->setFallbackValue($fallbackValue);
        $input->setValue($originalValue);

        $this->assertTrue(
            $input->isValid(),
            'isValid() should be return always true when fallback value is set. Detail: ' .
            json_encode($input->getMessages())
        );
        $this->assertEquals(array(), $input->getMessages(), 'getMessages() should be empty because the input is valid');
        $this->assertSame($expectedValue, $input->getRawValue(), 'getRawValue() value not match');
        $this->assertSame($expectedValue, $input->getValue(), 'getValue() value not match');
    }

    /**
     * @dataProvider fallbackValueVsIsValidProvider
     */
    public function testFallbackValueVsIsValidRulesWhenValueNotSet($required, $fallbackValue)
    {
        $expectedValue = $fallbackValue; // Should always return the fallback value

        $input = $this->input;
        $input->setContinueIfEmpty(true);

        $input->setRequired($required);
        $input->setValidatorChain($this->createValidatorChainMock(null));
        $input->setFallbackValue($fallbackValue);

        $this->assertTrue(
            $input->isValid(),
            'isValid() should be return always true when fallback value is set. Detail: ' .
            json_encode($input->getMessages())
        );
        $this->assertEquals(array(), $input->getMessages(), 'getMessages() should be empty because the input is valid');
        $this->assertSame($expectedValue, $input->getRawValue(), 'getRawValue() value not match');
        $this->assertSame($expectedValue, $input->getValue(), 'getValue() value not match');
    }

    public function testRequiredWithoutFallbackAndValueNotSetThenFail()
    {
        $input = $this->input;
        $input->setRequired(true);

        $this->assertFalse(
            $input->isValid(),
            'isValid() should be return always false when no fallback value, is required, and not data is set.'
        );
        $this->assertRequiredValidationErrorMessage($input);
    }

    public function testRequiredWithoutFallbackAndValueNotSetThenFailReturnsCustomErrorMessageWhenSet()
    {
        $input = $this->input;
        $input->setRequired(true);
        $input->setErrorMessage('FAILED TO VALIDATE');

        $this->assertFalse(
            $input->isValid(),
            'isValid() should be return always false when no fallback value, is required, and not data is set.'
        );
        $this->assertSame(array('FAILED TO VALIDATE'), $input->getMessages());
    }

    /**
     * @group 28
     * @group 60
     */
    public function testRequiredWithoutFallbackAndValueNotSetProvidesNotEmptyValidatorIsEmptyErrorMessage()
    {
        $input = $this->input;
        $input->setRequired(true);

        $this->assertFalse(
            $input->isValid(),
            'isValid() should always return false when no fallback value is present, '
            . 'the input is required, and no data is set.'
        );
        $this->assertRequiredValidationErrorMessage($input);
    }

    /**
     * @group 28
     * @group 60
     */
    public function testRequiredWithoutFallbackAndValueNotSetProvidesCustomErrorMessageWhenSet()
    {
        $input = $this->input;
        $input->setRequired(true);
        $input->setErrorMessage('FAILED TO VALIDATE');

        $this->assertFalse(
            $input->isValid(),
            'isValid() should always return false when no fallback value is present, '
            . 'the input is required, and no data is set.'
        );
        $this->assertSame(array('FAILED TO VALIDATE'), $input->getMessages());
    }

    public function testNotRequiredWithoutFallbackAndValueNotSetThenIsValid()
    {
        $input = $this->input;
        $input->setRequired(false);
        $input->setAllowEmpty(false);
        $input->setContinueIfEmpty(true);

        // Validator should not to be called
        $input->getValidatorChain()
            ->attach($this->createValidatorMock(null, null))
        ;
        $this->assertTrue(
            $input->isValid(),
            'isValid() should be return always true when is not required, and no data is set. Detail: ' .
            json_encode($input->getMessages())
        );
        $this->assertEquals(array(), $input->getMessages(), 'getMessages() should be empty because the input is valid');
    }

    /**
     * @dataProvider emptyValueProvider
     */
    public function testNotEmptyValidatorNotInjectedIfContinueIfEmptyIsTrue($value)
    {
        $input = $this->input;
        $input->setContinueIfEmpty(true);
        $input->setValue($value);
        $input->isValid();
        $validators = $input->getValidatorChain()
                                ->getValidators();
        $this->assertEmpty($validators);
    }

    public function testDefaultGetValue()
    {
        $this->assertNull($this->input->getValue());
    }

    public function testValueMayBeInjected()
    {
        $valueRaw = $this->getDummyValue();

        $this->input->setValue($valueRaw);
        $this->assertEquals($valueRaw, $this->input->getValue());
    }

    public function testRetrievingValueFiltersTheValue()
    {
        $valueRaw = $this->getDummyValue();
        $valueFiltered = $this->getDummyValue(false);

        $filterChain = $this->createFilterChainMock($valueRaw, $valueFiltered);

        $this->input->setFilterChain($filterChain);
        $this->input->setValue($valueRaw);

        $this->assertSame($valueFiltered, $this->input->getValue());
    }

    public function testCanRetrieveRawValue()
    {
        $valueRaw = $this->getDummyValue();

        $filterChain = $this->createFilterChainMock();

        $this->input->setFilterChain($filterChain);
        $this->input->setValue($valueRaw);

        $this->assertEquals($valueRaw, $this->input->getRawValue());
    }

    public function testValidationOperatesOnFilteredValue()
    {
        $valueRaw = $this->getDummyValue();
        $valueFiltered = $this->getDummyValue(false);

        $filterChain = $this->createFilterChainMock($valueRaw, $valueFiltered);

        $validatorChain = $this->createValidatorChainMock(true, $valueFiltered);

        $this->input->setAllowEmpty(true);
        $this->input->setFilterChain($filterChain);
        $this->input->setValidatorChain($validatorChain);
        $this->input->setValue($valueRaw);

        $this->assertTrue(
            $this->input->isValid(),
            'isValid() value not match. Detail . ' . json_encode($this->input->getMessages())
        );
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

    /**
     * @dataProvider emptyValueProvider
     */
    public function testNotEmptyValidatorAddedWhenIsValidIsCalled($value)
    {
        $this->assertTrue($this->input->isRequired());
        $this->input->setValue($value);
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

    /**
     * @dataProvider emptyValueProvider
     */
    public function testRequiredNotEmptyValidatorNotAddedWhenOneExists($value)
    {
        $this->input->setRequired(true);
        $this->input->setValue($value);

        $notEmptyMock = $this->createNonEmptyValidatorMock(false, $value);

        $validatorChain = $this->input->getValidatorChain();
        $validatorChain->prependValidator($notEmptyMock);
        $this->assertFalse($this->input->isValid());

        $validators = $validatorChain->getValidators();
        $this->assertEquals(1, count($validators));
        $this->assertEquals($notEmptyMock, $validators[0]['instance']);
    }

    /**
     * @dataProvider emptyValueProvider
     */
    public function testDoNotInjectNotEmptyValidatorIfAnywhereInChain($valueRaw, $valueFiltered)
    {
        $filterChain = $this->createFilterChainMock($valueRaw, $valueFiltered);
        $validatorChain = $this->input->getValidatorChain();

        $this->input->setRequired(true);
        $this->input->setFilterChain($filterChain);
        $this->input->setValue($valueRaw);

        $notEmptyMock = $this->createNonEmptyValidatorMock(false, $valueFiltered);

        $validatorChain->attach($this->createValidatorMock(true));
        $validatorChain->attach($notEmptyMock);

        $this->assertFalse($this->input->isValid());

        $validators = $validatorChain->getValidators();
        $this->assertEquals(2, count($validators));
        $this->assertEquals($notEmptyMock, $validators[1]['instance']);
    }

    /**
     * @group 7448
     * @dataProvider isRequiredVsAllowEmptyVsContinueIfEmptyVsIsValidProvider
     */
    public function testIsRequiredVsAllowEmptyVsContinueIfEmptyVsIsValid(
        $required,
        $allowEmpty,
        $continueIfEmpty,
        $validator,
        $value,
        $expectedIsValid,
        $expectedMessages
    ) {
        $this->input->setRequired($required);
        $this->input->setAllowEmpty($allowEmpty);
        $this->input->setContinueIfEmpty($continueIfEmpty);
        $this->input->getValidatorChain()
            ->attach($validator)
        ;
        $this->input->setValue($value);

        $this->assertEquals(
            $expectedIsValid,
            $this->input->isValid(),
            'isValid() value not match. Detail: ' . json_encode($this->input->getMessages())
        );
        $this->assertEquals($expectedMessages, $this->input->getMessages(), 'getMessages() value not match');
        $this->assertEquals($value, $this->input->getRawValue(), 'getRawValue() must return the value always');
        $this->assertEquals($value, $this->input->getValue(), 'getValue() must return the filtered value always');
    }

    /**
     * @dataProvider setValueProvider
     */
    public function testSetValuePutInputInTheDesiredState($value)
    {
        $input = $this->input;
        $this->assertFalse($input->hasValue(), 'Input should not have value by default');

        $input->setValue($value);
        $this->assertTrue($input->hasValue(), "hasValue() didn't return true when value was set");
    }

    /**
     * @dataProvider setValueProvider
     */
    public function testResetValueReturnsInputValueToDefaultValue($value)
    {
        $input = $this->input;
        $originalInput = clone $input;
        $this->assertFalse($input->hasValue(), 'Input should not have value by default');

        $input->setValue($value);
        $this->assertTrue($input->hasValue(), "hasValue() didn't return true when value was set");

        $return = $input->resetValue();
        $this->assertSame($input, $return, 'resetValue() must return itself');
        $this->assertEquals($originalInput, $input, 'Input was not reset to the default value state');
    }

    public function testMerge()
    {
        $sourceRawValue = $this->getDummyValue();

        $source = $this->createInputInterfaceMock();
        $source->method('getName')->willReturn('bazInput');
        $source->method('getErrorMessage')->willReturn('bazErrorMessage');
        $source->method('breakOnFailure')->willReturn(true);
        $source->method('isRequired')->willReturn(true);
        $source->method('getRawValue')->willReturn($sourceRawValue);
        $source->method('getFilterChain')->willReturn($this->createFilterChainMock());
        $source->method('getValidatorChain')->willReturn($this->createValidatorChainMock());

        $targetFilterChain = $this->createFilterChainMock();
        $targetFilterChain->expects(TestCase::once())
            ->method('merge')
            ->with($source->getFilterChain())
        ;

        $targetValidatorChain = $this->createValidatorChainMock();
        $targetValidatorChain->expects(TestCase::once())
            ->method('merge')
            ->with($source->getValidatorChain())
        ;

        $target = $this->input;
        $target->setName('fooInput');
        $target->setErrorMessage('fooErrorMessage');
        $target->setBreakOnFailure(false);
        $target->setRequired(false);
        $target->setFilterChain($targetFilterChain);
        $target->setValidatorChain($targetValidatorChain);

        $return = $target->merge($source);
        $this->assertSame($target, $return, 'merge() must return it self');

        $this->assertEquals('bazInput', $target->getName(), 'getName() value not match');
        $this->assertEquals('bazErrorMessage', $target->getErrorMessage(), 'getErrorMessage() value not match');
        $this->assertEquals(true, $target->breakOnFailure(), 'breakOnFailure() value not match');
        $this->assertEquals(true, $target->isRequired(), 'isRequired() value not match');
        $this->assertEquals($sourceRawValue, $target->getRawValue(), 'getRawValue() value not match');
        $this->assertTrue($target->hasValue(), 'hasValue() value not match');
    }

    /**
     * Specific Input::merge extras
     */
    public function testInputMergeWithoutValues()
    {
        $source = new Input();
        $source->setContinueIfEmpty(true);
        $this->assertFalse($source->hasValue(), 'Source should not have a value');

        $target = $this->input;
        $target->setContinueIfEmpty(false);
        $this->assertFalse($target->hasValue(), 'Target should not have a value');

        $return = $target->merge($source);
        $this->assertSame($target, $return, 'merge() must return it self');

        $this->assertEquals(true, $target->continueIfEmpty(), 'continueIfEmpty() value not match');
        $this->assertFalse($target->hasValue(), 'hasValue() value not match');
    }

    /**
     * Specific Input::merge extras
     */
    public function testInputMergeWithSourceValue()
    {
        $source = new Input();
        $source->setContinueIfEmpty(true);
        $source->setValue(array('foo'));

        $target = $this->input;
        $target->setContinueIfEmpty(false);
        $this->assertFalse($target->hasValue(), 'Target should not have a value');

        $return = $target->merge($source);
        $this->assertSame($target, $return, 'merge() must return it self');

        $this->assertEquals(true, $target->continueIfEmpty(), 'continueIfEmpty() value not match');
        $this->assertEquals(array('foo'), $target->getRawValue(), 'getRawValue() value not match');
        $this->assertTrue($target->hasValue(), 'hasValue() value not match');
    }

    /**
     * Specific Input::merge extras
     */
    public function testInputMergeWithTargetValue()
    {
        $source = new Input();
        $source->setContinueIfEmpty(true);
        $this->assertFalse($source->hasValue(), 'Source should not have a value');

        $target = $this->input;
        $target->setContinueIfEmpty(false);
        $target->setValue(array('foo'));

        $return = $target->merge($source);
        $this->assertSame($target, $return, 'merge() must return it self');

        $this->assertEquals(true, $target->continueIfEmpty(), 'continueIfEmpty() value not match');
        $this->assertEquals(array('foo'), $target->getRawValue(), 'getRawValue() value not match');
        $this->assertTrue($target->hasValue(), 'hasValue() value not match');
    }

    public function fallbackValueVsIsValidProvider()
    {
        $required = true;
        $isValid = true;

        $originalValue = 'fooValue';
        $fallbackValue = 'fooFallbackValue';

        // @codingStandardsIgnoreStart
        return array(
            // Description => [$inputIsRequired, $fallbackValue, $originalValue, $isValid, $expectedValue]
            'Required: T, Input: Invalid. getValue: fallback' => array( $required, $fallbackValue, $originalValue, !$isValid, $fallbackValue),
            'Required: T, Input: Valid. getValue: original' =>   array( $required, $fallbackValue, $originalValue,  $isValid, $originalValue),
            'Required: F, Input: Invalid. getValue: fallback' => array(!$required, $fallbackValue, $originalValue, !$isValid, $fallbackValue),
            'Required: F, Input: Valid. getValue: original' =>   array(!$required, $fallbackValue, $originalValue,  $isValid, $originalValue),
        );
        // @codingStandardsIgnoreEnd
    }

    public function setValueProvider()
    {
        $emptyValues = $this->emptyValueProvider();
        $mixedValues = $this->mixedValueProvider();

        $values = array_merge($emptyValues, $mixedValues);

        return $values;
    }

    public function isRequiredVsAllowEmptyVsContinueIfEmptyVsIsValidProvider()
    {
        $allValues = $this->setValueProvider();
        $emptyValues = $this->emptyValueProvider();
        $nonEmptyValues = array_diff_key($allValues, $emptyValues);

        $isRequired = true;
        $aEmpty = true;
        $cIEmpty = true;
        $isValid = true;

        $validatorMsg = array('FooValidator' => 'Invalid Value');
        $notEmptyMsg = array('isEmpty' => "Value is required and can't be empty");

        $self = $this;
        $validatorNotCall = function ($value, $context = null) use ($self) {
            return $self->createValidatorMock(null, $value, $context);
        };
        $validatorInvalid = function ($value, $context = null) use ($self, $validatorMsg) {
            return $self->createValidatorMock(false, $value, $context, $validatorMsg);
        };
        $validatorValid = function ($value, $context = null) use ($self) {
            return $self->createValidatorMock(true, $value, $context);
        };

        // @codingStandardsIgnoreStart
        $dataTemplates = array(
            // Description => [$isRequired, $allowEmpty, $continueIfEmpty, $validator, [$values], $expectedIsValid, $expectedMessages]
            'Required: T; AEmpty: T; CIEmpty: T; Validator: T'                   => array( $isRequired,  $aEmpty,  $cIEmpty, $validatorValid  , $allValues     ,  $isValid, array()),
            'Required: T; AEmpty: T; CIEmpty: T; Validator: F'                   => array( $isRequired,  $aEmpty,  $cIEmpty, $validatorInvalid, $allValues     , !$isValid, $validatorMsg),

            'Required: T; AEmpty: T; CIEmpty: F; Validator: X, Value: Empty'     => array( $isRequired,  $aEmpty, !$cIEmpty, $validatorNotCall, $emptyValues   ,  $isValid, array()),
            'Required: T; AEmpty: T; CIEmpty: F; Validator: T, Value: Not Empty' => array( $isRequired,  $aEmpty, !$cIEmpty, $validatorValid  , $nonEmptyValues,  $isValid, array()),
            'Required: T; AEmpty: T; CIEmpty: F; Validator: F, Value: Not Empty' => array( $isRequired,  $aEmpty, !$cIEmpty, $validatorInvalid, $nonEmptyValues, !$isValid, $validatorMsg),

            'Required: T; AEmpty: F; CIEmpty: T; Validator: T'                   => array( $isRequired, !$aEmpty,  $cIEmpty, $validatorValid  , $allValues     ,  $isValid, array()),
            'Required: T; AEmpty: F; CIEmpty: T; Validator: F'                   => array( $isRequired, !$aEmpty,  $cIEmpty, $validatorInvalid, $allValues     , !$isValid, $validatorMsg),

            'Required: T; AEmpty: F; CIEmpty: F; Validator: X, Value: Empty'     => array( $isRequired, !$aEmpty, !$cIEmpty, $validatorNotCall, $emptyValues   , !$isValid, $notEmptyMsg),
            'Required: T; AEmpty: F; CIEmpty: F; Validator: T, Value: Not Empty' => array( $isRequired, !$aEmpty, !$cIEmpty, $validatorValid  , $nonEmptyValues,  $isValid, array()),
            'Required: T; AEmpty: F; CIEmpty: F; Validator: F, Value: Not Empty' => array( $isRequired, !$aEmpty, !$cIEmpty, $validatorInvalid, $nonEmptyValues, !$isValid, $validatorMsg),

            'Required: F; AEmpty: T; CIEmpty: T; Validator: T'                   => array(!$isRequired,  $aEmpty,  $cIEmpty, $validatorValid  , $allValues     ,  $isValid, array()),
            'Required: F; AEmpty: T; CIEmpty: T; Validator: F'                   => array(!$isRequired,  $aEmpty,  $cIEmpty, $validatorInvalid, $allValues     , !$isValid, $validatorMsg),

            'Required: F; AEmpty: T; CIEmpty: F; Validator: X, Value: Empty'     => array(!$isRequired,  $aEmpty, !$cIEmpty, $validatorNotCall, $emptyValues   ,  $isValid, array()),
            'Required: F; AEmpty: T; CIEmpty: F; Validator: T, Value: Not Empty' => array(!$isRequired,  $aEmpty, !$cIEmpty, $validatorValid  , $nonEmptyValues,  $isValid, array()),
            'Required: F; AEmpty: T; CIEmpty: F; Validator: F, Value: Not Empty' => array(!$isRequired,  $aEmpty, !$cIEmpty, $validatorInvalid, $nonEmptyValues, !$isValid, $validatorMsg),

            'Required: F; AEmpty: F; CIEmpty: T; Validator: T'                   => array(!$isRequired, !$aEmpty,  $cIEmpty, $validatorValid  , $allValues     ,  $isValid, array()),
            'Required: F; AEmpty: F; CIEmpty: T; Validator: F'                   => array(!$isRequired, !$aEmpty,  $cIEmpty, $validatorInvalid, $allValues     , !$isValid, $validatorMsg),

            'Required: F; AEmpty: F; CIEmpty: F; Validator: X, Value: Empty'     => array(!$isRequired, !$aEmpty, !$cIEmpty, $validatorNotCall, $emptyValues   ,  $isValid, array()),
            'Required: F; AEmpty: F; CIEmpty: F; Validator: T, Value: Not Empty' => array(!$isRequired, !$aEmpty, !$cIEmpty, $validatorValid  , $nonEmptyValues,  $isValid, array()),
            'Required: F; AEmpty: F; CIEmpty: F; Validator: F, Value: Not Empty' => array(!$isRequired, !$aEmpty, !$cIEmpty, $validatorInvalid, $nonEmptyValues, !$isValid, $validatorMsg),
        );
        // @codingStandardsIgnoreEnd

        // Expand data template matrix for each possible input value.
        // Description => [$isRequired, $allowEmpty, $continueIfEmpty, $validator, $value, $expectedIsValid]
        $dataSets = array();
        foreach ($dataTemplates as $dataTemplateDescription => $dataTemplate) {
            foreach ($dataTemplate[4] as $valueDescription => $value) {
                $tmpTemplate = $dataTemplate;
                $tmpTemplate[3] = $dataTemplate[3]($value['filtered']); // Get validator mock for each data set
                $tmpTemplate[4] = $value['raw']; // expand value

                $dataSets[$dataTemplateDescription . ' / ' . $valueDescription] = $tmpTemplate;
            }
        }

        return $dataSets;
    }

    public function emptyValueProvider()
    {
        return array(
            // Description => [$value]
            'null' => array(
                'raw' => null,
                'filtered' => null,
            ),
            '""' => array(
                'raw' => '',
                'filtered' => '',
            ),
//            '"0"' => array('0'),
//            '0' => array(0),
//            '0.0' => array(0.0),
//            'false' => array(false),
            '[]' => array(
                'raw' => array(),
                'filtered' => array(),
            ),
        );
    }

    public function mixedValueProvider()
    {
        return array(
            // Description => [$value]
            '"0"' => array(
                'raw' => '0',
                'filtered' => '0',
            ),
            '0' => array(
                'raw' => 0,
                'filtered' => 0,
            ),
            '0.0' => array(
                'raw' => 0.0,
                'filtered' => 0.0,
            ),
//            TODO enable me
//            'false' => array(
//                'raw' => false,
//                'filtered' => false,
//            ),
            'php' => array(
                'raw' => 'php',
                'filtered' => 'php',
            ),
//            TODO enable me
//            'whitespace' => array(
//                'raw' => ' ',
//                'filtered' => ' ',
//            ),
            '1' => array(
                'raw' => 1,
                'filtered' => 1,
            ),
            '1.0' => array(
                'raw' => 1.0,
                'filtered' => 1.0,
            ),
            'true' => array(
                'raw' => true,
                'filtered' => true,
            ),
            '["php"]' => array(
                'raw' => array('php'),
                'filtered' => array('php'),
            ),
            'object' => array(
                'raw' => new stdClass(),
                'filtered' => new stdClass(),
            ),
            // @codingStandardsIgnoreStart
//            TODO Skip HHVM failure enable me
//            'callable' => array(
//                'raw' => function () {},
//                'filtered' => function () {},
//            ),
            // @codingStandardsIgnoreEnd
        );
    }

    /**
     * @return InputInterface|MockObject
     */
    public function createInputInterfaceMock()
    {
        /** @var InputInterface|MockObject $source */
        $source = $this->getMock('Zend\InputFilter\InputInterface');

        return $source;
    }

    /**
     * @param mixed $valueRaw
     * @param mixed $valueFiltered
     *
     * @return FilterChain|MockObject
     */
    public function createFilterChainMock($valueRaw = null, $valueFiltered = null)
    {
        /** @var FilterChain|MockObject $filterChain */
        $filterChain = $this->getMock('Zend\Filter\FilterChain');

        $filterChain->method('filter')
            ->with($valueRaw)
            ->willReturn($valueFiltered)
        ;

        return $filterChain;
    }

    /**
     * @param null|bool $isValid If set stub isValid method for return the argument value.
     * @param mixed $value
     * @param mixed $context
     * @param string[] $messages
     *
     * @return ValidatorChain|MockObject
     */
    public function createValidatorChainMock($isValid = null, $value = null, $context = null, $messages = array())
    {
        /** @var ValidatorChain|MockObject $validatorChain */
        $validatorChain = $this->getMock('Zend\Validator\ValidatorChain');

        if (($isValid === false) || ($isValid === true)) {
            $validatorChain->expects($this->once())
                ->method('isValid')
                ->with($value, $context)
                ->willReturn($isValid)
            ;
        } else {
            $validatorChain->expects($this->never())
                ->method('isValid')
                ->with($value, $context)
            ;
        }

        $validatorChain->method('getMessages')
            ->willReturn($messages)
        ;

        return $validatorChain;
    }

    /**
     * @param null|bool $isValid
     * @param mixed $value
     * @param mixed $context
     * @param string[] $messages
     *
     * @return ValidatorInterface|MockObject
     */
    public function createValidatorMock($isValid, $value = 'not-set', $context = null, $messages = array())
    {
        /** @var ValidatorInterface|MockObject $validator */
        $validator = $this->getMock('Zend\Validator\ValidatorInterface');

        if (($isValid === false) || ($isValid === true)) {
            $isValidMethod = $validator->expects($this->once())
                ->method('isValid')
                ->willReturn($isValid)
            ;
        } else {
            $isValidMethod = $validator->expects($this->never())
                ->method('isValid')
            ;
        }
        if ($value !== 'not-set') {
            $isValidMethod->with($value, $context);
        }

        $validator->method('getMessages')
            ->willReturn($messages)
        ;

        return $validator;
    }

    /**
     * @param bool $isValid
     * @param mixed $value
     * @param mixed $context
     *
     * @return NotEmptyValidator|MockObject
     */
    public function createNonEmptyValidatorMock($isValid, $value, $context = null)
    {
        /** @var NotEmptyValidator|MockObject $notEmptyMock */
        $notEmptyMock = $this->getMock('Zend\Validator\NotEmpty', array('isValid'));
        $notEmptyMock->expects($this->once())
            ->method('isValid')
            ->with($value, $context)
            ->willReturn($isValid)
        ;

        return $notEmptyMock;
    }

    public function getDummyValue($raw = true)
    {
        if ($raw) {
            return 'foo';
        }
        return 'filtered';
    }
}
