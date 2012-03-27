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

namespace ZendTest\Form;

use Zend\Form\Form,
    Zend\Form\SubForm,
    Zend\View\Renderer\PhpRenderer as View;

/**
 * @category   Zend
 * @package    Zend_Form
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Form
 */
class SubFormTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        Form::setDefaultTranslator(null);

        $this->form = new SubForm();
    }

    // General
    public function testSubFormUtilizesDefaultDecorators()
    {
        $decorators = $this->form->getDecorators();
        $this->assertTrue(array_key_exists('Zend\Form\Decorator\FormElements', $decorators));
        $this->assertTrue(array_key_exists('Zend\Form\Decorator\HtmlTag', $decorators));
        $this->assertTrue(array_key_exists('Zend\Form\Decorator\Fieldset', $decorators));
        $this->assertTrue(array_key_exists('Zend\Form\Decorator\DtDdWrapper', $decorators));

        $htmlTag = $decorators['Zend\Form\Decorator\HtmlTag'];
        $tag = $htmlTag->getOption('tag');
        $this->assertEquals('dl', $tag);
    }

    public function testSubFormIsArrayByDefault()
    {
        $this->assertTrue($this->form->isArray());
    }

    public function testElementsBelongToSubFormNameByDefault()
    {
        $this->testSubFormIsArrayByDefault();
        $this->form->setName('foo');
        $this->assertEquals($this->form->getName(), $this->form->getElementsBelongTo());
    }

    // Extensions

    public function testInitCalledBeforeLoadDecorators()
    {
        $form = new TestAsset\SubForm();
        $decorators = $form->getDecorators();
        $this->assertTrue(empty($decorators));
    }

    // Bugfixes

    /**
     * @group ZF-2883
     */
    public function testDisplayGroupsShouldInheritSubFormNamespace()
    {
        $this->form->addElement('text', 'foo')
                   ->addElement('text', 'bar')
                   ->addDisplayGroup(array('foo', 'bar'), 'foobar');

        $form = new Form();
        $form->addSubForm($this->form, 'attributes');
        $html = $form->render(new View());

        $this->assertContains('name="attributes[foo]"', $html);
        $this->assertContains('name="attributes[bar]"', $html);
    }

    /**
     * @group ZF-3272
     */
    public function testRenderedSubFormDtShouldContainNoBreakSpace()
    {
        $subForm = new SubForm(array(
            'elements' => array(
                'foo' => 'text',
                'bar' => 'text',
            ),
        ));
        $form = new Form();
        $form->addSubForm($subForm, 'foobar')
             ->setView(new View);
        $html = $form->render();
        $this->assertContains('>&#160;</dt>', $html  );
    }

    /**
     * Prove the fluent interface on Zend_Form_Subform::loadDefaultDecorators
     *
     * @group ZF-9913
     * @return void
     */
    public function testFluentInterfaceOnLoadDefaultDecorators()
    {
        $this->assertSame($this->form, $this->form->loadDefaultDecorators());
    }
}
