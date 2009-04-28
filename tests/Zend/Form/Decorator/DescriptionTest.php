<?php
// Call Zend_Form_Decorator_DescriptionTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Form_Decorator_DescriptionTest::main");
}

require_once dirname(__FILE__) . '/../../../TestHelper.php';
require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'Zend/Form/Decorator/Description.php';

require_once 'Zend/Form/Element.php';
require_once 'Zend/View.php';

/**
 * Test class for Zend_Form_Decorator_Description
 */
class Zend_Form_Decorator_DescriptionTest extends PHPUnit_Framework_TestCase 
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("Zend_Form_Decorator_DescriptionTest");
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
        if (isset($this->html)) {
            unset($this->html);
        }

        $this->element = new Zend_Form_Element('foo');
        $this->element->setDescription('a test description')
                      ->setView($this->getView());
        $this->decorator = new Zend_Form_Decorator_Description();
        $this->decorator->setElement($this->element);
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

    public function testRendersDescriptionInParagraphTagsByDefault()
    {
        $html = $this->decorator->render('');
        $this->assertContains('<p', $html, $html);
        $this->assertContains('</p>', $html);
        $this->assertContains($this->element->getDescription(), $html);
        $this->html = $html;
    }

    public function testParagraphTagsContainHintClassByDefault()
    {
        $this->testRendersDescriptionInParagraphTagsByDefault();
        $this->assertRegexp('/<p[^>]*?class="hint"/', $this->html);
    }

    public function testCanSpecifyAlternateTag()
    {
        $this->decorator->setTag('quote');
        $html = $this->decorator->render('');
        $this->assertContains('<quote', $html, $html);
        $this->assertContains('</quote>', $html);
        $this->assertContains($this->element->getDescription(), $html);
        $this->html = $html;
    }

    public function testCanSpecifyAlternateTagViaOption()
    {
        $this->decorator->setOption('tag', 'quote');
        $html = $this->decorator->render('');
        $this->assertContains('<quote', $html, $html);
        $this->assertContains('</quote>', $html);
        $this->assertContains($this->element->getDescription(), $html);
        $this->html = $html;
    }

    public function testAlternateTagContainsHintClass()
    {
        $this->testCanSpecifyAlternateTag();
        $this->assertRegexp('/<quote[^>]*?class="hint"/', $this->html);
    }

    public function testCanSpecifyAlternateClass()
    {
        $this->decorator->setOption('class', 'haha');
        $html = $this->decorator->render('');
        $this->assertRegexp('/<p[^>]*?class="haha"/', $html);
    }

    public function testRenderingEscapesDescriptionByDefault()
    {
        $description = '<span>some spanned text</span>';
        $this->element->setDescription($description);
        $html = $this->decorator->render('');
        $this->assertNotContains($description, $html);
        $this->assertContains('&lt;', $html);
        $this->assertContains('&gt;', $html);
        $this->assertContains('some spanned text', $html);
    }

    public function testCanDisableEscapingDescription()
    {
        $description = '<span>some spanned text</span>';
        $this->element->setDescription($description);
        $this->decorator->setEscape(false);
        $html = $this->decorator->render('');
        $this->assertContains($description, $html);
        $this->assertNotContains('&lt;', $html);
        $this->assertNotContains('&gt;', $html);
    }

    public function testCanSetEscapeFlagViaOption()
    {
        $description = '<span>some spanned text</span>';
        $this->element->setDescription($description);
        $this->decorator->setOption('escape', false);
        $html = $this->decorator->render('');
        $this->assertContains($description, $html);
        $this->assertNotContains('&lt;', $html);
        $this->assertNotContains('&gt;', $html);
    }

    public function testDescriptionIsTranslatedWhenTranslationAvailable()
    {
        require_once 'Zend/Translate.php';
        $translations = array('description' => 'This is the description');
        $translate = new Zend_Translate('array', $translations);
        $this->element->setDescription('description')
                      ->setTranslator($translate);
        $html = $this->decorator->render('');
        $this->assertContains($translations['description'], $html);
    }
}

// Call Zend_Form_Decorator_DescriptionTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Form_Decorator_DescriptionTest::main") {
    Zend_Form_Decorator_DescriptionTest::main();
}
