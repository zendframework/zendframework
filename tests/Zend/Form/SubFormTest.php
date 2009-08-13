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

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Form_SubFormTest::main');
}

require_once dirname(__FILE__) . '/../../TestHelper.php';
require_once 'PHPUnit/Framework/TestSuite.php';
require_once 'PHPUnit/TextUI/TestRunner.php';

// error_reporting(E_ALL);

require_once 'Zend/Form/SubForm.php';
require_once 'Zend/View.php';

/**
 * @category   Zend
 * @package    Zend_Form
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Form
 */
class Zend_Form_SubFormTest extends PHPUnit_Framework_TestCase
{
    public static function main()
    {
        require_once "PHPUnit/TextUI/TestRunner.php";
        $suite  = new PHPUnit_Framework_TestSuite('Zend_Form_SubFormTest');
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function setUp()
    {
        Zend_Form::setDefaultTranslator(null);

        $this->form = new Zend_Form_SubForm();
    }

    public function tearDown()
    {
    }

    // General
    public function testSubFormUtilizesDefaultDecorators()
    {
        $decorators = $this->form->getDecorators();
        $this->assertTrue(array_key_exists('Zend_Form_Decorator_FormElements', $decorators));
        $this->assertTrue(array_key_exists('Zend_Form_Decorator_HtmlTag', $decorators));
        $this->assertTrue(array_key_exists('Zend_Form_Decorator_Fieldset', $decorators));
        $this->assertTrue(array_key_exists('Zend_Form_Decorator_DtDdWrapper', $decorators));

        $htmlTag = $decorators['Zend_Form_Decorator_HtmlTag'];
        $tag = $htmlTag->getOption('tag');
        $this->assertEquals('dl', $tag);
    }

    public function testSubFormIsArrayByDefault()
    {
        $this->assertTrue($this->form->isArray());
    }

    public function testElementsBelongToSubFormNameByDefault()
    {
        $this->testSubFormIsArrayByDefault();
        $this->form->setName('foo');
        $this->assertEquals($this->form->getName(), $this->form->getElementsBelongTo());
    }

    // Extensions

    public function testInitCalledBeforeLoadDecorators()
    {
        $form = new Zend_Form_SubFormTest_SubForm();
        $decorators = $form->getDecorators();
        $this->assertTrue(empty($decorators));
    }

    // Bugfixes

    /**
     * @see ZF-2883
     */
    public function testDisplayGroupsShouldInheritSubFormNamespace()
    {
        $this->form->addElement('text', 'foo')
                   ->addElement('text', 'bar')
                   ->addDisplayGroup(array('foo', 'bar'), 'foobar');

        $form = new Zend_Form();
        $form->addSubForm($this->form, 'attributes');
        $html = $form->render(new Zend_View());

        $this->assertContains('name="attributes[foo]"', $html);
        $this->assertContains('name="attributes[bar]"', $html);
    }

    /**
     * @see ZF-3272
     */
    public function testRenderedSubFormDtShouldContainNoBreakSpace()
    {
        $subForm = new Zend_Form_SubForm(array(
            'elements' => array(
                'foo' => 'text',
                'bar' => 'text',
            ),
        ));
        $form = new Zend_Form();
        $form->addSubForm($subForm, 'foobar')
             ->setView(new Zend_View);
        $html = $form->render();
        $this->assertContains('<dt>&nbsp;</dt>', $html);
    }
}

class Zend_Form_SubFormTest_SubForm extends Zend_Form_SubForm
{
    public function init()
    {
        $this->setDisableLoadDefaultDecorators(true);
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Form_SubFormTest::main') {
    Zend_Form_SubFormTest::main();
}
