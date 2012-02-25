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

require_once __DIR__ . '/TestAsset/decorators/TableRow.php';

use Zend\Form\Element,
    Zend\Form\Element\Exception as ElementException,
    Zend\Form\Form,
    Zend\Config\Config,
    Zend\Json\Json,
    Zend\Loader\PrefixPathLoader,
    Zend\Loader\PrefixPathMapper,
    Zend\Registry,
    Zend\Translator\Translator,
    Zend\Validator\AbstractValidator,
    Zend\Validator\Alpha as AlphaValidator,
    Zend\View\Renderer\PhpRenderer;

/**
 * @category   Zend
 * @package    Zend_Form
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Form
 */
class ElementTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        Registry::_unsetInstance();
        Form::setDefaultTranslator(null);

        if (isset($this->error)) {
            unset($this->error);
        }

        $this->element = new Element('foo');
    }

    public function getView()
    {
        $view = new PhpRenderer();
        return $view;
    }

    public function testConstructorRequiresMinimallyElementName()
    {
        try {
            $element = new Element(1);
            $this->fail('Zend\Form\Element constructor should not accept integer argument');
        } catch (ElementException\UnexpectedValueException $e) {
        }
        try {
            $element = new Element(true);
            $this->fail('Zend\Form\Element constructor should not accept boolean argument');
        } catch (ElementException\UnexpectedValueException $e) {
        }

        try {
            $element = new Element('foo');
        } catch (ElementException\UnexpectedValueException $e) {
            $this->fail('Zend\Form\Element constructor should accept String values');
        }

        $config = array('foo' => 'bar');
        try {
            $element = new Element($config);
            $this->fail('Zend\Form\Element constructor requires array with name element');
        } catch (ElementException\UnexpectedValueException $e) {
        }

        $config = array('name' => 'bar');
        try {
            $element = new Element($config);
        } catch (ElementException\UnexpectedValueException $e) {
            $this->fail('Zend\Form\Element constructor should accept array with name element');
        }

        $config = new Config(array('foo' => 'bar'));
        try {
            $element = new Element($config);
            $this->fail('Zend\Form\Element constructor requires Zend\Config object with name element');
        } catch (ElementException\UnexpectedValueException $e) {
        }

        $config = new Config(array('name' => 'bar'));
        try {
            $element = new Element($config);
        } catch (ElementException\UnexpectedValueException $e) {
            $this->fail('Zend_Form_Element constructor should accept Zend\Config with name element');
        }
    }

    public function testNoTranslatorByDefault()
    {
        $this->assertNull($this->element->getTranslator());
    }

    public function testGetTranslatorRetrievesGlobalDefaultWhenAvailable()
    {
        $this->testNoTranslatorByDefault();
        $translator = new Translator('ArrayAdapter', array('foo' => 'bar'));
        Form::setDefaultTranslator($translator);
        $received = $this->element->getTranslator();
        $this->assertSame($translator->getAdapter(), $received);
    }

    public function testTranslatorAccessorsWork()
    {
        $translator = new Translator('ArrayAdapter', array('foo' => 'bar'));
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

        $this->setExpectedException('Zend\Form\Element\Exception\InvalidArgumentException', 'Invalid name provided');
        $this->element->setName('%\^&*)\(%$#@!.}{;-,');
    }

    public function testZeroIsAllowedAsElementName()
    {
        try {
            $this->element->setName(0);
            $this->assertSame('0', $this->element->getName());
        } catch (ElementException\InvalidArgumentException $e) {
            $this->fail('Should allow zero as element name');
        }
    }

    /**
     * @group ZF-2851
     */
    public function testSetNameShouldNotAllowEmptyString()
    {
        foreach (array('', ' ', '   ') as $name) {
            try {
                $this->element->setName($name);
                $this->fail('setName() should not allow empty string');
            } catch (ElementException\InvalidArgumentException $e) {
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
                      ->addFilter(new TestAsset\ArrayFilter());
        $test = $this->element->getValue();
        $this->assertTrue(is_array($test));
        $test = Json::encode($test);
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
        $this->element->setRequired(true);
        $this->assertFalse($this->element->isValid(''));
        $validator = $this->element->getValidator('NotEmpty');
        $this->assertTrue($validator instanceof \Zend\Validator\NotEmpty);
        $this->assertTrue($validator->zfBreakChainOnFailure);
    }

    /**
     * @group ZF-2862
     */
    public function testBreakChainOnFailureFlagsForExistingValidatorsRemainSetWhenNotEmptyValidatorAutoInserted()
    {
        $username = new Element('username');
        $username->addValidator('stringLength', true, array(5, 20))
                 ->addValidator('regex', true, array('/^[a-zA-Z0-9_]*$/'))
                 ->addFilter('StringToLower')
                 ->setRequired(true);
        $form = new Form(array('elements' => array($username)));
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
        $this->assertEquals('Zend\Form\Element', $this->element->getType());
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
        $this->setExpectedException('Zend\Form\Element\Exception\InvalidArgumentException', 'Invalid attribute');
        $this->element->setAttrib('_foo', 'bar');
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
        $this->setExpectedException('Zend\Form\Element\Exception\RunTimeException', 'Cannot retrieve value for protected/private');
        $name = $this->element->_name;
    }

    public function testCanSetAndRetrieveAttribsViaOverloading()
    {
        $this->element->foo = 'bar';
        $this->assertEquals('bar', $this->element->foo);
    }

    public function testGetPluginLoaderRetrievesDefaultValidatorPluginLoader()
    {
        $loader = $this->element->getPluginLoader('validator');
        $this->assertTrue($loader instanceof PrefixPathMapper);
        $paths = $loader->getPaths('Zend\Validator');
        $this->assertInstanceOf('SplStack', $paths);
        $this->assertTrue(0 < count($paths));
        $this->assertContains('Validator', $paths[0]);
    }

    public function testGetPluginLoaderRetrievesDefaultFilterPluginLoader()
    {
        $loader = $this->element->getPluginLoader('filter');
        $this->assertTrue($loader instanceof PrefixPathMapper);
        $paths = $loader->getPaths('Zend\Filter');
        $this->assertInstanceOf('SplStack', $paths);
        $this->assertTrue(0 < count($paths));
        $this->assertContains('Filter', $paths[0]);
    }

    public function testGetPluginLoaderRetrievesDefaultDecoratorPluginLoader()
    {
        $loader = $this->element->getPluginLoader('decorator');
        $this->assertTrue($loader instanceof PrefixPathMapper);
        $paths = $loader->getPaths('Zend\Form\Decorator');
        $this->assertInstanceOf('SplStack', $paths);
        $this->assertTrue(0 < count($paths));
        $this->assertContains('Decorator', $paths[0]);
    }

    public function testCanSetCustomValidatorPluginLoader()
    {
        $loader = new PrefixPathLoader();
        $this->element->setPluginLoader($loader, 'validator');
        $test = $this->element->getPluginLoader('validator');
        $this->assertSame($loader, $test);
    }

    public function testPassingInvalidTypeToSetPluginLoaderThrowsException()
    {
        $loader = new PrefixPathLoader();
        $this->setExpectedException('Zend\Form\Exception', 'Invalid type');
        $this->element->setPluginLoader($loader, 'foo');
    }

    public function testPassingInvalidTypeToGetPluginLoaderThrowsException()
    {
        $this->setExpectedException('Zend\Form\Element\Exception\InvalidArgumentException', 'Invalid type');
        $this->element->getPluginLoader('foo');
    }

    public function testCanSetCustomFilterPluginLoader()
    {
        $loader = new PrefixPathLoader();
        $this->element->setPluginLoader($loader, 'filter');
        $test = $this->element->getPluginLoader('filter');
        $this->assertSame($loader, $test);
    }

    public function testCanSetCustomDecoratorPluginLoader()
    {
        $loader = new PrefixPathLoader();
        $this->element->setPluginLoader($loader, 'decorator');
        $test = $this->element->getPluginLoader('decorator');
        $this->assertSame($loader, $test);
    }

    public function testPassingInvalidLoaderTypeToAddPrefixPathThrowsException()
    {
        $this->setExpectedException('Zend\Form\Element\Exception\InvalidArgumentException', 'Invalid type');
        $this->element->addPrefixPath('Zend_Foo', 'Zend/Foo/', 'foo');
    }

    public function testCanAddValidatorPluginLoaderPrefixPath()
    {
        $loader = $this->element->getPluginLoader('validator');
        $this->element->addPrefixPath('Zend\Form', 'Zend/Form/', 'validator');
        $paths = $loader->getPaths('Zend\Form');
        $this->assertInstanceOf('SplStack', $paths);
        $this->assertContains('Form', $paths[0]);
    }

    public function testAddingValidatorPluginLoaderPrefixPathDoesNotAffectOtherLoaders()
    {
        $validateLoader  = $this->element->getPluginLoader('validator');
        $filterLoader    = $this->element->getPluginLoader('filter');
        $decoratorLoader = $this->element->getPluginLoader('decorator');
        $this->element->addPrefixPath('Zend\Form', 'Zend/Form/', 'validator');
        $this->assertFalse($filterLoader->getPaths('Zend\Form'));
        $this->assertFalse($decoratorLoader->getPaths('Zend\Form'));
    }

    public function testCanAddFilterPluginLoaderPrefixPath()
    {
        $loader = $this->element->getPluginLoader('validator');
        $this->element->addPrefixPath('Zend\Form', 'Zend/Form/', 'validator');
        $paths = $loader->getPaths('Zend\Form');
        $this->assertInstanceOf('SplStack', $paths);
        $this->assertContains('Form', $paths[0]);
    }

    public function testAddingFilterPluginLoaderPrefixPathDoesNotAffectOtherLoaders()
    {
        $filterLoader    = $this->element->getPluginLoader('filter');
        $validateLoader  = $this->element->getPluginLoader('validator');
        $decoratorLoader = $this->element->getPluginLoader('decorator');
        $this->element->addPrefixPath('Zend\Form', 'Zend/Form/', 'filter');
        $this->assertFalse($validateLoader->getPaths('Zend\Form'));
        $this->assertFalse($decoratorLoader->getPaths('Zend\Form'));
    }

    public function testCanAddDecoratorPluginLoaderPrefixPath()
    {
        $loader = $this->element->getPluginLoader('decorator');
        $this->element->addPrefixPath('Zend\Foo', 'Zend/Foo/', 'decorator');
        $paths = $loader->getPaths('Zend\Foo');
        $this->assertInstanceOf('SplStack', $paths);
        $this->assertContains('Foo', $paths[0]);
    }

    public function testAddingDecoratorrPluginLoaderPrefixPathDoesNotAffectOtherLoaders()
    {
        $decoratorLoader = $this->element->getPluginLoader('decorator');
        $filterLoader    = $this->element->getPluginLoader('filter');
        $validateLoader  = $this->element->getPluginLoader('validator');
        $this->element->addPrefixPath('Zend\Foo', 'Zend/Foo/', 'decorator');
        $this->assertFalse($validateLoader->getPaths('Zend\Foo'));
        $this->assertFalse($filterLoader->getPaths('Zend\Foo'));
    }

    public function testCanAddAllPluginLoaderPrefixPathsSimultaneously()
    {
        $validatorLoader = new PrefixPathLoader();
        $filterLoader    = new PrefixPathLoader();
        $decoratorLoader = new PrefixPathLoader();
        $this->element->setPluginLoader($validatorLoader, 'validator')
                      ->setPluginLoader($filterLoader, 'filter')
                      ->setPluginLoader($decoratorLoader, 'decorator')
                      ->addPrefixPath('Zend', 'Zend/');

        $paths = $filterLoader->getPaths('Zend\Filter');
        $this->assertInstanceOf('SplStack', $paths);
        $this->assertContains('Filter', $paths[0]);

        $paths = $validatorLoader->getPaths('Zend\Validator');
        $this->assertInstanceOf('SplStack', $paths);
        $this->assertContains('Validator', $paths[0]);

        $paths = $decoratorLoader->getPaths('Zend\Decorator');
        $this->assertInstanceOf('SplStack', $paths);
        $this->assertContains('Decorator', $paths[0]);
    }

    public function testPassingInvalidValidatorToAddValidatorThrowsException()
    {
        $this->setExpectedException('Zend\Form\Element\Exception\InvalidArgumentException', 'Invalid validator');
        $this->element->addValidator(123);
    }

    public function testCanAddSingleValidatorAsString()
    {
        $this->assertFalse($this->element->getValidator('digits'));

        $this->element->addValidator('digits');
        $validator = $this->element->getValidator('digits');
        $this->assertTrue($validator instanceof \Zend\Validator\Digits, var_export($validator, 1));
        $this->assertFalse($validator->zfBreakChainOnFailure);
    }

    public function testCanNotRetrieveSingleValidatorRegisteredAsStringUsingClassName()
    {
        $this->assertFalse($this->element->getValidator('digits'));

        $this->element->addValidator('digits');
        $this->assertFalse($this->element->getValidator('Zend\Validator\Digits'));
    }

    public function testCanAddSingleValidatorAsValidatorObject()
    {
        $this->assertFalse($this->element->getValidator('Zend\Validator\Digits'));

        $validator = new \Zend\Validator\Digits();
        $this->element->addValidator($validator);
        $test = $this->element->getValidator('Zend\Validator\Digits');
        $this->assertSame($validator, $test);
        $this->assertFalse($validator->zfBreakChainOnFailure);
    }

    public function testOptionsAreCastToArrayWhenAddingValidator()
    {
        try {
            $this->element->addValidator('Alnum', false, true);
        } catch (ElementException\InvalidArgumentException $e) {
            $this->fail('Should be able to add non-array validator options');
        }
        $validator = $this->element->getValidator('Alnum');
        $this->assertTrue($validator instanceof \Zend\Validator\Alnum);
        $this->assertTrue($validator->getAllowWhiteSpace());
    }

    public function testCanRetrieveSingleValidatorRegisteredAsValidatorObjectUsingShortName()
    {
        $this->assertFalse($this->element->getValidator('digits'));

        $validator = new \Zend\Validator\Digits();
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
        $this->assertEquals(1, $order['Zend\Validator\Alnum'], var_export($order, 1));
    }


    public function testCanAddMultipleValidators()
    {
        $this->assertFalse($this->element->getValidator('Zend\Validator\Digits'));
        $this->assertFalse($this->element->getValidator('Zend\Validator\Alnum'));
        $this->element->addValidators(array('digits', 'alnum'));
        $digits = $this->element->getValidator('digits');
        $this->assertTrue($digits instanceof \Zend\Validator\Digits);
        $alnum  = $this->element->getValidator('alnum');
        $this->assertTrue($alnum instanceof \Zend\Validator\Alnum);
    }

    public function testRemovingUnregisteredValidatorReturnsObjectInstance()
    {
        $this->assertSame($this->element, $this->element->removeValidator('bogus'));
    }

    public function testPassingMessagesOptionToAddValidatorSetsValidatorMessages()
    {
        $messageTemplates = array(
            \Zend\Validator\Digits::NOT_DIGITS   => 'Value should only contain digits',
            \Zend\Validator\Digits::STRING_EMPTY => 'Value needs some digits',
        );
        $this->element->setAllowEmpty(false)
                      ->addValidator('digits', false, array('messages' => $messageTemplates));

        $this->element->isValid('');
        $messages = $this->element->getMessages();
        $found    = false;
        foreach ($messages as $key => $message) {
            if ($key == \Zend\Validator\Digits::STRING_EMPTY) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found, 'Empty string message not found: ' . var_export($messages, 1));
        $this->assertEquals($messageTemplates[\Zend\Validator\Digits::STRING_EMPTY], $message);

        $this->element->isValid('abc');
        $messages = $this->element->getMessages();
        $found    = false;
        foreach ($messages as $key => $message) {
            if ($key == \Zend\Validator\Digits::NOT_DIGITS) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found, 'Not digits message not found');
        $this->assertEquals($messageTemplates[\Zend\Validator\Digits::NOT_DIGITS], $message);
    }

    public function testCanPassSingleMessageToValidatorToSetValidatorMessages()
    {
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
        $localeFile   = __DIR__ . '/TestAsset/locale/array.php';
        $translations = include($localeFile);
        $translator   = new Translator('ArrayAdapter', $translations, 'en');
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

    /**
     * @group ZF-2988
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

    /**
     * @group ZF-2988
     */
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

    /**
     * @group ZF-2988
     */
    public function testShouldAllowSettingMultipleErrorMessagesAtOnce()
    {
        $set1 = array('foo', 'bar', 'baz');
        $this->element->addErrorMessages($set1);
        $this->assertSame($set1, $this->element->getErrorMessages());
    }

    /**
     * @group ZF-2988
     */
    public function testSetErrorMessagesShouldOverwriteMessages()
    {
        $set1 = array('foo', 'bar', 'baz');
        $set2 = array('bat', 'cat');
        $this->element->addErrorMessages($set1);
        $this->assertSame($set1, $this->element->getErrorMessages());
        $this->element->setErrorMessages($set2);
        $this->assertSame($set2, $this->element->getErrorMessages());
    }

    /**
     * @group ZF-2988
     */
    public function testCustomErrorMessageStackShouldBeClearable()
    {
        $this->testCustomErrorMessagesShouldBeManagedInAStack();
        $this->element->clearErrorMessages();
        $messages = $this->element->getErrorMessages();
        $this->assertTrue(empty($messages));
    }

    /**
     * @group ZF-2988
     */
    public function testCustomErrorMessagesShouldBeTranslated()
    {
        $translations = array(
            'foo' => 'Foo message',
        );
        $translate = new Translator('ArrayAdapter', $translations);
        $this->element->setTranslator($translate)
                      ->addErrorMessage('foo')
                      ->addValidator('Alpha');
        $this->assertFalse($this->element->isValid(123));
        $messages = $this->element->getMessages();
        $this->assertEquals(1, count($messages));
        $this->assertEquals('Foo message', array_shift($messages));
    }

    /**
     * @group ZF-2988
     */
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

    /**
     * @group ZF-2988
     */
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

    /**
     * @group ZF-2988
     */
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

    /**
     * @group ZF-2988
     */
    public function testHasErrorsShouldIndicateStatusOfValidationErrors()
    {
        $this->element->setValue('foo');
        $this->assertFalse($this->element->hasErrors());
        $this->element->markAsError();
        $this->assertTrue($this->element->hasErrors());
    }

    public function testAddingErrorToArrayElementShouldLoopOverAllValues()
    {
        $this->element->setIsArray(true)
                      ->setValue(array('foo', 'bar', 'baz'))
                      ->addError('error with value %value%');
        $errors = $this->element->getMessages();
        $errors = Json::encode($errors);
        foreach (array('foo', 'bar', 'baz') as $value) {
            $message = 'error with value ' . $value;
            $this->assertContains($message, $errors);
        }
    }

    /** ZF-2568 */
    public function testTranslatedMessagesCanContainVariableSubstitution()
    {
        $localeFile   = __DIR__ . '/TestAsset/locale/array.php';
        $translations = include($localeFile);
        $translations['notDigits'] .= ' "%value%"';
        $translator   = new Translator('ArrayAdapter', $translations, 'en');
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
        $this->assertFalse($this->element->getValidator('Zend\Validator\Digits'));
        $this->element->addValidator('digits');
        $digits = $this->element->getValidator('digits');
        $this->assertTrue($digits instanceof \Zend\Validator\Digits);
        $this->element->removeValidator('digits');
        $this->assertFalse($this->element->getValidator('digits'));
    }

    public function testCanClearAllValidators()
    {
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
        $this->element->addValidator(new \Zend\Validator\NotEmpty())
                      ->addValidator(new \Zend\Validator\EmailAddress());
        try {
            $result = $this->element->isValid('matthew@zend.com');
        } catch (\Exception $e) {
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
        $this->element->addValidator(new \Zend\Validator\NotEmpty())
                      ->addValidator(new \Zend\Validator\EmailAddress());

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
        $this->element->addValidator(new \Zend\Validator\NotEmpty())
                      ->addValidator(new \Zend\Validator\EmailAddress());

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
        $this->element->addValidator(new \Zend\Validator\EmailAddress());

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
                      ->addValidator(new \Zend\Validator\EmailAddress());

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
        $this->setExpectedException('Zend\Form\Element\Exception\InvalidArgumentException', 'Invalid filter');
        $this->element->addFilter(123);
    }

    public function testCanAddSingleFilterAsString()
    {
        $this->assertFalse($this->element->getFilter('digits'));

        $this->element->addFilter('digits');
        $filter = $this->element->getFilter('digits');
        $this->assertTrue($filter instanceof \Zend\Filter\Digits);
    }

    public function testCanNotRetrieveSingleFilterRegisteredAsStringUsingClassName()
    {
        $this->assertFalse($this->element->getFilter('digits'));

        $this->element->addFilter('digits');
        $this->assertFalse($this->element->getFilter('Zend\Filter\Digits'));
    }

    public function testCanAddSingleFilterAsFilterObject()
    {
        $this->assertFalse($this->element->getFilter('Zend\Filter\Digits'));

        $filter = new \Zend\Filter\Digits();
        $this->element->addFilter($filter);
        $test = $this->element->getFilter('Zend\Filter\Digits');
        $this->assertSame($filter, $test);
    }

    public function testCanRetrieveSingleFilterRegisteredAsFilterObjectUsingShortName()
    {
        $this->assertFalse($this->element->getFilter('digits'));

        $filter = new \Zend\Filter\Digits();
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
        $this->assertEquals(1, $order['Zend\Filter\Alnum'], var_export($order, 1));
    }

    public function testOptionsAreCastToArrayWhenAddingFilter()
    {
        try {
            $this->element->addFilter('Alnum', true);
        } catch (ElementException\InvalidArgumentException $e) {
            $this->fail('Should be able to add non-array filter options');
        }
        $filter = $this->element->getFilter('Alnum');
        $this->assertTrue($filter instanceof \Zend\Filter\Alnum);
        $this->assertTrue($filter->getAllowWhiteSpace());
    }

    public function testShouldUseFilterConstructorOptionsAsPassedToAddFilter()
    {
        $this->element->addFilter('HtmlEntities', array(array('quotestyle' => ENT_QUOTES, 'charset' => 'UTF-8')));
        $filter = $this->element->getFilter('HtmlEntities');
        $this->assertTrue($filter instanceof \Zend\Filter\HtmlEntities);
        $this->assertEquals(ENT_QUOTES, $filter->getQuoteStyle());
        $this->assertEquals('UTF-8', $filter->getCharSet());
    }

    public function testCanAddMultipleFilters()
    {
        $this->assertFalse($this->element->getFilter('Zend\Filter\Digits'));
        $this->assertFalse($this->element->getFilter('Zend\Filter\Alnum'));
        $this->element->addFilters(array('digits', 'alnum'));
        $digits = $this->element->getFilter('digits');
        $this->assertTrue($digits instanceof \Zend\Filter\Digits);
        $alnum  = $this->element->getFilter('alnum');
        $this->assertTrue($alnum instanceof \Zend\Filter\Alnum);
    }

    public function testRemovingUnregisteredFilterReturnsObjectInstance()
    {
        $this->assertSame($this->element, $this->element->removeFilter('bogus'));
    }

    public function testCanRemoveFilter()
    {
        $this->assertFalse($this->element->getFilter('Zend\Filter\Digits'));
        $this->element->addFilter('digits');
        $digits = $this->element->getFilter('digits');
        $this->assertTrue($digits instanceof \Zend\Filter\Digits);
        $this->element->removeFilter('digits');
        $this->assertFalse($this->element->getFilter('digits'));
    }

    public function testCanClearAllFilters()
    {
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

    public function testGetViewLazyLoadsPhpRendererByDefault()
    {
        $view = $this->element->getView();
        $this->assertInstanceOf('Zend\View\Renderer\PhpRenderer', $view);
    }

    public function testCanSetView()
    {
        $view = new PhpRenderer();
        $test = $this->element->getView();
        $this->assertNotSame($view, $test);
        $this->element->setView($view);
        $received = $this->element->getView();
        $this->assertSame($view, $received);
    }

    public function testViewHelperDecoratorRegisteredByDefault()
    {
        $decorator = $this->element->getDecorator('viewHelper');
        $this->assertTrue($decorator instanceof \Zend\Form\Decorator\ViewHelper);
    }

    /**
     * @group ZF-4822
     */
    public function testErrorsDecoratorRegisteredByDefault()
    {
        $decorator = $this->element->getDecorator('errors');
        $this->assertTrue($decorator instanceof \Zend\Form\Decorator\Errors);
    }

    /**
     * @group ZF-4822
     */
    public function testDescriptionDecoratorRegisteredByDefault()
    {
        $decorator = $this->element->getDecorator('description');
        $this->assertTrue($decorator instanceof \Zend\Form\Decorator\Description);
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
        $decorator = $this->element->getDecorator('HtmlTag');
        $this->assertTrue($decorator instanceof \Zend\Form\Decorator\HtmlTag);
    }

    /**
     * @group ZF-4822
     */
    public function testLabelDecoratorRegisteredByDefault()
    {
        $decorator = $this->element->getDecorator('Label');
        $this->assertTrue($decorator instanceof \Zend\Form\Decorator\Label);
    }

    public function testCanDisableRegisteringDefaultDecoratorsDuringInitialization()
    {
        $element = new Element('foo', array('disableLoadDefaultDecorators' => true));
        $decorators = $element->getDecorators();
        $this->assertEquals(array(), $decorators);
    }

    public function testAddingInvalidDecoratorThrowsException()
    {
        $this->setExpectedException('Zend\Form\Element\Exception\InvalidArgumentException', 'Invalid decorator');
        $this->element->addDecorator(123);
    }

    public function testCanAddSingleDecoratorAsString()
    {
        $this->element->clearDecorators();
        $this->assertFalse($this->element->getDecorator('viewHelper'));

        $this->element->addDecorator('viewHelper');
        $decorator = $this->element->getDecorator('viewHelper');
        $this->assertTrue($decorator instanceof \Zend\Form\Decorator\ViewHelper);
    }

    public function testCanNotRetrieveSingleDecoratorRegisteredAsStringUsingClassName()
    {
        $this->assertFalse($this->element->getDecorator('Zend\Form\Decorator\ViewHelper'));
    }

    public function testCanAddSingleDecoratorAsDecoratorObject()
    {
        $this->element->clearDecorators();
        $this->assertFalse($this->element->getDecorator('viewHelper'));

        $decorator = new \Zend\Form\Decorator\ViewHelper;
        $this->element->addDecorator($decorator);
        $test = $this->element->getDecorator('Zend\Form\Decorator\ViewHelper');
        $this->assertSame($decorator, $test);
    }

    /**
     * @group ZF-3597
     */
    public function testAddingConcreteDecoratorShouldHonorOrder()
    {
        $decorator = new \My\Decorator\TableRow();
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
        $this->element->clearDecorators();
        $this->assertFalse($this->element->getDecorator('viewHelper'));

        $decorator = new \Zend\Form\Decorator\ViewHelper;
        $this->element->addDecorator($decorator);
        $test = $this->element->getDecorator('viewHelper');
        $this->assertSame($decorator, $test);
    }

    public function testCanAddMultipleDecorators()
    {
        $this->element->clearDecorators();
        $this->assertFalse($this->element->getDecorator('viewHelper'));

        $testDecorator = new TestAsset\Decorator();
        $this->element->addDecorators(array(
            'ViewHelper',
            $testDecorator
        ));

        $viewHelper = $this->element->getDecorator('viewHelper');
        $this->assertTrue($viewHelper instanceof \Zend\Form\Decorator\ViewHelper);
        $decorator = $this->element->getDecorator('decorator');
        $this->assertSame($testDecorator, $decorator);
    }

    public function testRemovingUnregisteredDecoratorReturnsObjectInstance()
    {
        $this->assertSame($this->element, $this->element->removeDecorator('bogus'));
    }

    public function testCanRemoveDecorator()
    {
        $this->testViewHelperDecoratorRegisteredByDefault();
        $this->element->removeDecorator('viewHelper');
        $this->assertFalse($this->element->getDecorator('viewHelper'));
    }

    /**
     * @group ZF-3069
     */
    public function testRemovingNamedDecoratorsShouldWork()
    {
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
        $this->element->setDecorators(array(
            array('HtmlTag', array('tag' => 'span')),
            array('decorator' => array('FooBar' => 'HtmlTag'), 'options' => array('tag' => 'div')),
        ));
        $decorator = $this->element->getDecorator('FooBar');
        $this->assertTrue($decorator instanceof \Zend\Form\Decorator\HtmlTag);
        $this->assertEquals('div', $decorator->getOption('tag'));

        $decorator = $this->element->getDecorator('HtmlTag');
        $this->assertTrue($decorator instanceof \Zend\Form\Decorator\HtmlTag);
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
     * @group ZF-3376
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
        throw new \Exception('Raising exception in decorator callback');
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
        $config  = new Config($options);
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
        $options = $this->getOptions();
        $options['validators'] = array(
            'notEmpty',
            'digits'
        );
        $this->element->setOptions($options);
        $validator = $this->element->getValidator('notEmpty');
        $this->assertTrue($validator instanceof \Zend\Validator\NotEmpty);
        $validator = $this->element->getValidator('digits');
        $this->assertTrue($validator instanceof \Zend\Validator\Digits);
    }

    public function testSetOptionsSetsArrayOfArrayValidators()
    {
        $options = $this->getOptions();
        $options['validators'] = array(
            array('notEmpty', true, array(\Zend\Validator\NotEmpty::ALL)),
            array('digits', true, array('bar')),
        );
        $this->element->setOptions($options);
        $validator = $this->element->getValidator('notEmpty');
        $this->assertTrue($validator instanceof \Zend\Validator\NotEmpty);
        $this->assertTrue($validator->zfBreakChainOnFailure);
        $validator = $this->element->getValidator('digits');
        $this->assertTrue($validator instanceof \Zend\Validator\Digits);
        $this->assertTrue($validator->zfBreakChainOnFailure);
    }

    public function testSetOptionsSetsArrayOfAssociativeArrayValidators()
    {
        $options = $this->getOptions();
        $options['validators'] = array(
            array(
                'options'             => array(\Zend\Validator\NotEmpty::ALL),
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
        $this->assertTrue($validator instanceof \Zend\Validator\NotEmpty);
        $this->assertTrue($validator->zfBreakChainOnFailure);
        $validator = $this->element->getValidator('digits');
        $this->assertTrue($validator instanceof \Zend\Validator\Digits);
        $this->assertTrue($validator->zfBreakChainOnFailure);
    }

    public function testSetOptionsSetsArrayOfStringFilters()
    {
        $options = $this->getOptions();
        $options['filters'] = array('StringToUpper', 'Alpha');
        $this->element->setOptions($options);
        $filter = $this->element->getFilter('StringToUpper');
        $this->assertTrue($filter instanceof \Zend\Filter\StringToUpper);
        $filter = $this->element->getFilter('Alpha');
        $this->assertTrue($filter instanceof \Zend\Filter\Alpha);
    }

    public function testSetOptionsSetsArrayOfArrayFilters()
    {
        $options = $this->getOptions();
        $options['filters'] = array(
            array('Digits', array('bar' => 'baz')),
            array('Alpha', array('foo')),
        );
        $this->element->setOptions($options);
        $filter = $this->element->getFilter('Digits');
        $this->assertTrue($filter instanceof \Zend\Filter\Digits);
        $filter = $this->element->getFilter('Alpha');
        $this->assertTrue($filter instanceof \Zend\Filter\Alpha);
    }

    public function testSetOptionsSetsArrayOfAssociativeArrayFilters()
    {
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
        $this->assertTrue($filter instanceof \Zend\Filter\Digits);
        $filter = $this->element->getFilter('Alpha');
        $this->assertTrue($filter instanceof \Zend\Filter\Alpha);
    }

    public function testSetOptionsSetsArrayOfStringDecorators()
    {
        $options = $this->getOptions();
        $options['decorators'] = array('label', 'formDecorator');
        $this->element->setOptions($options);
        $this->assertFalse($this->element->getDecorator('viewHelper'));
        $this->assertFalse($this->element->getDecorator('errors'));
        $decorator = $this->element->getDecorator('label');
        $this->assertTrue($decorator instanceof \Zend\Form\Decorator\Label);
        $decorator = $this->element->getDecorator('formDecorator');
        $this->assertTrue($decorator instanceof \Zend\Form\Decorator\FormDecorator);
    }

    public function testSetOptionsSetsArrayOfArrayDecorators()
    {
        $options = $this->getOptions();
        $options['decorators'] = array(
            array('label', array('id' => 'mylabel')),
            array('formDecorator', array('id' => 'form')),
        );
        $this->element->setOptions($options);
        $this->assertFalse($this->element->getDecorator('viewHelper'));
        $this->assertFalse($this->element->getDecorator('errors'));

        $decorator = $this->element->getDecorator('label');
        $this->assertTrue($decorator instanceof \Zend\Form\Decorator\Label);
        $options = $decorator->getOptions();
        $this->assertEquals('mylabel', $options['id']);

        $decorator = $this->element->getDecorator('formDecorator');
        $this->assertTrue($decorator instanceof \Zend\Form\Decorator\FormDecorator);
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
        $this->element->setOptions($options);
        $this->assertFalse($this->element->getDecorator('viewHelper'));
        $this->assertFalse($this->element->getDecorator('errors'));

        $decorator = $this->element->getDecorator('label');
        $this->assertTrue($decorator instanceof \Zend\Form\Decorator\Label);
        $options = $decorator->getOptions();
        $this->assertEquals('mylabel', $options['id']);

        $decorator = $this->element->getDecorator('formDecorator');
        $this->assertTrue($decorator instanceof \Zend\Form\Decorator\FormDecorator);
        $options = $decorator->getOptions();
        $this->assertEquals('form', $options['id']);
    }

    public function testSetOptionsSetsGlobalPrefixPaths()
    {
        $options = $this->getOptions();
        $options['prefixPath'] = array(
            'prefix' => 'Zend\Foo',
            'path'   => 'Zend/Foo/'
        );
        $this->element->setOptions($options);

        foreach (array('validator', 'filter', 'decorator') as $type) {
            $loader = $this->element->getPluginLoader($type);
            $paths = $loader->getPaths('Zend\Foo\\' . ucfirst($type));
            $this->assertInstanceOf('SplStack', $paths);
            $this->assertNotEquals(0, count($paths));
            $this->assertContains('Foo', $paths[0]);
        }
    }

    public function testSetOptionsSetsIndividualPrefixPathsFromKeyedArrays()
    {
        $options = $this->getOptions();
        $options['prefixPath'] = array(
            'filter' => array('prefix' => 'Zend\Foo', 'path' => 'Zend/Foo/')
        );
        $this->element->setOptions($options);

        $loader = $this->element->getPluginLoader('filter');
        $paths = $loader->getPaths('Zend\Foo');
        $this->assertInstanceOf('SplStack', $paths);
        $this->assertNotEquals(0, count($paths));
        $this->assertContains('Foo', $paths[0]);
    }

    public function testSetOptionsSetsIndividualPrefixPathsFromUnKeyedArrays()
    {
        $options = $this->getOptions();
        $options['prefixPath'] = array(
            array('type' => 'decorator', 'prefix' => 'Zend\Foo', 'path' => 'Zend/Foo/')
        );
        $this->element->setOptions($options);

        $loader = $this->element->getPluginLoader('decorator');
        $paths = $loader->getPaths('Zend\Foo');
        $this->assertInstanceOf('SplStack', $paths);
        $this->assertNotEquals(0, count($paths));
        $this->assertContains('Foo', $paths[0]);
    }

    public function testCanSetObjectStateViaSetConfig()
    {
        $config = new Config($this->getOptions());
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
        $config = new Config($this->getOptions());
        $element = new Element($config);
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
        $this->element->addFilter('StringTrim')
                      ->addValidator('StringLength', false, array(3, 8));
        $this->assertTrue($this->element->isValid('  foobar  '));
        $this->assertEquals('foobar', $this->element->getValue());

        $this->element->setFilters(array('StringTrim'))
                      ->setRequired(true)
                      ->setValidators(array('NotEmpty'));
        $this->assertFalse($this->element->isValid('    '));
    }

    public function testTranslatedLabel()
    {
        $this->element->setLabel('FooBar');
        $translator = new Translator('ArrayAdapter', array('FooBar' => 'BazBar'));
        $this->element->setTranslator($translator);
        $this->assertEquals('BazBar', $this->element->getLabel());
    }

    // Extensions

    public function testInitCalledBeforeLoadDecorators()
    {
        $element = new TestAsset\ElementWithNoDecorators('test');
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
     */
    public function testOverloadingToInvalidMethodsShouldThrowAnException()
    {
        $this->setExpectedException('Zend\Form\Element\Exception\BadMethodCallException');
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
     * @group ZF-4915
     */
    public function testElementShouldAllowSettingDefaultErrorMessageSeparator()
    {
        $this->element->setErrorMessageSeparator('|');
        $this->assertEquals('|', $this->element->getErrorMessageSeparator());
    }

    /**
     * @group ZF-4915
     */
    public function testElementShouldUseSemicolonAndSpaceAsDefaultErrorMessageSeparator()
    {
        $this->assertEquals('; ', $this->element->getErrorMessageSeparator());
    }

    /**
     * @ZF-8882
     */
    public function testErrorMessagesShouldNotBeTranslatedWhenTranslatorIsDisabled()
    {
        $translations = array(
            'foo' => 'Foo message',
        );
        $translate = new Translator('ArrayAdapter', $translations);
        $this->element->setTranslator($translate)
                      ->addErrorMessage('foo')
                      ->addValidator('Alpha');
        $this->assertFalse($this->element->isValid(123));
        $messages = $this->element->getMessages();
        $this->assertEquals(1, count($messages));
        $this->assertEquals('Foo message', array_shift($messages));

        $this->element->setDisableTranslator(true);
        $this->assertFalse($this->element->isValid(123));
        $messages = $this->element->getMessages();
        $this->assertEquals(1, count($messages));
        $this->assertEquals('foo', array_shift($messages));
    }
    
    /**
     * @group ZF-9275
     */
    public function testElementDoesntOverrideDefaultValidatorTranslatorWithDefaultRegistryTranslator()
    {
        $registryTranslations = array('alphaInvalid' => 'Registry message');
        $registryTranslate = new Translator('ArrayAdapter', $registryTranslations);
        Registry::set('Zend_Translator', $registryTranslate);
        
        $validatorTranslations = array('alphaInvalid' => 'Validator message');
        $validatorTranslate = new Translator('ArrayAdapter', $validatorTranslations);
        AbstractValidator::setDefaultTranslator($validatorTranslate);
        
        $elementTranslations = array('alphaInvalid' => 'Element message');
        $elementTranslate = new Translator('ArrayAdapter', $elementTranslations);
       
        // the default validate translator should beat the registry one
        $this->element->addValidator('Alpha');
        $this->assertFalse($this->element->isValid(123));
        $messages = $this->element->getMessages();
        $this->assertEquals('Validator message', $messages['alphaInvalid']);
    }
    
    /**
     * @group ZF-9275
     */
    public function testDefaultTranslatorDoesntOverrideElementTranslatorOnValdiation()
    {
        $registryTranslations = array('alphaInvalid' => 'Registry message');
        $registryTranslate = new Translator('ArrayAdapter', $registryTranslations);
        Registry::set('Zend_Translator', $registryTranslate);
        
        $validatorTranslations = array('alphaInvalid' => 'Validator message');
        $validatorTranslate = new Translator('ArrayAdapter', $validatorTranslations);
        AbstractValidator::setDefaultTranslator($validatorTranslate);
        
        $elementTranslations = array('alphaInvalid' => 'Element message');
        $elementTranslate = new Translator('ArrayAdapter', $elementTranslations);
        
        $this->element->addValidator('Alpha');
        $this->element->setTranslator($elementTranslate);
        $this->assertFalse($this->element->isValid(123));
        $messages = $this->element->getMessages();
        $this->assertEquals('Element message', $messages['alphaInvalid']);
    }

    /**
     * @group ZF-9275
     */
    public function testValidatorsDefaultTranslatorDoesntOverrideFormsDefaultTranslator()
    {
        $formTranslations = array('alphaInvalid' => 'Form message');
        $formTranslate = new Translator('ArrayAdapter', $formTranslations);
        Form::setDefaultTranslator($formTranslate);
        
        $validatorTranslations = array('alphaInvalid' => 'Validator message');
        $validatorTranslate = new Translator('ArrayAdapter', $validatorTranslations);
        AbstractValidator::setDefaultTranslator($validatorTranslate);
        
        // the default validate translator should beat the registry one
        $this->element->addValidator('Alpha');
        $this->assertFalse($this->element->isValid(123));
        $messages = $this->element->getMessages();
        $this->assertEquals('Form message', $messages['alphaInvalid']);
    }
    
    /**
     * @group ZF-9275
     */
    public function testElementsTranslatorDoesntOverrideValidatorsDirectlyAttachedTranslator()
    {
        $elementTranslations = array('alphaInvalid' => 'Element message');
        $elementTranslate = new Translator('ArrayAdapter', $elementTranslations);
        
        $validatorTranslations = array('alphaInvalid' => 'Direct validator message');
        $validatorTranslate = new Translator('ArrayAdapter', $validatorTranslations);
        
        $validator = new AlphaValidator();
        $validator->setTranslator($validatorTranslate);
        $this->element->addValidator($validator);
        $this->assertFalse($this->element->isValid(123));
        $messages = $this->element->getMessages();
        $this->assertEquals('Direct validator message', $messages['alphaInvalid']);
    }

    /**
     * Prove the fluent interface on Zend_Form::loadDefaultDecorators
     *
     * @link http://framework.zend.com/issues/browse/ZF-9913
     * @return void
     */
    public function testFluentInterfaceOnLoadDefaultDecorators()
    {
        $this->assertSame($this->element, $this->element->loadDefaultDecorators());
    }

    /**
     * @group ZF-7552
     */
    public function testAddDecoratorsKeepsNonNumericKeyNames()
    {
        $this->element->addDecorators(array(array(array('td'  => 'HtmlTag'),
                                               array('tag' => 'td')),
                                         array(array('tr'  => 'HtmlTag'),
                                               array('tag' => 'tr')),
                                         array('HtmlTag', array('tag' => 'baz'))));
        $t1 = $this->element->getDecorators();
        $this->element->setDecorators($t1);
        $t2 = $this->element->getDecorators();
        $this->assertEquals($t1, $t2);
    }
}
