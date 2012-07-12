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

use Zend\Dojo\Form\Element\TextBox as TextBoxElement;
use Zend\Dojo\View\Helper\Dojo as DojoHelper;
use Zend\Registry;
use Zend\View;

/**
 * Test class for Zend_Dojo_Form_Element_TextBox.
 *
 * @category   Zend
 * @package    Zend_Dojo
 * @subpackage UnitTests
 * @group      Zend_Dojo
 * @group      Zend_Dojo_Form
 */
class TextBoxTest extends \PHPUnit_Framework_TestCase
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
        $element = new TextBoxElement(
            'foo',
            array(
                'value' => 'some text',
                'label' => 'TextBox',
                'trim'  => true,
                'propercase' => true,
                'class' => 'someclass',
                'style' => 'width: 100px;',
            )
        );
        return $element;
    }

    public function testLowercaseAccessorsShouldProxyToDijitParams()
    {
        $this->assertFalse($this->element->getLowercase());
        $this->assertFalse(array_key_exists('lowercase', $this->element->dijitParams));
        $this->element->setLowercase(true);
        $this->assertTrue($this->element->getLowercase());
        $this->assertTrue($this->element->dijitParams['lowercase']);
    }

    public function testPropercaseAccessorsShouldProxyToDijitParams()
    {
        $this->assertTrue($this->element->getPropercase());
        $this->assertTrue(array_key_exists('propercase', $this->element->dijitParams));
        $this->element->setPropercase(false);
        $this->assertFalse($this->element->getPropercase());
    }

    public function testUppercaseAccessorsShouldProxyToDijitParams()
    {
        $this->assertFalse($this->element->getUppercase());
        $this->assertFalse(array_key_exists('uppercase', $this->element->dijitParams));
        $this->element->setUppercase(true);
        $this->assertTrue($this->element->getUppercase());
        $this->assertTrue($this->element->dijitParams['uppercase']);
    }

    public function testTrimAccessorsShouldProxyToDijitParams()
    {
        $this->assertTrue($this->element->getTrim());
        $this->assertTrue(array_key_exists('trim', $this->element->dijitParams));
        $this->element->setTrim(false);
        $this->assertFalse($this->element->getTrim());
    }

    public function testMaxLengthAccessorsShouldProxyToDijitParams()
    {
        $this->assertNull($this->element->getMaxLength());
        $this->assertFalse(array_key_exists('maxLength', $this->element->dijitParams));
        $this->element->setMaxLength(20);
        $this->assertEquals(20, $this->element->getMaxLength());
        $this->assertEquals(20, $this->element->dijitParams['maxLength']);
    }
}
