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
 * @package    Zend_Dojo
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

// Call Zend_Dojo_Form_Element_DateTextBoxTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Dojo_Form_Element_DateTextBoxTest::main");
}


/** Zend_Dojo_Form_Element_DateTextBox */

/** Zend_View */

/** Zend_Registry */

/** Zend_Dojo_View_Helper_Dojo */

/**
 * Test class for Zend_Dojo_Form_Element_DateTextBox.
 *
 * @category   Zend
 * @package    Zend_Dojo
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Dojo
 * @group      Zend_Dojo_Form
 */
class Zend_Dojo_Form_Element_DateTextBoxTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_Dojo_Form_Element_DateTextBoxTest");
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
        Zend_Registry::_unsetInstance();
        Zend_Dojo_View_Helper_Dojo::setUseDeclarative();

        $this->view    = $this->getView();
        $this->element = $this->getElement();
        $this->element->setView($this->view);
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
        $view->addHelperPath('Zend/Dojo/View/Helper/', 'Zend_Dojo_View_Helper');
        return $view;
    }

    public function getElement()
    {
        $element = new Zend_Dojo_Form_Element_DateTextBox(
            'foo',
            array(
                'value' => 'some text',
                'label' => 'DateTextBox',
                'class' => 'someclass',
                'style' => 'width: 100px;',
            )
        );
        return $element;
    }

    public function testAmPmAccessorsShouldProxyToConstraints()
    {
        $this->assertFalse($this->element->getAmPm());
        $constraints = $this->element->getConstraints();
        $this->assertFalse(array_key_exists('am,pm', $constraints));
        $this->element->setAmPm(true);
        $this->assertTrue($this->element->getAmPm());
        $constraints = $this->element->getConstraints();
        $this->assertTrue(array_key_exists('am,pm', $constraints));
        $this->assertEquals('true', $this->element->dijitParams['constraints']['am,pm']);
    }

    public function testStrictAccessorsShouldProxyToConstraints()
    {
        $this->assertFalse($this->element->getStrict());
        $constraints = $this->element->getConstraints();
        $this->assertFalse(array_key_exists('strict', $constraints));
        $this->element->setStrict(true);
        $this->assertTrue($this->element->getStrict());
        $constraints = $this->element->getConstraints();
        $this->assertTrue(array_key_exists('strict', $constraints));
        $this->assertEquals('true', $this->element->dijitParams['constraints']['strict']);
    }

    public function testLocaleAccessorsShouldProxyToConstraints()
    {
        $this->assertNull($this->element->getLocale());
        $constraints = $this->element->getConstraints();
        $this->assertFalse(array_key_exists('locale', $constraints));
        $this->element->setLocale('en-US');
        $this->assertEquals('en-US', $this->element->getLocale());
        $constraints = $this->element->getConstraints();
        $this->assertEquals('en-US', $this->element->dijitParams['constraints']['locale']);
    }

    public function testFormatLengthAccessorsShouldProxyToConstraints()
    {
        $this->assertNull($this->element->getFormatLength());
        $constraints = $this->element->getConstraints();
        $this->assertFalse(array_key_exists('formatLength', $constraints));
        $this->element->setFormatLength('long');
        $this->assertEquals('long', $this->element->getFormatLength());
        $constraints = $this->element->getConstraints();
        $this->assertEquals('long', $this->element->dijitParams['constraints']['formatLength']);
    }

    /**
     * @expectedException Zend_Form_Element_Exception
     */
    public function testFormatLengthMutatorShouldThrowExceptionWithInvalidFormatLength()
    {
        $this->element->setFormatLength('foobar');
    }

    public function testSelectorAccessorsShouldProxyToConstraints()
    {
        $this->assertNull($this->element->getSelector());
        $constraints = $this->element->getConstraints();
        $this->assertFalse(array_key_exists('selector', $constraints));
        $this->element->setSelector('time');
        $this->assertEquals('time', $this->element->getSelector());
        $constraints = $this->element->getConstraints();
        $this->assertEquals('time', $this->element->dijitParams['constraints']['selector']);
    }

    /**
     * @expectedException Zend_Form_Element_Exception
     */
    public function testSelectorMutatorShouldThrowExceptionWithInvalidSelector()
    {
        $this->element->setSelector('foobar');
    }

    public function testDatePatternAccessorsShouldProxyToConstraints()
    {
        $this->assertNull($this->element->getDatePattern());
        $this->assertFalse($this->element->hasConstraint('datePattern'));
        $this->element->setDatePattern('EEE, MMM d, Y');
        $this->assertEquals('EEE, MMM d, Y', $this->element->getDatePattern());
        $this->assertEquals('EEE, MMM d, Y', $this->element->dijitParams['constraints']['datePattern']);
    }

    public function testShouldRenderDateTextBoxDijit()
    {
        $html = $this->element->render();
        $this->assertContains('dojoType="dijit.form.DateTextBox"', $html);
    }
}

// Call Zend_Dojo_Form_Element_DateTextBoxTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Dojo_Form_Element_DateTextBoxTest::main") {
    Zend_Dojo_Form_Element_DateTextBoxTest::main();
}
