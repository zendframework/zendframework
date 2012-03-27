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

use Zend\Filter\StringTrim as StringTrimFilter;

/**
 * @category   Zend
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Filter
 */
class StringTrimTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Zend_Filter_StringTrim object
     *
     * @var Zend_Filter_StringTrim
     */
    protected $_filter;

    /**
     * Creates a new Zend_Filter_StringTrim object for each test method
     *
     * @return void
     */
    public function setUp()
    {
        $this->_filter = new StringTrimFilter();
    }

    /**
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testBasic()
    {
        $filter = $this->_filter;
        $valuesExpected = array(
            'string' => 'string',
            ' str '  => 'str',
            "\ns\t"  => 's'
            );
        foreach ($valuesExpected as $input => $output) {
            $this->assertEquals($output, $filter($input));
        }
    }

    /**
     * Ensures that getCharList() returns expected default value
     *
     * @return void
     */
    public function testGetCharList()
    {
        $this->assertEquals(null, $this->_filter->getCharList());
    }

    /**
     * Ensures that setCharList() follows expected behavior
     *
     * @return void
     */
    public function testSetCharList()
    {
        $this->_filter->setCharList('&');
        $this->assertEquals('&', $this->_filter->getCharList());
    }

    /**
     * Ensures expected behavior under custom character list
     *
     * @return void
     */
    public function testCharList()
    {
        $filter = $this->_filter;
        $filter->setCharList('&');
        $this->assertEquals('a&b', $filter('&&a&b&&'));
    }

    /**
     * @ZF-7183
     */
    public function testZF7183()
    {
        $filter = $this->_filter;
        $this->assertEquals('Зенд', $filter('Зенд'));
    }

    /**
     * @ZF-7902
     */
    public function testZF7902()
    {
        $filter = $this->_filter;
        $this->assertEquals('/', $filter('/'));
    }

    /**
     * @ZF-10891
     */
    public function testZF10891()
    {
        $filter = $this->_filter;
        $this->assertEquals('Зенд', $filter('   Зенд   '));
        $this->assertEquals('Зенд', $filter('Зенд   '));
        $this->assertEquals('Зенд', $filter('   Зенд'));

        $trim_charlist = " \t\n\r\x0B・。";
        $filter = new StringTrimFilter($trim_charlist);
        $this->assertEquals('Зенд', $filter->filter('。  Зенд  。'));


    }
}
