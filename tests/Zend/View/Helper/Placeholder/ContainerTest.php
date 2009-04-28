<?php
// Call Zend_View_Helper_Placeholder_Container_AbstractTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_View_Helper_Placeholder_ContainerTest::main");
}

require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/TestHelper.php';
require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

/** Zend_View_Helper_Placeholder_Container */
require_once 'Zend/View/Helper/Placeholder/Container.php';

/**
 * Test class for Zend_View_Helper_Placeholder_Container.
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 */
class Zend_View_Helper_Placeholder_ContainerTest extends PHPUnit_Framework_TestCase 
{
    /**
     * @var Zend_View_Helper_Placeholder_Container
     */
    public $container;

    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("Zend_View_Helper_Placeholder_ContainerTest");
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
        $this->container = new Zend_View_Helper_Placeholder_Container(array());
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->container);
    }

    /**
     * @return void
     */
    public function testSetSetsASingleValue()
    {
        $this->container['foo'] = 'bar';
        $this->container['bar'] = 'baz';
        $this->assertEquals('bar', $this->container['foo']);
        $this->assertEquals('baz', $this->container['bar']);

        $this->container->set('foo');
        $this->assertEquals(1, count($this->container));
        $this->assertEquals('foo', $this->container[0]);
    }

    /**
     * @return void
     */
    public function testGetValueReturnsScalarWhenOneElementRegistered()
    {
        $this->container->set('foo');
        $this->assertEquals('foo', $this->container->getValue());
    }

    /**
     * @return void
     */
    public function testGetValueReturnsArrayWhenMultipleValuesPresent()
    {
        $this->container['foo'] = 'bar';
        $this->container['bar'] = 'baz';
        $expected = array('foo' => 'bar', 'bar' => 'baz');
        $return   = $this->container->getValue();
        $this->assertEquals($expected, $return);
    }

    /**
     * @return void
     */
    public function testPrefixAccesorsWork()
    {
        $this->assertEquals('', $this->container->getPrefix());
        $this->container->setPrefix('<ul><li>');
        $this->assertEquals('<ul><li>', $this->container->getPrefix());
    }

    /**
     * @return void
     */
    public function testSetPrefixImplementsFluentInterface()
    {
        $result = $this->container->setPrefix('<ul><li>');
        $this->assertSame($this->container, $result);
    }

    /**
     * @return void
     */
    public function testPostfixAccesorsWork()
    {
        $this->assertEquals('', $this->container->getPostfix());
        $this->container->setPostfix('</li></ul>');
        $this->assertEquals('</li></ul>', $this->container->getPostfix());
    }

    /**
     * @return void
     */
    public function testSetPostfixImplementsFluentInterface()
    {
        $result = $this->container->setPostfix('</li></ul>');
        $this->assertSame($this->container, $result);
    }

    /**
     * @return void
     */
    public function testSeparatorAccesorsWork()
    {
        $this->assertEquals('', $this->container->getSeparator());
        $this->container->setSeparator('</li><li>');
        $this->assertEquals('</li><li>', $this->container->getSeparator());
    }

    /**
     * @return void
     */
    public function testSetSeparatorImplementsFluentInterface()
    {
        $result = $this->container->setSeparator('</li><li>');
        $this->assertSame($this->container, $result);
    }

    /**
     * @return void
     */
    public function testIndentAccesorsWork()
    {
        $this->assertEquals('', $this->container->getIndent());
        $this->container->setIndent('    ');
        $this->assertEquals('    ', $this->container->getIndent());
        $this->container->setIndent(5);
        $this->assertEquals('     ', $this->container->getIndent());
    }

    /**
     * @return void
     */
    public function testSetIndentImplementsFluentInterface()
    {
        $result = $this->container->setIndent('    ');
        $this->assertSame($this->container, $result);
    }
    
    /**
     * @return void
     */
    public function testCapturingToPlaceholderStoresContent()
    {
        $this->container->captureStart();
        echo 'This is content intended for capture';
        $this->container->captureEnd();

        $value = $this->container->getValue();
        $this->assertContains('This is content intended for capture', $value);
    }

    /**
     * @return void
     */
    public function testCapturingToPlaceholderAppendsContent()
    {
        $this->container[] = 'foo';
        $originalCount = count($this->container);

        $this->container->captureStart();
        echo 'This is content intended for capture';
        $this->container->captureEnd();

        $this->assertEquals($originalCount + 1, count($this->container));

        $value     = $this->container->getValue();
        $keys      = array_keys($value);
        $lastIndex = array_pop($keys);
        $this->assertEquals('foo', $value[$lastIndex - 1]);
        $this->assertContains('This is content intended for capture', $value[$lastIndex]);
    }

    /**
     * @return void
     */
    public function testCapturingToPlaceholderUsingPrependPrependsContent()
    {
        $this->container[] = 'foo';
        $originalCount = count($this->container);

        $this->container->captureStart('PREPEND');
        echo 'This is content intended for capture';
        $this->container->captureEnd();

        $this->assertEquals($originalCount + 1, count($this->container));

        $value     = $this->container->getValue();
        $keys      = array_keys($value);
        $lastIndex = array_pop($keys);
        $this->assertEquals('foo', $value[$lastIndex]);
        $this->assertContains('This is content intended for capture', $value[$lastIndex - 1]);
    }

    /**
     * @return void
     */
    public function testCapturingToPlaceholderUsingSetOverwritesContent()
    {
        $this->container[] = 'foo';
        $this->container->captureStart('SET');
        echo 'This is content intended for capture';
        $this->container->captureEnd();

        $this->assertEquals(1, count($this->container));

        $value = $this->container->getValue();
        $this->assertContains('This is content intended for capture', $value);
    }

    /**
     * @return void
     */
    public function testCapturingToPlaceholderKeyUsingSetCapturesContent()
    {
        $this->container->captureStart('SET', 'key');
        echo 'This is content intended for capture';
        $this->container->captureEnd();

        $this->assertEquals(1, count($this->container));
        $this->assertTrue(isset($this->container['key']));
        $value = $this->container['key'];
        $this->assertContains('This is content intended for capture', $value);
    }

    /**
     * @return void
     */
    public function testCapturingToPlaceholderKeyUsingSetReplacesContentAtKey()
    {
        $this->container['key'] = 'Foobar';
        $this->container->captureStart('SET', 'key');
        echo 'This is content intended for capture';
        $this->container->captureEnd();

        $this->assertEquals(1, count($this->container));
        $this->assertTrue(isset($this->container['key']));
        $value = $this->container['key'];
        $this->assertContains('This is content intended for capture', $value);
    }

    /**
     * @return void
     */
    public function testCapturingToPlaceholderKeyUsingAppendAppendsContentAtKey()
    {
        $this->container['key'] = 'Foobar ';
        $this->container->captureStart('APPEND', 'key');
        echo 'This is content intended for capture';
        $this->container->captureEnd();

        $this->assertEquals(1, count($this->container));
        $this->assertTrue(isset($this->container['key']));
        $value = $this->container['key'];
        $this->assertContains('Foobar This is content intended for capture', $value);
    }

    /**
     * @return void
     */
    public function testNestedCapturesThrowsException()
    {
        $this->container[] = 'foo';
        $caught = false;
        try {
            $this->container->captureStart('SET');
                $this->container->captureStart('SET');
                $this->container->captureEnd();
            $this->container->captureEnd();
        } catch (Exception $e) {
            $this->container->captureEnd();
            $caught = true;
        }

        $this->assertTrue($caught, 'Nested captures should throw exceptions');
    }

    /**
     * @return void
     */
    public function testToStringWithNoModifiersAndSingleValueReturnsValue()
    {
        $this->container->set('foo');
        $value = $this->container->toString();
        $this->assertEquals($this->container->getValue(), $value);
    }

    /**
     * @return void
     */
    public function testToStringWithModifiersAndSingleValueReturnsFormattedValue()
    {
        $this->container->set('foo');
        $this->container->setPrefix('<li>')
                        ->setPostfix('</li>');
        $value = $this->container->toString();
        $this->assertEquals('<li>foo</li>', $value);
    }

    /**
     * @return void
     */
    public function testToStringWithNoModifiersAndCollectionReturnsImplodedString()
    {
        $this->container[] = 'foo';
        $this->container[] = 'bar';
        $this->container[] = 'baz';
        $value = $this->container->toString();
        $this->assertEquals('foobarbaz', $value);
    }

    /**
     * @return void
     */
    public function testToStringWithModifiersAndCollectionReturnsFormattedString()
    {
        $this->container[] = 'foo';
        $this->container[] = 'bar';
        $this->container[] = 'baz';
        $this->container->setPrefix('<ul><li>')
                        ->setSeparator('</li><li>')
                        ->setPostfix('</li></ul>');
        $value = $this->container->toString();
        $this->assertEquals('<ul><li>foo</li><li>bar</li><li>baz</li></ul>', $value);
    }

    /**
     * @return void
     */
    public function testToStringWithModifiersAndCollectionReturnsFormattedStringWithIndentation()
    {
        $this->container[] = 'foo';
        $this->container[] = 'bar';
        $this->container[] = 'baz';
        $this->container->setPrefix('<ul><li>')
                        ->setSeparator('</li>' . PHP_EOL . '<li>')
                        ->setPostfix('</li></ul>')
                        ->setIndent('    ');
        $value = $this->container->toString();
        $expectedValue = '    <ul><li>foo</li>' . PHP_EOL . '    <li>bar</li>' . PHP_EOL . '    <li>baz</li></ul>';
        $this->assertEquals($expectedValue, $value);
    }
    
    /**
     * @return void
     */
    public function test__toStringProxiesToToString()
    {
        $this->container[] = 'foo';
        $this->container[] = 'bar';
        $this->container[] = 'baz';
        $this->container->setPrefix('<ul><li>')
                        ->setSeparator('</li><li>')
                        ->setPostfix('</li></ul>');
        $value = $this->container->__toString();
        $this->assertEquals('<ul><li>foo</li><li>bar</li><li>baz</li></ul>', $value);
    }

    /**
     * @return void
     */
    public function testPrependPushesValueToTopOfContainer()
    {
        $this->container['foo'] = 'bar';
        $this->container->prepend('baz');

        $expected = array('baz', 'foo' => 'bar');
        $array = $this->container->getArrayCopy();
        $this->assertSame($expected, $array);
    }

    public function testIndentationIsHonored()
    {
        $this->container->setIndent(4)
                        ->setPrefix("<ul>\n    <li>")
                        ->setSeparator("</li>\n    <li>")
                        ->setPostfix("</li>\n</ul>");
        $this->container->append('foo');
        $this->container->append('bar');
        $this->container->append('baz');
        $string = $this->container->toString();

        $lis = substr_count($string, "\n        <li>");
        $this->assertEquals(3, $lis);
        $this->assertTrue((strstr($string, "    <ul>\n")) ? true : false, $string);
        $this->assertTrue((strstr($string, "\n    </ul>")) ? true : false);
    }
}

// Call Zend_View_Helper_Placeholder_ContainerTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_View_Helper_Placeholder_ContainerTest::main") {
    Zend_View_Helper_Placeholder_ContainerTest::main();
}
