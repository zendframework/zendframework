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

use Zend\Dojo\View\Helper\ComboBox as ComboBoxHelper,
    Zend\Dojo\View\Helper\Dojo as DojoHelper,
    Zend\Registry,
    Zend\View;

/**
 * Test class for Zend_Dojo_View_Helper_ComboBox.
 *
 * @category   Zend
 * @package    Zend_Dojo
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Dojo
 * @group      Zend_Dojo_View
 */
class ComboBoxTest extends \PHPUnit_Framework_TestCase
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
        $this->helper = new ComboBoxHelper();
        $this->helper->setView($this->view);
    }

    public function getView()
    {
        $view = new View\Renderer\PhpRenderer();
        \Zend\Dojo\Dojo::enableView($view);
        return $view;
    }

    public function getElementAsSelect()
    {
        return $this->helper->__invoke(
            'elementId',
            'someCombo',
            array(),
            array(),
            array(
                'red' => 'Rouge',
                'blue' => 'Bleu',
                'white' => 'Blanc',
                'orange' => 'Orange',
                'black' => 'Noir',
                'green' => 'Vert',
            )
        );
    }

    public function getElementAsRemoter()
    {
        return $this->helper->__invoke(
            'elementId',
            'someCombo',
            array(
                'store' => array(
                    'store' => 'stateStore',
                    'type' => 'dojo.data.ItemFileReadStore',
                    'params' => array(
                        'url' => 'states.txt'
                    )
                ),
                'searchAttr' => 'name'
            ),
            array()
        );
    }

    public function testShouldAllowDeclarativeDijitCreationAsSelect()
    {
        $html = $this->getElementAsSelect();
        $this->assertRegexp('/<select[^>]*(dojoType="dijit.form.ComboBox")/', $html, $html);
    }

    public function testShouldAllowProgrammaticDijitCreationAsSelect()
    {
        DojoHelper::setUseProgrammatic();
        $html = $this->getElementAsSelect();
        $this->assertNotRegexp('/<select[^>]*(dojoType="dijit.form.ComboBox")/', $html);
        $this->assertNotNull($this->view->plugin('dojo')->getDijit('elementId'));
    }

    public function testShouldAllowDeclarativeDijitCreationAsRemoter()
    {
        $html = $this->getElementAsRemoter();
        if (!preg_match('/(<input[^>]*(dojoType="dijit.form.ComboBox"))/', $html, $m)) {
            $this->fail('Did not create text input as remoter: ' . $html);
        }
        $this->assertContains('type="text"', $m[1]);
    }

    public function testShouldAllowProgrammaticDijitCreationAsRemoter()
    {
        DojoHelper::setUseProgrammatic();
        $html = $this->getElementAsRemoter();
        $this->assertNotRegexp('/<input[^>]*(dojoType="dijit.form.ComboBox")/', $html);
        $this->assertRegexp('/<input[^>]*(type="text")/', $html);
        $this->assertNotNull($this->view->plugin('dojo')->getDijit('elementId'));

        $found = false;
        $this->assertContains('var stateStore;', $this->view->plugin('dojo')->getJavascript());

        $scripts = $this->view->plugin('dojo')->_getZendLoadActions();
        foreach ($scripts as $js) {
            if (strstr($js, 'stateStore = new ')) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found, 'No store declaration found: ' . var_export($scripts, 1));
    }

    public function testShouldAllowAlternateNotationToSpecifyRemoter()
    {
        $html = $this->helper->__invoke(
            'elementId',
            'someCombo',
            array(
                'store'       => 'stateStore',
                'storeType'   => 'dojo.data.ItemFileReadStore',
                'storeParams' => array('url' => 'states.txt'),
                'searchAttr'  => 'name',
            )
        );
        if (!preg_match('/(<input[^>]*(dojoType="dijit.form.ComboBox"))/', $html, $m)) {
            $this->fail('Did not create text input as remoter: ' . $html);
        }
        $this->assertContains('type="text"', $m[1]);
        if (!preg_match('/(<div[^>]*(?:dojoType="dojo.data.ItemFileReadStore")[^>]*>)/', $html, $m)) {
            $this->fail('Did not create data store: ' . $html);
        }
        $this->assertContains('url="states.txt"', $m[1]);
    }

    /**
     * @group ZF-5987
     * @group ZF-7266
     */
    public function testStoreCreationWhenUsingProgrammaticCreationShouldRegisterAsDojoJavascript()
    {
        DojoHelper::setUseProgrammatic(true);
        $html = $this->getElementAsRemoter();

        $js   = $this->view->plugin('dojo')->getJavascript();
        $this->assertContains('var stateStore;', $js);

        $onLoad = $this->view->plugin('dojo')->_getZendLoadActions();
        $storeDeclarationFound = false;
        foreach ($onLoad as $statement) {
            if (strstr($statement, 'stateStore = new ')) {
                $storeDeclarationFound = true;
                break;
            }
        }
        $this->assertTrue($storeDeclarationFound, 'Store definition not found');
    }
}
