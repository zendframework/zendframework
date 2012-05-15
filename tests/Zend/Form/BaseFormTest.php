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
use Zend\Form\Fieldset;
use Zend\Form\BaseForm as Form;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFilterFactory;
use Zend\Stdlib\Hydrator;

/**
 * @category   Zend
 * @package    Zend_Form
 * @subpackage UnitTest
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class BaseFormTest extends TestCase
{
    public function setUp()
    {
        $this->form = new Form;
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
}
