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

// Call Zend_Dojo_View_Helper_HorizontalSliderTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Dojo_View_Helper_HorizontalSliderTest::main");
}


/** Zend_Dojo_View_Helper_HorizontalSlider */

/** Zend_View */

/** Zend_Registry */

/** Zend_Dojo_Form */

/** Zend_Dojo_Form_SubForm */

/** Zend_Dojo_View_Helper_Dojo */

/**
 * Test class for Zend_Dojo_View_Helper_HorizontalSlider.
 *
 * @category   Zend
 * @package    Zend_Dojo
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Dojo
 * @group      Zend_Dojo_View
 */
class Zend_Dojo_View_Helper_HorizontalSliderTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_Dojo_View_Helper_HorizontalSliderTest");
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
        $this->helper = new Zend_Dojo_View_Helper_HorizontalSlider();
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
        $view = new Zend_View();
        $view->addHelperPath('Zend/Dojo/View/Helper/', 'Zend_Dojo_View_Helper');
        return $view;
    }

    public function getElement()
    {
        return $this->helper->horizontalSlider(
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
                    'container' => 'top',
                    'attribs' => array(
                        'container' => array(
                            'style' => 'height:1.2em; font-size=75%;color:gray;',
                        ),
                        'labels' => array(
                            'style' => 'height:1em; font-size=75%;color:gray;',
                        ),
                    ),
                    'dijit' => 'HorizontalRuleLabels',
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
        $this->assertRegexp('/<div[^>]*(dojoType="dijit.form.HorizontalSlider")/', $html, $html);
    }

    public function testShouldAllowProgrammaticDijitCreation()
    {
        Zend_Dojo_View_Helper_Dojo::setUseProgrammatic();
        $html = $this->getElement();
        $this->assertNotRegexp('/<div[^>]*(dojoType="dijit.form.HorizontalSlider")/', $html);
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

    public function testShouldCreateTopAndBottomDecorationsWhenRequested()
    {
        $html = $this->getElement();
        $this->assertRegexp('/<div[^>]*(dojoType="dijit.form.HorizontalRule")/', $html, $html);
        $this->assertRegexp('/<ol[^>]*(dojoType="dijit.form.HorizontalRuleLabels")/', $html, $html);
        $this->assertContains('topDecoration', $html);
        $this->assertContains('bottomDecoration', $html);
    }

    public function testShouldIgnoreLeftAndRightDecorationsWhenPassed()
    {
        $html = $this->getElement();
        $this->assertNotContains('leftDecoration', $html);
        $this->assertNotContains('rightDecoration', $html);
    }

    /**
     * @expectedException Zend_Dojo_View_Exception
     */
    public function testSliderShouldRaiseExceptionIfMissingRequiredParameters()
    {
        $this->helper->prepareSlider('foo', 4);
    }

    public function testShouldAllowPassingLabelParametersViaDecorationParameters()
    {
        $html = $this->helper->horizontalSlider(
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
                    'params' => array(
                        'required' => true,
                        'labels' => array(
                            'minimum' => 5,
                        )
                    ),
                    'dijit' => 'HorizontalRuleLabels',
                ),
            )
        );
        $this->assertContains('required="', $html);
        $this->assertContains('minimum="', $html);
    }

    /**
     * @group ZF-4435
     */
    public function testShouldCreateAppropriateIdsForElementsInSubForms()
    {
        $form = new Zend_Dojo_Form;
        $form->setDecorators(array(
            'FormElements',
            array('TabContainer', array(
                'id' => 'tabContainer',
                'style' => 'width: 600px; height: 300px;',
                'dijitParams' => array(
                    'tabPosition' => 'top'
                ),
            )),
            'DijitForm',
        ));

        $sliderForm = new Zend_Dojo_Form_SubForm();
        $sliderForm->setAttribs(array(
            'name'   => 'slidertab',
            'legend' => 'Slider Elements',
        ));

        $sliderForm->addElement(
                'HorizontalSlider',
                'slide1',
                array(
                    'label' => 'Slide me:',
                    'minimum' => 0,
                    'maximum' => 25,
                    'discreteValues' => 10,
                    'style' => 'width: 450px;',
                    'topDecorationDijit' => 'HorizontalRuleLabels',
                    'topDecorationLabels' => array('0%', '50%', '100%'),
                    'topDecorationParams' => array('style' => 'padding-bottom: 20px;')
                )
            );

        $form->addSubForm($sliderForm, 'slidertab')
             ->setView($this->getView());
        $html = $form->render();
        $this->assertContains('id="slidertab-slide1-slider"', $html);
        $this->assertContains('id="slidertab-slide1-slider-topDecoration"', $html);
        $this->assertContains('id="slidertab-slide1-slider-topDecoration-labels"', $html);
    }

    /**
     * @group ZF-5220
     */
    public function testLabelDivShouldOpenAndCloseBeforeLabelOl()
    {
        $html = $this->getElement();
        $this->assertNotRegexp('/<div[^>]*(dojoType="dijit.form.HorizontalRuleLabels")[^>]*><\/div>\s*<ol/s', $html, $html);
        $this->assertRegexp('/<div[^>]*><\/div>\s*<ol[^>]*(dojoType="dijit.form.HorizontalRuleLabels")/s', $html, $html);
    }
}

// Call Zend_Dojo_View_Helper_HorizontalSliderTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Dojo_View_Helper_HorizontalSliderTest::main") {
    Zend_Dojo_View_Helper_HorizontalSliderTest::main();
}
