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

        // Check with valid data
        $data = array('foo' => '  This1sVal1d ');
        $filter->setData($data);
        $this->assertTrue($filter->isValid());
        $test = $filter->getValues();
        $this->assertArrayHasKey('foo', $test);
        $this->assertEquals('This1sVal1d', $test['foo']);
        
        // Check with invalid data
        $data = array('foo' => '  This1sN0tV@l1d ');
        $filter->setData($data);
        $this->assertFalse($filter->isValid());
        $test = $filter->getInvalidInput();
        $this->assertArrayHasKey('foo', $test);

        // Check with no data
        $data = array();
        $filter->setData($data);
        $this->assertFalse($filter->isValid());
        $test = $filter->getInvalidInput();
        $this->assertArrayHasKey('foo', $test);
    }

    public function testWillUseInputFilterSpecificationFromFieldsetInInputFilterIfNoMatchingInputFilterFound()
    {
        $this->markTestIncomplete();
    }

    public function testWillPopulateSubInputFilterFromInputSpecificationsOnFieldsetElements()
    {
        $this->markTestIncomplete();
    }
}
