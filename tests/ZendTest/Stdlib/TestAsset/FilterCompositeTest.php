<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link           http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright      Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license        http://framework.zend.com/license/new-bsd New BSD License
 * @package        Zend_Service
 */

namespace ZendTest\Stdlib;

use Zend\Stdlib\Hydrator\Filter\FilterComposite;

class FilterCompositeTest extends \PHPUnit_Framework_TestCase
{
    protected $filterComposite;

    public function setUp()
    {
        $this->filterComposite = new FilterComposite();
    }

    public function testValidationAdd()
    {
        $this->assertTrue($this->filterComposite->filter("foo"));
        $this->filterComposite->addFilter("has",
            function($property) {
                return false;
            }
        );
        $this->assertFalse($this->filterComposite->filter("foo"));
    }

    public function testValidationRemove()
    {
        $this->filterComposite->addFilter("has",
            function($property) {
                return false;
            }
        );
        $this->assertFalse($this->filterComposite->filter("foo"));
        $this->filterComposite->removeFilter("has");
        $this->assertTrue($this->filterComposite->filter("foo"));
    }

    public function testValidationHas()
    {
        $this->filterComposite->addFilter("has",
            function($property) {
                return false;
            }
        );
        $this->assertFalse($this->filterComposite->filter("foo"));
        $this->assertTrue($this->filterComposite->hasFilter("has"));
    }

    public function testComplexValidation()
    {
        $this->filterComposite->addFilter("has", new \Zend\Stdlib\Hydrator\Filter\HasFilter());
        $this->filterComposite->addFilter("get", new \Zend\Stdlib\Hydrator\Filter\GetFilter());
        $this->filterComposite->addFilter("is", new \Zend\Stdlib\Hydrator\Filter\IsFilter());

        $this->filterComposite->addFilter("exclude",
            function($property) {
                $method = substr($property, strpos($property, '::'));

                if ($method === 'getServiceLocator') {
                    return false;
                }

                return true;
            }, FilterComposite::CONDITION_AND
        );

        $this->assertTrue($this->filterComposite->filter('getFooBar'));
        $this->assertFalse($this->filterComposite->filter('getServiceLocator'));
    }

    public function testConstructorInjection()
    {
        $andCondition = array(
            'servicelocator' => function($property) {
                if($property === 'getServiceLocator') {
                    return false;
                }
                return true;
            },
            'foobar' => function($property) {
                if($property === 'getFooBar') {
                    return false;
                }
                return true;
            }
        );
        $orCondition = array(
            'has' => new \Zend\Stdlib\Hydrator\Filter\HasFilter(),
            'get' => new \Zend\Stdlib\Hydrator\Filter\GetFilter()
        );
        $filterComposite = new FilterComposite($orCondition, $andCondition);

        $this->assertFalse($filterComposite->filter('getFooBar'));
        $this->assertFalse($filterComposite->filter('geTFooBar'));
        $this->assertFalse($filterComposite->filter('getServiceLocator'));
        $this->assertTrue($filterComposite->filter('getFoo'));
        $this->assertTrue($filterComposite->filter('hasFoo'));
    }

    /**
     * @expectedException Zend\Stdlib\Exception\InvalidArgumentException
     * @expectedExceptionMessage The value of test should be either a callable
     * or an instance of Zend\Stdlib\Hydrator\Filter\FilterInterface
     */
    public function testInvalidParameterConstructorInjection()
    {
        $andCondition = array('foo' => 'bar');
        $orCondition = array('test' => 'blubb');

        new FilterComposite($orCondition, $andCondition);
    }

    /**
     * @expectedException Zend\Stdlib\Exception\InvalidArgumentException
     * @expectedExceptionMessage The value of foo should be either a callable
     * or an instance of Zend\Stdlib\Hydrator\Filter\FilterInterface
     */
    public function testInvalidFilterInjection()
    {
        $this->filterComposite->addFilter('foo', 'bar');
    }
}
