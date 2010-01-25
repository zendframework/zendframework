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

// Call Zend_Dojo_Form_Decorator_DijitElementTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Dojo_Form_Decorator_DijitElementTest::main");
}

require_once dirname(__FILE__) . '/../../../../TestHelper.php';

/** Zend_Dojo_Form_Decorator_DijitElement */
require_once 'Zend/Dojo/Form/Decorator/DijitElement.php';

/** Zend_Dojo_Form_Element_TextBox */
require_once 'Zend/Dojo/Form/Element/TextBox.php';

/** Zend_View */
require_once 'Zend/View.php';

/** Zend_Registry */
require_once 'Zend/Registry.php';

/** Zend_Dojo_View_Helper_Dojo */
require_once 'Zend/Dojo/View/Helper/Dojo.php';

/**
 * Test class for Zend_Dojo_Form_Decorator_DijitElement.
 *
 * @category   Zend
 * @package    Zend_Dojo
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Dojo
 * @group      Zend_Dojo_Form
 */
class Zend_Dojo_Form_Decorator_DijitElementTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_Dojo_Form_Decorator_DijitElementTest");
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

        $this->errors = array();
        $this->view   = $this->getView();
        $this->decorator = new Zend_Dojo_Form_Decorator_DijitElement();
        $this->element   = $this->getElement();
        $this->element->setView($this->view);
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
        require_once 'Zend/View.php';
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

    /**
     * Handle an error (for testing notices)
     *
     * @param  int $errno
     * @param  string $errstr
     * @return void
     */
    public function handleError($errno, $errstr)
    {
        $this->errors[] = $errstr;
    }

    public function testRetrievingElementAttributesShouldOmitDijitParams()
    {
        $attribs = $this->decorator->getElementAttribs();
        $this->assertTrue(is_array($attribs));
        $this->assertFalse(array_key_exists('dijitParams', $attribs));
        $this->assertFalse(array_key_exists('propercase', $attribs));
        $this->assertFalse(array_key_exists('trim', $attribs));
    }

    public function testRetrievingDijitParamsShouldOmitNormalAttributes()
    {
        $params = $this->decorator->getDijitParams();
        $this->assertTrue(is_array($params));
        $this->assertFalse(array_key_exists('class', $params));
        $this->assertFalse(array_key_exists('style', $params));
        $this->assertFalse(array_key_exists('value', $params));
        $this->assertFalse(array_key_exists('label', $params));
    }

    public function testRenderingShouldEnableDojo()
    {
        $html = $this->decorator->render('');
        $this->assertTrue($this->view->dojo()->isEnabled());
    }

    public function testRenderingShouldTriggerErrorWhenDuplicateDijitDetected()
    {
        $this->view->dojo()->addDijit('foo', array('dojoType' => 'dijit.form.TextBox'));

        $handler = set_error_handler(array($this, 'handleError'));
        $html = $this->decorator->render('');
        restore_error_handler();
        $this->assertFalse(empty($this->errors), var_export($this->errors, 1));
        $found = false;
        foreach ($this->errors as $error) {
            if (strstr($error, 'Duplicate')) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
    }

    public function testShouldAllowAddingAndRetrievingIndividualDijitParams()
    {
        $this->assertNull($this->decorator->getDijitParam('bogus'));
        $this->decorator->setDijitParam('bogus', 'value');
        $this->assertEquals('value', $this->decorator->getDijitParam('bogus'));
    }

    /**
     * @expectedException Zend_Form_Decorator_Exception
     */
    public function testRenderingShouldThrowExceptionWhenNoViewObjectRegistered()
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
        $this->decorator->setElement($element);
        $html = $this->decorator->render('');
    }

    public function testRenderingShouldCreateDijit()
    {
        $html = $this->decorator->render('');
        $this->assertContains('dojoType="dijit.form.TextBox"', $html);
    }

    public function testRenderingShouldSetRequiredDijitParamWhenElementIsRequired()
    {
        $this->element->setRequired(true);
        $html = $this->decorator->render('');
        $this->assertContains('required="', $html);
    }

    /**
     * @group ZF-7660
     */
    public function testRenderingShouldRenderRequiredFlagAlways()
    {
        $this->element->setRequired(false);
        $html = $this->decorator->render('');
        $this->assertContains('required="false"', $html, $html);
    }
}

// Call Zend_Dojo_Form_Decorator_DijitElementTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Dojo_Form_Decorator_DijitElementTest::main") {
    Zend_Dojo_Form_Decorator_DijitElementTest::main();
}
