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

// Call Zend_Dojo_Form_Element_DijitTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Dojo_Form_Element_DijitTest::main");
}


/** Zend_Dojo_Form_Element_TextBox */

/** Zend_View */

/** Zend_Registry */

/** Zend_Dojo_View_Helper_Dojo */

/**
 * Test class for Zend_Dojo_Form_Element_Dijit.
 *
 * @category   Zend
 * @package    Zend_Dojo
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Dojo
 * @group      Zend_Dojo_Form
 */
class Zend_Dojo_Form_Element_DijitTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_Dojo_Form_Element_DijitTest");
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
        $element = new Zend_Dojo_Form_Element_TextBox(
            'foo',
            array(
                'value' => 'some text',
                'label' => 'TextBox',
                'trim'  => true,
                'propercase' => true,
                'class' => 'someclass',
                'style' => 'width: 100px;',
            )
        );
        return $element;
    }

    public function testShouldAbstractDijitParameterManipulation()
    {
        $params = $this->element->getDijitParams();
        $this->assertSame($this->element->dijitParams, $params);

        $this->assertFalse($this->element->hasDijitParam('foo'));
        $this->element->setDijitParam('foo', 'bar');
        $this->assertTrue($this->element->hasDijitParam('foo'));
        $this->element->removeDijitParam('foo');
        $this->assertFalse($this->element->hasDijitParam('foo'));
        $this->element->clearDijitParams();
        $params = $this->element->getDijitParams();
        $this->assertTrue(empty($params));
        $this->assertTrue(empty($this->element->dijitParams));
    }

    public function testDijitElementDecoratorShouldBeEnabledByDefault()
    {
        $decorator = $this->element->getDecorator('DijitElement');
        $this->assertTrue($decorator instanceof Zend_Dojo_Form_Decorator_DijitElement, get_class($decorator));
    }

    /**
     * @group ZF-5264
     */
    public function testDescriptionDecoratorShouldBeEnabledByDefault()
    {
        $decorator = $this->element->getDecorator('Description');
        $this->assertTrue($decorator instanceof Zend_Form_Decorator_Description, get_class($decorator));
    }

    public function testRenderingShouldCreateDijit()
    {
        $html = $this->element->render();
        $this->assertContains('dojoType="dijit.form.TextBox"', $html);
    }

    public function testElementShouldDojoEnableViewObject()
    {
        $this->element->setView(new Zend_View);
        $view = $this->element->getView();
        $loader = $view->getPluginLoader('helper');
        $paths = $loader->getPaths('Zend_Dojo_View_Helper');
        $this->assertTrue(is_array($paths));
    }
}

// Call Zend_Dojo_Form_Element_DijitTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Dojo_Form_Element_DijitTest::main") {
    Zend_Dojo_Form_Element_DijitTest::main();
}
