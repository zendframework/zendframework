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
use Zend\Form\ElementInterface;

/**
 * @category   Zend
 * @package    Zend_Form
 * @subpackage UnitTest
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
}
