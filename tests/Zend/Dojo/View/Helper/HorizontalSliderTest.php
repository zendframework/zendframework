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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Dojo\View\Helper;

use Zend\Dojo\View\Helper\HorizontalSlider as HorizontalSliderHelper,
    Zend\Dojo\View\Helper\Dojo as DojoHelper,
    Zend\Dojo\Form\Form as DojoForm,
    Zend\Dojo\Form\SubForm as DojoSubForm,
    Zend\Registry,
    Zend\View;

/**
 * Test class for Zend_Dojo_View_Helper_HorizontalSlider.
 *
 * @category   Zend
 * @package    Zend_Dojo
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Dojo
 * @group      Zend_Dojo_View
 */
class HorizontalSliderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        Registry::_unsetInstance();
        DojoHelper::setUseDeclarative();

        $this->view   = $this->getView();
        $this->helper = new HorizontalSliderHelper();
        $this->helper->setView($this->view);
    }

    public function getView()
    {
        $view = new View\Renderer\PhpRenderer();
        \Zend\Dojo\Dojo::enableView($view);
        return $view;
    }

    public function getElement()
    {
        return $this->helper->__invoke(
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
        DojoHelper::setUseProgrammatic();
        $html = $this->getElement();
        $this->assertNotRegexp('/<div[^>]*(dojoType="dijit.form.HorizontalSlider")/', $html);
        $this->assertNotNull($this->view->plugin('dojo')->getDijit('elementId-slider'));
    }

    public function testShouldCreateOnChangeAttributeByDefault()
    {
        $html = $this->getElement();
        $this->assertContains('onChange="dojo.byId(&#39;elementId&#39;).value = arguments[0];"', $html, $html);
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

    public function testSliderShouldRaiseExceptionIfMissingRequiredParameters()
    {
        $this->setExpectedException('Zend\Dojo\View\Exception\InvalidArgumentException', 'prepareSlider() requires minimally the "minimum", "maximum", and "discreteValues" parameters');
        $this->helper->prepareSlider('foo', 4);
    }

    public function testShouldAllowPassingLabelParametersViaDecorationParameters()
    {
        $html = $this->helper->__invoke(
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
        $form = new DojoForm;
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

        $sliderForm = new DojoSubForm();
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
