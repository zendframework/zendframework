<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Filter
 */

namespace ZendTest\Filter;

use Zend\Filter\FilterChain;
use Zend\Filter\AbstractFilter;

/**
 * @category   Zend
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @group      Zend_Filter
 */
class FilterChainTest extends \PHPUnit_Framework_TestCase
{
    public function testEmptyFilterChainReturnsOriginalValue()
    {
        $chain = new FilterChain();
        $value = 'something';
        $this->assertEquals($value, $chain->filter($value));
    }

    public function testFiltersAreExecutedInFifoOrder()
    {
        $chain = new FilterChain();
        $chain->attach(new LowerCase())
              ->attach(new StripUpperCase());
        $value = 'AbC';
        $valueExpected = 'abc';
        $this->assertEquals($valueExpected, $chain->filter($value));
    }

    public function testFiltersAreExecutedAccordingToPriority()
    {
        $chain = new FilterChain();
        $chain->attach(new StripUpperCase())
              ->attach(new LowerCase, 100);
        $value = 'AbC';
        $valueExpected = 'b';
        $this->assertEquals($valueExpected, $chain->filter($value));
    }

    public function testAllowsConnectingArbitraryCallbacks()
    {
        $chain = new FilterChain();
        $chain->attach(function ($value) {
            return strtolower($value);
        });
        $value = 'AbC';
        $this->assertEquals('abc', $chain->filter($value));
    }

    public function testAllowsConnectingViaClassShortName()
    {
        if (!function_exists('mb_strtolower')) {
            $this->markTestSkipped('mbstring required');
        }

        $chain = new FilterChain();
        $chain->attachByName('string_trim', null, 100)
              ->attachByName('strip_tags')
              ->attachByName('string_to_lower', array('encoding' => 'utf-8'), 900);
        $value = '<a name="foo"> ABC </a>';
        $valueExpected = 'abc';
        $this->assertEquals($valueExpected, $chain->filter($value));
    }

    public function testAllowsConfiguringFilters()
    {
        $config = $this->getChainConfig();
        $chain  = new FilterChain();
        $chain->setOptions($config);
        $value = '<a name="foo"> abc </a><img id="bar" />';
        $valueExpected = 'ABC <IMG ID="BAR" />';
        $this->assertEquals($valueExpected, $chain->filter($value));
    }

    public function testAllowsConfiguringFiltersViaConstructor()
    {
        $config = $this->getChainConfig();
        $chain  = new FilterChain($config);
        $value = '<a name="foo"> abc </a>';
        $valueExpected = 'ABC';
        $this->assertEquals($valueExpected, $chain->filter($value));
    }

    public function testConfigurationAllowsTraversableObjects()
    {
        $config = $this->getChainConfig();
        $config = new \ArrayIterator($config);
        $chain  = new FilterChain($config);
        $value = '<a name="foo"> abc </a>';
        $valueExpected = 'ABC';
        $this->assertEquals($valueExpected, $chain->filter($value));
    }

    public function testCanRetrieveFilterWithUndefinedConstructor()
    {
        $chain = new FilterChain(array(
            'filters' => array(
                array('name' => 'int'),
            ),
        ));
        $filtered = $chain->filter('127.1');
        $this->assertEquals(127, $filtered);
    }

    protected function getChainConfig()
    {
        return array(
            'callbacks' => array(
                array('callback' => __CLASS__ . '::staticUcaseFilter'),
                array('priority' => 10000, 'callback' => function ($value) {
                    return trim($value);
                }),
            ),
            'filters' => array(
                array('name' => 'strip_tags', 'options' => array('allowTags' => 'img', 'allowAttribs' => 'id'), 'priority' => 10100),
            ),
        );
    }

    public static function staticUcaseFilter($value)
    {
        return strtoupper($value);
    }

    /**
     * @group ZF-412
     */
    public function testCanAttachMultipleFiltersOfTheSameTypeAsDiscreteInstances()
    {
        $chain = new FilterChain();
        $chain->attachByName('PregReplace', array(
            'pattern'     => '/Foo/',
            'replacement' => 'Bar',
        ));
        $chain->attachByName('PregReplace', array(
            'pattern'     => '/Bar/',
            'replacement' => 'PARTY',
        ));

        $this->assertEquals(2, count($chain));
        $filters = $chain->getFilters();
        $compare = null;
        foreach ($filters as $filter) {
            $this->assertNotSame($compare, $filter);
            $compare = $filter;
        }

        $this->assertEquals('Tu et PARTY', $chain->filter('Tu et Foo'));
    }

    public function testClone()
    {
        $chain = new FilterChain();
        $clone = clone $chain;

        $chain->attachByName('strip_tags');

        $this->assertCount(0, $clone);
    }

    public function testCanSerializeFilterChain()
    {
        $chain = new FilterChain();
        $chain->attach(new LowerCase())
              ->attach(new StripUpperCase());
        $serialized = serialize($chain);

        $unserialized = unserialize($serialized);
        $this->assertInstanceOf('Zend\Filter\FilterChain', $unserialized);
        $this->assertEquals(2, count($unserialized));
        $value         = 'AbC';
        $valueExpected = 'abc';
        $this->assertEquals($valueExpected, $unserialized->filter($value));
    }
}


class LowerCase extends AbstractFilter
{
    public function filter($value)
    {
        return strtolower($value);
    }
}


class StripUpperCase extends AbstractFilter
{
    public function filter($value)
    {
        return preg_replace('/[A-Z]/', '', $value);
    }
}
