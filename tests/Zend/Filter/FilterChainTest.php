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
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Filter;

use Zend\Filter\FilterChain,
    Zend\Filter\AbstractFilter;

/**
 * @category   Zend
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
        $chain->attach(function($value) {
            return strtolower($value);
        });
        $value = 'AbC';
        $this->assertEquals('abc', $chain->filter($value));
    }

    public function testAllowsConnectingViaClassShortName()
    {
        $chain = new FilterChain();
        $chain->attachByName('string_trim', array('encoding' => 'utf-8'), 100)
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
        $value = '<a name="foo"> abc </a>';
        $valueExpected = 'ABC';
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

    protected function getChainConfig()
    {
        return array(
            'callbacks' => array(
                array('callback' => __CLASS__ . '::staticUcaseFilter'),
                array('priority' => 10000, 'callback' => function($value) {
                    return trim($value);
                }),
            ),
            'filters' => array(
                array('name' => 'strip_tags', 'options' => array('encoding' => 'utf-8'), 'priority' => 10100),
            ),
        );
    }

    public static function staticUcaseFilter($value)
    {
        return strtoupper($value);
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
