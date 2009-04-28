<?php
// Call Zend_Form_Decorator_HtmlTagTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Form_Decorator_HtmlTagTest::main");
}

require_once dirname(__FILE__) . '/../../../TestHelper.php';
require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'Zend/Form/Decorator/HtmlTag.php';

require_once 'Zend/Form/Element.php';
require_once 'Zend/View.php';

/**
 * Test class for Zend_Form_Decorator_HtmlTag
 */
class Zend_Form_Decorator_HtmlTagTest extends PHPUnit_Framework_TestCase 
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("Zend_Form_Decorator_HtmlTagTest");
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
        $this->decorator = new Zend_Form_Decorator_HtmlTag();
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

    public function getView()
    {
        $view = new Zend_View();
        $view->addHelperPath(dirname(__FILE__) . '/../../../../library/Zend/View/Helper');
        return $view;
    }

    public function testNormalizeTagStripsNonAlphanumericCharactersAndLowersCase()
    {
        $tag = 'ab1-cd0EFG';
        $received = $this->decorator->normalizeTag($tag);
        $this->assertEquals('ab1cd0efg', $received);
    }

    public function testRendersOptionsAsHtmlAttribsByDefault()
    {
        $element = new Zend_Form_Element('foo');
        $options = array('tag' => 'div', 'class' => 'foobar', 'id' => 'foo');
        $this->decorator->setElement($element)
                        ->setOptions($options);
        $html = $this->decorator->render('');
        foreach ($options as $key => $value) {
            if ('tag' == $key) {
                $this->assertContains('<' . $value, $html);
                $this->assertContains('</' . $value . '>', $html);
            } else {
                $this->assertContains($key . '="' . $value . '"', $html);
            }
        }
    }

    public function testDoesNotRenderAttribsWhenNoAttribsOptionSet()
    {
        $element = new Zend_Form_Element('foo');
        $options = array('tag' => 'div', 'class' => 'foobar', 'id' => 'foo', 'noAttribs' => true);
        $this->decorator->setElement($element)
                        ->setOptions($options);
        $html = $this->decorator->render('');
        foreach ($options as $key => $value) {
            if ('tag' == $key) {
                $this->assertContains('<' . $value, $html);
                $this->assertContains('</' . $value . '>', $html);
            } else {
                $this->assertNotContains($key . '="' . (string) $value . '"', $html);
            }
        }
    }

    public function testCanRenderOnlyOpeningTag()
    {
        $element = new Zend_Form_Element('foo');
        $options = array('tag' => 'div', 'class' => 'foobar', 'id' => 'foo', 'openOnly' => true);
        $this->decorator->setElement($element)
                        ->setOptions($options);
        $html = $this->decorator->render('');
        foreach ($options as $key => $value) {
            if ('tag' == $key) {
                $this->assertContains('<' . $value, $html);
                $this->assertNotContains('</' . $value . '>', $html);
            } elseif ('openOnly' == $key) {
                $this->assertNotContains($key, $html);
            } else {
                $this->assertContains($key . '="' . (string) $value . '"', $html);
            }
        }
    }

    public function testCanRenderOnlyClosingTag()
    {
        $element = new Zend_Form_Element('foo');
        $options = array('tag' => 'div', 'class' => 'foobar', 'id' => 'foo', 'closeOnly' => true);
        $this->decorator->setElement($element)
                        ->setOptions($options);
        $html = $this->decorator->render('');
        foreach ($options as $key => $value) {
            if ('tag' == $key) {
                $this->assertNotContains('<' . $value, $html);
                $this->assertContains('</' . $value . '>', $html);
            } else {
                $this->assertNotContains($key . '="' . (string) $value . '"', $html);
            }
        }
    }

    public function testArrayAttributesAreRenderedAsSpaceSeparatedLists()
    {
        $element = new Zend_Form_Element('foo');
        $options = array('tag' => 'div', 'class' => array('foobar', 'bazbat'), 'id' => 'foo');
        $this->decorator->setElement($element)
                        ->setOptions($options);
        $html = $this->decorator->render('');
        $this->assertContains('class="foobar bazbat"', $html);
    }

    public function testAppendPlacementWithCloseOnlyRendersClosingTagFollowingContent()
    {
        $options = array(
            'closeOnly' => true,
            'tag'       => 'div',
            'placement' => 'append'
        );
        $this->decorator->setOptions($options);
        $html = $this->decorator->render('content');
        $this->assertRegexp('#(content).*?(</div>)#', $html, $html);
    }

    public function testAppendPlacementWithOpenOnlyRendersOpeningTagFollowingContent()
    {
        $options = array(
            'openOnly'  => true,
            'tag'       => 'div',
            'placement' => 'append'
        );
        $this->decorator->setOptions($options);
        $html = $this->decorator->render('content');
        $this->assertRegexp('#(content).*?(<div>)#', $html, $html);
    }

    public function testPrependPlacementWithCloseOnlyRendersClosingTagBeforeContent()
    {
        $options = array(
            'closeOnly' => true,
            'tag'       => 'div',
            'placement' => 'prepend'
        );
        $this->decorator->setOptions($options);
        $html = $this->decorator->render('content');
        $this->assertRegexp('#(</div>).*?(content)#', $html, $html);
    }

    public function testPrependPlacementWithOpenOnlyRendersOpeningTagBeforeContent()
    {
        $options = array(
            'openOnly'  => true,
            'tag'       => 'div',
            'placement' => 'prepend'
        );
        $this->decorator->setOptions($options);
        $html = $this->decorator->render('content');
        $this->assertRegexp('#(<div>).*?(content)#', $html, $html);
    }

    public function testTagIsInitiallyDiv()
    {
        $this->assertEquals('div', $this->decorator->getTag());
    }

    public function testCanSetTag()
    {
        $this->testTagIsInitiallyDiv();
        $this->decorator->setTag('dl');
        $this->assertEquals('dl', $this->decorator->getTag());
    }

    public function testCanSetTagViaOption()
    {
        $this->decorator->setOption('tag', 'dl');
        $this->assertEquals('dl', $this->decorator->getTag());
    }
}

// Call Zend_Form_Decorator_HtmlTagTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Form_Decorator_HtmlTagTest::main") {
    Zend_Form_Decorator_HtmlTagTest::main();
}
