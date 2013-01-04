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

use stdClass;
use Zend\Filter\StringTrim;

/**
 * @category   Zend
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @group      Zend_Filter
 */
class StringTrimTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var StringTrim
     */
    protected $_filter;

    /**
     * Creates a new Zend\Filter\StringTrim object for each test method
     *
     * @return void
     */
    public function setUp()
    {
        $this->_filter = new StringTrim();
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
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testUtf8()
    {
        if (version_compare(PHP_VERSION, '5.3.4', 'lt')) {
            $this->markTestSkipped('PCRE update in 5.3.4 fixes unicode whitespace checking issues; in 5.3.3, this test fails due to outdated PCRE version');
        }
        $this->assertEquals('a', $this->_filter->filter(utf8_encode("\xa0a\xa0")));
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
     * @group ZF-7183
     */
    public function testZF7183()
    {
        $filter = $this->_filter;
        $this->assertEquals('Зенд', $filter('Зенд'));
    }

    /**
     * @group ZF2-170
     */
    public function testZF2170()
    {
        $filter = $this->_filter;
        $this->assertEquals('Расчет', $filter('Расчет'));
    }


    /**
     * @group ZF-7902
     */
    public function testZF7902()
    {
        $filter = $this->_filter;
        $this->assertEquals('/', $filter('/'));
    }

    /**
     * @group ZF-10891
     */
    public function testZF10891()
    {
        $filter = $this->_filter;
        $this->assertEquals('Зенд', $filter('   Зенд   '));
        $this->assertEquals('Зенд', $filter('Зенд   '));
        $this->assertEquals('Зенд', $filter('   Зенд'));

        $trim_charlist = " \t\n\r\x0B・。";
        $filter = new StringTrim($trim_charlist);
        $this->assertEquals('Зенд', $filter->filter('。  Зенд  。'));
    }

    public function getNonStringValues()
    {
        return array(
            array(1),
            array(1.0),
            array(true),
            array(false),
            array(null),
            array(array(1, 2, 3)),
            array(new stdClass()),
        );
    }

    /**
     * @dataProvider getNonStringValues
     */
    public function testShouldNotFilterNonStringValues($value)
    {
        $filtered = $this->_filter->filter($value);
        $this->assertSame($value, $filtered);
    }
}
