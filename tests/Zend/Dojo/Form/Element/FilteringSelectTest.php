<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Dojo
 */

namespace ZendTest\Dojo\Form\Element;

use Zend\Dojo\Form\Element\FilteringSelect as FilteringSelectElement;
use Zend\Dojo\View\Helper\Dojo as DojoHelper;
use Zend\Registry;
use Zend\Validator\InArray as InArrayValidator;
use Zend\View;

/**
 * Test class for Zend_Dojo_Form_Element_FilteringSelect.
 *
 * @category   Zend
 * @package    Zend_Dojo
 * @subpackage UnitTests
 * @group      Zend_Dojo
 * @group      Zend_Dojo_Form
 */
class FilteringSelectTest extends \PHPUnit_Framework_TestCase
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

        $this->view    = $this->getView();
        $this->element = $this->getElement();
        $this->element->setView($this->view);
    }

    public function getView()
    {
        $view = new View\Renderer\PhpRenderer();
        \Zend\Dojo\Dojo::enableView($view);
        return $view;
    }

    public function getElement()
    {
        $element = new FilteringSelectElement(
            'foo',
            array(
                'value' => 'some text',
                'label' => 'FilteringSelect',
                'class' => 'someclass',
                'style' => 'width: 100px;',
            )
        );
        return $element;
    }

    public function testShouldRenderFilteringSelectDijit()
    {
        $html = $this->element->render();
        $this->assertContains('dojoType="dijit.form.FilteringSelect"', $html);
    }

    /**
     * @group ZF-3286
     */
    public function testShouldRegisterInArrayValidatorWhenNoStoreProvided()
    {
        $options = array(
            'foo' => 'Foo Value',
            'bar' => 'Bar Value',
            'baz' => 'Baz Value',
        );
        $this->element->setMultiOptions($options);
        $this->assertFalse($this->element->getValidator('InArray'));
        $this->element->isValid('test');
        $validator = $this->element->getValidator('InArray');
        $this->assertTrue($validator instanceof InArrayValidator);
    }

    /**
     * @group ZF-3286
     */
    public function testShouldNotRegisterInArrayValidatorWhenStoreProvided()
    {
        $options = array(
            'foo' => 'Foo Value',
            'bar' => 'Bar Value',
            'baz' => 'Baz Value',
        );
        $this->element->setMultiOptions($options);
        $this->element->setStoreId('fooStore')
                      ->setStoreType('dojo.data.ItemFileReadStore')
                      ->setStoreParams(array('url' => '/foo'));
        $this->assertFalse($this->element->getValidator('InArray'));
        $this->element->isValid('test');
        $this->assertFalse($this->element->getValidator('InArray'));
    }
}
