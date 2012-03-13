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

use Zend\Dojo\View\Helper\VerticalSlider as VerticalSliderHelper,
    Zend\Dojo\View\Helper\Dojo as DojoHelper,
    Zend\Registry,
    Zend\View;

/**
 * Test class for Zend_Dojo_View_Helper_VerticalSlider.
 *
 * @category   Zend
 * @package    Zend_Dojo
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Dojo
 * @group      Zend_Dojo_View
 */
class VerticalSliderTest extends \PHPUnit_Framework_TestCase
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
        $this->helper = new VerticalSliderHelper();
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
        DojoHelper::setUseProgrammatic();
        $html = $this->getElement();
        $this->assertNotRegexp('/<div[^>]*(dojoType="dijit.form.VerticalSlider")/', $html);
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
