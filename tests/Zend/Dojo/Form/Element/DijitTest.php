<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Dojo
 */

namespace ZendTest\Dojo\Form\Element;

use Zend\Dojo\Form\Element\TextBox as TextBoxElement;
use Zend\Dojo\Form\Decorator\DijitElement as DijitElementDecorator;
use Zend\Dojo\View\Helper\Dojo as DojoHelper;
use Zend\Form\Decorator\Description as DescriptionDecorator;
use Zend\Registry;
use Zend\View;

/**
 * Test class for Zend_Dojo_Form_Element_Dijit.
 *
 * @category   Zend
 * @package    Zend_Dojo
 * @subpackage UnitTests
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
