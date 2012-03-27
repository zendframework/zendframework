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

use Zend\Dojo\Form\Element\TextBox as TextBoxElement,
    Zend\Dojo\Form\Decorator\DijitElement as DijitElementDecorator,
    Zend\Dojo\View\Helper\Dojo as DojoHelper,
    Zend\Form\Decorator\Description as DescriptionDecorator,
    Zend\Registry,
    Zend\View;

/**
 * Test class for Zend_Dojo_Form_Element_Dijit.
 *
 * @category   Zend
 * @package    Zend_Dojo
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Dojo
 * @group      Zend_Dojo_Form
 */
class DijitTest extends \PHPUnit_Framework_TestCase
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
        $element = new TextBoxElement(
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
        $this->assertTrue($decorator instanceof DijitElementDecorator, get_class($decorator));
    }

    /**
     * @group ZF-5264
     */
    public function testDescriptionDecoratorShouldBeEnabledByDefault()
    {
        $decorator = $this->element->getDecorator('Description');
        $this->assertTrue($decorator instanceof DescriptionDecorator, get_class($decorator));
    }

    public function testRenderingShouldCreateDijit()
    {
        $html = $this->element->render();
        $this->assertContains('dojoType="dijit.form.TextBox"', $html);
    }

    public function testElementShouldDojoEnableViewObject()
    {
        $this->element->setView(new View\Renderer\PhpRenderer());
        $view = $this->element->getView();
        
        $this->assertInstanceOf('Zend\Dojo\View\Helper\Dojo', $view->plugin('dojo'));
    }
}
