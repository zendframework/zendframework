<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Filter\Word;

use Zend\Filter\Word\DashToSeparator as DashToSeparatorFilter;

/**
 * Test class for Zend\Filter\Word\DashToSeparator.
 *
 * @group      Zend_Filter
 */
class DashToSeparatorTest extends \PHPUnit_Framework_TestCase
{
    public function testFilterSeparatesDashedWordsWithDefaultSpaces()
    {
        $string   = 'dash-separated-words';
        $filter   = new DashToSeparatorFilter();
        $filtered = $filter($string);

        $this->assertNotEquals($string, $filtered);
        $this->assertEquals('dash separated words', $filtered);
    }

    public function testFilterSeparatesDashedWordsWithSomeString()
    {
        $string   = 'dash-separated-words';
        $filter   = new DashToSeparatorFilter(':-:');
        $filtered = $filter($string);

        $this->assertNotEquals($string, $filtered);
        $this->assertEquals('dash:-:separated:-:words', $filtered);
    }

    /**
     * @return void
     */
    public function testFilterSupportArray()
    {
        $filter = new DashToSeparatorFilter();

        $input = array(
            'dash-separated-words',
            'something-different'
        );

        $filtered = $filter($input);

        $this->assertNotEquals($input, $filtered);
        $this->assertEquals(array('dash separated words', 'something different'), $filtered);
    }


    public function returnUnfilteredDataProvider()
    {
        return array(
            array(null),
            array(new \stdClass())
        );
    }

    /**
     * @dataProvider returnUnfilteredDataProvider
     * @return void
     */
    public function testReturnUnfiltered($input)
    {
        $filter = new DashToSeparatorFilter();

        $this->assertEquals($input, $filter($input));
    }

}
