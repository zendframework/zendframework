<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Dom
 */

namespace ZendTest\Dom;

use Zend\Dom\Css2Xpath;

/**
 * Test class for Css2Xpath.
 *
 * @category   Zend
 * @package    Zend_Dom
 * @subpackage UnitTests
 * @group      Zend_Dom
 */
class Css2XpathTest extends \PHPUnit_Framework_TestCase
{
    public function testTransformShouldBeCalledStatically()
    {
        Css2Xpath::transform('');
    }

    public function testTransformShouldReturnStringByDefault()
    {
        $test = Css2Xpath::transform('');
        $this->assertTrue(is_string($test));
    }

    /**
     * @group ZF-6281
     */
    public function testTransformShouldReturnMultiplePathsWhenExpressionContainsCommas()
    {
        $test = Css2Xpath::transform('#foo, #bar');
        $this->assertTrue(is_string($test));
        $this->assertContains('|', $test);
        $this->assertEquals(2, count(explode('|', $test)));
    }

    public function testTransformShouldRecognizeHashSymbolAsId()
    {
        $test = Css2Xpath::transform('#foo');
        $this->assertEquals("//*[@id='foo']", $test);
    }

    public function testTransformShouldRecognizeDotSymbolAsClass()
    {
        $test = Css2Xpath::transform('.foo');
        $this->assertEquals("//*[contains(concat(' ', normalize-space(@class), ' '), ' foo ')]", $test);
    }

    public function testTransformShouldAssumeSpacesToIndicateRelativeXpathQueries()
    {
        $test = Css2Xpath::transform('div#foo .bar');
        $this->assertContains('|', $test);
        $expected = array(
            "//div[@id='foo']//*[contains(concat(' ', normalize-space(@class), ' '), ' bar ')]",
            "//div[@id='foo'][contains(concat(' ', normalize-space(@class), ' '), ' bar ')]",
        );
        foreach ($expected as $path) {
            $this->assertContains($path, $test);
        }
    }

    public function testTransformShouldWriteChildSelectorsAsAbsoluteXpathRelations()
    {
        $test = Css2Xpath::transform('div#foo>span');
        $this->assertEquals("//div[@id='foo']/span", $test);
    }

    /**
     * @group ZF-6281
     */
    public function testMultipleComplexCssSpecificationShouldTransformToExpectedXpath()
    {
        $test = Css2Xpath::transform('div#foo span.bar, #bar li.baz a');
        $this->assertTrue(is_string($test));
        $this->assertContains('|', $test);
        $actual   = explode('|', $test);
        $expected = array(
            "//div[@id='foo']//span[contains(concat(' ', normalize-space(@class), ' '), ' bar ')]",
            "//*[@id='bar']//li[contains(concat(' ', normalize-space(@class), ' '), ' baz ')]//a",
        );
        $this->assertEquals(count($expected), count($actual));
        foreach ($actual as $path) {
            $this->assertContains($path, $expected);
        }
    }

    public function testClassNotationWithoutSpecifiedTagShouldResultInMultipleQueries()
    {
        $test = Css2Xpath::transform('div.foo .bar a .baz span');
        $this->assertContains('|', $test);
        $segments = array(
            "//div[contains(concat(' ', normalize-space(@class), ' '), ' foo ')]//*[contains(concat(' ', normalize-space(@class), ' '), ' bar ')]//a//*[contains(concat(' ', normalize-space(@class), ' '), ' baz ')]//span",
            "//div[contains(concat(' ', normalize-space(@class), ' '), ' foo ')]//*[contains(concat(' ', normalize-space(@class), ' '), ' bar ')]//a[contains(concat(' ', normalize-space(@class), ' '), ' baz ')]//span",
            "//div[contains(concat(' ', normalize-space(@class), ' '), ' foo ')][contains(concat(' ', normalize-space(@class), ' '), ' bar ')]//a//*[contains(concat(' ', normalize-space(@class), ' '), ' baz ')]//span",
            "//div[contains(concat(' ', normalize-space(@class), ' '), ' foo ')][contains(concat(' ', normalize-space(@class), ' '), ' bar ')]//a[contains(concat(' ', normalize-space(@class), ' '), ' baz ')]//span",
        );
        foreach ($segments as $xpath) {
            $this->assertContains($xpath, $test);
        }
    }

    public function testShouldAllowEqualitySelectionOfArbitraryAttributes()
    {
        $test = Css2Xpath::transform('div[foo="bar"]');
        $this->assertEquals("//div[@foo='bar']", $test);
    }

    public function testShouldCastAttributeNamesToLowerCase()
    {
        $test = Css2Xpath::transform('div[dojoType="bar"]');
        $this->assertEquals("//div[@dojotype='bar']", $test);
    }

    public function testShouldAllowContentSubSelectionOfArbitraryAttributes()
    {
        $test = Css2Xpath::transform('div[foo~="bar"]');
        $this->assertEquals("//div[contains(concat(' ', normalize-space(@foo), ' '), ' bar ')]", $test);
    }

    public function testShouldAllowContentMatchingOfArbitraryAttributes()
    {
        $test = Css2Xpath::transform('div[foo*="bar"]');
        $this->assertEquals("//div[contains(@foo, 'bar')]", $test);
    }

    /**
     * @group ZF-4010
     */
    public function testShouldAllowMatchingOfAttributeValues()
    {
        $test = Css2Xpath::transform('tag#id @attribute');
        $this->assertEquals("//tag[@id='id']//@attribute", $test);
    }

    /**
     * @group ZF-8006
     */
    public function testShouldAllowWhitespaceInDescendentSelectorExpressions()
    {
        $test = Css2Xpath::transform('child > leaf');
        $this->assertEquals("//child/leaf", $test);
    }

    /**
     * @group ZF-9764
     */
    public function testIdSelectorWithAttribute()
    {
        $test = Css2Xpath::transform('#id[attribute="value"]');
        $this->assertEquals("//*[@id='id'][@attribute='value']", $test);
    }

    /**
     * @group ZF-9764
     */
    public function testIdSelectorWithLeadingAsterix()
    {
        $test = Css2Xpath::transform('*#id');
        $this->assertEquals("//*[@id='id']", $test);
    }
}
