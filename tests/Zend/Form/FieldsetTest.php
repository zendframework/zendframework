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

    public function testFieldsetIsEmptyByDefault()
    {
        $this->assertEquals(0, count($this->fieldset));
    }

    public function testCanAddElementsToFieldset()
    {
        $this->element->add(new Element('foo'));
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

    public function testElementRetainsNameEvenWhenProvidedWithAliasWhenAdded()
    {
        $element = new Element('foo');
        $this->fieldset->add($element, array('name' => 'bar'));
        $this->assertEquals('foo', $element->getName());
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
        $this->markTestIncomplete();
    }

    public function testCanRetrieveAllAttachedFieldsetsSeparateFromElementsAtOnce()
    {
        $this->markTestIncomplete();
    }

    public function testCanSetAndRetrieveErrorMessagesForAllElementsAndFieldsets()
    {
        $this->markTestIncomplete();
    }

    public function testCanRetrieveMessagesForSingleElementsAfterMessagesHaveBeenSet()
    {
        $this->markTestIncomplete();
    }

    public function testCanRetrieveMessagesForSingleFieldsetsAfterMessagesHaveBeenSet()
    {
        $this->markTestIncomplete();
    }

    public function testCountGivesCountOfAttachedElementsAndFieldsets()
    {
        $this->markTestIncomplete();
    }

    public function testCanIterateOverElementsAndFieldsetsInOrderAttached()
    {
        $this->markTestIncomplete();
    }

    /**
     * @todo Should this use priority queue, or the hack used in Zend_Form v1?
     */
    public function testIteratingRespectsOrderPriorityProvidedWhenAttaching()
    {
        $this->markTestIncomplete();
    }
}
