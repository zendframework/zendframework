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
use Zend\Filter;
use Zend\InputFilter\Factory;
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

    public function testResetEmptyValidationGroupRecursively()
    {
        $data = array(
            'flat' => 'foo',
            'deep' => array(
                'deep-input1' => 'deep-foo1',
                'deep-input2' => 'deep-foo2',
            )
        );
        $this->filter->add(array(
            'name' => 'flat'
        ));
        $deepInputFilter = new InputFilter;
        $deepInputFilter->add(array(
            'name' => 'deep-input1'
        ));
        $deepInputFilter->add(array(
            'name' => 'deep-input2'
        ));
        $this->filter->add($deepInputFilter, 'deep');
        $this->filter->setData($data);
        $this->filter->setValidationGroup(array('deep' => 'deep-input1'));
        // reset validation group
        $this->filter->setValidationGroup(InputFilter::VALIDATE_ALL);
        $this->assertEquals($data, $this->filter->getValues());
    }

    /**
     * @expectedException Zend\InputFilter\Exception\InvalidArgumentException
     */
    public function testSettingDeepValidationGroupToNonInputFilterThrowsException()
    {
        $this->filter->add(array(
            'name' => 'flat'
        ));
        $this->filter->setValidationGroup(array('flat' => 'foo'));
    }
}
