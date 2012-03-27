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
 * @package    Zend_Form
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Form\Decorator;

use Zend\Form\Form,
    Zend\Form\SubForm,
    Zend\Translator\Translator,
    Zend\View\Renderer\PhpRenderer;

/**
 * Test class for Zend_Form_Decorator_PrepareElements
 *
 * @category   Zend
 * @package    Zend_Form
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Form
 */
class PrepareElementsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        $this->form = new Form();
        $this->form->setDecorators(array('PrepareElements'));
        $this->decorator = $this->form->getDecorator('PrepareElements');
    }

    public function getView()
    {
        $view = new PhpRenderer();
        return $view;
    }

    public function prepareForm()
    {
        $sub1 = new SubForm();
        $sub1->addElement('text', 'foo')
             ->addElement('text', 'bar');

        $this->form->setElementsBelongTo('foo')
                   ->addElement('text', 'foo')
                   ->addElement('text', 'bar')
                   ->addElement('text', 'baz')
                   ->addElement('text', 'bat')
                   ->addDisplayGroup(array('baz', 'bat'), 'bazbat')
                   ->addSubForm($sub1, 'sub')
                   ->setView($this->getView());
    }

    public function testEachElementShouldHaveUpdatedBelongsToProperty()
    {
        $this->prepareForm();
        $this->form->render();
        $belongsTo = $this->form->getElementsBelongTo();
        foreach ($this->form->getElements() as $element) {
            $this->assertEquals($belongsTo, $element->getBelongsTo(), 'Tested element; wrong belongsTo');
        }
        foreach ($this->form->getSubForms() as $subForm) {
            $name = $subForm->getElementsBelongTo();
            foreach ($subForm->getElements() as $element) {
                $this->assertEquals($name, $element->getBelongsTo(), 'Tested sub element; wrong belongsTo; ' . $name . ': ' . $element->getName());
            }
        }
    }

    public function testEachElementShouldHaveUpdatedViewProperty()
    {
        $this->prepareForm();
        $this->form->render();
        $view = $this->form->getView();
        foreach ($this->form as $item) {
            $this->assertSame($view, $item->getView());
            if ($item instanceof Form) {
                foreach ($item->getElements() as $subItem) {
                    $this->assertSame($view, $subItem->getView(), var_export($subItem, 1));
                }
            }
        }
    }

    public function testEachElementShouldHaveUpdatedTranslatorProperty()
    {
        $this->prepareForm();
        $translator = new Translator('ArrayAdapter', array('foo' => 'bar'), 'en');
        $this->form->setTranslator($translator);
        $this->form->render();
        $translator = $this->form->getTranslator();
        foreach ($this->form as $item) {
            $this->assertSame($translator, $item->getTranslator(), 'Translator not the same: ' . var_export($item->getTranslator(), 1));
            if ($item instanceof Form) {
                foreach ($item->getElements() as $subItem) {
                    $this->assertSame($translator, $subItem->getTranslator(), var_export($subItem, 1));
                }
            }
        }
    }

    public function testEachSubFormShouldBePrepared()
    {
        $subForm = new SubForm();
        $subSubForm = new SubForm();

        $subForm->addSubForm($subSubForm, "subSubForm");
        $this->form->addSubForm($subForm, "subForm");

        $this->form->render();

        $this->assertEquals("subForm[subSubForm]", $this->form->getSubForm("subForm")->getSubForm("subSubForm")->getElementsBelongTo());
    }

}
