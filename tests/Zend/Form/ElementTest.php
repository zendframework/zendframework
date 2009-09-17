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
    define('PHPUnit_MAIN_METHOD', 'Zend_Form_ElementTest::main');
}

require_once dirname(__FILE__) . '/../../TestHelper.php';

// error_reporting(E_ALL);

require_once 'Zend/Form/Element.php';

require_once 'Zend/Config.php';
require_once 'Zend/Controller/Action/HelperBroker.php';
require_once 'Zend/Form.php';
require_once 'Zend/Form/Decorator/Abstract.php';
require_once 'Zend/Form/Decorator/HtmlTag.php';
require_once 'Zend/Loader/PluginLoader.php';
require_once 'Zend/Translate.php';
require_once 'Zend/Validate/NotEmpty.php';
require_once 'Zend/Validate/EmailAddress.php';
require_once 'Zend/View.php';

/**
 * @category   Zend
 * @package    Zend_Form
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Form
 */
class Zend_Form_ElementTest extends PHPUnit_Framework_TestCase
{
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite('Zend_Form_ElementTest');
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function setUp()
    {
        Zend_Form::setDefaultTranslator(null);

        if (isset($this->error)) {
            unset($this->error);
        }

        $this->element = new Zend_Form_Element('foo');
        Zend_Controller_Action_HelperBroker::resetHelpers();
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

    public function testConstructorRequiresMinimallyElementName()
    {
        try {
            $element = new Zend_Form_Element(1);
            $this->fail('Zend_Form_Element constructor should not accept integer argument');
        } catch (Zend_Form_Exception $e) {
        }
        try {
            $element = new Zend_Form_Element(true);
            $this->fail('Zend_Form_Element constructor should not accept boolean argument');
        } catch (Zend_Form_Exception $e) {
        }

        try {
            $element = new Zend_Form_Element('foo');
        } catch (Exception $e) {
            $this->fail('Zend_Form_Element constructor should accept String values');
        }

        $config = array('foo' => 'bar');
        try {
            $element = new Zend_Form_Element($config);
            $this->fail('Zend_Form_Element constructor requires array with name element');
        } catch (Zend_Form_Exception $e) {
        }

        $config = array('name' => 'bar');
        try {
            $element = new Zend_Form_Element($config);
        } catch (Zend_Form_Exception $e) {
            $this->fail('Zend_Form_Element constructor should accept array with name element');
        }

        $config = new Zend_Config(array('foo' => 'bar'));
        try {
            $element = new Zend_Form_Element($config);
            $this->fail('Zend_Form_Element constructor requires Zend_Config object with name element');
        } catch (Zend_Form_Exception $e) {
        }

        $config = new Zend_Config(array('name' => 'bar'));
        try {
            $element = new Zend_Form_Element($config);
        } catch (Zend_Form_Exception $e) {
            $this->fail('Zend_Form_Element constructor should accept Zend_Config with name element');
        }
    }

    public function testNoTranslatorByDefault()
    {
        $this->assertNull($this->element->getTranslator());
    }

    public function testGetTranslatorRetrievesGlobalDefaultWhenAvailable()
    {
        $this->testNoTranslatorByDefault();
        $translator = new Zend_Translate('array', array('foo' => 'bar'));
        Zend_Form::setDefaultTranslator($translator);
        $received = $this->element->getTranslator();
        $this->assertSame($translator->getAdapter(), $received);
    }

    public function testTranslatorAccessorsWork()
    {
        $translator = new Zend_Translate('array', array('foo' => 'bar'));
        $this->element->setTranslator($translator);
        $received = $this->element->getTranslator($translator);
        $this->assertSame($translator->getAdapter(), $received);
    }

    public function testCanDisableTranslation()
    {
        $this->testGetTranslatorRetrievesGlobalDefaultWhenAvailable();
        $this->element->setDisableTranslator(true);
        $this->assertNull($this->element->getTranslator());
    }

    public function testSetNameNormalizesValueToContainOnlyValidVariableCharacters()
    {
        $this->element->setName('f%\o^&*)o\(%$b#@!.a}{;-,r');
        $this->assertEquals('foobar', $this->element->getName());

        try {
            $this->element->setName('%\^&*)\(%$#@!.}{;-,');
            $this->fail('Empty names should raise exception');
        } catch (Zend_Form_Exception $e) {
            $this->assertContains('Invalid name provided', $e->getMessage());
        }
    }

    public function testZeroIsAllowedAsElementName()
    {
        try {
            $this->element->setName(0);
            $this->assertSame('0', $this->element->getName());
        } catch (Zend_Form_Exception $e) {
            $this->fail('Should allow zero as element name');
        }
    }

    /**
     * @see ZF-2851
     */
    public function testSetNameShouldNotAllowEmptyString()
    {
        foreach (array('', ' ', '   ') as $name) {
            try {
                $this->element->setName($name);
                $this->fail('setName() should not allow empty string');
            } catch (Zend_Form_Exception $e) {
                $this->assertContains('Invalid name', $e->getMessage());
            }
        }
    }

    public function testElementValueInitiallyNull()
    {
        $this->assertNull($this->element->getValue());
    }

    public function testValueAccessorsWork()
    {
        $this->element->setValue('bar');
        $this->assertContains('bar', $this->element->getValue());
    }

    public function testGetValueFiltersValue()
    {
        $this->element->setValue('This 0 is 1 a-2-TEST')
                      ->addFilter('alnum')
                      ->addFilter('stringToUpper');
        $test = $this->element->getValue();
        $this->assertEquals('THIS0IS1A2TEST', $test);
    }

    public function checkFilterValues($item, $key)
    {
        $this->assertRegexp('/^[A-Z]+$/', $item);
    }

    public function testRetrievingArrayValueFiltersAllArrayValues()
    {
        $this->element->setValue(array(
                    'foo',
                    array(
                        'bar',
                        'baz'
                    ),
                    'bat'
                ))
             ->setIsArray(true)
             ->addFilter('StringToUpper');
        $test = $this->element->getValue();
        $this->assertTrue(is_array($test));
        array_walk_recursive($test, array($this, 'checkFilterValues'));
    }

    public function testRetrievingArrayValueDoesNotFilterAllValuesWhenNotIsArray()
    {
        $values = array(
            'foo',
            array(
                'bar',
                'baz'
            ),
            'bat'
        );
        $this->element->setValue($values)
                      ->addFilter(new Zend_Form_ElementTest_ArrayFilter());
        $test = $this->element->getValue();
        $this->assertTrue(is_array($test));
        require_once 'Zend/Json.php';
        $test = Zend_Json::encode($test);
        $this->assertNotContains('foo', $test);
        foreach (array('bar', 'baz', 'bat') as $value) {
            $this->assertContains($value, $test);
        }
    }

    public function testGetUnfilteredValueRetrievesOriginalValue()
    {
        $this->element->setValue('bar');
        $this->assertSame('bar', $this->element->getUnfilteredValue());
    }

    public function testLabelInitiallyNull()
    {
        $this->assertNull($this->element->getLabel());
    }

    public function testLabelAccessorsWork()
    {
        $this->element->setLabel('FooBar');
        $this->assertEquals('FooBar', $this->element->getLabel());
    }

    public function testOrderNullByDefault()
    {
        $this->assertNull($this->element->getOrder());
    }

    public function testCanSetOrder()
    {
        $this->testOrderNullByDefault();
        $this->element->setOrder(50);
        $this->assertEquals(50, $this->element->getOrder());
    }

    public function testRequiredFlagFalseByDefault()
    {
        $this->assertFalse($this->element->isRequired());
    }

    public function testRequiredAcccessorsWork()
    {
        $this->assertFalse($this->element->isRequired());
        $this->element->setRequired(true);
        $this->assertTrue($this->element->isRequired());
    }

    public function testIsValidInsertsNotEmptyValidatorWhenElementIsRequiredByDefault()
    {
        $this->_checkZf2794();

        $this->element->setRequired(true);
        $this->assertFalse($this->element->isValid(''));
        $validator = $this->element->getValidator('NotEmpty');
        $this->assertTrue($validator instanceof Zend_Validate_NotEmpty);
        $this->assertTrue($validator->zfBreakChainOnFailure);
    }

    /**
     * @see ZF-2862
     */
    public function testBreakChainOnFailureFlagsForExistingValidatorsRemainSetWhenNotEmptyValidatorAutoInserted()
    {
        $this->_checkZf2794();

        $username = new Zend_Form_Element('username');
        $username->addValidator('stringLength', true, array(5, 20))
                 ->addValidator('regex', true, array('/^[a-zA-Z0-9_]*$/'))
                 ->addFilter('StringToLower')
                 ->setRequired(true);
        $form = new Zend_Form(array('elements' => array($username)));
        $form->isValid(array('username' => '#'));

        $validator = $username->getValidator('stringLength');
        $this->assertTrue($validator->zfBreakChainOnFailure);
        $validator = $username->getValidator('regex');
        $this->assertTrue($validator->zfBreakChainOnFailure);
    }

    public function testAutoInsertNotEmptyValidatorFlagTrueByDefault()
    {
        $this->assertTrue($this->element->autoInsertNotEmptyValidator());
    }

    public function testCanSetAutoInsertNotEmptyValidatorFlag()
    {
        $this->testAutoInsertNotEmptyValidatorFlagTrueByDefault();
        $this->element->setAutoInsertNotEmptyValidator(false);
        $this->assertFalse($this->element->autoInsertNotEmptyValidator());
        $this->element->setAutoInsertNotEmptyValidator(true);
        $this->assertTrue($this->element->autoInsertNotEmptyValidator());
    }

    public function testIsValidDoesNotInsertNotEmptyValidatorWhenElementIsRequiredButAutoInsertNotEmptyValidatorFlagIsFalse()
    {
        $this->element->setAutoInsertNotEmptyValidator(false)
             ->setRequired(true);
        $this->assertTrue($this->element->isValid(''));
    }

    public function testDescriptionInitiallyNull()
    {
        $this->assertNull($this->element->getDescription());
    }

    public function testCanSetDescription()
    {
        $this->testDescriptionInitiallyNull();
        $this->element->setDescription('element hint');
        $this->assertEquals('element hint', $this->element->getDescription());
    }

    public function testElementIsNotArrayByDefault()
    {
        $this->assertFalse($this->element->isArray());
    }

    public function testCanSetArrayFlag()
    {
        $this->testElementIsNotArrayByDefault();
        $this->element->setIsArray(true);
        $this->assertTrue($this->element->isArray());
        $this->element->setIsArray(false);
        $this->assertFalse($this->element->isArray());
    }

    public function testElementBelongsToNullByDefault()
    {
        $this->assertNull($this->element->getBelongsTo());
    }

    public function testCanSetArrayElementBelongsTo()
    {
        $this->testElementBelongsToNullByDefault();
        $this->element->setBelongsTo('foo');
        $this->assertEquals('foo', $this->element->getBelongsTo());
    }

    public function testArrayElementBelongsToNormalizedToValidVariableCharactersOnly()
    {
        $this->testElementBelongsToNullByDefault();
        $this->element->setBelongsTo('f%\o^&*)o\(%$b#@!.a}{;-,r');
        $this->assertEquals('foobar', $this->element->getBelongsTo());
    }

    public function testGetTypeReturnsCurrentElementClass()
    {
        $this->assertEquals('Zend_Form_Element', $this->element->getType());
    }

    public function testCanUseAccessorsToSetIndidualAttribs()
    {
        $this->element->setAttrib('foo', 'bar')
                      ->setAttrib('bar', 'baz')
                      ->setAttrib('baz', 'bat');

        $this->assertEquals('bar', $this->element->getAttrib('foo'));
        $this->assertEquals('baz', $this->element->getAttrib('bar'));
        $this->assertEquals('bat', $this->element->getAttrib('baz'));
    }

    public function testGetUndefinedAttribShouldReturnNull()
    {
        $this->assertNull($this->element->getAttrib('bogus'));
    }

    public function testSetAttribThrowsExceptionsForKeysWithLeadingUnderscores()
    {
        try {
            $this->element->setAttrib('_foo', 'bar');
            $this->fail('setAttrib() should throw an exception for invalid keys');
        } catch (Zend_Form_Exception $e) {
            $this->assertContains('Invalid attribute', $e->getMessage());
        }
    }

    public function testPassingNullValueToSetAttribUnsetsAttrib()
    {
        $this->element->setAttrib('foo', 'bar');
        $this->assertEquals('bar', $this->element->getAttrib('foo'));
        $this->element->setAttrib('foo', null);
        $this->assertFalse(isset($this->element->foo));
    }

    public function testSetAttribsSetsMultipleAttribs()
    {
        $this->element->setAttribs(array(
            'foo' => 'bar',
            'bar' => 'baz',
            'baz' => 'bat'
        ));

        $this->assertEquals('bar', $this->element->getAttrib('foo'));
        $this->assertEquals('baz', $this->element->getAttrib('bar'));
        $this->assertEquals('bat', $this->element->getAttrib('baz'));
    }

    public function testGetAttribsRetrievesAllAttributes()
    {
        $attribs = array(
            'foo' => 'bar',
            'bar' => 'baz',
            'baz' => 'bat'
        );
        $this->element->setAttribs($attribs);

        $attribs['helper'] = 'formText';

        $received = $this->element->getAttribs();
        $this->assertEquals($attribs, $received);
    }

    public function testPassingNullValuesToSetAttribsUnsetsAttribs()
    {
        $this->testSetAttribsSetsMultipleAttribs();
        $this->element->setAttribs(array('foo' => null));
        $this->assertNull($this->element->foo);
    }

    public function testRetrievingOverloadedValuesThrowsExceptionWithInvalidKey()
    {
        try {
            $name = $this->element->_name;
            $this->fail('Overloading should not return protected or private members');
        } catch (Zend_Form_Exception $e) {
            $this->assertContains('Cannot retrieve value for protected/private', $e->getMessage());
        }
    }

    public function testCanSetAndRetrieveAttribsViaOverloading()
    {
        $this->element->foo = 'bar';
        $this->assertEquals('bar', $this->element->foo);
    }

    public function testGetPluginLoaderRetrievesDefaultValidatorPluginLoader()
    {
        $loader = $this->element->getPluginLoader('validate');
        $this->assertTrue($loader instanceof Zend_Loader_PluginLoader);
        $paths = $loader->getPaths('Zend_Validate');
        $this->assertTrue(is_array($paths), var_export($loader, 1));
        $this->assertTrue(0 < count($paths));
        $this->assertContains('Validate', $paths[0]);
    }

    public function testGetPluginLoaderRetrievesDefaultFilterPluginLoader()
    {
        $loader = $this->element->getPluginLoader('filter');
        $this->assertTrue($loader instanceof Zend_Loader_PluginLoader);
        $paths = $loader->getPaths('Zend_Filter');
        $this->assertTrue(is_array($paths));
        $this->assertTrue(0 < count($paths));
        $this->assertContains('Filter', $paths[0]);
    }

    public function testGetPluginLoaderRetrievesDefaultDecoratorPluginLoader()
    {
        $loader = $this->element->getPluginLoader('decorator');
        $this->assertTrue($loader instanceof Zend_Loader_PluginLoader);
        $paths = $loader->getPaths('Zend_Form_Decorator');
        $this->assertTrue(is_array($paths));
        $this->assertTrue(0 < count($paths));
        $this->assertContains('Decorator', $paths[0]);
    }

    public function testCanSetCustomValidatorPluginLoader()
    {
        $loader = new Zend_Loader_PluginLoader();
        $this->element->setPluginLoader($loader, 'validate');
        $test = $this->element->getPluginLoader('validate');
        $this->assertSame($loader, $test);
    }

    public function testPassingInvalidTypeToSetPluginLoaderThrowsException()
    {
        $loader = new Zend_Loader_PluginLoader();
        try {
            $this->element->setPluginLoader($loader, 'foo');
            $this->fail('Invalid loader type should raise exception');
        } catch (Zend_Form_Exception $e) {
            $this->assertContains('Invalid type', $e->getMessage());
        }
    }

    public function testPassingInvalidTypeToGetPluginLoaderThrowsException()
    {
        try {
            $this->element->getPluginLoader('foo');
            $this->fail('Invalid loader type should raise exception');
        } catch (Zend_Form_Exception $e) {
            $this->assertContains('Invalid type', $e->getMessage());
        }
    }

    public function testCanSetCustomFilterPluginLoader()
    {
        $loader = new Zend_Loader_PluginLoader();
        $this->element->setPluginLoader($loader, 'filter');
        $test = $this->element->getPluginLoader('filter');
        $this->assertSame($loader, $test);
    }

    public function testCanSetCustomDecoratorPluginLoader()
    {
        $loader = new Zend_Loader_PluginLoader();
        $this->element->setPluginLoader($loader, 'decorator');
        $test = $this->element->getPluginLoader('decorator');
        $this->assertSame($loader, $test);
    }

    public function testPassingInvalidLoaderTypeToAddPrefixPathThrowsException()
    {
        try {
            $this->element->addPrefixPath('Zend_Foo', 'Zend/Foo/', 'foo');
            $this->fail('Invalid loader type should raise exception');
        } catch (Zend_Form_Exception $e) {
            $this->assertContains('Invalid type', $e->getMessage());
        }
    }

    public function testCanAddValidatorPluginLoaderPrefixPath()
    {
        $loader = $this->element->getPluginLoader('validate');
        $this->element->addPrefixPath('Zend_Form', 'Zend/Form/', 'validate');
        $paths = $loader->getPaths('Zend_Form');
        $this->assertTrue(is_array($paths));
        $this->assertContains('Form', $paths[0]);
    }

    public function testAddingValidatorPluginLoaderPrefixPathDoesNotAffectOtherLoaders()
    {
        $validateLoader  = $this->element->getPluginLoader('validate');
        $filterLoader    = $this->element->getPluginLoader('filter');
        $decoratorLoader = $this->element->getPluginLoader('decorator');
        $this->element->addPrefixPath('Zend_Form', 'Zend/Form/', 'validate');
        $this->assertFalse($filterLoader->getPaths('Zend_Form'));
        $this->assertFalse($decoratorLoader->getPaths('Zend_Form'));
    }

    public function testCanAddFilterPluginLoaderPrefixPath()
    {
        $loader = $this->element->getPluginLoader('validate');
        $this->element->addPrefixPath('Zend_Form', 'Zend/Form/', 'validate');
        $paths = $loader->getPaths('Zend_Form');
        $this->assertTrue(is_array($paths));
        $this->assertContains('Form', $paths[0]);
    }

    public function testAddingFilterPluginLoaderPrefixPathDoesNotAffectOtherLoaders()
    {
        $filterLoader    = $this->element->getPluginLoader('filter');
        $validateLoader  = $this->element->getPluginLoader('validate');
        $decoratorLoader = $this->element->getPluginLoader('decorator');
        $this->element->addPrefixPath('Zend_Form', 'Zend/Form/', 'filter');
        $this->assertFalse($validateLoader->getPaths('Zend_Form'));
        $this->assertFalse($decoratorLoader->getPaths('Zend_Form'));
    }

    public function testCanAddDecoratorPluginLoaderPrefixPath()
    {
        $loader = $this->element->getPluginLoader('decorator');
        $this->element->addPrefixPath('Zend_Foo', 'Zend/Foo/', 'decorator');
        $paths = $loader->getPaths('Zend_Foo');
        $this->assertTrue(is_array($paths));
        $this->assertContains('Foo', $paths[0]);
    }

    public function testAddingDecoratorrPluginLoaderPrefixPathDoesNotAffectOtherLoaders()
    {
        $decoratorLoader = $this->element->getPluginLoader('decorator');
        $filterLoader    = $this->element->getPluginLoader('filter');
        $validateLoader  = $this->element->getPluginLoader('validate');
        $this->element->addPrefixPath('Zend_Foo', 'Zend/Foo/', 'decorator');
        $this->assertFalse($validateLoader->getPaths('Zend_Foo'));
        $this->assertFalse($filterLoader->getPaths('Zend_Foo'));
    }

    public function testCanAddAllPluginLoaderPrefixPathsSimultaneously()
    {
        $validatorLoader = new Zend_Loader_PluginLoader();
        $filterLoader    = new Zend_Loader_PluginLoader();
        $decoratorLoader = new Zend_Loader_PluginLoader();
        $this->element->setPluginLoader($validatorLoader, 'validate')
                      ->setPluginLoader($filterLoader, 'filter')
                      ->setPluginLoader($decoratorLoader, 'decorator')
                      ->addPrefixPath('Zend', 'Zend/');

        $paths = $filterLoader->getPaths('Zend_Filter');
        $this->assertTrue(is_array($paths));
        $this->assertContains('Filter', $paths[0]);

        $paths = $validatorLoader->getPaths('Zend_Validate');
        $this->assertTrue(is_array($paths));
        $this->assertContains('Validate', $paths[0]);

        $paths = $decoratorLoader->getPaths('Zend_Decorator');
        $this->assertTrue(is_array($paths), var_export($paths, 1));
        $this->assertContains('Decorator', $paths[0]);
    }

    public function testPassingInvalidValidatorToAddValidatorThrowsException()
    {
        try {
            $this->element->addValidator(123);
            $this->fail('Invalid validator should raise exception');
        } catch (Zend_Form_Exception $e) {
            $this->assertContains('Invalid validator', $e->getMessage());
        }
    }

    public function testCanAddSingleValidatorAsString()
    {
        $this->_checkZf2794();

        $this->assertFalse($this->element->getValidator('digits'));

        $this->element->addValidator('digits');
        $validator = $this->element->getValidator('digits');
        $this->assertTrue($validator instanceof Zend_Validate_Digits, var_export($validator, 1));
        $this->assertFalse($validator->zfBreakChainOnFailure);
    }

    public function testCanNotRetrieveSingleValidatorRegisteredAsStringUsingClassName()
    {
        $this->assertFalse($this->element->getValidator('digits'));

        $this->element->addValidator('digits');
        $this->assertFalse($this->element->getValidator('Zend_Validate_Digits'));
    }

    public function testCanAddSingleValidatorAsValidatorObject()
    {
        $this->assertFalse($this->element->getValidator('Zend_Validate_Digits'));

        require_once 'Zend/Validate/Digits.php';
        $validator = new Zend_Validate_Digits();
        $this->element->addValidator($validator);
        $test = $this->element->getValidator('Zend_Validate_Digits');
        $this->assertSame($validator, $test);
        $this->assertFalse($validator->zfBreakChainOnFailure);
    }

    public function testOptionsAreCastToArrayWhenAddingValidator()
    {
        $this->_checkZf2794();

        try {
            $this->element->addValidator('Alnum', false, true);
        } catch (Exception $e) {
            $this->fail('Should be able to add non-array validator options');
        }
        $validator = $this->element->getValidator('Alnum');
        $this->assertTrue($validator instanceof Zend_Validate_Alnum);
        $this->assertTrue($validator->allowWhiteSpace);
    }

    public function testCanRetrieveSingleValidatorRegisteredAsValidatorObjectUsingShortName()
    {
        $this->_checkZf2794();

        $this->assertFalse($this->element->getValidator('digits'));

        require_once 'Zend/Validate/Digits.php';
        $validator = new Zend_Validate_Digits();
        $this->element->addValidator($validator);
        $test = $this->element->getValidator('digits');
        $this->assertSame($validator, $test);
        $this->assertFalse($validator->zfBreakChainOnFailure);
    }

    public function testRetrievingNamedValidatorShouldNotReorderValidators()
    {
        $this->element->addValidators(array(
            'NotEmpty',
            'Alnum',
            'Digits',
        ));

        $validator  = $this->element->getValidator('Alnum');
        $validators = $this->element->getValidators();
        $i          = 0;
        $order      = array();

        foreach (array_keys($validators) as $name) {
            $order[$name] = $i;
            ++$i;
        }
        $this->assertEquals(1, $order['Zend_Validate_Alnum'], var_export($order, 1));
    }


    public function testCanAddMultipleValidators()
    {
        $this->_checkZf2794();

        $this->assertFalse($this->element->getValidator('Zend_Validate_Digits'));
        $this->assertFalse($this->element->getValidator('Zend_Validate_Alnum'));
        $this->element->addValidators(array('digits', 'alnum'));
        $digits = $this->element->getValidator('digits');
        $this->assertTrue($digits instanceof Zend_Validate_Digits);
        $alnum  = $this->element->getValidator('alnum');
        $this->assertTrue($alnum instanceof Zend_Validate_Alnum);
    }

    public function testRemovingUnregisteredValidatorReturnsObjectInstance()
    {
        $this->assertSame($this->element, $this->element->removeValidator('bogus'));
    }

    public function testPassingMessagesOptionToAddValidatorSetsValidatorMessages()
    {
        $messageTemplates = array(
            Zend_Validate_Digits::NOT_DIGITS   => 'Value should only contain digits',
            Zend_Validate_Digits::STRING_EMPTY => 'Value needs some digits',
        );
        $this->element->setAllowEmpty(false)
                      ->addValidator('digits', false, array('messages' => $messageTemplates));

        $this->element->isValid('');
        $messages = $this->element->getMessages();
        $found    = false;
        foreach ($messages as $key => $message) {
            if ($key == Zend_Validate_Digits::STRING_EMPTY) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found, 'Empty string message not found: ' . var_export($messages, 1));
        $this->assertEquals($messageTemplates[Zend_Validate_Digits::STRING_EMPTY], $message);

        $this->element->isValid('abc');
        $messages = $this->element->getMessages();
        $found    = false;
        foreach ($messages as $key => $message) {
            if ($key == Zend_Validate_Digits::NOT_DIGITS) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found, 'Not digits message not found');
        $this->assertEquals($messageTemplates[Zend_Validate_Digits::NOT_DIGITS], $message);
    }

    public function testCanPassSingleMessageToValidatorToSetValidatorMessages()
    {
        $this->_checkZf2794();

        $message = 'My custom empty message';
        $this->element->addValidator('notEmpty', false, array('messages' => $message))
                      ->setRequired(true);

        $this->element->isValid('');
        $messages = $this->element->getMessages();
        $this->assertEquals(1, count($messages));
        $this->assertEquals($message, current($messages));
    }

    public function testMessagesAreTranslatedForCurrentLocale()
    {
        $localeFile   = dirname(__FILE__) . '/_files/locale/array.php';
        $translations = include($localeFile);
        $translator   = new Zend_Translate('array', $translations, 'en');
        $translator->setLocale('en');

        $this->element->setAllowEmpty(false)
                      ->setTranslator($translator)
                      ->addValidator('digits');

        $this->element->isValid('');
        $messages = $this->element->getMessages();
        $found    = false;
        foreach ($messages as $key => $message) {
            if ($key == 'digitsStringEmpty') {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found, 'String Empty message not found: ' . var_export($messages, 1));
        $this->assertEquals($translations['stringEmpty'], $message);

        $this->element->isValid('abc');
        $messages = $this->element->getMessages();
        $found    = false;
        foreach ($messages as $key => $message) {
            if ($key == 'notDigits') {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found, 'Not Digits message not found');
        $this->assertEquals($translations['notDigits'], $message);
    }

    /**#@+
     * @see ZF-2988
     */
    public function testSettingErrorMessageShouldOverrideValidationErrorMessages()
    {
        $this->element->addValidator('Alpha');
        $this->element->addErrorMessage('Invalid value entered');
        $this->assertFalse($this->element->isValid(123));
        $messages = $this->element->getMessages();
        $this->assertEquals(1, count($messages));
        $this->assertEquals('Invalid value entered', array_shift($messages));
    }

    public function testCustomErrorMessagesShouldBeManagedInAStack()
    {
        $this->element->addValidator('Alpha');
        $this->element->addErrorMessage('Invalid value entered');
        $this->element->addErrorMessage('Really, it is not valid');
        $messages = $this->element->getErrorMessages();
        $this->assertEquals(2, count($messages));

        $this->assertFalse($this->element->isValid(123));
        $messages = $this->element->getMessages();
        $this->assertEquals(2, count($messages));
        $this->assertEquals('Invalid value entered', array_shift($messages));
        $this->assertEquals('Really, it is not valid', array_shift($messages));
    }

    public function testShouldAllowSettingMultipleErrorMessagesAtOnce()
    {
        $set1 = array('foo', 'bar', 'baz');
        $this->element->addErrorMessages($set1);
        $this->assertSame($set1, $this->element->getErrorMessages());
    }

    public function testSetErrorMessagesShouldOverwriteMessages()
    {
        $set1 = array('foo', 'bar', 'baz');
        $set2 = array('bat', 'cat');
        $this->element->addErrorMessages($set1);
        $this->assertSame($set1, $this->element->getErrorMessages());
        $this->element->setErrorMessages($set2);
        $this->assertSame($set2, $this->element->getErrorMessages());
    }

    public function testCustomErrorMessageStackShouldBeClearable()
    {
        $this->testCustomErrorMessagesShouldBeManagedInAStack();
        $this->element->clearErrorMessages();
        $messages = $this->element->getErrorMessages();
        $this->assertTrue(empty($messages));
    }

    public function testCustomErrorMessagesShouldBeTranslated()
    {
        $translations = array(
            'foo' => 'Foo message',
        );
        $translate = new Zend_Translate('array', $translations);
        $this->element->setTranslator($translate)
                      ->addErrorMessage('foo')
                      ->addValidator('Alpha');
        $this->assertFalse($this->element->isValid(123));
        $messages = $this->element->getMessages();
        $this->assertEquals(1, count($messages));
        $this->assertEquals('Foo message', array_shift($messages));
    }

    public function testCustomErrorMessagesShouldAllowValueSubstitution()
    {
        $this->element->addErrorMessage('"%value%" is an invalid value')
                      ->addValidator('Alpha');
        $this->assertFalse($this->element->isValid(123));
        $this->assertTrue($this->element->hasErrors());
        $messages = $this->element->getMessages();
        $this->assertEquals(1, count($messages));
        $this->assertEquals('"123" is an invalid value', array_shift($messages));
    }

    public function testShouldAllowMarkingElementAsInvalid()
    {
        $this->element->setValue('foo');
        $this->element->addErrorMessage('Invalid value entered');
        $this->assertFalse($this->element->hasErrors());
        $this->element->markAsError();
        $this->assertTrue($this->element->hasErrors());
        $messages = $this->element->getMessages();
        $this->assertEquals(1, count($messages));
        $this->assertEquals('Invalid value entered', array_shift($messages));
    }

    public function testShouldAllowPushingErrorsOntoErrorStackWithErrorMessages()
    {
        $this->element->setValue('foo');
        $this->assertFalse($this->element->hasErrors());
        $this->element->setErrors(array('Error 1', 'Error 2'))
                      ->addError('Error 3')
                      ->addErrors(array('Error 4', 'Error 5'));
        $this->assertTrue($this->element->hasErrors());
        $messages = $this->element->getMessages();
        $this->assertEquals(5, count($messages));
        foreach (range(1, 5) as $id) {
            $message = 'Error ' . $id;
            $this->assertContains($message, $messages);
        }
    }

    public function testHasErrorsShouldIndicateStatusOfValidationErrors()
    {
        $this->element->setValue('foo');
        $this->assertFalse($this->element->hasErrors());
        $this->element->markAsError();
        $this->assertTrue($this->element->hasErrors());
    }

    /**#@-*/

    public function testAddingErrorToArrayElementShouldLoopOverAllValues()
    {
        $this->element->setIsArray(true)
                      ->setValue(array('foo', 'bar', 'baz'))
                      ->addError('error with value %value%');
        $errors = $this->element->getMessages();
        require_once 'Zend/Json.php';
        $errors = Zend_Json::encode($errors);
        foreach (array('foo', 'bar', 'baz') as $value) {
            $message = 'error with value ' . $value;
            $this->assertContains($message, $errors);
        }
    }

    /** ZF-2568 */
    public function testTranslatedMessagesCanContainVariableSubstitution()
    {
        $localeFile   = dirname(__FILE__) . '/_files/locale/array.php';
        $translations = include($localeFile);
        $translations['notDigits'] .= ' "%value%"';
        $translator   = new Zend_Translate('array', $translations, 'en');
        $translator->setLocale('en');

        $this->element->setAllowEmpty(false)
                      ->setTranslator($translator)
                      ->addValidator('digits');

        $this->element->isValid('abc');
        $messages = $this->element->getMessages();
        $found    = false;
        foreach ($messages as $key => $message) {
            if ($key == 'notDigits') {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found, 'String Empty message not found: ' . var_export($messages, 1));
        $this->assertContains(' "abc"', $message);
        $this->assertContains('Translating the notDigits string', $message);
    }

    public function testCanRemoveValidator()
    {
        $this->_checkZf2794();

        $this->assertFalse($this->element->getValidator('Zend_Validate_Digits'));
        $this->element->addValidator('digits');
        $digits = $this->element->getValidator('digits');
        $this->assertTrue($digits instanceof Zend_Validate_Digits);
        $this->element->removeValidator('digits');
        $this->assertFalse($this->element->getValidator('digits'));
    }

    public function testCanClearAllValidators()
    {
        $this->_checkZf2794();

        $this->testCanAddMultipleValidators();
        $validators = $this->element->getValidators();
        $this->element->clearValidators();
        $test = $this->element->getValidators();
        $this->assertNotEquals($validators, $test);
        $this->assertTrue(empty($test));
        foreach (array_keys($validators) as $validator) {
            $this->assertFalse($this->element->getValidator($validator));
        }
    }

    public function testCanValidateElement()
    {
        $this->element->addValidator(new Zend_Validate_NotEmpty())
                      ->addValidator(new Zend_Validate_EmailAddress());
        try {
            $result = $this->element->isValid('matthew@zend.com');
        } catch (Exception $e) {
            $this->fail('Validating an element should work');
        }
    }

    public function testCanValidateArrayValue()
    {
        $this->element->setIsArray(true)
             ->addValidator('InArray', false, array(array('foo', 'bar', 'baz', 'bat')));
        $this->assertTrue($this->element->isValid(array('foo', 'bat')));
    }

    public function testShouldAllowZeroAsNonEmptyValue()
    {
        $this->element->addValidator('between', false, array(1, 100));
        $this->assertFalse($this->element->isValid('0'));
    }

    public function testIsValidPopulatesElementValue()
    {
        $this->testCanValidateElement();
        $this->assertEquals('matthew@zend.com', $this->element->getValue());
    }

    public function testErrorsPopulatedFollowingFailedIsValidCheck()
    {
        $this->element->addValidator(new Zend_Validate_NotEmpty())
                      ->addValidator(new Zend_Validate_EmailAddress());

        $result = $this->element->isValid('matthew');
        if ($result) {
            $this->fail('Invalid data should fail validations');
        }
        $errors = $this->element->getErrors();
        $this->assertTrue(is_array($errors));
        $this->assertTrue(0 < count($errors));
    }

    public function testMessagesPopulatedFollowingFailedIsValidCheck()
    {
        require_once 'Zend/Validate/NotEmpty.php';
        require_once 'Zend/Validate/EmailAddress.php';
        $this->element->addValidator(new Zend_Validate_NotEmpty())
                      ->addValidator(new Zend_Validate_EmailAddress());

        $result = $this->element->isValid('matthew');
        if ($result) {
            $this->fail('Invalid data should fail validations');
        }
        $messages = $this->element->getMessages();
        $this->assertTrue(is_array($messages));
        $this->assertTrue(0 < count($messages));
    }

    public function testOptionalElementDoesNotPerformValidationsOnEmptyValuesByDefault()
    {
        $this->element->addValidator(new Zend_Validate_EmailAddress());

        $result = $this->element->isValid('');
        if (!$result) {
            $this->fail('Empty data should not fail validations');
        }
        $errors = $this->element->getErrors();
        $this->assertTrue(is_array($errors));
        $this->assertTrue(empty($errors));
    }

    public function testOptionalElementDoesPerformValidationsWhenAllowEmptyIsFalse()
    {
        $this->element->setAllowEmpty(false)
                      ->addValidator(new Zend_Validate_EmailAddress());

        $result = $this->element->isValid('');
        if ($result) {
            $this->fail('Empty data should fail validations when AllowEmpty is false');
        }
        $errors = $this->element->getErrors();
        $this->assertTrue(is_array($errors));
        $this->assertTrue(0 < count($errors));
    }

    public function testAddingInvalidFilterTypeThrowsException()
    {
        try {
            $this->element->addFilter(123);
            $this->fail('Invalid filter type should raise exception');
        } catch (Zend_Form_Exception $e) {
            $this->assertContains('Invalid filter', $e->getMessage());
        }
    }

    public function testCanAddSingleFilterAsString()
    {
        $this->_checkZf2794();

        $this->assertFalse($this->element->getFilter('digits'));

        $this->element->addFilter('digits');
        $filter = $this->element->getFilter('digits');
        $this->assertTrue($filter instanceof Zend_Filter_Digits);
    }

    public function testCanNotRetrieveSingleFilterRegisteredAsStringUsingClassName()
    {
        $this->assertFalse($this->element->getFilter('digits'));

        $this->element->addFilter('digits');
        $this->assertFalse($this->element->getFilter('Zend_Filter_Digits'));
    }

    public function testCanAddSingleFilterAsFilterObject()
    {
        $this->assertFalse($this->element->getFilter('Zend_Filter_Digits'));

        require_once 'Zend/Filter/Digits.php';
        $filter = new Zend_Filter_Digits();
        $this->element->addFilter($filter);
        $test = $this->element->getFilter('Zend_Filter_Digits');
        $this->assertSame($filter, $test);
    }

    public function testCanRetrieveSingleFilterRegisteredAsFilterObjectUsingShortName()
    {
        $this->_checkZf2794();

        $this->assertFalse($this->element->getFilter('digits'));

        require_once 'Zend/Filter/Digits.php';
        $filter = new Zend_Filter_Digits();
        $this->element->addFilter($filter);
        $test = $this->element->getFilter('digits');
    }

    public function testRetrievingNamedFilterShouldNotReorderFilters()
    {
        $this->element->addFilters(array(
            'Alpha',
            'Alnum',
            'Digits',
        ));

        $filter  = $this->element->getFilter('Alnum');
        $filters = $this->element->getFilters();
        $i          = 0;
        $order      = array();

        foreach (array_keys($filters) as $name) {
            $order[$name] = $i;
            ++$i;
        }
        $this->assertEquals(1, $order['Zend_Filter_Alnum'], var_export($order, 1));
    }

    public function testOptionsAreCastToArrayWhenAddingFilter()
    {
        $this->_checkZf2794();

        try {
            $this->element->addFilter('Alnum', true);
        } catch (Exception $e) {
            $this->fail('Should be able to add non-array filter options');
        }
        $filter = $this->element->getFilter('Alnum');
        $this->assertTrue($filter instanceof Zend_Filter_Alnum);
        $this->assertTrue($filter->allowWhiteSpace);
    }

    public function testShouldUseFilterConstructorOptionsAsPassedToAddFilter()
    {
        $this->element->addFilter('HtmlEntities', array(array('quotestyle' => ENT_QUOTES, 'charset' => 'UTF-8')));
        $filter = $this->element->getFilter('HtmlEntities');
        $this->assertTrue($filter instanceof Zend_Filter_HtmlEntities);
        $this->assertEquals(ENT_QUOTES, $filter->getQuoteStyle());
        $this->assertEquals('UTF-8', $filter->getCharSet());
    }

    public function testCanAddMultipleFilters()
    {
        $this->_checkZf2794();

        $this->assertFalse($this->element->getFilter('Zend_Filter_Digits'));
        $this->assertFalse($this->element->getFilter('Zend_Filter_Alnum'));
        $this->element->addFilters(array('digits', 'alnum'));
        $digits = $this->element->getFilter('digits');
        $this->assertTrue($digits instanceof Zend_Filter_Digits);
        $alnum  = $this->element->getFilter('alnum');
        $this->assertTrue($alnum instanceof Zend_Filter_Alnum);
    }

    public function testRemovingUnregisteredFilterReturnsObjectInstance()
    {
        $this->assertSame($this->element, $this->element->removeFilter('bogus'));
    }

    public function testCanRemoveFilter()
    {
        $this->_checkZf2794();

        $this->assertFalse($this->element->getFilter('Zend_Filter_Digits'));
        $this->element->addFilter('digits');
        $digits = $this->element->getFilter('digits');
        $this->assertTrue($digits instanceof Zend_Filter_Digits);
        $this->element->removeFilter('digits');
        $this->assertFalse($this->element->getFilter('digits'));
    }

    public function testCanClearAllFilters()
    {
        $this->_checkZf2794();

        $this->testCanAddMultipleFilters();
        $filters = $this->element->getFilters();
        $this->element->clearFilters();
        $test = $this->element->getFilters();
        $this->assertNotEquals($filters, $test);
        $this->assertTrue(empty($test));
        foreach (array_keys($filters) as $filter) {
            $this->assertFalse($this->element->getFilter($filter));
        }
    }

    public function testGetViewReturnsNullWithNoViewRenderer()
    {
        $this->assertNull($this->element->getView());
    }

    public function testGetViewReturnsViewRendererViewInstanceIfViewRendererActive()
    {
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        $viewRenderer->initView();
        $view = $viewRenderer->view;
        $test = $this->element->getView();
        $this->assertSame($view, $test);
    }

    public function testCanSetView()
    {
        $view = new Zend_View();
        $this->assertNull($this->element->getView());
        $this->element->setView($view);
        $received = $this->element->getView();
        $this->assertSame($view, $received);
    }

    public function testViewHelperDecoratorRegisteredByDefault()
    {
        $this->_checkZf2794();

        $decorator = $this->element->getDecorator('viewHelper');
        $this->assertTrue($decorator instanceof Zend_Form_Decorator_ViewHelper);
    }

    /**
     * @group ZF-4822
     */
    public function testErrorsDecoratorRegisteredByDefault()
    {
        $this->_checkZf2794();

        $decorator = $this->element->getDecorator('errors');
        $this->assertTrue($decorator instanceof Zend_Form_Decorator_Errors);
    }

    /**
     * @group ZF-4822
     */
    public function testDescriptionDecoratorRegisteredByDefault()
    {
        $this->_checkZf2794();

        $decorator = $this->element->getDecorator('description');
        $this->assertTrue($decorator instanceof Zend_Form_Decorator_Description);
        $options = $decorator->getOptions();
        $this->assertTrue(array_key_exists('tag', $options));
        $this->assertEquals('p', $options['tag']);
        $this->assertTrue(array_key_exists('class', $options));
        $this->assertEquals('description', $options['class']);
    }

    /**
     * @group ZF-4822
     */
    public function testHtmlTagDecoratorRegisteredByDefault()
    {
        $this->_checkZf2794();

        $decorator = $this->element->getDecorator('HtmlTag');
        $this->assertTrue($decorator instanceof Zend_Form_Decorator_HtmlTag);
    }

    /**
     * @group ZF-4822
     */
    public function testLabelDecoratorRegisteredByDefault()
    {
        $this->_checkZf2794();

        $decorator = $this->element->getDecorator('Label');
        $this->assertTrue($decorator instanceof Zend_Form_Decorator_Label);
    }

    public function testCanDisableRegisteringDefaultDecoratorsDuringInitialization()
    {
        $element = new Zend_Form_Element('foo', array('disableLoadDefaultDecorators' => true));
        $decorators = $element->getDecorators();
        $this->assertEquals(array(), $decorators);
    }

    public function testAddingInvalidDecoratorThrowsException()
    {
        try {
            $this->element->addDecorator(123);
            $this->fail('Invalid decorator type should raise exception');
        } catch (Zend_Form_Exception $e) {
            $this->assertContains('Invalid decorator', $e->getMessage());
        }
    }

    public function testCanAddSingleDecoratorAsString()
    {
        $this->_checkZf2794();

        $this->element->clearDecorators();
        $this->assertFalse($this->element->getDecorator('viewHelper'));

        $this->element->addDecorator('viewHelper');
        $decorator = $this->element->getDecorator('viewHelper');
        $this->assertTrue($decorator instanceof Zend_Form_Decorator_ViewHelper);
    }

    public function testCanNotRetrieveSingleDecoratorRegisteredAsStringUsingClassName()
    {
        $this->assertFalse($this->element->getDecorator('Zend_Form_Decorator_ViewHelper'));
    }

    public function testCanAddSingleDecoratorAsDecoratorObject()
    {
        $this->element->clearDecorators();
        $this->assertFalse($this->element->getDecorator('viewHelper'));

        $decorator = new Zend_Form_Decorator_ViewHelper;
        $this->element->addDecorator($decorator);
        $test = $this->element->getDecorator('Zend_Form_Decorator_ViewHelper');
        $this->assertSame($decorator, $test);
    }

    /**
     * @see ZF-3597
     */
    public function testAddingConcreteDecoratorShouldHonorOrder()
    {
        require_once dirname(__FILE__) . '/_files/decorators/TableRow.php';
        $decorator = new My_Decorator_TableRow();
        $this->element->setLabel('Foo')
                      ->setDescription('sample description')
                      ->clearDecorators()
                      ->addDecorators(array(
            'ViewHelper',
            $decorator,
        ));
        $html = $this->element->render($this->getView());
        $this->assertRegexp('#<tr><td>Foo</td><td>.*?<input[^>]+>.*?</td><td>sample description</td></tr>#s', $html, $html);
    }

    public function testCanRetrieveSingleDecoratorRegisteredAsDecoratorObjectUsingShortName()
    {
        $this->_checkZf2794();

        $this->element->clearDecorators();
        $this->assertFalse($this->element->getDecorator('viewHelper'));

        $decorator = new Zend_Form_Decorator_ViewHelper;
        $this->element->addDecorator($decorator);
        $test = $this->element->getDecorator('viewHelper');
        $this->assertSame($decorator, $test);
    }

    public function testCanAddMultipleDecorators()
    {
        $this->_checkZf2794();

        $this->element->clearDecorators();
        $this->assertFalse($this->element->getDecorator('viewHelper'));

        $testDecorator = new Zend_Form_ElementTest_Decorator;
        $this->element->addDecorators(array(
            'ViewHelper',
            $testDecorator
        ));

        $viewHelper = $this->element->getDecorator('viewHelper');
        $this->assertTrue($viewHelper instanceof Zend_Form_Decorator_ViewHelper);
        $decorator = $this->element->getDecorator('decorator');
        $this->assertSame($testDecorator, $decorator);
    }

    public function testRemovingUnregisteredDecoratorReturnsObjectInstance()
    {
        $this->_checkZf2794();

        $this->assertSame($this->element, $this->element->removeDecorator('bogus'));
    }

    public function testCanRemoveDecorator()
    {
        $this->testViewHelperDecoratorRegisteredByDefault();
        $this->element->removeDecorator('viewHelper');
        $this->assertFalse($this->element->getDecorator('viewHelper'));
    }

    /**
     * @see ZF-3069
     */
    public function testRemovingNamedDecoratorsShouldWork()
    {
        $this->_checkZf2794();
        $this->element->setDecorators(array(
            'ViewHelper',
            array(array('div' => 'HtmlTag'), array('tag' => 'div')),
            array(array('div2' => 'HtmlTag'), array('tag' => 'div')),
        ));
        $decorators = $this->element->getDecorators();
        $this->assertTrue(array_key_exists('div', $decorators));
        $this->assertTrue(array_key_exists('div2', $decorators));
        $this->element->removeDecorator('div');
        $decorators = $this->element->getDecorators();
        $this->assertFalse(array_key_exists('div', $decorators));
        $this->assertTrue(array_key_exists('div2', $decorators));
    }

    public function testCanClearAllDecorators()
    {
        $this->testCanAddMultipleDecorators();
        $this->element->clearDecorators();
        $this->assertFalse($this->element->getDecorator('viewHelper'));
        $this->assertFalse($this->element->getDecorator('decorator'));
    }

    public function testCanAddDecoratorAliasesToAllowMultipleDecoratorsOfSameType()
    {
        $this->_checkZf2794();

        $this->element->setDecorators(array(
            array('HtmlTag', array('tag' => 'span')),
            array('decorator' => array('FooBar' => 'HtmlTag'), 'options' => array('tag' => 'div')),
        ));
        $decorator = $this->element->getDecorator('FooBar');
        $this->assertTrue($decorator instanceof Zend_Form_Decorator_HtmlTag);
        $this->assertEquals('div', $decorator->getOption('tag'));

        $decorator = $this->element->getDecorator('HtmlTag');
        $this->assertTrue($decorator instanceof Zend_Form_Decorator_HtmlTag);
        $this->assertEquals('span', $decorator->getOption('tag'));
    }

    public function testRetrievingNamedDecoratorShouldNotReorderDecorators()
    {
        $this->element->setDecorators(array(
            'ViewHelper',
            'Errors',
            array(array('inner' => 'HtmlTag'), array('tag' => 'div', 'class' => 'element')),
            'Label',
            array(array('outer' => 'HtmlTag'), array('tag' => 'div')),
        ));

        $decorator  = $this->element->getDecorator('inner');
        $decorators = $this->element->getDecorators();
        $i          = 0;
        $order      = array();

        foreach (array_keys($decorators) as $name) {
            $order[$name] = $i;
            ++$i;
        }
        $this->assertEquals(2, $order['inner'], var_export($order, 1));
    }

    /**
     * @see ZF-3376
     */
    public function testSetDecoratorsShouldAcceptReturnOfGetDecorators()
    {
        $this->element->setDecorators(array(
            'ViewHelper',
            'Errors',
            array('input' => 'HtmlTag', array('tag' => 'div', 'class' => 'input')),
            'Label',
            array('element' => 'HtmlTag', array('tag' => 'div', 'class' => 'element')),
        ));
        $decorators = $this->element->getDecorators();
        $this->element->setDecorators($decorators);
        $this->assertSame($decorators, $this->element->getDecorators());
    }

    public function testRenderElementReturnsMarkup()
    {
        $this->element->setName('foo');
        $html = $this->element->render($this->getView());
        $this->assertTrue(is_string($html));
        $this->assertFalse(empty($html));
        $this->assertContains('<input', $html);
        $this->assertContains('"foo"', $html);
    }

    public function testRenderElementRendersLabelWhenProvided()
    {
        $this->element->setView($this->getView());
        $this->element->setName('foo')
                      ->setLabel('Foo');
        $html = $this->element->render();
        $this->assertTrue(is_string($html));
        $this->assertFalse(empty($html));
        $this->assertContains('<label', $html);
        $this->assertContains('Foo', $html);
        $this->assertContains('</label>', $html);
    }

    public function testRenderElementRendersValueWhenProvided()
    {
        $this->element->setView($this->getView());
        $this->element->setName('foo')
                      ->setValue('bar');
        $html = $this->element->render();
        $this->assertTrue(is_string($html));
        $this->assertFalse(empty($html));
        $this->assertContains('<input', $html);
        $this->assertContains('"foo"', $html);
        $this->assertContains('"bar"', $html);
    }

    public function testRenderElementRendersErrorsWhenProvided()
    {
        $this->_checkZf2794();

        $this->element->setView($this->getView())
                      ->setRequired(true)
                      ->setName('foo')
                      ->addValidator('NotEmpty');
        $this->element->isValid('');

        $html = $this->element->render();
        $this->assertTrue(is_string($html));
        $this->assertFalse(empty($html));
        $this->assertContains('error', $html);
        $this->assertRegexp('/empty/i', $html);
    }

    public function testToStringProxiesToRender()
    {
        $this->element->setView($this->getView());
        $this->element->setName('foo');
        $html = $this->element->__toString();
        $this->assertTrue(is_string($html));
        $this->assertFalse(empty($html));
        $this->assertContains('<input', $html);
        $this->assertContains('"foo"', $html);
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
        $this->element->setDecorators(array(
            array(
                'decorator' => 'Callback',
                'options'   => array('callback' => array($this, 'raiseDecoratorException'))
            ),
        ));
        $origErrorHandler = set_error_handler(array($this, 'handleDecoratorErrors'), E_USER_WARNING);

        $text = $this->element->__toString();

        restore_error_handler();

        $this->assertTrue(empty($text));
        $this->assertTrue(isset($this->error));
        $this->assertEquals('Raising exception in decorator callback', $this->error);
    }

    public function getOptions()
    {
        $options = array(
            'name'     => 'changed',
            'value'    => 'foo',
            'label'    => 'bar',
            'order'    => 50,
            'required' => false,
            'foo'      => 'bar',
            'baz'      => 'bat'
        );
        return $options;
    }

    public function testCanSetObjectStateViaSetOptions()
    {
        $options = $this->getOptions();
        $this->element->setOptions($options);
        $this->assertEquals('changed', $this->element->getName());
        $this->assertEquals('foo', $this->element->getValue());
        $this->assertEquals('bar', $this->element->getLabel());
        $this->assertEquals(50, $this->element->getOrder());
        $this->assertFalse($this->element->isRequired());
        $this->assertEquals('bar', $this->element->foo);
        $this->assertEquals('bat', $this->element->baz);
    }

    public function testSetOptionsSkipsCallsToSetOptionsAndSetConfig()
    {
        $options = $this->getOptions();
        $config  = new Zend_Config($options);
        $options['config']  = $config;
        $options['options'] = $config->toArray();
        $this->element->setOptions($options);
    }

    public function testSetOptionsSkipsSettingAccessorsRequiringObjectsWhenNoObjectPresent()
    {
        $options = $this->getOptions();
        $options['translator'] = true;
        $options['pluginLoader'] = true;
        $options['view'] = true;
        $this->element->setOptions($options);
    }

    public function testSetOptionsSetsArrayOfStringValidators()
    {
        $this->_checkZf2794();

        $options = $this->getOptions();
        $options['validators'] = array(
            'notEmpty',
            'digits'
        );
        $this->element->setOptions($options);
        $validator = $this->element->getValidator('notEmpty');
        $this->assertTrue($validator instanceof Zend_Validate_NotEmpty);
        $validator = $this->element->getValidator('digits');
        $this->assertTrue($validator instanceof Zend_Validate_Digits);
    }

    public function testSetOptionsSetsArrayOfArrayValidators()
    {
        $this->_checkZf2794();

        $options = $this->getOptions();
        $options['validators'] = array(
            array('notEmpty', true, array('bar')),
            array('digits', true, array('bar')),
        );
        $this->element->setOptions($options);
        $validator = $this->element->getValidator('notEmpty');
        $this->assertTrue($validator instanceof Zend_Validate_NotEmpty);
        $this->assertTrue($validator->zfBreakChainOnFailure);
        $validator = $this->element->getValidator('digits');
        $this->assertTrue($validator instanceof Zend_Validate_Digits);
        $this->assertTrue($validator->zfBreakChainOnFailure);
    }

    public function testSetOptionsSetsArrayOfAssociativeArrayValidators()
    {
        $this->_checkZf2794();

        $options = $this->getOptions();
        $options['validators'] = array(
            array(
                'options'             => array('bar'),
                'breakChainOnFailure' => true,
                'validator'           => 'notEmpty',
            ),
            array(
                'options'             => array('bar'),
                'validator'           => 'digits',
                'breakChainOnFailure' => true,
            ),
        );
        $this->element->setOptions($options);
        $validator = $this->element->getValidator('notEmpty');
        $this->assertTrue($validator instanceof Zend_Validate_NotEmpty);
        $this->assertTrue($validator->zfBreakChainOnFailure);
        $validator = $this->element->getValidator('digits');
        $this->assertTrue($validator instanceof Zend_Validate_Digits);
        $this->assertTrue($validator->zfBreakChainOnFailure);
    }

    public function testSetOptionsSetsArrayOfStringFilters()
    {
        $this->_checkZf2794();

        $options = $this->getOptions();
        $options['filters'] = array('StringToUpper', 'Alpha');
        $this->element->setOptions($options);
        $filter = $this->element->getFilter('StringToUpper');
        $this->assertTrue($filter instanceof Zend_Filter_StringToUpper);
        $filter = $this->element->getFilter('Alpha');
        $this->assertTrue($filter instanceof Zend_Filter_Alpha);
    }

    public function testSetOptionsSetsArrayOfArrayFilters()
    {
        $this->_checkZf2794();

        $options = $this->getOptions();
        $options['filters'] = array(
            array('Digits', array('bar' => 'baz')),
            array('Alpha', array('foo')),
        );
        $this->element->setOptions($options);
        $filter = $this->element->getFilter('Digits');
        $this->assertTrue($filter instanceof Zend_Filter_Digits);
        $filter = $this->element->getFilter('Alpha');
        $this->assertTrue($filter instanceof Zend_Filter_Alpha);
    }

    public function testSetOptionsSetsArrayOfAssociativeArrayFilters()
    {
        $this->_checkZf2794();

        $options = $this->getOptions();
        $options['filters'] = array(
            array(
                'options' => array('baz'),
                'filter'  => 'Digits'
            ),
            array(
                'options' => array('foo'),
                'filter'  => 'Alpha',
            ),
        );
        $this->element->setOptions($options);
        $filter = $this->element->getFilter('Digits');
        $this->assertTrue($filter instanceof Zend_Filter_Digits);
        $filter = $this->element->getFilter('Alpha');
        $this->assertTrue($filter instanceof Zend_Filter_Alpha);
    }

    public function testSetOptionsSetsArrayOfStringDecorators()
    {
        $this->_checkZf2794();

        $options = $this->getOptions();
        $options['decorators'] = array('label', 'form');
        $this->element->setOptions($options);
        $this->assertFalse($this->element->getDecorator('viewHelper'));
        $this->assertFalse($this->element->getDecorator('errors'));
        $decorator = $this->element->getDecorator('label');
        $this->assertTrue($decorator instanceof Zend_Form_Decorator_Label);
        $decorator = $this->element->getDecorator('form');
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
        $this->element->setOptions($options);
        $this->assertFalse($this->element->getDecorator('viewHelper'));
        $this->assertFalse($this->element->getDecorator('errors'));

        $decorator = $this->element->getDecorator('label');
        $this->assertTrue($decorator instanceof Zend_Form_Decorator_Label);
        $options = $decorator->getOptions();
        $this->assertEquals('mylabel', $options['id']);

        $decorator = $this->element->getDecorator('form');
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
        $this->element->setOptions($options);
        $this->assertFalse($this->element->getDecorator('viewHelper'));
        $this->assertFalse($this->element->getDecorator('errors'));

        $decorator = $this->element->getDecorator('label');
        $this->assertTrue($decorator instanceof Zend_Form_Decorator_Label);
        $options = $decorator->getOptions();
        $this->assertEquals('mylabel', $options['id']);

        $decorator = $this->element->getDecorator('form');
        $this->assertTrue($decorator instanceof Zend_Form_Decorator_Form);
        $options = $decorator->getOptions();
        $this->assertEquals('form', $options['id']);
    }

    public function testSetOptionsSetsGlobalPrefixPaths()
    {
        $options = $this->getOptions();
        $options['prefixPath'] = array(
            'prefix' => 'Zend_Foo',
            'path'   => 'Zend/Foo/'
        );
        $this->element->setOptions($options);

        foreach (array('validate', 'filter', 'decorator') as $type) {
            $loader = $this->element->getPluginLoader($type);
            $paths = $loader->getPaths('Zend_Foo_' . ucfirst($type));
            $this->assertTrue(is_array($paths), "Failed for type $type: " . var_export($paths, 1));
            $this->assertFalse(empty($paths));
            $this->assertContains('Foo', $paths[0]);
        }
    }

    public function testSetOptionsSetsIndividualPrefixPathsFromKeyedArrays()
    {
        $options = $this->getOptions();
        $options['prefixPath'] = array(
            'filter' => array('prefix' => 'Zend_Foo', 'path' => 'Zend/Foo/')
        );
        $this->element->setOptions($options);

        $loader = $this->element->getPluginLoader('filter');
        $paths = $loader->getPaths('Zend_Foo');
        $this->assertTrue(is_array($paths));
        $this->assertFalse(empty($paths));
        $this->assertContains('Foo', $paths[0]);
    }

    public function testSetOptionsSetsIndividualPrefixPathsFromUnKeyedArrays()
    {
        $options = $this->getOptions();
        $options['prefixPath'] = array(
            array('type' => 'decorator', 'prefix' => 'Zend_Foo', 'path' => 'Zend/Foo/')
        );
        $this->element->setOptions($options);

        $loader = $this->element->getPluginLoader('decorator');
        $paths = $loader->getPaths('Zend_Foo');
        $this->assertTrue(is_array($paths));
        $this->assertFalse(empty($paths));
        $this->assertContains('Foo', $paths[0]);
    }

    public function testCanSetObjectStateViaSetConfig()
    {
        $config = new Zend_Config($this->getOptions());
        $this->element->setConfig($config);
        $this->assertEquals('changed', $this->element->getName());
        $this->assertEquals('foo', $this->element->getValue());
        $this->assertEquals('bar', $this->element->getLabel());
        $this->assertEquals(50, $this->element->getOrder());
        $this->assertFalse($this->element->isRequired());
        $this->assertEquals('bar', $this->element->foo);
        $this->assertEquals('bat', $this->element->baz);
    }

    public function testPassingConfigObjectToConstructorSetsObjectState()
    {
        $config = new Zend_Config($this->getOptions());
        $element = new Zend_Form_Element($config);
        $this->assertEquals('changed', $element->getName());
        $this->assertEquals('foo', $element->getValue());
        $this->assertEquals('bar', $element->getLabel());
        $this->assertEquals(50, $element->getOrder());
        $this->assertFalse($element->isRequired());
        $this->assertEquals('bar', $element->foo);
        $this->assertEquals('bat', $element->baz);
    }

    public function testValueIsFilteredPriorToValidation()
    {
        $this->_checkZf2794();

        $this->element->addFilter('StringTrim')
                      ->addValidator('StringLength', false, array(3, 8));
        $this->assertTrue($this->element->isValid('  foobar  '));
        $this->assertEquals('foobar', $this->element->getValue());

        $this->element->setFilters(array('StringTrim'))
                      ->setRequired(true)
                      ->setValidators(array('NotEmpty'));
        $this->assertFalse($this->element->isValid('    '));
    }

    // Extensions

    public function testInitCalledBeforeLoadDecorators()
    {
        $element = new Zend_Form_ElementTest_Element('test');
        $decorators = $element->getDecorators();
        $this->assertTrue(empty($decorators));
    }

    /**
     * @group ZF-3217
     */
    public function testElementShouldOverloadToRenderDecorators()
    {
        $this->element->setLabel('Foo Label')
                      ->setView($this->getView());
        $html = $this->element->renderViewHelper();
        $this->assertContains('<input', $html);
        $this->assertContains('id="' . $this->element->getFullyQualifiedName() . '"', $html, 'Received: ' . $html);
        $this->assertNotContains('<dd', $html);
        $this->assertNotContains('<label', $html);

        $html = $this->element->renderLabel('this is the content');
        $this->assertRegexp('#<label[^>]*for="' . $this->element->getFullyQualifiedName() . '"[^>]*>Foo Label</label>#', $html);
        $this->assertContains('this is the content', $html);
        $this->assertNotContains('<input', $html);
    }

    /**
     * @group ZF-3217
     * @expectedException Zend_Form_Element_Exception
     */
    public function testOverloadingToInvalidMethodsShouldThrowAnException()
    {
        $html = $this->element->bogusMethodCall();
    }

    /**
     * @group ZF-5150
     */
    public function testMarkingAsErrorShouldCauseIsErrorToReturnFalse()
    {
        $this->element->setValue('foo');
        $this->element->markAsError();
        $this->assertFalse($this->element->isValid('foo'));
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

class Zend_Form_ElementTest_Decorator extends Zend_Form_Decorator_Abstract
{
}

class Zend_Form_ElementTest_Element extends Zend_Form_Element
{
    public function init()
    {
        $this->setDisableLoadDefaultDecorators(true);
    }
}

class Zend_Form_ElementTest_ArrayFilter implements Zend_Filter_Interface
{
    public function filter($value)
    {
        $value = array_filter($value, array($this, '_filter'));
        return $value;
    }

    protected function _filter($value)
    {
        if (is_array($value)) {
            return array_filter($value, array($this, '_filter'));
        }
        return (strstr($value, 'ba'));
    }

    /**
     * Check array notation for validators
     */
    public function testValidatorsGivenArrayKeysOnValidation()
    {
        $username = new Zend_Form_Element('username');
        $username->addValidator('stringLength', true, array('min' => 5, 'max' => 20, 'ignore' => 'something'));
        $form = new Zend_Form(array('elements' => array($username)));
        $this->assertTrue($form->isValid(array('username' => 'abcde')));
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Form_ElementTest::main') {
    Zend_Form_ElementTest::main();
}
