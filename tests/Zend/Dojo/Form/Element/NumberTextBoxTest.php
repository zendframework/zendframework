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
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id:$
 */

// Call Zend_Dojo_Form_Element_NumberTextBoxTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Dojo_Form_Element_NumberTextBoxTest::main");
}

require_once dirname(__FILE__) . '/../../../../TestHelper.php';

/** Zend_Dojo_Form_Element_NumberTextBox */
require_once 'Zend/Dojo/Form/Element/NumberTextBox.php';

/** Zend_View */
require_once 'Zend/View.php';

/** Zend_Registry */
require_once 'Zend/Registry.php';

/** Zend_Dojo_View_Helper_Dojo */
require_once 'Zend/Dojo/View/Helper/Dojo.php';

/**
 * Test class for Zend_Dojo_Form_Element_Dijit.
 *
 * @package    Zend_Dojo
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Dojo_Form_Element_NumberTextBoxTest extends PHPUnit_Framework_TestCase 
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_Dojo_Form_Element_NumberTextBoxTest");
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
        require_once 'Zend/View.php';
        $view = new Zend_View();
        $view->addHelperPath('Zend/Dojo/View/Helper/', 'Zend_Dojo_View_Helper');
        return $view;
    }

    public function getElement()
    {
        $element = new Zend_Dojo_Form_Element_NumberTextBox(
            'foo',
            array(
                'value' => 'some text',
                'label' => 'NumberTextBox',
                'class' => 'someclass',
                'style' => 'width: 100px;',
            )
        );
        return $element;
    }

    public function testLocaleAccessorsShouldProxyToConstraints()
    {
        $this->assertNull($this->element->getLocale());
        $this->assertNull($this->element->getConstraint('locale'));
        $this->element->setLocale('en-US');
        $this->assertEquals('en-US', $this->element->getLocale());
        $this->assertTrue($this->element->hasConstraint('locale'));
        $this->assertEquals('en-US', $this->element->dijitParams['constraints']['locale']);
    }

    public function testPatternAccessorsShouldProxyToConstraints()
    {
        $this->assertNull($this->element->getPattern());
        $this->assertFalse($this->element->hasConstraint('pattern'));
        $this->element->setPattern('###0.#####');
        $this->assertEquals('###0.#####', $this->element->getPattern());
        $this->assertTrue($this->element->hasConstraint('pattern'));
        $this->assertEquals('###0.#####', $this->element->dijitParams['constraints']['pattern']);
    }

    public function testTypeAccessorsShouldProxyToConstraints()
    {
        $this->assertNull($this->element->getType());
        $this->assertFalse($this->element->hasConstraint('type'));
        $this->element->setType('percent');
        $this->assertEquals('percent', $this->element->getType());
        $this->assertTrue($this->element->hasConstraint('type'));
        $this->assertEquals('percent', $this->element->dijitParams['constraints']['type']);
    }

    /**
     * @expectedException Zend_Form_Element_Exception
     */
    public function testTypeMutatorShouldThrowExceptionWithInvalidType()
    {
        $this->element->setType('foobar');
    }

    public function testPlacesAccessorsShouldProxyToConstraints()
    {
        $this->assertNull($this->element->getPlaces());
        $this->assertFalse($this->element->hasConstraint('places'));
        $this->element->setPlaces(3);
        $this->assertEquals(3, $this->element->getPlaces());
        $this->assertTrue($this->element->hasConstraint('places'));
        $this->assertEquals(3, $this->element->dijitParams['constraints']['places']);
    }

    public function testStrictAccessorsShouldProxyToConstraints()
    {
        $this->assertFalse($this->element->getStrict());
        $this->assertFalse($this->element->hasConstraint('strict'));
        $this->element->setStrict(true);
        $this->assertTrue($this->element->getStrict());
        $this->assertTrue($this->element->hasConstraint('strict'));
        $this->assertEquals('true', $this->element->dijitParams['constraints']['strict']);
    }

    public function testShouldRenderNumberTextBoxDijit()
    {
        $html = $this->element->render();
        $this->assertContains('dojoType="dijit.form.NumberTextBox"', $html);
    }
}

// Call Zend_Dojo_Form_Element_NumberTextBoxTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Dojo_Form_Element_NumberTextBoxTest::main") {
    Zend_Dojo_Form_Element_NumberTextBoxTest::main();
}
