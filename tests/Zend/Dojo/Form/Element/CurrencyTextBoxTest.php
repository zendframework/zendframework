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

use Zend\Dojo\Form\Element\CurrencyTextBox as CurrencyTextBoxElement,
    Zend\Dojo\Form\Element\NumberTextBox as NumberTextBoxElement,
    Zend\Dojo\View\Helper\Dojo as DojoHelper,
    Zend\Registry,
    Zend\View;

/**
 * Test class for Zend_Dojo_Form_Element_CurrencyTextBox.
 *
 * @category   Zend
 * @package    Zend_Dojo
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Dojo
 * @group      Zend_Dojo_Form
 */
class CurrencyTextBoxTest extends \PHPUnit_Framework_TestCase
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
        $element = new CurrencyTextBoxElement(
            'foo',
            array(
                'value' => 'some text',
                'label' => 'CurrencyTextBox',
                'class' => 'someclass',
                'style' => 'width: 100px;',
            )
        );
        return $element;
    }

    public function testShouldExtendNumberTextBox()
    {
        $this->assertTrue($this->element instanceof NumberTextBoxElement);
    }

    public function testCurrencyAccessorsShouldProxyToDijitParams()
    {
        $this->assertNull($this->element->getCurrency());
        $this->assertNull($this->element->getDijitParam('currency'));
        $this->element->setCurrency('USD');
        $this->assertEquals('USD', $this->element->getCurrency());
        $this->assertEquals('USD', $this->element->getDijitParam('currency'));
    }

    public function testFractionalAccessorsShouldProxyToConstraints()
    {
        $this->assertFalse($this->element->getFractional());
        $this->assertFalse(array_key_exists('constraints', $this->element->dijitParams));
        $this->element->setFractional(true);
        $this->assertTrue($this->element->getFractional());
        $this->assertEquals('true', $this->element->dijitParams['constraints']['fractional']);
    }

    public function testSymbolAccessorsShouldProxyToConstraints()
    {
        $this->assertNull($this->element->getSymbol());
        $this->assertFalse($this->element->hasConstraint('symbol'));
        $this->element->setSymbol('USD');
        $this->assertEquals('USD', $this->element->getSymbol());
        $this->assertEquals('USD', $this->element->getConstraint('symbol'));
    }

    public function testSymbolMutatorShouldCastToStringAndUppercaseAndLimitTo3Chars()
    {
        $this->element->setSymbol('usdollar');
        $this->assertEquals('USD', $this->element->getSymbol());
        $this->assertEquals('USD', $this->element->getConstraint('symbol'));
    }

    public function testSymbolMutatorShouldRaiseExceptionWhenFewerThan3CharsProvided()
    {
        $this->setExpectedException('Zend\Form\Element\Exception\InvalidArgumentException');
        $this->element->setSymbol('$');
    }

    public function testShouldRenderCurrencyTextBoxDijit()
    {
        $html = $this->element->render();
        $this->assertContains('dojoType="dijit.form.CurrencyTextBox"', $html);
    }
}
