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

use Zend\Form\Decorator\FormDecorator,
    Zend\Form\DisplayGroup,
    Zend\Form\Form,
    Zend\Loader\PrefixPathLoader,
    Zend\View\Renderer\PhpRenderer as View;

/**
 * Test class for Zend_Form_Decorator_Form
 *
 * @category   Zend
 * @package    Zend_Form
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Form
 */
class FormDecoratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        $this->decorator = new FormDecorator();
    }

    public function getView()
    {
        $view = new View();
        return $view;
    }

    public function testHelperIsFormByDefault()
    {
        $this->assertEquals('form', $this->decorator->getHelper());
    }

    public function testCanSetHelperWithOption()
    {
        $this->testHelperIsFormByDefault();
        $this->decorator->setOption('helper', 'formForm');
        $this->assertEquals('formForm', $this->decorator->getHelper());

        $attribs = array(
            'enctype' => 'ascii',
            'charset' => 'us-ascii'
        );
        $loader = new PrefixPathLoader(array('Zend\Form\Decorator' => 'Zend/Form/Decorator/'));
        $displayGroup = new DisplayGroup('foo', $loader, array('attribs' => $attribs));
        $this->decorator->setElement($displayGroup);
        $options = $this->decorator->getOptions();
        $this->assertTrue(isset($options['enctype']));
        $this->assertEquals($attribs['enctype'], $options['enctype']);
        $this->assertTrue(isset($options['charset']));
        $this->assertEquals($attribs['charset'], $options['charset']);
    }

    /**
     * @group ZF-3643
     */
    public function testShouldPreferFormIdAttributeOverFormName()
    {
        $form = new Form();
        $form->setMethod('post')
             ->setAction('/foo/bar')
             ->setName('foobar')
             ->setAttrib('id', 'bazbat')
             ->setView($this->getView());
        $html = $form->render();
        $this->assertContains('id="bazbat"', $html, $html);
    }

    public function testEmptyFormNameShouldNotRenderEmptyFormId()
    {
        $form = new Form();
        $form->setMethod('post')
             ->setAction('/foo/bar')
             ->setView($this->getView());
        $html = $form->render();
        $this->assertNotContains('id=""', $html, $html);
    }
}
