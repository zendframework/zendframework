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
use Zend\Filter;
use Zend\InputFilter\Factory;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Validator;

class FactoryTest extends TestCase
{
    public function testFactoryDoesNotComposeFilterChainByDefault()
    {
        $factory = new Factory();
        $this->assertNull($factory->getDefaultFilterChain());
    }

    public function testFactoryDoesNotComposeValidatorChainByDefault()
    {
        $factory = new Factory();
        $this->assertNull($factory->getDefaultValidatorChain());
    }

    public function testFactoryAllowsInjectingFilterChain()
    {
        $factory     = new Factory();
        $filterChain = new Filter\FilterChain();
        $factory->setDefaultFilterChain($filterChain);
        $this->assertSame($filterChain, $factory->getDefaultFilterChain());
    }

    public function testFactoryAllowsInjectingValidatorChain()
    {
        $factory        = new Factory();
        $validatorChain = new Validator\ValidatorChain();
        $factory->setDefaultValidatorChain($validatorChain);
        $this->assertSame($validatorChain, $factory->getDefaultValidatorChain());
    }

    public function testFactoryUsesComposedFilterChainWhenCreatingNewInputObjects()
    {
        $factory       = new Factory();
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
        $factory          = new Factory();
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
        $factory          = new Factory();
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

    public function testFactoryWillCreateInputWithSuggestedFilters()
    {
        $factory      = new Factory();
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
        $factory = new Factory();
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

    public function testFactoryWillCreateInputWithSuggestedRequiredFlagAndImpliesAllowEmptyFlag()
    {
        $factory = new Factory();
        $input   = $factory->createInput(array(
            'name'     => 'foo',
            'required' => false,
        ));
        $this->assertInstanceOf('Zend\InputFilter\InputInterface', $input);
        $this->assertFalse($input->isRequired());
        $this->assertTrue($input->allowEmpty());
    }

    public function testFactoryWillCreateInputWithSuggestedRequiredFlagAndAlternativeAllowEmptyFlag()
    {
        $factory = new Factory();
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
        $factory = new Factory();
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
        $factory = new Factory();
        $input   = $factory->createInput(array(
            'name'        => 'foo',
        ));
        $this->assertInstanceOf('Zend\InputFilter\InputInterface', $input);
        $this->assertEquals('foo', $input->getName());
    }

    public function testFactoryAcceptsInputInterface()
    {
        $factory = new Factory();
        $input = new Input();

        $inputFilter = $factory->createInputFilter(array(
            'foo' => $input
        ));

        $this->assertInstanceOf('Zend\InputFilter\InputFilterInterface', $inputFilter);
        $this->assertTrue($inputFilter->has('foo'));
        $this->assertTrue($inputFilter->get('foo') === $input);
    }

    public function testFactoryAcceptsInputFilterInterface()
    {
        $factory = new Factory();
        $input = new InputFilter();

        $inputFilter = $factory->createInputFilter(array(
            'foo' => $input
        ));

        $this->assertInstanceOf('Zend\InputFilter\InputFilterInterface', $inputFilter);
        $this->assertTrue($inputFilter->has('foo'));
        $this->assertTrue($inputFilter->get('foo') === $input);
    }

    public function testFactoryWillCreateInputFilterAndAllInputObjectsFromGivenConfiguration()
    {
        $factory     = new Factory();
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
        ));
        $this->assertInstanceOf('Zend\InputFilter\InputFilter', $inputFilter);
        $this->assertEquals(4, count($inputFilter));

        foreach (array('foo', 'bar', 'baz', 'bat') as $name) {
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
            }
        }
    }

    public function testFactoryWillCreateInputFilterMatchingInputNameWhenNotSpecified()
    {
        $factory     = new Factory();
        $inputFilter = $factory->createInputFilter(array(
            array('name' => 'foo')
        ));

        $this->assertTrue($inputFilter->has('foo'));
        $this->assertInstanceOf('Zend\InputFilter\Input', $inputFilter->get('foo'));
    }

    public function testFactoryAllowsPassingValidatorChainsInInputSpec()
    {
        $factory = new Factory();
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
        $factory = new Factory();
        $chain   = new Filter\FilterChain();
        $input   = $factory->createInput(array(
            'name'    => 'foo',
            'filters' => $chain,
        ));
        $this->assertInstanceOf('Zend\InputFilter\InputInterface', $input);
        $test = $input->getFilterChain();
        $this->assertSame($chain, $test);
    }
}
