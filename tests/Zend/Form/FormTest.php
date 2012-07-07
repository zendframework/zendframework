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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Form;

use PHPUnit_Framework_TestCase as TestCase;
use stdClass;
use Zend\Form\Element;
use Zend\Form\Factory;
use Zend\Form\Fieldset;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFilterFactory;
use Zend\Stdlib\Hydrator;

class FormTest extends TestCase
{
    public function setUp()
    {
        $this->form = new Form();
    }

    public function getComposedEntity()
    {
        $address = new TestAsset\Entity\Address();
        $address->setStreet('1 Rue des Champs Elysées');

        $city = new TestAsset\Entity\City();
        $city->setName('Paris');
        $city->setZipCode('75008');

        $country = new TestAsset\Entity\Country();
        $country->setName('France');
        $country->setContinent('Europe');

        $city->setCountry($country);
        $address->setCity($city);

        return $address;
    }

    public function getOneToManyEntity()
    {
        $product = new TestAsset\Entity\Product();
        $product->setName('Chair');
        $product->setPrice(10);

        $firstCategory = new TestAsset\Entity\Category();
        $firstCategory->setName('Office');

        $secondCategory = new TestAsset\Entity\Category();
        $secondCategory->setName('Armchair');

        $product->setCategories(array($firstCategory, $secondCategory));

        return $product;
    }

    public function populateForm()
    {
        $this->form->add(new Element('foo'));
        $this->form->add(new Element('bar'));

        $fieldset = new Fieldset('foobar');
        $fieldset->add(new Element('foo'));
        $fieldset->add(new Element('bar'));
        $this->form->add($fieldset);

        $inputFilterFactory = new InputFilterFactory();
        $inputFilter = $inputFilterFactory->createInputFilter(array(
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
            'foobar' => array(
                'type'   => 'Zend\InputFilter\InputFilter',
                'foo' => array(
                    'name'       => 'foo',
                    'required'   => true,
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
        ));
        $this->form->setInputFilter($inputFilter);
    }

    public function testNoInputFilterPresentByDefault()
    {
        $this->assertNull($this->form->getInputFilter());
    }

    public function testCanComposeAnInputFilter()
    {
        $filter = new InputFilter();
        $this->form->setInputFilter($filter);
        $this->assertSame($filter, $this->form->getInputFilter());
    }

    public function testCallingIsValidRaisesExceptionIfNoDataSet()
    {
        $this->setExpectedException('Zend\Form\Exception\DomainException');
        $this->form->isValid();
    }

    public function testValidatesEntireDataSetByDefault()
    {
        $this->populateForm();
        $invalidSet = array(
            'foo' => 'a',
            'bar' => 'always valid',
            'foobar' => array(
                'foo' => 'a',
                'bar' => 'always valid',
            ),
        );
        $this->form->setData($invalidSet);
        $this->assertFalse($this->form->isValid());

        $validSet = array(
            'foo' => 'abcde',
            'bar' => 'always valid',
            'foobar' => array(
                'foo' => 'abcde',
                'bar' => 'always valid',
            ),
        );
        $this->form->setData($validSet);
        $this->assertTrue($this->form->isValid());
    }

    public function testSpecifyingValidationGroupForcesPartialValidation()
    {
        $this->populateForm();
        $invalidSet = array(
            'foo' => 'a',
        );
        $this->form->setValidationGroup('foo');
        $this->form->setData($invalidSet);
        $this->assertFalse($this->form->isValid());

        $validSet = array(
            'foo' => 'abcde',
        );
        $this->form->setData($validSet);
        $this->assertTrue($this->form->isValid());
    }

    public function testSpecifyingValidationGroupForNestedFieldsetsForcesPartialValidation()
    {
        $form = new TestAsset\NewProductForm();
        $form->setData(array(
            'product' => array(
                'name' => 'Chair'
            )
        ));

        $this->assertFalse($form->isValid());

        $form->setValidationGroup(array(
            'product' => array(
                'name'
            )
        ));

        $this->assertTrue($form->isValid());
    }

    public function testSettingValidateAllFlagAfterPartialValidationForcesFullValidation()
    {
        $this->populateForm();
        $this->form->setValidationGroup('foo');

        $validSet = array(
            'foo' => 'abcde',
        );
        $this->form->setData($validSet);
        $this->form->setValidationGroup(Form::VALIDATE_ALL);
        $this->assertFalse($this->form->isValid());
        $messages = $this->form->getMessages();
        $this->assertArrayHasKey('foobar', $messages, var_export($messages, 1));
    }

    public function testCallingGetDataPriorToValidationRaisesException()
    {
        $this->setExpectedException('Zend\Form\Exception\DomainException');
        $this->form->getData();
    }

    public function testAttemptingToValidateWithNoInputFilterAttachedRaisesException()
    {
        $this->setExpectedException('Zend\Form\Exception\DomainException');
        $this->form->isValid();
    }

    public function testCanRetrieveDataWithoutErrorsFollowingValidation()
    {
        $this->populateForm();
        $validSet = array(
            'foo' => 'abcde',
            'bar' => ' ALWAYS valid ',
            'foobar' => array(
                'foo' => 'abcde',
                'bar' => ' ALWAYS valid',
            ),
        );
        $this->form->setData($validSet);
        $this->form->isValid();

        $data = $this->form->getData();
        $this->assertInternalType('array', $data);
    }

    public function testCallingGetDataReturnsNormalizedDataByDefault()
    {
        $this->populateForm();
        $validSet = array(
            'foo' => 'abcde',
            'bar' => ' ALWAYS valid ',
            'foobar' => array(
                'foo' => 'abcde',
                'bar' => ' ALWAYS valid',
            ),
        );
        $this->form->setData($validSet);
        $this->form->isValid();
        $data = $this->form->getData();

        $expected = array(
            'foo' => 'abcde',
            'bar' => 'always valid',
            'foobar' => array(
                'foo' => 'abcde',
                'bar' => 'always valid',
            ),
        );
        $this->assertEquals($expected, $data);
    }

    public function testAllowsReturningRawValuesViaGetData()
    {
        $this->populateForm();
        $validSet = array(
            'foo' => 'abcde',
            'bar' => ' ALWAYS valid ',
            'foobar' => array(
                'foo' => 'abcde',
                'bar' => ' ALWAYS valid',
            ),
        );
        $this->form->setData($validSet);
        $this->form->isValid();
        $data = $this->form->getData(Form::VALUES_RAW);
        $this->assertEquals($validSet, $data);
    }

    public function testGetDataReturnsBoundModel()
    {
        $model = new stdClass;
        $this->form->setHydrator(new Hydrator\ObjectProperty());
        $this->populateForm();
        $this->form->setData(array());
        $this->form->bind($model);
        $this->form->isValid();
        $data = $this->form->getData();
        $this->assertSame($model, $data);
    }

    public function testGetDataCanReturnValuesAsArrayWhenModelIsBound()
    {
        $model = new stdClass;
        $validSet = array(
            'foo' => 'abcde',
            'bar' => 'always valid',
            'foobar' => array(
                'foo' => 'abcde',
                'bar' => 'always valid',
            ),
        );
        $this->populateForm();
        $this->form->setHydrator(new Hydrator\ObjectProperty());
        $this->form->bind($model);
        $this->form->setData($validSet);
        $this->form->isValid();
        $data = $this->form->getData(Form::VALUES_AS_ARRAY);
        $this->assertEquals($validSet, $data);
    }

    public function testValuesBoundToModelAreNormalizedByDefault()
    {
        $model = new stdClass;
        $validSet = array(
            'foo' => 'abcde',
            'bar' => ' ALWAYS valid ',
            'foobar' => array(
                'foo' => 'abcde',
                'bar' => ' always VALID',
            ),
        );
        $this->populateForm();
        $this->form->setHydrator(new Hydrator\ObjectProperty());
        $this->form->bind($model);
        $this->form->setData($validSet);
        $this->form->isValid();

        $this->assertObjectHasAttribute('foo', $model);
        $this->assertEquals($validSet['foo'], $model->foo);
        $this->assertObjectHasAttribute('bar', $model);
        $this->assertEquals('always valid', $model->bar);
        $this->assertObjectHasAttribute('foobar', $model);
        $this->assertEquals(array(
            'foo' => 'abcde',
            'bar' => 'always valid',
        ), $model->foobar);
    }

    public function testCanBindRawValuesToModel()
    {
        $model = new stdClass;
        $validSet = array(
            'foo' => 'abcde',
            'bar' => ' ALWAYS valid ',
            'foobar' => array(
                'foo' => 'abcde',
                'bar' => ' always VALID',
            ),
        );
        $this->populateForm();
        $this->form->setHydrator(new Hydrator\ObjectProperty());
        $this->form->bind($model, Form::VALUES_RAW);
        $this->form->setData($validSet);
        $this->form->isValid();

        $this->assertObjectHasAttribute('foo', $model);
        $this->assertEquals($validSet['foo'], $model->foo);
        $this->assertObjectHasAttribute('bar', $model);
        $this->assertEquals(' ALWAYS valid ', $model->bar);
        $this->assertObjectHasAttribute('foobar', $model);
        $this->assertEquals(array(
            'foo' => 'abcde',
            'bar' => ' always VALID',
        ), $model->foobar);
    }

    public function testGetDataReturnsSubsetOfDataWhenValidationGroupSet()
    {
        $validSet = array(
            'foo' => 'abcde',
            'bar' => ' ALWAYS valid ',
            'foobar' => array(
                'foo' => 'abcde',
                'bar' => ' always VALID',
            ),
        );
        $this->populateForm();
        $this->form->setValidationGroup('foo');
        $this->form->setData($validSet);
        $this->form->isValid();
        $data = $this->form->getData();
        $this->assertInternalType('array', $data);
        $this->assertEquals(1, count($data));
        $this->assertArrayHasKey('foo', $data);
        $this->assertEquals('abcde', $data['foo']);
    }

    public function testSettingValidationGroupBindsOnlyThoseValuesToModel()
    {
        $model = new stdClass;
        $validSet = array(
            'foo' => 'abcde',
            'bar' => ' ALWAYS valid ',
            'foobar' => array(
                'foo' => 'abcde',
                'bar' => ' always VALID',
            ),
        );
        $this->populateForm();
        $this->form->setHydrator(new Hydrator\ObjectProperty());
        $this->form->bind($model);
        $this->form->setData($validSet);
        $this->form->setValidationGroup('foo');
        $this->form->isValid();

        $this->assertObjectHasAttribute('foo', $model);
        $this->assertEquals('abcde', $model->foo);
        $this->assertObjectNotHasAttribute('bar', $model);
        $this->assertObjectNotHasAttribute('foobar', $model);
    }

    public function testCanBindModelsToArraySerializableObjects()
    {
        $model = new TestAsset\Model();
        $validSet = array(
            'foo' => 'abcde',
            'bar' => 'always valid',
            'foobar' => array(
                'foo' => 'abcde',
                'bar' => 'always valid',
            ),
        );
        $this->populateForm();
        $this->form->setHydrator(new Hydrator\ArraySerializable());
        $this->form->bind($model);
        $this->form->setData($validSet);
        $this->form->isValid();

        $this->assertEquals('abcde', $model->foo);
        $this->assertEquals('always valid', $model->bar);
        $this->assertEquals(array(
            'foo' => 'abcde',
            'bar' => 'always valid',
        ), $model->foobar);
    }

    public function testSetsInputFilterToFilterFromBoundModelIfModelImplementsInputLocatorAware()
    {
        $model = new TestAsset\ValidatingModel();
        $model->setInputFilter(new InputFilter());
        $this->populateForm();
        $this->form->bind($model);
        $this->assertSame($model->getInputFilter(), $this->form->getInputFilter());
    }

    public function testSettingDataShouldSetElementValueAttributes()
    {
        $this->populateForm();
        $data = array(
            'foo' => 'abcde',
            'bar' => 'always valid',
            'foobar' => array(
                'foo' => 'abcde',
                'bar' => 'always valid',
            ),
        );
        $this->form->setData($data);

        $fieldset = $this->form->get('foobar');
        foreach (array('foo', 'bar') as $name) {
            $element = $this->form->get($name);
            $this->assertEquals($data[$name], $element->getAttribute('value'));

            $element = $fieldset->get($name);
            $this->assertEquals($data[$name], $element->getAttribute('value'));
        }
    }

    public function testElementValuesArePopulatedFollowingBind()
    {
        $this->populateForm();
        $object = new stdClass();
        $object->foo = 'foobar';
        $object->bar = 'barbaz';
        $this->form->setHydrator(new Hydrator\ObjectProperty());
        $this->form->bind($object);

        $foo = $this->form->get('foo');
        $this->assertEquals('foobar', $foo->getAttribute('value'));
        $bar = $this->form->get('bar');
        $this->assertEquals('barbaz', $bar->getAttribute('value'));
    }

    public function testUsesBoundObjectAsDataSourceWhenNoDataSet()
    {
        $this->populateForm();
        $object         = new stdClass();
        $object->foo    = 'foos';
        $object->bar    = 'bar';
        $object->foobar = array(
            'foo' => 'foos',
            'bar' => 'bar',
        );
        $this->form->setHydrator(new Hydrator\ObjectProperty());
        $this->form->bind($object);

        $this->assertTrue($this->form->isValid());
    }

    public function testBindOnValidateIsTrueByDefault()
    {
        $this->assertTrue($this->form->bindOnValidate());
    }

    public function testCanDisableBindOnValidateFunctionality()
    {
        $this->form->setBindOnValidate(false);
        $this->assertFalse($this->form->bindOnValidate());
    }

    public function testCallingBindValuesWhenBindOnValidateIsDisabledPopulatesBoundObject()
    {
        $model = new stdClass;
        $validSet = array(
            'foo' => 'abcde',
            'bar' => ' ALWAYS valid ',
            'foobar' => array(
                'foo' => 'abcde',
                'bar' => ' always VALID',
            ),
        );
        $this->populateForm();
        $this->form->setHydrator(new Hydrator\ObjectProperty());
        $this->form->setBindOnValidate(false);
        $this->form->bind($model);
        $this->form->setData($validSet);
        $this->form->isValid();

        $this->assertObjectNotHasAttribute('foo', $model);
        $this->assertObjectNotHasAttribute('bar', $model);
        $this->assertObjectNotHasAttribute('foobar', $model);

        $this->form->bindValues();

        $this->assertObjectHasAttribute('foo', $model);
        $this->assertEquals($validSet['foo'], $model->foo);
        $this->assertObjectHasAttribute('bar', $model);
        $this->assertEquals('always valid', $model->bar);
        $this->assertObjectHasAttribute('foobar', $model);
        $this->assertEquals(array(
            'foo' => 'abcde',
            'bar' => 'always valid',
        ), $model->foobar);
    }

    public function testHasFactoryComposedByDefault()
    {
        $factory = $this->form->getFormFactory();
        $this->assertInstanceOf('Zend\Form\Factory', $factory);
    }

    public function testCanComposeFactory()
    {
        $factory = new Factory();
        $this->form->setFormFactory($factory);
        $this->assertSame($factory, $this->form->getFormFactory());
    }

    public function testCanAddElementsUsingSpecs()
    {
        $this->form->add(array(
            'name'       => 'foo',
            'attributes' => array(
                'type'         => 'text',
                'class'        => 'foo-class',
                'data-js-type' => 'my.form.text',
            ),
        ));
        $this->assertTrue($this->form->has('foo'));
        $element = $this->form->get('foo');
        $this->assertInstanceOf('Zend\Form\ElementInterface', $element);
        $this->assertEquals('foo', $element->getName());
        $this->assertEquals('text', $element->getAttribute('type'));
        $this->assertEquals('foo-class', $element->getAttribute('class'));
        $this->assertEquals('my.form.text', $element->getAttribute('data-js-type'));
    }

    public function testCanAddFieldsetsUsingSpecs()
    {
        $this->form->add(array(
            'type'       => 'Zend\Form\Fieldset',
            'name'       => 'foo',
            'attributes' => array(
                'type'         => 'fieldset',
                'class'        => 'foo-class',
                'data-js-type' => 'my.form.fieldset',
            ),
        ));
        $this->assertTrue($this->form->has('foo'));
        $fieldset = $this->form->get('foo');
        $this->assertInstanceOf('Zend\Form\FieldsetInterface', $fieldset);
        $this->assertEquals('foo', $fieldset->getName());
        $this->assertEquals('fieldset', $fieldset->getAttribute('type'));
        $this->assertEquals('foo-class', $fieldset->getAttribute('class'));
        $this->assertEquals('my.form.fieldset', $fieldset->getAttribute('data-js-type'));
    }

    public function testWillUseInputSpecificationFromElementInInputFilterIfNoMatchingInputFound()
    {
        $element = new TestAsset\ElementWithFilter('foo');
        $filter  = new InputFilter();
        $this->form->setInputFilter($filter);
        $this->form->add($element);

        $test = $this->form->getInputFilter();
        $this->assertSame($filter, $test);
        $this->assertTrue($filter->has('foo'));
        $input = $filter->get('foo');
        $filters = $input->getFilterChain();
        $this->assertEquals(1, count($filters));
        $validators = $input->getValidatorChain();
        $this->assertEquals(2, count($validators));
        $this->assertTrue($input->isRequired());
        $this->assertEquals('foo', $input->getName());
    }

    public function testWillUseInputFilterSpecificationFromFieldsetInInputFilterIfNoMatchingInputFilterFound()
    {
        $fieldset = new TestAsset\FieldsetWithInputFilter('set');
        $filter   = new InputFilter();
        $this->form->setInputFilter($filter);
        $this->form->add($fieldset);

        $test = $this->form->getInputFilter();
        $this->assertSame($filter, $test);
        $this->assertTrue($filter->has('set'));
        $input = $filter->get('set');
        $this->assertInstanceOf('Zend\InputFilter\InputFilterInterface', $input);
        $this->assertEquals(2, count($input));
        $this->assertTrue($input->has('foo'));
        $this->assertTrue($input->has('bar'));
    }

    public function testWillPopulateSubInputFilterFromInputSpecificationsOnFieldsetElements()
    {
        $element        = new TestAsset\ElementWithFilter('foo');
        $fieldset       = new Fieldset('set');
        $filter         = new InputFilter();
        $fieldsetFilter = new InputFilter();
        $fieldset->add($element);
        $filter->add($fieldsetFilter, 'set');
        $this->form->setInputFilter($filter);
        $this->form->add($fieldset);

        $test = $this->form->getInputFilter();
        $this->assertSame($filter, $test);
        $test = $filter->get('set');
        $this->assertSame($fieldsetFilter, $test);

        $this->assertEquals(1, count($fieldsetFilter));
        $this->assertTrue($fieldsetFilter->has('foo'));

        $input = $fieldsetFilter->get('foo');
        $this->assertInstanceOf('Zend\InputFilter\InputInterface', $input);
        $filters = $input->getFilterChain();
        $this->assertEquals(1, count($filters));
        $validators = $input->getValidatorChain();
        $this->assertEquals(2, count($validators));
        $this->assertTrue($input->isRequired());
        $this->assertEquals('foo', $input->getName());
    }

    public function testDisablingUseInputFilterDefaultsFlagDisablesInputFilterScanning()
    {
        $element        = new TestAsset\ElementWithFilter('foo');
        $fieldset       = new Fieldset('set');
        $filter         = new InputFilter();
        $fieldsetFilter = new InputFilter();
        $fieldset->add($element);
        $filter->add($fieldsetFilter, 'set');
        $this->form->setInputFilter($filter);
        $this->form->add($fieldset);

        $this->form->setUseInputFilterDefaults(false);
        $test = $this->form->getInputFilter();
        $this->assertSame($filter, $test);
        $this->assertSame($fieldsetFilter, $test->get('set'));
        $this->assertEquals(0, count($fieldsetFilter));
    }

    public function testCallingPrepareEnsuresInputFilterRetrievesDefaults()
    {
        $element = new TestAsset\ElementWithFilter('foo');
        $filter  = new InputFilter();
        $this->form->setInputFilter($filter);
        $this->form->add($element);
        $this->form->prepare();

        $this->assertTrue($filter->has('foo'));
        $input = $filter->get('foo');
        $filters = $input->getFilterChain();
        $this->assertEquals(1, count($filters));
        $validators = $input->getValidatorChain();
        $this->assertEquals(2, count($validators));
        $this->assertTrue($input->isRequired());
        $this->assertEquals('foo', $input->getName());
    }

    public function testCanProperlyPrepareNestedFieldsets()
    {
        $this->form->add(array(
            'name'       => 'foo',
            'attributes' => array(
                'type'         => 'text'
            )
        ));

        $this->form->add(array(
            'type' => 'ZendTest\Form\TestAsset\BasicFieldset'
        ));

        $this->form->prepare();

        $this->assertEquals('foo', $this->form->get('foo')->getName());

        $basicFieldset = $this->form->get('basic_fieldset');
        $this->assertEquals('basic_fieldset[field]', $basicFieldset->get('field')->getName());

        $nestedFieldset = $basicFieldset->get('nested_fieldset');
        $this->assertEquals('basic_fieldset[nested_fieldset][anotherField]', $nestedFieldset->get('anotherField')
                                                                                            ->getName());
    }

    public function testCanCorrectlyExtractDataFromComposedEntities()
    {
        $address = $this->getComposedEntity();

        $form = new TestAsset\CreateAddressForm();
        $form->bind($address);
        $form->setBindOnValidate(false);

        if ($form->isValid()) {
            $this->assertEquals($address, $form->getData());
        }
    }

    public function testCanCorrectlyPopulateDataToComposedEntities()
    {
        $address = $this->getComposedEntity();
        $emptyAddress = new TestAsset\Entity\Address();

        $form = new TestAsset\CreateAddressForm();
        $form->bind($emptyAddress);

        $data = array(
            'address' => array(
                'street' => '1 Rue des Champs Elysées',
                'city' => array(
                    'name' => 'Paris',
                    'zipCode' => '75008',
                    'country' => array(
                        'name' => 'France',
                        'continent' => 'Europe'
                    )
                )
            )
        );

        $form->setData($data);

        if ($form->isValid()) {
            $this->assertEquals($address, $emptyAddress, var_export($address, 1) . "\n\n" . var_export($emptyAddress, 1));
        }
    }

    public function testCanCorrectlyExtractDataFromOneToManyRelationship()
    {
        $product = $this->getOneToManyEntity();

        $form = new TestAsset\NewProductForm();
        $form->bind($product);
        $form->setBindOnValidate(false);

        if ($form->isValid()) {
            $this->assertEquals($product, $form->getData());
        }
    }

    public function testCanCorrectlyPopulateDataToOneToManyEntites()
    {
        $product = $this->getOneToManyEntity();
        $emptyProduct = new TestAsset\Entity\Product();

        $form = new TestAsset\NewProductForm();
        $form->bind($emptyProduct);

        $data = array(
            'product' => array(
                'name' => 'Chair',
                'price' => 10,
                'categories' => array(
                    array(
                        'name' => 'Office'
                    ),
                    array(
                        'name' => 'Armchair'
                    )
                )
            )
        );

        $form->setData($data);

        if ($form->isValid()) {
            $this->assertEquals($product, $emptyProduct, var_export($product, 1) . "\n\n" . var_export($emptyProduct, 1));
        }
    }
}
