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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

// Call Zend_Dojo_Form_Decorator_DijitContainerTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Dojo_Form_Decorator_DijitContainerTest::main");
}

require_once dirname(__FILE__) . '/../../../../TestHelper.php';

/** Zend_Dojo_Form_Decorator_DijitContainer */
require_once 'Zend/Dojo/Form/Decorator/DijitContainer.php';

/** Zend_Dojo_Form_Decorator_ContentPane */
require_once 'Zend/Dojo/Form/Decorator/ContentPane.php';

/** Zend_Dojo_Form_SubForm */
require_once 'Zend/Dojo/Form/SubForm.php';

/** Zend_View */
require_once 'Zend/View.php';

/** Zend_Registry */
require_once 'Zend/Registry.php';

/** Zend_Dojo_View_Helper_Dojo */
require_once 'Zend/Dojo/View/Helper/Dojo.php';

/**
 * Test class for Zend_Dojo_Form_Decorator_DijitContainer.
 *
 * @category   Zend
 * @package    Zend_Dojo
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Dojo
 * @group      Zend_Dojo_Form
 */
class Zend_Dojo_Form_Decorator_DijitContainerTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_Dojo_Form_Decorator_DijitContainerTest");
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
        Zend_Registry::_unsetInstance();
        Zend_Dojo_View_Helper_Dojo::setUseDeclarative();

        $this->errors = array();
        $this->view   = $this->getView();
        $this->decorator = new Zend_Dojo_Form_Decorator_ContentPane();
        $this->element   = $this->getElement();
        $this->element->setView($this->view);
        $this->decorator->setElement($this->element);
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
        $view = new Zend_View();
        $view->addHelperPath('Zend/Dojo/View/Helper/', 'Zend_Dojo_View_Helper');
        return $view;
    }

    public function getElement()
    {
        $element = new Zend_Dojo_Form_SubForm();
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
        $this->assertTrue($this->view->dojo()->isEnabled());
    }

    public function testRenderingShouldTriggerErrorWhenDuplicateDijitDetected()
    {
        $this->view->dojo()->addDijit('foo-ContentPane', array('dojoType' => 'dijit.layout.ContentPane'));

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

    /**
     * @expectedException Zend_Form_Decorator_Exception
     */
    public function testAbsenceOfHelperShouldRaiseException()
    {
        $decorator = new Zend_Dojo_Form_Decorator_DijitContainerTest_Example();
        $helper = $decorator->getHelper();
    }

    public function testShouldAllowPassingDijitParamsAsOptions()
    {
        $element = new Zend_Dojo_Form_SubForm();
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
        $element = new Zend_Dojo_Form_SubForm();
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

class Zend_Dojo_Form_Decorator_DijitContainerTest_Example extends Zend_Dojo_Form_Decorator_DijitContainer
{
}

// Call Zend_Dojo_Form_Decorator_DijitContainerTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Dojo_Form_Decorator_DijitContainerTest::main") {
    Zend_Dojo_Form_Decorator_DijitContainerTest::main();
}
