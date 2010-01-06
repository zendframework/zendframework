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

// Call Zend_Dojo_Form_Element_HorizontalSliderTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Dojo_Form_Element_HorizontalSliderTest::main");
}

require_once dirname(__FILE__) . '/../../../../TestHelper.php';

/** Zend_Dojo_Form_Element_HorizontalSlider */
require_once 'Zend/Dojo/Form/Element/HorizontalSlider.php';

/** Zend_View */
require_once 'Zend/View.php';

/** Zend_Registry */
require_once 'Zend/Registry.php';

/** Zend_Dojo_View_Helper_Dojo */
require_once 'Zend/Dojo/View/Helper/Dojo.php';

/**
 * Test class for Zend_Dojo_Form_Element_HorizontalSlider.
 *
 * @category   Zend
 * @package    Zend_Dojo
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Dojo
 * @group      Zend_Dojo_Form
 */
class Zend_Dojo_Form_Element_HorizontalSliderTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_Dojo_Form_Element_HorizontalSliderTest");
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
        $element = new Zend_Dojo_Form_Element_HorizontalSlider(
            'foo',
            array(
                'value' => 'some text',
                'label' => 'HorizontalSlider',
                'class' => 'someclass',
                'style' => 'width: 100px;',
            )
        );
        return $element;
    }

    public function testClickSelectAccessorsShouldProxyToDijitParams()
    {
        $this->assertFalse($this->element->getClickSelect());
        $this->assertFalse(array_key_exists('clickSelect', $this->element->dijitParams));
        $this->element->setClickSelect(true);
        $this->assertTrue($this->element->getClickSelect());
        $this->assertTrue($this->element->dijitParams['clickSelect']);
    }

    public function testIntermediateChangesAccessorsShouldProxyToDijitParams()
    {
        $this->assertFalse($this->element->getIntermediateChanges());
        $this->assertFalse(array_key_exists('intermediateChanges', $this->element->dijitParams));
        $this->element->setIntermediateChanges(true);
        $this->assertTrue($this->element->getIntermediateChanges());
        $this->assertTrue($this->element->dijitParams['intermediateChanges']);
    }

    public function testShowButtonsAccessorsShouldProxyToDijitParams()
    {
        $this->assertFalse($this->element->getShowButtons());
        $this->assertFalse(array_key_exists('showButtons', $this->element->dijitParams));
        $this->element->setShowButtons(true);
        $this->assertTrue($this->element->getShowButtons());
        $this->assertTrue($this->element->dijitParams['showButtons']);
    }

    public function testDiscreteValuesAccessorsShouldProxyToDijitParams()
    {
        $this->assertNull($this->element->getDiscreteValues());
        $this->assertFalse(array_key_exists('discreteValues', $this->element->dijitParams));
        $this->element->setDiscreteValues(20);
        $this->assertEquals(20, $this->element->getDiscreteValues());
        $this->assertEquals(20, $this->element->dijitParams['discreteValues']);
    }

    public function testMinimumAccessorsShouldProxyToDijitParams()
    {
        $this->assertNull($this->element->getMinimum());
        $this->assertFalse(array_key_exists('minimum', $this->element->dijitParams));
        $this->element->setMinimum(20);
        $this->assertEquals(20, $this->element->getMinimum());
        $this->assertEquals(20, $this->element->dijitParams['minimum']);
    }

    public function testMaximumAccessorsShouldProxyToDijitParams()
    {
        $this->assertNull($this->element->getMaximum());
        $this->assertFalse(array_key_exists('maximum', $this->element->dijitParams));
        $this->element->setMaximum(20);
        $this->assertEquals(20, $this->element->getMaximum());
        $this->assertEquals(20, $this->element->dijitParams['maximum']);
    }

    public function testPageIncrementAccessorsShouldProxyToDijitParams()
    {
        $this->assertNull($this->element->getPageIncrement());
        $this->assertFalse(array_key_exists('pageIncrement', $this->element->dijitParams));
        $this->element->setPageIncrement(20);
        $this->assertEquals(20, $this->element->getPageIncrement());
        $this->assertEquals(20, $this->element->dijitParams['pageIncrement']);
    }

    public function testSettingTopDecorationDijitShouldProxyToTopDecorationDijitParam()
    {
        $this->element->setTopDecorationDijit('HorizontalRule');
        $this->assertTrue($this->element->hasDijitParam('topDecoration'));
        $topDecoration = $this->element->getDijitParam('topDecoration');

        $test = $this->element->getTopDecoration();
        $this->assertSame($topDecoration, $test);

        $this->assertTrue(array_key_exists('dijit', $topDecoration));
        $this->assertEquals('HorizontalRule', $topDecoration['dijit']);
    }

    public function testSettingTopDecorationContainerShouldProxyToTopDecorationDijitParam()
    {
        $this->element->setTopDecorationContainer('top');
        $this->assertTrue($this->element->hasDijitParam('topDecoration'));
        $topDecoration = $this->element->getDijitParam('topDecoration');

        $test = $this->element->getTopDecoration();
        $this->assertSame($topDecoration, $test);

        $this->assertTrue(array_key_exists('container', $topDecoration));
        $this->assertEquals('top', $topDecoration['container']);
    }

    public function testSettingTopDecorationLabelsShouldProxyToTopDecorationDijitParam()
    {
        $labels = array('0%', '50%', '100%');
        $this->element->setTopDecorationLabels($labels);
        $this->assertTrue($this->element->hasDijitParam('topDecoration'));
        $topDecoration = $this->element->getDijitParam('topDecoration');

        $test = $this->element->getTopDecoration();
        $this->assertSame($topDecoration, $test);

        $this->assertTrue(array_key_exists('labels', $topDecoration));
        $this->assertSame($labels, $topDecoration['labels']);
    }

    public function testSettingTopDecorationParamsShouldProxyToTopDecorationDijitParam()
    {
        $params = array(
            'container' => array(
                'style' => 'height:1.2em; font-size=75%;color:gray;',
            ),
            'list' => array(
                'style' => 'height:1em; font-size=75%;color:gray;',
            ),
        );
        $this->element->setTopDecorationParams($params);
        $this->assertTrue($this->element->hasDijitParam('topDecoration'));
        $topDecoration = $this->element->getDijitParam('topDecoration');

        $test = $this->element->getTopDecoration();
        $this->assertSame($topDecoration, $test);

        $this->assertTrue(array_key_exists('params', $topDecoration));
        $this->assertSame($params, $topDecoration['params']);
    }

    public function testSettingTopDecorationAttribsShouldProxyToTopDecorationDijitParam()
    {
        $attribs = array(
            'container' => array(
                'style' => 'height:1.2em; font-size=75%;color:gray;',
            ),
            'list' => array(
                'style' => 'height:1em; font-size=75%;color:gray;',
            ),
        );
        $this->element->setTopDecorationAttribs($attribs);
        $this->assertTrue($this->element->hasDijitParam('topDecoration'));
        $topDecoration = $this->element->getDijitParam('topDecoration');

        $test = $this->element->getTopDecoration();
        $this->assertSame($topDecoration, $test);

        $this->assertTrue(array_key_exists('attribs', $topDecoration));
        $this->assertSame($attribs, $topDecoration['attribs']);
    }

    public function testSettingBottomDecorationDijitShouldProxyToBottomDecorationDijitParam()
    {
        $this->element->setBottomDecorationDijit('HorizontalRule');
        $this->assertTrue($this->element->hasDijitParam('bottomDecoration'));
        $bottomDecoration = $this->element->getDijitParam('bottomDecoration');

        $test = $this->element->getBottomDecoration();
        $this->assertSame($bottomDecoration, $test);

        $this->assertTrue(array_key_exists('dijit', $bottomDecoration));
        $this->assertEquals('HorizontalRule', $bottomDecoration['dijit']);
    }

    public function testSettingBottomDecorationContainerShouldProxyToBottomDecorationDijitParam()
    {
        $this->element->setBottomDecorationContainer('bottom');
        $this->assertTrue($this->element->hasDijitParam('bottomDecoration'));
        $bottomDecoration = $this->element->getDijitParam('bottomDecoration');

        $test = $this->element->getBottomDecoration();
        $this->assertSame($bottomDecoration, $test);

        $this->assertTrue(array_key_exists('container', $bottomDecoration));
        $this->assertEquals('bottom', $bottomDecoration['container']);
    }

    public function testSettingBottomDecorationLabelsShouldProxyToBottomDecorationDijitParam()
    {
        $labels = array('0%', '50%', '100%');
        $this->element->setBottomDecorationLabels($labels);
        $this->assertTrue($this->element->hasDijitParam('bottomDecoration'));
        $bottomDecoration = $this->element->getDijitParam('bottomDecoration');

        $test = $this->element->getBottomDecoration();
        $this->assertSame($bottomDecoration, $test);

        $this->assertTrue(array_key_exists('labels', $bottomDecoration));
        $this->assertSame($labels, $bottomDecoration['labels']);
    }

    public function testSettingBottomDecorationParamsShouldProxyToBottomDecorationDijitParam()
    {
        $params = array(
            'container' => array(
                'style' => 'height:1.2em; font-size=75%;color:gray;',
            ),
            'list' => array(
                'style' => 'height:1em; font-size=75%;color:gray;',
            ),
        );
        $this->element->setBottomDecorationParams($params);
        $this->assertTrue($this->element->hasDijitParam('bottomDecoration'));
        $bottomDecoration = $this->element->getDijitParam('bottomDecoration');

        $test = $this->element->getBottomDecoration();
        $this->assertSame($bottomDecoration, $test);

        $this->assertTrue(array_key_exists('params', $bottomDecoration));
        $this->assertSame($params, $bottomDecoration['params']);
    }

    public function testSettingBottomDecorationAttribsShouldProxyToBottomDecorationDijitParam()
    {
        $attribs = array(
            'container' => array(
                'style' => 'height:1.2em; font-size=75%;color:gray;',
            ),
            'list' => array(
                'style' => 'height:1em; font-size=75%;color:gray;',
            ),
        );
        $this->element->setBottomDecorationAttribs($attribs);
        $this->assertTrue($this->element->hasDijitParam('bottomDecoration'));
        $bottomDecoration = $this->element->getDijitParam('bottomDecoration');

        $test = $this->element->getBottomDecoration();
        $this->assertSame($bottomDecoration, $test);

        $this->assertTrue(array_key_exists('attribs', $bottomDecoration));
        $this->assertSame($attribs, $bottomDecoration['attribs']);
    }

    public function testShouldRenderHorizontalSliderDijit()
    {
        $this->element->setMinimum(-10)
                      ->setMaximum(10)
                      ->setDiscreteValues(11);
        $html = $this->element->render();
        $this->assertContains('dojoType="dijit.form.HorizontalSlider"', $html);
    }
}

// Call Zend_Dojo_Form_Element_HorizontalSliderTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_Dojo_Form_Element_HorizontalSliderTest::main") {
    Zend_Dojo_Form_Element_HorizontalSliderTest::main();
}
