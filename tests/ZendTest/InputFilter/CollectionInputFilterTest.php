<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\InputFilter;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\InputFilter\BaseInputFilter;
use Zend\InputFilter\CollectionInputFilter;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Validator;

class CollectionInputFilterTest extends TestCase
{
    /**
     * @var \Zend\InputFilter\CollectionInputFilter
     */
    protected $filter;

    public function setUp()
    {
        $this->filter = new CollectionInputFilter();
    }

    public function getBaseInputFilter()
    {
        $filter = new BaseInputFilter();

        $foo = new Input();
        $foo->getFilterChain()->attachByName('stringtrim')
                              ->attachByName('alpha');
        $foo->getValidatorChain()->attach(new Validator\StringLength(3, 6));

        $bar = new Input();
        $bar->getFilterChain()->attachByName('stringtrim');
        $bar->getValidatorChain()->attach(new Validator\Digits());

        $baz = new Input();
        $baz->setRequired(false);
        $baz->getFilterChain()->attachByName('stringtrim');
        $baz->getValidatorChain()->attach(new Validator\StringLength(1, 6));

        $filter->add($foo, 'foo')
               ->add($bar, 'bar')
               ->add($baz, 'baz')
               ->add($this->getChildInputFilter(), 'nest');

        return $filter;
    }

    public function getChildInputFilter()
    {
        $filter = new BaseInputFilter();

        $foo = new Input();
        $foo->getFilterChain()->attachByName('stringtrim')
                              ->attachByName('alpha');
        $foo->getValidatorChain()->attach(new Validator\StringLength(3, 6));

        $bar = new Input();
        $bar->getFilterChain()->attachByName('stringtrim');
        $bar->getValidatorChain()->attach(new Validator\Digits());

        $baz = new Input();
        $baz->setRequired(false);
        $baz->getFilterChain()->attachByName('stringtrim');
        $baz->getValidatorChain()->attach(new Validator\StringLength(1, 6));

        $filter->add($foo, 'foo')
               ->add($bar, 'bar')
               ->add($baz, 'baz');
        return $filter;
    }

    public function getValidCollectionData()
    {
        return array(
            array(
                'foo' => ' bazbat ',
                'bar' => '12345',
                'baz' => '',
                'nest' => array(
                    'foo' => ' bazbat ',
                    'bar' => '12345',
                    'baz' => '',
                ),
            ),
            array(
                'foo' => ' batbaz ',
                'bar' => '54321',
                'baz' => '',
                'nest' => array(
                    'foo' => ' batbaz ',
                    'bar' => '54321',
                    'baz' => '',
                ),
            )
        );
    }

    public function testSetInputFilter()
    {
        $this->filter->setInputFilter(new BaseInputFilter());
        $this->assertInstanceOf('Zend\InputFilter\BaseInputFilter', $this->filter->getInputFilter());
    }

    public function testGetDefaultInputFilter()
    {
        $this->assertInstanceOf('Zend\InputFilter\BaseInputFilter', $this->filter->getInputFilter());
    }

    public function testSetCount()
    {
        $this->filter->setCount(5);
        $this->assertEquals(5, $this->filter->getCount());
    }

    public function testSetCountBelowZero()
    {
        $this->filter->setCount(-1);
        $this->assertEquals(0, $this->filter->getCount());
    }

    public function testGetCountUsesCountOfCollectionDataWhenNotSet()
    {
        $collectionData = array(
            array('foo' => 'bar'),
            array('foo' => 'baz')
        );

        $this->filter->setData($collectionData);
        $this->assertEquals(2, $this->filter->getCount());
    }

    public function testGetCountUsesSpecifiedCount()
    {
        $collectionData = array(
            array('foo' => 'bar'),
            array('foo' => 'baz')
        );

        $this->filter->setCount(3);
        $this->filter->setData($collectionData);
        $this->assertEquals(3, $this->filter->getCount());
    }

    /**
     * @group 6160
     */
    public function testGetCountReturnsRightCountOnConsecutiveCallsWithDifferentData()
    {
        $collectionData1 = array(
            array('foo' => 'bar'),
            array('foo' => 'baz')
        );

        $collectionData2 = array(
            array('foo' => 'bar')
        );

        $this->filter->setData($collectionData1);
        $this->assertEquals(2, $this->filter->getCount());
        $this->filter->setData($collectionData2);
        $this->assertEquals(1, $this->filter->getCount());
    }

    public function testCanValidateValidData()
    {
        if (!extension_loaded('intl')) {
            $this->markTestSkipped('ext/intl not enabled');
        }

        $this->filter->setInputFilter($this->getBaseInputFilter());
        $this->filter->setData($this->getValidCollectionData());
        $this->assertTrue($this->filter->isValid());
    }

    public function testCanValidateValidDataWithNonConsecutiveKeys()
    {
        if (!extension_loaded('intl')) {
            $this->markTestSkipped('ext/intl not enabled');
        }

        $collectionData = $this->getValidCollectionData();
        $collectionData[2] = $collectionData[0];
        unset($collectionData[0]);
        $this->filter->setInputFilter($this->getBaseInputFilter());
        $this->filter->setData($collectionData);
        $this->assertTrue($this->filter->isValid());
    }

    public function testInvalidDataReturnsFalse()
    {
        if (!extension_loaded('intl')) {
            $this->markTestSkipped('ext/intl not enabled');
        }

        $invalidCollectionData = array(
            array(
                'foo' => ' bazbatlong ',
                'bar' => '12345',
                'baz' => '',
            ),
            array(
                'foo' => ' bazbat ',
                'bar' => '12345',
                'baz' => '',
            )
        );

        $this->filter->setInputFilter($this->getBaseInputFilter());
        $this->filter->setData($invalidCollectionData);
        $this->assertFalse($this->filter->isValid());
    }

    public function testDataLessThanCountIsInvalid()
    {
        if (!extension_loaded('intl')) {
            $this->markTestSkipped('ext/intl not enabled');
        }

        $invalidCollectionData = array(
            array(
                'foo' => ' bazbat ',
                'bar' => '12345',
                'baz' => '',
                'nest' => array(
                    'foo' => ' bazbat ',
                    'bar' => '12345',
                    'baz' => '',
                ),
            ),
        );

        $this->filter->setCount(2);
        $this->filter->setInputFilter($this->getBaseInputFilter());
        $this->filter->setData($invalidCollectionData);
        $this->assertFalse($this->filter->isValid());
    }

    public function testGetValues()
    {
        if (!extension_loaded('intl')) {
            $this->markTestSkipped('ext/intl not enabled');
        }

        $expectedData = array(
            array(
                'foo' => 'bazbat',
                'bar' => '12345',
                'baz' => '',
                'nest' => array(
                    'foo' => 'bazbat',
                    'bar' => '12345',
                    'baz' => '',
                ),
            ),
            array(
                'foo' => 'batbaz',
                'bar' => '54321',
                'baz' => '',
                'nest' => array(
                    'foo' => 'batbaz',
                    'bar' => '54321',
                    'baz' => '',
                ),
            )
        );

        $this->filter->setInputFilter($this->getBaseInputFilter());
        $this->filter->setData($this->getValidCollectionData());

        $this->assertTrue($this->filter->isValid());
        $this->assertEquals($expectedData, $this->filter->getValues());

        $this->assertCount(2, $this->filter->getValidInput());
        foreach ($this->filter->getValidInput() as $validInputs) {
            $this->assertCount(4, $validInputs);
        }
    }

    public function testGetRawValues()
    {
        if (!extension_loaded('intl')) {
            $this->markTestSkipped('ext/intl not enabled');
        }

        $expectedData = array(
            array(
                'foo' => ' bazbat ',
                'bar' => '12345',
                'baz' => '',
                'nest' => array(
                    'foo' => ' bazbat ',
                    'bar' => '12345',
                    'baz' => '',
                ),
            ),
            array(
                'foo' => ' batbaz ',
                'bar' => '54321',
                'baz' => '',
                'nest' => array(
                    'foo' => ' batbaz ',
                    'bar' => '54321',
                    'baz' => '',
                ),
            )
        );

        $this->filter->setInputFilter($this->getBaseInputFilter());
        $this->filter->setData($this->getValidCollectionData());

        $this->assertTrue($this->filter->isValid());
        $this->assertEquals($expectedData, $this->filter->getRawValues());
    }

    public function testGetMessagesForInvalidInputs()
    {
        if (!extension_loaded('intl')) {
            $this->markTestSkipped('ext/intl not enabled');
        }

        $invalidCollectionData = array(
            array(
                'foo' => ' bazbattoolong ',
                'bar' => '12345',
                'baz' => '',
                'nest' => array(
                    'foo' => ' bazbat ',
                    'bar' => '12345',
                    'baz' => '',
                ),
            ),
            array(
                'foo' => ' bazbat ',
                'bar' => 'notstring',
                'baz' => '',
                'nest' => array(
                    'foo' => ' bazbat ',
                    'bar' => '12345',
                    'baz' => '',
                ),
            ),
            array(
                'foo' => ' bazbat ',
                'bar' => '12345',
                'baz' => '',
                'nest' => array(
                    // missing 'foo' here
                    'bar' => '12345',
                    'baz' => '',
                ),
            ),
        );

        $this->filter->setInputFilter($this->getBaseInputFilter());
        $this->filter->setData($invalidCollectionData);

        $this->assertFalse($this->filter->isValid());

        $this->assertCount(3, $this->filter->getInvalidInput());
        foreach ($this->filter->getInvalidInput() as $invalidInputs) {
            $this->assertCount(1, $invalidInputs);
        }

        $messages = $this->filter->getMessages();

        $this->assertCount(3, $messages);
        $this->assertArrayHasKey('foo', $messages[0]);
        $this->assertArrayHasKey('bar', $messages[1]);
        $this->assertArrayHasKey('nest', $messages[2]);

        $this->assertCount(1, $messages[0]['foo']);
        $this->assertCount(1, $messages[1]['bar']);
        $this->assertCount(1, $messages[2]['nest']);
    }

    public function testSetValidationGroupUsingFormStyle()
    {
        if (!extension_loaded('intl')) {
            $this->markTestSkipped('ext/intl not enabled');
        }

        // forms set an array of identical validation groups for each set of data
        $formValidationGroup = array(
            array(
                'foo',
                'bar',
            ),
            array(
                'foo',
                'bar',
            ),
            array(
                'foo',
                'bar',
            )
        );

        $data = array(
            array(
                'foo' => ' bazbat ',
                'bar' => '12345'
            ),
            array(
                'foo' => ' batbaz ',
                'bar' => '54321'
            ),
            array(
                'foo' => ' batbaz ',
                'bar' => '54321'
            )
        );

        $this->filter->setInputFilter($this->getBaseInputFilter());
        $this->filter->setData($data);
        $this->filter->setValidationGroup($formValidationGroup);

        $this->assertTrue($this->filter->isValid());
    }

    public function testEmptyCollectionIsValidByDefault()
    {
        if (!extension_loaded('intl')) {
            $this->markTestSkipped('ext/intl not enabled');
        }

        $data = array();

        $this->filter->setInputFilter($this->getBaseInputFilter());
        $this->filter->setData($data);

        $this->assertTrue($this->filter->isValid());
    }

    public function testEmptyCollectionIsNotValidIfRequired()
    {
        if (!extension_loaded('intl')) {
            $this->markTestSkipped('ext/intl not enabled');
        }

        $data = array();

        $this->filter->setInputFilter($this->getBaseInputFilter());
        $this->filter->setData($data);
        $this->filter->setIsRequired(true);

        $this->assertFalse($this->filter->isValid());
    }

    public function testSetRequired()
    {
        $this->filter->setIsRequired(true);
        $this->assertEquals(true, $this->filter->getIsRequired());
    }

    public function testNonRequiredFieldsAreValidated()
    {
        $invalidCollectionData = array(
            array(
                'foo' => ' bazbattoolong ',
                'bar' => '12345',
                'baz' => 'baztoolong',
                'nest' => array(
                    'foo' => ' bazbat ',
                    'bar' => '12345',
                    'baz' => '',
                ),
            )
        );

        $this->filter->setInputFilter($this->getBaseInputFilter());
        $this->filter->setData($invalidCollectionData);

        $this->assertFalse($this->filter->isValid());
        $this->assertCount(2, current($this->filter->getInvalidInput()));
        $this->assertArrayHasKey('baz', current($this->filter->getMessages()));
    }

    public function testNestedCollectionWithEmptyChild()
    {
        $items_inputfilter = new BaseInputFilter();
        $items_inputfilter->add(new Input(), 'id')
                          ->add(new Input(), 'type');
        $items = new CollectionInputFilter();
        $items->setInputFilter($items_inputfilter);

        $groups_inputfilter = new BaseInputFilter();
        $groups_inputfilter->add(new Input(), 'group_class')
                           ->add($items, 'items');
        $groups = new CollectionInputFilter();
        $groups->setInputFilter($groups_inputfilter);

        $inputFilter = new BaseInputFilter();
        $inputFilter->add($groups, 'groups');

        $preFilterdata = array(
            'groups' => array(
                array(
                    'group_class' => 'bar',
                    'items' => array(
                        array(
                            'id' => 100,
                            'type' => 'item-1',
                        ),
                    ),
                ),
                array(
                    'group_class' => 'bar',
                    'items' => array(
                        array(
                            'id' => 200,
                            'type' => 'item-2',
                        ),
                        array(
                            'id' => 300,
                            'type' => 'item-3',
                        ),
                        array(
                            'id' => 400,
                            'type' => 'item-4',
                        ),
                    ),
                ),
                array(
                    'group_class' => 'biz',
                ),
            ),
        );

        $postFilterdata = array(
            'groups' => array(
                array(
                    'group_class' => 'bar',
                    'items' => array(
                        array(
                            'id' => 100,
                            'type' => 'item-1',
                        ),
                    ),
                ),
                array(
                    'group_class' => 'bar',
                    'items' => array(
                        array(
                            'id' => 200,
                            'type' => 'item-2',
                        ),
                        array(
                            'id' => 300,
                            'type' => 'item-3',
                        ),
                        array(
                            'id' => 400,
                            'type' => 'item-4',
                        ),
                    ),
                ),
                array(
                    'group_class' => 'biz',
                    'items' => array(),
                ),
            ),
        );

        $inputFilter->setData($preFilterdata);
        $inputFilter->isValid();
        $values = $inputFilter->getValues();
        $this->assertEquals($postFilterdata, $values);
    }

    public function testNestedCollectionWithEmptyData()
    {
        $items_inputfilter = new BaseInputFilter();
        $items_inputfilter->add(new Input(), 'id')
                          ->add(new Input(), 'type');
        $items = new CollectionInputFilter();
        $items->setInputFilter($items_inputfilter);

        $groups_inputfilter = new BaseInputFilter();
        $groups_inputfilter->add(new Input(), 'group_class')
                           ->add($items, 'items');
        $groups = new CollectionInputFilter();
        $groups->setInputFilter($groups_inputfilter);

        $inputFilter = new BaseInputFilter();
        $inputFilter->add($groups, 'groups');

        $data = array(
            'groups' => array(
                array(
                    'group_class' => 'bar',
                    'items' => array(
                        array(
                            'id' => 100,
                            'type' => 'item-1',
                        ),
                    ),
                ),
                array(
                    'group_class' => 'biz',
                    'items' => array(),
                ),
                array(
                    'group_class' => 'bar',
                    'items' => array(
                        array(
                            'id' => 200,
                            'type' => 'item-2',
                        ),
                        array(
                            'id' => 300,
                            'type' => 'item-3',
                        ),
                        array(
                            'id' => 400,
                            'type' => 'item-4',
                        ),
                    ),
                ),
            ),
        );

        $inputFilter->setData($data);
        $inputFilter->isValid();
        $values = $inputFilter->getValues();
        $this->assertEquals($data, $values);
    }

    /**
     * @group 6472
     */
    public function testNestedCollectionWhereChildDataIsNotOverwritten()
    {
        $items_inputfilter = new BaseInputFilter();
        $items_inputfilter->add(new Input(), 'id')
                          ->add(new Input(), 'type');
        $items = new CollectionInputFilter();
        $items->setInputFilter($items_inputfilter);

        $groups_inputfilter = new BaseInputFilter();
        $groups_inputfilter->add(new Input(), 'group_class')
                           ->add($items, 'items');
        $groups = new CollectionInputFilter();
        $groups->setInputFilter($groups_inputfilter);

        $inputFilter = new BaseInputFilter();
        $inputFilter->add($groups, 'groups');

        $data = array(
            'groups' => array(
                array(
                    'group_class' => 'bar',
                    'items' => array(
                        array(
                            'id' => 100,
                            'type' => 'item-100',
                        ),
                        array(
                            'id' => 101,
                            'type' => 'item-101',
                        ),
                        array(
                            'id' => 102,
                            'type' => 'item-102',
                        ),
                        array(
                            'id' => 103,
                            'type' => 'item-103',
                        ),
                    ),
                ),
                array(
                    'group_class' => 'foo',
                    'items' => array(
                        array(
                            'id' => 200,
                            'type' => 'item-200',
                        ),
                        array(
                            'id' => 201,
                            'type' => 'item-201',
                        ),
                    ),
                ),
            ),
        );

        $inputFilter->setData($data);
        $inputFilter->isValid();
        $values = $inputFilter->getValues();
        $this->assertEquals($data, $values);
    }

    public function dataNestingCollection()
    {
        return array(
            'count not specified' => array(
                'count' => null,
                'isValid' => true
            ),
            'count = 1' =>  array(
                'count' => 1,
                'isValid' => true
            ),
            'count = 2' => array(
                'count' => 2,
                'isValid' => false
            ),
            'count = 3' => array(
                'count' => 3,
                'isValid' => false
            )
        );
    }

    /**
     * @dataProvider dataNestingCollection
     */
    public function testNestingCollectionCountCached($count, $expectedIsValid)
    {
        $firstInputFilter = new InputFilter();

        $firstCollection = new CollectionInputFilter();
        $firstCollection->setInputFilter($firstInputFilter);

        $someInput = new Input('input');
        $secondInputFilter = new InputFilter();
        $secondInputFilter->add($someInput, 'input');

        $secondCollection = new CollectionInputFilter();
        $secondCollection->setInputFilter($secondInputFilter);
        if (!is_null($count)) {
            $secondCollection->setCount($count);
        }

        $firstInputFilter->add($secondCollection, 'second_collection');

        $mainInputFilter = new InputFilter();
        $mainInputFilter->add($firstCollection, 'first_collection');

        $data = array(
            'first_collection' => array(
                array(
                    'second_collection' => array(
                        array(
                            'input' => 'some value'
                        ),
                        array(
                            'input' => 'some value'
                        )
                    )
                ),
                array(
                    'second_collection' => array(
                        array(
                            'input' => 'some value'
                        ),
                    )
                )
            )
        );

        $mainInputFilter->setData($data);
        $this->assertSame($expectedIsValid, $mainInputFilter->isValid());
    }
}
