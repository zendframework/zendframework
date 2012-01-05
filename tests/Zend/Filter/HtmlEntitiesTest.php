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

use Zend\Filter\HtmlEntities as HtmlEntitiesFilter;

/**
 * @category   Zend
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Filter
 */
class HtmlEntitiesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Zend_Filter_HtmlEntities object
     *
     * @var Zend_Filter_HtmlEntities
     */
    protected $_filter;

    /**
     * Creates a new Zend_Filter_HtmlEntities object for each test method
     *
     * @return void
     */
    public function setUp()
    {
        $this->_filter = new HtmlEntitiesFilter();
    }

    /**
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testBasic()
    {
        $valuesExpected = array(
            'string' => 'string',
            '<'      => '&lt;',
            '>'      => '&gt;',
            '\''     => '\'',
            '"'      => '&quot;',
            '&'      => '&amp;'
            );
        $filter = $this->_filter;
        foreach ($valuesExpected as $input => $output) {
            $this->assertEquals($output, $filter($input));
        }
    }

    /**
     * Ensures that getQuoteStyle() returns expected default value
     *
     * @return void
     */
    public function testGetQuoteStyle()
    {
        $this->assertEquals(ENT_COMPAT, $this->_filter->getQuoteStyle());
    }

    /**
     * Ensures that setQuoteStyle() follows expected behavior
     *
     * @return void
     */
    public function testSetQuoteStyle()
    {
        $this->_filter->setQuoteStyle(ENT_QUOTES);
        $this->assertEquals(ENT_QUOTES, $this->_filter->getQuoteStyle());
    }

    /**
     * Ensures that getCharSet() returns expected default value
     *
     * @group ZF-8715
     * @return void
     */
    public function testGetCharSet()
    {
        $this->assertEquals('UTF-8', $this->_filter->getCharSet());
    }

    /**
     * Ensures that setCharSet() follows expected behavior
     *
     * @return void
     */
    public function testSetCharSet()
    {
        $this->_filter->setCharSet('UTF-8');
        $this->assertEquals('UTF-8', $this->_filter->getCharSet());
    }

    /**
     * Ensures that getDoubleQuote() returns expected default value
     *
     * @return void
     */
    public function testGetDoubleQuote()
    {
        $this->assertEquals(true, $this->_filter->getDoubleQuote());
    }

    /**
     * Ensures that setDoubleQuote() follows expected behavior
     *
     * @return void
     */
    public function testSetDoubleQuote()
    {
        $this->_filter->setDoubleQuote(false);
        $this->assertEquals(false, $this->_filter->getDoubleQuote());
    }

    /**
     * Ensure that fluent interfaces are supported
     *
     * @group ZF-3172
     */
    public function testFluentInterface()
    {
        $instance = $this->_filter->setCharSet('UTF-8')->setQuoteStyle(ENT_QUOTES)->setDoubleQuote(false);
        $this->assertTrue($instance instanceof HtmlEntitiesFilter);
    }

    /**
     * @group ZF-8995
     */
    public function testConfigObject()
    {
        $options = array('quotestyle' => 5, 'encoding' => 'ISO-8859-1');
        $config  = new \Zend\Config\Config($options);

        $filter = new HtmlEntitiesFilter(
            $config
        );

        $this->assertEquals('ISO-8859-1', $filter->getEncoding());
        $this->assertEquals(5, $filter->getQuoteStyle());
    }

    /**
     * Ensures that when ENT_QUOTES is set, the filtered value has both 'single' and "double" quotes encoded
     *
     * @group  ZF-8962
     * @return void
     */
    public function testQuoteStyleQuotesEncodeBoth()
    {
        $input  = "A 'single' and " . '"double"';
        $result = 'A &#039;single&#039; and &quot;double&quot;';

        $this->_filter->setQuoteStyle(ENT_QUOTES);
        $this->assertEquals($result, $this->_filter->filter($input));
    }

    /**
     * Ensures that when ENT_COMPAT is set, the filtered value has only "double" quotes encoded
     *
     * @group  ZF-8962
     * @return void
     */
    public function testQuoteStyleQuotesEncodeDouble()
    {
        $input  = "A 'single' and " . '"double"';
        $result = "A 'single' and &quot;double&quot;";
        
        $this->_filter->setQuoteStyle(ENT_COMPAT);
        $this->assertEquals($result, $this->_filter->filter($input));
    }

    /**
     * Ensures that when ENT_NOQUOTES is set, the filtered value leaves both "double" and 'single' quotes un-altered
     *
     * @group  ZF-8962
     * @return void
     */
    public function testQuoteStyleQuotesEncodeNone()
    {
        $input  = "A 'single' and " . '"double"';
        $result = "A 'single' and " . '"double"';

        $this->_filter->setQuoteStyle(ENT_NOQUOTES);
        $this->assertEquals($result, $this->_filter->filter($input));
    }
}
