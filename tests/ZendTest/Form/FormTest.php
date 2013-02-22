<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Form
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
use ZendTest\Form\TestAsset\Entity;

class FormTest extends TestCase
{
    /**
     * @var Form
     */
    protected $form;

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

    public function populateHydratorStrategyForm()
    {
        $this->form->add(new Element('entities'));
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

    public function testInputFilterPresentByDefault()
    {
        $this->assertNotNull($this->form->getInputFilter());
    }

    public function testCanComposeAnInputFilter()
    {
        $filter = new InputFilter();
        $this->form->setInputFilter($filter);
        $this->assertSame($filter, $this->form->getInputFilter());
    }

    public function testDefaultNonRequiredInputFilterIsSet()
    {
        $this->form->add(new Element('foo'));
        $inputFilter = $this->form->getInputFilter();
        $fooInput = $inputFilter->get('foo');
        $this->assertFalse($fooInput->isRequired());
    }

    public function testInputProviderInterfaceAddsInputFilters()
    {
        $form = new TestAsset\InputFilterProvider();

        $inputFilter = $form->getInputFilter();
        $fooInput = $inputFilter->get('foo');
        $this->assertTrue($fooInput->isRequired());
    }

    public function testCallingIsValidRaisesExceptionIfNoDataSet()
    {
        $this->setExpectedException('Zend\Form\Exception\DomainException');
        $this->form->isValid();
    }

    public function testHasValidatedFlag()
    {
        $form = new TestAsset\NewProductForm();

        $this->assertFalse($form->hasValidated());

        $form->setData(array());
        $form->isValid();


        $this->assertTrue($form->hasValidated());
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

    public function testSetValidationGroupWithNoArgumentsRaisesException()
    {
        $this->setExpectedException('Zend\Form\Exception\InvalidArgumentException');
        $this->form->setValidationGroup();
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

    /**
     * @group ZF2-336
     */
    public function testCanAddFileEnctypeAttribute()
    {
        $file = new Element\File('file_resource');
        $file
            ->setOptions(array())
            ->setLabel('File');
        $this->form->add($file);

        $this->form->prepare();
        $enctype = $this->form->getAttribute('enctype');
        $this->assertNotEmpty($enctype);
        $this->assertEquals($enctype, 'multipart/form-data');
    }

    /**
     * @group ZF2-336
     */
    public function testCanAddFileEnctypeFromCollectionAttribute()
    {
        $file = new Element\File('file_resource');
        $file
            ->setOptions(array())
            ->setLabel('File');

        $fileCollection = new Element\Collection('collection');
        $fileCollection->setOptions(array(
             'count' => 2,
             'allow_add' => false,
             'allow_remove' => false,
             'target_element' => $file,
        ));
        $this->form->add($fileCollection);

        $this->form->prepare();
        $enctype = $this->form->getAttribute('enctype');
        $this->assertNotEmpty($enctype);
        $this->assertEquals($enctype, 'multipart/form-data');
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

    public function testSettingDataShouldSetElementValues()
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
            $this->assertEquals($data[$name], $element->getValue());

            $element = $fieldset->get($name);
            $this->assertEquals($data[$name], $element->getValue());
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
        $this->assertEquals('foobar', $foo->getValue());
        $bar = $this->form->get('bar');
        $this->assertEquals('barbaz', $bar->getValue());
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

    public function testFormAsFieldsetWillBindValuesToObject()
    {
        $parentForm        = new Form('parent');
        $parentFormObject  = new \ArrayObject(array('parentId' => null));
        $parentFormElement = new Element('parentId');
        $parentForm->setObject($parentFormObject);
        $parentForm->add($parentFormElement);

        $childForm        = new Form('child');
        $childFormObject  = new \ArrayObject(array('childId' => null));
        $childFormElement = new Element('childId');
        $childForm->setObject($childFormObject);
        $childForm->add($childFormElement);

        $parentForm->add($childForm);

        $data = array(
            'parentId' => 'mpinkston was here',
            'child' => array(
                'childId' => 'testing 123'
            )
        );

        $parentForm->setData($data);
        $this->assertTrue($parentForm->isValid());
        $this->assertEquals($data['parentId'], $parentFormObject['parentId']);
        $this->assertEquals($data['child']['childId'], $childFormObject['childId']);
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

    public function testWillUseFormInputFilterOverrideOverInputSpecificationFromElement()
    {
        $element = new TestAsset\ElementWithFilter('foo');
        $filter  = new InputFilter();
        $filterFactory = new InputFilterFactory();
        $filter = $filterFactory->createInputFilter(array(
            'foo' => array(
                'name'       => 'foo',
                'required'   => false,
            ),
        ));
        $this->form->setPreferFormInputFilter(true);
        $this->form->setInputFilter($filter);
        $this->form->add($element);

        $test = $this->form->getInputFilter();
        $this->assertSame($filter, $test);
        $this->assertTrue($filter->has('foo'));
        $input = $filter->get('foo');
        $filters = $input->getFilterChain();
        $this->assertEquals(0, count($filters));
        $validators = $input->getValidatorChain();
        $this->assertEquals(0, count($validators));
        $this->assertFalse($input->isRequired());
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

        // Issue #2586 Ensure default filters aren't added twice
        $filter = $this->form->getInputFilter();

        $this->assertTrue($filter->has('foo'));
        $input = $filter->get('foo');
        $filters = $input->getFilterChain();
        $this->assertEquals(1, count($filters));
        $validators = $input->getValidatorChain();
        $this->assertEquals(2, count($validators));
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

        $this->assertEquals(true, $form->isValid());
        $this->assertEquals($address, $form->getData());
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

        $this->assertEquals(true, $form->isValid());
        $this->assertEquals($address, $emptyAddress, var_export($address, 1) . "\n\n" . var_export($emptyAddress, 1));
    }

    public function testCanCorrectlyExtractDataFromOneToManyRelationship()
    {
        $product = $this->getOneToManyEntity();

        $form = new TestAsset\NewProductForm();
        $form->bind($product);
        $form->setBindOnValidate(false);

        $this->assertEquals(true, $form->isValid());
        $this->assertEquals($product, $form->getData());
    }

    public function testCanCorrectlyPopulateDataToOneToManyEntites()
    {
        if (!extension_loaded('intl')) {
            $this->markTestSkipped("The Intl extension is not loaded");
        }
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

        $this->assertEquals(true, $form->isValid());
        $this->assertEquals($product, $emptyProduct, var_export($product, 1) . "\n\n" . var_export($emptyProduct, 1));
    }

    public function testAssertElementsNamesAreNotWrappedAroundFormNameByDefault()
    {
        $form = new \ZendTest\Form\TestAsset\FormCollection();
        $form->prepare();

        $this->assertEquals('colors[0]', $form->get('colors')->get('0')->getName());
        $this->assertEquals('fieldsets[0][field]', $form->get('fieldsets')->get('0')->get('field')->getName());
    }

    public function testAssertElementsNamesCanBeWrappedAroundFormName()
    {
        $form = new \ZendTest\Form\TestAsset\FormCollection();
        $form->setWrapElements(true);
        $form->setName('foo');
        $form->prepare();

        $this->assertEquals('foo[colors][0]', $form->get('colors')->get('0')->getName());
        $this->assertEquals('foo[fieldsets][0][field]', $form->get('fieldsets')->get('0')->get('field')->getName());
    }

    public function testUnsetValuesNotBound()
    {
        $model = new stdClass;
        $validSet = array(
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
        $data = $this->form->getData();
        $this->assertObjectNotHasAttribute('foo', $data);
        $this->assertObjectHasAttribute('bar', $data);
    }

    public function testRemoveCollectionFromValidationGroupWhenZeroCountAndNoData()
    {
        $dataWithoutCollection = array(
            'foo' => 'bar'
        );
        $this->populateForm();
        $this->form->add(array(
            'type' => 'Zend\Form\Element\Collection',
            'name' => 'categories',
            'options' => array(
                'count' => 0,
                'target_element' => array(
                    'type' => 'ZendTest\Form\TestAsset\CategoryFieldset'
                )
            )
        ));
        $this->form->setValidationGroup(array(
            'foo',
            'categories' => array(
                'name'
            )
        ));
        $this->form->setData($dataWithoutCollection);
        $this->assertTrue($this->form->isValid());
    }

    public function testFieldsetValidationGroupStillPreparedWhenEmptyData()
    {
        $emptyData = array();

        $this->populateForm();
        $this->form->get('foobar')->add(array(
            'type' => 'Zend\Form\Element\Collection',
            'name' => 'categories',
            'options' => array(
                'count' => 0,
                'target_element' => array(
                    'type' => 'ZendTest\Form\TestAsset\CategoryFieldset'
                )
            )
        ));

        $this->form->setValidationGroup(array(
            'foobar' => array(
                'categories' => array(
                    'name'
                )
            )
        ));

        $this->form->setData($emptyData);
        $this->assertFalse($this->form->isValid());
    }

    public function testApplyObjectInputFilterToBaseFieldsetAndApplyValidationGroup()
    {
        $fieldset = new Fieldset('foobar');
        $fieldset->add(new Element('foo'));
        $fieldset->setUseAsBaseFieldset(true);
        $this->form->add($fieldset);
        $this->form->setValidationGroup(array(
            'foobar'=> array(
                'foo',
            )
        ));

        $inputFilterFactory = new InputFilterFactory();
        $inputFilter = $inputFilterFactory->createInputFilter(array(
            'foo' => array(
                'name'       => 'foo',
                'required'   => true,
            ),
        ));
        $model = new TestAsset\ValidatingModel();
        $model->setInputFilter($inputFilter);
        $this->form->bind($model);

        $this->form->setData(array());
        $this->assertFalse($this->form->isValid());

        $validSet = array(
            'foobar' => array(
                'foo' => 'abcde',
            )
        );
        $this->form->setData($validSet);
        $this->assertTrue($this->form->isValid());
    }

    public function testFormValidationCanHandleNonConsecutiveKeysOfCollectionInData()
    {
        $dataWithCollection = array(
            'foo' => 'bar',
            'categories' => array(
                0 => array('name' => 'cat1'),
                1 => array('name' => 'cat2'),
                3 => array('name' => 'cat3'),
            ),
        );
        $this->populateForm();
        $this->form->add(array(
            'type' => 'Zend\Form\Element\Collection',
            'name' => 'categories',
            'options' => array(
                'count' => 1,
                'allow_add' => true,
                'target_element' => array(
                    'type' => 'ZendTest\Form\TestAsset\CategoryFieldset'
                )
            )
        ));
        $this->form->setValidationGroup(array(
            'foo',
            'categories' => array(
                'name'
            )
        ));
        $this->form->setData($dataWithCollection);
        $this->assertTrue($this->form->isValid());
    }

    public function testAddNonBaseFieldsetObjectInputFilterToFormInputFilter()
    {
        $fieldset = new Fieldset('foobar');
        $fieldset->add(new Element('foo'));
        $fieldset->setUseAsBaseFieldset(false);
        $this->form->add($fieldset);

        $inputFilterFactory = new InputFilterFactory();
        $inputFilter = $inputFilterFactory->createInputFilter(array(
            'foo' => array(
                'name'       => 'foo',
                'required'   => true,
            ),
        ));
        $model = new TestAsset\ValidatingModel();
        $model->setInputFilter($inputFilter);

        $this->form->bind($model);

        $this->assertInstanceOf('Zend\InputFilter\InputFilterInterface', $this->form->getInputFilter()->get('foobar'));
    }

    public function testExtractDataHydratorStrategy()
    {
        $this->populateHydratorStrategyForm();

        $hydrator = new Hydrator\ObjectProperty();
        $hydrator->addStrategy('entities', new TestAsset\HydratorStrategy());
        $this->form->setHydrator($hydrator);

        $model = new TestAsset\HydratorStrategyEntityA();
        $this->form->bind($model);

        $validSet = array(
            'entities' => array(
                111,
                333
            ),
        );

        $this->form->setData($validSet);
        $this->form->isValid();

        $data = $this->form->getData(Form::VALUES_AS_ARRAY);
        $this->assertEquals($validSet, $data);

        $entities = $model->getEntities();
        $this->assertCount(2, $entities);

        $this->assertEquals(111, $entities[0]->getField1());
        $this->assertEquals(333, $entities[1]->getField1());

        $this->assertEquals('AAA', $entities[0]->getField2());
        $this->assertEquals('CCC', $entities[1]->getField2());
    }

    public function testSetDataWithNullValues()
    {
        $this->populateForm();

        $set = array(
            'foo' => null,
            'bar' => 'always valid',
            'foobar' => array(
                'foo' => 'abcde',
                'bar' => 'always valid',
            ),
        );
        $this->form->setData($set);
        $this->assertTrue($this->form->isValid());
    }

    public function testHydratorAppliedToBaseFieldset()
    {
        $fieldset = new Fieldset('foobar');
        $fieldset->add(new Element('foo'));
        $fieldset->setUseAsBaseFieldset(true);
        $this->form->add($fieldset);
        $this->form->setHydrator(new Hydrator\ArraySerializable());

        $baseHydrator = $this->form->get('foobar')->getHydrator();
        $this->assertInstanceOf('Zend\Stdlib\Hydrator\ArraySerializable', $baseHydrator);
    }

    public function testBindWithWrongFlagRaisesException()
    {
        $model = new stdClass;
        $this->setExpectedException('Zend\Form\Exception\InvalidArgumentException');
        $this->form->bind($model, Form::VALUES_AS_ARRAY);
    }

    public function testSetBindOnValidateWrongFlagRaisesException()
    {
        $this->setExpectedException('Zend\Form\Exception\InvalidArgumentException');
        $this->form->setBindOnValidate(Form::VALUES_AS_ARRAY);
    }

    public function testSetDataOnValidateWrongFlagRaisesException()
    {
        $this->setExpectedException('Zend\Form\Exception\InvalidArgumentException');
        $this->form->setData(null);
    }

    public function testSetDataIsTraversable()
    {
        $this->form->setData(new \ArrayObject(array('foo' => 'bar')));
        $this->assertTrue($this->form->isValid());
    }

    public function testResetPasswordValueIfFormIsNotValid()
    {
        $this->form->add(array(
            'type' => 'Zend\Form\Element\Password' ,
            'name' => 'password'
        ));

        $this->form->add(array(
            'type' => 'Zend\Form\Element\Email',
            'name' => 'email'
        ));

        $this->form->setData(array(
            'password' => 'azerty',
            'email'    => 'wrongEmail'
        ));

        $this->assertFalse($this->form->isValid());
        $this->form->prepare();

        $this->assertEquals('', $this->form->get('password')->getValue());
    }

    public function testCorrectlyHydrateBaseFieldsetWhenHydratorThatDoesNotIgnoreInvalidDataIsUsed()
    {
        $fieldset = new Fieldset('example');
        $fieldset->add(array(
            'name' => 'foo'
        ));

        // Add an hydrator that ignores if values does not exist in the
        $fieldset->setObject(new Entity\SimplePublicProperty());
        $fieldset->setHydrator(new \Zend\Stdlib\Hydrator\ObjectProperty());

        $this->form->add($fieldset);
        $this->form->setBaseFieldset($fieldset);
        $this->form->setHydrator(new \Zend\Stdlib\Hydrator\ObjectProperty());

        // Add some inputs that do not belong to the base fieldset
        $this->form->add(array(
            'type' => 'Zend\Form\Element\Submit',
            'name' => 'submit'
        ));

        $object = new Entity\SimplePublicProperty();
        $this->form->bind($object);

        $this->form->setData(array(
            'submit' => 'Confirm',
            'example' => array(
                'foo' => 'value example'
            )
        ));

        $this->assertTrue($this->form->isValid());

        // Make sure the object was not hydrated at the "form level"
        $this->assertFalse(isset($object->submit));
    }

    public function testPrepareBindDataAllowsFilterToConvertStringToArray()
    {
        $data = array(
            'foo' => '1,2',
        );

        $filteredData = array(
            'foo' => array(1, 2)
        );

        $element = new TestAsset\ElementWithStringToArrayFilter('foo');
        $hydrator = $this->getMock('Zend\Stdlib\Hydrator\ArraySerializable');
        $hydrator->expects($this->any())->method('hydrate')->with($filteredData, $this->anything());

        $this->form->add($element);
        $this->form->setHydrator($hydrator);
        $this->form->setObject(new stdClass());
        $this->form->setData($data);
        $this->form->bindValues($data);
    }

    public function testGetValidationGroup()
    {
        $group = array('foo');
        $this->form->setValidationGroup($group);
        $this->assertEquals($group, $this->form->getValidationGroup());
    }

    public function testGetValidationGroupReturnsNullWhenNoneSet()
    {
        $this->assertNull($this->form->getValidationGroup());
    }
}
