<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Form
 */

namespace ZendTest\Form\Element;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Form\Element;
use Zend\Form\Element\Collection as Collection;

class CollectionTest extends TestCase
{
    protected $form;

    public function setUp()
    {
        $this->form = new \ZendTest\Form\TestAsset\FormCollection();

        parent::setUp();
    }

    public function testCanRetrieveDefaultPlaceholder()
    {
        $placeholder = $this->form->get('colors')->getTemplatePlaceholder();
        $this->assertEquals('__index__', $placeholder);
    }

    public function testCannotAllowNewElementsIfAllowAddIsFalse()
    {
        $collection = $this->form->get('colors');
        $collection->setAllowAdd(false);

        // By default, $collection contains 2 elements
        $data = array();
        $data[] = 'blue';
        $data[] = 'green';

        $collection->populateValues($data);
        $this->assertEquals(2, count($collection->getElements()));

        $this->setExpectedException('Zend\Form\Exception\DomainException');
        $data[] = 'orange';
        $collection->populateValues($data);
    }

    public function testCanAddNewElementsIfAllowAddIsTrue()
    {
        $collection = $this->form->get('colors');
        $collection->setAllowAdd(true);

        // By default, $collection contains 2 elements
        $data = array();
        $data[] = 'blue';
        $data[] = 'green';

        $collection->populateValues($data);
        $this->assertEquals(2, count($collection->getElements()));

        $data[] = 'orange';
        $collection->populateValues($data);
        $this->assertEquals(3, count($collection->getElements()));
    }

    public function testCanValidateFormWithCollectionWithoutTemplate()
    {
        $this->form->setData(array(
            'colors' => array(
                '#ffffff',
                '#ffffff'
            ),
            'fieldsets' => array(
                array(
                    'field' => 'oneValue',
                    'nested_fieldset' => array(
                        'anotherField' => 'anotherValue'
                    )
                ),
                array(
                    'field' => 'twoValue',
                    'nested_fieldset' => array(
                        'anotherField' => 'anotherValue'
                    )
                )
            )
        ));

        $this->assertEquals(true, $this->form->isValid());
    }

    public function testCanValidateFormWithCollectionWithTemplate()
    {
        $collection = $this->form->get('colors');
        $collection->setShouldCreateTemplate(true);
        $collection->setTemplatePlaceholder('__template__');

        $this->form->setData(array(
            'colors' => array(
                '#ffffff',
                '#ffffff'
            ),
            'fieldsets' => array(
                array(
                    'field' => 'oneValue',
                    'nested_fieldset' => array(
                        'anotherField' => 'anotherValue'
                    )
                ),
                array(
                    'field' => 'twoValue',
                    'nested_fieldset' => array(
                        'anotherField' => 'anotherValue'
                    )
                )
            )
        ));

        $this->assertEquals(true, $this->form->isValid());
    }

    public function testThrowExceptionIfThereAreLessElementsAndAllowRemoveNotAllowed()
    {
        $this->setExpectedException('Zend\Form\Exception\DomainException');

        $collection = $this->form->get('colors');
        $collection->setAllowRemove(false);

        $this->form->setData(array(
            'colors' => array(
                '#ffffff'
            ),
            'fieldsets' => array(
                array(
                    'field' => 'oneValue',
                    'nested_fieldset' => array(
                        'anotherField' => 'anotherValue'
                    )
                ),
                array(
                    'field' => 'twoValue',
                    'nested_fieldset' => array(
                        'anotherField' => 'anotherValue'
                    )
                )
            )
        ));

        $this->form->isValid();
    }

    public function testCanValidateLessThanSpecifiedCount()
    {
        $collection = $this->form->get('colors');
        $collection->setAllowRemove(true);

        $this->form->setData(array(
            'colors' => array(
                '#ffffff'
            ),
            'fieldsets' => array(
                array(
                    'field' => 'oneValue',
                    'nested_fieldset' => array(
                        'anotherField' => 'anotherValue'
                    )
                ),
                array(
                    'field' => 'twoValue',
                    'nested_fieldset' => array(
                        'anotherField' => 'anotherValue'
                    )
                )
            )
        ));

        $this->assertEquals(true, $this->form->isValid());
    }

    public function testSetOptions()
    {
        $collection = $this->form->get('colors');
        $element = new Element('foo');
        $collection->setOptions(array(
                                  'target_element' => $element,
                                  'count' => 2,
                                  'allow_add' => true,
                                  'allow_remove' => false,
                                  'should_create_template' => true,
                                  'template_placeholder' => 'foo',
                             ));
        $this->assertInstanceOf('Zend\Form\Element', $collection->getOption('target_element'));
        $this->assertEquals(2, $collection->getOption('count'));
        $this->assertEquals(true, $collection->getOption('allow_add'));
        $this->assertEquals(false, $collection->getOption('allow_remove'));
        $this->assertEquals(true, $collection->getOption('should_create_template'));
        $this->assertEquals('foo', $collection->getOption('template_placeholder'));
    }

    public function testSetObjectNullRaisesException()
    {
        $collection = $this->form->get('colors');
        $this->setExpectedException('Zend\Form\Exception\InvalidArgumentException');
        $collection->setObject(null);
    }

    public function testPopulateValuesNullRaisesException()
    {
        $collection = $this->form->get('colors');
        $this->setExpectedException('Zend\Form\Exception\InvalidArgumentException');
        $collection->populateValues(null);
    }

    public function testSetTargetElementNullRaisesException()
    {
        $collection = $this->form->get('colors');
        $this->setExpectedException('Zend\Form\Exception\InvalidArgumentException');
        $collection->setTargetElement(null);
    }

    public function testGetTargetElement()
    {
        $collection = $this->form->get('colors');
        $element = new Element('foo');
        $collection->setTargetElement($element);

        $this->assertInstanceOf('Zend\Form\Element', $collection->getTargetElement());
    }


}
