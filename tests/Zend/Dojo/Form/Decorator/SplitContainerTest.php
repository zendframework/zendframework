<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Dojo
 */

namespace ZendTest\Dojo\Form\Decorator;

use Zend\Dojo\Form\Decorator\SplitContainer as SplitContainerDecorator;
use Zend\Dojo\Form\SubForm as DojoSubForm;
use Zend\Dojo\Form\Form as DojoForm;
use Zend\Dojo\View\Helper\Dojo as DojoHelper;
use Zend\Registry;
use Zend\View;

/**
 * Test class for Zend_Dojo_Form_Decorator_SplitContainer.
 *
 * @category   Zend
 * @package    Zend_Dojo
 * @subpackage UnitTests
 * @group      Zend_Dojo
 * @group      Zend_Dojo_Form
 */
class SplitContainerTest extends \PHPUnit_Framework_TestCase
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
        $this->decorator = new SplitContainerDecorator();
        $this->element   = $this->getElement();
        $this->element->setView($this->view);
        $this->decorator->setElement($this->element);
    }

    public function getView()
    {
        $view = new View\Renderer\PhpRenderer();
        \Zend\Dojo\Dojo::enableView($view);
        return $view;
    }

    public function getElement()
    {
        $element = new DojoForm();
        $element->setAttribs(array(
            'name'   => 'foo',
            'style'  => 'width: 300px; height: 500px;',
            'class'  => 'someclass',
            'dijitParams' => array(
                'labelAttr' => 'foobar',
                'typeAttr'  => 'barbaz',
            ),
        ));
        return $element;
    }

    public function testRenderingShouldEnableDojo()
    {
        $html = $this->decorator->render('');
        $this->assertTrue($this->view->plugin('dojo')->isEnabled());
    }

    public function testRenderingShouldCreateDijit()
    {
        $html = $this->decorator->render('');
        $this->assertContains('dojoType="dijit.layout.SplitContainer"', $html);
    }
}
