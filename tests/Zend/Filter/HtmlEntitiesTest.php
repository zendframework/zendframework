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
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../TestHelper.php';

/**
 * @see Zend_Filter_HtmlEntities
 */
require_once 'Zend/Filter/HtmlEntities.php';

/**
 * @category   Zend
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Filter
 */
class Zend_Filter_HtmlEntitiesTest extends PHPUnit_Framework_TestCase
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
        $this->_filter = new Zend_Filter_HtmlEntities();
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
        foreach ($valuesExpected as $input => $output) {
            $this->assertEquals($output, $this->_filter->filter($input));
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
     * @return void
     */
    public function testGetCharSet()
    {
        $this->assertEquals('ISO-8859-1', $this->_filter->getCharSet());
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
        $this->assertTrue($instance instanceof Zend_Filter_HtmlEntities);
    }
}
