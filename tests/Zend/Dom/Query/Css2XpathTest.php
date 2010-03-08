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
 * @package    Zend_Dom
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

// Call Zend_Dom_Query_Css2XpathTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Dom_Query_Css2XpathTest::main");
}


/** Zend_Dom_Query_Css2Xpath */

/**
 * Test class for Zend_Dom_Query_Css2Xpath.
 *
 * @category   Zend
 * @package    Zend_Dom
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Dom
 */
class Zend_Dom_Query_Css2XpathTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_Dom_Query_Css2XpathTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    public function tearDown()
    {
    }

    public function testTransformShouldBeCalledStatically()
    {
        Zend_Dom_Query_Css2Xpath::transform('');
    }

    public function testTransformShouldReturnStringByDefault()
    {
        $test = Zend_Dom_Query_Css2Xpath::transform('');
        $this->assertTrue(is_string($test));
    }

    /**
     * @group ZF-6281
     */
    public function testTransformShouldReturnMultiplePathsWhenExpressionContainsCommas()
    {
        $test = Zend_Dom_Query_Css2Xpath::transform('#foo, #bar');
        $this->assertTrue(is_string($test));
        $this->assertContains('|', $test);
        $this->assertEquals(2, count(explode('|', $test)));
    }

    public function testTransformShouldRecognizeHashSymbolAsId()
    {
        $test = Zend_Dom_Query_Css2Xpath::transform('#foo');
        $this->assertEquals("//*[@id='foo']", $test);
    }

    public function testTransformShouldRecognizeDotSymbolAsClass()
    {
        $test = Zend_Dom_Query_Css2Xpath::transform('.foo');
        $this->assertEquals("//*[contains(@class, ' foo ')]", $test);
    }

    public function testTransformShouldAssumeSpacesToIndicateRelativeXpathQueries()
    {
        $test = Zend_Dom_Query_Css2Xpath::transform('div#foo .bar');
        $this->assertContains('|', $test);
        $expected = array(
            "//div[@id='foo']//*[contains(@class, ' bar ')]",
            "//div[@id='foo'][contains(@class, ' bar ')]",
        );
        foreach ($expected as $path) {
            $this->assertContains($path, $test);
        }
    }

    public function testTransformShouldWriteChildSelectorsAsAbsoluteXpathRelations()
    {
        $test = Zend_Dom_Query_Css2Xpath::transform('div#foo>span');
        $this->assertEquals("//div[@id='foo']/span", $test);
    }

    /**
     * @group ZF-6281
     */
    public function testMultipleComplexCssSpecificationShouldTransformToExpectedXpath()
    {
        $test = Zend_Dom_Query_Css2Xpath::transform('div#foo span.bar, #bar li.baz a');
        $this->assertTrue(is_string($test));
        $this->assertContains('|', $test);
        $actual   = explode('|', $test);
        $expected = array(
            "//div[@id='foo']//span[contains(@class, ' bar ')]",
            "//*[@id='bar']//li[contains(@class, ' baz ')]//a",
        );
        $this->assertEquals(count($expected), count($actual));
        foreach ($actual as $path) {
            $this->assertContains($path, $expected);
        }
    }

    public function testClassNotationWithoutSpecifiedTagShouldResultInMultipleQueries()
    {
        $test = Zend_Dom_Query_Css2Xpath::transform('div.foo .bar a .baz span');
        $this->assertContains('|', $test);
        $segments = array(
            "//div[contains(@class, ' foo ')]//*[contains(@class, ' bar ')]//a//*[contains(@class, ' baz ')]//span",
            "//div[contains(@class, ' foo ')]//*[contains(@class, ' bar ')]//a[contains(@class, ' baz ')]//span",
            "//div[contains(@class, ' foo ')][contains(@class, ' bar ')]//a//*[contains(@class, ' baz ')]//span",
            "//div[contains(@class, ' foo ')][contains(@class, ' bar ')]//a[contains(@class, ' baz ')]//span",
        );
        foreach ($segments as $xpath) {
            $this->assertContains($xpath, $test);
        }
    }

    public function testShouldAllowEqualitySelectionOfArbitraryAttributes()
    {
        $test = Zend_Dom_Query_Css2Xpath::transform('div[foo="bar"]');
        $this->assertEquals("//div[@foo='bar']", $test);
    }

    public function testShouldCastAttributeNamesToLowerCase()
    {
        $test = Zend_Dom_Query_Css2Xpath::transform('div[dojoType="bar"]');
        $this->assertEquals("//div[@dojotype='bar']", $test);
    }

    public function testShouldAllowContentSubSelectionOfArbitraryAttributes()
    {
        $test = Zend_Dom_Query_Css2Xpath::transform('div[foo~="bar"]');
        $this->assertEquals("//div[contains(@foo, ' bar ')]", $test);
    }

    public function testShouldAllowContentMatchingOfArbitraryAttributes()
    {
        $test = Zend_Dom_Query_Css2Xpath::transform('div[foo*="bar"]');
        $this->assertEquals("//div[contains(@foo, 'bar')]", $test);
    }

    /**
     * @group ZF-4010
     */
    public function testShouldAllowMatchingOfAttributeValues()
    {
        $test = Zend_Dom_Query_Css2Xpath::transform('tag#id @attribute');
        $this->assertEquals("//tag[@id='id']//@attribute", $test);
    }

    /**
     * @group ZF-8006
     */
    public function testShouldAllowWhitespaceInDescendentSelectorExpressions()
    {
        $test = Zend_Dom_Query_Css2Xpath::transform('child > leaf');
        $this->assertEquals("//child/leaf", $test);
    }
}

// Call Zend_Dom_Query_Css2XpathTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Dom_Query_Css2XpathTest::main") {
    Zend_Dom_Query_Css2XpathTest::main();
}
