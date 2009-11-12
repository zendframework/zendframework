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
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

// Call Zend_Dojo_View_Helper_VerticalSliderTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Dojo_View_Helper_VerticalSliderTest::main");
}

require_once dirname(__FILE__) . '/../../../../TestHelper.php';

/** Zend_Dojo_View_Helper_VerticalSlider */
require_once 'Zend/Dojo/View/Helper/VerticalSlider.php';

/** Zend_View */
require_once 'Zend/View.php';

/** Zend_Registry */
require_once 'Zend/Registry.php';

/** Zend_Dojo_View_Helper_Dojo */
require_once 'Zend/Dojo/View/Helper/Dojo.php';

/**
 * Test class for Zend_Dojo_View_Helper_VerticalSlider.
 *
 * @category   Zend
 * @package    Zend_Dojo
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Dojo
 * @group      Zend_Dojo_View
 */
class Zend_Dojo_View_Helper_VerticalSliderTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_Dojo_View_Helper_VerticalSliderTest");
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
        $this->helper = new Zend_Dojo_View_Helper_VerticalSlider();
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
        return $this->helper->verticalSlider(
            'elementId',
            '',
            array(
                'minimum'        => -10,
                'maximum'        => 10,
                'discreteValues' => 11,
                'topDecoration' => array(
                    'labels' => array(
                        ' ',
                        '20%',
                        '40%',
                        '60%',
                        '80%',
                        ' ',
                    ),
                    'attribs' => array(
                        'container' => array(
                            'style' => 'height:1.2em; font-size=75%;color:gray;',
                        ),
                        'labels' => array(
                            'style' => 'height:1em; font-size=75%;color:gray;',
                        ),
                    ),
                    'dijit' => 'VerticalRuleLabels',
                ),
                'bottomDecoration' => array(
                    'labels' => array(
                        '0%',
                        '50%',
                        '100%',
                    ),
                    'attribs' => array(
                        'labels' => array(
                            'style' => 'height:1em; font-size=75%;color:gray;',
                        ),
                    ),
                ),
                'leftDecoration' => array(
                    'labels' => array(
                        ' ',
                        '20%',
                        '40%',
                        '60%',
                        '80%',
                        ' ',
                    ),
                    'attribs' => array(
                        'container' => array(
                            'style' => 'height:1.2em; font-size=75%;color:gray;',
                        ),
                        'labels' => array(
                            'style' => 'height:1em; font-size=75%;color:gray;',
                        ),
                    ),
                    'dijit' => 'VerticalRuleLabels',
                ),
                'rightDecoration' => array(
                    'labels' => array(
                        '0%',
                        '50%',
                        '100%',
                    ),
                    'attribs' => array(
                        'labels' => array(
                            'style' => 'height:1em; font-size=75%;color:gray;',
                        ),
                    ),
                ),
            ),
            array()
        );
    }

    public function testShouldAllowDeclarativeDijitCreation()
    {
        $html = $this->getElement();
        $this->assertRegexp('/<div[^>]*(dojoType="dijit.form.VerticalSlider")/', $html, $html);
    }

    public function testShouldAllowProgrammaticDijitCreation()
    {
        Zend_Dojo_View_Helper_Dojo::setUseProgrammatic();
        $html = $this->getElement();
        $this->assertNotRegexp('/<div[^>]*(dojoType="dijit.form.VerticalSlider")/', $html);
        $this->assertNotNull($this->view->dojo()->getDijit('elementId-slider'));
    }

    public function testShouldCreateOnChangeAttributeByDefault()
    {
        $html = $this->getElement();
        $this->assertContains('onChange="dojo.byId(\'elementId\').value = arguments[0];"', $html, $html);
    }

    public function testShouldCreateHiddenElementWithValue()
    {
        $html = $this->getElement();
        if (!preg_match('/(<input[^>]*(type="hidden")[^>]*>)/', $html, $m)) {
            $this->fail('No hidden element found');
        }
        $this->assertContains('id="elementId"', $m[1]);
        $this->assertContains('value="', $m[1]);
    }

    public function testShouldCreateLeftAndRightDecorationsWhenRequested()
    {
        $html = $this->getElement();
        $this->assertRegexp('/<div[^>]*(dojoType="dijit.form.VerticalRule")/', $html, $html);
        $this->assertRegexp('/<ol[^>]*(dojoType="dijit.form.VerticalRuleLabels")/', $html, $html);
        $this->assertContains('leftDecoration', $html);
        $this->assertContains('rightDecoration', $html);
    }

    public function testShouldIgnoreTopAndBottomDecorationsWhenPassed()
    {
        $html = $this->getElement();
        $this->assertNotContains('topDecoration', $html);
        $this->assertNotContains('bottomDecoration', $html);
    }
}

// Call Zend_Dojo_View_Helper_VerticalSliderTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Dojo_View_Helper_VerticalSliderTest::main") {
    Zend_Dojo_View_Helper_VerticalSliderTest::main();
}
