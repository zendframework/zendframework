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

use Zend\Filter\PregReplace as PregReplaceFilter;

/**
 * Test class for Zend_Filter_PregReplace.
 *
 * @category   Zend
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Filter
 */
class PregReplaceTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        $this->filter = new PregReplaceFilter();
    }

    public function testPassingMatchPatternToConstructorSetsMatchPattern()
    {
        $pattern = '#^controller/(?P<action>[a-z_-]+)#';
        $filter  = new PregReplaceFilter($pattern);
        $this->assertEquals($pattern, $filter->getMatchPattern());
    }

    public function testPassingReplacementToConstructorSetsReplacement()
    {
        $replace = 'foo/bar';
        $filter  = new PregReplaceFilter(null, $replace);
        $this->assertEquals($replace, $filter->getReplacement());
    }

    public function testIsUnicodeSupportEnabledReturnsSaneValue()
    {
        $enabled = (@preg_match('/\pL/u', 'a')) ? true : false;
        $this->assertEquals($enabled, $this->filter->isUnicodeSupportEnabled());
    }

    public function testMatchPatternInitiallyNull()
    {
        $this->assertNull($this->filter->getMatchPattern());
    }

    public function testMatchPatternAccessorsWork()
    {
        $pattern = '#^controller/(?P<action>[a-z_-]+)#';
        $this->filter->setMatchPattern($pattern);
        $this->assertEquals($pattern, $this->filter->getMatchPattern());
    }

    public function testReplacementInitiallyEmpty()
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
        $filter->setMatchPattern('#^controller/(?P<action>[a-z_-]+)#')
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
        $this->setExpectedException('\Zend\Filter\Exception\RuntimeException', 'does not have a valid MatchPattern set');
        $filtered = $filter($string);
    }

    /**
      * @group ZF-9202
      */
    public function testExtendsPregReplace()
    {
        $startMatchPattern = '~(&gt;){3,}~i';
        $filter = new XPregReplace();
        $this->assertEquals($startMatchPattern, $filter->getMatchPattern());
    }
}

/**
 * @group ZF-9202
 */
class XPregReplace extends PregReplaceFilter
{
    protected $_matchPattern = '~(&gt;){3,}~i';
}
