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
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\View\Helper;

use Zend\View\Renderer\PhpRenderer as View,
    Zend\View\Helper\FormErrors;

/**
 * Test class for Zend_View_Helper_FormErrors
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
class FormErrorsTest extends \PHPUnit_Framework_TestCase
{

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        $this->view   = new View();
        $this->helper = new FormErrors();
        $this->helper->setView($this->view);
        ob_start();
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    public function tearDown()
    {
        ob_end_clean();
    }

    public function testGetElementEndReturnsDefaultValue()
    {
        $this->assertEquals('</li></ul>', $this->helper->getElementEnd());
    }

    public function testGetElementSeparatorReturnsDefaultValue()
    {
        $this->assertEquals('</li><li>', $this->helper->getElementSeparator());
    }

    public function testGetElementStartReturnsDefaultValue()
    {
        $this->assertEquals('<ul%s><li>', $this->helper->getElementStart());
    }

    public function testCanSetElementEndString()
    {
        $this->testGetElementEndReturnsDefaultValue();
        $this->helper->setElementEnd('</pre></div>');
        $this->assertEquals('</pre></div>', $this->helper->getElementEnd());
    }

    public function testCanSetElementSeparatorString()
    {
        $this->testGetElementSeparatorReturnsDefaultValue();
        $this->helper->setElementSeparator('<br />');
        $this->assertEquals('<br />', $this->helper->getElementSeparator());
    }

    public function testCanSetElementStartString()
    {
        $this->testGetElementStartReturnsDefaultValue();
        $this->helper->setElementStart('<div><pre>');
        $this->assertEquals('<div><pre>', $this->helper->getElementStart());
    }

    public function testFormErrorsRendersUnorderedListByDefault()
    {
        $errors = array('foo', 'bar', 'baz');
        $html = $this->helper->__invoke($errors);
        $this->assertContains('<ul', $html);
        foreach ($errors as $error) {
            $this->assertContains('<li>' . $error . '</li>', $html);
        }
        $this->assertContains('</ul>', $html);
    }

    public function testFormErrorsRendersWithSpecifiedStrings()
    {
        $this->helper->setElementStart('<dl><dt>')
                     ->setElementSeparator('</dt><dt>')
                     ->setElementEnd('</dt></dl>');
        $errors = array('foo', 'bar', 'baz');
        $html = $this->helper->__invoke($errors);
        $this->assertContains('<dl>', $html);
        foreach ($errors as $error) {
            $this->assertContains('<dt>' . $error . '</dt>', $html);
        }
        $this->assertContains('</dl>', $html);
    }

    public function testFormErrorsPreventsXssAttacks()
    {
        $errors = array(
            'bad' => '\"><script>alert("xss");</script>',
        );
        $html = $this->helper->__invoke($errors);
        $this->assertNotContains($errors['bad'], $html);
        $this->assertContains('&', $html);
    }

    public function testCanDisableEscapingErrorMessages()
    {
        $errors = array(
            'foo' => '<b>Field is required</b>',
            'bar' => '<a href="/help">Please click here for more information</a>'
        );
        $html = $this->helper->__invoke($errors, array('escape' => false));
        $this->assertContains($errors['foo'], $html);
        $this->assertContains($errors['bar'], $html);
    }

    /**
     * @issue ZF-3477
     * @link http://framework.zend.com/issues/browse/ZF-3477
     */
    public function testCanSetClassAttribute()
    {
        $options = array('class' => 'custom-class');
        $acutallHtml = $this->helper->__invoke(array(), $options);
        $this->assertEquals('<ul class="custom-class"><li></li></ul>', $acutallHtml);
    }
}
