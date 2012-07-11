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

use Zend\Dojo\View\Helper\SubmitButton as SubmitButtonHelper;
use Zend\Dojo\View\Helper\Dojo as DojoHelper;
use Zend\Registry;
use Zend\View;

/**
 * Test class for Zend_Dojo_View_Helper_SubmitButton.
 *
 * @category   Zend
 * @package    Zend_Dojo
 * @subpackage UnitTests
 * @group      Zend_Dojo
 * @group      Zend_Dojo_View
 */
class SubmitButtonTest extends \PHPUnit_Framework_TestCase
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
        $this->helper = new SubmitButtonHelper();
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
            array()
        );
    }

    public function testShouldAllowDeclarativeDijitCreation()
    {
        $html = $this->getElement();
        $this->assertRegexp('/<input[^>]*(type="submit")/', $html, $html);
        $this->assertRegexp('/<input[^>]*(dojoType="dijit.form.Button")/', $html, $html);
        $this->assertRegexp('/<input[^>]*(label="foo")/', $html, $html);
    }

    public function testShouldAllowProgrammaticDijitCreation()
    {
        DojoHelper::setUseProgrammatic();
        $html = $this->getElement();
        $this->assertNotRegexp('/<input[^>]*(dojoType="dijit.form.Button")/', $html);
        $this->assertNotNull($this->view->plugin('dojo')->getDijit('elementId'));
    }

    /**
     * @group ZF-4977
     */
    public function testHelperShouldRenderContentKeyAsLabelWhenPassed()
    {
        $html = $this->helper->__invoke('foo', '', array('content' => 'Label'));
        $this->assertRegexp('/<input[^>]*(value="Label")/', $html, $html);
    }
}
