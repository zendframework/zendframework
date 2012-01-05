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

use Zend\Filter\Null as NullFilter;

/**
 * @category   Zend
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Filter
 */
class NullTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Zend_Filter_Null object
     *
     * @var Zend_Filter_Null
     */
    protected $_filter;

    /**
     * Creates a new Zend_Filter_Null object for each test method
     *
     * @return void
     */
    public function setUp()
    {
        $this->_filter = new NullFilter();
    }

    /**
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testBasic()
    {
        $filter = $this->_filter;
        $this->assertEquals(null, $filter(0.0));
        $this->assertEquals(null, $filter('0'));
        $this->assertEquals(null, $filter(''));
        $this->assertEquals(null, $filter(0));
        $this->assertEquals(null, $filter(array()));
        $this->assertEquals(null, $filter(false));
        $this->assertEquals('test', $filter('test'));
        $this->assertEquals(true, $filter(true));
    }

    /**
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testOnlyBoolean()
    {
        $filter = $this->_filter;
        $filter->setType(NullFilter::BOOLEAN);
        $this->assertEquals(0.0, $filter(0.0));
        $this->assertEquals('0', $filter('0'));
        $this->assertEquals('', $filter(''));
        $this->assertEquals(0, $filter(0));
        $this->assertEquals(array(), $filter(array()));
        $this->assertEquals(null, $filter(false));
        $this->assertEquals('test', $filter('test'));
        $this->assertEquals(true, $filter(true));
    }

    /**
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testOnlyInteger()
    {
        $filter = $this->_filter;
        $filter->setType(NullFilter::INTEGER);
        $this->assertEquals(0.0, $filter(0.0));
        $this->assertEquals('0', $filter('0'));
        $this->assertEquals('', $filter(''));
        $this->assertEquals(null, $filter(0));
        $this->assertEquals(array(), $filter(array()));
        $this->assertEquals(false, $filter(false));
        $this->assertEquals('test', $filter('test'));
        $this->assertEquals(true, $filter(true));
    }

    /**
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testOnlyArray()
    {
        $filter = $this->_filter;
        $filter->setType(NullFilter::EMPTY_ARRAY);
        $this->assertEquals(0.0, $filter(0.0));
        $this->assertEquals('0', $filter('0'));
        $this->assertEquals('', $filter(''));
        $this->assertEquals(0, $filter(0));
        $this->assertEquals(null, $filter(array()));
        $this->assertEquals(false, $filter(false));
        $this->assertEquals('test', $filter('test'));
        $this->assertEquals(true, $filter(true));
    }

    /**
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testOnlyString()
    {
        $filter = $this->_filter;
        $filter->setType(NullFilter::STRING);
        $this->assertEquals(0.0, $filter(0.0));
        $this->assertEquals('0', $filter('0'));
        $this->assertEquals(null, $filter(''));
        $this->assertEquals(0, $filter(0));
        $this->assertEquals(array(), $filter(array()));
        $this->assertEquals(false, $filter(false));
        $this->assertEquals('test', $filter('test'));
        $this->assertEquals(true, $filter(true));
    }

    /**
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testOnlyZero()
    {
        $filter = $this->_filter;
        $filter->setType(NullFilter::ZERO);
        $this->assertEquals(0.0, $filter(0.0));
        $this->assertEquals(null, $filter('0'));
        $this->assertEquals('', $filter(''));
        $this->assertEquals(0, $filter(0));
        $this->assertEquals(array(), $filter(array()));
        $this->assertEquals(false, $filter(false));
        $this->assertEquals('test', $filter('test'));
        $this->assertEquals(true, $filter(true));
    }

    /**
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testArrayConstantNotation()
    {
        $filter = new NullFilter(
            array(
                NullFilter::ZERO,
                NullFilter::STRING,
                NullFilter::BOOLEAN,
            )
        );

        $this->assertEquals(0.0, $filter(0.0));
        $this->assertEquals(null, $filter('0'));
        $this->assertEquals(null, $filter(''));
        $this->assertEquals(0, $filter(0));
        $this->assertEquals(array(), $filter(array()));
        $this->assertEquals(null, $filter(false));
        $this->assertEquals('test', $filter('test'));
        $this->assertEquals(true, $filter(true));
    }

    /**
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testArrayConfigNotation()
    {
        $filter = new NullFilter(
            array(
                'type' => array(
                    NullFilter::ZERO,
                    NullFilter::STRING,
                    NullFilter::BOOLEAN),
                'test' => false
            )
        );

        $this->assertEquals(0.0, $filter(0.0));
        $this->assertEquals(null, $filter('0'));
        $this->assertEquals(null, $filter(''));
        $this->assertEquals(0, $filter(0));
        $this->assertEquals(array(), $filter(array()));
        $this->assertEquals(null, $filter(false));
        $this->assertEquals('test', $filter('test'));
        $this->assertEquals(true, $filter(true));
    }

    /**
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testMultiConstantNotation()
    {
        $filter = new NullFilter(
            NullFilter::ZERO + NullFilter::STRING + NullFilter::BOOLEAN
        );

        $this->assertEquals(0.0, $filter(0.0));
        $this->assertEquals(null, $filter('0'));
        $this->assertEquals(null, $filter(''));
        $this->assertEquals(0, $filter(0));
        $this->assertEquals(array(), $filter(array()));
        $this->assertEquals(null, $filter(false));
        $this->assertEquals('test', $filter('test'));
        $this->assertEquals(true, $filter(true));
    }

    /**
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testStringNotation()
    {
        $filter = new NullFilter(
            array(
                'zero', 'string', 'boolean'
            )
        );

        $this->assertEquals(0.0, $filter(0.0));
        $this->assertEquals(null, $filter('0'));
        $this->assertEquals(null, $filter(''));
        $this->assertEquals(0, $filter(0));
        $this->assertEquals(array(), $filter(array()));
        $this->assertEquals(null, $filter(false));
        $this->assertEquals('test', $filter('test'));
        $this->assertEquals(true, $filter(true));
    }

    /**
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testSingleStringNotation()
    {
        $filter = new NullFilter(
            'boolean'
        );

        $this->assertEquals(0.0, $filter(0.0));
        $this->assertEquals('0', $filter('0'));
        $this->assertEquals(null, $filter(''));
        $this->assertEquals(0, $filter(0));
        $this->assertEquals(array(), $filter(array()));
        $this->assertEquals(false, $filter(false));
        $this->assertEquals('test', $filter('test'));
        $this->assertEquals(true, $filter(true));
    }

    /**
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testSettingFalseType()
    {
        $this->setExpectedException('\Zend\Filter\Exception\InvalidArgumentException', 'Unknown');
        $this->_filter->setType(true);
    }

    /**
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testGetType()
    {
        $this->assertEquals(63, $this->_filter->getType());
    }

    /**
     * @group ZF-10388
     */
    public function testDataTypeFloat()
    {
        $filter = $this->_filter;
        $this->assertEquals(null, $filter(0.0));
    }

    /**
     * @group ZF-10388
     */
    public function testOnlyFloat()
    {
        $filter = $this->_filter;
        $filter->setType(NullFilter::FLOAT);
        $this->assertEquals(null, $filter(0.0));
        $this->assertEquals('0', $filter('0'));
        $this->assertEquals('', $filter(''));
        $this->assertEquals(0, $filter(0));
        $this->assertEquals(array(), $filter(array()));
        $this->assertEquals(false, $filter(false));
        $this->assertEquals('test', $filter('test'));
        $this->assertEquals(true, $filter(true));
    }
}
