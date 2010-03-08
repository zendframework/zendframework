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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

// Call Zend_Form_Decorator_FieldsetTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Form_Decorator_FieldsetTest::main");
}




/**
 * Test class for Zend_Form_Decorator_Fieldset
 *
 * @category   Zend
 * @package    Zend_Form
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Form
 */
class Zend_Form_Decorator_FieldsetTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {

        $suite  = new PHPUnit_Framework_TestSuite("Zend_Form_Decorator_FieldsetTest");
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
        $this->decorator = new Zend_Form_Decorator_Fieldset();
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

    public function testPlacementInitiallyNull()
    {
        $this->assertNull($this->decorator->getPlacement());
    }

    public function testRenderReturnsOriginalContentWhenNoViewPresentInElement()
    {
        $element = new Zend_Form_Element('foo');
        $this->decorator->setElement($element);
        $content = 'test content';
        $this->assertSame($content, $this->decorator->render($content));
    }

    public function testLegendInitiallyNull()
    {
        $this->assertNull($this->decorator->getLegend());
    }

    public function testUsesLegendOptionWhenSetAndNoLegendInElement()
    {
        $this->testLegendInitiallyNull();
        $element = new Zend_Form_Element('foo');
        $this->decorator->setElement($element)
                        ->setOption('legend', 'this is a legend');
        $this->assertEquals('this is a legend', $this->decorator->getLegend());
    }

    public function testUsesElementLegendWhenPresent()
    {
        $this->testLegendInitiallyNull();
        $element = new Zend_Form();
        $element->setLegend('this is a legend');
        $this->decorator->setElement($element);
        $this->assertEquals('this is a legend', $this->decorator->getLegend());
    }

    public function testCanSetLegend()
    {
        $this->testLegendInitiallyNull();
        $this->decorator->setLegend('this is a legend');
        $this->assertEquals('this is a legend', $this->decorator->getLegend());
    }

    /**
     * @see ZF-2981
     */
    public function testActionAndMethodAttributesShouldNotBePresentInFieldsetTag()
    {
        $form = new Zend_Form();
        $form->setAction('/foo/bar')
             ->setMethod('post')
             ->setView($this->getView());
        $this->decorator->setElement($form);
        $test = $this->decorator->render('content');
        $this->assertContains('<fieldset', $test, $test);
        $this->assertNotContains('action="', $test);
        $this->assertNotContains('method="', $test);
    }

    /**#@+
     * @see ZF-3731
     */
    public function testIdShouldBePrefixedWithFieldset()
    {
        $form = new Zend_Form();
        $form->setAction('/foo/bar')
             ->setMethod('post')
             ->setName('foobar')
             ->setView($this->getView());
        $this->decorator->setElement($form);
        $test = $this->decorator->render('content');
        $this->assertContains('id="fieldset-foobar"', $test);
    }

    public function testElementWithNoIdShouldNotCreateFieldsetId()
    {
        $form = new Zend_Form();
        $form->setAction('/foo/bar')
             ->setMethod('post')
             ->setView($this->getView());
        $this->decorator->setElement($form);
        $test = $this->decorator->render('content');
        $this->assertNotContains('id="', $test);
    }
    /**#@-*/

    /**
     * @see ZF-3728
     */
    public function testEnctypeAttributeShouldNotBePresentInFieldsetTag()
    {
        $form = new Zend_Form();
        $form->setAction('/foo/bar')
             ->setMethod('post')
             ->setAttrib('enctype', 'dojo/method')
             ->setView($this->getView());
        $this->decorator->setElement($form);
        $test = $this->decorator->render('content');
        $this->assertContains('<fieldset', $test, $test);
        $this->assertNotContains('enctype="', $test);
    }

    /**
     * @see ZF-3499
     */
    public function testHelperAttributeShouldNotBePresentInFieldsetTag()
    {
        $form = new Zend_Form();
        $form->setAction('/foo/bar')
             ->setMethod('post')
             ->setAttrib('helper', 'form')
             ->setView($this->getView());
        $this->decorator->setElement($form);
        $test = $this->decorator->render('content');
        $this->assertContains('<fieldset', $test, $test);
        $this->assertNotContains('helper="', $test);
    }
}

// Call Zend_Form_Decorator_FieldsetTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Form_Decorator_FieldsetTest::main") {
    Zend_Form_Decorator_FieldsetTest::main();
}
