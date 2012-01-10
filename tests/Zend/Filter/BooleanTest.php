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

use Zend\Filter\Boolean as BooleanFilter,
    Zend\Locale\Locale;

/**
 * @category   Zend
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Filter
 */
class BooleanTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Zend_Filter_Boolean object
     *
     * @var Zend_Filter_Boolean
     */
    protected $_filter;

    /**
     * Creates a new Zend_Filter_Boolean object for each test method
     *
     * @return void
     */
    public function setUp()
    {
        $this->_filter = new BooleanFilter();
    }

    /**
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testBasic()
    {
        $filter = $this->_filter;
        $this->assertFalse($filter(false));
        $this->assertTrue($filter(true));
        $this->assertFalse($filter(0));
        $this->assertTrue($filter(1));
        $this->assertFalse($filter(0.0));
        $this->assertTrue($filter(1.0));
        $this->assertFalse($filter(''));
        $this->assertTrue($filter('abc'));
        $this->assertFalse($filter('0'));
        $this->assertTrue($filter('1'));
        $this->assertFalse($filter(array()));
        $this->assertTrue($filter(array('xxx')));
        $this->assertFalse($filter(null));
        $this->assertTrue($filter('false'));
        $this->assertTrue($filter('true'));
        $this->assertTrue($filter('no'));
        $this->assertTrue($filter('yes'));
    }

    /**
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testOnlyBoolean()
    {
        $filter = $this->_filter;
        $filter->setType(BooleanFilter::BOOLEAN);
        $this->assertFalse($filter(false));
        $this->assertTrue($filter(true));
        $this->assertTrue($filter(0));
        $this->assertTrue($filter(1));
        $this->assertTrue($filter(0.0));
        $this->assertTrue($filter(1.0));
        $this->assertTrue($filter(''));
        $this->assertTrue($filter('abc'));
        $this->assertTrue($filter('0'));
        $this->assertTrue($filter('1'));
        $this->assertTrue($filter(array()));
        $this->assertTrue($filter(array('xxx')));
        $this->assertTrue($filter(null));
        $this->assertTrue($filter('false'));
        $this->assertTrue($filter('true'));
        $this->assertTrue($filter('no'));
        $this->assertTrue($filter('yes'));
    }

    /**
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testOnlyInteger()
    {
        $filter = $this->_filter;
        $filter->setType(BooleanFilter::INTEGER);
        $this->assertTrue($filter(false));
        $this->assertTrue($filter(true));
        $this->assertFalse($filter(0));
        $this->assertTrue($filter(1));
        $this->assertTrue($filter(0.0));
        $this->assertTrue($filter(1.0));
        $this->assertTrue($filter(''));
        $this->assertTrue($filter('abc'));
        $this->assertTrue($filter('0'));
        $this->assertTrue($filter('1'));
        $this->assertTrue($filter(array()));
        $this->assertTrue($filter(array('xxx')));
        $this->assertTrue($filter(null));
        $this->assertTrue($filter('false'));
        $this->assertTrue($filter('true'));
        $this->assertTrue($filter('no'));
        $this->assertTrue($filter('yes'));
    }

    /**
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testOnlyFloat()
    {
        $filter = $this->_filter;
        $filter->setType(BooleanFilter::FLOAT);
        $this->assertTrue($filter(false));
        $this->assertTrue($filter(true));
        $this->assertTrue($filter(0));
        $this->assertTrue($filter(1));
        $this->assertFalse($filter(0.0));
        $this->assertTrue($filter(1.0));
        $this->assertTrue($filter(''));
        $this->assertTrue($filter('abc'));
        $this->assertTrue($filter('0'));
        $this->assertTrue($filter('1'));
        $this->assertTrue($filter(array()));
        $this->assertTrue($filter(array('xxx')));
        $this->assertTrue($filter(null));
        $this->assertTrue($filter('false'));
        $this->assertTrue($filter('true'));
        $this->assertTrue($filter('no'));
        $this->assertTrue($filter('yes'));
    }

    /**
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testOnlyString()
    {
        $filter = $this->_filter;
        $filter->setType(BooleanFilter::STRING);
        $this->assertTrue($filter(false));
        $this->assertTrue($filter(true));
        $this->assertTrue($filter(0));
        $this->assertTrue($filter(1));
        $this->assertTrue($filter(0.0));
        $this->assertTrue($filter(1.0));
        $this->assertFalse($filter(''));
        $this->assertTrue($filter('abc'));
        $this->assertTrue($filter('0'));
        $this->assertTrue($filter('1'));
        $this->assertTrue($filter(array()));
        $this->assertTrue($filter(array('xxx')));
        $this->assertTrue($filter(null));
        $this->assertTrue($filter('false'));
        $this->assertTrue($filter('true'));
        $this->assertTrue($filter('no'));
        $this->assertTrue($filter('yes'));
    }

    /**
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testOnlyZero()
    {
        $filter = $this->_filter;
        $filter->setType(BooleanFilter::ZERO);
        $this->assertTrue($filter(false));
        $this->assertTrue($filter(true));
        $this->assertTrue($filter(0));
        $this->assertTrue($filter(1));
        $this->assertTrue($filter(0.0));
        $this->assertTrue($filter(1.0));
        $this->assertTrue($filter(''));
        $this->assertTrue($filter('abc'));
        $this->assertFalse($filter('0'));
        $this->assertTrue($filter('1'));
        $this->assertTrue($filter(array()));
        $this->assertTrue($filter(array('xxx')));
        $this->assertTrue($filter(null));
        $this->assertTrue($filter('false'));
        $this->assertTrue($filter('true'));
        $this->assertTrue($filter('no'));
        $this->assertTrue($filter('yes'));
    }

    /**
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testOnlyArray()
    {
        $filter = $this->_filter;
        $filter->setType(BooleanFilter::EMPTY_ARRAY);
        $this->assertTrue($filter(false));
        $this->assertTrue($filter(true));
        $this->assertTrue($filter(0));
        $this->assertTrue($filter(1));
        $this->assertTrue($filter(0.0));
        $this->assertTrue($filter(1.0));
        $this->assertTrue($filter(''));
        $this->assertTrue($filter('abc'));
        $this->assertTrue($filter('0'));
        $this->assertTrue($filter('1'));
        $this->assertFalse($filter(array()));
        $this->assertTrue($filter(array('xxx')));
        $this->assertTrue($filter(null));
        $this->assertTrue($filter('false'));
        $this->assertTrue($filter('true'));
        $this->assertTrue($filter('no'));
        $this->assertTrue($filter('yes'));
    }

    /**
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testOnlyNull()
    {
        $filter = $this->_filter;
        $filter->setType(BooleanFilter::NULL);
        $this->assertTrue($filter(false));
        $this->assertTrue($filter(true));
        $this->assertTrue($filter(0));
        $this->assertTrue($filter(1));
        $this->assertTrue($filter(0.0));
        $this->assertTrue($filter(1.0));
        $this->assertTrue($filter(''));
        $this->assertTrue($filter('abc'));
        $this->assertTrue($filter('0'));
        $this->assertTrue($filter('1'));
        $this->assertTrue($filter(array()));
        $this->assertTrue($filter(array('xxx')));
        $this->assertFalse($filter(null));
        $this->assertTrue($filter('false'));
        $this->assertTrue($filter('true'));
        $this->assertTrue($filter('no'));
        $this->assertTrue($filter('yes'));
    }

    /**
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testOnlyPHP()
    {
        $filter = $this->_filter;
        $filter->setType(BooleanFilter::PHP);
        $this->assertFalse($filter(false));
        $this->assertTrue($filter(true));
        $this->assertFalse($filter(0));
        $this->assertTrue($filter(1));
        $this->assertFalse($filter(0.0));
        $this->assertTrue($filter(1.0));
        $this->assertFalse($filter(''));
        $this->assertTrue($filter('abc'));
        $this->assertFalse($filter('0'));
        $this->assertTrue($filter('1'));
        $this->assertFalse($filter(array()));
        $this->assertTrue($filter(array('xxx')));
        $this->assertFalse($filter(null));
        $this->assertTrue($filter('false'));
        $this->assertTrue($filter('true'));
        $this->assertTrue($filter('no'));
        $this->assertTrue($filter('yes'));
    }

    /**
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testOnlyFalseString()
    {
        $filter = $this->_filter;
        $filter->setType(BooleanFilter::FALSE_STRING);
        $this->assertTrue($filter(false));
        $this->assertTrue($filter(true));
        $this->assertTrue($filter(0));
        $this->assertTrue($filter(1));
        $this->assertTrue($filter(0.0));
        $this->assertTrue($filter(1.0));
        $this->assertTrue($filter(''));
        $this->assertTrue($filter('abc'));
        $this->assertTrue($filter('0'));
        $this->assertTrue($filter('1'));
        $this->assertTrue($filter(array()));
        $this->assertTrue($filter(array('xxx')));
        $this->assertTrue($filter(null));
        $this->assertFalse($filter('false'));
        $this->assertTrue($filter('true'));
        $this->assertTrue($filter('no'));
        $this->assertTrue($filter('yes'));
    }

    /**
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testOnlyYes()
    {
        $filter = $this->_filter;
        $filter->setType(BooleanFilter::YES);
        $filter->setLocale('en');
        $this->assertTrue($filter(false));
        $this->assertTrue($filter(true));
        $this->assertTrue($filter(0));
        $this->assertTrue($filter(1));
        $this->assertTrue($filter(0.0));
        $this->assertTrue($filter(1.0));
        $this->assertTrue($filter(''));
        $this->assertTrue($filter('abc'));
        $this->assertTrue($filter('0'));
        $this->assertTrue($filter('1'));
        $this->assertTrue($filter(array()));
        $this->assertTrue($filter(array('xxx')));
        $this->assertTrue($filter(null));
        $this->assertTrue($filter('false'));
        $this->assertTrue($filter('true'));
        $this->assertFalse($filter('no'));
        $this->assertTrue($filter('yes'));
    }

    /**
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testOnlyAll()
    {
        $filter = $this->_filter;
        $filter->setType(BooleanFilter::ALL);
        $filter->setLocale('en');
        $this->assertFalse($filter(false));
        $this->assertTrue($filter(true));
        $this->assertFalse($filter(0));
        $this->assertTrue($filter(1));
        $this->assertFalse($filter(0.0));
        $this->assertTrue($filter(1.0));
        $this->assertFalse($filter(''));
        $this->assertTrue($filter('abc'));
        $this->assertFalse($filter('0'));
        $this->assertTrue($filter('1'));
        $this->assertFalse($filter(array()));
        $this->assertTrue($filter(array('xxx')));
        $this->assertFalse($filter(null));
        $this->assertFalse($filter('false'));
        $this->assertTrue($filter('true'));
        $this->assertFalse($filter('no'));
        $this->assertTrue($filter('yes'));
    }

    /**
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testArrayConstantNotation()
    {
        $filter = new BooleanFilter(
            array(
                'type' => array(
                    BooleanFilter::ZERO,
                    BooleanFilter::STRING,
                    BooleanFilter::BOOLEAN,
                ),
            )
        );

        $this->assertFalse($filter(false));
        $this->assertTrue($filter(true));
        $this->assertTrue($filter(0));
        $this->assertTrue($filter(1));
        $this->assertTrue($filter(0.0));
        $this->assertTrue($filter(1.0));
        $this->assertFalse($filter(''));
        $this->assertTrue($filter('abc'));
        $this->assertFalse($filter('0'));
        $this->assertTrue($filter('1'));
        $this->assertTrue($filter(array()));
        $this->assertTrue($filter(array('xxx')));
        $this->assertTrue($filter(null));
        $this->assertTrue($filter('false'));
        $this->assertTrue($filter('true'));
        $this->assertTrue($filter('no'));
        $this->assertTrue($filter('yes'));
    }

    /**
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testArrayConfigNotation()
    {
        $filter = new BooleanFilter(
            array(
                'type' => array(
                    BooleanFilter::ZERO,
                    BooleanFilter::STRING,
                    BooleanFilter::BOOLEAN,
                ),
                'test' => false,
            )
        );

        $this->assertFalse($filter(false));
        $this->assertTrue($filter(true));
        $this->assertTrue($filter(0));
        $this->assertTrue($filter(1));
        $this->assertTrue($filter(0.0));
        $this->assertTrue($filter(1.0));
        $this->assertFalse($filter(''));
        $this->assertTrue($filter('abc'));
        $this->assertFalse($filter('0'));
        $this->assertTrue($filter('1'));
        $this->assertTrue($filter(array()));
        $this->assertTrue($filter(array('xxx')));
        $this->assertTrue($filter(null));
        $this->assertTrue($filter('false'));
        $this->assertTrue($filter('true'));
        $this->assertTrue($filter('no'));
        $this->assertTrue($filter('yes'));
    }

    /**
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testMultiConstantNotation()
    {
        $filter = new BooleanFilter(
            BooleanFilter::ZERO + BooleanFilter::STRING + BooleanFilter::BOOLEAN
        );

        $this->assertFalse($filter(false));
        $this->assertTrue($filter(true));
        $this->assertTrue($filter(0));
        $this->assertTrue($filter(1));
        $this->assertTrue($filter(0.0));
        $this->assertTrue($filter(1.0));
        $this->assertFalse($filter(''));
        $this->assertTrue($filter('abc'));
        $this->assertFalse($filter('0'));
        $this->assertTrue($filter('1'));
        $this->assertTrue($filter(array()));
        $this->assertTrue($filter(array('xxx')));
        $this->assertTrue($filter(null));
        $this->assertTrue($filter('false'));
        $this->assertTrue($filter('true'));
        $this->assertTrue($filter('no'));
        $this->assertTrue($filter('yes'));
    }

    /**
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testStringNotation()
    {
        $filter = new BooleanFilter(
            array(
                'type' => array('zero', 'string', 'boolean')
            )
        );

        $this->assertFalse($filter(false));
        $this->assertTrue($filter(true));
        $this->assertTrue($filter(0));
        $this->assertTrue($filter(1));
        $this->assertTrue($filter(0.0));
        $this->assertTrue($filter(1.0));
        $this->assertFalse($filter(''));
        $this->assertTrue($filter('abc'));
        $this->assertFalse($filter('0'));
        $this->assertTrue($filter('1'));
        $this->assertTrue($filter(array()));
        $this->assertTrue($filter(array('xxx')));
        $this->assertTrue($filter(null));
        $this->assertTrue($filter('false'));
        $this->assertTrue($filter('true'));
        $this->assertTrue($filter('no'));
        $this->assertTrue($filter('yes'));
    }

    /**
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testSingleStringNotation()
    {
        $filter = new BooleanFilter(
            'boolean'
        );

        $this->assertFalse($filter(false));
        $this->assertTrue($filter(true));
        $this->assertTrue($filter(0));
        $this->assertTrue($filter(1));
        $this->assertTrue($filter(0.0));
        $this->assertTrue($filter(1.0));
        $this->assertTrue($filter(''));
        $this->assertTrue($filter('abc'));
        $this->assertTrue($filter('0'));
        $this->assertTrue($filter('1'));
        $this->assertTrue($filter(array()));
        $this->assertTrue($filter(array('xxx')));
        $this->assertTrue($filter(null));
        $this->assertTrue($filter('false'));
        $this->assertTrue($filter('true'));
        $this->assertTrue($filter('no'));
        $this->assertTrue($filter('yes'));
    }

    /**
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testSettingLocale()
    {
        $filter = $this->_filter;
        $filter->setType(BooleanFilter::ALL);
        $filter->setLocale('de');
        $this->assertFalse($filter(false));
        $this->assertTrue($filter(true));
        $this->assertFalse($filter(0));
        $this->assertTrue($filter(1));
        $this->assertFalse($filter(0.0));
        $this->assertTrue($filter(1.0));
        $this->assertFalse($filter(''));
        $this->assertTrue($filter('abc'));
        $this->assertFalse($filter('0'));
        $this->assertTrue($filter('1'));
        $this->assertFalse($filter(array()));
        $this->assertTrue($filter(array('xxx')));
        $this->assertFalse($filter(null));
        $this->assertFalse($filter('false'));
        $this->assertTrue($filter('true'));
        $this->assertTrue($filter('no'));
        $this->assertTrue($filter('yes'));
        $this->assertFalse($filter('nein'));
        $this->assertTrue($filter('ja'));
    }

    /**
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testSettingLocalePerConstructorString()
    {
        $filter = new BooleanFilter(
            'all', true, 'de'
        );

        $this->assertFalse($filter(false));
        $this->assertTrue($filter(true));
        $this->assertFalse($filter(0));
        $this->assertTrue($filter(1));
        $this->assertFalse($filter(0.0));
        $this->assertTrue($filter(1.0));
        $this->assertFalse($filter(''));
        $this->assertTrue($filter('abc'));
        $this->assertFalse($filter('0'));
        $this->assertTrue($filter('1'));
        $this->assertFalse($filter(array()));
        $this->assertTrue($filter(array('xxx')));
        $this->assertFalse($filter(null));
        $this->assertFalse($filter('false'));
        $this->assertTrue($filter('true'));
        $this->assertTrue($filter('no'));
        $this->assertTrue($filter('yes'));
        $this->assertFalse($filter('nein'));
        $this->assertTrue($filter('ja'));
    }

    /**
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testConfigObject()
    {
        $options = array('type' => 'all', 'locale' => 'de');
        $config  = new \Zend\Config\Config($options);

        $filter = new BooleanFilter(
            $config
        );

        $this->assertFalse($filter(false));
        $this->assertTrue($filter(true));
        $this->assertFalse($filter(0));
        $this->assertTrue($filter(1));
        $this->assertFalse($filter(0.0));
        $this->assertTrue($filter(1.0));
        $this->assertFalse($filter(''));
        $this->assertTrue($filter('abc'));
        $this->assertFalse($filter('0'));
        $this->assertTrue($filter('1'));
        $this->assertFalse($filter(array()));
        $this->assertTrue($filter(array('xxx')));
        $this->assertFalse($filter(null));
        $this->assertFalse($filter('false'));
        $this->assertTrue($filter('true'));
        $this->assertTrue($filter('no'));
        $this->assertTrue($filter('yes'));
        $this->assertFalse($filter('nein'));
        $this->assertTrue($filter('ja'));
    }

    /**
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testSettingLocalePerConstructorArray()
    {
        $filter = new BooleanFilter(
            array('type' => 'all', 'locale' => 'de')
        );

        $this->assertFalse($filter(false));
        $this->assertTrue($filter(true));
        $this->assertFalse($filter(0));
        $this->assertTrue($filter(1));
        $this->assertFalse($filter(0.0));
        $this->assertTrue($filter(1.0));
        $this->assertFalse($filter(''));
        $this->assertTrue($filter('abc'));
        $this->assertFalse($filter('0'));
        $this->assertTrue($filter('1'));
        $this->assertFalse($filter(array()));
        $this->assertTrue($filter(array('xxx')));
        $this->assertFalse($filter(null));
        $this->assertFalse($filter('false'));
        $this->assertTrue($filter('true'));
        $this->assertTrue($filter('no'));
        $this->assertTrue($filter('yes'));
        $this->assertFalse($filter('nein'));
        $this->assertTrue($filter('ja'));
    }

    /**
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testSettingLocaleInstance()
    {
        $locale = new Locale('de');
        $filter = new BooleanFilter(
            array('type' => 'all', 'locale' => $locale)
        );

        $this->assertFalse($filter(false));
        $this->assertTrue($filter(true));
        $this->assertFalse($filter(0));
        $this->assertTrue($filter(1));
        $this->assertFalse($filter(0.0));
        $this->assertTrue($filter(1.0));
        $this->assertFalse($filter(''));
        $this->assertTrue($filter('abc'));
        $this->assertFalse($filter('0'));
        $this->assertTrue($filter('1'));
        $this->assertFalse($filter(array()));
        $this->assertTrue($filter(array('xxx')));
        $this->assertFalse($filter(null));
        $this->assertFalse($filter('false'));
        $this->assertTrue($filter('true'));
        $this->assertTrue($filter('no'));
        $this->assertTrue($filter('yes'));
        $this->assertFalse($filter('nein'));
        $this->assertTrue($filter('ja'));
    }

    /**
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testWithoutCasting()
    {
        $locale = new Locale('de');
        $filter = new BooleanFilter(
            array('type' => 'all', 'casting' => false, 'locale' => $locale)
        );

        $this->assertFalse($filter(false));
        $this->assertTrue($filter(true));
        $this->assertFalse($filter(0));
        $this->assertTrue($filter(1));
        $this->assertEquals(2, $filter(2));
        $this->assertFalse($filter(0.0));
        $this->assertTrue($filter(1.0));
        $this->assertEquals(0.5, $filter(0.5));
        $this->assertFalse($filter(''));
        $this->assertEquals('abc', $filter('abc'));
        $this->assertFalse($filter('0'));
        $this->assertTrue($filter('1'));
        $this->assertEquals('2', $filter('2'));
        $this->assertFalse($filter(array()));
        $this->assertEquals(array('xxx'), $filter(array('xxx')));
        $this->assertEquals(null, $filter(null));
        $this->assertFalse($filter('false'));
        $this->assertTrue($filter('true'));
        $this->assertEquals('no', $filter('no'));
        $this->assertEquals('yes', $filter('yes'));
        $this->assertFalse($filter('nein'));
        $this->assertTrue($filter('ja'));
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
        $this->assertEquals(127, $this->_filter->getType());
    }

    /**
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testSettingFalseLocaleType()
    {
        $this->setExpectedException('\Zend\Filter\Exception\InvalidArgumentException', 'Locale has to be');
        $this->_filter->setLocale(true);
    }

    /**
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testSettingUnknownLocale()
    {
        $this->setExpectedException('\Zend\Filter\Exception\InvalidArgumentException', 'Unknown locale');
        $this->_filter->setLocale('yy');
    }
}
