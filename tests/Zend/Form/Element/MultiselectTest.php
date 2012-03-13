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

namespace ZendTest\Form\Element;

use Zend\Form\Element\Multiselect as MultiselectElement,
    Zend\Form\Element\Multi as MultiElement,
    Zend\Form\Element\Xhtml as XhtmlElement,
    Zend\Form\Element,
    Zend\Form\Decorator,
    Zend\Config\Factory as ConfigFactory,
    Zend\Translator\Translator,
    Zend\View\Renderer\PhpRenderer as View;

/**
 * Test class for Zend_Form_Element_Multiselect
 *
 * @category   Zend
 * @package    Zend_Form
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Form
 */
class MultiselectTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        $this->element = new MultiselectElement('foo');
    }

    public function getView()
    {
        $view = new View();
        return $view;
    }

    public function testMultiselectElementInstanceOfMultiElement()
    {
        $this->assertTrue($this->element instanceof MultiElement);
    }

    public function testMultiselectElementInstanceOfXhtmlElement()
    {
        $this->assertTrue($this->element instanceof XhtmlElement);
    }

    public function testMultiselectElementInstanceOfBaseElement()
    {
        $this->assertTrue($this->element instanceof Element);
    }

    public function testMultiselectElementIsAnArrayByDefault()
    {
        $this->assertTrue($this->element->isArray());
    }

    public function testMultiselectElementUsesSelectHelperInViewHelperDecoratorByDefault()
    {
        $decorator = $this->element->getDecorator('viewHelper');
        $this->assertTrue($decorator instanceof Decorator\ViewHelper);
        $decorator->setElement($this->element);
        $helper = $decorator->getHelper();
        $this->assertEquals('formSelect', $helper);
    }

    public function testMultipleOptionSetByDefault()
    {
        $this->assertNotNull($this->element->multiple);
        $this->assertEquals('multiple', $this->element->multiple);
    }

    public function testHasDefaultSeparator()
    {
        $this->assertEquals('<br />', $this->element->getSeparator());
    }

    public function testCanSetSeparator()
    {
        $this->testHasDefaultSeparator();
        $this->element->setSeparator("\n");
        $this->assertEquals("\n", $this->element->getSeparator());
    }

    public function testMultiOptionsEmptyByDefault()
    {
        $options = $this->element->getMultiOptions();
        $this->assertTrue(is_array($options));
        $this->assertTrue(empty($options));
    }

    public function testCanSetMultiOptions()
    {
        $this->testMultiOptionsEmptyByDefault();
        $this->element->addMultiOption('foo', 'foovalue');
        $this->assertEquals('foovalue', $this->element->getMultiOption('foo'));
        $this->element->setMultiOptions(array('bar' => 'barvalue', 'baz' => 'bazvalue'));
        $this->assertEquals(array('bar' => 'barvalue', 'baz' => 'bazvalue'), $this->element->getMultiOptions());
        $this->element->addMultiOptions(array('bat' => 'batvalue', 'foo' => 'foovalue'));
        $this->assertEquals(array('bar' => 'barvalue', 'baz' => 'bazvalue', 'bat' => 'batvalue', 'foo' => 'foovalue'), $this->element->getMultiOptions());
        $this->element->addMultiOption('test', 'testvalue');
        $this->assertEquals(array('bar' => 'barvalue', 'baz' => 'bazvalue', 'bat' => 'batvalue', 'foo' => 'foovalue', 'test' => 'testvalue'), $this->element->getMultiOptions());
    }

    /**
     * @group ZF-2824
     */
    public function testCanSetMultiOptionsUsingAssocArraysWithKeyValueKeys()
    {
        $options = array(
            array(
                'value' => '1',
                'key'   => 'aa',
            ),
            array (
                'key'   => '2',
                'value' => 'xxxx',
            ),
            array (
                'value' => '444',
                'key'   => 'ssss',
            ),
        );
        $this->element->addMultiOptions($options);
        $this->assertEquals($options[0]['value'], $this->element->getMultiOption('aa'));
        $this->assertEquals($options[1]['value'], $this->element->getMultiOption(2));
        $this->assertEquals($options[2]['value'], $this->element->getMultiOption('ssss'));
    }

    /**
     * @group ZF-2824
     */
    public function testCanSetMultiOptionsUsingConfigWithKeyValueKeys()
    {
        $config = ConfigFactory::fromFile(__DIR__ . '/../TestAsset/config/multiOptions.xml', true)->get('testing');
        $this->element->setMultiOptions($config->options->toArray());
        $this->assertEquals($config->options->first->value, $this->element->getMultiOption('aa'));
        $this->assertEquals($config->options->second->value, $this->element->getMultiOption(2));
        $this->assertEquals($config->options->third->value, $this->element->getMultiOption('ssss'));

        $config = ConfigFactory::fromFile(__DIR__ . '/../TestAsset/config/multiOptions.ini', true)->get('testing');
        $this->element->setMultiOptions($config->options->toArray());
        $this->assertEquals($config->options->first->value, $this->element->getMultiOption('aa'));
        $this->assertEquals($config->options->second->value, $this->element->getMultiOption(2));
        $this->assertEquals($config->options->third->value, $this->element->getMultiOption('ssss'));

    }

    public function testCanRemoveMultiOption()
    {
        $this->testMultiOptionsEmptyByDefault();
        $this->element->addMultiOption('foo', 'foovalue');
        $this->assertEquals('foovalue', $this->element->getMultiOption('foo'));
        $this->element->removeMultiOption('foo');
        $this->assertNull($this->element->getMultiOption('foo'));
    }

    public function testOptionsAreRenderedInFinalMarkup()
    {
        $options = array(
            'foovalue' => 'Foo',
            'barvalue' => 'Bar'
        );
        $this->element->addMultiOptions($options);
        $html = $this->element->render($this->getView());
        foreach ($options as $value => $label) {
            $this->assertRegexp('/<option.*value="' . $value . '"[^>]*>' . $label . '/s', $html, $html);
        }
    }

    public function testTranslatedOptionsAreRenderedInFinalMarkupWhenTranslatorPresent()
    {
        $translations = array(
            'ThisShouldNotShow'   => 'Foo Value',
            'ThisShouldNeverShow' => 'Bar Value'
        );
        $translate = new Translator('ArrayAdapter', $translations, 'en');
        $translate->setLocale('en');

        $options = array(
            'foovalue' => 'ThisShouldNotShow',
            'barvalue' => 'ThisShouldNeverShow'
        );

        $this->element->setTranslator($translate)
                      ->addMultiOptions($options);

        $html = $this->element->render($this->getView());
        foreach ($options as $value => $label) {
            $this->assertNotContains($label, $html, $html);
            $this->assertRegexp('/<option.*value="' . $value . '"[^>]*>' . $translations[$label] . '/s', $html, $html);
        }
    }

    public function testOptionLabelsAreTranslatedWhenTranslateAdapterIsPresent()
    {
        $translations = include __DIR__ . '/../TestAsset/locale/array.php';
        $translate    = new Translator('ArrayAdapter', $translations, 'en');
        $translate->setLocale('en');

        $options = array(
            'foovalue' => 'Foo',
            'barvalue' => 'Bar'
        );
        $this->element->addMultiOptions($options)
                      ->setTranslator($translate);
        $test = $this->element->getMultiOption('barvalue');
        $this->assertEquals($translations[$options['barvalue']], $test);

        $test = $this->element->getMultiOptions();
        foreach ($test as $key => $value) {
            $this->assertEquals($translations[$options[$key]], $value);
        }
    }

    public function testOptionLabelsAreUntouchedIfTranslatonDoesNotExistInnTranslateAdapter()
    {
        $translations = include __DIR__ . '/../TestAsset/locale/array.php';
        $translate    = new Translator('ArrayAdapter', $translations, 'en');
        $translate->setLocale('en');

        $options = array(
            'foovalue' => 'Foo',
            'barvalue' => 'Bar',
            'testing'  => 'Test Value',
        );
        $this->element->addMultiOptions($options)
                      ->setTranslator($translate);
        $test = $this->element->getMultiOption('testing');
        $this->assertEquals($options['testing'], $test);
    }

    public function testMultiselectIsArrayByDefault()
    {
        $this->assertTrue($this->element->isArray());
    }

    /**
     * @group ZF-5568
     */
    public function testOptGroupTranslationsShouldWorkAfterPopulatingElement()
    {
        $translations = array(
            'ThisIsTheLabel'      => 'Optgroup label',
            'ThisShouldNotShow'   => 'Foo Value',
            'ThisShouldNeverShow' => 'Bar Value'
        );
        $translate = new Translator('ArrayAdapter', $translations, 'en');
        $translate->setLocale('en');

        $options = array(
            'ThisIsTheLabel' => array(
                'foovalue' => 'ThisShouldNotShow',
                'barvalue' => 'ThisShouldNeverShow',
            ),
        );

        $this->element->setTranslator($translate)
                      ->addMultiOptions($options);

        $this->element->setValue('barValue');

        $html = $this->element->render($this->getView());
        $this->assertContains($translations['ThisIsTheLabel'], $html, $html);
    }

    /**
     * @group ZF-5937
     */
    public function testAddMultiOptionShouldWorkAfterTranslatorIsDisabled()
    {
        $options = array(
            'foovalue' => 'Foo',
        );
        $this->element->setDisableTranslator(true)
                      ->addMultiOptions($options);
        $test = $this->element->getMultiOption('foovalue');
        $this->assertEquals($options['foovalue'], $test);
    }
}
