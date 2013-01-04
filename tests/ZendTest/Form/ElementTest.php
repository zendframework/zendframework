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

/**
 * @category   Zend
 * @package    Zend_Form
 * @subpackage UnitTest
 */
class ElementTest extends TestCase
{
    public function testAttributesAreEmptyByDefault()
    {
        $element = new Element();
        $this->assertEquals(array(), $element->getAttributes());
    }

    public function testCanAddAttributesSingly()
    {
        $element = new Element();
        $element->setAttribute('data-foo', 'bar');
        $this->assertEquals('bar', $element->getAttribute('data-foo'));
    }

    public function testCanAddManyAttributesAtOnce()
    {
        $element = new Element();
        $attributes = array(
            'type'     => 'text',
            'class'    => 'text-element',
            'data-foo' => 'bar',
        );
        $element->setAttributes($attributes);
        $this->assertEquals($attributes, $element->getAttributes());
    }

    public function testAddingAttributesMerges()
    {
        $element = new Element();
        $attributes = array(
            'type'     => 'text',
            'class'    => 'text-element',
            'data-foo' => 'bar',
        );
        $attributesExtra = array(
            'data-foo' => 'baz',
            'width'    => 20,
        );
        $element->setAttributes($attributes);
        $element->setAttributes($attributesExtra);
        $expected = array_merge($attributes, $attributesExtra);
        $this->assertEquals($expected, $element->getAttributes());
    }

    public function testCanClearAllAttributes()
    {
        $element = new Element();
        $attributes = array(
            'type'     => 'text',
            'class'    => 'text-element',
            'data-foo' => 'bar',
        );
        $element->setAttributes($attributes);
        $element->clearAttributes();
        $this->assertEquals(array(), $element->getAttributes());
    }

    public function testCanRemoveSingleAttribute()
    {
        $element = new Element();
        $attributes = array(
            'type'     => 'text',
            'class'    => 'text-element',
            'data-foo' => 'bar',
        );
        $element->setAttributes($attributes);
        $element->removeAttribute('type');
        $this->assertFalse($element->hasAttribute('type'));
    }

    public function testCanRemoveMultipleAttributes()
    {
        $element = new Element();
        $attributes = array(
            'type'     => 'text',
            'class'    => 'text-element',
            'data-foo' => 'bar',
        );
        $element->setAttributes($attributes);
        $element->removeAttributes(array('type', 'class'));
        $this->assertFalse($element->hasAttribute('type'));
        $this->assertFalse($element->hasAttribute('class'));
    }

    public function testSettingNameSetsNameAttribute()
    {
        $element = new Element();
        $element->setName('foo');
        $this->assertEquals('foo', $element->getAttribute('name'));
    }

    public function testSettingNameAttributeAllowsRetrievingName()
    {
        $element = new Element();
        $element->setAttribute('name', 'foo');
        $this->assertEquals('foo', $element->getName());
    }

    public function testCanPassNameToConstructor()
    {
        $element = new Element('foo');
        $this->assertEquals('foo', $element->getName());
    }

    public function testCanSetCustomOptionFromConstructor()
    {
        $element = new Element('foo', array(
            'custom' => 'option'
        ));
        $options = $element->getOptions();
        $this->assertArrayHasKey('custom', $options);
        $this->assertEquals('option', $options['custom']);
    }

    public function testCanSetCustomOptionFromMethod()
    {
        $element = new Element('foo');
        $element->setOptions(array(
            'custom' => 'option'
        ));

        $options = $element->getOptions();
        $this->assertArrayHasKey('custom', $options);
        $this->assertEquals('option', $options['custom']);
    }

    public function testCanRetrieveSpecificOption()
    {
        $element = new Element('foo');
        $element->setOptions(array(
            'custom' => 'option'
        ));
        $option = $element->getOption('custom');
        $this->assertEquals('option', $option);
    }

    public function testSpecificOptionsSetLabelAttributes()
    {
        $element = new Element('foo');
        $element->setOptions(array(
                                  'label' => 'foo',
                                  'label_attributes' => array('bar' => 'baz')
                             ));
        $option = $element->getOption('label_attributes');
        $this->assertEquals(array('bar' => 'baz'), $option);
    }

    public function testSetOptionsWrongInputRaisesException()
    {
        $element = new Element('foo');

        $this->setExpectedException('Zend\Form\Exception\InvalidArgumentException');
        $element->setOptions(null);
    }

    public function testSetOptionsIsTraversable()
    {
        $element = new Element('foo');
        $element->setOptions(new \ArrayObject(array('foo' => 'bar')));
        $this->assertEquals('foo', $element->getName());
        $this->assertEquals(array('foo' => 'bar'), $element->getOptions());
    }

    public function testGetOption()
    {
        $element = new Element('foo');
        $this->assertNull($element->getOption('foo'));
    }

    public function testSetAttributesWrongInputRaisesException()
    {
        $element = new Element('foo');

        $this->setExpectedException('Zend\Form\Exception\InvalidArgumentException');
        $element->setAttributes(null);
    }

    public function testSetMessagesWrongInputRaisesException()
    {
        $element = new Element('foo');

        $this->setExpectedException('Zend\Form\Exception\InvalidArgumentException');
        $element->setMessages(null);
    }
}
