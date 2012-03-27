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

namespace ZendTest\Dojo\Form\Decorator;

use Zend\Dojo\Form\Decorator\DijitContainer as DijitContainerDecorator,
    Zend\Dojo\Form\Decorator\ContentPane as ContentPaneDecorator,
    Zend\Dojo\Form\SubForm as DojoSubForm,
    Zend\Dojo\Form\Form as DojoForm,
    Zend\Dojo\View\Helper\Dojo as DojoHelper,
    Zend\Registry,
    Zend\View;

/**
 * Test class for Zend_Dojo_Form_Decorator_DijitContainer.
 *
 * @category   Zend
 * @package    Zend_Dojo
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Dojo
 * @group      Zend_Dojo_Form
 */
class DijitContainerTest extends \PHPUnit_Framework_TestCase
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

        $this->errors = array();
        $this->view   = $this->getView();
        $this->decorator = new ContentPaneDecorator();
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
        $element = new DojoSubForm();
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

    /**
     * Handle an error (for testing notices)
     *
     * @param  int $errno
     * @param  string $errstr
     * @return void
     */
    public function handleError($errno, $errstr)
    {
        $this->errors[] = $errstr;
    }

    public function testRetrievingElementAttributesShouldOmitDijitParams()
    {
        $attribs = $this->decorator->getAttribs();
        $this->assertTrue(is_array($attribs));
        $this->assertFalse(array_key_exists('dijitParams', $attribs));
    }

    public function testRetrievingDijitParamsShouldOmitNormalAttributes()
    {
        $params = $this->decorator->getDijitParams();
        $this->assertTrue(is_array($params));
        $this->assertFalse(array_key_exists('class', $params));
        $this->assertFalse(array_key_exists('style', $params));
    }

    public function testLegendShouldBeUsedAsTitleByDefault()
    {
        $this->element->setLegend('Foo Bar');
        $this->assertEquals('Foo Bar', $this->decorator->getTitle());
    }

    public function testLegendOptionShouldBeUsedAsFallbackTitleWhenNoLegendPresentInElement()
    {
        $this->decorator->setOption('legend', 'Legend Option')
                        ->setOption('title', 'Title Option');
        $options = $this->decorator->getOptions();
        $this->assertEquals('Legend Option', $this->decorator->getTitle(), var_export($options, 1));
    }

    public function testTitleOptionShouldBeUsedAsFinalFallbackTitleWhenNoLegendPresentInElement()
    {
        $this->decorator->setOption('title', 'Title Option');
        $options = $this->decorator->getOptions();
        $this->assertEquals('Title Option', $this->decorator->getTitle(), var_export($options, 1));
    }

    public function testRenderingShouldEnableDojo()
    {
        $html = $this->decorator->render('');
        $this->assertTrue($this->view->plugin('dojo')->isEnabled());
    }

    public function testRenderingShouldTriggerErrorWhenDuplicateDijitDetected()
    {
        $this->view->plugin('dojo')->addDijit('foo-ContentPane', array('dojoType' => 'dijit.layout.ContentPane'));

        $handler = set_error_handler(array($this, 'handleError'));
        $html = $this->decorator->render('');
        restore_error_handler();

        $this->assertFalse(empty($this->errors), var_export($this->errors, 1));
        $found = false;
        foreach ($this->errors as $error) {
            if (strstr($error, 'Duplicate')) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found);
    }

    public function testRenderingShouldCreateDijit()
    {
        $html = $this->decorator->render('');
        $this->assertContains('dojoType="dijit.layout.ContentPane"', $html);
    }

    public function testAbsenceOfHelperShouldRaiseException()
    {
        $decorator = new TestAsset\ExampleContainer();
        $this->setExpectedException('Zend\Form\Decorator\Exception');
        $helper = $decorator->getHelper();
    }

    public function testShouldAllowPassingDijitParamsAsOptions()
    {
        $element = new DojoSubForm();
        $element->setAttribs(array(
            'name'   => 'foo',
            'style'  => 'width: 300px; height: 500px;',
            'class'  => 'someclass',
        ));
        $dijitParams = array(
            'labelAttr' => 'foobar',
            'typeAttr'  => 'barbaz',
        );
        $this->decorator->setElement($element);
        $this->decorator->setOption('dijitParams', $dijitParams);
        $test = $this->decorator->getDijitParams();
        foreach ($dijitParams as $key => $value) {
            $this->assertEquals($value, $test[$key]);
        }
    }

    public function testShouldUseLegendAttribAsTitleIfNoTitlePresent()
    {
        $element = new DojoSubForm();
        $element->setAttribs(array(
                    'name'   => 'foo',
                    'legend' => 'FooBar',
                    'style'  => 'width: 300px; height: 500px;',
                    'class'  => 'someclass',
                ))
                ->setView($this->view);
        $this->decorator->setElement($element);
        $html = $this->decorator->render('');
        $this->assertContains('FooBar', $html);
    }
}
