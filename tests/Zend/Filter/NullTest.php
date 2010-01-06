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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../TestHelper.php';

/**
 * @see Zend_Filter_Int
 */
require_once 'Zend/Filter/Null.php';

/**
 * @category   Zend
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Filter
 */
class Zend_Filter_NullTest extends PHPUnit_Framework_TestCase
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
        $this->_filter = new Zend_Filter_Null();
    }

    /**
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testBasic()
    {
        $this->assertEquals(null, $this->_filter->filter('0'));
        $this->assertEquals(null, $this->_filter->filter(''));
        $this->assertEquals(null, $this->_filter->filter(0));
        $this->assertEquals(null, $this->_filter->filter(array()));
        $this->assertEquals(null, $this->_filter->filter(false));
        $this->assertEquals('test', $this->_filter->filter('test'));
        $this->assertEquals(true, $this->_filter->filter(true));
    }

    /**
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testOnlyBoolean()
    {
        $this->_filter->setType(Zend_Filter_Null::BOOLEAN);
        $this->assertEquals('0', $this->_filter->filter('0'));
        $this->assertEquals('', $this->_filter->filter(''));
        $this->assertEquals(0, $this->_filter->filter(0));
        $this->assertEquals(array(), $this->_filter->filter(array()));
        $this->assertEquals(null, $this->_filter->filter(false));
        $this->assertEquals('test', $this->_filter->filter('test'));
        $this->assertEquals(true, $this->_filter->filter(true));
    }

    /**
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testOnlyInteger()
    {
        $this->_filter->setType(Zend_Filter_Null::INTEGER);
        $this->assertEquals('0', $this->_filter->filter('0'));
        $this->assertEquals('', $this->_filter->filter(''));
        $this->assertEquals(null, $this->_filter->filter(0));
        $this->assertEquals(array(), $this->_filter->filter(array()));
        $this->assertEquals(false, $this->_filter->filter(false));
        $this->assertEquals('test', $this->_filter->filter('test'));
        $this->assertEquals(true, $this->_filter->filter(true));
    }

    /**
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testOnlyArray()
    {
        $this->_filter->setType(Zend_Filter_Null::EMPTY_ARRAY);
        $this->assertEquals('0', $this->_filter->filter('0'));
        $this->assertEquals('', $this->_filter->filter(''));
        $this->assertEquals(0, $this->_filter->filter(0));
        $this->assertEquals(null, $this->_filter->filter(array()));
        $this->assertEquals(false, $this->_filter->filter(false));
        $this->assertEquals('test', $this->_filter->filter('test'));
        $this->assertEquals(true, $this->_filter->filter(true));
    }

    /**
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testOnlyString()
    {
        $this->_filter->setType(Zend_Filter_Null::STRING);
        $this->assertEquals('0', $this->_filter->filter('0'));
        $this->assertEquals(null, $this->_filter->filter(''));
        $this->assertEquals(0, $this->_filter->filter(0));
        $this->assertEquals(array(), $this->_filter->filter(array()));
        $this->assertEquals(false, $this->_filter->filter(false));
        $this->assertEquals('test', $this->_filter->filter('test'));
        $this->assertEquals(true, $this->_filter->filter(true));
    }

    /**
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testOnlyZero()
    {
        $this->_filter->setType(Zend_Filter_Null::ZERO);
        $this->assertEquals(null, $this->_filter->filter('0'));
        $this->assertEquals('', $this->_filter->filter(''));
        $this->assertEquals(0, $this->_filter->filter(0));
        $this->assertEquals(array(), $this->_filter->filter(array()));
        $this->assertEquals(false, $this->_filter->filter(false));
        $this->assertEquals('test', $this->_filter->filter('test'));
        $this->assertEquals(true, $this->_filter->filter(true));
    }

    /**
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testArrayConstantNotation()
    {
        $filter = new Zend_Filter_Null(
            array(
                Zend_Filter_Null::ZERO,
                Zend_Filter_Null::STRING,
                Zend_Filter_Null::BOOLEAN
            )
        );

        $this->assertEquals(null, $filter->filter('0'));
        $this->assertEquals(null, $filter->filter(''));
        $this->assertEquals(0, $filter->filter(0));
        $this->assertEquals(array(), $filter->filter(array()));
        $this->assertEquals(null, $filter->filter(false));
        $this->assertEquals('test', $filter->filter('test'));
        $this->assertEquals(true, $filter->filter(true));
    }

    /**
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testArrayConfigNotation()
    {
        $filter = new Zend_Filter_Null(
            array(
                'type' => array(
                    Zend_Filter_Null::ZERO,
                    Zend_Filter_Null::STRING,
                    Zend_Filter_Null::BOOLEAN),
                'test' => false
            )
        );

        $this->assertEquals(null, $filter->filter('0'));
        $this->assertEquals(null, $filter->filter(''));
        $this->assertEquals(0, $filter->filter(0));
        $this->assertEquals(array(), $filter->filter(array()));
        $this->assertEquals(null, $filter->filter(false));
        $this->assertEquals('test', $filter->filter('test'));
        $this->assertEquals(true, $filter->filter(true));
    }

    /**
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testMultiConstantNotation()
    {
        $filter = new Zend_Filter_Null(
            Zend_Filter_Null::ZERO + Zend_Filter_Null::STRING + Zend_Filter_Null::BOOLEAN
        );

        $this->assertEquals(null, $filter->filter('0'));
        $this->assertEquals(null, $filter->filter(''));
        $this->assertEquals(0, $filter->filter(0));
        $this->assertEquals(array(), $filter->filter(array()));
        $this->assertEquals(null, $filter->filter(false));
        $this->assertEquals('test', $filter->filter('test'));
        $this->assertEquals(true, $filter->filter(true));
    }

    /**
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testStringNotation()
    {
        $filter = new Zend_Filter_Null(
            array(
                'zero', 'string', 'boolean'
            )
        );

        $this->assertEquals(null, $filter->filter('0'));
        $this->assertEquals(null, $filter->filter(''));
        $this->assertEquals(0, $filter->filter(0));
        $this->assertEquals(array(), $filter->filter(array()));
        $this->assertEquals(null, $filter->filter(false));
        $this->assertEquals('test', $filter->filter('test'));
        $this->assertEquals(true, $filter->filter(true));
    }

    /**
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testSingleStringNotation()
    {
        $filter = new Zend_Filter_Null(
            'boolean'
        );

        $this->assertEquals('0', $filter->filter('0'));
        $this->assertEquals(null, $filter->filter(''));
        $this->assertEquals(0, $filter->filter(0));
        $this->assertEquals(array(), $filter->filter(array()));
        $this->assertEquals(false, $filter->filter(false));
        $this->assertEquals('test', $filter->filter('test'));
        $this->assertEquals(true, $filter->filter(true));
    }

    /**
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testSettingFalseType()
    {
        try {
            $this->_filter->setType(true);
            $this->fail();
        } catch (Zend_Exception $e) {
            $this->assertContains('Unknown', $e->getMessage());
        }
    }

    /**
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testGetType()
    {
        $this->assertEquals(31, $this->_filter->getType());
    }
}
