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

use Zend\Dojo\Form\Element\NumberTextBox as NumberTextBoxElement,
    Zend\Dojo\View\Helper\Dojo as DojoHelper,
    Zend\Registry,
    Zend\View;

/**
 * Test class for Zend_Dojo_Form_Element_NumberTextBox.
 *
 * @category   Zend
 * @package    Zend_Dojo
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Dojo
 * @group      Zend_Dojo_Form
 */
class NumberTextBoxTest extends \PHPUnit_Framework_TestCase
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
        $element = new NumberTextBoxElement(
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

    public function testTypeMutatorShouldThrowExceptionWithInvalidType()
    {
        $this->setExpectedException('Zend\Form\Element\Exception\InvalidArgumentException');
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
