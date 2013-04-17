<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Form
 */

namespace ZendTest\Form\Element;

use stdClass;
use ArrayObject;
use PHPUnit_Framework_TestCase as TestCase;
use Zend\Form\Element;
use Zend\Form\Element\Collection as Collection;
use Zend\Stdlib\Hydrator\ObjectProperty as ObjectPropertyHydrator;
use ZendTest\Form\TestAsset\Entity\Product;

class CollectionTest extends TestCase
{
    protected $form;
    protected $productFieldset;

    public function setUp()
    {
        $this->form = new \ZendTest\Form\TestAsset\FormCollection();
        $this->productFieldset = new \ZendTest\Form\TestAsset\ProductFieldset();

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

        $this->assertTrue($collection->allowAdd());
        $collection->setAllowAdd(false);
        $this->assertFalse($collection->allowAdd());

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
        $this->assertTrue($collection->allowAdd());

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

        $this->assertFalse($collection->shouldCreateTemplate());
        $collection->setShouldCreateTemplate(true);
        $this->assertTrue($collection->shouldCreateTemplate());

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

    public function testExtractFromObjectDoesntTouchOriginalObject()
    {
        $form = new \Zend\Form\Form();
        $form->setHydrator(new \Zend\Stdlib\Hydrator\ClassMethods());
        $this->productFieldset->setUseAsBaseFieldset(true);
        $form->add($this->productFieldset);

        $originalObjectHash = spl_object_hash($this->productFieldset->get("categories")->getTargetElement()->getObject());

        $product = new Product();
        $product->setName("foo");
        $product->setPrice(42);
        $cat1 = new \ZendTest\Form\TestAsset\Entity\Category();
        $cat1->setName("bar");
        $cat2 = new \ZendTest\Form\TestAsset\Entity\Category();
        $cat2->setName("bar2");

        $product->setCategories(array($cat1,$cat2));

        $form->bind($product);

        $form->setData(
            array("product"=>
                array(
                    "name" => "franz",
                    "price" => 13,
                    "categories" => array(
                        array("name" => "sepp"),
                        array("name" => "herbert")
                    )
                )
            )
        );

        $objectAfterExtractHash = spl_object_hash($this->productFieldset->get("categories")->getTargetElement()->getObject());

        $this->assertSame($originalObjectHash,$objectAfterExtractHash);
    }

    public function testDoesNotCreateNewObjects()
    {
        $form = new \Zend\Form\Form();
        $form->setHydrator(new \Zend\Stdlib\Hydrator\ClassMethods());
        $this->productFieldset->setUseAsBaseFieldset(true);
        $form->add($this->productFieldset);

        $product = new Product();
        $product->setName("foo");
        $product->setPrice(42);
        $cat1 = new \ZendTest\Form\TestAsset\Entity\Category();
        $cat1->setName("bar");
        $cat2 = new \ZendTest\Form\TestAsset\Entity\Category();
        $cat2->setName("bar2");

        $product->setCategories(array($cat1,$cat2));

        $form->bind($product);

        $form->setData(
            array("product"=>
                array(
                    "name" => "franz",
                    "price" => 13,
                    "categories" => array(
                        array("name" => "sepp"),
                        array("name" => "herbert")
                    )
                )
            )
        );
        $form->isValid();

        $categories = $product->getCategories();
        $this->assertSame($categories[0], $cat1);
        $this->assertSame($categories[1], $cat2);
    }

    public function testExtractDefaultIsEmptyArray()
    {
        $collection = $this->form->get('fieldsets');
        $this->assertEquals(array(), $collection->extract());
    }

    public function testExtractThroughTargetElementHydrator()
    {
        $collection = $this->form->get('fieldsets');
        $this->prepareForExtract($collection);

        $expected = array(
            'obj2' => array('field' => 'fieldOne'),
            'obj3' => array('field' => 'fieldTwo'),
        );

        $this->assertEquals($expected, $collection->extract());
    }

    public function testExtractMaintainsTargetElementObject()
    {
        $collection = $this->form->get('fieldsets');
        $this->prepareForExtract($collection);

        $expected = $collection->getTargetElement()->getObject();

        $collection->extract();

        $test = $collection->getTargetElement()->getObject();

        $this->assertSame($expected, $test);
    }

    public function testExtractThroughCustomHydrator()
    {
        $collection = $this->form->get('fieldsets');
        $this->prepareForExtract($collection);

        $mockHydrator = $this->getMock('Zend\Stdlib\Hydrator\HydratorInterface');
        $mockHydrator->expects($this->exactly(2))
                     ->method('extract')
                     ->will($this->returnCallback(function ($object) {
                         return $object->field . '_foo';
                     }));

        $collection->setHydrator($mockHydrator);

        $expected = array(
            'obj2' => 'fieldOne_foo',
            'obj3' => 'fieldTwo_foo',
        );

        $this->assertEquals($expected, $collection->extract());
    }

    public function testExtractFromTraversable()
    {
        $collection = $this->form->get('fieldsets');
        $this->prepareForExtract($collection);

        $traversable = new ArrayObject($collection->getObject());
        $collection->setObject($traversable);

        $expected = array(
            'obj2' => array('field' => 'fieldOne'),
            'obj3' => array('field' => 'fieldTwo'),
        );

        $this->assertEquals($expected, $collection->extract());
    }

    protected function prepareForExtract($collection)
    {
        $targetElement = $collection->getTargetElement();

        $obj1 = new stdClass();

        $targetElement->setHydrator(new ObjectPropertyHydrator())
                      ->setObject($obj1);

        $obj2 = new stdClass();
        $obj2->field = 'fieldOne';

        $obj3 = new stdClass();
        $obj3->field = 'fieldTwo';

        $collection->setObject(array(
            'obj2' => $obj2,
            'obj3' => $obj3,
        ));
    }
}
