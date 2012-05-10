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
use Zend\Form\Factory;
use Zend\Form\Fieldset;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

class FormTest extends TestCase
{
    public function setUp()
    {
        $this->form = new Form();
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
}
