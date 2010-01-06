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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Form_DisplayGroupTest::main');
}

require_once dirname(__FILE__) . '/../../TestHelper.php';

require_once 'Zend/Form/DisplayGroup.php';

require_once 'Zend/Config.php';
require_once 'Zend/Controller/Action/HelperBroker.php';
require_once 'Zend/Form.php';
require_once 'Zend/Form/Decorator/Form.php';
require_once 'Zend/Form/Decorator/HtmlTag.php';
require_once 'Zend/Form/Element.php';
require_once 'Zend/Form/Element/Text.php';
require_once 'Zend/Loader/PluginLoader.php';
require_once 'Zend/Translate.php';
require_once 'Zend/View.php';

/**
 * @category   Zend
 * @package    Zend_Form
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Form
 */
class Zend_Form_DisplayGroupTest extends PHPUnit_Framework_TestCase
{
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite('Zend_Form_DisplayGroupTest');
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function setUp()
    {
        Zend_Form::setDefaultTranslator(null);

        if (isset($this->error)) {
            unset($this->error);
        }

        Zend_Controller_Action_HelperBroker::resetHelpers();
        $this->loader = new Zend_Loader_PluginLoader(
            array('Zend_Form_Decorator' => 'Zend/Form/Decorator')
        );
        $this->group = new Zend_Form_DisplayGroup(
            'test',
            $this->loader
        );
    }

    public function tearDown()
    {
    }

    public function getView()
    {
        $view = new Zend_View();
        $libPath = dirname(__FILE__) . '/../../../library';
        $view->addHelperPath($libPath . '/Zend/View/Helper');
        return $view;
    }

    // General
    public function testConstructorRequiresNameAndPluginLoader()
    {
        $this->assertEquals('test', $this->group->getName());
        $this->assertSame($this->loader, $this->group->getPluginLoader());
    }

    public function testSetNameNormalizesValueToContainOnlyValidVariableCharacters()
    {
        $this->group->setName('f%\o^&*)o\(%$b#@!.a}{;-,r');
        $this->assertEquals('foobar', $this->group->getName());

        try {
            $this->group->setName('%\^&*)\(%$#@!.}{;-,');
            $this->fail('Empty names should raise exception');
        } catch (Zend_Form_Exception $e) {
            $this->assertContains('Invalid name provided', $e->getMessage());
        }
    }

    public function testZeroIsAValidGroupName()
    {
        try {
            $this->group->setName(0);
            $this->assertSame('0', $this->group->getName());
        } catch (Zend_Form_Exception $e) {
            $this->fail('Should allow zero as group name');
        }
    }

    public function testOrderNullByDefault()
    {
        $this->assertNull($this->group->getOrder());
    }

    public function testCanSetOrder()
    {
        $this->testOrderNullByDefault();
        $this->group->setOrder(50);
        $this->assertEquals(50, $this->group->getOrder());
    }

    public function testDescriptionInitiallyNull()
    {
        $this->assertNull($this->group->getDescription());
    }

    public function testCanSetDescription()
    {
        $this->testDescriptionInitiallyNull();
        $description = "this is a description";
        $this->group->setDescription($description);
        $this->assertEquals($description, $this->group->getDescription());
    }

    // Elements

    public function testPassingInvalidElementsToAddElementsThrowsException()
    {
        $elements = array('foo' => true);
        try {
            $this->group->addElements($elements);
            $this->fail('Invalid elements should raise exception');
        } catch (Zend_Form_Exception $e) {
            $this->assertContains('must be Zend_Form_Elements only', $e->getMessage());
        }
    }

    public function testCanAddElements()
    {
        $foo = new Zend_Form_Element('foo');
        $this->group->addElement($foo);
        $element = $this->group->getElement('foo');
        $this->assertSame($foo, $element);
    }

    public function testCanAddMultipleElements()
    {
        $foo = new Zend_Form_Element('foo');
        $bar = new Zend_Form_Element('bar');
        $this->group->addElements(array($foo, $bar));
        $elements = $this->group->getElements();
        $this->assertEquals(array('foo' => $foo, 'bar' => $bar), $elements);
    }

    public function testSetElementsOverWritesExistingElements()
    {
        $this->testCanAddMultipleElements();
        $baz = new Zend_Form_Element('baz');
        $this->group->setElements(array($baz));
        $elements = $this->group->getElements();
        $this->assertEquals(array('baz' => $baz), $elements);
    }

    public function testCanRemoveSingleElements()
    {
        $this->testCanAddMultipleElements();
        $this->group->removeElement('bar');
        $this->assertNull($this->group->getElement('bar'));
    }

    public function testRemoveElementReturnsFalseIfElementNotRegistered()
    {
        $this->assertFalse($this->group->removeElement('bar'));
    }

    public function testCanRemoveAllElements()
    {
        $this->testCanAddMultipleElements();
        $this->group->clearElements();
        $elements = $this->group->getElements();
        $this->assertTrue(is_array($elements));
        $this->assertTrue(empty($elements));
    }

    // Plugin loader

    public function testCanSetPluginLoader()
    {
        $loader = new Zend_Loader_PluginLoader();
        $this->group->setPluginLoader($loader);
        $this->assertSame($loader, $this->group->getPluginLoader());
    }

    // Decorators

    public function testDefaultDecoratorsRegistered()
    {
        $this->_checkZf2794();

        $decorator = $this->group->getDecorator('FormElements');
        $this->assertTrue($decorator instanceof Zend_Form_Decorator_FormElements);
        $decorator = $this->group->getDecorator('Fieldset');
        $this->assertTrue($decorator instanceof Zend_Form_Decorator_Fieldset);
    }

    public function testCanDisableRegisteringDefaultDecoratorsDuringInitialization()
    {
        $group = new Zend_Form_DisplayGroup(
            'test',
            $this->loader,
            array('disableLoadDefaultDecorators' => true)
        );
        $decorators = $group->getDecorators();
        $this->assertEquals(array(), $decorators);
    }

    public function testAddingInvalidDecoratorThrowsException()
    {
        try {
            $this->group->addDecorator(123);
            $this->fail('Invalid decorator should raise exception');
        } catch (Zend_Form_Exception $e) {
            $this->assertContains('Invalid decorator', $e->getMessage());
        }
    }

    public function testCanAddSingleDecoratorAsString()
    {
        $this->_checkZf2794();

        $this->group->clearDecorators();
        $this->assertFalse($this->group->getDecorator('form'));

        $this->group->addDecorator('viewHelper');
        $decorator = $this->group->getDecorator('viewHelper');
        $this->assertTrue($decorator instanceof Zend_Form_Decorator_ViewHelper);
    }

    public function testCanNotRetrieveSingleDecoratorRegisteredAsStringUsingClassName()
    {
        $this->assertFalse($this->group->getDecorator('Zend_Form_Decorator_FormElements'));
    }

    public function testCanAddSingleDecoratorAsDecoratorObject()
    {
        $this->group->clearDecorators();
        $this->assertFalse($this->group->getDecorator('form'));

        $decorator = new Zend_Form_Decorator_ViewHelper;
        $this->group->addDecorator($decorator);
        $test = $this->group->getDecorator('Zend_Form_Decorator_ViewHelper');
        $this->assertSame($decorator, $test);
    }

    public function testCanRetrieveSingleDecoratorRegisteredAsDecoratorObjectUsingShortName()
    {
        $this->_checkZf2794();

        $this->group->clearDecorators();
        $this->assertFalse($this->group->getDecorator('form'));

        $decorator = new Zend_Form_Decorator_Form;
        $this->group->addDecorator($decorator);
        $test = $this->group->getDecorator('form');
        $this->assertSame($decorator, $test);
    }

    public function testCanAddMultipleDecorators()
    {
        $this->_checkZf2794();

        $this->group->clearDecorators();
        $this->assertFalse($this->group->getDecorator('form'));

        $testDecorator = new Zend_Form_Decorator_HtmlTag;
        $this->group->addDecorators(array(
            'ViewHelper',
            $testDecorator
        ));

        $viewHelper = $this->group->getDecorator('viewHelper');
        $this->assertTrue($viewHelper instanceof Zend_Form_Decorator_ViewHelper);
        $decorator = $this->group->getDecorator('HtmlTag');
        $this->assertSame($testDecorator, $decorator);
    }

    public function testCanRemoveDecorator()
    {
        $this->_checkZf2794();

        $this->testDefaultDecoratorsRegistered();
        $this->group->removeDecorator('form');
        $this->assertFalse($this->group->getDecorator('form'));
    }

    /**
     * @see ZF-3069
     */
    public function testRemovingNamedDecoratorsShouldWork()
    {
        $this->_checkZf2794();
        $this->group->setDecorators(array(
            'FormElements',
            array(array('div' => 'HtmlTag'), array('tag' => 'div')),
            array(array('div2' => 'HtmlTag'), array('tag' => 'div')),
        ));
        $decorators = $this->group->getDecorators();
        $this->assertTrue(array_key_exists('div', $decorators));
        $this->assertTrue(array_key_exists('div2', $decorators));
        $this->group->removeDecorator('div');
        $decorators = $this->group->getDecorators();
        $this->assertFalse(array_key_exists('div', $decorators));
        $this->assertTrue(array_key_exists('div2', $decorators));
    }


    public function testCanClearAllDecorators()
    {
        $this->_checkZf2794();

        $this->testCanAddMultipleDecorators();
        $this->group->clearDecorators();
        $this->assertFalse($this->group->getDecorator('viewHelper'));
        $this->assertFalse($this->group->getDecorator('HtmlTag'));
    }

    public function testCanAddDecoratorAliasesToAllowMultipleDecoratorsOfSameType()
    {
        $this->_checkZf2794();

        $this->group->setDecorators(array(
            array('HtmlTag', array('tag' => 'fieldset')),
            array('decorator' => array('FooBar' => 'HtmlTag'), 'options' => array('tag' => 'dd')),
        ));
        $decorator = $this->group->getDecorator('FooBar');
        $this->assertTrue($decorator instanceof Zend_Form_Decorator_HtmlTag);
        $this->assertEquals('dd', $decorator->getOption('tag'));

        $decorator = $this->group->getDecorator('HtmlTag');
        $this->assertTrue($decorator instanceof Zend_Form_Decorator_HtmlTag);
        $this->assertEquals('fieldset', $decorator->getOption('tag'));
    }

    /**
     * @see ZF-3494
     */
    public function testGetViewShouldNotReturnNullWhenViewRendererIsActive()
    {
        require_once 'Zend/Controller/Action/HelperBroker.php';
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('ViewRenderer');
        $viewRenderer->initView();
        $view = $this->group->getView();
        $this->assertSame($viewRenderer->view, $view);
    }

    public function testRetrievingNamedDecoratorShouldNotReorderDecorators()
    {
        $this->group->setDecorators(array(
            'FormElements',
            array(array('dl' => 'HtmlTag'), array('tag' => 'dl')),
            array(array('div' => 'HtmlTag'), array('tag' => 'div')),
            array(array('fieldset' => 'HtmlTag'), array('tag' => 'fieldset')),
        ));

        $decorator  = $this->group->getDecorator('div');
        $decorators = $this->group->getDecorators();
        $i          = 0;
        $order      = array();

        foreach (array_keys($decorators) as $name) {
            $order[$name] = $i;
            ++$i;
        }
        $this->assertEquals(2, $order['div'], var_export($order, 1));
    }

    public function testRenderingRendersAllElementsWithinFieldsetByDefault()
    {
        $foo  = new Zend_Form_Element_Text('foo');
        $bar  = new Zend_Form_Element_Text('bar');

        $this->group->addElements(array($foo, $bar));
        $html = $this->group->render($this->getView());
        $this->assertRegexp('#^<dt[^>]*>&nbsp;</dt><dd[^>]*><fieldset.*?</fieldset></dd>$#s', $html, $html);
        $this->assertContains('<input', $html, $html);
        $this->assertContains('"foo"', $html);
        $this->assertContains('"bar"', $html);
    }

    public function testToStringProxiesToRender()
    {
        $foo  = new Zend_Form_Element_Text('foo');
        $bar  = new Zend_Form_Element_Text('bar');

        $this->group->addElements(array($foo, $bar))
                    ->setView($this->getView());
        $html = $this->group->__toString();
        $this->assertRegexp('#^<dt[^>]*>&nbsp;</dt><dd[^>]*><fieldset.*?</fieldset></dd>$#s', $html, $html);
        $this->assertContains('<input', $html);
        $this->assertContains('"foo"', $html);
        $this->assertContains('"bar"', $html);
    }

    public function raiseDecoratorException($content, $element, $options)
    {
        throw new Exception('Raising exception in decorator callback');
    }

    public function handleDecoratorErrors($errno, $errstr, $errfile = '', $errline = 0, array $errcontext = array())
    {
        $this->error = $errstr;
    }

    public function testToStringRaisesErrorWhenExceptionCaught()
    {
        $this->group->setDecorators(array(
            array(
                'decorator' => 'Callback',
                'options'   => array('callback' => array($this, 'raiseDecoratorException'))
            ),
        ));
        $origErrorHandler = set_error_handler(array($this, 'handleDecoratorErrors'), E_USER_WARNING);

        $text = $this->group->__toString();

        restore_error_handler();

        $this->assertTrue(empty($text));
        $this->assertTrue(isset($this->error));
        $this->assertEquals('Raising exception in decorator callback', $this->error);
    }

    public function testNoTranslatorByDefault()
    {
        $this->assertNull($this->group->getTranslator());
    }

    public function testGetTranslatorRetrievesGlobalDefaultWhenAvailable()
    {
        $this->testNoTranslatorByDefault();
        $translator = new Zend_Translate('array', array('foo' => 'bar'));
        Zend_Form::setDefaultTranslator($translator);
        $received = $this->group->getTranslator();
        $this->assertSame($translator->getAdapter(), $received);
    }

    public function testTranslatorAccessorsWorks()
    {
        $translator = new Zend_Translate('array', array('foo' => 'bar'));
        $this->group->setTranslator($translator);
        $received = $this->group->getTranslator($translator);
        $this->assertSame($translator->getAdapter(), $received);
    }

    public function testCanDisableTranslation()
    {
        $this->testGetTranslatorRetrievesGlobalDefaultWhenAvailable();
        $this->group->setDisableTranslator(true);
        $this->assertNull($this->group->getTranslator());
    }

    // Iteration

    public function setupIteratorElements()
    {
        $foo = new Zend_Form_Element('foo');
        $bar = new Zend_Form_Element('bar');
        $baz = new Zend_Form_Element('baz');
        $this->group->addElements(array($foo, $bar, $baz));
    }

    public function testDisplayGroupIsIterableAndIteratesElements()
    {
        $this->setupIteratorElements();
        $expected = array('foo', 'bar', 'baz');
        $received = array();
        foreach ($this->group as $key => $element) {
            $received[] = $key;
            $this->assertTrue($element instanceof Zend_Form_Element);
        }
        $this->assertSame($expected, $received);
    }

    public function testDisplayGroupIteratesElementsInExpectedOrder()
    {
        $this->setupIteratorElements();
        $test = new Zend_Form_Element('checkorder', array('order' => 1));
        $this->group->addElement($test);
        $expected = array('foo', 'checkorder', 'bar', 'baz');
        $received = array();
        foreach ($this->group as $key => $element) {
            $received[] = $key;
        }
        $this->assertSame($expected, $received);
    }

    public function testDisplayGroupIteratesElementsInExpectedOrderWhenFirstElementHasNoOrderSpecified()
    {
        $a = new Zend_Form_Element('a',array('label'=>'a'));
        $b = new Zend_Form_Element('b',array('label'=>'b', 'order' => 0));
        $c = new Zend_Form_Element('c',array('label'=>'c', 'order' => 1));
        $this->group->addElement($a)
                    ->addElement($b)
                    ->addElement($c)
                    ->setView($this->getView());
        $test = $this->group->render();
        $this->assertContains('name="a"', $test);
        if (!preg_match_all('/(<input[^>]+>)/', $test, $matches)) {
            $this->fail('Expected markup not found');
        }
        $order = array();
        foreach ($matches[1] as $element) {
            if (preg_match('/name="(a|b|c)"/', $element, $m)) {
                $order[] = $m[1];
            }
        }
        $this->assertSame(array('b', 'c', 'a'), $order);
    }

    public function testRemovingElementsShouldNotRaiseExceptionsDuringIteration()
    {
        $this->setupIteratorElements();
        $bar = $this->group->getElement('bar');
        $this->group->removeElement('bar');

        try {
            foreach ($this->group as $item) {
            }
        } catch (Exception $e) {
            $this->fail('Exceptions should not be raised by iterator when elements are removed; error message: ' . $e->getMessage());
        }
    }

    // Countable

    public function testCanCountDisplayGroup()
    {
        $this->setupIteratorElements();
        $this->assertEquals(3, count($this->group));
    }

    // Configuration

    public function getOptions()
    {
        $options = array(
            'name'   => 'foo',
            'legend' => 'Display Group',
            'order'  => 20,
            'class'  => 'foobar'
        );
        return $options;
    }

    public function testCanSetObjectStateViaSetOptions()
    {
        $this->group->setOptions($this->getOptions());
        $this->assertEquals('foo', $this->group->getName());
        $this->assertEquals('Display Group', $this->group->getLegend());
        $this->assertEquals(20, $this->group->getOrder());
        $this->assertEquals('foobar', $this->group->getAttrib('class'));
    }

    public function testSetOptionsOmitsAccessorsRequiringObjectsOrMultipleParams()
    {
        $options = $this->getOptions();
        $config  = new Zend_Config($options);
        $options['config']       = $config;
        $options['options']      = $config->toArray();
        $options['pluginLoader'] = true;
        $options['view']         = true;
        $options['translator']   = true;
        $options['attrib']       = true;
        $this->group->setOptions($options);
    }

    public function testSetOptionsSetsArrayOfStringDecorators()
    {
        $this->_checkZf2794();

        $options = $this->getOptions();
        $options['decorators'] = array('label', 'form');
        $this->group->setOptions($options);
        $this->assertFalse($this->group->getDecorator('group'));

        $decorator = $this->group->getDecorator('label');
        $this->assertTrue($decorator instanceof Zend_Form_Decorator_Label);
        $decorator = $this->group->getDecorator('form');
        $this->assertTrue($decorator instanceof Zend_Form_Decorator_Form);
    }

    public function testSetOptionsSetsArrayOfArrayDecorators()
    {
        $this->_checkZf2794();

        $options = $this->getOptions();
        $options['decorators'] = array(
            array('label', array('id' => 'mylabel')),
            array('form', array('id' => 'form')),
        );
        $this->group->setOptions($options);
        $this->assertFalse($this->group->getDecorator('group'));

        $decorator = $this->group->getDecorator('label');
        $this->assertTrue($decorator instanceof Zend_Form_Decorator_Label);
        $options = $decorator->getOptions();
        $this->assertEquals('mylabel', $options['id']);

        $decorator = $this->group->getDecorator('form');
        $this->assertTrue($decorator instanceof Zend_Form_Decorator_Form);
        $options = $decorator->getOptions();
        $this->assertEquals('form', $options['id']);
    }

    public function testSetOptionsSetsArrayOfAssocArrayDecorators()
    {
        $this->_checkZf2794();

        $options = $this->getOptions();
        $options['decorators'] = array(
            array(
                'options'   => array('id' => 'mylabel'),
                'decorator' => 'label',
            ),
            array(
                'options'   => array('id' => 'form'),
                'decorator' => 'form',
            ),
        );
        $this->group->setOptions($options);
        $this->assertFalse($this->group->getDecorator('group'));

        $decorator = $this->group->getDecorator('label');
        $this->assertTrue($decorator instanceof Zend_Form_Decorator_Label);
        $options = $decorator->getOptions();
        $this->assertEquals('mylabel', $options['id']);

        $decorator = $this->group->getDecorator('form');
        $this->assertTrue($decorator instanceof Zend_Form_Decorator_Form);
        $options = $decorator->getOptions();
        $this->assertEquals('form', $options['id']);
    }

    public function testCanSetObjectStateViaSetConfig()
    {
        $config = new Zend_Config($this->getOptions());
        $this->group->setConfig($config);
        $this->assertEquals('foo', $this->group->getName());
        $this->assertEquals('Display Group', $this->group->getLegend());
        $this->assertEquals(20, $this->group->getOrder());
        $this->assertEquals('foobar', $this->group->getAttrib('class'));
    }

    public function testPassingConfigObjectToConstructorSetsObjectState()
    {
        $config = new Zend_Config($this->getOptions());
        $group  = new Zend_Form_DisplayGroup('foo', $this->loader, $config);
        $this->assertEquals('foo', $group->getName());
        $this->assertEquals('Display Group', $group->getLegend());
        $this->assertEquals(20, $group->getOrder());
        $this->assertEquals('foobar', $group->getAttrib('class'));
    }

    public function testGetAttribReturnsNullForUndefinedAttribs()
    {
        $this->assertNull($this->group->getAttrib('bogus'));
    }

    public function testCanAddMultipleAttribsSimultaneously()
    {
        $attribs = array(
            'foo' => 'fooval',
            'bar' => 'barval',
            'baz' => 'bazval'
        );
        $this->group->addAttribs($attribs);
        $this->assertEquals($attribs, $this->group->getAttribs());
    }

    public function testSetAttribsOverwritesPreviouslySetAttribs()
    {
        $this->testCanAddMultipleAttribsSimultaneously();
        $attribs = array(
            'foo' => 'valfoo',
            'bat' => 'batval'
        );
        $this->group->setAttribs($attribs);
        $this->assertEquals($attribs, $this->group->getAttribs());
    }

    public function testCanRemoveSingleAttrib()
    {
        $this->testCanAddMultipleAttribsSimultaneously();
        $this->group->removeAttrib('bar');
        $this->assertNull($this->group->getAttrib('bar'));
    }

    public function testCanClearAllAttribs()
    {
        $this->testCanAddMultipleAttribsSimultaneously();
        $this->group->clearAttribs();
        $this->assertEquals(array(), $this->group->getAttribs());
    }

    // Extension

    public function testInitCalledBeforeLoadDecorators()
    {
        $group = new Zend_Form_DisplayGroupTest_DisplayGroup(
            'test',
            $this->loader
        );
        $decorators = $group->getDecorators();
        $this->assertTrue(empty($decorators));
    }

    /**
     * @group ZF-3217
     */
    public function testGroupShouldOverloadToRenderDecorators()
    {
        $foo  = new Zend_Form_Element_Text('foo');
        $bar  = new Zend_Form_Element_Text('bar');
        $this->group->addElements(array($foo, $bar));

        $this->group->setView($this->getView());
        $html = $this->group->renderFormElements();
        foreach ($this->group->getElements() as $element) {
            $this->assertContains('id="' . $element->getFullyQualifiedName() . '"', $html, 'Received: ' . $html);
        }
        $this->assertNotContains('<dl', $html);
        $this->assertNotContains('<form', $html);

        $html = $this->group->renderFieldset('this is the content');
        $this->assertContains('<fieldset', $html);
        $this->assertContains('</fieldset>', $html);
        $this->assertContains('this is the content', $html);
    }

    /**
     * @group ZF-3217
     * @expectedException Zend_Form_Exception
     */
    public function testOverloadingToInvalidMethodsShouldThrowAnException()
    {
        $html = $this->group->bogusMethodCall();
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

class Zend_Form_DisplayGroupTest_DisplayGroup extends Zend_Form_DisplayGroup
{
    public function init()
    {
        $this->setDisableLoadDefaultDecorators(true);
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Form_DisplayGroupTest::main') {
    Zend_Form_DisplayGroupTest::main();
}
