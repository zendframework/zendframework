<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Dojo
 */

namespace ZendTest\Dojo\View\Helper;

use Zend\Dojo\View\Helper\SimpleTextarea as SimpleTextareaHelper;
use Zend\Dojo\View\Helper\Dojo as DojoHelper;
use Zend\Registry;
use Zend\View\Renderer\PhpRenderer as View;

/**
 * Test class for Zend_Dojo_View_Helper_SimpleTextarea.
 *
 * @category   Zend
 * @package    Zend_Dojo
 * @subpackage UnitTests
 * @group      Zend_Dojo
 * @group      Zend_Dojo_View
 */
class SimpleTextareaTest extends \PHPUnit_Framework_TestCase
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
        $this->helper = new SimpleTextareaHelper();
        $this->helper->setView($this->view);
    }

    public function getView()
    {
        $view = new View();
        \Zend\Dojo\Dojo::enableView($view);
        return $view;
    }

    public function getElement()
    {
        return $this->helper->__invoke(
            'elementId',
            'some content',
            array(),
            array()
        );
    }

    public function testShouldAllowDeclarativeDijitCreation()
    {
        $html = $this->getElement();
        $this->assertRegexp('/<textarea[^>]*(dojoType="dijit.form.SimpleTextarea")/', $html, $html);
    }

    public function testShouldAllowProgrammaticDijitCreation()
    {
        DojoHelper::setUseProgrammatic();
        $html = $this->getElement();
        $this->assertNotRegexp('/<textarea[^>]*(dojoType="dijit.form.SimpleTextarea")/', $html);
        $this->assertNotNull($this->view->plugin('dojo')->getDijit('elementId'));
    }

    public function testPassingIdAsAttributeShouldOverrideUsingNameAsId()
    {
        $html = $this->helper->__invoke('foo[bar]', '', array(), array('id' => 'foo-bar'));
        $this->assertContains('id="foo-bar"', $html);
    }

    public function testGeneratedMarkupShouldNotIncludeTypeAttribute()
    {
        $html = $this->getElement();
        $this->assertNotRegexp('/type="text/', $html, $html);
    }
}
