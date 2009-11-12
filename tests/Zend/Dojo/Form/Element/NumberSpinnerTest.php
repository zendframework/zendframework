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
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

// Call Zend_Dojo_Form_Element_NumberSpinnerTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Dojo_Form_Element_NumberSpinnerTest::main");
}

require_once dirname(__FILE__) . '/../../../../TestHelper.php';

/** Zend_Dojo_Form_Element_NumberSpinner */
require_once 'Zend/Dojo/Form/Element/NumberSpinner.php';

/** Zend_View */
require_once 'Zend/View.php';

/** Zend_Registry */
require_once 'Zend/Registry.php';

/** Zend_Dojo_View_Helper_Dojo */
require_once 'Zend/Dojo/View/Helper/Dojo.php';

/**
 * Test class for Zend_Dojo_Form_Element_NumberSpinner.
 *
 * @category   Zend
 * @package    Zend_Dojo
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Dojo
 * @group      Zend_Dojo_Form
 */
class Zend_Dojo_Form_Element_NumberSpinnerTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_Dojo_Form_Element_NumberSpinnerTest");
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

        $this->view    = $this->getView();
        $this->element = $this->getElement();
        $this->element->setView($this->view);
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
        $element = new Zend_Dojo_Form_Element_NumberSpinner(
            'foo',
            array(
                'value' => 'some text',
                'label' => 'NumberSpinner',
                'class' => 'someclass',
                'style' => 'width: 100px;',
            )
        );
        return $element;
    }

    public function testDefaultTimeoutAccessorsShouldProxyToDijitParams()
    {
        $this->assertNull($this->element->getDefaultTimeout());
        $this->assertFalse(array_key_exists('defaultTimeout', $this->element->dijitParams));
        $this->element->setDefaultTimeout(20);
        $this->assertEquals(20, $this->element->getDefaultTimeout());
        $this->assertEquals(20, $this->element->dijitParams['defaultTimeout']);
    }

    public function testTimeoutChangeRateAccessorsShouldProxyToDijitParams()
    {
        $this->assertNull($this->element->getTimeoutChangeRate());
        $this->assertFalse(array_key_exists('timeoutChangeRate', $this->element->dijitParams));
        $this->element->setTimeoutChangeRate(20);
        $this->assertEquals(20, $this->element->getTimeoutChangeRate());
        $this->assertEquals(20, $this->element->dijitParams['timeoutChangeRate']);
    }

    public function testLargeDeltaAccessorsShouldProxyToDijitParams()
    {
        $this->assertNull($this->element->getLargeDelta());
        $this->assertFalse(array_key_exists('largeDelta', $this->element->dijitParams));
        $this->element->setLargeDelta(20);
        $this->assertEquals(20, $this->element->getLargeDelta());
        $this->assertEquals(20, $this->element->dijitParams['largeDelta']);
    }

    public function testSmallDeltaAccessorsShouldProxyToDijitParams()
    {
        $this->assertNull($this->element->getSmallDelta());
        $this->assertFalse(array_key_exists('smallDelta', $this->element->dijitParams));
        $this->element->setSmallDelta(20);
        $this->assertEquals(20, $this->element->getSmallDelta());
        $this->assertEquals(20, $this->element->dijitParams['smallDelta']);
    }

    public function testIntermediateChangesAccessorsShouldProxyToDijitParams()
    {
        $this->assertFalse($this->element->getIntermediateChanges());
        $this->assertFalse(array_key_exists('intermediateChanges', $this->element->dijitParams));
        $this->element->setIntermediateChanges(true);
        $this->assertTrue($this->element->getIntermediateChanges());
        $this->assertTrue($this->element->dijitParams['intermediateChanges']);
    }

    public function testRangeMessageAccessorsShouldProxyToDijitParams()
    {
        $this->assertNull($this->element->getRangeMessage());
        $this->assertFalse(array_key_exists('rangeMessage', $this->element->dijitParams));
        $this->element->setRangeMessage('foo');
        $this->assertEquals('foo', $this->element->getRangeMessage());
        $this->assertEquals('foo', $this->element->dijitParams['rangeMessage']);
    }

    public function testMinAccessorsShouldProxyToConstraintsDijitParam()
    {
        $this->assertNull($this->element->getMin());
        $this->assertFalse(array_key_exists('constraints', $this->element->dijitParams));
        $this->element->setMin(5);
        $this->assertEquals(5, $this->element->getMin());
        $this->assertEquals(5, $this->element->dijitParams['constraints']['min']);
    }

    public function testMaxAccessorsShouldProxyToConstraintsDijitParam()
    {
        $this->assertNull($this->element->getMax());
        $this->assertFalse(array_key_exists('constraints', $this->element->dijitParams));
        $this->element->setMax(5);
        $this->assertEquals(5, $this->element->getMax());
        $this->assertEquals(5, $this->element->dijitParams['constraints']['max']);
    }

    public function testShouldRenderNumberSpinnerDijit()
    {
        $html = $this->element->render();
        $this->assertContains('dojoType="dijit.form.NumberSpinner"', $html);
    }

    /**
     * @group ZF-4638
     */
    public function testRenderingShouldOutputMinAndMaxConstraints()
    {
        $this->element->setMin(5)
                      ->setMax(10);
        $html = $this->element->render();
        $this->assertRegexp('/\'min\':\s*5/', $html, $html);
        $this->assertRegexp('/\'max\':\s*10/', $html, $html);
    }
}

// Call Zend_Dojo_Form_Element_NumberSpinnerTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Dojo_Form_Element_NumberSpinnerTest::main") {
    Zend_Dojo_Form_Element_NumberSpinnerTest::main();
}
