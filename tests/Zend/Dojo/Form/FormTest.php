<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Dojo
 */

namespace ZendTest\Dojo\Form;

use Zend\Dojo\Form\Form as DojoForm,
    Zend\View;

/**
 * Test class for Zend_Dojo_Form and Zend_Dojo_Form_DisplayGroup
 *
 * @category   Zend
 * @package    Zend_Dojo
 * @subpackage UnitTests
 * @group      Zend_Dojo
 * @group      Zend_Dojo_Form
 */
class FormTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        $this->form = new DojoForm();
        $this->form->addElement('TextBox', 'foo')
                   ->addDisplayGroup(array('foo'), 'dg')
                   ->setView(new View\Renderer\PhpRenderer());
    }

    public function testDojoFormDecoratorPathShouldBeRegisteredByDefault()
    {
        $paths = $this->form->getPluginLoader('decorator')->getPaths('Zend\Dojo\Form\Decorator');
        $this->assertInstanceOf('Zend\Stdlib\SplStack', $paths);
    }

    public function testDojoFormElementPathShouldBeRegisteredByDefault()
    {
        $paths = $this->form->getPluginLoader('element')->getPaths('Zend\Dojo\Form\Element');
        $this->assertInstanceOf('Zend\Stdlib\SplStack', $paths);
    }

    public function testDojoFormElementDecoratorPathShouldBeRegisteredByDefault()
    {
        $paths = $this->form->foo->getPluginLoader('decorator')->getPaths('Zend\Dojo\Form\Decorator');
        $this->assertInstanceOf('Zend\Stdlib\SplStack', $paths);
    }

    public function testDojoFormDisplayGroupDecoratorPathShouldBeRegisteredByDefault()
    {
        $paths = $this->form->dg->getPluginLoader()->getPaths('Zend\Dojo\Form\Decorator');
        $this->assertInstanceOf('Zend\Stdlib\SplStack', $paths);
    }

    public function testDefaultDisplayGroupClassShouldBeDojoDisplayGroupByDefault()
    {
        $this->assertEquals('Zend\Dojo\Form\DisplayGroup', $this->form->getDefaultDisplayGroupClass());
    }

    public function testDefaultDecoratorsShouldIncludeDijitForm()
    {
        $this->assertNotNull($this->form->getDecorator('DijitForm'));
    }

    public function testShouldRegisterDojoViewHelper()
    {
        $view = $this->form->getView();
        $this->assertInstanceOf('Zend\Dojo\View\Helper\Dojo', $view->plugin('dojo'));
    }

    public function testDisplayGroupShouldRegisterDojoViewHelper()
    {
        $this->form->dg->setView(new View\Renderer\PhpRenderer());
        $view   = $this->form->dg->getView();
        $this->assertInstanceOf('Zend\Dojo\View\Helper\Dojo', $view->plugin('dojo'));
    }

    /**
     * @group ZF-4748
     */
    public function testHtmlTagDecoratorShouldHaveZendFormDojoClassByDefault()
    {
        $decorator = $this->form->getDecorator('HtmlTag');
        $this->assertEquals('zend_form_dojo', $decorator->getOption('class'));
    }
}
