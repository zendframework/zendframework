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


// Call Zend_Filter_PregReplaceTest::main() if this source file is executed directly.
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Filter_PregReplaceTest::main');
}

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../TestHelper.php';

/**
 * @see Zend_Filter_PregReplace
 */
require_once 'Zend/Filter/PregReplace.php';


/**
 * Test class for Zend_Filter_PregReplace.
 *
 * @category   Zend
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Filter
 */
class Zend_Filter_PregReplaceTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite('Zend_Filter_PregReplaceTest');
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function setUp()
    {
        $this->filter = new Zend_Filter_PregReplace();
    }

    public function testPassingMatchPatternToConstructorSetsMatchPattern()
    {
        $pattern = '#^controller/(?P<action>[a-z_-]+)#';
        $filter  = new Zend_Filter_PregReplace($pattern);
        $this->assertEquals($pattern, $filter->getMatchPattern());
    }

    public function testPassingReplacementToConstructorSetsReplacement()
    {
        $replace = 'foo/bar';
        $filter  = new Zend_Filter_PregReplace(null, $replace);
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
        $string = 'controller/action';
        $this->filter->setMatchPattern('#^controller/(?P<action>[a-z_-]+)#')
             ->setReplacement('foo/bar');
        $filtered = $this->filter->filter($string);
        $this->assertNotEquals($string, $filtered);
        $this->assertEquals('foo/bar', $filtered);
    }

    public function testFilterThrowsExceptionWhenNoMatchPatternPresent()
    {
        $string = 'controller/action';
        $this->filter->setReplacement('foo/bar');
        try {
            $filtered = $this->filter->filter($string);
            $this->fail('Replacement should fail when no match pattern present');
        } catch (Exception $e) {
        }
    }
}

// Call Zend_Filter_PregReplaceTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == 'Zend_Filter_PregReplaceTest::main') {
    Zend_Filter_PregReplaceTest::main();
}
