<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Stdlib\Hydrator\Filter;

use Zend\Stdlib\Hydrator\Filter\OptionalParametersFilter;

/**
 * Unit tests for {@see \Zend\Stdlib\Hydrator\Filter\OptionalParametersFilter}
 *
 * @covers \Zend\Stdlib\Hydrator\Filter\OptionalParametersFilter
 */
class OptionalParametersFilterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var OptionalParametersFilter
     */
    protected $filter;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        $this->filter = new OptionalParametersFilter();
    }

    /**
     * Verifies a list of methods against expected results
     *
     * @param string $method
     * @param bool   $expectedResult
     *
     * @dataProvider methodProvider
     */
    public function testMethods($method, $expectedResult)
    {
        $this->assertSame($expectedResult, $this->filter->filter($method));
    }

    public function testTriggersExceptionOnUnknownMethod()
    {
        $this->setExpectedException('InvalidArgumentException');
        $this->filter->filter(__CLASS__ . '::' . 'nonExistingMethod');
    }

    /**
     * Provides a list of methods to be checked against the filter
     *
     * @return array
     */
    public function methodProvider()
    {
        return array(
            array(__CLASS__ . '::' . 'methodWithoutParameters', true),
            array(__CLASS__ . '::' . 'methodWithSingleMandatoryParameter', false),
            array(__CLASS__ . '::' . 'methodWithSingleOptionalParameter', true),
            array(__CLASS__ . '::' . 'methodWithMultipleMandatoryParameters', false),
            array(__CLASS__ . '::' . 'methodWithMultipleOptionalParameters', true),
        );
    }

    /**
     * Test asset method
     */
    public function methodWithoutParameters()
    {
    }

    /**
     * Test asset method
     */
    public function methodWithSingleMandatoryParameter($parameter)
    {
    }

    /**
     * Test asset method
     */
    public function methodWithSingleOptionalParameter($parameter = null)
    {
    }

    /**
     * Test asset method
     */
    public function methodWithMultipleMandatoryParameters($parameter, $otherParameter)
    {
    }

    /**
     * Test asset method
     */
    public function methodWithMultipleOptionalParameters($parameter = null, $otherParameter = null)
    {
    }
}
