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
use Zend\Form\Element;
use Zend\Form\Form;
use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilter;

/**
 * @category   Zend
 * @package    Zend_Form
 * @subpackage UnitTest
 */
class FieldsetTest extends TestCase
{
    public function setUp()
    {
        $this->fieldset = new Fieldset();
    }

    public function populateFieldset()
    {
        $this->fieldset->add(new Element('foo'));
        $this->fieldset->add(new Element('bar'));
        $this->fieldset->add(new Element('baz'));

        $subFieldset = new Fieldset('foobar');
        $subFieldset->add(new Element('foo'));
        $subFieldset->add(new Element('bar'));
        $subFieldset->add(new Element('baz'));
        $this->fieldset->add($subFieldset);

        $subFieldset = new Fieldset('barbaz');
        $subFieldset->add(new Element('foo'));
        $subFieldset->add(new Element('bar'));
        $subFieldset->add(new Element('baz'));
        $this->fieldset->add($subFieldset);
    }

    public function getMessages()
    {
        return array(
            'foo' => array(
                'Foo message 1',
            ),
            'bar' => array(
                'Bar message 1',
                'Bar message 2',
            ),
            'baz' => array(
                'Baz message 1',
            ),
            'foobar' => array(
                'foo' => array(
                    'Foo message 1',
                ),
                'bar' => array(
                    'Bar message 1',
                    'Bar message 2',
                ),
                'baz' => array(
                    'Baz message 1',
                ),
            ),
            'barbaz' => array(
                'foo' => array(
                    'Foo message 1',
                ),
                'bar' => array(
                    'Bar message 1',
                    'Bar message 2',
                ),
                'baz' => array(
                    'Baz message 1',
                ),
            ),
        );
    }

    public function testExtractOnAnEmptyRelationship()
    {
        $form = new TestAsset\FormCollection();
        $form->populateValues(array('fieldsets' => array()));
    }

    public function testPopulateValuesWithInvalidElementRaisesException()
    {
        $this->setExpectedException('Zend\Form\Exception\InvalidArgumentException');
        $this->fieldset->populateValues(null);
    }

    public function testFieldsetIsEmptyByDefault()
    {
        $this->assertEquals(0, count($this->fieldset));
    }

    public function testCanAddElementsToFieldset()
    {
        $this->fieldset->add(new Element('foo'));
        $this->assertEquals(1, count($this->fieldset));
    }

    public function testCanSetCustomOptionFromConstructor()
    {
        $fieldset = new Fieldset('foo', array(
            'custom' => 'option'
        ));
        $options = $fieldset->getOptions();
        $this->assertArrayHasKey('custom', $options);
        $this->assertEquals('option', $options['custom']);
    }

    public function testAddWithInvalidElementRaisesException()
    {
        $this->setExpectedException('Zend\Form\Exception\InvalidArgumentException');
        $this->fieldset->add(null);
    }

    public function testCanGrabElementByNameWhenNotProvidedWithAlias()
    {
        $element = new Element('foo');
        $this->fieldset->add($element);
        $this->assertSame($element, $this->fieldset->get('foo'));
    }

    public function testElementMayBeRetrievedByAliasProvidedWhenAdded()
    {
        $element = new Element('foo');
        $this->fieldset->add($element, array('name' => 'bar'));
        $this->assertSame($element, $this->fieldset->get('bar'));
    }

    public function testElementNameIsChangedToAliasWhenAdded()
    {
        $element = new Element('foo');
        $this->fieldset->add($element, array('name' => 'bar'));
        $this->assertEquals('bar', $element->getName());
    }

    public function testCannotRetrieveElementByItsNameWhenProvidingAnAliasDuringAddition()
    {
        $element = new Element('foo');
        $this->fieldset->add($element, array('name' => 'bar'));
        $this->assertFalse($this->fieldset->has('foo'));
    }

    public function testAddingAnElementWithNoNameOrAliasWillRaiseException()
    {
        $element = new Element();
        $this->setExpectedException('Zend\Form\Exception\InvalidArgumentException');
        $this->fieldset->add($element);
    }

    public function testCanAddFieldsetsToFieldset()
    {
        $fieldset = new Fieldset('foo');
        $this->fieldset->add($fieldset);
        $this->assertEquals(1, count($this->fieldset));
    }

    public function testCanRemoveElementsByName()
    {
        $element = new Element('foo');
        $this->fieldset->add($element);
        $this->assertTrue($this->fieldset->has('foo'));
        $this->fieldset->remove('foo');
        $this->assertFalse($this->fieldset->has('foo'));
    }

    public function testCanRemoveFieldsetsByName()
    {
        $fieldset = new Fieldset('foo');
        $this->fieldset->add($fieldset);
        $this->assertTrue($this->fieldset->has('foo'));
        $this->fieldset->remove('foo');
        $this->assertFalse($this->fieldset->has('foo'));
    }

    public function testCanRemoveElementsByWrongName()
    {
        $element = new Element('foo');
        $this->fieldset->add($element);
        $element2 = new Element('bar');
        $this->fieldset->add($element2);
        $this->assertTrue($this->fieldset->has('foo'));
        $this->assertTrue($this->fieldset->has('bar'));

        // remove wrong element, bar still available
        $this->fieldset->remove('bars');
        $this->assertTrue($this->fieldset->has('foo'));
        $this->assertTrue($this->fieldset->has('bar'));

        $this->fieldset->remove('bar');
        $this->assertTrue($this->fieldset->has('foo'));
        $this->assertFalse($this->fieldset->has('bar'));
    }

    public function testCanRetrieveAllAttachedElementsSeparateFromFieldsetsAtOnce()
    {
        $this->populateFieldset();
        $elements = $this->fieldset->getElements();
        $this->assertEquals(3, count($elements));
        foreach (array('foo', 'bar', 'baz') as $name) {
            $this->assertTrue(isset($elements[$name]));
            $element = $this->fieldset->get($name);
            $this->assertSame($element, $elements[$name]);
        }
    }

    public function testCanRetrieveAllAttachedFieldsetsSeparateFromElementsAtOnce()
    {
        $this->populateFieldset();
        $fieldsets = $this->fieldset->getFieldsets();
        $this->assertEquals(2, count($fieldsets));
        foreach (array('foobar', 'barbaz') as $name) {
            $this->assertTrue(isset($fieldsets[$name]));
            $fieldset = $this->fieldset->get($name);
            $this->assertSame($fieldset, $fieldsets[$name]);
        }
    }

    public function testCanSetAndRetrieveErrorMessagesForAllElementsAndFieldsets()
    {
        $this->populateFieldset();
        $messages = $this->getMessages();
        $this->fieldset->setMessages($messages);
        $test = $this->fieldset->getMessages();
        $this->assertEquals($messages, $test);
    }

    public function testSetMessagesWithInvalidElementRaisesException()
    {
        $this->setExpectedException('Zend\Form\Exception\InvalidArgumentException');
        $this->fieldset->setMessages(null);
    }

    public function testOnlyElementsWithErrorsInMessages()
    {
        $fieldset = new TestAsset\FieldsetWithInputFilter('set');
        $fieldset->add(new Element('foo'));
        $fieldset->add(new Element('bar'));

        $form = new Form();
        $form->add($fieldset);
        $form->setInputFilter(new InputFilter());
        $form->setData(array());
        $form->isValid();

        $messages = $form->getMessages();
        $this->assertArrayHasKey('foo', $messages['set']);
        $this->assertArrayNotHasKey('bar', $messages['set']);
    }

    public function testCanRetrieveMessagesForSingleElementsAfterMessagesHaveBeenSet()
    {
        $this->populateFieldset();
        $messages = $this->getMessages();
        $this->fieldset->setMessages($messages);

        $test = $this->fieldset->getMessages('bar');
        $this->assertEquals($messages['bar'], $test);
    }

    public function testCanRetrieveMessagesForSingleFieldsetsAfterMessagesHaveBeenSet()
    {
        $this->populateFieldset();
        $messages = $this->getMessages();
        $this->fieldset->setMessages($messages);

        $test = $this->fieldset->getMessages('barbaz');
        $this->assertEquals($messages['barbaz'], $test);
    }

    public function testGetMessagesWithInvalidElementRaisesException()
    {
        $this->setExpectedException('Zend\Form\Exception\InvalidArgumentException');
        $this->fieldset->getMessages('foo');
    }

    public function testCountGivesCountOfAttachedElementsAndFieldsets()
    {
        $this->populateFieldset();
        $this->assertEquals(5, count($this->fieldset));
    }

    public function testCanIterateOverElementsAndFieldsetsInOrderAttached()
    {
        $this->populateFieldset();
        $expected = array('foo', 'bar', 'baz', 'foobar', 'barbaz');
        $test     = array();
        foreach ($this->fieldset as $element) {
            $test[] = $element->getName();
        }
        $this->assertEquals($expected, $test);
    }

    public function testIteratingRespectsOrderPriorityProvidedWhenAttaching()
    {
        $this->fieldset->add(new Element('foo'), array('priority' => 10));
        $this->fieldset->add(new Element('bar'), array('priority' => 20));
        $this->fieldset->add(new Element('baz'), array('priority' => -10));
        $this->fieldset->add(new Fieldset('barbaz'), array('priority' => 30));

        $expected = array('barbaz', 'bar', 'foo', 'baz');
        $test     = array();
        foreach ($this->fieldset as $element) {
            $test[] = $element->getName();
        }
        $this->assertEquals($expected, $test);
    }

    public function testIteratingRespectsOrderPriorityProvidedWhenSetLater()
    {
        $this->fieldset->add(new Element('foo'), array('priority' => 10));
        $this->fieldset->add(new Element('bar'), array('priority' => 20));
        $this->fieldset->add(new Element('baz'), array('priority' => -10));
        $this->fieldset->add(new Fieldset('barbaz'), array('priority' => 30));
        $this->fieldset->setPriority('baz', 99);

        $expected = array('baz', 'barbaz', 'bar', 'foo');
        $test     = array();
        foreach ($this->fieldset as $element) {
            $test[] = $element->getName();
        }
        $this->assertEquals($expected, $test);
    }

    public function testIteratingRespectsOrderPriorityWhenCloned()
    {
        $this->fieldset->add(new Element('foo'), array('priority' => 10));
        $this->fieldset->add(new Element('bar'), array('priority' => 20));
        $this->fieldset->add(new Element('baz'), array('priority' => -10));
        $this->fieldset->add(new Fieldset('barbaz'), array('priority' => 30));

        $expected = array('barbaz', 'bar', 'foo', 'baz');

        $testOrig  = array();
        $testClone = array();

        $fieldsetClone = clone $this->fieldset;

        foreach ($this->fieldset as $element) {
            $testOrig[] = $element->getName();
        }

        foreach ($fieldsetClone as $element) {
            $testClone[] = $element->getName();
        }

        $this->assertEquals($expected, $testClone);
        $this->assertEquals($testOrig, $testClone);
    }

    public function testCloneDeepClonesElementsAndObject()
    {
        $this->fieldset->add(new Element('foo'));
        $this->fieldset->add(new Element('bar'));
        $this->fieldset->setObject(new \stdClass);

        $fieldsetClone = clone $this->fieldset;

        $this->assertNotSame($this->fieldset->get('foo'), $fieldsetClone->get('foo'));
        $this->assertNotSame($this->fieldset->get('bar'), $fieldsetClone->get('bar'));
        $this->assertNotSame($this->fieldset->getObject(), $fieldsetClone->getObject());
    }

    public function testSubFieldsetsBindObject()
    {
        $form = new Form();
        $fieldset = new Fieldset('foobar');
        $form->add($fieldset);
        $value = new \ArrayObject(array(
            'foobar' => 'abc',
        ));
        $value['foobar'] = new \ArrayObject(array(
            'foo' => 'abc'
        ));
        $form->bind($value);
        $this->assertSame($fieldset, $form->get('foobar'));
    }

    public function testBindEmptyValue()
    {
        $value = new \ArrayObject(array(
            'foo' => 'abc',
            'bar' => 'def',
        ));

        $inputFilter = new InputFilter();
        $inputFilter->add(array('name' => 'foo', 'required' => false));
        $inputFilter->add(array('name' => 'bar', 'required' => false));

        $form = new Form();
        $form->add(new Element('foo'));
        $form->add(new Element('bar'));
        $form->setInputFilter($inputFilter);
        $form->bind($value);
        $form->setData(array(
            'foo' => '',
            'bar' => 'ghi',
        ));
        $form->isValid();

        $this->assertSame('', $value['foo']);
        $this->assertSame('ghi', $value['bar']);
    }

    public function testFieldsetExposesFluentInterface()
    {
        $fieldset = $this->fieldset->add(new Element('foo'));
        $this->assertSame($this->fieldset, $fieldset);
        $fieldset = $this->fieldset->remove('foo');
        $this->assertSame($this->fieldset, $fieldset);
    }

    public function testSetOptions()
    {
        $this->fieldset->setOptions(array(
                                   'foo' => 'bar'
                              ));
        $option = $this->fieldset->getOption('foo');

        $this->assertEquals('bar', $option);
    }

    public function testSetOptionsUseAsBaseFieldset()
    {
        $this->fieldset->setOptions(array(
                                   'use_as_base_fieldset' => 'bar'
                              ));
        $option = $this->fieldset->getOption('use_as_base_fieldset');

        $this->assertEquals('bar', $option);
    }

    public function testGetReturnsNull()
    {
        $foo = $this->fieldset->get('foo');
        $this->assertNull($foo);
    }

    public function testBindValuesHasNoName()
    {
        $bindValues = $this->fieldset->bindValues(array('foo'));
        $this->assertNull($bindValues);
    }

    public function testSetObjectWithStringRaisesException()
    {
        $this->setExpectedException('Zend\Form\Exception\InvalidArgumentException');
        $this->fieldset->setObject('foo');
    }
}
