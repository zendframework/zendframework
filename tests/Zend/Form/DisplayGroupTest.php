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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Form;

use Zend\Form\Form,
    Zend\Form\DisplayGroup,
    Zend\Form\Decorator,
    Zend\Form\Element,
    Zend\Config\Config,
    Zend\Loader\PrefixPathLoader as PluginLoader,
    Zend\Registry,
    Zend\Translator\Translator,
    Zend\View\Renderer\PhpRenderer as View;

/**
 * @category   Zend
 * @package    Zend_Form
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Form
 */
class DisplayGroupTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        Registry::_unsetInstance();
        Form::setDefaultTranslator(null);

        if (isset($this->error)) {
            unset($this->error);
        }

        $this->loader = new PluginLoader(
            array('Zend\Form\Decorator' => 'Zend/Form/Decorator')
        );
        $this->group = new DisplayGroup(
            'test',
            $this->loader
        );
    }

    public function getView()
    {
        $view = new View();
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

        $this->setExpectedException('Zend\Form\Exception\InvalidArgumentException', 'Invalid name provided');
        $this->group->setName('%\^&*)\(%$#@!.}{;-,');
    }

    public function testZeroIsAValidGroupName()
    {
        $this->group->setName(0);
        $this->assertSame('0', $this->group->getName());
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
        $this->setExpectedException('Zend\Form\Exception\InvalidArgumentException', 'must be Zend\Form\Elements only');
        $this->group->addElements($elements);
    }

    public function testCanAddElements()
    {
        $foo = new Element('foo');
        $this->group->addElement($foo);
        $element = $this->group->getElement('foo');
        $this->assertSame($foo, $element);
    }

    public function testCanAddMultipleElements()
    {
        $foo = new Element('foo');
        $bar = new Element('bar');
        $this->group->addElements(array($foo, $bar));
        $elements = $this->group->getElements();
        $this->assertEquals(array('foo' => $foo, 'bar' => $bar), $elements);
    }

    public function testSetElementsOverWritesExistingElements()
    {
        $this->testCanAddMultipleElements();
        $baz = new Element('baz');
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
        $loader = new PluginLoader();
        $this->group->setPluginLoader($loader);
        $this->assertSame($loader, $this->group->getPluginLoader());
    }

    // Decorators

    public function testDefaultDecoratorsRegistered()
    {
        $decorator = $this->group->getDecorator('FormElements');
        $this->assertTrue($decorator instanceof Decorator\FormElements);
        $decorator = $this->group->getDecorator('Fieldset');
        $this->assertTrue($decorator instanceof Decorator\Fieldset);
    }

    public function testCanDisableRegisteringDefaultDecoratorsDuringInitialization()
    {
        $group = new DisplayGroup(
            'test',
            $this->loader,
            array('disableLoadDefaultDecorators' => true)
        );
        $decorators = $group->getDecorators();
        $this->assertEquals(array(), $decorators);
    }

    public function testAddingInvalidDecoratorThrowsException()
    {
        $this->setExpectedException('Zend\Form\Exception\InvalidArgumentException', 'Invalid decorator');
        $this->group->addDecorator(123);
    }

    public function testCanAddSingleDecoratorAsString()
    {
        $this->group->clearDecorators();
        $this->assertFalse($this->group->getDecorator('formDecorator'));

        $this->group->addDecorator('viewHelper');
        $decorator = $this->group->getDecorator('viewHelper');
        $this->assertTrue($decorator instanceof Decorator\ViewHelper);
    }

    public function testCanNotRetrieveSingleDecoratorRegisteredAsStringUsingClassName()
    {
        $this->assertFalse($this->group->getDecorator('Zend\Form\Decorator\FormElements'));
    }

    public function testCanAddSingleDecoratorAsDecoratorObject()
    {
        $this->group->clearDecorators();
        $this->assertFalse($this->group->getDecorator('formDecorator'));

        $decorator = new Decorator\ViewHelper;
        $this->group->addDecorator($decorator);
        $test = $this->group->getDecorator('Zend\Form\Decorator\ViewHelper');
        $this->assertSame($decorator, $test);
    }

    public function testCanRetrieveSingleDecoratorRegisteredAsDecoratorObjectUsingShortName()
    {
        $this->group->clearDecorators();
        $this->assertFalse($this->group->getDecorator('formDecorator'));

        $decorator = new Decorator\FormDecorator;
        $this->group->addDecorator($decorator);
        $test = $this->group->getDecorator('formDecorator');
        $this->assertSame($decorator, $test);
    }

    public function testCanAddMultipleDecorators()
    {
        $this->group->clearDecorators();
        $this->assertFalse($this->group->getDecorator('formDecorator'));

        $testDecorator = new Decorator\HtmlTag;
        $this->group->addDecorators(array(
            'ViewHelper',
            $testDecorator
        ));

        $viewHelper = $this->group->getDecorator('viewHelper');
        $this->assertTrue($viewHelper instanceof Decorator\ViewHelper);
        $decorator = $this->group->getDecorator('HtmlTag');
        $this->assertSame($testDecorator, $decorator);
    }

    public function testCanRemoveDecorator()
    {
        $this->testDefaultDecoratorsRegistered();
        $this->group->removeDecorator('formDecorator');
        $this->assertFalse($this->group->getDecorator('formDecorator'));
    }

    /**
     * @group ZF-3069
     */
    public function testRemovingNamedDecoratorsShouldWork()
    {
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
        $this->testCanAddMultipleDecorators();
        $this->group->clearDecorators();
        $this->assertFalse($this->group->getDecorator('viewHelper'));
        $this->assertFalse($this->group->getDecorator('HtmlTag'));
    }

    public function testCanAddDecoratorAliasesToAllowMultipleDecoratorsOfSameType()
    {
        $this->group->setDecorators(array(
            array('HtmlTag', array('tag' => 'fieldset')),
            array('decorator' => array('FooBar' => 'HtmlTag'), 'options' => array('tag' => 'dd')),
        ));
        $decorator = $this->group->getDecorator('FooBar');
        $this->assertTrue($decorator instanceof Decorator\HtmlTag);
        $this->assertEquals('dd', $decorator->getOption('tag'));

        $decorator = $this->group->getDecorator('HtmlTag');
        $this->assertTrue($decorator instanceof Decorator\HtmlTag);
        $this->assertEquals('fieldset', $decorator->getOption('tag'));
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
        $foo  = new Element\Text('foo');
        $bar  = new Element\Text('bar');

        $this->group->addElements(array($foo, $bar));
        $html = $this->group->render($this->getView());
        $this->assertRegexp('#^<dt[^>]*>&\#160;</dt><dd[^>]*><fieldset.*?</fieldset></dd>$#s', $html, $html);
        $this->assertContains('<input', $html, $html);
        $this->assertContains('"foo"', $html);
        $this->assertContains('"bar"', $html);
    }

    public function testToStringProxiesToRender()
    {
        $foo  = new Element\Text('foo');
        $bar  = new Element\Text('bar');

        $this->group->addElements(array($foo, $bar))
                    ->setView($this->getView());
        $html = $this->group->__toString();
        $this->assertRegexp('#^<dt[^>]*>&\#160;</dt><dd[^>]*><fieldset.*?</fieldset></dd>$#s', $html, $html);
        $this->assertContains('<input', $html);
        $this->assertContains('"foo"', $html);
        $this->assertContains('"bar"', $html);
    }

    public function raiseDecoratorException($content, $element, $options)
    {
        throw new \Exception('Raising exception in decorator callback');
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
        $translator = new Translator('ArrayAdapter', array('foo' => 'bar'));
        Form::setDefaultTranslator($translator);
        $received = $this->group->getTranslator();
        $this->assertSame($translator->getAdapter(), $received);
    }

    public function testTranslatorAccessorsWorks()
    {
        $translator = new Translator('ArrayAdapter', array('foo' => 'bar'));
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
        $foo = new Element('foo');
        $bar = new Element('bar');
        $baz = new Element('baz');
        $this->group->addElements(array($foo, $bar, $baz));
    }

    public function testDisplayGroupIsIterableAndIteratesElements()
    {
        $this->setupIteratorElements();
        $expected = array('foo', 'bar', 'baz');
        $received = array();
        foreach ($this->group as $key => $element) {
            $received[] = $key;
            $this->assertTrue($element instanceof Element);
        }
        $this->assertSame($expected, $received);
    }

    public function testDisplayGroupIteratesElementsInExpectedOrder()
    {
        $this->setupIteratorElements();
        $test = new Element('checkorder', array('order' => 1));
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
        $a = new Element('a',array('label'=>'a'));
        $b = new Element('b',array('label'=>'b', 'order' => 0));
        $c = new Element('c',array('label'=>'c', 'order' => 1));
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
        } catch (\Exception $e) {
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
        $config  = new Config($options);
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
        $options = $this->getOptions();
        $options['decorators'] = array('label', 'formDecorator');
        $this->group->setOptions($options);
        $this->assertFalse($this->group->getDecorator('group'));

        $decorator = $this->group->getDecorator('label');
        $this->assertTrue($decorator instanceof Decorator\Label);
        $decorator = $this->group->getDecorator('formDecorator');
        $this->assertTrue($decorator instanceof Decorator\FormDecorator);
    }

    public function testSetOptionsSetsArrayOfArrayDecorators()
    {
        $options = $this->getOptions();
        $options['decorators'] = array(
            array('label', array('id' => 'mylabel')),
            array('formDecorator', array('id' => 'form')),
        );
        $this->group->setOptions($options);
        $this->assertFalse($this->group->getDecorator('group'));

        $decorator = $this->group->getDecorator('label');
        $this->assertTrue($decorator instanceof Decorator\Label);
        $options = $decorator->getOptions();
        $this->assertEquals('mylabel', $options['id']);

        $decorator = $this->group->getDecorator('formDecorator');
        $this->assertTrue($decorator instanceof Decorator\FormDecorator);
        $options = $decorator->getOptions();
        $this->assertEquals('form', $options['id']);
    }

    public function testSetOptionsSetsArrayOfAssocArrayDecorators()
    {
        $options = $this->getOptions();
        $options['decorators'] = array(
            array(
                'options'   => array('id' => 'mylabel'),
                'decorator' => 'label',
            ),
            array(
                'options'   => array('id' => 'form'),
                'decorator' => 'formDecorator',
            ),
        );
        $this->group->setOptions($options);
        $this->assertFalse($this->group->getDecorator('group'));

        $decorator = $this->group->getDecorator('label');
        $this->assertTrue($decorator instanceof Decorator\Label);
        $options = $decorator->getOptions();
        $this->assertEquals('mylabel', $options['id']);

        $decorator = $this->group->getDecorator('formDecorator');
        $this->assertTrue($decorator instanceof Decorator\FormDecorator);
        $options = $decorator->getOptions();
        $this->assertEquals('form', $options['id']);
    }

    public function testCanSetObjectStateViaSetConfig()
    {
        $config = new Config($this->getOptions());
        $this->group->setConfig($config);
        $this->assertEquals('foo', $this->group->getName());
        $this->assertEquals('Display Group', $this->group->getLegend());
        $this->assertEquals(20, $this->group->getOrder());
        $this->assertEquals('foobar', $this->group->getAttrib('class'));
    }

    public function testPassingConfigObjectToConstructorSetsObjectState()
    {
        $config = new Config($this->getOptions());
        $group  = new DisplayGroup('foo', $this->loader, $config);
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
        $group = new TestAsset\DisplayGroupEmpty(
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
        $foo  = new Element\Text('foo');
        $bar  = new Element\Text('bar');
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
     */
    public function testOverloadingToInvalidMethodsShouldThrowAnException()
    {
        $this->setExpectedException('Zend\Form\Exception\BadMethodCallException');
        $html = $this->group->bogusMethodCall();
    }

    /**
     * Prove the fluent interface on Zend_Form::loadDefaultDecorators
     *
     * @group ZF-9913
     */
    public function testFluentInterfaceOnLoadDefaultDecorators()
    {
        $this->assertSame($this->group, $this->group->loadDefaultDecorators());
    }

    /**
     * @group ZF-7552
     */
    public function testAddDecoratorsKeepsNonNumericKeyNames()
    {
        $this->group->addDecorators(array(array(array('td'  => 'HtmlTag'),
                                               array('tag' => 'td')),
                                         array(array('tr'  => 'HtmlTag'),
                                               array('tag' => 'tr')),
                                         array('HtmlTag', array('tag' => 'baz'))));
        $t1 = $this->group->getDecorators();
        $this->group->setDecorators($t1);
        $t2 = $this->group->getDecorators();
        $this->assertEquals($t1, $t2);
    }
}
