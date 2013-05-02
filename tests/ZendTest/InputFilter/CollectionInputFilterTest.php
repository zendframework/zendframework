<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\InputFilter;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\InputFilter\BaseInputFilter;
use Zend\InputFilter\CollectionInputFilter;
use Zend\InputFilter\Input;
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

    public function testInputFilterInputsAppliedToCollection()
    {
        $this->filter->setInputFilter($this->getBaseInputFilter());

        $this->assertCount(4, $this->filter->getInputs());
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

    public function testCanValidateValidData()
    {
        $this->filter->setInputFilter($this->getBaseInputFilter());
        $this->filter->setData($this->getValidCollectionData());
        $this->assertTrue($this->filter->isValid());
    }

    public function testInvalidDataReturnsFalse()
    {
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
        );

        $this->filter->setInputFilter($this->getBaseInputFilter());
        $this->filter->setData($invalidCollectionData);

        $this->assertFalse($this->filter->isValid());

        $this->assertCount(2, $this->filter->getInvalidInput());
        foreach ($this->filter->getInvalidInput() as $invalidInputs) {
            $this->assertCount(1, $invalidInputs);
        }

        $messages = $this->filter->getMessages();

        $this->assertCount(2, $messages);
        $this->assertArrayHasKey('foo', $messages[0]);
        $this->assertArrayHasKey('bar', $messages[1]);
    }

    public function testSetValidationGroupUsingFormStyle()
    {
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
        $data = array();

        $this->filter->setInputFilter($this->getBaseInputFilter());
        $this->filter->setData($data);

        $this->assertTrue($this->filter->isValid());
    }

    public function testEmptyCollectionIsNotValidIfRequired()
    {
        $data = array();

        $this->filter->setInputFilter($this->getBaseInputFilter());
        $this->filter->setData($data);
        $this->filter->setIsRequired(true);

        $this->assertFalse($this->filter->isValid());
    }

    public function testSetRequired()
    {
        $this->filter->setIsRequired(true);
        $this->assertEquals(true,$this->filter->getIsRequired());
    }
}
