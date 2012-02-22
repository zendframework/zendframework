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

namespace ZendTest\Dojo\Form\Element;

use Zend\Dojo\Form\Element\VerticalSlider as VerticalSliderElement,
    Zend\Dojo\View\Helper\Dojo as DojoHelper,
    Zend\Registry,
    Zend\View;

/**
 * Test class for Zend_Dojo_Form_Element_VerticalSlider.
 *
 * @category   Zend
 * @package    Zend_Dojo
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Dojo
 * @group      Zend_Dojo_Form
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

        $this->view    = $this->getView();
        $this->element = $this->getElement();
        $this->element->setView($this->view);
    }

    public function getView()
    {
        $view = new View\Renderer\PhpRenderer();
        \Zend\Dojo\Dojo::enableView($view);
        return $view;
    }

    public function getElement()
    {
        $element = new VerticalSliderElement(
            'foo',
            array(
                'value' => 'some text',
                'label' => 'VerticalSlider',
                'class' => 'someclass',
                'style' => 'width: 100px;',
            )
        );
        return $element;
    }

    public function testSettingLeftDecorationDijitShouldProxyToLeftDecorationDijitParam()
    {
        $this->element->setLeftDecorationDijit('VerticalRule');
        $this->assertTrue($this->element->hasDijitParam('leftDecoration'));
        $leftDecoration = $this->element->getDijitParam('leftDecoration');

        $test = $this->element->getLeftDecoration();
        $this->assertSame($leftDecoration, $test);

        $this->assertTrue(array_key_exists('dijit', $leftDecoration));
        $this->assertEquals('VerticalRule', $leftDecoration['dijit']);
    }

    public function testSettingLeftDecorationContainerShouldProxyToLeftDecorationDijitParam()
    {
        $this->element->setLeftDecorationContainer('left');
        $this->assertTrue($this->element->hasDijitParam('leftDecoration'));
        $leftDecoration = $this->element->getDijitParam('leftDecoration');

        $test = $this->element->getLeftDecoration();
        $this->assertSame($leftDecoration, $test);

        $this->assertTrue(array_key_exists('container', $leftDecoration));
        $this->assertEquals('left', $leftDecoration['container']);
    }

    public function testSettingLeftDecorationLabelsShouldProxyToLeftDecorationDijitParam()
    {
        $labels = array('0%', '50%', '100%');
        $this->element->setLeftDecorationLabels($labels);
        $this->assertTrue($this->element->hasDijitParam('leftDecoration'));
        $leftDecoration = $this->element->getDijitParam('leftDecoration');

        $test = $this->element->getLeftDecoration();
        $this->assertSame($leftDecoration, $test);

        $this->assertTrue(array_key_exists('labels', $leftDecoration));
        $this->assertSame($labels, $leftDecoration['labels']);
    }

    public function testSettingLeftDecorationParamsShouldProxyToLeftDecorationDijitParam()
    {
        $params = array(
            'container' => array(
                'style' => 'height:1.2em; font-size=75%;color:gray;',
            ),
            'list' => array(
                'style' => 'height:1em; font-size=75%;color:gray;',
            ),
        );
        $this->element->setLeftDecorationParams($params);
        $this->assertTrue($this->element->hasDijitParam('leftDecoration'));
        $leftDecoration = $this->element->getDijitParam('leftDecoration');

        $test = $this->element->getLeftDecoration();
        $this->assertSame($leftDecoration, $test);

        $this->assertTrue(array_key_exists('params', $leftDecoration));
        $this->assertSame($params, $leftDecoration['params']);
    }

    public function testSettingLeftDecorationAttribsShouldProxyToLeftDecorationDijitParam()
    {
        $attribs = array(
            'container' => array(
                'style' => 'height:1.2em; font-size=75%;color:gray;',
            ),
            'list' => array(
                'style' => 'height:1em; font-size=75%;color:gray;',
            ),
        );
        $this->element->setLeftDecorationAttribs($attribs);
        $this->assertTrue($this->element->hasDijitParam('leftDecoration'));
        $leftDecoration = $this->element->getDijitParam('leftDecoration');

        $test = $this->element->getLeftDecoration();
        $this->assertSame($leftDecoration, $test);

        $this->assertTrue(array_key_exists('attribs', $leftDecoration));
        $this->assertSame($attribs, $leftDecoration['attribs']);
    }

    public function testSettingRightDecorationDijitShouldProxyToRightDecorationDijitParam()
    {
        $this->element->setRightDecorationDijit('VerticalRule');
        $this->assertTrue($this->element->hasDijitParam('rightDecoration'));
        $rightDecoration = $this->element->getDijitParam('rightDecoration');

        $test = $this->element->getRightDecoration();
        $this->assertSame($rightDecoration, $test);

        $this->assertTrue(array_key_exists('dijit', $rightDecoration));
        $this->assertEquals('VerticalRule', $rightDecoration['dijit']);
    }

    public function testSettingRightDecorationContainerShouldProxyToRightDecorationDijitParam()
    {
        $this->element->setRightDecorationContainer('right');
        $this->assertTrue($this->element->hasDijitParam('rightDecoration'));
        $rightDecoration = $this->element->getDijitParam('rightDecoration');

        $test = $this->element->getRightDecoration();
        $this->assertSame($rightDecoration, $test);

        $this->assertTrue(array_key_exists('container', $rightDecoration));
        $this->assertEquals('right', $rightDecoration['container']);
    }

    public function testSettingRightDecorationLabelsShouldProxyToRightDecorationDijitParam()
    {
        $labels = array('0%', '50%', '100%');
        $this->element->setRightDecorationLabels($labels);
        $this->assertTrue($this->element->hasDijitParam('rightDecoration'));
        $rightDecoration = $this->element->getDijitParam('rightDecoration');

        $test = $this->element->getRightDecoration();
        $this->assertSame($rightDecoration, $test);

        $this->assertTrue(array_key_exists('labels', $rightDecoration));
        $this->assertSame($labels, $rightDecoration['labels']);
    }

    public function testSettingRightDecorationParamsShouldProxyToRightDecorationDijitParam()
    {
        $params = array(
            'container' => array(
                'style' => 'height:1.2em; font-size=75%;color:gray;',
            ),
            'list' => array(
                'style' => 'height:1em; font-size=75%;color:gray;',
            ),
        );
        $this->element->setRightDecorationParams($params);
        $this->assertTrue($this->element->hasDijitParam('rightDecoration'));
        $rightDecoration = $this->element->getDijitParam('rightDecoration');

        $test = $this->element->getRightDecoration();
        $this->assertSame($rightDecoration, $test);

        $this->assertTrue(array_key_exists('params', $rightDecoration));
        $this->assertSame($params, $rightDecoration['params']);
    }

    public function testSettingRightDecorationAttribsShouldProxyToRightDecorationDijitParam()
    {
        $attribs = array(
            'container' => array(
                'style' => 'height:1.2em; font-size=75%;color:gray;',
            ),
            'list' => array(
                'style' => 'height:1em; font-size=75%;color:gray;',
            ),
        );
        $this->element->setRightDecorationAttribs($attribs);
        $this->assertTrue($this->element->hasDijitParam('rightDecoration'));
        $rightDecoration = $this->element->getDijitParam('rightDecoration');

        $test = $this->element->getRightDecoration();
        $this->assertSame($rightDecoration, $test);

        $this->assertTrue(array_key_exists('attribs', $rightDecoration));
        $this->assertSame($attribs, $rightDecoration['attribs']);
    }

    public function testShouldRenderVerticalSliderDijit()
    {
        $this->element->setMinimum(-10)
                      ->setMaximum(10)
                      ->setDiscreteValues(11);
        $html = $this->element->render();
        $this->assertContains('dojoType="dijit.form.VerticalSlider"', $html);
    }
}
