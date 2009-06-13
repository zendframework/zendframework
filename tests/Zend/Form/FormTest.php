<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Form_FormTest::main');
}

require_once dirname(__FILE__) . '/../../TestHelper.php';

// error_reporting(E_ALL);

require_once 'Zend/Form.php';

require_once 'Zend/Config.php';
require_once 'Zend/Controller/Action/HelperBroker.php';
require_once 'Zend/Form/Decorator/Form.php';
require_once 'Zend/Form/DisplayGroup.php';
require_once 'Zend/Form/Element.php';
require_once 'Zend/Form/Element/Text.php';
require_once 'Zend/Form/Element/File.php';
require_once 'Zend/Form/SubForm.php';
require_once 'Zend/Loader/PluginLoader.php';
require_once 'Zend/Registry.php';
require_once 'Zend/Translate.php';
require_once 'Zend/View.php';

class Zend_Form_FormTest extends PHPUnit_Framework_TestCase
{
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite('Zend_Form_FormTest');
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function clearRegistry()
    {
        if (Zend_Registry::isRegistered('Zend_Translate')) {
            $registry = Zend_Registry::getInstance();
            unset($registry['Zend_Translate']);
        }
    }

    public function setUp()
    {
        $this->clearRegistry();
        Zend_Form::setDefaultTranslator(null);

        if (isset($this->error)) {
            unset($this->error);
        }

        Zend_Controller_Action_HelperBroker::resetHelpers();
        $this->form = new Zend_Form();
    }

    public function tearDown()
    {
        $this->clearRegistry();
    }

    public function testZendFormImplementsZendValidateInterface()
    {
        $this->assertTrue($this->form instanceof Zend_Validate_Interface);
    }

    // Configuration

    public function getOptions()
    {
        $options = array(
            'name'   => 'foo',
            'class'  => 'someform',
            'action' => '/foo/bar',
            'method' => 'put',
        );
        return $options;
    }

    public function testCanSetObjectStateViaSetOptions()
    {
        $options = $this->getOptions();
        $this->form->setOptions($options);
        $this->assertEquals('foo', $this->form->getName());
        $this->assertEquals('someform', $this->form->getAttrib('class'));
        $this->assertEquals('/foo/bar', $this->form->getAction());
        $this->assertEquals('put', $this->form->getMethod());
    }

    public function testCanSetObjectStateByPassingOptionsToConstructor()
    {
        $options = $this->getOptions();
        $form = new Zend_Form($options);
        $this->assertEquals('foo', $form->getName());
        $this->assertEquals('someform', $form->getAttrib('class'));
        $this->assertEquals('/foo/bar', $form->getAction());
        $this->assertEquals('put', $form->getMethod());
    }

    public function testSetOptionsSkipsCallsToSetOptionsAndSetConfig()
    {
        $options = $this->getOptions();
        $config  = new Zend_Config($options);
        $options['config']  = $config;
        $options['options'] = $config->toArray();
        $this->form->setOptions($options);
    }

    public function testSetOptionsSkipsSettingAccessorsRequiringObjectsWhenNonObjectPassed()
    {
        $options = $this->getOptions();
        $options['pluginLoader'] = true;
        $options['subForms']     = true;
        $options['view']         = true;
        $options['translator']   = true;
        $options['default']      = true;
        $options['attrib']       = true;
        $this->form->setOptions($options);
    }

    public function testSetOptionsWithAttribsDoesNotOverwriteActionOrMethodOrName()
    {
        $attribs = $this->getOptions();
        unset($attribs['action'], $attribs['method']);
        $options = array(
            'name'    => 'MYFORM',
            'action'  => '/bar/baz',
            'method'  => 'GET',
            'attribs' => $attribs,
        );
        $form = new Zend_Form($options);
        $this->assertEquals($options['name'], $form->getName());
        $this->assertEquals($options['action'], $form->getAction());
        $this->assertEquals(strtolower($options['method']), strtolower($form->getMethod()));
    }

    public function getElementOptions()
    {
        $elements = array(
            'foo' => 'text',
            array('text', 'bar', array('class' => 'foobar')),
            array(
                'options' => array('class' => 'barbaz'),
                'type'    => 'text',
                'name'    => 'baz',
            ),
            'bat' => array(
                'options' => array('class' => 'bazbat'),
                'type'    => 'text',
            ),
            'lol' => array(
                'text',
                array('class' => 'lolcat'),
            )
        );
        return $elements;
    }

    public function testSetOptionsSetsElements()
    {
        $options = $this->getOptions();
        $options['elements'] = $this->getElementOptions();
        $this->form->setOptions($options);

        $this->assertTrue(isset($this->form->foo));
        $this->assertTrue($this->form->foo instanceof Zend_Form_Element_Text);

        $this->assertTrue(isset($this->form->bar));
        $this->assertTrue($this->form->bar instanceof Zend_Form_Element_Text);
        $this->assertEquals('foobar', $this->form->bar->class);

        $this->assertTrue(isset($this->form->baz));
        $this->assertTrue($this->form->baz instanceof Zend_Form_Element_Text);
        $this->assertEquals('barbaz', $this->form->baz->class);

        $this->assertTrue(isset($this->form->bat));
        $this->assertTrue($this->form->bat instanceof Zend_Form_Element_Text);
        $this->assertEquals('bazbat', $this->form->bat->class);

        $this->assertTrue(isset($this->form->lol));
        $this->assertTrue($this->form->lol instanceof Zend_Form_Element_Text);
        $this->assertEquals('lolcat', $this->form->lol->class);
    }

    public function testSetOptionsSetsDefaultValues()
    {
        $options = $this->getOptions();
        $options['defaults'] = array(
            'bar' => 'barvalue',
            'bat' => 'batvalue',
        );
        $options['elements'] = $this->getElementOptions();
        $this->form->setOptions($options);

        $this->assertEquals('barvalue', $this->form->bar->getValue());
        $this->assertEquals('batvalue', $this->form->bat->getValue());
    }

    public function testSetOptionsSetsArrayOfStringDecorators()
    {
        $this->_checkZf2794();

        $options = $this->getOptions();
        $options['decorators'] = array('label', 'errors');
        $this->form->setOptions($options);
        $this->assertFalse($this->form->getDecorator('form'));

        $decorator = $this->form->getDecorator('label');
        $this->assertTrue($decorator instanceof Zend_Form_Decorator_Label);
        $decorator = $this->form->getDecorator('errors');
        $this->assertTrue($decorator instanceof Zend_Form_Decorator_Errors);
    }

    public function testSetOptionsSetsArrayOfArrayDecorators()
    {
        $this->_checkZf2794();

        $options = $this->getOptions();
        $options['decorators'] = array(
            array('label', array('id' => 'mylabel')),
            array('errors', array('id' => 'errors')),
        );
        $this->form->setOptions($options);
        $this->assertFalse($this->form->getDecorator('form'));

        $decorator = $this->form->getDecorator('label');
        $this->assertTrue($decorator instanceof Zend_Form_Decorator_Label);
        $options = $decorator->getOptions();
        $this->assertEquals('mylabel', $options['id']);

        $decorator = $this->form->getDecorator('errors');
        $this->assertTrue($decorator instanceof Zend_Form_Decorator_Errors);
        $options = $decorator->getOptions();
        $this->assertEquals('errors', $options['id']);
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
                'options'   => array('id' => 'errors'),
                'decorator' => 'errors',
            ),
        );
        $this->form->setOptions($options);
        $this->assertFalse($this->form->getDecorator('form'));

        $decorator = $this->form->getDecorator('label');
        $this->assertTrue($decorator instanceof Zend_Form_Decorator_Label);
        $options = $decorator->getOptions();
        $this->assertEquals('mylabel', $options['id']);

        $decorator = $this->form->getDecorator('errors');
        $this->assertTrue($decorator instanceof Zend_Form_Decorator_Errors);
        $options = $decorator->getOptions();
        $this->assertEquals('errors', $options['id']);
    }

    public function testSetOptionsSetsGlobalPrefixPaths()
    {
        $options = $this->getOptions();
        $options['prefixPath'] = array(
            'prefix' => 'Zend_Foo',
            'path'   => 'Zend/Foo/'
        );
        $this->form->setOptions($options);

        foreach (array('element', 'decorator') as $type) {
            $loader = $this->form->getPluginLoader($type);
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
            'element' => array('prefix' => 'Zend_Foo', 'path' => 'Zend/Foo/')
        );
        $this->form->setOptions($options);

        $loader = $this->form->getPluginLoader('element');
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
        $this->form->setOptions($options);

        $loader = $this->form->getPluginLoader('decorator');
        $paths = $loader->getPaths('Zend_Foo');
        $this->assertTrue(is_array($paths));
        $this->assertFalse(empty($paths));
        $this->assertContains('Foo', $paths[0]);
    }

    public function testSetOptionsSetsDisplayGroups()
    {
        $options = $this->getOptions();
        $options['displayGroups'] = array(
            'barbat' => array(array('bar', 'bat'), array('order' => 20)),
            array(array('foo', 'baz'), 'foobaz', array('order' => 10)),
            array(
                'name'     => 'ghiabc',
                'elements' => array('ghi', 'abc'),
                'options'  => array('order' => 15),
            ),
        );
        $options['elements'] = array(
            'foo' => 'text',
            'bar' => 'text',
            'baz' => 'text',
            'bat' => 'text',
            'abc' => 'text',
            'ghi' => 'text',
            'jkl' => 'text',
            'mno' => 'text',
        );
        $this->form->setOptions($options);

        $this->assertTrue(isset($this->form->barbat));
        $elements = $this->form->barbat->getElements();
        $expected = array('bar', 'bat');
        $this->assertEquals($expected, array_keys($elements));
        $this->assertEquals(20, $this->form->barbat->getOrder());

        $this->assertTrue(isset($this->form->foobaz));
        $elements = $this->form->foobaz->getElements();
        $expected = array('foo', 'baz');
        $this->assertEquals($expected, array_keys($elements));
        $this->assertEquals(10, $this->form->foobaz->getOrder());

        $this->assertTrue(isset($this->form->ghiabc));
        $elements = $this->form->ghiabc->getElements();
        $expected = array('ghi', 'abc');
        $this->assertEquals($expected, array_keys($elements));
        $this->assertEquals(15, $this->form->ghiabc->getOrder());
    }

    /**
     * @group ZF-3250
     */
    public function testDisplayGroupOrderInConfigShouldNotMatter()
    {
        require_once 'Zend/Config/Xml.php';
        $config = new Zend_Config_Xml(dirname(__FILE__) . '/_files/config/zf3250.xml', 'sitearea', true);
        $form = new Zend_Form($config->test);
        // no assertions needed; throws error if order matters
    }

    /**
     * @group ZF-3112
     */
    public function testSetOptionsShouldCreateDisplayGroupsLast()
    {
        $options = array();
        $options['displayGroups'] = array(
            'barbat' => array(array('bar', 'bat'), array('order' => 20)),
            array(array('foo', 'baz'), 'foobaz', array('order' => 10)),
            array(
                'name'     => 'ghiabc',
                'elements' => array('ghi', 'abc'),
                'options'  => array('order' => 15),
            ),
        );
        $options = array_merge($options, $this->getOptions());
        $options['elements'] = array(
            'foo' => 'text',
            'bar' => 'text',
            'baz' => 'text',
            'bat' => 'text',
            'abc' => 'text',
            'ghi' => 'text',
            'jkl' => 'text',
            'mno' => 'text',
        );
        $this->form = new Zend_Form($options);

        $this->assertTrue(isset($this->form->barbat));
        $elements = $this->form->barbat->getElements();
        $expected = array('bar', 'bat');
        $this->assertEquals($expected, array_keys($elements));
        $this->assertEquals(20, $this->form->barbat->getOrder());

        $this->assertTrue(isset($this->form->foobaz));
        $elements = $this->form->foobaz->getElements();
        $expected = array('foo', 'baz');
        $this->assertEquals($expected, array_keys($elements));
        $this->assertEquals(10, $this->form->foobaz->getOrder());

        $this->assertTrue(isset($this->form->ghiabc));
        $elements = $this->form->ghiabc->getElements();
        $expected = array('ghi', 'abc');
        $this->assertEquals($expected, array_keys($elements));
        $this->assertEquals(15, $this->form->ghiabc->getOrder());
    }

    public function testSetConfigSetsObjectState()
    {
        $config = new Zend_Config($this->getOptions());
        $this->form->setConfig($config);
        $this->assertEquals('foo', $this->form->getName());
        $this->assertEquals('someform', $this->form->getAttrib('class'));
        $this->assertEquals('/foo/bar', $this->form->getAction());
        $this->assertEquals('put', $this->form->getMethod());
    }

    public function testCanSetObjectStateByPassingConfigObjectToConstructor()
    {
        $config = new Zend_Config($this->getOptions());
        $form = new Zend_Form($config);
        $this->assertEquals('foo', $form->getName());
        $this->assertEquals('someform', $form->getAttrib('class'));
        $this->assertEquals('/foo/bar', $form->getAction());
        $this->assertEquals('put', $form->getMethod());
    }


    // Attribs:

    public function testAttribsArrayInitiallyEmpty()
    {
        $attribs = $this->form->getAttribs();
        $this->assertTrue(is_array($attribs));
        $this->assertTrue(empty($attribs));
    }

    public function testRetrievingUndefinedAttribReturnsNull()
    {
        $this->assertNull($this->form->getAttrib('foo'));
    }

    public function testCanAddAndRetrieveSingleAttribs()
    {
        $this->testRetrievingUndefinedAttribReturnsNull();
        $this->form->setAttrib('foo', 'bar');
        $this->assertEquals('bar', $this->form->getAttrib('foo'));
    }

    public function testCanAddAndRetrieveMultipleAttribs()
    {
        $this->form->setAttrib('foo', 'bar');
        $this->assertEquals('bar', $this->form->getAttrib('foo'));
        $this->form->addAttribs(array(
            'bar' => 'baz',
            'baz' => 'bat',
            'bat' => 'foo'
        ));
        $test = $this->form->getAttribs();
        $attribs = array(
            'foo' => 'bar',
            'bar' => 'baz',
            'baz' => 'bat',
            'bat' => 'foo'
        );
        $this->assertSame($attribs, $test);
    }

    public function testSetAttribsOverwritesExistingAttribs()
    {
        $this->testCanAddAndRetrieveMultipleAttribs();
        $array = array('bogus' => 'value', 'not' => 'real');
        $this->form->setAttribs($array);
        $this->assertSame($array, $this->form->getAttribs());
    }

    public function testCanRemoveSingleAttrib()
    {
        $this->testCanAddAndRetrieveSingleAttribs();
        $this->assertTrue($this->form->removeAttrib('foo'));
        $this->assertNull($this->form->getAttrib('foo'));
    }

    public function testRemoveAttribReturnsFalseIfAttribDoesNotExist()
    {
        $this->assertFalse($this->form->removeAttrib('foo'));
    }

    public function testCanClearAllAttribs()
    {
        $this->testCanAddAndRetrieveMultipleAttribs();
        $this->form->clearAttribs();
        $attribs = $this->form->getAttribs();
        $this->assertTrue(is_array($attribs));
        $this->assertTrue(empty($attribs));
    }

    public function testNameIsInitiallyNull()
    {
        $this->assertNull($this->form->getName());
    }

    public function testCanSetName()
    {
        $this->testNameIsInitiallyNull();
        $this->form->setName('foo');
        $this->assertEquals('foo', $this->form->getName());
    }

    public function testZeroAsNameIsAllowed()
    {
        try {
            $this->form->setName(0);
            $this->assertEquals(0, $this->form->getName());
        } catch (Zend_Form_Exception $e) {
            $this->fail('Should allow zero as form name');
        }
    }

    public function testSetNameNormalizesValueToContainOnlyValidVariableCharacters()
    {
        $this->form->setName('f%\o^&*)o\(%$b#@!.a}{;-,r');
        $this->assertEquals('foobar', $this->form->getName());

        try {
            $this->form->setName('%\^&*)\(%$#@!.}{;-,');
            $this->fail('Empty names should raise exception');
        } catch (Zend_Form_Exception $e) {
            $this->assertContains('Invalid name provided', $e->getMessage());
        }
    }

    public function testActionDefaultsToEmptyString()
    {
        $this->assertSame('', $this->form->getAction());
    }

    public function testCanSetAction()
    {
        $this->testActionDefaultsToEmptyString();
        $this->form->setAction('/foo/bar');
        $this->assertEquals('/foo/bar', $this->form->getAction());
    }

    public function testMethodDefaultsToPost()
    {
        $this->assertEquals('post', $this->form->getMethod());
    }

    public function testCanSetMethod()
    {
        $this->testMethodDefaultsToPost();
        $this->form->setMethod('get');
        $this->assertEquals('get', $this->form->getMethod());
    }

    public function testMethodLimitedToGetPostPutAndDelete()
    {
        foreach (array('get', 'post', 'put', 'delete') as $method) {
            $this->form->setMethod($method);
            $this->assertEquals($method, $this->form->getMethod());
        }
        try {
            $this->form->setMethod('bogus');
            $this->fail('Invalid method type should throw exception');
        } catch (Zend_Form_Exception $e) {
            $this->assertContains('invalid', $e->getMessage());
        }
    }

    public function testEnctypeDefaultsToUrlEncoded()
    {
        $this->assertEquals(Zend_Form::ENCTYPE_URLENCODED, $this->form->getEnctype());
    }

    public function testCanSetEnctype()
    {
        $this->testEnctypeDefaultsToUrlEncoded();
        $this->form->setEnctype(Zend_Form::ENCTYPE_MULTIPART);
        $this->assertEquals(Zend_Form::ENCTYPE_MULTIPART, $this->form->getEnctype());
    }

    public function testLegendInitiallyNull()
    {
        $this->assertNull($this->form->getLegend());
    }

    public function testCanSetLegend()
    {
        $this->testLegendInitiallyNull();
        $legend = "This is a legend";
        $this->form->setLegend($legend);
        $this->assertEquals($legend, $this->form->getLegend());
    }

    public function testDescriptionInitiallyNull()
    {
        $this->assertNull($this->form->getDescription());
    }

    public function testCanSetDescription()
    {
        $this->testDescriptionInitiallyNull();
        $description = "This is a description";
        $this->form->setDescription($description);
        $this->assertEquals($description, $this->form->getDescription());
    }

    // Plugin loaders

    public function testGetPluginLoaderRetrievesDefaultDecoratorPluginLoader()
    {
        $loader = $this->form->getPluginLoader('decorator');
        $this->assertTrue($loader instanceof Zend_Loader_PluginLoader);
        $paths = $loader->getPaths('Zend_Form_Decorator');
        $this->assertTrue(is_array($paths), var_export($loader, 1));
        $this->assertTrue(0 < count($paths));
        $this->assertContains('Form', $paths[0]);
        $this->assertContains('Decorator', $paths[0]);
    }

    public function testPassingInvalidTypeToSetPluginLoaderThrowsException()
    {
        $loader = new Zend_Loader_PluginLoader();
        try {
            $this->form->setPluginLoader($loader, 'foo');
            $this->fail('Invalid plugin loader type should raise exception');
        } catch (Zend_Form_Exception $e) {
            $this->assertContains('Invalid type', $e->getMessage());
        }
    }

    public function testPassingInvalidTypeToGetPluginLoaderThrowsException()
    {
        try {
            $this->form->getPluginLoader('foo');
            $this->fail('Invalid plugin loader type should raise exception');
        } catch (Zend_Form_Exception $e) {
            $this->assertContains('Invalid type', $e->getMessage());
        }
    }

    public function testCanSetCustomDecoratorPluginLoader()
    {
        $loader = new Zend_Loader_PluginLoader();
        $this->form->setPluginLoader($loader, 'decorator');
        $test = $this->form->getPluginLoader('decorator');
        $this->assertSame($loader, $test);
    }

    public function testPassingInvalidTypeToAddPrefixPathThrowsException()
    {
        try {
            $this->form->addPrefixPath('Zend_Foo', 'Zend/Foo/', 'foo');
            $this->fail('Passing invalid loader type to addPrefixPath() should raise exception');
        } catch (Zend_Form_Exception $e) {
            $this->assertContains('Invalid type', $e->getMessage());
        }
    }

    public function testCanAddDecoratorPluginLoaderPrefixPath()
    {
        $loader = $this->form->getPluginLoader('decorator');
        $this->form->addPrefixPath('Zend_Foo', 'Zend/Foo/', 'decorator');
        $paths = $loader->getPaths('Zend_Foo');
        $this->assertTrue(is_array($paths));
        $this->assertContains('Foo', $paths[0]);
    }

    public function testUpdatedDecoratorPrefixPathUsedForNewElements()
    {
        $loader = $this->form->getPluginLoader('decorator');
        $this->form->addPrefixPath('Zend_Foo', 'Zend/Foo/', 'decorator');
        $foo = new Zend_Form_Element_Text('foo');
        $this->form->addElement($foo);
        $loader = $foo->getPluginLoader('decorator');
        $paths  = $loader->getPaths('Zend_Foo');
        $this->assertTrue(is_array($paths));
        $this->assertContains('Foo', $paths[0]);

        $this->form->addElement('text', 'bar');
        $bar = $this->form->bar;
        $loader = $bar->getPluginLoader('decorator');
        $paths  = $loader->getPaths('Zend_Foo');
        $this->assertTrue(is_array($paths));
        $this->assertContains('Foo', $paths[0]);
    }

    public function testUpdatedDecoratorPrefixPathUsedForNewDisplayGroups()
    {
        $loader = $this->form->getPluginLoader('decorator');
        $this->form->addPrefixPath('Zend_Foo', 'Zend/Foo/', 'decorator');
        $this->setupElements();
        $foo    = $this->form->foo;
        $loader = $foo->getPluginLoader('decorator');
        $paths  = $loader->getPaths('Zend_Foo');
        $this->assertTrue(is_array($paths));
        $this->assertContains('Foo', $paths[0]);
    }

    public function testUpdatedPrefixPathUsedForNewSubForms()
    {
        $loader = $this->form->getPluginLoader('decorator');
        $this->form->addPrefixPath('Zend_Foo', 'Zend/Foo/', 'decorator');
        $this->setupSubForm();
        $loader = $this->form->sub->getPluginLoader('decorator');
        $paths  = $loader->getPaths('Zend_Foo');
        $this->assertTrue(is_array($paths));
        $this->assertContains('Foo', $paths[0]);
    }

    public function testGetPluginLoaderRetrievesDefaultElementPluginLoader()
    {
        $loader = $this->form->getPluginLoader('element');
        $this->assertTrue($loader instanceof Zend_Loader_PluginLoader);
        $paths = $loader->getPaths('Zend_Form_Element');
        $this->assertTrue(is_array($paths), var_export($loader, 1));
        $this->assertTrue(0 < count($paths));
        $this->assertContains('Form', $paths[0]);
        $this->assertContains('Element', $paths[0]);
    }

    public function testCanSetCustomDecoratorElementLoader()
    {
        $loader = new Zend_Loader_PluginLoader();
        $this->form->setPluginLoader($loader, 'element');
        $test = $this->form->getPluginLoader('element');
        $this->assertSame($loader, $test);
    }

    public function testCanAddElementPluginLoaderPrefixPath()
    {
        $loader = $this->form->getPluginLoader('element');
        $this->form->addPrefixPath('Zend_Foo', 'Zend/Foo/', 'element');
        $paths = $loader->getPaths('Zend_Foo');
        $this->assertTrue(is_array($paths));
        $this->assertContains('Foo', $paths[0]);
    }

    public function testAddAllPluginLoaderPrefixPathsSimultaneously()
    {
        $decoratorLoader = new Zend_Loader_PluginLoader();
        $elementLoader   = new Zend_Loader_PluginLoader();
        $this->form->setPluginLoader($decoratorLoader, 'decorator')
                   ->setPluginLoader($elementLoader, 'element')
                   ->addPrefixPath('Zend', 'Zend/');

        $paths = $decoratorLoader->getPaths('Zend_Decorator');
        $this->assertTrue(is_array($paths), var_export($paths, 1));
        $this->assertContains('Decorator', $paths[0]);

        $paths = $elementLoader->getPaths('Zend_Element');
        $this->assertTrue(is_array($paths), var_export($paths, 1));
        $this->assertContains('Element', $paths[0]);
    }

    // Elements:

    public function testCanAddAndRetrieveSingleElements()
    {
        $element = new Zend_Form_Element('foo');
        $this->form->addElement($element);
        $this->assertSame($element, $this->form->getElement('foo'));
    }

    public function testGetElementReturnsNullForUnregisteredElement()
    {
        $this->assertNull($this->form->getElement('foo'));
    }

    public function testCanAddAndRetrieveSingleElementsByStringType()
    {
        $this->form->addElement('text', 'foo');
        $element = $this->form->getElement('foo');
        $this->assertTrue($element instanceof Zend_Form_Element);
        $this->assertTrue($element instanceof Zend_Form_Element_Text);
        $this->assertEquals('foo', $element->getName());
    }

    public function testAddElementAsStringElementThrowsExceptionWhenNoNameProvided()
    {
        try {
            $this->form->addElement('text');
            $this->fail('Should not be able to specify string element type without name');
        } catch (Zend_Form_Exception $e) {
            $this->assertContains('must have', $e->getMessage());
        }
    }

    public function testCreateElementReturnsNewElement()
    {
        $element = $this->form->createElement('text', 'foo');
        $this->assertTrue($element instanceof Zend_Form_Element);
    }

    public function testCreateElementDoesNotAttachElementToForm()
    {
        $element = $this->form->createElement('text', 'foo');
        $this->assertTrue($element instanceof Zend_Form_Element);
        $this->assertNull($this->form->foo);
    }

    public function testCanAddAndRetrieveMultipleElements()
    {
        $this->form->addElements(array(
            'foo' => 'text',
            array('text', 'bar'),
            array('text', 'baz', array('foo' => 'bar')),
            new Zend_Form_Element_Text('bat'),
        ));
        $elements = $this->form->getElements();
        $names = array('foo', 'bar', 'baz', 'bat');
        $this->assertEquals($names, array_keys($elements));
        $foo = $elements['foo'];
        $this->assertTrue($foo instanceof Zend_Form_Element_Text);
        $bar = $elements['bar'];
        $this->assertTrue($bar instanceof Zend_Form_Element_Text);
        $baz = $elements['baz'];
        $this->assertTrue($baz instanceof Zend_Form_Element_Text);
        $this->assertEquals('bar', $baz->foo, var_export($baz->getAttribs(), 1));
        $bat = $elements['bat'];
        $this->assertTrue($bat instanceof Zend_Form_Element_Text);
    }

    public function testSetElementsOverwritesExistingElements()
    {
        $this->testCanAddAndRetrieveMultipleElements();
        $this->form->setElements(array(
            'bogus' => 'text'
        ));
        $elements = $this->form->getElements();
        $names = array('bogus');
        $this->assertEquals($names, array_keys($elements));
    }

    public function testCanRemoveSingleElement()
    {
        $this->testCanAddAndRetrieveMultipleElements();
        $this->assertTrue($this->form->removeElement('bar'));
        $this->assertNull($this->form->getElement('bar'));
    }

    public function testRemoveElementReturnsFalseWhenElementNotRegistered()
    {
        $this->assertFalse($this->form->removeElement('bogus'));
    }

    public function testCanClearAllElements()
    {
        $this->testCanAddAndRetrieveMultipleElements();
        $this->form->clearElements();
        $elements = $this->form->getElements();
        $this->assertTrue(is_array($elements));
        $this->assertTrue(empty($elements));
    }

    public function testGetValueReturnsNullForUndefinedElements()
    {
        $this->assertNull($this->form->getValue('foo'));
    }

    public function testCanSetElementDefaultValues()
    {
        $this->testCanAddAndRetrieveMultipleElements();
        $values = array(
            'foo' => 'foovalue',
            'bar' => 'barvalue',
            'baz' => 'bazvalue',
            'bat' => 'batvalue'
        );
        $this->form->setDefaults($values);
        $elements = $this->form->getElements();
        foreach (array_keys($values) as $name) {
            $this->assertEquals($name . 'value', $elements[$name]->getValue(), var_export($elements[$name], 1));
        }
    }

    public function testSettingElementDefaultsDoesNotSetElementValuesToNullIfNotInDefaultsArray()
    {
        $this->testCanAddAndRetrieveMultipleElements();
        $this->form->baz->setValue('testing');
        $this->form->bar->setValue('testing');
        $values = array(
            'foo' => 'foovalue',
            'bat' => 'batvalue'
        );
        $this->form->setDefaults($values);
        $this->assertEquals('foovalue', $this->form->foo->getValue());
        $this->assertEquals('batvalue', $this->form->bat->getValue());
        $this->assertNotNull($this->form->baz->getValue());
        $this->assertNotNull($this->form->bar->getValue());
    }

    public function testCanRetrieveSingleElementValue()
    {
        $this->form->addElement('text', 'foo', array('value' => 'foovalue'));
        $this->assertEquals('foovalue', $this->form->getValue('foo'));
    }

    public function testCanRetrieveAllElementValues()
    {
        $this->testCanAddAndRetrieveMultipleElements();
        $values = array(
            'foo' => 'foovalue',
            'bar' => 'barvalue',
            'baz' => 'bazvalue',
            'bat' => 'batvalue'
        );
        $this->form->setDefaults($values);
        $test     = $this->form->getValues();
        $elements = $this->form->getElements();
        foreach (array_keys($values) as $name) {
            $this->assertEquals($values[$name], $test[$name]);
        }
    }

    public function testRetrievingAllElementValuesSkipsThoseFlaggedAsIgnore()
    {
        $this->form->addElements(array(
            'foo' => 'text',
            'bar' => 'text',
            'baz' => 'text'
        ));
        $this->form->setDefaults(array(
            'foo' => 'Foo Value',
            'bar' => 'Bar Value',
            'baz' => 'Baz Value',
        ));
        $this->form->bar->setIgnore(true);
        $test = $this->form->getValues();
        $this->assertFalse(array_key_exists('bar', $test));
        $this->assertTrue(array_key_exists('foo', $test));
        $this->assertTrue(array_key_exists('baz', $test));
    }

    public function testCanRetrieveSingleUnfilteredElementValue()
    {
        $foo = new Zend_Form_Element_Text('foo');
        $foo->addFilter('StringToUpper')
            ->setValue('foovalue');
        $this->form->addElement($foo);
        $this->assertEquals('FOOVALUE', $this->form->getValue('foo'));
        $this->assertEquals('foovalue', $this->form->getUnfilteredValue('foo'));
    }

    public function testCanRetrieveAllUnfilteredElementValues()
    {
        $foo = new Zend_Form_Element_Text('foo');
        $foo->addFilter('StringToUpper')
            ->setValue('foovalue');
        $bar = new Zend_Form_Element_Text('bar');
        $bar->addFilter('StringToUpper')
            ->setValue('barvalue');
        $this->form->addElements(array($foo, $bar));
        $values     = $this->form->getValues();
        $unfiltered = $this->form->getUnfilteredValues();
        foreach (array('foo', 'bar') as $key) {
            $value = $key . 'value';
            $this->assertEquals(strtoupper($value), $values[$key]);
            $this->assertEquals($value, $unfiltered[$key]);
        }
    }

    public function testOverloadingElements()
    {
        $this->form->addElement('text', 'foo');
        $this->assertTrue(isset($this->form->foo));
        $element = $this->form->foo;
        $this->assertTrue($element instanceof Zend_Form_Element);
        unset($this->form->foo);
        $this->assertFalse(isset($this->form->foo));

        $bar = new Zend_Form_Element_Text('bar');
        $this->form->bar = $bar;
        $this->assertTrue(isset($this->form->bar));
        $element = $this->form->bar;
        $this->assertSame($bar, $element);
    }

    public function testOverloadingGetReturnsNullForUndefinedFormItems()
    {
        $this->assertNull($this->form->bogus);
    }

    public function testOverloadingSetThrowsExceptionForInvalidTypes()
    {
        try {
            $this->form->foo = true;
            $this->fail('Overloading should not allow scalars');
        } catch (Zend_Form_Exception $e) {
            $this->assertContains('Only form elements and groups may be overloaded', $e->getMessage());
        }

        try {
            $this->form->foo = new Zend_Config(array());
            $this->fail('Overloading should not allow arbitrary object types');
        } catch (Zend_Form_Exception $e) {
            $this->assertContains('Only form elements and groups may be overloaded', $e->getMessage());
            $this->assertContains('Zend_Config', $e->getMessage());
        }
    }

    public function testFormIsNotAnArrayByDefault()
    {
        $this->assertFalse($this->form->isArray());
    }

    public function testCanSetArrayFlag()
    {
        $this->testFormIsNotAnArrayByDefault();
        $this->form->setIsArray(true);
        $this->assertTrue($this->form->isArray());
        $this->form->setIsArray(false);
        $this->assertFalse($this->form->isArray());
    }

    public function testElementsBelongToReturnsFormNameWhenFormIsArray()
    {
        $this->form->setName('foo')
                   ->setIsArray(true);
        $this->assertEquals('foo', $this->form->getElementsBelongTo());
    }

    public function testElementsInitiallyBelongToNoArrays()
    {
        $this->assertNull($this->form->getElementsBelongTo());
    }

    public function testCanSetArrayToWhichElementsBelong()
    {
        $this->testElementsInitiallyBelongToNoArrays();
        $this->form->setElementsBelongTo('foo');
        $this->assertEquals('foo', $this->form->getElementsBelongTo());
    }

    public function testSettingArrayToWhichElementsBelongSetsArrayFlag()
    {
        $this->testFormIsNotAnArrayByDefault();
        $this->testCanSetArrayToWhichElementsBelong();
        $this->assertTrue($this->form->isArray());
    }

    public function testArrayToWhichElementsBelongCanConsistOfValidVariableCharsOnly()
    {
        $this->testElementsInitiallyBelongToNoArrays();
        $this->form->setElementsBelongTo('f%\o^&*)o\(%$b#@!.a}{;-,r');
        $this->assertEquals('foobar', $this->form->getElementsBelongTo());
    }

    public function testSettingArrayToWhichElementsBelongEmptyClearsIt()
    {
        $this->testCanSetArrayToWhichElementsBelong();
        $this->form->setElementsBelongTo('');
        $this->assertNull($this->form->getElementsBelongTo());
    }

    public function testSettingArrayToWhichElementsBelongEmptySetsArrayFlagToFalse()
    {
        $this->testSettingArrayToWhichElementsBelongEmptyClearsIt();
        $this->assertFalse($this->form->isArray());
    }

    /**
     * @group ZF-3146
     */
    public function testSetElementsBelongToShouldApplyToBothExistingAndFutureElements()
    {
        $this->form->addElement('text', 'testBelongsTo');
        $this->form->setElementsBelongTo('foo');
        $this->assertEquals('foo', $this->form->testBelongsTo->getBelongsTo(), 'Failed determining testBelongsTo belongs to array');
        $this->setupElements();
        foreach ($this->form->getElements() as $element) {
            $message = sprintf('Failed determining element "%s" belongs to foo', $element->getName());
            $this->assertEquals('foo', $element->getBelongsTo(), $message);
        }
    }

    /**
     * @group ZF-3742
     */
    public function testElementsInDisplayGroupsShouldInheritFormElementsBelongToSetting()
    {
        $subForm = new Zend_Form_SubForm();
        $subForm->addElements(array(
                    new Zend_Form_Element_Text('foo'),
                    new Zend_Form_Element_Text('bar'),
                    new Zend_Form_Element_Text('baz'),
                    new Zend_Form_Element_Text('bat'),
                ))
                ->addDisplayGroup(array('bar', 'baz'), 'barbaz');
        $this->form->addSubForm($subForm, 'sub')
                   ->setElementsBelongTo('myform')
                   ->setView(new Zend_View);
        $html = $this->form->render();
        foreach (array('foo', 'bar', 'baz', 'bat') as $test) {
            $this->assertContains('id="myform-sub-' . $test . '"', $html);
            $this->assertContains('name="myform[sub][' . $test . ']"', $html);
        }
    }

    public function testIsValidWithOneLevelElementsBelongTo()
    {
        $this->form->addElement('text', 'test')->test
            ->addValidator('Identical', false, array('Test Value'));
        $this->form->setElementsBelongTo('foo');

        $data = array(
            'foo' => array(
                'test' => 'Test Value',
            ),
        );

        $this->assertTrue($this->form->isValid($data));
    }

    public function testIsValidWithMultiLevelElementsBelongTo()
    {
        $this->form->addElement('text', 'test')->test
            ->addValidator('Identical', false, array('Test Value'));
        $this->form->setElementsBelongTo('foo[bar][zot]');

        $data = array(
            'foo' => array(
                'bar' => array(
                    'zot' => array(
                        'test' => 'Test Value',
                    ),
                ),
            ),
        );

        $this->assertTrue($this->form->isValid($data));
    }

    // Sub forms

    public function testCanAddAndRetrieveSingleSubForm()
    {
        $subForm = new Zend_Form_SubForm;
        $subForm->addElements(array('foo' => 'text', 'bar' => 'text'));
        $this->form->addSubForm($subForm, 'page1');
        $test = $this->form->getSubForm('page1');
        $this->assertSame($subForm, $test);
    }

    public function testAddingSubFormSetsSubFormName()
    {
        $subForm = new Zend_Form_SubForm;
        $subForm->addElements(array('foo' => 'text', 'bar' => 'text'));
        $this->form->addSubForm($subForm, 'page1');
        $this->assertEquals('page1', $subForm->getName());
    }

    public function testGetSubFormReturnsNullForUnregisteredSubForm()
    {
        $this->assertNull($this->form->getSubForm('foo'));
    }

    public function testCanAddAndRetrieveMultipleSubForms()
    {
        $page1 = new Zend_Form_SubForm();
        $page2 = new Zend_Form_SubForm();
        $page3 = new Zend_Form_SubForm();
        $this->form->addSubForms(array(
            'page1' => $page1,
            array($page2, 'page2'),
            array($page3, 'page3', 3)
        ));
        $subforms = $this->form->getSubForms();
        $keys = array('page1', 'page2', 'page3');
        $this->assertEquals($keys, array_keys($subforms));
        $this->assertSame($page1, $subforms['page1']);
        $this->assertSame($page2, $subforms['page2']);
        $this->assertSame($page3, $subforms['page3']);
    }

    public function testSetSubFormsOverwritesExistingSubForms()
    {
        $this->testCanAddAndRetrieveMultipleSubForms();
        $foo = new Zend_Form_SubForm();
        $this->form->setSubForms(array('foo' => $foo));
        $subforms = $this->form->getSubForms();
        $keys = array('foo');
        $this->assertEquals($keys, array_keys($subforms));
        $this->assertSame($foo, $subforms['foo']);
    }

    public function testCanRemoveSingleSubForm()
    {
        $this->testCanAddAndRetrieveMultipleSubForms();
        $this->assertTrue($this->form->removeSubForm('page2'));
        $this->assertNull($this->form->getSubForm('page2'));
    }

    public function testRemoveSubFormReturnsFalseForNonexistantSubForm()
    {
        $this->assertFalse($this->form->removeSubForm('foo'));
    }

    public function testCanClearAllSubForms()
    {
        $this->testCanAddAndRetrieveMultipleSubForms();
        $this->form->clearSubForms();
        $subforms = $this->form->getSubForms();
        $this->assertTrue(is_array($subforms));
        $this->assertTrue(empty($subforms));
    }

    public function testOverloadingSubForms()
    {
        $foo = new Zend_Form_SubForm;
        $this->form->addSubForm($foo, 'foo');
        $this->assertTrue(isset($this->form->foo));
        $subform = $this->form->foo;
        $this->assertSame($foo, $subform);
        unset($this->form->foo);
        $this->assertFalse(isset($this->form->foo));

        $bar = new Zend_Form_SubForm();
        $this->form->bar = $bar;
        $this->assertTrue(isset($this->form->bar));
        $subform = $this->form->bar;
        $this->assertSame($bar, $subform);
    }

    public function testCanSetDefaultsForSubFormElementsFromForm()
    {
        $subForm = new Zend_Form_SubForm;
        $subForm->addElements(array('foo' => 'text', 'bar' => 'text'));
        $this->form->addSubForm($subForm, 'page1');

        $data = array('foo' => 'foo value', 'bar' => 'bar value');
        $this->form->setDefaults($data);
        $this->assertEquals($data['foo'], $subForm->foo->getValue());
        $this->assertEquals($data['bar'], $subForm->bar->getValue());
    }

    public function testCanSetDefaultsForSubFormElementsFromFormWithArray()
    {
        $subForm = new Zend_Form_SubForm;
        $subForm->addElements(array('foo' => 'text', 'bar' => 'text'));
        $this->form->addSubForm($subForm, 'page1');

        $data = array( 'page1' => array(
            'foo' => 'foo value',
            'bar' => 'bar value'
        ));
        $this->form->setDefaults($data);
        $this->assertEquals($data['page1']['foo'], $subForm->foo->getValue());
        $this->assertEquals($data['page1']['bar'], $subForm->bar->getValue());
    }

    public function testGetValuesReturnsSubFormValues()
    {
        $subForm = new Zend_Form_SubForm;
        $subForm->addElements(array('foo' => 'text', 'bar' => 'text'));
        $subForm->foo->setValue('foo value');
        $subForm->bar->setValue('bar value');
        $this->form->addSubForm($subForm, 'page1');

        $values = $this->form->getValues();
        $this->assertTrue(isset($values['page1']));
        $this->assertTrue(isset($values['page1']['foo']));
        $this->assertTrue(isset($values['page1']['bar']));
        $this->assertEquals($subForm->foo->getValue(), $values['page1']['foo']);
        $this->assertEquals($subForm->bar->getValue(), $values['page1']['bar']);
    }

    public function testGetValuesReturnsSubFormValuesFromArrayToWhichElementsBelong()
    {
        $subForm = new Zend_Form_SubForm;
        $subForm->addElements(array('foo' => 'text', 'bar' => 'text'))
                ->setElementsBelongTo('subform');
        $subForm->foo->setValue('foo value');
        $subForm->bar->setValue('bar value');
        $this->form->addSubForm($subForm, 'page1');

        $values = $this->form->getValues();
        $this->assertTrue(isset($values['subform']), var_export($values, 1));
        $this->assertTrue(isset($values['subform']['foo']));
        $this->assertTrue(isset($values['subform']['bar']));
        $this->assertEquals($subForm->foo->getValue(), $values['subform']['foo']);
        $this->assertEquals($subForm->bar->getValue(), $values['subform']['bar']);
    }

    public function testGetValuesReturnsNestedSubFormValuesFromArraysToWhichElementsBelong()
    {
        $form = new Zend_Form();
        $form->setElementsBelongTo('foobar');

        $form->addElement('text', 'firstName')
             ->getElement('firstName')
             ->setRequired(true);

        $form->addElement('text', 'lastName')
             ->getElement('lastName')
             ->setRequired(true);

        $subForm = new Zend_Form_SubForm();
        $subForm->setElementsBelongTo('baz[quux]');
        $subForm->addElement('text', 'email')
                ->getElement('email')->setRequired(true);

        $subSubForm = new Zend_Form_SubForm();
        $subSubForm->setElementsBelongTo('bat');
        $subSubForm->addElement('checkbox', 'home')
                   ->getElement('home')->setRequired(true);

        $subForm->addSubForm($subSubForm, 'subSub');

        $form->addSubForm($subForm, 'sub')
             ->addElement('submit', 'save', array('value' => 'submit', 'ignore' => true));


        $data = array('foobar' => array(
            'firstName' => 'Mabel',
            'lastName'  => 'Cow',
            'baz'    => array(
                'quux' => array(
                    'email' => 'mabel@cow.org',
                    'bat'   => array(
                        'home' => 1,
                    )
                ),
            )
        ));
        $this->assertTrue($form->isValid($data));

        $values = $form->getValues();
        $this->assertEquals($data, $values);
    }

    public function testGetValueCanReturnSubFormValues()
    {
        $subForm = new Zend_Form_SubForm;
        $subForm->addElements(array('foo' => 'text', 'bar' => 'text'));
        $subForm->foo->setValue('foo value');
        $subForm->bar->setValue('bar value');
        $this->form->addSubForm($subForm, 'page1');

        $values = $this->form->getValue('page1');
        $this->assertTrue(isset($values['foo']), var_export($values, 1));
        $this->assertTrue(isset($values['bar']));
        $this->assertEquals($subForm->foo->getValue(), $values['foo']);
        $this->assertEquals($subForm->bar->getValue(), $values['bar']);
    }

    public function testGetValueCanReturnSubFormValuesFromArrayToWhichElementsBelong()
    {
        $subForm = new Zend_Form_SubForm;
        $subForm->addElements(array('foo' => 'text', 'bar' => 'text'))
                ->setElementsBelongTo('subform');
        $subForm->foo->setValue('foo value');
        $subForm->bar->setValue('bar value');
        $this->form->addSubForm($subForm, 'page1');

        $values = $this->form->getValue('subform');
        $this->assertTrue(isset($values['foo']), var_export($values, 1));
        $this->assertTrue(isset($values['bar']));
        $this->assertEquals($subForm->foo->getValue(), $values['foo']);
        $this->assertEquals($subForm->bar->getValue(), $values['bar']);
    }

    public function testIsValidCanValidateSubFormsWithArbitraryElementsBelong()
    {
        $subForm = new Zend_Form_SubForm();
        $subForm->addElement('text', 'test')->test
            ->setRequired(true)->addValidator('Identical', false, array('Test Value'));
        $this->form->addSubForm($subForm, 'sub');

        $this->form->setElementsBelongTo('foo[bar]');
        $subForm->setElementsBelongTo('my[subform]');

        $data = array(
            'foo' => array(
                'bar' => array(
                    'my' => array(
                        'subform' => array(
                            'test' => 'Test Value',
                        ),
                    ),
                ),
            ),
        );

        $this->assertTrue($this->form->isValid($data));
    }

    public function testIsValidCanValidateNestedSubFormsWithArbitraryElementsBelong()
    {
        $subForm = new Zend_Form_SubForm();
        $subForm->addElement('text', 'test1')->test1
            ->setRequired(true)->addValidator('Identical', false, array('Test1 Value'));
        $this->form->addSubForm($subForm, 'sub');

        $subSubForm = new Zend_Form_SubForm();
        $subSubForm->addElement('text', 'test2')->test2
            ->setRequired(true)->addValidator('Identical', false, array('Test2 Value'));
        $subForm->addSubForm($subSubForm, 'subSub');

        $this->form->setElementsBelongTo('form[first]');
        // Notice we skipped subForm, to mix manual and auto elementsBelongTo.
        $subSubForm->setElementsBelongTo('subsubform[first]');

        $data = array(
            'form' => array(
                'first' => array(
                    'sub' => array(
                        'test1' => 'Test1 Value',

                        'subsubform' => array(
                            'first' => array(
                                'test2' => 'Test2 Value',
                            ),
                        ),
                    ),
                ),
            ),
        );

        $this->assertTrue($this->form->isValid($data));
    }


    // Display groups

    public function testCanAddAndRetrieveSingleDisplayGroups()
    {
        $this->testCanAddAndRetrieveMultipleElements();
        $this->form->addDisplayGroup(array('bar', 'bat'), 'barbat');
        $group = $this->form->getDisplayGroup('barbat');
        $this->assertTrue($group instanceof Zend_Form_DisplayGroup);
        $elements = $group->getElements();
        $expected = array('bar' => $this->form->bar, 'bat' => $this->form->bat);
        $this->assertEquals($expected, $elements);
    }

    public function testDisplayGroupsMustContainAtLeastOneElement()
    {
        try {
            $this->form->addDisplayGroup(array(), 'foo');
            $this->fail('Empty display group should raise exception');
        } catch (Zend_Form_Exception $e) {
            $this->assertContains('No valid elements', $e->getMessage());
        }
    }

    public function testCanAddAndRetrieveMultipleDisplayGroups()
    {
        $this->testCanAddAndRetrieveMultipleElements();
        $this->form->addDisplayGroups(array(
            array(array('bar', 'bat'), 'barbat'),
            'foobaz' => array('baz', 'foo')
        ));
        $groups = $this->form->getDisplayGroups();
        $expected = array(
            'barbat' => array('bar' => $this->form->bar, 'bat' => $this->form->bat),
            'foobaz' => array('baz' => $this->form->baz, 'foo' => $this->form->foo),
        );
        foreach ($groups as $group) {
            $this->assertTrue($group instanceof Zend_Form_DisplayGroup);
        }
        $this->assertEquals($expected['barbat'], $groups['barbat']->getElements());
        $this->assertEquals($expected['foobaz'], $groups['foobaz']->getElements());
    }

    public function testSetDisplayGroupsOverwritesExistingDisplayGroups()
    {
        $this->testCanAddAndRetrieveMultipleDisplayGroups();
        $this->form->setDisplayGroups(array('foobar' => array('bar', 'foo')));
        $groups = $this->form->getDisplayGroups();
        $expected = array('bar' => $this->form->bar, 'foo' => $this->form->foo);
        $this->assertEquals(1, count($groups));
        $this->assertTrue(isset($groups['foobar']));
        $this->assertEquals($expected, $groups['foobar']->getElements());
    }

    public function testCanRemoveSingleDisplayGroup()
    {
        $this->testCanAddAndRetrieveMultipleDisplayGroups();
        $this->assertTrue($this->form->removeDisplayGroup('barbat'));
        $this->assertNull($this->form->getDisplayGroup('barbat'));
    }

    public function testRemoveDisplayGroupReturnsFalseForNonexistantGroup()
    {
        $this->assertFalse($this->form->removeDisplayGroup('bogus'));
    }

    public function testCanClearAllDisplayGroups()
    {
        $this->testCanAddAndRetrieveMultipleDisplayGroups();
        $this->form->clearDisplayGroups();
        $groups = $this->form->getDisplayGroups();
        $this->assertTrue(is_array($groups));
        $this->assertTrue(empty($groups));
    }

    public function testOverloadingDisplayGroups()
    {
        $this->testCanAddAndRetrieveMultipleElements();
        $this->form->addDisplayGroup(array('foo', 'bar'), 'foobar');
        $this->assertTrue(isset($this->form->foobar));
        $group = $this->form->foobar;
        $expected = array('foo' => $this->form->foo, 'bar' => $this->form->bar);
        $this->assertEquals($expected, $group->getElements());
        unset($this->form->foobar);
        $this->assertFalse(isset($this->form->foobar));

        $this->form->barbaz = array('bar', 'baz');
        $this->assertTrue(isset($this->form->barbaz));
        $group = $this->form->barbaz;
        $expected = array('bar' => $this->form->bar, 'baz' => $this->form->baz);
        $this->assertSame($expected, $group->getElements());
    }

    public function testDefaultDisplayGroupClassExists()
    {
        $this->assertEquals('Zend_Form_DisplayGroup', $this->form->getDefaultDisplayGroupClass());
    }

    public function testCanSetDefaultDisplayGroupClass()
    {
        $this->testDefaultDisplayGroupClassExists();
        $this->form->setDefaultDisplayGroupClass('Zend_Form_FormTest_DisplayGroup');
        $this->assertEquals('Zend_Form_FormTest_DisplayGroup', $this->form->getDefaultDisplayGroupClass());
    }

    public function testDefaultDisplayGroupClassUsedForNewDisplayGroups()
    {
        $this->form->setDefaultDisplayGroupClass('Zend_Form_FormTest_DisplayGroup');
        $this->setupElements();
        $this->form->addDisplayGroup(array('foo', 'bar'), 'foobar');
        $displayGroup = $this->form->getDisplayGroup('foobar');
        $this->assertTrue($displayGroup instanceof Zend_Form_FormTest_DisplayGroup);
    }

    public function testCanPassDisplayGroupClassWhenAddingDisplayGroup()
    {
        $this->setupElements();
        $this->form->addDisplayGroup(array('foo', 'bar'), 'foobar', array('displayGroupClass' => 'Zend_Form_FormTest_DisplayGroup'));
        $this->assertTrue($this->form->foobar instanceof Zend_Form_FormTest_DisplayGroup);
    }

    /**
     * @group ZF-3254
     */
    public function testAddingDisplayGroupShouldPassOptions()
    {
        $this->testCanAddAndRetrieveMultipleElements();
        $this->form->addDisplayGroup(array('bar', 'bat'), 'barbat', array('disableLoadDefaultDecorators' => true));
        $group = $this->form->getDisplayGroup('barbat');
        $this->assertTrue($group instanceof Zend_Form_DisplayGroup);
        $decorators = $group->getDecorators();
        $this->assertTrue(is_array($decorators));
        $this->assertTrue(empty($decorators));
    }

    // Processing

    public function testPopulateProxiesToSetDefaults()
    {
        $this->testCanAddAndRetrieveMultipleElements();
        $values = array(
            'foo' => 'foovalue',
            'bar' => 'barvalue',
            'baz' => 'bazvalue',
            'bat' => 'batvalue'
        );
        $this->form->populate($values);
        $test     = $this->form->getValues();
        $elements = $this->form->getElements();
        foreach (array_keys($values) as $name) {
            $this->assertEquals($values[$name], $test[$name]);
        }
    }

    public function setupElements()
    {
        $foo = new Zend_Form_Element_Text('foo');
        $foo->addValidator('NotEmpty')
            ->addValidator('Alpha');
        $bar = new Zend_Form_Element_Text('bar');
        $bar->addValidator('NotEmpty')
            ->addValidator('Digits');
        $baz = new Zend_Form_Element_Text('baz');
        $baz->addValidator('NotEmpty')
            ->addValidator('Alnum');
        $this->form->addElements(array($foo, $bar, $baz));
        $this->elementValues = array(
            'foo' => 'fooBarBAZ',
            'bar' => '123456789',
            'baz' => 'foo123BAR',
        );
    }

    public function testIsValidShouldThrowExceptionWithNonArrayArgument()
    {
        try {
            $this->form->isValid(true);
            $this->fail('isValid() should raise exception with non-array argument');
        } catch (Zend_Form_Exception $e) {
            $this->assertContains('expects an array', $e->getMessage());
        }
    }

    public function testCanValidateFullFormContainingOnlyElements()
    {
        $this->_checkZf2794();

        $this->setupElements();
        $this->assertTrue($this->form->isValid($this->elementValues));
        $values = array(
            'foo' => '12345',
            'bar' => 'abc',
            'baz' => 'abc-123'
        );
        $this->assertFalse($this->form->isValid($values));

        $validator = $this->form->foo->getValidator('alpha');
        $this->assertEquals('12345', $validator->value);

        $validator = $this->form->bar->getValidator('digits');
        $this->assertEquals('abc', $validator->value);

        $validator = $this->form->baz->getValidator('alnum');
        $this->assertEquals('abc-123', $validator->value);
    }

    public function testValidationTakesElementRequiredFlagsIntoAccount()
    {
        $this->_checkZf2794();

        $this->setupElements();

        $this->assertTrue($this->form->isValid(array()));

        $this->form->getElement('foo')->setRequired(true);
        $this->assertTrue($this->form->isValid(array(
            'foo' => 'abc',
            'baz' => 'abc123'
        )));
        $this->assertFalse($this->form->isValid(array(
            'baz' => 'abc123'
        )));
    }

    public function testCanValidatePartialFormContainingOnlyElements()
    {
        $this->_checkZf2794();

        $this->setupElements();
        $this->form->getElement('foo')->setRequired(true);
        $this->form->getElement('bar')->setRequired(true);
        $this->form->getElement('baz')->setRequired(true);
        $this->assertTrue($this->form->isValidPartial(array(
            'foo' => 'abc',
            'baz' => 'abc123'
        )));
        $this->assertFalse($this->form->isValidPartial(array(
            'foo' => '123',
            'baz' => 'abc-123'
        )));
    }

    public function setupSubForm()
    {
        $subForm = new Zend_Form_SubForm();
        $foo = new Zend_Form_Element_Text('subfoo');
        $foo->addValidators(array('NotEmpty', 'Alpha'))->setRequired(true);
        $bar = new Zend_Form_Element_Text('subbar');
        $bar->addValidators(array('NotEmpty', 'Digits'));
        $baz = new Zend_Form_Element_Text('subbaz');
        $baz->addValidators(array('NotEmpty', 'Alnum'))->setRequired(true);
        $subForm->addElements(array($foo, $bar, $baz));
        $this->form->addSubForm($subForm, 'sub');
    }

    public function testFullDataArrayUsedToValidateSubFormByDefault()
    {
        $this->_checkZf2794();

        $this->setupElements();
        $this->setupSubForm();
        $data = array(
            'foo'    => 'abcdef',
            'bar'    => '123456',
            'baz'    => '123abc',
            'subfoo' => 'abcdef',
            'subbar' => '123456',
            'subbaz' => '123abc',
        );
        $this->assertTrue($this->form->isValid($data));

        $data = array(
            'foo'    => 'abcdef',
            'bar'    => '123456',
            'baz'    => '123abc',
            'subfoo' => '123',
            'subbar' => 'abc',
            'subbaz' => '123-abc',
        );
        $this->assertFalse($this->form->isValid($data));

        $data = array(
            'foo'    => 'abcdef',
            'bar'    => '123456',
            'baz'    => '123abc',
            'subfoo' => 'abc',
            'subbaz' => '123abc',
        );
        $this->assertTrue($this->form->isValid($data));

        $data = array(
            'foo'    => 'abcdef',
            'bar'    => '123456',
            'baz'    => '123abc',
            'subbar' => '123',
            'subbaz' => '123abc',
        );
        $this->assertFalse($this->form->isValid($data));
    }

    public function testDataKeyWithSameNameAsSubFormIsUsedForValidatingSubForm()
    {
        $this->_checkZf2794();

        $this->setupElements();
        $this->setupSubForm();
        $data = array(
            'foo'    => 'abcdef',
            'bar'    => '123456',
            'baz'    => '123abc',
            'sub'    => array(
                'subfoo' => 'abcdef',
                'subbar' => '123456',
                'subbaz' => '123abc',
            ),
        );
        $this->assertTrue($this->form->isValid($data));

        $data = array(
            'foo'    => 'abcdef',
            'bar'    => '123456',
            'baz'    => '123abc',
            'sub'    => array(
                'subfoo' => '123',
                'subbar' => 'abc',
                'subbaz' => '123-abc',
            )
        );
        $this->assertFalse($this->form->isValid($data));

        $data = array(
            'foo'    => 'abcdef',
            'bar'    => '123456',
            'baz'    => '123abc',
            'sub'    => array(
                'subfoo' => 'abc',
                'subbaz' => '123abc',
            )
        );
        $this->assertTrue($this->form->isValid($data));

        $data = array(
            'foo'    => 'abcdef',
            'bar'    => '123456',
            'baz'    => '123abc',
            'sub'    => array(
                'subbar' => '123',
                'subbaz' => '123abc',
            )
        );
        $this->assertFalse($this->form->isValid($data));
    }

    public function testCanValidateNestedFormsWithElementsBelongingToArrays()
    {
        $form = new Zend_Form();
        $form->setElementsBelongTo('foobar');

        $form->addElement('text', 'firstName')
             ->getElement('firstName')
             ->setRequired(true);

        $form->addElement('text', 'lastName')
             ->getElement('lastName')
             ->setRequired(true);

        $subForm = new Zend_Form_SubForm();
        $subForm->setElementsBelongTo('baz');
        $subForm->addElement('text', 'email')
                ->getElement('email')->setRequired(true);

        $subSubForm = new Zend_Form_SubForm();
        $subSubForm->setElementsBelongTo('bat');
        $subSubForm->addElement('checkbox', 'home')
                   ->getElement('home')->setRequired(true);

        $subForm->addSubForm($subSubForm, 'subSub');

        $form->addSubForm($subForm, 'sub')
             ->addElement('submit', 'save', array('value' => 'submit'));


        $data = array('foobar' => array(
            'firstName' => 'Mabel',
            'lastName'  => 'Cow',
            'baz'    => array(
                'email' => 'mabel@cow.org',
                'bat'   => array(
                    'home' => 1,
                )
            )
        ));
        $this->assertTrue($form->isValid($data));
        $this->assertEquals('Mabel', $form->firstName->getValue());
        $this->assertEquals('Cow', $form->lastName->getValue());
        $this->assertEquals('mabel@cow.org', $form->sub->email->getValue());
        $this->assertEquals(1, $form->sub->subSub->home->getValue());
    }

    public function testCanValidatePartialFormContainingSubForms()
    {
        $this->_checkZf2794();

        $this->setupElements();
        $this->setupSubForm();

        $data = array(
            'subfoo' => 'abcdef',
            'subbar' => '123456',
        );
        $this->assertTrue($this->form->isValidPartial($data));

        $data = array(
            'foo'    => 'abcdef',
            'baz'    => '123abc',
            'sub'    => array(
                'subbar' => '123',
            )
        );
        $this->assertTrue($this->form->isValidPartial($data));

        $data = array(
            'foo'    => 'abcdef',
            'bar'    => '123456',
            'baz'    => '123abc',
            'sub'    => array(
                'subfoo' => '123',
            )
        );
        $this->assertFalse($this->form->isValidPartial($data));
    }

    public function testCanValidatePartialNestedFormsWithElementsBelongingToArrays()
    {
        $this->_checkZf2794();

        $form = new Zend_Form();
        $form->setElementsBelongTo('foobar');

        $form->addElement('text', 'firstName')
             ->getElement('firstName')
             ->setRequired(false);

        $form->addElement('text', 'lastName')
             ->getElement('lastName')
             ->setRequired(true);

        $subForm = new Zend_Form_SubForm();
        $subForm->setElementsBelongTo('baz');
        $subForm->addElement('text', 'email')
                ->getElement('email')
                ->setRequired(true)
                ->addValidator('NotEmpty');

        $subSubForm = new Zend_Form_SubForm();
        $subSubForm->setElementsBelongTo('bat');
        $subSubForm->addElement('checkbox', 'home')
                   ->getElement('home')
                   ->setRequired(true)
                   ->addValidator('InArray', false, array(array('1')));

        $subForm->addSubForm($subSubForm, 'subSub');

        $form->addSubForm($subForm, 'sub')
             ->addElement('submit', 'save', array('value' => 'submit'));


        $data = array('foobar' => array(
            'lastName'  => 'Cow',
        ));
        $this->assertTrue($form->isValidPartial($data));
        $this->assertEquals('Cow', $form->lastName->getValue());
        $firstName = $form->firstName->getValue();
        $email     = $form->sub->email->getValue();
        $home      = $form->sub->subSub->home->getValue();
        $this->assertTrue(empty($firstName));
        $this->assertTrue(empty($email));
        $this->assertTrue(empty($home));

        $form->sub->subSub->home->addValidator('StringLength', false, array(4, 6));
        $data['foobar']['baz'] = array('bat' => array('home' => 'ab'));

        $this->assertFalse($form->isValidPartial($data), var_export($data, 1));
        $this->assertEquals('0', $form->sub->subSub->home->getValue());
        $messages = $form->getMessages();
        $this->assertFalse(empty($messages));
        $this->assertTrue(isset($messages['foobar']['baz']['bat']['home']), var_export($messages, 1));
        $this->assertTrue(isset($messages['foobar']['baz']['bat']['home']['notInArray']), var_export($messages, 1));
    }

    public function testCanValidatePartialNestedFormsWithMultiLevelElementsBelongingToArrays()
    {
        $this->_checkZf2794();

        $form = new Zend_Form();
        $form->setElementsBelongTo('foo[bar]');

        $form->addElement('text', 'firstName')
             ->getElement('firstName')
             ->setRequired(false);

        $form->addElement('text', 'lastName')
             ->getElement('lastName')
             ->setRequired(true);

        $subForm = new Zend_Form_SubForm();
        $subForm->setElementsBelongTo('baz');
        $subForm->addElement('text', 'email')
                ->getElement('email')
                ->setRequired(true)
                ->addValidator('NotEmpty');

        $subSubForm = new Zend_Form_SubForm();
        $subSubForm->setElementsBelongTo('bat[quux]');
        $subSubForm->addElement('checkbox', 'home')
                   ->getElement('home')
                   ->setRequired(true)
                   ->addValidator('InArray', false, array(array('1')));

        $subForm->addSubForm($subSubForm, 'subSub');

        $form->addSubForm($subForm, 'sub')
             ->addElement('submit', 'save', array('value' => 'submit'));


        $data = array('foo' => array(
            'bar' => array(
                'lastName'  => 'Cow',
            ),
        ));
        $this->assertTrue($form->isValidPartial($data));
        $this->assertEquals('Cow', $form->lastName->getValue());
        $firstName = $form->firstName->getValue();
        $email     = $form->sub->email->getValue();
        $home      = $form->sub->subSub->home->getValue();
        $this->assertTrue(empty($firstName));
        $this->assertTrue(empty($email));
        $this->assertTrue(empty($home));

        $form->sub->subSub->home->addValidator('StringLength', false, array(4, 6));
        $data['foo']['bar']['baz'] = array('bat' => array('quux' => array('home' => 'ab')));

        $this->assertFalse($form->isValidPartial($data), var_export($data, 1));
        $this->assertEquals('0', $form->sub->subSub->home->getValue());
    }

    public function testCanGetMessagesOfNestedFormsWithMultiLevelElementsBelongingToArrays()
    {
        $this->_checkZf2794();

        $form = new Zend_Form();
        $form->setElementsBelongTo('foo[bar]');

        $form->addElement('text', 'firstName')
             ->getElement('firstName')
             ->setRequired(false);

        $form->addElement('text', 'lastName')
             ->getElement('lastName')
             ->setRequired(true);

        $subForm = new Zend_Form_SubForm();
        $subForm->setElementsBelongTo('baz');
        $subForm->addElement('text', 'email')
                ->getElement('email')
                ->setRequired(true)
                ->addValidator('NotEmpty');

        $subSubForm = new Zend_Form_SubForm();
        $subSubForm->setElementsBelongTo('bat[quux]');
        $subSubForm->addElement('checkbox', 'home')
                   ->getElement('home')
                   ->setRequired(true)
                   ->addValidator('InArray', false, array(array('1')));

        $subForm->addSubForm($subSubForm, 'subSub');

        $form->addSubForm($subForm, 'sub')
             ->addElement('submit', 'save', array('value' => 'submit'));


        $data = array('foo' => array(
            'bar' => array(
                'lastName'  => 'Cow',
            ),
        ));


        $form->sub->subSub->home->addValidator('StringLength', false, array(4, 6));
        $data['foo']['bar']['baz'] = array('bat' => array('quux' => array('home' => 'ab')));

        $form->isValidPartial($data);

        $messages = $form->getMessages();
        $this->assertFalse(empty($messages));
        $this->assertTrue(isset($messages['foo']['bar']['baz']['bat']['quux']['home']), var_export($messages, 1));
        $this->assertTrue(isset($messages['foo']['bar']['baz']['bat']['quux']['home']['notInArray']), var_export($messages, 1));
    }

    public function testValidatingFormWithDisplayGroupsDoesSameAsWithout()
    {
        $this->setupElements();
        $this->form->addDisplayGroup(array('foo', 'baz'), 'foobaz');
        $this->assertTrue($this->form->isValid($this->elementValues));
        $this->assertFalse($this->form->isValid(array(
            'foo' => '123',
            'bar' => 'abc',
            'baz' => 'abc-123'
        )));
    }

    public function testValidatePartialFormWithDisplayGroupsDoesSameAsWithout()
    {
        $this->setupElements();
        $this->form->addDisplayGroup(array('foo', 'baz'), 'foobaz');
        $this->assertTrue($this->form->isValid(array(
            'foo' => 'abc',
            'baz' => 'abc123'
        )));
        $this->assertFalse($this->form->isValid(array(
            'foo' => '123',
            'baz' => 'abc-123'
        )));
    }

    public function testProcessAjaxReturnsJsonTrueForValidForm()
    {
        $this->setupElements();
        $return = $this->form->processAjax($this->elementValues);
        $this->assertTrue(Zend_Json::decode($return));
    }

    public function testProcessAjaxReturnsJsonTrueForValidPartialForm()
    {
        $this->setupElements();
        $data = array('foo' => 'abcdef', 'baz' => 'abc123');
        $return = $this->form->processAjax($data);
        $this->assertTrue(Zend_Json::decode($return));
    }

    public function testProcessAjaxReturnsJsonWithAllErrorMessagesForInvalidForm()
    {
        $this->setupElements();
        $data = array('foo' => '123456', 'bar' => 'abcdef', 'baz' => 'abc-123');
        $return = Zend_Json::decode($this->form->processAjax($data));
        $this->assertTrue(is_array($return));
        $this->assertEquals(array_keys($data), array_keys($return));
    }

    public function testProcessAjaxReturnsJsonWithAllErrorMessagesForInvalidPartialForm()
    {
        $this->setupElements();
        $data = array('baz' => 'abc-123');
        $return = Zend_Json::decode($this->form->processAjax($data));
        $this->assertTrue(is_array($return));
        $this->assertEquals(array_keys($data), array_keys($return), var_export($return, 1));
    }

    public function testPersistDataStoresDataInSession()
    {
        $this->markTestIncomplete('Zend_Form does not implement session storage at this time');
    }

    public function testCanCheckIfErrorsAreRegistered()
    {
        $this->assertFalse($this->form->isErrors());
        $this->testCanValidateFullFormContainingOnlyElements();
        $this->assertTrue($this->form->isErrors());
    }

    public function testCanRetrieveErrorCodesFromAllElementsAfterFailedValidation()
    {
        $this->_checkZf2794();

        $this->testCanValidateFullFormContainingOnlyElements();
        $codes = $this->form->getErrors();
        $keys = array('foo', 'bar', 'baz');
        $this->assertEquals($keys, array_keys($codes));
    }

    public function testCanRetrieveErrorCodesFromSingleElementAfterFailedValidation()
    {
        $this->_checkZf2794();

        $this->testCanValidateFullFormContainingOnlyElements();
        $codes  = $this->form->getErrors();
        $keys   = array('foo', 'bar', 'baz');
        $errors = $this->form->getErrors('foo');
        $foo    = $this->form->foo;
        $this->assertEquals($foo->getErrors(), $errors);
    }

    public function testCanRetrieveErrorMessagesFromAllElementsAfterFailedValidation()
    {
        $this->_checkZf2794();

        $this->testCanValidateFullFormContainingOnlyElements();
        $codes = $this->form->getMessages();
        $keys = array('foo', 'bar', 'baz');
        $this->assertEquals($keys, array_keys($codes));
    }

    public function testCanRetrieveErrorMessagesFromSingleElementAfterFailedValidation()
    {
        $this->_checkZf2794();

        $this->testCanValidateFullFormContainingOnlyElements();
        $codes    = $this->form->getMessages();
        $keys     = array('foo', 'bar', 'baz');
        $messages = $this->form->getMessages('foo');
        $foo      = $this->form->foo;
        $this->assertEquals($foo->getMessages(), $messages);
    }

    public function testErrorCodesFromSubFormReturnedInSeparateArray()
    {
        $this->_checkZf2794();

        $this->testFullDataArrayUsedToValidateSubFormByDefault();
        $codes    = $this->form->getErrors();
        $this->assertTrue(array_key_exists('sub', $codes));
        $this->assertTrue(is_array($codes['sub']));
        $keys     = array('subfoo', 'subbar', 'subbaz');
        $this->assertEquals($keys, array_keys($codes['sub']));
    }

    public function testCanRetrieveErrorCodesFromSingleSubFormAfterFailedValidation()
    {
        $this->_checkZf2794();

        $this->testFullDataArrayUsedToValidateSubFormByDefault();
        $codes    = $this->form->getErrors('sub');
        $this->assertTrue(is_array($codes));
        $this->assertFalse(empty($codes));
        $keys     = array('subfoo', 'subbar', 'subbaz');
        $this->assertEquals($keys, array_keys($codes));
    }

    public function testGetErrorsHonorsElementsBelongTo()
    {
        $this->_checkZf2794();

        $subForm = new Zend_Form_SubForm();
        $subForm->setElementsBelongTo('foo[bar]');
        $subForm->addElement('text', 'test')->test
            ->setRequired(true);

        $this->form->addSubForm($subForm, 'sub');

        $data = array('foo' => array(
            'bar' => array(
                'test' => '',
            ),
        ));

        $this->form->isValid($data);
        $codes = $this->form->getErrors();
        $this->assertFalse(empty($codes['foo']['bar']['test']));
    }

    public function testErrorMessagesFromSubFormReturnedInSeparateArray()
    {
        $this->_checkZf2794();

        $this->testFullDataArrayUsedToValidateSubFormByDefault();
        $data = array(
            'foo'    => 'abcdef',
            'bar'    => '123456',
            'baz'    => '123abc',
            'subfoo' => '123',
            'subbar' => 'abc',
            'subbaz' => '123-abc',
        );
        $this->assertFalse($this->form->isValid($data));

        $codes    = $this->form->getMessages();
        $this->assertTrue(array_key_exists('sub', $codes));
        $this->assertTrue(is_array($codes['sub']));
        $keys     = array('subfoo', 'subbar', 'subbaz');
        $this->assertEquals($keys, array_keys($codes['sub']));
    }

    public function testCanRetrieveErrorMessagesFromSingleSubFormAfterFailedValidation()
    {
        $this->_checkZf2794();

        $this->testFullDataArrayUsedToValidateSubFormByDefault();
        $data = array(
            'foo'    => 'abcdef',
            'bar'    => '123456',
            'baz'    => '123abc',
            'subfoo' => '123',
            'subbar' => 'abc',
            'subbaz' => '123-abc',
        );

        $this->assertFalse($this->form->isValid($data));
        $codes    = $this->form->getMessages('sub');
        $this->assertTrue(is_array($codes));
        $this->assertFalse(empty($codes));
        $keys     = array('subfoo', 'subbar', 'subbaz');
        $this->assertEquals($keys, array_keys($codes), var_export($codes, 1));
    }

    public function testErrorMessagesAreLocalizedWhenTranslateAdapterPresent()
    {
        $this->_checkZf2794();

        $translations = include dirname(__FILE__) . '/_files/locale/array.php';
        $translate = new Zend_Translate('array', $translations, 'en');
        $translate->setLocale('en');

        $this->form->addElements(array(
            'foo' => array(
                'type' => 'text',
                'options' => array(
                    'required'   => true,
                    'validators' => array('NotEmpty')
                )
            ),
            'bar' => array(
                'type' => 'text',
                'options' => array(
                    'required'   => true,
                    'validators' => array('Digits')
                )
            ),
        ))
        ->setTranslator($translate);

        $data = array(
            'foo' => '',
            'bar' => 'abc',
        );
        if ($this->form->isValid($data)) {
            $this->fail('Form should not validate');
        }

        $messages = $this->form->getMessages();
        $this->assertTrue(isset($messages['foo']));
        $this->assertTrue(isset($messages['bar']));

        foreach ($messages['foo'] as $key => $message) {
            if (array_key_exists($key, $translations)) {
                $this->assertEquals($translations[$key], $message);
            } else {
                $this->fail('Translation for ' . $key . ' does not exist?');
            }
        }
        foreach ($messages['bar'] as $key => $message) {
            if (array_key_exists($key, $translations)) {
                $this->assertEquals($translations[$key], $message);
            } else {
                $this->fail('Translation for ' . $key . ' does not exist?');
            }
        }
    }

    public function testErrorMessagesFromPartialValidationAreLocalizedWhenTranslateAdapterPresent()
    {
        $this->_checkZf2794();

        $translations = include dirname(__FILE__) . '/_files/locale/array.php';
        $translate = new Zend_Translate('array', $translations, 'en');
        $translate->setLocale('en');

        $this->form->addElements(array(
            'foo' => array(
                'type' => 'text',
                'options' => array(
                    'required'   => true,
                    'validators' => array('NotEmpty')
                )
            ),
            'bar' => array(
                'type' => 'text',
                'options' => array(
                    'required'   => true,
                    'validators' => array('Digits')
                )
            ),
        ))
        ->setTranslator($translate);

        $data = array(
            'foo' => '',
        );
        if ($this->form->isValidPartial($data)) {
            $this->fail('Form should not validate');
        }

        $messages = $this->form->getMessages();
        $this->assertTrue(isset($messages['foo']));
        $this->assertFalse(isset($messages['bar']));

        foreach ($messages['foo'] as $key => $message) {
            if (array_key_exists($key, $translations)) {
                $this->assertEquals($translations[$key], $message);
            } else {
                $this->fail('Translation for ' . $key . ' does not exist?');
            }
        }
    }

    public function testErrorMessagesFromProcessAjaxAreLocalizedWhenTranslateAdapterPresent()
    {
        $this->_checkZf2794();

        $translations = include dirname(__FILE__) . '/_files/locale/array.php';
        $translate = new Zend_Translate('array', $translations, 'en');
        $translate->setLocale('en');

        $this->form->addElements(array(
            'foo' => array(
                'type' => 'text',
                'options' => array(
                    'required'   => true,
                    'validators' => array('NotEmpty')
                )
            ),
            'bar' => array(
                'type' => 'text',
                'options' => array(
                    'required'   => true,
                    'validators' => array('Digits')
                )
            ),
        ))
        ->setTranslator($translate);

        $data = array(
            'foo' => '',
        );
        $return = $this->form->processAjax($data);
        $messages = Zend_Json::decode($return);
        $this->assertTrue(is_array($messages));

        $this->assertTrue(isset($messages['foo']));
        $this->assertFalse(isset($messages['bar']));

        foreach ($messages['foo'] as $key => $message) {
            if (array_key_exists($key, $translations)) {
                $this->assertEquals($translations[$key], $message);
            } else {
                $this->fail('Translation for ' . $key . ' does not exist?');
            }
        }
    }

    /**#@+
     * @group ZF-2988
     */
    public function testSettingErrorMessageShouldOverrideValidationErrorMessages()
    {
        $this->form->addElement('text', 'foo', array('validators' => array('Alpha')));
        $this->form->addErrorMessage('Invalid values entered');
        $this->assertFalse($this->form->isValid(array('foo' => 123)));
        $messages = $this->form->getMessages();
        $this->assertEquals(1, count($messages));
        $this->assertEquals('Invalid values entered', array_shift($messages));
    }

    public function testCustomErrorMessagesShouldBeManagedInAStack()
    {
        $this->form->addElement('text', 'foo', array('validators' => array('Alpha')));
        $this->form->addErrorMessage('Invalid values entered');
        $this->form->addErrorMessage('Really, they are not valid');
        $messages = $this->form->getErrorMessages();
        $this->assertEquals(2, count($messages));

        $this->assertFalse($this->form->isValid(array('foo' => 123)));
        $messages = $this->form->getMessages();
        $this->assertEquals(2, count($messages));
        $this->assertEquals('Invalid values entered', array_shift($messages));
        $this->assertEquals('Really, they are not valid', array_shift($messages));
    }

    public function testShouldAllowSettingMultipleErrorMessagesAtOnce()
    {
        $set1 = array('foo', 'bar', 'baz');
        $this->form->addErrorMessages($set1);
        $this->assertSame($set1, $this->form->getErrorMessages());
    }

    public function testSetErrorMessagesShouldOverwriteMessages()
    {
        $set1 = array('foo', 'bar', 'baz');
        $set2 = array('bat', 'cat');
        $this->form->addErrorMessages($set1);
        $this->assertSame($set1, $this->form->getErrorMessages());
        $this->form->setErrorMessages($set2);
        $this->assertSame($set2, $this->form->getErrorMessages());
    }

    public function testCustomErrorMessageStackShouldBeClearable()
    {
        $this->testCustomErrorMessagesShouldBeManagedInAStack();
        $this->form->clearErrorMessages();
        $messages = $this->form->getErrorMessages();
        $this->assertTrue(empty($messages));
    }

    public function testCustomErrorMessagesShouldBeTranslated()
    {
        $translations = array(
            'foo' => 'Foo message',
        );
        $translate = new Zend_Translate('array', $translations);
        $this->form->addElement('text', 'foo', array('validators' => array('Alpha')));
        $this->form->setTranslator($translate)
                      ->addErrorMessage('foo');
        $this->assertFalse($this->form->isValid(array('foo' => 123)));
        $messages = $this->form->getMessages();
        $this->assertEquals(1, count($messages));
        $this->assertEquals('Foo message', array_shift($messages));
    }

    public function testShouldAllowMarkingFormAsInvalid()
    {
        $this->form->addErrorMessage('Invalid values entered');
        $this->assertFalse($this->form->isErrors());
        $this->form->markAsError();
        $this->assertTrue($this->form->isErrors());
        $messages = $this->form->getMessages();
        $this->assertEquals(1, count($messages));
        $this->assertEquals('Invalid values entered', array_shift($messages));
    }

    public function testShouldAllowPushingErrorsOntoErrorStackWithErrorMessages()
    {
        $this->assertFalse($this->form->isErrors());
        $this->form->setErrors(array('Error 1', 'Error 2'))
                   ->addError('Error 3')
                   ->addErrors(array('Error 4', 'Error 5'));
        $this->assertTrue($this->form->isErrors());
        $messages = $this->form->getMessages();
        $this->assertEquals(5, count($messages));
        foreach (range(1, 5) as $id) {
            $message = 'Error ' . $id;
            $this->assertContains($message, $messages);
        }
    }

    /**#@-*/

    // View object

    public function getView()
    {
        $view = new Zend_View();
        $libPath = dirname(__FILE__) . '/../../../library';
        $view->addHelperPath($libPath . '/Zend/View/Helper');
        return $view;
    }

    public function testGetViewRetrievesFromViewRendererByDefault()
    {
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
        $viewRenderer->initView();
        $view = $viewRenderer->view;
        $test = $this->form->getView();
        $this->assertSame($view, $test);
    }

    public function testGetViewReturnsNullWhenNoViewRegisteredWithViewRenderer()
    {
        $this->assertNull($this->form->getView());
    }

    public function testCanSetView()
    {
        $view = new Zend_View();
        $this->assertNull($this->form->getView());
        $this->form->setView($view);
        $received = $this->form->getView();
        $this->assertSame($view, $received);
    }

    // Decorators

    public function testFormDecoratorRegisteredByDefault()
    {
        $this->_checkZf2794();

        $decorator = $this->form->getDecorator('form');
        $this->assertTrue($decorator instanceof Zend_Form_Decorator_Form);
    }

    public function testCanDisableRegisteringFormDecoratorsDuringInitialization()
    {
        $form = new Zend_Form(array('disableLoadDefaultDecorators' => true));
        $decorators = $form->getDecorators();
        $this->assertEquals(array(), $decorators);
    }

    public function testCanAddSingleDecoratorAsString()
    {
        $this->_checkZf2794();

        $this->form->clearDecorators();
        $this->assertFalse($this->form->getDecorator('viewHelper'));

        $this->form->addDecorator('viewHelper');
        $decorator = $this->form->getDecorator('viewHelper');
        $this->assertTrue($decorator instanceof Zend_Form_Decorator_ViewHelper);
    }

    public function testNotCanRetrieveSingleDecoratorRegisteredAsStringUsingClassName()
    {
        $this->assertFalse($this->form->getDecorator('Zend_Form_Decorator_Form'));
    }

    public function testCanAddSingleDecoratorAsDecoratorObject()
    {
        $this->form->clearDecorators();
        $this->assertFalse($this->form->getDecorator('viewHelper'));

        $decorator = new Zend_Form_Decorator_ViewHelper;
        $this->form->addDecorator($decorator);
        $test = $this->form->getDecorator('Zend_Form_Decorator_ViewHelper');
        $this->assertSame($decorator, $test);
    }

    public function testCanRetrieveSingleDecoratorRegisteredAsDecoratorObjectUsingShortName()
    {
        $this->_checkZf2794();

        $this->form->clearDecorators();
        $this->assertFalse($this->form->getDecorator('viewHelper'));

        $decorator = new Zend_Form_Decorator_ViewHelper;
        $this->form->addDecorator($decorator);
        $test = $this->form->getDecorator('viewHelper');
        $this->assertSame($decorator, $test);
    }

    public function testCanAddMultipleDecorators()
    {
        $this->_checkZf2794();

        $this->form->clearDecorators();
        $this->assertFalse($this->form->getDecorator('viewHelper'));

        $testDecorator = new Zend_Form_Decorator_Errors;
        $this->form->addDecorators(array(
            'ViewHelper',
            $testDecorator
        ));

        $viewHelper = $this->form->getDecorator('viewHelper');
        $this->assertTrue($viewHelper instanceof Zend_Form_Decorator_ViewHelper);
        $decorator = $this->form->getDecorator('errors');
        $this->assertSame($testDecorator, $decorator);
    }

    public function testRemoveDecoratorReturnsFalseForUnregisteredDecorators()
    {
        $this->_checkZf2794();

        $this->assertFalse($this->form->removeDecorator('foobar'));
    }

    public function testCanRemoveDecorator()
    {
        $this->_checkZf2794();

        $this->testFormDecoratorRegisteredByDefault();
        $this->form->removeDecorator('form');
        $this->assertFalse($this->form->getDecorator('form'));
    }

    /**
     * @group ZF-3069
     */
    public function testRemovingNamedDecoratorShouldWork()
    {
        $this->_checkZf2794();
        $this->form->setDecorators(array(
            'FormElements',
            array(array('div' => 'HtmlTag'), array('tag' => 'div')),
            array(array('fieldset' => 'HtmlTag'), array('tag' => 'fieldset')),
        ));
        $decorators = $this->form->getDecorators();
        $this->assertTrue(array_key_exists('div', $decorators));
        $this->assertTrue(array_key_exists('fieldset', $decorators));
        $this->form->removeDecorator('div');
        $decorators = $this->form->getDecorators();
        $this->assertFalse(array_key_exists('div', $decorators));
        $this->assertTrue(array_key_exists('fieldset', $decorators));
    }

    public function testCanClearAllDecorators()
    {
        $this->_checkZf2794();

        $this->testCanAddMultipleDecorators();
        $this->form->clearDecorators();
        $this->assertFalse($this->form->getDecorator('viewHelper'));
        $this->assertFalse($this->form->getDecorator('fieldset'));
    }

    public function testCanAddDecoratorAliasesToAllowMultipleDecoratorsOfSameType()
    {
        $this->_checkZf2794();

        $this->form->setDecorators(array(
            array('HtmlTag', array('tag' => 'div')),
            array('decorator' => array('FooBar' => 'HtmlTag'), 'options' => array('tag' => 'dd')),
        ));
        $decorator = $this->form->getDecorator('FooBar');
        $this->assertTrue($decorator instanceof Zend_Form_Decorator_HtmlTag);
        $this->assertEquals('dd', $decorator->getOption('tag'));

        $decorator = $this->form->getDecorator('HtmlTag');
        $this->assertTrue($decorator instanceof Zend_Form_Decorator_HtmlTag);
        $this->assertEquals('div', $decorator->getOption('tag'));
    }

    public function testRetrievingNamedDecoratorShouldNotReorderDecorators()
    {
        $this->form->setDecorators(array(
            'FormElements',
            array(array('div' => 'HtmlTag'), array('tag' => 'div')),
            array(array('fieldset' => 'HtmlTag'), array('tag' => 'fieldset')),
            'Form',
        ));

        $decorator  = $this->form->getDecorator('fieldset');
        $decorators = $this->form->getDecorators();
        $i          = 0;
        $order      = array();

        foreach (array_keys($decorators) as $name) {
            $order[$name] = $i;
            ++$i;
        }
        $this->assertEquals(2, $order['fieldset'], var_export($order, 1));
    }

    // Rendering

    public function checkMarkup($html)
    {
        $this->assertFalse(empty($html));
        $this->assertContains('<form', $html);
        $this->assertRegexp('/<form[^>]+action="' . $this->form->getAction() . '"/', $html);
        $this->assertRegexp('/<form[^>]+method="' . $this->form->getMethod() . '"/i', $html);
        $this->assertRegexp('#<form[^>]+enctype="application/x-www-form-urlencoded"#', $html);
        $this->assertContains('</form>', $html);
    }

    public function testRenderReturnsMarkup()
    {
        $this->setupElements();
        $html = $this->form->render($this->getView());
        $this->checkMarkup($html);
    }

    public function testRenderReturnsMarkupRepresentingAllElements()
    {
        $this->testRenderReturnsMarkup();
        $html = $this->form->render();
        foreach ($this->form->getElements() as $key => $element) {
            $this->assertFalse(empty($key));
            $this->assertFalse(is_numeric($key));
            $this->assertContains('<input', $html);
            $this->assertRegexp('/<input type="text" name="' . $key . '"/', $html);
        }
    }

    public function testRenderReturnsMarkupContainingSubForms()
    {
        $this->setupElements();
        $this->setupSubForm();
        $this->form->setView($this->getView());
        $html = $this->form->render();
        $this->assertRegexp('/<fieldset/', $html);
        $this->assertContains('</fieldset>', $html);
        foreach ($this->form->sub as $key => $item) {
            $this->assertFalse(empty($key));
            $this->assertFalse(is_numeric($key));
            $this->assertContains('<input', $html);
            $pattern = '/<input type="text" name="sub\[' . $key . '\]"/';
            $this->assertRegexp($pattern, $html, 'Pattern: ' . $pattern . "\nHTML:\n" . $html);
        }
    }

    public function testRenderReturnsMarkupContainingDisplayGroups()
    {
        $this->setupElements();
        $this->form->addDisplayGroup(array('foo', 'baz'), 'foobaz', array('legend' => 'Display Group'));
        $this->form->setView($this->getView());
        $html = $this->html = $this->form->render();
        $this->assertRegexp('/<fieldset/', $html);
        $this->assertContains('</fieldset>', $html);
        $this->assertRegexp('#<legend>Display Group</legend>#', $html, $html);
        $dom = new DOMDocument();
        $dom->loadHTML($html);
        $fieldsets = $dom->getElementsByTagName('fieldset');
        $this->assertTrue(0 < $fieldsets->length);
        $fieldset = $fieldsets->item(0);
        $nodes = $fieldset->childNodes;
        $this->assertNotNull($nodes);
        for ($i = 0; $i < $nodes->length; ++$i) {
            $node = $nodes->item($i);
            if ('input' != $node->nodeName) {
                continue;
            }
            $this->assertTrue($node->hasAttribute('name'));
            $nameNode = $node->getAttributeNode('name');
            switch ($i) {
                case 0:
                    $this->assertEquals('foo', $nameNode->nodeValue);
                    break;
                case 1:
                    $this->assertEquals('baz', $nameNode->nodeValue);
                    break;
                default:
                    $this->fail('There should only be two input nodes in this display group: ' . $html);
            }
        }
    }

    public function testRenderDoesNotRepeatElementsInDisplayGroups()
    {
        $this->testRenderReturnsMarkupContainingDisplayGroups();
        if (!preg_match_all('#<input[^>]+name="foo"#', $this->html, $matches)) {
            $this->fail("Should find foo element in rendered form");
        }
        $this->assertEquals(1, count($matches));
        $this->assertEquals(1, count($matches[0]));
    }

    public function testElementsRenderAsArrayMembersWhenElementsBelongToAnArray()
    {
        $this->setupElements();
        $this->form->setElementsBelongTo('anArray');
        $html = $this->form->render($this->getView());
        $this->assertContains('name="anArray[foo]"', $html);
        $this->assertContains('name="anArray[bar]"', $html);
        $this->assertContains('name="anArray[baz]"', $html);
        $this->assertContains('id="anArray-foo"', $html);
        $this->assertContains('id="anArray-bar"', $html);
        $this->assertContains('id="anArray-baz"', $html);
    }

    public function testElementsRenderAsSubArrayMembersWhenElementsBelongToASubArray()
    {
        $this->setupElements();
        $this->form->setElementsBelongTo('data[foo]');
        $html = $this->form->render($this->getView());
        $this->assertContains('name="data[foo][foo]"', $html);
        $this->assertContains('name="data[foo][bar]"', $html);
        $this->assertContains('name="data[foo][baz]"', $html);
        $this->assertContains('id="data-foo-foo"', $html);
        $this->assertContains('id="data-foo-bar"', $html);
        $this->assertContains('id="data-foo-baz"', $html);
    }

    public function testElementsRenderAsArrayMembersWhenRenderAsArrayToggled()
    {
        $this->setupElements();
        $this->form->setName('data')
                   ->setIsArray(true);
        $html = $this->form->render($this->getView());
        $this->assertContains('name="data[foo]"', $html);
        $this->assertContains('name="data[bar]"', $html);
        $this->assertContains('name="data[baz]"', $html);
        $this->assertContains('id="data-foo"', $html);
        $this->assertContains('id="data-bar"', $html);
        $this->assertContains('id="data-baz"', $html);
    }

    public function testElementsRenderAsMembersOfSubFormsWithElementsBelongTo()
    {
        $this->form->setName('data')
            ->setIsArray(true);
        $subForm = new Zend_Form_SubForm();
        $subForm->setElementsBelongTo('billing[info]');
        $subForm->addElement('text', 'name');
        $subForm->addElement('text', 'number');
        $this->form->addSubForm($subForm, 'sub');

        $html = $this->form->render($this->getView());
        $this->assertContains('name="data[billing][info][name]', $html);
        $this->assertContains('name="data[billing][info][number]', $html);
        $this->assertContains('id="data-billing-info-name"', $html);
        $this->assertContains('id="data-billing-info-number"', $html);
    }

    public function testToStringProxiesToRender()
    {
        $this->setupElements();
        $this->form->setView($this->getView());
        $html = $this->form->__toString();
        $this->checkMarkup($html);
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
        $this->form->setDecorators(array(
            array(
                'decorator' => 'Callback',
                'options'   => array('callback' => array($this, 'raiseDecoratorException'))
            ),
        ));
        $origErrorHandler = set_error_handler(array($this, 'handleDecoratorErrors'), E_USER_WARNING);

        $text = $this->form->__toString();

        restore_error_handler();

        $this->assertTrue(empty($text));
        $this->assertTrue(isset($this->error));
        $this->assertContains('Raising exception in decorator callback', $this->error);
    }

    /**
     * ZF-2718
     */
    public function testHiddenElementsGroupedWhenRendered()
    {
        $this->markTestIncomplete('Scheduling for future release');
        $this->form->addElements(array(
            array('type' => 'hidden', 'name' => 'first', 'options' => array('value' => 'first value')),
            array('type' => 'text', 'name' => 'testone'),
            array('type' => 'hidden', 'name' => 'second', 'options' => array('value' => 'second value')),
            array('type' => 'text', 'name' => 'testtwo'),
            array('type' => 'hidden', 'name' => 'third', 'options' => array('value' => 'third value')),
            array('type' => 'text', 'name' => 'testthree'),
        ));
        $html = $this->form->render($this->getView());
        if (!preg_match('#(<input type="hidden" name="[^>].*>\s*){3}#', $html, $matches)) {
            $this->fail('Hidden elements should be grouped');
        }
        foreach (array('first', 'second', 'third') as $which) {
            $this->assertRegexp('#<input[^]*name="' . $which . '"#', $matches[0]);
            $this->assertRegexp('#<input[^]*value="' . $which . ' value"#', $matches[0]);
        }
    }

    // Localization

    public function testTranslatorIsNullByDefault()
    {
        $this->assertNull($this->form->getTranslator());
    }

    public function testCanSetTranslator()
    {
        require_once 'Zend/Translate/Adapter/Array.php';
        $translator = new Zend_Translate('array', array('foo' => 'bar'));
        $this->form->setTranslator($translator);
        $received = $this->form->getTranslator($translator);
        $this->assertSame($translator->getAdapter(), $received);
    }

    public function testCanSetDefaultGlobalTranslator()
    {
        $this->assertNull($this->form->getTranslator());
        $translator = new Zend_Translate('array', array('foo' => 'bar'));
        Zend_Form::setDefaultTranslator($translator);

        $received = Zend_Form::getDefaultTranslator();
        $this->assertSame($translator->getAdapter(), $received);

        $received = $this->form->getTranslator();
        $this->assertSame($translator->getAdapter(), $received);

        $form = new Zend_Form();
        $received = $form->getTranslator();
        $this->assertSame($translator->getAdapter(), $received);
    }

    public function testLocalTranslatorPreferredOverDefaultGlobalTranslator()
    {
        $this->assertNull($this->form->getTranslator());
        $translatorDefault = new Zend_Translate('array', array('foo' => 'bar'));
        Zend_Form::setDefaultTranslator($translatorDefault);

        $received = $this->form->getTranslator();
        $this->assertSame($translatorDefault->getAdapter(), $received);

        $translator = new Zend_Translate('array', array('foo' => 'bar'));
        $this->form->setTranslator($translator);
        $received = $this->form->getTranslator();
        $this->assertNotSame($translatorDefault->getAdapter(), $received);
        $this->assertSame($translator->getAdapter(), $received);
    }

    public function testTranslatorFromRegistryUsedWhenNoneRegistered()
    {
        $this->assertNull($this->form->getTranslator());
        $translator = new Zend_Translate('array', array('foo' => 'bar'));
        Zend_Registry::set('Zend_Translate', $translator);

        $received = Zend_Form::getDefaultTranslator();
        $this->assertSame($translator->getAdapter(), $received);

        $received = $this->form->getTranslator();
        $this->assertSame($translator->getAdapter(), $received);

        $form = new Zend_Form();
        $received = $form->getTranslator();
        $this->assertSame($translator->getAdapter(), $received);
    }

    public function testCanDisableTranslation()
    {
        $this->testCanSetDefaultGlobalTranslator();
        $this->form->setDisableTranslator(true);
        $this->assertNull($this->form->getTranslator());
    }

    // Iteration

    public function testFormObjectIsIterableAndIteratesElements()
    {
        $this->setupElements();
        $expected = array('foo', 'bar', 'baz');
        $received = array();
        foreach ($this->form as $key => $value) {
            $received[] = $key;
        }
        $this->assertSame($expected, $received);
    }

    public function testFormObjectIteratesElementsInExpectedOrder()
    {
        $this->setupElements();
        $this->form->addElement('text', 'checkorder', array('order' => 2));
        $expected = array('foo', 'bar', 'checkorder', 'baz');
        $received = array();
        foreach ($this->form as $key => $value) {
            $received[] = $key;
            $this->assertTrue($value instanceof Zend_Form_Element);
        }
        $this->assertSame($expected, $received);
    }

    public function testFormObjectIteratesElementsInExpectedOrderWhenAllElementsHaveOrder()
    {
        $this->form->addElement('submit', 'submit')->submit->setLabel('Submit')->setOrder(30);
        $this->form->addElement('text', 'name')->name->setLabel('Name')->setOrder(10);
        $this->form->addElement('text', 'email')->email->setLabel('E-mail')->setOrder(20);

        $expected = array('name', 'email', 'submit');
        $received = array();
        foreach ($this->form as $key => $value) {
            $received[] = $key;
            $this->assertTrue($value instanceof Zend_Form_Element);
        }
        $this->assertSame($expected, $received);
    }

    public function testFormObjectIteratesElementsInExpectedOrderWhenFirstElementHasNoOrderSpecified()
    {
        $this->form->addElement(new Zend_Form_Element('a',array('label'=>'a')))
                   ->addElement(new Zend_Form_Element('b',array('label'=>'b', 'order' => 0)))
                   ->addElement(new Zend_Form_Element('c',array('label'=>'c', 'order' => 1)))
                   ->setView($this->getView());
        $test = $this->form->render();
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

    public function testFormObjectIteratesElementsAndSubforms()
    {
        $this->setupElements();
        $this->setupSubForm();
        $expected = array('foo', 'bar', 'baz', 'sub');
        $received = array();
        foreach ($this->form as $key => $value) {
            $received[] = $key;
            $this->assertTrue(($value instanceof Zend_Form_Element)
                              or ($value instanceof Zend_Form_SubForm));
        }
        $this->assertSame($expected, $received);
    }

    public function testFormObjectIteratesDisplayGroupsButSkipsDisplayGroupElements()
    {
        $this->setupElements();
        $this->form->addDisplayGroup(array('foo', 'baz'), 'foobaz');
        $expected = array('bar', 'foobaz');
        $received = array();
        foreach ($this->form as $key => $value) {
            $received[] = $key;
            $this->assertTrue(($value instanceof Zend_Form_Element)
                              or ($value instanceof Zend_Form_DisplayGroup));
        }
        $this->assertSame($expected, $received);
    }

    public function testRemovingFormItemsShouldNotRaiseExceptionsDuringIteration()
    {
        $this->setupElements();
        $bar = $this->form->bar;
        $this->form->removeElement('bar');

        try {
            foreach ($this->form as $item) {
            }
        } catch (Exception $e) {
            $this->fail('Exceptions should not be raised by iterator when elements are removed; error message: ' . $e->getMessage());
        }

        $this->form->addElement($bar);
        $this->form->addDisplayGroup(array('baz', 'bar'), 'bazbar');
        $this->form->removeDisplayGroup('bazbar');

        try {
            foreach ($this->form as $item) {
            }
        } catch (Exception $e) {
            $this->fail('Exceptions should not be raised by iterator when elements are removed; error message: ' . $e->getMessage());
        }

        $subForm = new Zend_Form_SubForm;
        $subForm->addElements(array('foo' => 'text', 'bar' => 'text'));
        $this->form->addSubForm($subForm, 'page1');
        $this->form->removeSubForm('page1');

        try {
            foreach ($this->form as $item) {
            }
        } catch (Exception $e) {
            $this->fail('Exceptions should not be raised by iterator when elements are removed; error message: ' . $e->getMessage());
        }
    }

    public function testClearingAttachedItemsShouldNotCauseIterationToRaiseExceptions()
    {
        $form = new Zend_Form();
        $form->addElements(array(
            'username' => 'text',
            'password' => 'text',
        ));
        $form->clearElements();

        try {
            foreach ($form as $item) {
            }
        } catch (Zend_Form_Exception $e) {
            $message = "Clearing elements prior to iteration should not cause iteration to fail;\n"
                     . $e->getMessage();
            $this->fail($message);
        }

        $form->addElements(array(
                 'username' => 'text',
                 'password' => 'text',
             ))
             ->addDisplayGroup(array('username', 'password'), 'login');
        $form->clearDisplayGroups();

        try {
            foreach ($form as $item) {
            }
        } catch (Zend_Form_Exception $e) {
            $message = "Clearing display groups prior to iteration should not cause iteration to fail;\n"
                     . $e->getMessage();
            $this->fail($message);
        }

        $subForm = new Zend_Form_SubForm();
        $form->addSubForm($subForm, 'foo');
        $form->clearSubForms();

        try {
            foreach ($form as $item) {
            }
        } catch (Zend_Form_Exception $e) {
            $message = "Clearing sub forms prior to iteration should not cause iteration to fail;\n"
                     . $e->getMessage();
            $this->fail($message);
        }
    }

    // Countable

    public function testCanCountFormObject()
    {
        $this->setupElements();
        $this->assertEquals(3, count($this->form));
    }

    public function testCountingFormObjectCountsSubForms()
    {
        $this->setupElements();
        $this->setupSubForm();
        $this->assertEquals(4, count($this->form));
    }

    public function testCountingFormCountsDisplayGroupsButOmitsElementsInDisplayGroups()
    {
        $this->testCountingFormObjectCountsSubForms();
        $this->form->addDisplayGroup(array('foo', 'baz'), 'foobaz');
        $this->assertEquals(3, count($this->form));
    }

    // Element decorators and plugin paths

    public function testCanSetAllElementDecoratorsAtOnce()
    {
        $this->_checkZf2794();

        $this->setupElements();
        $this->form->setElementDecorators(array(
            array('ViewHelper'),
            array('Label'),
            array('Fieldset'),
        ));
        foreach ($this->form->getElements() as $element) {
            $this->assertFalse($element->getDecorator('Errors'));
            $this->assertFalse($element->getDecorator('HtmlTag'));
            $decorator = $element->getDecorator('ViewHelper');
            $this->assertTrue($decorator instanceof Zend_Form_Decorator_ViewHelper);
            $decorator = $element->getDecorator('Label');
            $this->assertTrue($decorator instanceof Zend_Form_Decorator_Label);
            $decorator = $element->getDecorator('Fieldset');
            $this->assertTrue($decorator instanceof Zend_Form_Decorator_Fieldset);
        }
    }

    /**
     * @group ZF-3597
     */
    public function testSettingElementDecoratorsWithConcreteDecoratorShouldHonorOrder()
    {
        $this->form->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'table')),
            'Form',
        ));
        $this->form->addElementPrefixPath('My_Decorator', dirname(__FILE__) . '/_files/decorators/', 'decorator');
        $this->form->addElement('text', 'test', array(
            'label'       => 'Foo',
            'description' => 'sample description',
        ));

        require_once dirname(__FILE__) . '/_files/decorators/TableRow.php';
        $decorator = new My_Decorator_TableRow();
        $this->form->setElementDecorators(array(
            'ViewHelper',
            $decorator,
        ));
        $html = $this->form->render($this->getView());
        $this->assertRegexp('#<tr><td>Foo</td><td>.*?<input[^>]+>.*?</td><td>sample description</td></tr>#s', $html, $html);
    }

    /**#@+
     * @group ZF-3228
     */
    public function testShouldAllowSpecifyingSpecificElementsToDecorate()
    {
        $this->_checkZf2794();

        $this->setupElements();
        $this->form->setElementDecorators(
            array(
                'Description',
                'Form',
                'Fieldset',
            ),
            array(
                'bar',
            )
        );

        $element = $this->form->bar;
        $this->assertFalse($element->getDecorator('ViewHelper'));
        $this->assertFalse($element->getDecorator('Errors'));
        $this->assertFalse($element->getDecorator('Label'));
        $this->assertFalse($element->getDecorator('HtmlTag'));
        $decorator = $element->getDecorator('Description');
        $this->assertTrue($decorator instanceof Zend_Form_Decorator_Description);
        $decorator = $element->getDecorator('Form');
        $this->assertTrue($decorator instanceof Zend_Form_Decorator_Form);
        $decorator = $element->getDecorator('Fieldset');
        $this->assertTrue($decorator instanceof Zend_Form_Decorator_Fieldset);

        foreach (array('foo', 'baz') as $name) {
            $element = $this->form->$name;
            $this->assertFalse($element->getDecorator('Form'));
            $this->assertFalse($element->getDecorator('Fieldset'));
        }
    }

    public function testShouldAllowSpecifyingListOfElementsNotToDecorate()
    {
        $this->_checkZf2794();

        $this->setupElements();
        $this->form->setElementDecorators(
            array(
                'Description',
                'Form',
                'Fieldset',
            ),
            array(
                'foo',
                'baz',
            ),
            false
        );

        $element = $this->form->bar;
        $this->assertFalse($element->getDecorator('ViewHelper'));
        $this->assertFalse($element->getDecorator('Errors'));
        $this->assertFalse($element->getDecorator('Label'));
        $this->assertFalse($element->getDecorator('HtmlTag'));
        $decorator = $element->getDecorator('Description');
        $this->assertTrue($decorator instanceof Zend_Form_Decorator_Description);
        $decorator = $element->getDecorator('Form');
        $this->assertTrue($decorator instanceof Zend_Form_Decorator_Form);
        $decorator = $element->getDecorator('Fieldset');
        $this->assertTrue($decorator instanceof Zend_Form_Decorator_Fieldset);

        foreach (array('foo', 'baz') as $name) {
            $element = $this->form->$name;
            $this->assertFalse($element->getDecorator('Form'));
            $this->assertFalse($element->getDecorator('Fieldset'));
        }
    }
    /**#@-*/

    public function testCanSetAllElementFiltersAtOnce()
    {
        $this->_checkZf2794();

        $this->setupElements();
        $this->form->setElementFilters(array(
            'Alnum',
            'StringToLower'
        ));
        foreach ($this->form->getElements() as $element) {
            $filter = $element->getFilter('Alnum');
            $this->assertTrue($filter instanceof Zend_Filter_Alnum);
            $filter = $element->getFilter('StringToLower');
            $this->assertTrue($filter instanceof Zend_Filter_StringToLower);
        }
    }

    public function testCanSetGlobalElementPrefixPath()
    {
        $this->setupElements();
        $this->form->addElementPrefixPath('Zend_Foo', 'Zend/Foo/');
        $this->form->addElement('text', 'prefixTest');
        foreach ($this->form->getElements() as $element) {
            $loader = $element->getPluginLoader('validate');
            $paths  = $loader->getPaths('Zend_Foo_Validate');
            $this->assertFalse(empty($paths), $element->getName() . ':' . var_export($loader->getPaths(), 1));
            $this->assertContains('Foo', $paths[0]);
            $this->assertContains('Validate', $paths[0]);

            $paths = $element->getPluginLoader('filter')->getPaths('Zend_Foo_Filter');
            $this->assertFalse(empty($paths));
            $this->assertContains('Foo', $paths[0]);
            $this->assertContains('Filter', $paths[0]);

            $paths = $element->getPluginLoader('decorator')->getPaths('Zend_Foo_Decorator');
            $this->assertFalse(empty($paths));
            $this->assertContains('Foo', $paths[0]);
            $this->assertContains('Decorator', $paths[0]);
        }
    }

    public function testCustomGlobalElementPrefixPathUsedInNewlyCreatedElements()
    {
        $this->_checkZf2794();

        $this->form->addElementPrefixPath('My_Decorator', dirname(__FILE__) . '/_files/decorators', 'decorator');
        $this->form->addElement('text', 'prefixTest');
        $element = $this->form->prefixTest;
        $label   = $element->getDecorator('Label');
        $this->assertTrue($label instanceof My_Decorator_Label, get_class($label));
    }

    /**
     * @group ZF-3093
     */
    public function testSettingElementPrefixPathPropagatesToAttachedSubForms()
    {
        $subForm = new Zend_Form_SubForm();
        $subForm->addElement('text', 'foo');
        $this->form->addSubForm($subForm, 'subForm');
        $this->form->addElementPrefixPath('Zend_Foo', 'Zend/Foo/');
        $loader = $this->form->subForm->foo->getPluginLoader('decorator');
        $paths = $loader->getPaths('Zend_Foo_Decorator');
        $this->assertFalse(empty($paths));
        $this->assertContains('Foo', $paths[0]);
        $this->assertContains('Decorator', $paths[0]);
    }

    public function testCanSetElementValidatorPrefixPath()
    {
        $this->setupElements();
        $this->form->addElementPrefixPath('Zend_Foo', 'Zend/Foo/', 'validate');
        $this->form->addElement('text', 'prefixTest');
        foreach ($this->form->getElements() as $element) {
            $loader = $element->getPluginLoader('validate');
            $paths  = $loader->getPaths('Zend_Foo');
            $this->assertFalse(empty($paths));
            $this->assertContains('Foo', $paths[0]);
            $this->assertNotContains('Validate', $paths[0]);
        }
    }

    public function testCanSetElementFilterPrefixPath()
    {
        $this->setupElements();
        $this->form->addElementPrefixPath('Zend_Foo', 'Zend/Foo/', 'filter');
        $this->form->addElement('text', 'prefixTest');
        foreach ($this->form->getElements() as $element) {
            $loader = $element->getPluginLoader('filter');
            $paths  = $loader->getPaths('Zend_Foo');
            $this->assertFalse(empty($paths));
            $this->assertContains('Foo', $paths[0]);
            $this->assertNotContains('Filter', $paths[0]);
        }
    }

    public function testCanSetElementDecoratorPrefixPath()
    {
        $this->setupElements();
        $this->form->addElementPrefixPath('Zend_Foo', 'Zend/Foo/', 'decorator');
        $this->form->addElement('text', 'prefixTest');
        foreach ($this->form->getElements() as $element) {
            $loader = $element->getPluginLoader('decorator');
            $paths  = $loader->getPaths('Zend_Foo');
            $this->assertFalse(empty($paths));
            $this->assertContains('Foo', $paths[0]);
            $this->assertNotContains('Decorator', $paths[0]);
        }
    }

    // Display Group decorators and plugin paths

    public function setupDisplayGroups()
    {
        $this->testCanAddAndRetrieveMultipleElements();
        $this->form->addElements(array(
            'test1' => 'text',
            'test2' => 'text',
            'test3' => 'text',
            'test4' => 'text'
        ));
        $this->form->addDisplayGroup(array('bar', 'bat'), 'barbat');
        $this->form->addDisplayGroup(array('foo', 'baz'), 'foobaz');
    }

    public function testCanSetAllDisplayGroupDecoratorsAtOnce()
    {
        $this->_checkZf2794();

        $this->setupDisplayGroups();
        $this->form->setDisplayGroupDecorators(array(
            array('Callback', array('callback' => 'strip_tags')),
        ));
        foreach ($this->form->getDisplayGroups() as $element) {
            $this->assertFalse($element->getDecorator('FormElements'));
            $this->assertFalse($element->getDecorator('HtmlTag'));
            $this->assertFalse($element->getDecorator('Fieldset'));
            $this->assertFalse($element->getDecorator('DtDdWrapper'));

            $decorator = $element->getDecorator('Callback');
            $this->assertTrue($decorator instanceof Zend_Form_Decorator_Callback);
        }
    }

    public function testCanSetDisplayGroupPrefixPath()
    {
        $this->setupDisplayGroups();
        $this->form->addDisplayGroupPrefixPath('Zend_Foo', 'Zend/Foo/');
        $this->form->addDisplayGroup(array('test1', 'test2'), 'testgroup');
        foreach ($this->form->getDisplayGroups() as $group) {
            $loader = $group->getPluginLoader();
            $paths  = $loader->getPaths('Zend_Foo');
            $this->assertFalse(empty($paths));
            $this->assertContains('Foo', $paths[0]);
        }
    }

    /**
     * @group ZF-3213
     */
    public function testShouldAllowSettingDisplayGroupPrefixPathViaConfigOptions()
    {
        require_once 'Zend/Config/Ini.php';
        $config = new Zend_Config_Ini(dirname(__FILE__) . '/_files/config/zf3213.ini', 'form');
        $form   = new Zend_Form($config);
        $dg     = $form->foofoo;
        $paths  = $dg->getPluginLoader()->getPaths('My_Decorator');
        $this->assertTrue($paths !== false);
    }

    // Subform decorators

    public function testCanSetAllSubFormDecoratorsAtOnce()
    {
        $this->_checkZf2794();

        $this->setupSubForm();
        $this->form->setSubFormDecorators(array(
            array('Callback', array('callback' => 'strip_tags')),
        ));
        foreach ($this->form->getSubForms() as $subForm) {
            $this->assertFalse($subForm->getDecorator('FormElements'));
            $this->assertFalse($subForm->getDecorator('HtmlTag'));
            $this->assertFalse($subForm->getDecorator('Fieldset'));
            $this->assertFalse($subForm->getDecorator('DtDdWrapper'));

            $decorator = $subForm->getDecorator('Callback');
            $this->assertTrue($decorator instanceof Zend_Form_Decorator_Callback);
        }
    }

    // Extension

    public function testInitCalledPriorToLoadingDefaultDecorators()
    {
        $form = new Zend_Form_FormTest_FormExtension();
        $decorators = $form->getDecorators();
        $this->assertTrue(empty($decorators));
    }

    // Clone

    /**
     * @group ZF-3819
     */
    public function testCloningShouldCloneAllChildren()
    {
        $form = new Zend_Form();
        $foo = new Zend_Form_SubForm(array(
            'name' => 'foo',
            'elements' => array(
                'one' => 'text',
                'two' => 'text',
            ),
        ));
        $form->addElement('text', 'bar')
             ->addElement('text', 'baz')
             ->addElement('text', 'bat')
             ->addDisplayGroup(array('bar', 'bat'), 'barbat')
             ->addSubForm($foo, 'foo');
        $bar = $form->bar;
        $baz = $form->baz;
        $bat = $form->bat;
        $barbat = $form->barbat;

        $cloned = clone $form;
        $this->assertNotSame($foo, $cloned->foo);
        $this->assertNotSame($bar, $cloned->bar);
        $this->assertNotSame($baz, $cloned->baz);
        $this->assertNotSame($bat, $cloned->bat);
        $this->assertNotSame($barbat, $cloned->getDisplayGroup('barbat'));
        $this->assertNotSame($foo->one, $cloned->foo->one);
        $this->assertNotSame($foo->two, $cloned->foo->two);
    }

    // Reset

    /**
     * @group ZF-3227
     */
    public function testFormsShouldAllowResetting()
    {
        $form = new Zend_Form();
        $foo = new Zend_Form_SubForm(array(
            'name' => 'foo',
            'elements' => array(
                'one' => 'text',
                'two' => 'text',
            ),
        ));
        $form->addElement('text', 'bar')
             ->addElement('text', 'baz')
             ->addElement('text', 'bat')
             ->addDisplayGroup(array('bar', 'bat'), 'barbat')
             ->addSubForm($foo, 'foo');
        $values = array(
            'bar' => 'Bar Value',
            'baz' => 'Baz Value',
            'bat' => 'Bat Value',
            'foo' => array(
                'one' => 'One Value',
                'two' => 'Two Value',
            ),
        );
        $form->populate($values);
        $test = $form->getValues();
        $this->assertEquals($values, $test);
        $form->reset();
        $test = $form->getValues();
        $this->assertNotEquals($values, $test);
        $this->assertEquals(0, array_sum($test));
    }

    /**
     * @group ZF-3217
     */
    public function testFormShouldOverloadToRenderDecorators()
    {
        $this->setupElements();
        $this->form->setView($this->getView());
        $html = $this->form->renderFormElements();
        foreach ($this->form->getElements() as $element) {
            $this->assertContains('id="' . $element->getFullyQualifiedName() . '"', $html, 'Received: ' . $html);
        }
        $this->assertNotContains('<dl', $html);
        $this->assertNotContains('<form', $html);

        $html = $this->form->renderForm('this is the content');
        $this->assertContains('<form', $html);
        $this->assertContains('</form>', $html);
        $this->assertContains('this is the content', $html);
    }

    /**
     * @group ZF-3217
     * @expectedException Zend_Form_Exception
     */
    public function testOverloadingToInvalidMethodsShouldThrowAnException()
    {
        $html = $this->form->bogusMethodCall();
    }

    /**
     * @group ZF-2950
     */
    public function testDtDdElementsWithLabelGetUniqueId()
    {
        $form = new Zend_Form();
        $form->setView($this->getView());

        $fooElement = new Zend_Form_Element_Text('foo');
        $fooElement->setLabel('Foo');

        $form->addElement($fooElement);

        $html = $form->render();

        $this->assertContains('<dt id="foo-label">', $html);
        $this->assertContains('<dd id="foo-element">', $html);
    }

    /**
     * @group ZF-2950
     */
    public function testDtDdElementsWithoutLabelGetUniqueId()
    {
        $form = new Zend_Form();
        $form->setView($this->getView())
             ->addElement(new Zend_Form_Element_Text('foo'));

        $html = $form->render();

        $this->assertContains('<dt id="foo-label">&nbsp;</dt>', $html);
        $this->assertContains('<dd id="foo-element">', $html);
    }

    /**
     * @group ZF-2950
     */
    public function testSubFormGetsUniqueIdWithName()
    {
        $form = new Zend_Form();
        $form->setView($this->getView())
             ->setName('testform')
             ->addSubForm(new Zend_Form_SubForm(), 'testform');

        $html = $form->render();

        $this->assertContains('<dt id="testform-label">&nbsp;</dt>', $html);
        $this->assertContains('<dd id="testform-element">', $html);
    }

    /**
     * @group ZF-5370
     */
    public function testEnctypeDefaultsToMultipartWhenFileElementIsAttachedToForm()
    {
        $file = new Zend_Form_Element_File('txt');
        $this->form->addElement($file);

        $html = $this->form->render($this->getView());
        $this->assertFalse(empty($html));
        $this->assertRegexp('#<form[^>]+enctype="multipart/form-data"#', $html);
    }

    /**
     * @group ZF-5370
     */
    public function testEnctypeDefaultsToMultipartWhenFileElementIsAttachedToSubForm()
    {
        $subForm = new Zend_Form_SubForm();
        $subForm->addElement('file', 'txt');
        $this->form->addSubForm($subForm, 'page1')
                   ->setView(new Zend_View);
        $html = $this->form->render();

        $this->assertContains('id="txt"', $html);
        $this->assertContains('name="txt"', $html);
        $this->assertRegexp('#<form[^>]+enctype="multipart/form-data"#', $html, $html);
    }

    /**
     * @group ZF-5370
     */
    public function testEnctypeDefaultsToMultipartWhenFileElementIsAttachedToDisplayGroup()
    {
        $this->form->addElement('file', 'txt')
                   ->addDisplayGroup(array('txt'), 'txtdisplay')
                   ->setView(new Zend_View);
        $html = $this->form->render();

        $this->assertContains('id="txt"', $html);
        $this->assertContains('name="txt"', $html);
        $this->assertRegexp('#<form[^>]+enctype="multipart/form-data"#', $html, $html);
    }

    /**
     * @group ZF-6070
     */
    public function testIndividualElementDecoratorsShouldOverrideGlobalElementDecorators()
    {
        $this->form->setOptions(array(
            'elementDecorators' => array(
                'ViewHelper',
                'Label',
            ),
            'elements' => array(
                'foo' => array(
                    'type' => 'text',
                    'options' => array(
                        'decorators' => array(
                            'Errors',
                            'ViewHelper',
                        ),
                    ),
                ),
            ),
        ));
        $element    = $this->form->getElement('foo');
        $expected   = array('Zend_Form_Decorator_Errors', 'Zend_Form_Decorator_ViewHelper');
        $actual     = array();
        foreach ($element->getDecorators() as $decorator) {
            $actual[] = get_class($decorator);
        }
        $this->assertSame($expected, $actual);
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

class Zend_Form_FormTest_DisplayGroup extends Zend_Form_DisplayGroup
{
}

class Zend_Form_FormTest_FormExtension extends Zend_Form
{
    public function init()
    {
        $this->setDisableLoadDefaultDecorators(true);
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Form_FormTest::main') {
    Zend_Form_FormTest::main();
}
