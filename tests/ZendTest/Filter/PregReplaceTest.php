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

use Zend\Filter\PregReplace as PregReplaceFilter;

/**
 * Test class for Zend_Filter_PregReplace.
 *
 * @category   Zend
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @group      Zend_Filter
 */
class PregReplaceTest extends \PHPUnit_Framework_TestCase
{
    /**
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
        $filter  = new PregReplaceFilter($pattern);
        $this->assertEquals($pattern, $filter->getPattern());
    }

    public function testPassingReplacementToConstructorSetsReplacement()
    {
        $replace = 'foo/bar';
        $filter  = new PregReplaceFilter(null, $replace);
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
        $string = 'controller/action';
        $filter->setPattern('#^controller/(?P<action>[a-z_-]+)#')
               ->setReplacement('foo/bar');
        $filtered = $filter($string);
        $this->assertNotEquals($string, $filtered);
        $this->assertEquals('foo/bar', $filtered);
    }

    public function testFilterThrowsExceptionWhenNoMatchPatternPresent()
    {
        $filter = $this->filter;
        $string = 'controller/action';
        $filter->setReplacement('foo/bar');
        $this->setExpectedException('Zend\Filter\Exception\RuntimeException',
                                    'does not have a valid pattern set');
        $filtered = $filter($string);
    }

    public function testPassingPatternWithExecModifierRaisesException()
    {
        $filter = new PregReplaceFilter();
        $this->setExpectedException('Zend\Filter\Exception\InvalidArgumentException', '"e" pattern modifier');
        $filter->setPattern('/foo/e');
    }
}
