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

// Call Zend_Dojo_View_Helper_NumberSpinnerTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Dojo_View_Helper_NumberSpinnerTest::main");
}

require_once dirname(__FILE__) . '/../../../../TestHelper.php';

/** Zend_Dojo_View_Helper_NumberSpinner */
require_once 'Zend/Dojo/View/Helper/NumberSpinner.php';

/** Zend_View */
require_once 'Zend/View.php';

/** Zend_Registry */
require_once 'Zend/Registry.php';

/** Zend_Dojo_View_Helper_Dojo */
require_once 'Zend/Dojo/View/Helper/Dojo.php';

/**
 * Test class for Zend_Dojo_View_Helper_NumberSpinner.
 *
 * @category   Zend
 * @package    Zend_Dojo
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Dojo
 * @group      Zend_Dojo_View
 */
class Zend_Dojo_View_Helper_NumberSpinnerTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_Dojo_View_Helper_NumberSpinnerTest");
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

        $this->view   = $this->getView();
        $this->helper = new Zend_Dojo_View_Helper_NumberSpinner();
        $this->helper->setView($this->view);
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
        return $this->helper->numberSpinner(
            'elementId',
            '5',
            array(
                'smallDelta' => '10',
                'min' => 9,
                'max' => 1550,
                'places' => 0,
                'required'    => true,
            ),
            array()
        );
    }

    public function testShouldAllowDeclarativeDijitCreation()
    {
        $html = $this->getElement();
        $this->assertRegexp('/<input[^>]*(dojoType="dijit.form.NumberSpinner")/', $html, $html);
    }

    public function testShouldAllowProgrammaticDijitCreation()
    {
        Zend_Dojo_View_Helper_Dojo::setUseProgrammatic();
        $html = $this->getElement();
        $this->assertNotRegexp('/<input[^>]*(dojoType="dijit.form.NumberSpinner")/', $html);
        $this->assertNotNull($this->view->dojo()->getDijit('elementId'));
    }

    public function testShouldCreateTextInput()
    {
        $html = $this->getElement();
        $this->assertRegexp('/<input[^>]*(type="text")/', $html);
    }

    public function testShouldJsonEncodeConstraints()
    {
        $html = $this->getElement();
        if (!preg_match('/constraints="(.*?)(" )/', $html, $m)) {
            $this->fail('Did not serialize constraints');
        }
        $constraints = str_replace("'", '"', $m[1]);
        $constraints = Zend_Json::decode($constraints);
        $this->assertTrue(is_array($constraints), var_export($m[1], 1));
        $this->assertTrue(array_key_exists('min', $constraints));
        $this->assertTrue(array_key_exists('max', $constraints));
        $this->assertTrue(array_key_exists('places', $constraints));
    }

    public function testInvalidConstraintsShouldBeStrippedPriorToRendering()
    {
        $html = $this->helper->numberSpinner(
            'foo',
            5,
            array (
                'constraints' => 'bogus',
            )
        );
        $this->assertNotContains('constraints="', $html);
    }
}

// Call Zend_Dojo_View_Helper_NumberSpinnerTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Dojo_View_Helper_NumberSpinnerTest::main") {
    Zend_Dojo_View_Helper_NumberSpinnerTest::main();
}
