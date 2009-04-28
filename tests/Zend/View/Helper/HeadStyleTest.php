<?php
// Call Zend_View_Helper_HeadStyleTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_View_Helper_HeadStyleTest::main");
}

require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/TestHelper.php';
require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

/** Zend_View_Helper_HeadStyle */
require_once 'Zend/View/Helper/HeadStyle.php';

/** Zend_View_Helper_Placeholder_Registry */
require_once 'Zend/View/Helper/Placeholder/Registry.php';

/** Zend_Registry */
require_once 'Zend/Registry.php';

/**
 * Test class for Zend_View_Helper_HeadStyle.
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 */
class Zend_View_Helper_HeadStyleTest extends PHPUnit_Framework_TestCase
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
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("Zend_View_Helper_HeadStyleTest");
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
        $regKey = Zend_View_Helper_Placeholder_Registry::REGISTRY_KEY;
        if (Zend_Registry::isRegistered($regKey)) {
            $registry = Zend_Registry::getInstance();
            unset($registry[$regKey]);
        }
        $this->basePath = dirname(__FILE__) . '/_files/modules';
        $this->helper = new Zend_View_Helper_HeadStyle();
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
        $registry = Zend_View_Helper_Placeholder_Registry::getRegistry();
        if ($registry->containerExists('Zend_View_Helper_HeadStyle')) {
            $registry->deleteContainer('Zend_View_Helper_HeadStyle');
        }
        $this->assertFalse($registry->containerExists('Zend_View_Helper_HeadStyle'));
        $helper = new Zend_View_Helper_HeadStyle();
        $this->assertTrue($registry->containerExists('Zend_View_Helper_HeadStyle'));
    }

    public function testHeadStyleReturnsObjectInstance()
    {
        $placeholder = $this->helper->headStyle();
        $this->assertTrue($placeholder instanceof Zend_View_Helper_HeadStyle);
    }

    public function testAppendPrependAndSetThrowExceptionsWhenNonStyleValueProvided()
    {
        try {
            $this->helper->append('foo');
            $this->fail('Non-style value should not append');
        } catch (Zend_View_Exception $e) { }
        try {
            $this->helper->offsetSet(5, 'foo');
            $this->fail('Non-style value should not offsetSet');
        } catch (Zend_View_Exception $e) { }
        try {
            $this->helper->prepend('foo');
            $this->fail('Non-style value should not prepend');
        } catch (Zend_View_Exception $e) { }
        try {
            $this->helper->set('foo');
            $this->fail('Non-style value should not set');
        } catch (Zend_View_Exception $e) { }
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

            $this->assertTrue($item instanceof stdClass);
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

            $this->assertTrue($item instanceof stdClass);
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

        $this->assertTrue($item instanceof stdClass);
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

    public function testHeadStyleProxiesProperly()
    {
        $style1 = 'a {}';
        $style2 = 'a {}' . PHP_EOL . 'h1 {}';
        $style3 = 'a {}' . PHP_EOL . 'h2 {}';

        $this->helper->headStyle($style1, 'SET')
                     ->headStyle($style2, 'PREPEND')
                     ->headStyle($style3, 'APPEND');
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

        $this->helper->headStyle($style1, 'SET')
                     ->headStyle($style2, 'PREPEND')
                     ->headStyle($style3, 'APPEND');
        $html = $this->helper->toString();
        $doc  = new DOMDocument;
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
        } catch (Zend_View_Exception $e) { }
    }

    public function testTooFewArgumentsRaisesException()
    {
        try {
            $this->helper->appendStyle();
            $this->fail('Too few arguments should raise exception');
        } catch (Zend_View_Exception $e) { }
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
        $this->helper->headStyle()->captureStart();
        echo "Captured text";
        $this->helper->headStyle()->captureEnd();

        try {
            $this->helper->headStyle()->captureStart();
        } catch (Zend_View_Exception $e) {
            $this->fail('Serial capturing should work');
        }
        $this->helper->headStyle()->captureEnd();
    }

    public function testNestedCapturingFails()
    {
        $this->helper->headStyle()->captureStart();
        echo "Captured text";
            try {
                $this->helper->headStyle()->captureStart();
                $this->helper->headStyle()->captureEnd();
                $this->fail('Nested capturing should fail');
            } catch (Zend_View_Exception $e) {
                $this->helper->headStyle()->captureEnd();
                $this->assertContains('Cannot nest', $e->getMessage());
            }
        $this->helper->headStyle()->captureEnd();
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

    public function testMediaAttributeAsCommaSeperatedString()
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

        $this->helper->offsetSetStyle(10, '
a {
    display: none;
}');
        $this->helper->offsetSetStyle(5, '
h1 {
    font-weight: bold
}');


        $test = $this->helper->toString();

        $expected = '<style type="text/css" media="screen">
<!--

h1 {
    font-weight: bold
}
-->
</style>
<style type="text/css" media="screen">
<!--

a {
    display: none;
}
-->
</style>';

        $this->assertEquals($expected, $test);
    }
}

// Call Zend_View_Helper_HeadStyleTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_View_Helper_HeadStyleTest::main") {
    Zend_View_Helper_HeadStyleTest::main();
}
