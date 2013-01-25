<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_View
 */

namespace ZendTest\View\Helper;

use Zend\View\Helper\Placeholder\Registry;
use Zend\View\Helper;
use Zend\View;

/**
 * Test class for Zend_View_Helper_HeadStyle.
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
class HeadStyleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Zend_View_Helper_HeadStyle
     */
    public $helper;

    /**
     * @var string
     */
    public $basePath;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        Registry::unsetRegistry();
        $this->basePath = __DIR__ . '/_files/modules';
        $this->helper = new Helper\HeadStyle();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->helper);
    }

    public function testNamespaceRegisteredInPlaceholderRegistryAfterInstantiation()
    {
        $registry = Registry::getRegistry();
        if ($registry->containerExists('Zend_View_Helper_HeadStyle')) {
            $registry->deleteContainer('Zend_View_Helper_HeadStyle');
        }
        $this->assertFalse($registry->containerExists('Zend_View_Helper_HeadStyle'));
        $helper = new Helper\HeadStyle();
        $this->assertTrue($registry->containerExists('Zend_View_Helper_HeadStyle'));
    }

    public function testHeadStyleReturnsObjectInstance()
    {
        $placeholder = $this->helper->__invoke();
        $this->assertTrue($placeholder instanceof Helper\HeadStyle);
    }

    public function testAppendPrependAndSetThrowExceptionsWhenNonStyleValueProvided()
    {
        try {
            $this->helper->append('foo');
            $this->fail('Non-style value should not append');
        } catch (View\Exception\ExceptionInterface $e) { }
        try {
            $this->helper->offsetSet(5, 'foo');
            $this->fail('Non-style value should not offsetSet');
        } catch (View\Exception\ExceptionInterface $e) { }
        try {
            $this->helper->prepend('foo');
            $this->fail('Non-style value should not prepend');
        } catch (View\Exception\ExceptionInterface $e) { }
        try {
            $this->helper->set('foo');
            $this->fail('Non-style value should not set');
        } catch (View\Exception\ExceptionInterface $e) { }
    }

    public function testOverloadAppendStyleAppendsStyleToStack()
    {
        $string = 'a {}';
        for ($i = 0; $i < 3; ++$i) {
            $string .= PHP_EOL . 'a {}';
            $this->helper->appendStyle($string);
            $values = $this->helper->getArrayCopy();
            $this->assertEquals($i + 1, count($values));
            $item = $values[$i];

            $this->assertTrue($item instanceof \stdClass);
            $this->assertObjectHasAttribute('content', $item);
            $this->assertObjectHasAttribute('attributes', $item);
            $this->assertEquals($string, $item->content);
        }
    }

    public function testOverloadPrependStylePrependsStyleToStack()
    {
        $string = 'a {}';
        for ($i = 0; $i < 3; ++$i) {
            $string .= PHP_EOL . 'a {}';
            $this->helper->prependStyle($string);
            $values = $this->helper->getArrayCopy();
            $this->assertEquals($i + 1, count($values));
            $item = array_shift($values);

            $this->assertTrue($item instanceof \stdClass);
            $this->assertObjectHasAttribute('content', $item);
            $this->assertObjectHasAttribute('attributes', $item);
            $this->assertEquals($string, $item->content);
        }
    }

    public function testOverloadSetOversitesStack()
    {
        $string = 'a {}';
        for ($i = 0; $i < 3; ++$i) {
            $this->helper->appendStyle($string);
            $string .= PHP_EOL . 'a {}';
        }
        $this->helper->setStyle($string);
        $values = $this->helper->getArrayCopy();
        $this->assertEquals(1, count($values));
        $item = array_shift($values);

        $this->assertTrue($item instanceof \stdClass);
        $this->assertObjectHasAttribute('content', $item);
        $this->assertObjectHasAttribute('attributes', $item);
        $this->assertEquals($string, $item->content);
    }

    public function testCanBuildStyleTagsWithAttributes()
    {
        $this->helper->setStyle('a {}', array(
            'lang'  => 'us_en',
            'title' => 'foo',
            'media' => 'projection',
            'dir'   => 'rtol',
            'bogus' => 'unused'
        ));
        $value = $this->helper->getValue();

        $this->assertObjectHasAttribute('attributes', $value);
        $attributes = $value->attributes;

        $this->assertTrue(isset($attributes['lang']));
        $this->assertTrue(isset($attributes['title']));
        $this->assertTrue(isset($attributes['media']));
        $this->assertTrue(isset($attributes['dir']));
        $this->assertTrue(isset($attributes['bogus']));
        $this->assertEquals('us_en', $attributes['lang']);
        $this->assertEquals('foo', $attributes['title']);
        $this->assertEquals('projection', $attributes['media']);
        $this->assertEquals('rtol', $attributes['dir']);
        $this->assertEquals('unused', $attributes['bogus']);
    }

    public function testRenderedStyleTagsContainHtmlEscaping()
    {
        $this->helper->setStyle('a {}', array(
            'lang'  => 'us_en',
            'title' => 'foo',
            'media' => 'screen',
            'dir'   => 'rtol',
            'bogus' => 'unused'
        ));
        $value = $this->helper->toString();
        $this->assertContains('<!--' . PHP_EOL, $value);
        $this->assertContains(PHP_EOL . '-->', $value);
    }

    public function testRenderedStyleTagsContainsDefaultMedia()
    {
        $this->helper->setStyle('a {}', array(
        ));
        $value = $this->helper->toString();
        $this->assertRegexp('#<style [^>]*?media="screen"#', $value, $value);
    }

    /**
     * @group ZF-8056
     */
    public function testMediaAttributeCanHaveSpaceInCommaSeparatedString()
    {
        $this->helper->appendStyle('a { }', array('media' => 'screen, projection'));
        $string = $this->helper->toString();
        $this->assertContains('media="screen,projection"', $string);
    }

    public function testHeadStyleProxiesProperly()
    {
        $style1 = 'a {}';
        $style2 = 'a {}' . PHP_EOL . 'h1 {}';
        $style3 = 'a {}' . PHP_EOL . 'h2 {}';

        $this->helper->__invoke($style1, 'SET')
                     ->__invoke($style2, 'PREPEND')
                     ->__invoke($style3, 'APPEND');
        $this->assertEquals(3, count($this->helper));
        $values = $this->helper->getArrayCopy();
        $this->assertTrue((strstr($values[0]->content, $style2)) ? true : false);
        $this->assertTrue((strstr($values[1]->content, $style1)) ? true : false);
        $this->assertTrue((strstr($values[2]->content, $style3)) ? true : false);
    }

    public function testToStyleGeneratesValidHtml()
    {
        $style1 = 'a {}';
        $style2 = 'body {}' . PHP_EOL . 'h1 {}';
        $style3 = 'div {}' . PHP_EOL . 'li {}';

        $this->helper->__invoke($style1, 'SET')
                     ->__invoke($style2, 'PREPEND')
                     ->__invoke($style3, 'APPEND');
        $html = $this->helper->toString();
        $doc  = new \DOMDocument;
        $dom  = $doc->loadHtml($html);
        $this->assertTrue(($dom !== false));

        $styles = substr_count($html, '<style type="text/css"');
        $this->assertEquals(3, $styles);
        $styles = substr_count($html, '</style>');
        $this->assertEquals(3, $styles);
        $this->assertContains($style3, $html);
        $this->assertContains($style2, $html);
        $this->assertContains($style1, $html);
    }

    public function testCapturingCapturesToObject()
    {
        $this->helper->captureStart();
        echo 'foobar';
        $this->helper->captureEnd();
        $values = $this->helper->getArrayCopy();
        $this->assertEquals(1, count($values));
        $item = array_shift($values);
        $this->assertContains('foobar', $item->content);
    }

    public function testOverloadingOffsetSetWritesToSpecifiedIndex()
    {
        $this->helper->offsetSetStyle(100, 'foobar');
        $values = $this->helper->getArrayCopy();
        $this->assertEquals(1, count($values));
        $this->assertTrue(isset($values[100]));
        $item = $values[100];
        $this->assertContains('foobar', $item->content);
    }

    public function testInvalidMethodRaisesException()
    {
        try {
            $this->helper->bogusMethod();
            $this->fail('Invalid method should raise exception');
        } catch (View\Exception\ExceptionInterface $e) { }
    }

    public function testTooFewArgumentsRaisesException()
    {
        try {
            $this->helper->appendStyle();
            $this->fail('Too few arguments should raise exception');
        } catch (View\Exception\ExceptionInterface $e) { }
    }

    public function testIndentationIsHonored()
    {
        $this->helper->setIndent(4);
        $this->helper->appendStyle('
a {
    display: none;
}');
        $this->helper->appendStyle('
h1 {
    font-weight: bold
}');
        $string = $this->helper->toString();

        $scripts = substr_count($string, '    <style');
        $this->assertEquals(2, $scripts);
        $this->assertContains('    <!--', $string);
        $this->assertContains('    a {', $string);
        $this->assertContains('    h1 {', $string);
        $this->assertContains('        display', $string);
        $this->assertContains('        font-weight', $string);
        $this->assertContains('    }', $string);
    }

    public function testSerialCapturingWorks()
    {
        $this->helper->__invoke()->captureStart();
        echo "Captured text";
        $this->helper->__invoke()->captureEnd();

        $this->helper->__invoke()->captureStart();

        $this->helper->__invoke()->captureEnd();
    }

    public function testNestedCapturingFails()
    {
        $this->helper->__invoke()->captureStart();
        echo "Captured text";
            try {
                $this->helper->__invoke()->captureStart();
                $this->helper->__invoke()->captureEnd();
                $this->fail('Nested capturing should fail');
            } catch (View\Exception\ExceptionInterface $e) {
                $this->helper->__invoke()->captureEnd();
                $this->assertContains('Cannot nest', $e->getMessage());
            }
    }

    public function testMediaAttributeAsArray()
    {
        $this->helper->setIndent(4);
        $this->helper->appendStyle('
a {
    display: none;
}', array('media' => array('screen', 'projection')));
        $string = $this->helper->toString();

        $scripts = substr_count($string, '    <style');
        $this->assertEquals(1, $scripts);
        $this->assertContains('    <!--', $string);
        $this->assertContains('    a {', $string);
        $this->assertContains(' media="screen,projection"', $string);

    }

    public function testMediaAttributeAsCommaSeparatedString()
    {
        $this->helper->setIndent(4);
        $this->helper->appendStyle('
a {
    display: none;
}', array('media' => 'screen,projection'));
        $string = $this->helper->toString();

        $scripts = substr_count($string, '    <style');
        $this->assertEquals(1, $scripts);
        $this->assertContains('    <!--', $string);
        $this->assertContains('    a {', $string);
        $this->assertContains(' media="screen,projection"', $string);

    }

    public function testConditionalScript()
    {
        $this->helper->appendStyle('
a {
    display: none;
}', array('media' => 'screen,projection', 'conditional' => 'lt IE 7'));
        $test = $this->helper->toString();
        $this->assertContains('<!--[if lt IE 7]>', $test);
    }

    /**
     * @issue ZF-5435
     */
    public function testContainerMaintainsCorrectOrderOfItems()
    {

        $style1 = 'a {display: none;}';
        $this->helper->offsetSetStyle(10, $style1);

        $style2 = 'h1 {font-weight: bold}';
        $this->helper->offsetSetStyle(5, $style2);

        $test = $this->helper->toString();
        $expected = '<style type="text/css" media="screen">' . PHP_EOL
                  . '<!--' . PHP_EOL
                  . $style2 . PHP_EOL
                  . '-->' . PHP_EOL
                  . '</style>' . PHP_EOL
                  . '<style type="text/css" media="screen">' . PHP_EOL
                  . '<!--' . PHP_EOL
                  . $style1 . PHP_EOL
                  . '-->' . PHP_EOL
                  . '</style>';

        $this->assertEquals($expected, $test);
    }

    /**
     * @group ZF-9532
     */
    public function testRenderConditionalCommentsShouldNotContainHtmlEscaping()
    {
        $style = 'a{display:none;}';
        $this->helper->appendStyle($style, array(
            'conditional' => 'IE 8'
        ));
        $value = $this->helper->toString();

        $this->assertNotContains('<!--' . PHP_EOL, $value);
        $this->assertNotContains(PHP_EOL . '-->', $value);
    }
}
