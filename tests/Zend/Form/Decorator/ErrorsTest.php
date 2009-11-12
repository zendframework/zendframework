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
 * @package    Zend_Form
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

// Call Zend_Form_Decorator_ErrorsTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Form_Decorator_ErrorsTest::main");
}

require_once dirname(__FILE__) . '/../../../TestHelper.php';
require_once "PHPUnit/Framework/TestCase.php";
require_once "PHPUnit/Framework/TestSuite.php";

require_once 'Zend/Form/Decorator/Errors.php';

require_once 'Zend/Form/Element.php';
require_once 'Zend/View.php';

/**
 * Test class for Zend_Form_Decorator_Errors
 *
 * @category   Zend
 * @package    Zend_Form
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Form
 */
class Zend_Form_Decorator_ErrorsTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        require_once "PHPUnit/TextUI/TestRunner.php";

        $suite  = new PHPUnit_Framework_TestSuite("Zend_Form_Decorator_ErrorsTest");
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
        $this->decorator = new Zend_Form_Decorator_Errors();
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

    public function testRenderReturnsInitialContentIfNoViewPresentInElement()
    {
        $element = new Zend_Form_Element('foo');
        $this->decorator->setElement($element);
        $content = 'test content';
        $this->assertSame($content, $this->decorator->render($content));
    }

    public function getView()
    {
        $view = new Zend_View();
        $view->addHelperPath(dirname(__FILE__) . '/../../../../library/Zend/View/Helper');
        return $view;
    }

    public function setupElement()
    {
        $element = new Zend_Form_Element('foo');
        $element->addValidator('Alnum')
                ->addValidator('Alpha')
                ->setView($this->getView());
        $element->isValid('abc-123');
        $this->element = $element;
        $this->decorator->setElement($element);
    }

    public function testRenderRendersAllErrorMessages()
    {
        $this->setupElement();
        $content = 'test content';
        $test = $this->decorator->render($content);
        $this->assertContains($content, $test);
        foreach ($this->element->getMessages() as $message) {
            $this->assertContains($message, $test);
        }
    }

    public function testRenderAppendsMessagesToContentByDefault()
    {
        $this->setupElement();
        $content = 'test content';
        $test = $this->decorator->render($content);
        $this->assertRegexp('#' . $content . '.*?<ul#s', $test, $test);
    }

    public function testRenderPrependsMessagesToContentWhenRequested()
    {
        $this->decorator->setOptions(array('placement' => 'PREPEND'));
        $this->setupElement();
        $content = 'test content';
        $test = $this->decorator->render($content);
        $this->assertRegexp('#</ul>.*?' . $content . '#s', $test);
    }

    public function testRenderSeparatesContentAndErrorsWithPhpEolByDefault()
    {
        $this->setupElement();
        $content = 'test content';
        $test = $this->decorator->render($content);
        $this->assertContains($content . PHP_EOL . '<ul', $test);
    }

    public function testRenderSeparatesContentAndErrorsWithCustomSeparatorWhenRequested()
    {
        $this->decorator->setOptions(array('separator' => '<br />'));
        $this->setupElement();
        $content = 'test content';
        $test = $this->decorator->render($content);
        $this->assertContains($content . $this->decorator->getSeparator() . '<ul', $test, $test);
    }
}

// Call Zend_Form_Decorator_ErrorsTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Form_Decorator_ErrorsTest::main") {
    Zend_Form_Decorator_ErrorsTest::main();
}
