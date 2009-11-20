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
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

// Call Zend_Form_Element_SelectTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Form_Element_SelectTest::main");
}

require_once dirname(__FILE__) . '/../../../TestHelper.php';

require_once 'Zend/Form/Element/Select.php';

/**
 * Test class for Zend_Form_Element_Select
 *
 * @category   Zend
 * @package    Zend_Form
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Form
 */
class Zend_Form_Element_SelectTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_Form_Element_SelectTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        $this->element = new Zend_Form_Element_Select('foo');
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @return void
     */
    public function tearDown()
    {
    }

    public function getView()
    {
        require_once 'Zend/View.php';
        $view = new Zend_View(array(
            'encoding' => 'UTF-8',
        ));
        $view->addHelperPath(dirname(__FILE__) . '/../../../../library/Zend/View/Helper');
        return $view;
    }

    public function testSelectElementSubclassesXhtmlElement()
    {
        $this->assertTrue($this->element instanceof Zend_Form_Element_Xhtml);
    }

    public function testSelectElementInstanceOfBaseElement()
    {
        $this->assertTrue($this->element instanceof Zend_Form_Element);
    }

    public function testSelectElementIsNotAnArrayByDefault()
    {
        $this->assertFalse($this->element->isArray());
    }

    public function testSelectElementUsesSelectHelperInViewHelperDecoratorByDefault()
    {
        $this->_checkZf2794();

        $decorator = $this->element->getDecorator('viewHelper');
        $this->assertTrue($decorator instanceof Zend_Form_Decorator_ViewHelper);
        $decorator->setElement($this->element);
        $helper = $decorator->getHelper();
        $this->assertEquals('formSelect', $helper);
    }

    public function testCanDisableIndividualSelectOptions()
    {
        $this->element->setMultiOptions(array(
                'foo' => 'foo',
                'bar' => array(
                    'baz' => 'Baz',
                    'bat' => 'Bat'
                ),
                'test' => 'Test',
            ))
            ->setAttrib('disable', array('baz', 'test'));
        $html = $this->element->render($this->getView());
        $this->assertNotRegexp('/<select[^>]*?(disabled="disabled")/', $html, $html);
        foreach (array('baz', 'test') as $test) {
            if (!preg_match('/(<option[^>]*?(value="' . $test . '")[^>]*>)/', $html, $m)) {
                $this->fail('Unable to find matching disabled option for ' . $test);
            }
            $this->assertRegexp('/<option[^>]*?(disabled="disabled")/', $m[1]);
        }
        foreach (array('foo', 'bat') as $test) {
            if (!preg_match('/(<option[^>]*?(value="' . $test . '")[^>]*>)/', $html, $m)) {
                $this->fail('Unable to find matching option for ' . $test);
            }
            $this->assertNotRegexp('/<option[^>]*?(disabled="disabled")/', $m[1], var_export($m, 1));
        }
    }

    /**
     * No explicit assertions; just checking for error conditions
     *
     * @see ZF-2847
     */
    public function testTranslationShouldNotRaiseWarningsWithNestedGroups()
    {
        require_once 'Zend/Translate.php';
        require_once 'Zend/View.php';
        $translate = new Zend_Translate('array', array('Select Test', 'Select Test Translated'), 'en');
        $this->element
             ->setLabel('Select Test')
             ->setMultiOptions(array(
                 'Group 1' => array(
                     '1-1' => 'Hi 1-1',
                     '1-2' => 'Hi 1-2',
                 ),
                 'Group 2' => array(
                     '2-1' => 'Hi 2-1',
                     '2-2' => 'Hi 2-2',
                 ),
             ))
             ->setTranslator($translate)
             ->setView(new Zend_View());
        $html = $this->element->render();
    }

    /**
     * @see   ZF-3953
     * @group ZF-3953
     */
    public function testUsingZeroAsValueShouldSelectAppropriateOption()
    {
        $this->element->setMultiOptions(array(
            array('key' => '1', 'value' => 'Yes'),
            array('key' => '0', 'value' => 'No'),
            array('key' => 'somewhat', 'value' => 'Somewhat'),
        ));
        $this->element->setValue(0);
        $html = $this->element->render($this->getView());

        if (!preg_match('#(<option[^>]*(?:value="somewhat")[^>]*>)#s', $html, $matches)) {
            $this->fail('Could not find option: ' . $html);
        }
        $this->assertNotContains('selected', $matches[1]);
    }

    /**
     * @group ZF-4390
     */
    public function testEmptyOptionsShouldNotBeTranslated()
    {
        $translate = new Zend_Translate('array', array('unused', 'foo' => 'bar'), 'en');
        $this->element->setTranslator($translate);
        $this->element->setMultiOptions(array(
            array('key' => '', 'value' => ''),
            array('key' => 'foo', 'value' => 'foo'),
        ));
        $this->element->setView($this->getView());
        $html = $this->element->render();
        $this->assertNotContains('unused', $html, $html);
        $this->assertContains('bar', $html, $html);
    }

    /**
     * Test isValid() on select elements without optgroups. This
     * ensures fixing ZF-3985 doesn't break existing functionality.
     *
     * @see ZF-3985
     */
    public function testIsValidWithPlainOptions()
    {
        // test both syntaxes for setting plain options
        $this->element->setMultiOptions(array(
            array('key' => '1', 'value' => 'Web Developer'),
            '2' => 'Software Engineer',
        ));

        $this->assertTrue($this->element->isValid('1'));
        $this->assertTrue($this->element->isValid('2'));
        $this->assertFalse($this->element->isValid('3'));
        $this->assertFalse($this->element->isValid('Web Developer'));
    }

    /**
     * @group ZF-3985
     */
    public function testIsValidWithOptionGroups()
    {
        // test optgroup and both syntaxes for setting plain options
        $this->element->setMultiOptions(array(
            'Technology' => array(
                '1' => 'Web Developer',
                '2' => 'Software Engineer',
            ),
            array('key' => '3', 'value' => 'Trainee'),
            '4' => 'Intern',
        ));

        $this->assertTrue($this->element->isValid('1'));
        $this->assertTrue($this->element->isValid('3'));
        $this->assertTrue($this->element->isValid('4'));
        $this->assertFalse($this->element->isValid('5'));
        $this->assertFalse($this->element->isValid('Technology'));
        $this->assertFalse($this->element->isValid('Web Developer'));
    }

    /**
     * @group ZF-8342
     */
    public function testUsingPoundSymbolInOptionLabelShouldRenderCorrectly()
    {
        $this->element->addMultiOption('1', '£' . number_format(1));
        $html = $this->element->render($this->getView());
        $this->assertContains('>£', $html);
    }

    /**
     * Used by test methods susceptible to ZF-2794, marks a test as incomplete
     *
     * @link   http://framework.zend.com/issues/browse/ZF-2794
     * @return void
     */
    protected function _checkZf2794()
    {
        if (strtolower(substr(PHP_OS, 0, 3)) == 'win' && version_compare(PHP_VERSION, '5.1.4', '=')) {
            $this->markTestIncomplete('Error occurs for PHP 5.1.4 on Windows');
        }
    }
}

// Call Zend_Form_Element_SelectTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Form_Element_SelectTest::main") {
    Zend_Form_Element_SelectTest::main();
}
