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

use Zend\Filter\HtmlEntities as HtmlEntitiesFilter;
use Zend\Filter\Exception;

/**
 * @category   Zend
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @group      Zend_Filter
 */
class HtmlEntitiesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Zend\Filter\HtmlEntities object
     *
     * @var \Zend\Filter\HtmlEntities
     */
    protected $_filter;

    /**
     * Creates a new Zend\Filter\HtmlEntities object for each test method
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
            '\''     => '&#039;',
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
        $this->assertEquals(ENT_QUOTES, $this->_filter->getQuoteStyle());
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

    /**
     * @group ZF-11344
     */
    public function testCorrectsForEncodingMismatch()
    {
        if (version_compare(phpversion(), '5.4', '>=')) {
            $this->markTestIncomplete('Code to test is not compatible with PHP 5.4 ');
        }

        $string = file_get_contents(dirname(__FILE__) . '/_files/latin-1-text.txt');

        // restore_error_handler can emit an E_WARNING; let's ignore that, as
        // we want to test the returned value
        set_error_handler(array($this, 'errorHandler'), E_NOTICE | E_WARNING);
        $result = $this->_filter->filter($string);
        restore_error_handler();

        $this->assertTrue(strlen($result) > 0);
    }

    /**
     * @group ZF-11344
     */
    public function testStripsUnknownCharactersWhenEncodingMismatchDetected()
    {
        if (version_compare(phpversion(), '5.4', '>=')) {
            $this->markTestIncomplete('Code to test is not compatible with PHP 5.4 ');
        }

        $string = file_get_contents(dirname(__FILE__) . '/_files/latin-1-text.txt');

        // restore_error_handler can emit an E_WARNING; let's ignore that, as
        // we want to test the returned value
        set_error_handler(array($this, 'errorHandler'), E_NOTICE | E_WARNING);
        $result = $this->_filter->filter($string);
        restore_error_handler();

        $this->assertContains('&quot;&quot;', $result);
    }

    /**
     * @group ZF-11344
     */
    public function testRaisesExceptionIfEncodingMismatchDetectedAndFinalStringIsEmpty()
    {
        if (version_compare(phpversion(), '5.4', '>=')) {
            $this->markTestIncomplete('Code to test is not compatible with PHP 5.4 ');
        }

        $string = file_get_contents(dirname(__FILE__) . '/_files/latin-1-dash-only.txt');

        // restore_error_handler can emit an E_WARNING; let's ignore that, as
        // we want to test the returned value
        // Also, explicit try, so that we don't mess up PHPUnit error handlers
        set_error_handler(array($this, 'errorHandler'), E_NOTICE | E_WARNING);
        try {
            $result = $this->_filter->filter($string);
            $this->fail('Expected exception from single non-utf-8 character');
        } catch (\Exception $e) {
            $this->assertTrue($e instanceof Exception\DomainException);
        }
    }

    /**
     * Null error handler; used when wanting to ignore specific error types
     */
    public function errorHandler($errno, $errstr)
    {
    }
}
