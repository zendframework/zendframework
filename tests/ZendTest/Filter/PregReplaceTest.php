<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */
namespace ZendTest\Filter;

use Zend\Filter\PregReplace as PregReplaceFilter;

/**
 * Test class for Zend\Filter\PregReplace.
 *
 * @group Zend_Filter
 */
class PregReplaceTest extends \PHPUnit_Framework_TestCase
{

    /**
     *
     * @var PregReplaceFilter
     */
    protected $filter;

    public function setUp()
    {
        $this->filter = new PregReplaceFilter();
    }

    public function testDetectsPcreUnicodeSupport()
    {
        $enabled = (@preg_match('/\pL/u', 'a')) ? true : false;
        $this->assertEquals($enabled, PregReplaceFilter::hasPcreUnicodeSupport());
    }

    public function testPassingPatternToConstructorSetsPattern()
    {
        $pattern = '#^controller/(?P<action>[a-z_-]+)#';
        $filter = new PregReplaceFilter($pattern);
        $this->assertEquals($pattern, $filter->getPattern());
    }

    public function testPassingReplacementToConstructorSetsReplacement()
    {
        $replace = 'foo/bar';
        $filter = new PregReplaceFilter(null, $replace);
        $this->assertEquals($replace, $filter->getReplacement());
    }

    public function testPatternIsNullByDefault()
    {
        $this->assertNull($this->filter->getPattern());
    }

    public function testPatternAccessorsWork()
    {
        $pattern = '#^controller/(?P<action>[a-z_-]+)#';
        $this->filter->setPattern($pattern);
        $this->assertEquals($pattern, $this->filter->getPattern());
    }

    public function testReplacementIsEmptyByDefault()
    {
        $replacement = $this->filter->getReplacement();
        $this->assertTrue(empty($replacement));
    }

    public function testReplacementAccessorsWork()
    {
        $replacement = 'foo/bar';
        $this->filter->setReplacement($replacement);
        $this->assertEquals($replacement, $this->filter->getReplacement());
    }

    public function testFilterPerformsRegexReplacement()
    {
        $filter = $this->filter;
        $filter->setPattern('#^controller/(?P<action>[a-z_-]+)#')->setReplacement('foo/bar');

        $string = 'controller/action';
        $filtered = $filter($string);
        $this->assertNotEquals($string, $filtered);
        $this->assertEquals('foo/bar', $filtered);
    }

    public function testFilterPerformsRegexReplacementWithArray()
    {
        $filter = $this->filter;
        $filter->setPattern('#^controller/(?P<action>[a-z_-]+)#')->setReplacement('foo/bar');

        $input = array(
            'controller/action',
            'This should stay the same'
        );

        $filtered = $filter($input);
        $this->assertNotEquals($input, $filtered);
        $this->assertEquals(array(
            'foo/bar',
            'This should stay the same'
        ), $filtered);
    }

    public function testFilterThrowsExceptionWhenNoMatchPatternPresent()
    {
        $filter = $this->filter;
        $string = 'controller/action';
        $filter->setReplacement('foo/bar');
        $this->setExpectedException('Zend\Filter\Exception\RuntimeException', 'does not have a valid pattern set');
        $filtered = $filter($string);
    }

    public function testPassingPatternWithExecModifierRaisesException()
    {
        $filter = new PregReplaceFilter();
        $this->setExpectedException('Zend\Filter\Exception\InvalidArgumentException', '"e" pattern modifier');
        $filter->setPattern('/foo/e');
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
        $filter = $this->filter;
        $filter->setPattern('#^controller/(?P<action>[a-z_-]+)#')->setReplacement('foo/bar');

        $this->assertEquals($input, $filter->filter($input));
    }
}
