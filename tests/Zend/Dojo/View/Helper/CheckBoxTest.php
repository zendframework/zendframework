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

namespace ZendTest\Dojo\View\Helper;

use Zend\Dojo\View\Helper\CheckBox as CheckBoxHelper,
    Zend\Dojo\View\Helper\Dojo as DojoHelper,
    Zend\Registry,
    Zend\View;

/**
 * Test class for Zend_Dojo_View_Helper_CheckBox.
 *
 * @category   Zend
 * @package    Zend_Dojo
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Dojo
 * @group      Zend_Dojo_View
 */
class CheckBoxTest extends \PHPUnit_Framework_TestCase
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
        $this->helper = new CheckBoxHelper();
        $this->helper->setView($this->view);
    }

    public function getView()
    {
        $view = new View\Renderer\PhpRenderer();
        \Zend\Dojo\Dojo::enableView($view);
        return $view;
    }

    public function getElement()
    {
        return $this->helper->__invoke(
            'elementId',
            'foo',
            array(),
            array(),
            array(
                'checked'   => 'foo',
                'unChecked' => 'bar',
            )
        );
    }

    public function testShouldAllowDeclarativeDijitCreation()
    {
        $html = $this->getElement();
        $this->assertRegexp('/<input[^>]*(dojoType="dijit.form.CheckBox")/', $html, $html);
    }

    public function testShouldAllowProgrammaticDijitCreation()
    {
        DojoHelper::setUseProgrammatic();
        $html = $this->getElement();
        $this->assertNotRegexp('/<input[^>]*(dojoType="dijit.form.CheckBox")/', $html);
        $this->assertNotNull($this->view->plugin('dojo')->getDijit('elementId'));
    }

    public function testShouldCreateHiddenElementWithUncheckedValue()
    {
        $html = $this->getElement();
        if (!preg_match('/(<input[^>]*(type="hidden")[^>]*>)/s', $html, $m)) {
            $this->fail('Missing hidden element with unchecked value');
        }
        $this->assertContains('value="bar"', $m[1]);
    }

    public function testShouldCheckElementWhenValueMatchesCheckedValue()
    {
        $html = $this->getElement();
        if (!preg_match('/(<input[^>]*(type="checkbox")[^>]*>)/s', $html, $m)) {
            $this->fail('Missing checkbox element: ' . $html);
        }
        $this->assertContains('checked="checked"', $m[1]);
    }

    /**
     * @group ZF-4006
     */
    public function testElementShouldUseCheckedValueForCheckboxInput()
    {
        $html = $this->helper->__invoke('foo', '0', array(), array(), array(
            'checkedValue'   => '1',
            'unCheckedValue' => '0',
        ));
        if (!preg_match('#(<input[^>]*(?:type="checkbox")[^>]*>)#s', $html, $matches)) {
            $this->fail('Did not find checkbox in html: ' . $html);
        }
        $this->assertContains('value="1"', $matches[1]);
        $this->assertNotContains('checked', $matches[1]);
    }

    /**
     * @group ZF-3878
     */
    public function testElementShouldCreateAppropriateIdWhenNameIncludesArrayNotation()
    {
        $html = $this->helper->__invoke('foo[bar]', '0');
        $this->assertContains('id="foo-bar"', $html);
    }
}
