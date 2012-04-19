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
use Zend\Form\Element;
use Zend\Form\Fieldset;

/**
 * @category   Zend
 * @package    Zend_Form
 * @subpackage UnitTest
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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

    public function testFieldsetIsEmptyByDefault()
    {
        $this->assertEquals(0, count($this->fieldset));
    }

    public function testCanAddElementsToFieldset()
    {
        $this->fieldset->add(new Element('foo'));
        $this->assertEquals(1, count($this->fieldset));
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
}
