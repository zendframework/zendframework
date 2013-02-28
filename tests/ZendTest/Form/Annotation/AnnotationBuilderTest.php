<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Form
 */

namespace ZendTest\Form\Annotation;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Form\Annotation;
use ZendTest\Form\TestAsset;

class AnnotationBuilderTest extends TestCase
{
    public function setUp()
    {
        if (!defined('TESTS_ZEND_FORM_ANNOTATION_SUPPORT')
            || !constant('TESTS_ZEND_FORM_ANNOTATION_SUPPORT')
        ) {
            $this->markTestSkipped('Enable TESTS_ZEND_FORM_ANNOTATION_SUPPORT to test annotation parsing');
        }
    }

    public function testCanCreateFormFromStandardEntity()
    {
        $entity  = new TestAsset\Annotation\Entity();
        $builder = new Annotation\AnnotationBuilder();
        $form    = $builder->createForm($entity);

        $this->assertTrue($form->has('username'));
        $this->assertTrue($form->has('password'));

        $username = $form->get('username');
        $this->assertInstanceOf('Zend\Form\Element', $username);
        $this->assertEquals('required', $username->getAttribute('required'));

        $password = $form->get('password');
        $this->assertInstanceOf('Zend\Form\Element', $password);
        $attributes = $password->getAttributes();
        $this->assertEquals(array('type' => 'password', 'label' => 'Enter your password', 'name' => 'password'), $attributes);
        $this->assertNull($password->getAttribute('required'));

        $filter = $form->getInputFilter();
        $this->assertTrue($filter->has('username'));
        $this->assertTrue($filter->has('password'));

        $username = $filter->get('username');
        $this->assertTrue($username->isRequired());
        $this->assertEquals(1, count($username->getFilterChain()));
        $this->assertEquals(2, count($username->getValidatorChain()));

        $password = $filter->get('password');
        $this->assertTrue($password->isRequired());
        $this->assertEquals(1, count($password->getFilterChain()));
        $this->assertEquals(1, count($password->getValidatorChain()));
    }

    public function testCanCreateFormWithClassAnnotations()
    {
        $entity  = new TestAsset\Annotation\ClassEntity();
        $builder = new Annotation\AnnotationBuilder();
        $form    = $builder->createForm($entity);

        $this->assertTrue($form->has('keeper'));
        $this->assertFalse($form->has('keep'));
        $this->assertFalse($form->has('omit'));
        $this->assertEquals('some_name', $form->getName());

        $attributes = $form->getAttributes();
        $this->assertArrayHasKey('legend', $attributes);
        $this->assertEquals('Some Fieldset', $attributes['legend']);

        $filter = $form->getInputFilter();
        $this->assertInstanceOf('ZendTest\Form\TestAsset\Annotation\InputFilter', $filter);

        $keeper     = $form->get('keeper');
        $attributes = $keeper->getAttributes();
        $this->assertArrayHasKey('type', $attributes);
        $this->assertEquals('text', $attributes['type']);

        $this->assertObjectHasAttribute('validationGroup', $form);
        $this->assertAttributeEquals(array('omit', 'keep'), 'validationGroup', $form);
    }

    public function testComplexEntityCreationWithPriorities()
    {
        $entity  = new TestAsset\Annotation\ComplexEntity();
        $builder = new Annotation\AnnotationBuilder();
        $form    = $builder->createForm($entity);

        $this->assertEquals('user', $form->getName());
        $attributes = $form->getAttributes();
        $this->assertArrayHasKey('legend', $attributes);
        $this->assertEquals('Register', $attributes['legend']);

        $this->assertFalse($form->has('someComposedObject'));
        $this->assertTrue($form->has('user_image'));
        $this->assertTrue($form->has('email'));
        $this->assertTrue($form->has('password'));
        $this->assertTrue($form->has('username'));

        $email = $form->get('email');
        $test  = $form->getIterator()->getIterator()->current();
        $this->assertSame($email, $test, 'Test is element ' . $test->getName());

        $hydrator = $form->getHydrator();
        $this->assertInstanceOf('Zend\Stdlib\Hydrator\ObjectProperty', $hydrator);
    }

    public function testCanRetrieveOnlyFormSpecification()
    {
        $entity  = new TestAsset\Annotation\ComplexEntity();
        $builder = new Annotation\AnnotationBuilder();
        $spec    = $builder->getFormSpecification($entity);
        $this->assertInstanceOf('ArrayObject', $spec);
    }

    public function testAllowsExtensionOfEntities()
    {
        $entity  = new TestAsset\Annotation\ExtendedEntity();
        $builder = new Annotation\AnnotationBuilder();
        $form    = $builder->createForm($entity);

        $this->assertTrue($form->has('username'));
        $this->assertTrue($form->has('password'));
        $this->assertTrue($form->has('email'));

        $this->assertEquals('extended', $form->getName());
        $expected = array('username', 'password', 'email');
        $test     = array();
        foreach ($form as $element) {
            $test[] = $element->getName();
        }
        $this->assertEquals($expected, $test);
    }

    public function testAllowsSpecifyingFormAndElementTypes()
    {
        $entity  = new TestAsset\Annotation\TypedEntity();
        $builder = new Annotation\AnnotationBuilder();
        $form    = $builder->createForm($entity);

        $this->assertInstanceOf('ZendTest\Form\TestAsset\Annotation\Form', $form);
        $element = $form->get('typed_element');
        $this->assertInstanceOf('ZendTest\Form\TestAsset\Annotation\Element', $element);
    }

    public function testAllowsComposingChildEntities()
    {
        $entity  = new TestAsset\Annotation\EntityComposingAnEntity();
        $builder = new Annotation\AnnotationBuilder();
        $form    = $builder->createForm($entity);

        $this->assertTrue($form->has('composed'));
        $composed = $form->get('composed');
        $this->assertInstanceOf('Zend\Form\FieldsetInterface', $composed);
        $this->assertTrue($composed->has('username'));
        $this->assertTrue($composed->has('password'));

        $filter = $form->getInputFilter();
        $this->assertTrue($filter->has('composed'));
        $composed = $filter->get('composed');
        $this->assertInstanceOf('Zend\InputFilter\InputFilterInterface', $composed);
        $this->assertTrue($composed->has('username'));
        $this->assertTrue($composed->has('password'));
    }

    public function testCanHandleOptionsAnnotation()
    {
        $entity  = new TestAsset\Annotation\EntityUsingOptions();
        $builder = new Annotation\AnnotationBuilder();
        $form    = $builder->createForm($entity);

        $this->assertTrue($form->useAsBaseFieldset());

        $this->assertTrue($form->has('username'));

        $username = $form->get('username');
        $this->assertInstanceOf('Zend\Form\Element', $username);

        $this->assertEquals('Username:', $username->getLabel());
        $this->assertEquals(array('class' => 'label'), $username->getLabelAttributes());
    }

    public function testCanHandleHydratorArrayAnnotation()
    {
        $entity  = new TestAsset\Annotation\EntityWithHydratorArray();
        $builder = new Annotation\AnnotationBuilder();
        $form    = $builder->createForm($entity);

        $hydrator = $form->getHydrator();
        $this->assertInstanceOf('Zend\Stdlib\Hydrator\ClassMethods', $hydrator);
        $this->assertFalse($hydrator->getUnderscoreSeparatedKeys());
    }

    public function testAllowTypeAsElementNameInInputFilter()
    {
        $entity  = new TestAsset\Annotation\EntityWithTypeAsElementName();
        $builder = new Annotation\AnnotationBuilder();
        $form    = $builder->createForm($entity);

        $this->assertInstanceOf('Zend\Form\Form', $form);
        $element = $form->get('type');
        $this->assertInstanceOf('Zend\Form\Element', $element);
    }

    public function testAllowEmptyInput()
    {
        $entity  = new TestAsset\Annotation\SampleEntity();
        $builder = new Annotation\AnnotationBuilder();
        $form    = $builder->createForm($entity);

        $inputFilter = $form->getInputFilter();
        $sampleinput = $inputFilter->get('sampleinput');
        $this->assertTrue($sampleinput->allowEmpty());
    }

    public function testObjectElementAnnotation()
    {
        $entity = new TestAsset\Annotation\EntityUsingObjectProperty();
        $builder = new Annotation\AnnotationBuilder();
        $form = $builder->createForm($entity);

        $fieldset = $form->get('object');
        /* @var $fieldset Zend\Form\Fieldset */

        $this->assertInstanceOf('Zend\Form\Fieldset',$fieldset);
        $this->assertInstanceOf('ZendTest\Form\TestAsset\Annotation\Entity',$fieldset->getObject());
        $this->assertInstanceOf("Zend\Stdlib\Hydrator\ClassMethods",$fieldset->getHydrator());
        $this->assertFalse($fieldset->getHydrator()->getUnderscoreSeparatedKeys());
    }
}
