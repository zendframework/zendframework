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
use Zend\Filter;
use Zend\InputFilter\CollectionInputFilter;
use Zend\InputFilter\Factory;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterPluginManager;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\InputFilter\InputProviderInterface;
use Zend\ServiceManager;
use Zend\Validator;

/**
 * @covers Zend\InputFilter\Factory
 */
class FactoryTest extends TestCase
{
    public function testCreateInputWithInvalidDataTypeThrowsInvalidArgumentException()
    {
        $factory = $this->createDefaultFactory();

        $this->setExpectedException(
            'Zend\InputFilter\Exception\InvalidArgumentException',
            'expects an array or Traversable; received "string"'
        );
        /** @noinspection PhpParamsInspection */
        $factory->createInput('invalid_value');
    }

    public function testCreateInputWithTypeAsAnUnknownPluginAndNotExistsAsClassNameThrowException()
    {
        $factory = $this->createDefaultFactory();
        $type = 'foo';

        /** @var InputFilterPluginManager|MockObject $pluginManager */
        $pluginManager = $this->getMock('Zend\InputFilter\InputFilterPluginManager');
        $pluginManager->expects($this->atLeastOnce())
            ->method('has')
            ->with($type)
            ->willReturn(false)
        ;
        $factory->setInputFilterManager($pluginManager);

        $this->setExpectedException(
            'Zend\InputFilter\Exception\RuntimeException',
            'Input factory expects the "type" to be a valid class or a plugin name; received "foo"'
        );
        $factory->createInput(
            array(
                'type' => $type,
            )
        );
    }

    public function testCreateInputWithTypeAsAnInvalidPluginInstanceThrowException()
    {
        $factory = $this->createDefaultFactory();
        $type = 'fooPlugin';
        $pluginManager = $this->createInputFilterPluginManagerMockForPlugin($type, 'invalid_value');

        $factory->setInputFilterManager($pluginManager);

        $this->setExpectedException(
            'Zend\InputFilter\Exception\RuntimeException',
            'Input factory expects the "type" to be a class implementing Zend\InputFilter\InputInterface; '
            . 'received "fooPlugin"'
        );
        $factory->createInput(
            array(
                'type' => $type,
            )
        );
    }

    public function testCreateInputWithTypeAsAnInvalidClassInstanceThrowException()
    {
        $factory = $this->createDefaultFactory();
        $type = 'stdClass';

        $this->setExpectedException(
            'Zend\InputFilter\Exception\RuntimeException',
            'Input factory expects the "type" to be a class implementing Zend\InputFilter\InputInterface; ' .
            'received "stdClass"'
        );
        $factory->createInput(
            array(
                'type' => $type,
            )
        );
    }

    public function testCreateInputWithFiltersAsAnInvalidTypeThrowException()
    {
        $factory = $this->createDefaultFactory();

        $this->setExpectedException(
            'Zend\InputFilter\Exception\RuntimeException',
            'expects the value associated with "filters" to be an array/Traversable of filters or filter specifications,' .
            ' or a FilterChain; received "string"'
        );
        $factory->createInput(
            array(
                'filters' => 'invalid_value',
            )
        );
    }

    public function testCreateInputWithFiltersAsAnSpecificationWithMissingNameThrowException()
    {
        $factory = $this->createDefaultFactory();

        $this->setExpectedException(
            'Zend\InputFilter\Exception\RuntimeException',
            'Invalid filter specification provided; does not include "name" key'
        );
        $factory->createInput(
            array(
                'filters' => array(
                    array(
                        // empty
                    )
                ),
            )
        );
    }

    public function testCreateInputWithFiltersAsAnCollectionOfInvalidTypesThrowException()
    {
        $factory = $this->createDefaultFactory();

        $this->setExpectedException(
            'Zend\InputFilter\Exception\RuntimeException',
            'Invalid filter specification provided; was neither a filter instance nor an array specification'
        );
        $factory->createInput(
            array(
                'filters' => array(
                    'invalid value'
                ),
            )
        );
    }

    public function testCreateInputWithValidatorsAsAnInvalidTypeThrowException()
    {
        $factory = $this->createDefaultFactory();

        $this->setExpectedException(
            'Zend\InputFilter\Exception\RuntimeException',
            'expects the value associated with "validators" to be an array/Traversable of validators or validator ' .
            'specifications, or a ValidatorChain; received "string"'
        );
        $factory->createInput(
            array(
                'validators' => 'invalid_value',
            )
        );
    }

    public function testCreateInputWithValidatorsAsAnSpecificationWithMissingNameThrowException()
    {
        $factory = $this->createDefaultFactory();

        $this->setExpectedException(
            'Zend\InputFilter\Exception\RuntimeException',
            'Invalid validator specification provided; does not include "name" key'
        );
        $factory->createInput(
            array(
                'validators' => array(
                    array(
                        // empty
                    )
                ),
            )
        );
    }

    public function inputTypeSpecificationProvider()
    {
        return array(
            // Description => [$specificationKey]
            'continue_if_empty' => array('continue_if_empty'),
            'fallback_value' => array('fallback_value'),
        );
    }

    /**
     * @dataProvider inputTypeSpecificationProvider
     */
    public function testCreateInputWithSpecificInputTypeSettingsThrowException($specificationKey)
    {
        $factory = $this->createDefaultFactory();
        $type = 'pluginInputInterface';

        $pluginManager = $this->createInputFilterPluginManagerMockForPlugin($type, $this->getMock('Zend\InputFilter\InputInterface'));
        $factory->setInputFilterManager($pluginManager);

        $this->setExpectedException(
            'Zend\InputFilter\Exception\RuntimeException',
            sprintf('"%s" can only set to inputs of type "Zend\InputFilter\Input"', $specificationKey)
        );
        $factory->createInput(
            array(
                'type' => $type,
                $specificationKey => true,
            )
        );
    }

    public function testCreateInputWithValidatorsAsAnCollectionOfInvalidTypesThrowException()
    {
        $factory = $this->createDefaultFactory();

        $this->setExpectedException(
            'Zend\InputFilter\Exception\RuntimeException',
            'Invalid validator specification provided; was neither a validator instance nor an array specification'
        );
        $factory->createInput(
            array(
                'validators' => array(
                    'invalid value'
                ),
            )
        );
    }

    public function testCreateInputFilterWithInvalidDataTypeThrowsInvalidArgumentException()
    {
        $factory = $this->createDefaultFactory();

        $this->setExpectedException(
            'Zend\InputFilter\Exception\InvalidArgumentException',
            'expects an array or Traversable; received "string"'
        );
        /** @noinspection PhpParamsInspection */
        $factory->createInputFilter('invalid_value');
    }

    public function testFactoryComposesFilterChainByDefault()
    {
        $factory = $this->createDefaultFactory();
        $this->assertInstanceOf('Zend\Filter\FilterChain', $factory->getDefaultFilterChain());
    }

    public function testFactoryComposesValidatorChainByDefault()
    {
        $factory = $this->createDefaultFactory();
        $this->assertInstanceOf('Zend\Validator\ValidatorChain', $factory->getDefaultValidatorChain());
    }

    public function testFactoryAllowsInjectingFilterChain()
    {
        $factory = $this->createDefaultFactory();
        $filterChain = new Filter\FilterChain();
        $factory->setDefaultFilterChain($filterChain);
        $this->assertSame($filterChain, $factory->getDefaultFilterChain());
    }

    public function testFactoryAllowsInjectingValidatorChain()
    {
        $factory = $this->createDefaultFactory();
        $validatorChain = new Validator\ValidatorChain();
        $factory->setDefaultValidatorChain($validatorChain);
        $this->assertSame($validatorChain, $factory->getDefaultValidatorChain());
    }

    public function testFactoryUsesComposedFilterChainWhenCreatingNewInputObjects()
    {
        $factory = $this->createDefaultFactory();
        $filterChain   = new Filter\FilterChain();
        $pluginManager = new Filter\FilterPluginManager();
        $filterChain->setPluginManager($pluginManager);
        $factory->setDefaultFilterChain($filterChain);
        $input = $factory->createInput(array(
            'name' => 'foo',
        ));
        $this->assertInstanceOf('Zend\InputFilter\InputInterface', $input);
        $inputFilterChain = $input->getFilterChain();
        $this->assertNotSame($filterChain, $inputFilterChain);
        $this->assertSame($pluginManager, $inputFilterChain->getPluginManager());
    }

    public function testFactoryUsesComposedValidatorChainWhenCreatingNewInputObjects()
    {
        $factory          = $this->createDefaultFactory();
        $validatorChain   = new Validator\ValidatorChain();
        $validatorPlugins = new Validator\ValidatorPluginManager();
        $validatorChain->setPluginManager($validatorPlugins);
        $factory->setDefaultValidatorChain($validatorChain);
        $input = $factory->createInput(array(
            'name' => 'foo',
        ));
        $this->assertInstanceOf('Zend\InputFilter\InputInterface', $input);
        $inputValidatorChain = $input->getValidatorChain();
        $this->assertNotSame($validatorChain, $inputValidatorChain);
        $this->assertSame($validatorPlugins, $inputValidatorChain->getPluginManager());
    }

    public function testFactoryInjectsComposedFilterAndValidatorChainsIntoInputObjectsWhenCreatingNewInputFilterObjects()
    {
        $factory          = $this->createDefaultFactory();
        $filterPlugins    = new Filter\FilterPluginManager();
        $validatorPlugins = new Validator\ValidatorPluginManager();
        $filterChain      = new Filter\FilterChain();
        $validatorChain   = new Validator\ValidatorChain();
        $filterChain->setPluginManager($filterPlugins);
        $validatorChain->setPluginManager($validatorPlugins);
        $factory->setDefaultFilterChain($filterChain);
        $factory->setDefaultValidatorChain($validatorChain);

        $inputFilter = $factory->createInputFilter(array(
            'foo' => array(
                'name' => 'foo',
            ),
        ));
        $this->assertInstanceOf('Zend\InputFilter\InputFilterInterface', $inputFilter);
        $this->assertEquals(1, count($inputFilter));
        $input = $inputFilter->get('foo');
        $this->assertInstanceOf('Zend\InputFilter\InputInterface', $input);
        $inputFilterChain    = $input->getFilterChain();
        $inputValidatorChain = $input->getValidatorChain();
        $this->assertSame($filterPlugins, $inputFilterChain->getPluginManager());
        $this->assertSame($validatorPlugins, $inputValidatorChain->getPluginManager());
    }

    /**
     * @requires extension mbstring
     */
    public function testFactoryWillCreateInputWithSuggestedFilters()
    {
        $factory      = $this->createDefaultFactory();
        $htmlEntities = new Filter\HtmlEntities();
        $input = $factory->createInput(array(
            'name'    => 'foo',
            'filters' => array(
                array(
                    'name' => 'string_trim',
                ),
                $htmlEntities,
                array(
                    'name' => 'string_to_lower',
                    'options' => array(
                        'encoding' => 'ISO-8859-1',
                    ),
                ),
            ),
        ));
        $this->assertInstanceOf('Zend\InputFilter\InputInterface', $input);
        $this->assertEquals('foo', $input->getName());
        $chain = $input->getFilterChain();
        $index = 0;
        foreach ($chain as $filter) {
            switch ($index) {
                case 0:
                    $this->assertInstanceOf('Zend\Filter\StringTrim', $filter);
                    break;
                case 1:
                    $this->assertSame($htmlEntities, $filter);
                    break;
                case 2:
                    $this->assertInstanceOf('Zend\Filter\StringToLower', $filter);
                    $this->assertEquals('ISO-8859-1', $filter->getEncoding());
                    break;
                default:
                    $this->fail('Found more filters than expected');
            }
            $index++;
        }
    }

    public function testFactoryWillCreateInputWithSuggestedValidators()
    {
        $factory = $this->createDefaultFactory();
        $digits  = new Validator\Digits();
        $input = $factory->createInput(array(
            'name'       => 'foo',
            'validators' => array(
                array(
                    'name' => 'not_empty',
                ),
                $digits,
                array(
                    'name' => 'string_length',
                    'options' => array(
                        'min' => 3,
                        'max' => 5,
                    ),
                ),
            ),
        ));
        $this->assertInstanceOf('Zend\InputFilter\InputInterface', $input);
        $this->assertEquals('foo', $input->getName());
        $chain = $input->getValidatorChain();
        $index = 0;
        foreach ($chain as $validator) {
            switch ($index) {
                case 0:
                    $this->assertInstanceOf('Zend\Validator\NotEmpty', $validator);
                    break;
                case 1:
                    $this->assertSame($digits, $validator);
                    break;
                case 2:
                    $this->assertInstanceOf('Zend\Validator\StringLength', $validator);
                    $this->assertEquals(3, $validator->getMin());
                    $this->assertEquals(5, $validator->getMax());
                    break;
                default:
                    $this->fail('Found more validators than expected');
            }
            $index++;
        }
    }

    public function testFactoryWillCreateInputWithSuggestedRequiredFlagAndAlternativeAllowEmptyFlag()
    {
        $factory = $this->createDefaultFactory();
        $input   = $factory->createInput(array(
            'name'     => 'foo',
            'required' => false,
            'allow_empty' => false,
        ));
        $this->assertInstanceOf('Zend\InputFilter\InputInterface', $input);
        $this->assertFalse($input->isRequired());
        $this->assertFalse($input->allowEmpty());
    }

    public function testFactoryWillCreateInputWithSuggestedAllowEmptyFlagAndImpliesRequiredFlag()
    {
        $factory = $this->createDefaultFactory();
        $input   = $factory->createInput(array(
            'name'        => 'foo',
            'allow_empty' => true,
        ));
        $this->assertInstanceOf('Zend\InputFilter\InputInterface', $input);
        $this->assertTrue($input->allowEmpty());
        $this->assertFalse($input->isRequired());
    }

    public function testFactoryWillCreateInputWithSuggestedName()
    {
        $factory = $this->createDefaultFactory();
        $input   = $factory->createInput(array(
            'name'        => 'foo',
        ));
        $this->assertInstanceOf('Zend\InputFilter\InputInterface', $input);
        $this->assertEquals('foo', $input->getName());
    }

    public function testFactoryWillCreateInputWithContinueIfEmptyFlag()
    {
        $factory = $this->createDefaultFactory();
        $input = $factory->createInput(array(
            'name'              => 'foo',
            'continue_if_empty' => true,
        ));
        $this->assertInstanceOf('Zend\InputFilter\InputInterface', $input);
        $this->assertTrue($input->continueIfEmpty());
    }

    public function testFactoryAcceptsInputInterface()
    {
        $factory = $this->createDefaultFactory();
        $input = new Input();

        $inputFilter = $factory->createInputFilter(array(
            'foo' => $input
        ));

        $this->assertInstanceOf('Zend\InputFilter\InputFilterInterface', $inputFilter);
        $this->assertTrue($inputFilter->has('foo'));
        $this->assertEquals($input, $inputFilter->get('foo'));
    }

    public function testFactoryAcceptsInputFilterInterface()
    {
        $factory = $this->createDefaultFactory();
        $input = new InputFilter();

        $inputFilter = $factory->createInputFilter(array(
            'foo' => $input
        ));

        $this->assertInstanceOf('Zend\InputFilter\InputFilterInterface', $inputFilter);
        $this->assertTrue($inputFilter->has('foo'));
        $this->assertEquals($input, $inputFilter->get('foo'));
    }

    public function testFactoryWillCreateInputFilterAndAllInputObjectsFromGivenConfiguration()
    {
        $factory     = $this->createDefaultFactory();
        $inputFilter = $factory->createInputFilter(array(
            'foo' => array(
                'name'       => 'foo',
                'required'   => false,
                'validators' => array(
                    array(
                        'name' => 'not_empty',
                    ),
                    array(
                        'name' => 'string_length',
                        'options' => array(
                            'min' => 3,
                            'max' => 5,
                        ),
                    ),
                ),
            ),
            'bar' => array(
                'allow_empty' => true,
                'filters'     => array(
                    array(
                        'name' => 'string_trim',
                    ),
                    array(
                        'name' => 'string_to_lower',
                        'options' => array(
                            'encoding' => 'ISO-8859-1',
                        ),
                    ),
                ),
            ),
            'baz' => array(
                'type'   => 'Zend\InputFilter\InputFilter',
                'foo' => array(
                    'name'       => 'foo',
                    'required'   => false,
                    'validators' => array(
                        array(
                            'name' => 'not_empty',
                        ),
                        array(
                            'name' => 'string_length',
                            'options' => array(
                                'min' => 3,
                                'max' => 5,
                            ),
                        ),
                    ),
                ),
                'bar' => array(
                    'allow_empty' => true,
                    'filters'     => array(
                        array(
                            'name' => 'string_trim',
                        ),
                        array(
                            'name' => 'string_to_lower',
                            'options' => array(
                                'encoding' => 'ISO-8859-1',
                            ),
                        ),
                    ),
                ),
            ),
            'bat' => array(
                'type' => 'ZendTest\InputFilter\TestAsset\CustomInput',
                'name' => 'bat',
            ),
            'zomg' => array(
                'name' => 'zomg',
                'continue_if_empty' => true,
            ),
        ));
        $this->assertInstanceOf('Zend\InputFilter\InputFilter', $inputFilter);
        $this->assertEquals(5, count($inputFilter));

        foreach (array('foo', 'bar', 'baz', 'bat', 'zomg') as $name) {
            $input = $inputFilter->get($name);

            switch ($name) {
                case 'foo':
                    $this->assertInstanceOf('Zend\InputFilter\Input', $input);
                    $this->assertFalse($input->isRequired());
                    $this->assertEquals(2, count($input->getValidatorChain()));
                    break;
                case 'bar':
                    $this->assertInstanceOf('Zend\InputFilter\Input', $input);
                    $this->assertTrue($input->allowEmpty());
                    $this->assertEquals(2, count($input->getFilterChain()));
                    break;
                case 'baz':
                    $this->assertInstanceOf('Zend\InputFilter\InputFilter', $input);
                    $this->assertEquals(2, count($input));
                    $foo = $input->get('foo');
                    $this->assertInstanceOf('Zend\InputFilter\Input', $foo);
                    $this->assertFalse($foo->isRequired());
                    $this->assertEquals(2, count($foo->getValidatorChain()));
                    $bar = $input->get('bar');
                    $this->assertInstanceOf('Zend\InputFilter\Input', $bar);
                    $this->assertTrue($bar->allowEmpty());
                    $this->assertEquals(2, count($bar->getFilterChain()));
                    break;
                case 'bat':
                    $this->assertInstanceOf('ZendTest\InputFilter\TestAsset\CustomInput', $input);
                    $this->assertEquals('bat', $input->getName());
                    break;
                case 'zomg':
                    $this->assertInstanceOf('Zend\InputFilter\Input', $input);
                    $this->assertTrue($input->continueIfEmpty());
            }
        }
    }

    public function testFactoryWillCreateInputFilterMatchingInputNameWhenNotSpecified()
    {
        $factory     = $this->createDefaultFactory();
        $inputFilter = $factory->createInputFilter(array(
            array('name' => 'foo')
        ));

        $this->assertTrue($inputFilter->has('foo'));
        $this->assertInstanceOf('Zend\InputFilter\Input', $inputFilter->get('foo'));
    }

    public function testFactoryAllowsPassingValidatorChainsInInputSpec()
    {
        $factory = $this->createDefaultFactory();
        $chain   = new Validator\ValidatorChain();
        $input   = $factory->createInput(array(
            'name'       => 'foo',
            'validators' => $chain,
        ));
        $this->assertInstanceOf('Zend\InputFilter\InputInterface', $input);
        $test = $input->getValidatorChain();
        $this->assertSame($chain, $test);
    }

    public function testFactoryAllowsPassingFilterChainsInInputSpec()
    {
        $factory = $this->createDefaultFactory();
        $chain   = new Filter\FilterChain();
        $input   = $factory->createInput(array(
            'name'    => 'foo',
            'filters' => $chain,
        ));
        $this->assertInstanceOf('Zend\InputFilter\InputInterface', $input);
        $test = $input->getFilterChain();
        $this->assertSame($chain, $test);
    }

    public function testFactoryAcceptsCollectionInputFilter()
    {
        $factory = $this->createDefaultFactory();

        /** @var CollectionInputFilter $inputFilter */
        $inputFilter = $factory->createInputFilter(array(
            'type'        => 'Zend\InputFilter\CollectionInputFilter',
            'required'    => true,
            'inputfilter' => new InputFilter(),
            'count'       => 3,
        ));

        $this->assertInstanceOf('Zend\InputFilter\CollectionInputFilter', $inputFilter);
        $this->assertInstanceOf('Zend\InputFilter\InputFilter', $inputFilter->getInputFilter());
        $this->assertTrue($inputFilter->getIsRequired());
        $this->assertEquals(3, $inputFilter->getCount());
    }

    public function testFactoryWillCreateInputWithErrorMessage()
    {
        $factory = $this->createDefaultFactory();
        $input   = $factory->createInput(array(
            'name'          => 'foo',
            'error_message' => 'My custom error message',
        ));
        $this->assertEquals('My custom error message', $input->getErrorMessage());
    }

    /**
     * @requires extension mbstring
     */
    public function testFactoryWillNotGetPrioritySetting()
    {
        //Reminder: Priority at which to enqueue filter; defaults to 1000 (higher executes earlier)
        $factory = $this->createDefaultFactory();
        $input   = $factory->createInput(array(
            'name'    => 'foo',
            'filters' => array(
                array(
                    'name'      => 'string_trim',
                    'priority'  => Filter\FilterChain::DEFAULT_PRIORITY - 1 // 999
                ),
                array(
                    'name'      => 'string_to_upper',
                    'priority'  => Filter\FilterChain::DEFAULT_PRIORITY + 1 //1001
                ),
                array(
                    'name'      => 'string_to_lower', // default priority 1000
                )
            )
        ));

        // We should have 3 filters
        $this->assertEquals(3, $input->getFilterChain()->count());

        // Filters should pop in the following order:
        // string_to_upper (1001), string_to_lower (1000), string_trim (999)
        $index = 0;
        foreach ($input->getFilterChain()->getFilters() as $filter) {
            switch ($index) {
                case 0:
                    $this->assertInstanceOf('Zend\Filter\StringToUpper', $filter);
                    break;
                case 1:
                    $this->assertInstanceOf('Zend\Filter\StringToLower', $filter);
                    break;
                case 2:
                    $this->assertInstanceOf('Zend\Filter\StringTrim', $filter);
                    break;
            }
            $index++;
        }
    }

    public function testConflictNameWithInputFilterType()
    {
        $factory = $this->createDefaultFactory();

        $inputFilter = $factory->createInputFilter(
            array(
                'type' => array(
                    'required' => true
                )
            )
        );

        $this->assertInstanceOf('Zend\InputFilter\InputFilter', $inputFilter);
        $this->assertTrue($inputFilter->has('type'));
    }

    public function testCustomFactoryInCollection()
    {
        $factory = new TestAsset\CustomFactory();
        /** @var CollectionInputFilter $inputFilter */
        $inputFilter = $factory->createInputFilter(array(
            'type'        => 'collection',
            'input_filter' => new InputFilter(),
        ));
        $this->assertInstanceOf('ZendTest\InputFilter\TestAsset\CustomFactory', $inputFilter->getFactory());
    }

    /**
     * @group 4838
     */
    public function testCanSetInputErrorMessage()
    {
        $factory = $this->createDefaultFactory();
        $input   = $factory->createInput(array(
            'name'          => 'test',
            'type'          => 'Zend\InputFilter\Input',
            'error_message' => 'Custom error message',
        ));
        $this->assertEquals('Custom error message', $input->getErrorMessage());
    }

    public function testSetInputFilterManagerWithServiceManager()
    {
        $inputFilterManager = new InputFilterPluginManager;
        $serviceManager = new ServiceManager\ServiceManager;
        $serviceManager->setService('ValidatorManager', new Validator\ValidatorPluginManager);
        $serviceManager->setService('FilterManager', new Filter\FilterPluginManager);
        $inputFilterManager->setServiceLocator($serviceManager);
        $factory = $this->createDefaultFactory();
        $factory->setInputFilterManager($inputFilterManager);
        $this->assertInstanceOf(
            'Zend\Validator\ValidatorPluginManager',
            $factory->getDefaultValidatorChain()->getPluginManager()
        );
        $this->assertInstanceOf(
            'Zend\Filter\FilterPluginManager',
            $factory->getDefaultFilterChain()->getPluginManager()
        );
    }

    public function testSetInputFilterManagerWithoutServiceManager()
    {
        $inputFilterManager = new InputFilterPluginManager();
        $factory = $this->createDefaultFactory();
        $factory->setInputFilterManager($inputFilterManager);
        $this->assertSame($inputFilterManager, $factory->getInputFilterManager());
    }

    public function testSetInputFilterManagerOnConstruct()
    {
        $inputFilterManager = new InputFilterPluginManager();
        $factory = new Factory($inputFilterManager);
        $this->assertSame($inputFilterManager, $factory->getInputFilterManager());
    }

    /**
     * @group 5691
     *
     * @covers \Zend\InputFilter\Factory::createInput
     */
    public function testSetsBreakChainOnFailure()
    {
        $factory = $this->createDefaultFactory();

        $this->assertTrue($factory->createInput(array('break_on_failure' => true))->breakOnFailure());

        $this->assertFalse($factory->createInput(array('break_on_failure' => false))->breakOnFailure());
    }

    public function testCanCreateInputFilterWithNullInputs()
    {
        $factory = $this->createDefaultFactory();

        $inputFilter = $factory->createInputFilter(array(
            'foo' => array(
                'name' => 'foo',
            ),
            'bar' => null,
            'baz' => array(
                'name' => 'baz',
            ),
        ));

        $this->assertInstanceOf('Zend\InputFilter\InputFilter', $inputFilter);
        $this->assertEquals(2, count($inputFilter));
        $this->assertTrue($inputFilter->has('foo'));
        $this->assertFalse($inputFilter->has('bar'));
        $this->assertTrue($inputFilter->has('baz'));
    }

    /**
     * @group 7010
     */
    public function testCanCreateInputFromProvider()
    {
        /** @var InputProviderInterface|MockObject $provider */
        $provider = $this->getMock('Zend\InputFilter\InputProviderInterface', array('getInputSpecification'));

        $provider
            ->expects($this->any())
            ->method('getInputSpecification')
            ->will($this->returnValue(array('name' => 'foo')));

        $factory = $this->createDefaultFactory();
        $input   = $factory->createInput($provider);

        $this->assertInstanceOf('Zend\InputFilter\InputInterface', $input);
    }

    /**
     * @group 7010
     */
    public function testCanCreateInputFilterFromProvider()
    {
        /** @var InputFilterProviderInterface|MockObject $provider */
        $provider = $this->getMock(
            'Zend\InputFilter\InputFilterProviderInterface',
            array('getInputFilterSpecification')
        );
        $provider
            ->expects($this->any())
            ->method('getInputFilterSpecification')
            ->will($this->returnValue(array(
                'foo' => array(
                    'name'       => 'foo',
                    'required'   => false,
                ),
                'baz' => array(
                    'name'       => 'baz',
                    'required'   => true,
                ),
            )));

        $factory     = $this->createDefaultFactory();
        $inputFilter = $factory->createInputFilter($provider);

        $this->assertInstanceOf('Zend\InputFilter\InputFilterInterface', $inputFilter);
    }

    public function testSuggestedTypeMayBePluginNameInInputFilterPluginManager()
    {
        $factory = $this->createDefaultFactory();
        $pluginManager = new InputFilterPluginManager();
        $pluginManager->setService('bar', new Input('bar'));
        $factory->setInputFilterManager($pluginManager);

        $input = $factory->createInput(array(
            'type' => 'bar'
        ));
        $this->assertSame('bar', $input->getName());
    }

    public function testInputFromPluginManagerMayBeFurtherConfiguredWithSpec()
    {
        $factory = $this->createDefaultFactory();
        $pluginManager = new InputFilterPluginManager();
        $pluginManager->setService('bar', $barInput = new Input('bar'));
        $this->assertTrue($barInput->isRequired());
        $factory->setInputFilterManager($pluginManager);

        $input = $factory->createInput(array(
            'type' => 'bar',
            'required' => false
        ));

        $this->assertFalse($input->isRequired());
        $this->assertSame('bar', $input->getName());
    }

    /**
     * @return Factory
     */
    protected function createDefaultFactory()
    {
        $factory = new Factory();

        return $factory;
    }

    /**
     * @param string $pluginName
     * @param mixed $pluginValue
     *
     * @return MockObject|InputFilterPluginManager
     */
    protected function createInputFilterPluginManagerMockForPlugin($pluginName, $pluginValue)
    {
        /** @var InputFilterPluginManager|MockObject $pluginManager */
        $pluginManager = $this->getMock('Zend\InputFilter\InputFilterPluginManager');
        $pluginManager->expects($this->atLeastOnce())
            ->method('has')
            ->with($pluginName)
            ->willReturn(true)
        ;
        $pluginManager->expects($this->atLeastOnce())
            ->method('get')
            ->with($pluginName)
            ->willReturn($pluginValue)
        ;
        return $pluginManager;
    }
}
