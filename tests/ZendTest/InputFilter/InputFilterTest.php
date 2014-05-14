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
use Zend\Filter;
use Zend\InputFilter\CollectionInputFilter;
use Zend\InputFilter\Factory;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;

class InputFilterTest extends TestCase
{
    public function setUp()
    {
        $this->filter = new InputFilter();
    }

    public function testLazilyComposesAFactoryByDefault()
    {
        $factory = $this->filter->getFactory();
        $this->assertInstanceOf('Zend\InputFilter\Factory', $factory);
    }

    public function testCanComposeAFactory()
    {
        $factory = new Factory();
        $this->filter->setFactory($factory);
        $this->assertSame($factory, $this->filter->getFactory());
    }

    public function testCanAddUsingSpecification()
    {
        $this->filter->add(array(
            'name' => 'foo',
        ));
        $this->assertTrue($this->filter->has('foo'));
        $foo = $this->filter->get('foo');
        $this->assertInstanceOf('Zend\InputFilter\InputInterface', $foo);
    }

    /**
     * @covers \Zend\InputFilter\BaseInputFilter::getValue
     *
     * @group 6028
     */
    public function testGetValueReturnsArrayIfNestedInputFilters()
    {
        $inputFilter = new InputFilter();
        $inputFilter->add(new Input(), 'name');

        $this->filter->add($inputFilter, 'people');

        $data = array(
            'people' => array(
                 'name' => 'Wanderson'
            )
        );

        $this->filter->setData($data);
        $this->assertTrue($this->filter->isValid());

        $this->assertInternalType('array', $this->filter->getValue('people'));
    }

    /**
     * @group ZF2-5648
     */
    public function testCountZeroValidateInternalInputWithCollectionInputFilter()
    {
        $inputFilter = new InputFilter();
        $inputFilter->add(new Input(), 'name');

        $collection = new CollectionInputFilter();
        $collection->setInputFilter($inputFilter);
        $collection->setCount(0);

        $this->filter->add($collection, 'people');

        $data = array(
            'people' => array(
                array(
                    'name' => 'Wanderson',
                ),
            ),
        );
        $this->filter->setData($data);

        $this->assertTrue($this->filter->isvalid());
        $this->assertSame($data, $this->filter->getValues());
    }
}
