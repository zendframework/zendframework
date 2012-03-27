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

use Zend\Form\Element\File as FileElement,
    Zend\Form\Element\Xhtml as XhtmlElement,
    Zend\Form\Element,
    Zend\Form\Form,
    Zend\Form\Decorator,
    Zend\Form\SubForm,
    Zend\Loader\PrefixPathLoader,
    Zend\Loader\PrefixPathMapper,
    Zend\Registry,
    Zend\Translator\Translator,
    Zend\View\Renderer\PhpRenderer as View;

/**
 * Test class for Zend_Form_Element_File
 *
 * @category   Zend
 * @package    Zend_Form
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Form
 */
class FileTest extends \PHPUnit_Framework_TestCase
{
    protected $_errorOccurred = false;

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        Registry::_unsetInstance();
        Form::setDefaultTranslator(null);
        $this->element = new FileElement('foo');
    }

    public function testElementShouldProxyToParentForDecoratorPluginLoader()
    {
        $loader = $this->element->getPluginLoader('decorator');
        $paths = $loader->getPaths('Zend\Form\Decorator');
        $this->assertInstanceOf('SplStack', $paths);

        $loader = new PrefixPathLoader;
        $this->element->setPluginLoader($loader, 'decorator');
        $test = $this->element->getPluginLoader('decorator');
        $this->assertSame($loader, $test);
    }

    public function testElementShouldProxyToParentWhenSettingDecoratorPrefixPaths()
    {
        $this->element->addPrefixPath('Foo\Decorator', 'Foo/Decorator/', 'decorator');
        $loader = $this->element->getPluginLoader('decorator');
        $paths = $loader->getPaths('Foo\Decorator');
        $this->assertInstanceOf('SplStack', $paths);
    }

    public function testElementShouldAddToAllPluginLoadersWhenAddingNullPrefixPath()
    {
        $this->element->addPrefixPath('Foo', 'Foo');
        foreach (array('validator', 'filter', 'decorator', 'transfer\\adapter') as $type) {
            $loader = $this->element->getPluginLoader($type);
            $string = str_replace('\\', ' ', $type);
            $string = ucwords($string);
            $string = str_replace(' ', '\\', $string);
            $prefix = 'Foo\\' . $string;
            $paths  = $loader->getPaths($prefix);
            $this->assertInstanceOf('SplStack', $paths);
            $this->assertNotEquals(0, count($paths));
        }
    }

    public function testElementShouldUseHttpTransferAdapterByDefault()
    {
        $adapter = $this->element->getTransferAdapter();
        $this->assertTrue($adapter instanceof \Zend\File\Transfer\Adapter\Http);
    }

    public function testElementShouldAllowSpecifyingAdapterUsingConcreteInstance()
    {
        $adapter = new TestAsset\MockFileAdapter();
        $this->element->setTransferAdapter($adapter);
        $test = $this->element->getTransferAdapter();
        $this->assertSame($adapter, $test);
    }

    public function testElementShouldThrowExceptionWhenAddingAdapterOfInvalidType()
    {
        $this->setExpectedException('Zend\Form\Element\Exception\InvalidArgumentException');
        $this->element->setTransferAdapter(new \stdClass);
    }

    public function testShouldRegisterPluginLoaderWithFileTransferAdapterPathByDefault()
    {
        $loader = $this->element->getPluginLoader('transfer\\adapter');
        $this->assertTrue($loader instanceof PrefixPathMapper);
        $paths = $loader->getPaths('Zend\File\Transfer\Adapter');
        $this->assertInstanceOf('SplStack', $paths);
    }

    public function testElementShouldAllowSpecifyingAdapterUsingPluginLoader()
    {
        $this->element->addPrefixPath('ZendTest\Form\Element\TestAsset\TransferAdapter', __DIR__ . '/TestAsset/TransferAdapter', 'transfer\adapter');
        $this->element->setTransferAdapter('Foo');
        $test = $this->element->getTransferAdapter();
        $this->assertTrue($test instanceof TestAsset\TransferAdapter\Foo);
    }

    public function testValidatorAccessAndMutationShouldProxyToAdapter()
    {
        $this->testElementShouldAllowSpecifyingAdapterUsingConcreteInstance();
        $this->element->addValidator('Count', false, 1)
                      ->addValidators(array(
                          'Extension' => 'jpg',
                          new \Zend\Validator\File\Upload(),
                      ));
        $validators = $this->element->getValidators();
        $test       = $this->element->getTransferAdapter()->getValidators();
        $this->assertEquals($validators, $test);
        $this->assertTrue(is_array($test));
        $this->assertEquals(3, count($test));

        $validator = $this->element->getValidator('count');
        $test      = $this->element->getTransferAdapter()->getValidator('count');
        $this->assertNotNull($validator);
        $this->assertSame($validator, $test);

        $this->element->removeValidator('Extension');
        $this->assertFalse($this->element->getTransferAdapter()->hasValidator('Extension'));

        $this->element->setValidators(array(
            'Upload',
            array('validator' => 'Extension', 'options' => 'jpg'),
            array('validator' => 'Count', 'options' => 1),
        ));
        $validators = $this->element->getValidators();
        $test       = $this->element->getTransferAdapter()->getValidators();
        $this->assertSame($validators, $test);
        $this->assertTrue(is_array($test));
        $this->assertEquals(3, count($test), var_export($test, 1));

        $this->element->clearValidators();
        $validators = $this->element->getValidators();
        $this->assertTrue(is_array($validators));
        $this->assertEquals(0, count($validators));
        $test = $this->element->getTransferAdapter()->getValidators();
        $this->assertSame($validators, $test);
    }

    public function testValidationShouldProxyToAdapter()
    {
        $this->markTestIncomplete('Unsure how to accurately test');

        $this->element->setTransferAdapter(new TestAsset\MockFileAdapter);
        $this->element->addValidator('Regex', '/([a-z0-9]{13})$/i');
        $this->assertTrue($this->element->isValid('foo.jpg'));
    }

    public function testDestinationMutatorsShouldProxyToTransferAdapter()
    {
        $adapter = new TestAsset\MockFileAdapter();
        $this->element->setTransferAdapter($adapter);

        $this->element->setDestination(__DIR__);
        $this->assertEquals(__DIR__, $this->element->getDestination());
        $this->assertEquals(__DIR__, $this->element->getTransferAdapter()->getDestination('foo'));
    }

    public function testSettingMultipleFiles()
    {
        $this->element->setMultiFile(3);
        $this->assertEquals(3, $this->element->getMultiFile());
    }

    public function testFileInSubSubSubform()
    {
        $form = new Form();
        $element  = new FileElement('file1');
        $element2 = new FileElement('file2');

        $subform0 = new SubForm();
        $subform0->addElement($element);
        $subform0->addElement($element2);
        $subform1 = new SubForm();
        $subform1->addSubform($subform0, 'subform0');
        $subform2 = new SubForm();
        $subform2->addSubform($subform1, 'subform1');
        $subform3 = new SubForm();
        $subform3->addSubform($subform2, 'subform2');
        $form->addSubform($subform3, 'subform3');

        $form->setView(new View());
        $output = (string) $form;
        $this->assertContains('name="file1"', $output, $output);
        $this->assertContains('name="file2"', $output, $output);
    }

    public function testMultiFileInSubSubSubform()
    {
        $form    = new Form();
        $element = new FileElement('file');
        $element->setMultiFile(2);

        $subform0 = new SubForm();
        $subform0->addElement($element);
        $subform1 = new SubForm();
        $subform1->addSubform($subform0, 'subform0');
        $subform2 = new SubForm();
        $subform2->addSubform($subform1, 'subform1');
        $subform3 = new SubForm();
        $subform3->addSubform($subform2, 'subform2');
        $form->addSubform($subform3, 'subform3');

        $form->setView(new View());
        $output = (string) $form;
        $this->assertContains('name="file[]"', $output, $output);
        $this->assertEquals(2, substr_count($output, 'file[]'));
    }

    public function testMultiFileWithOneFile()
    {
        $form    = new Form();
        $element = new FileElement('file');
        $element->setMultiFile(1);

        $subform0 = new SubForm();
        $subform0->addElement($element);
        $subform1 = new SubForm();
        $subform1->addSubform($subform0, 'subform0');
        $subform2 = new SubForm();
        $subform2->addSubform($subform1, 'subform1');
        $subform3 = new SubForm();
        $subform3->addSubform($subform2, 'subform2');
        $form->addSubform($subform3, 'subform3');

        $form->setView(new View());
        $output = (string) $form;
        $this->assertNotContains('name="file[]"', $output);
    }

    public function testSettingMaxFileSize()
    {
        $max = $this->_convertIniToInteger(trim(ini_get('upload_max_filesize')));

        $this->assertEquals(0, $this->element->getMaxFileSize());
        $this->element->setMaxFileSize($max);
        $this->assertEquals($max, $this->element->getMaxFileSize());

        $this->_errorOccurred = false;
        set_error_handler(array($this, 'errorHandlerIgnore'));
        $this->element->setMaxFileSize(999999999999);
        if (!$this->_errorOccurred) {
            $this->fail('INI exception expected');
        }
        restore_error_handler();
    }

    public function testAutoGetPostMaxSize()
    {
        $this->element->setMaxFileSize(-1);
        $this->assertNotEquals(-1, $this->element->getMaxFileSize());
    }

    public function testTranslatingValidatorErrors()
    {
        $translate = new Translator('ArrayAdapter', array('unused', 'foo' => 'bar'), 'en');
        $this->element->setTranslator($translate);

        $adapter = $this->element->getTranslator();
        $this->assertTrue($adapter instanceof \Zend\Translator\Adapter\ArrayAdapter);

        $adapter = $this->element->getTransferAdapter();
        $adapter = $adapter->getTranslator();
        $this->assertTrue($adapter instanceof \Zend\Translator\Adapter\ArrayAdapter);

        $this->assertFalse($this->element->translatorIsDisabled());
        $this->element->setDisableTranslator($translate);
        $this->assertTrue($this->element->translatorIsDisabled());
    }

    public function testFileNameWithoutPath()
    {
        $this->element->setTransferAdapter(new TestAsset\MockFileAdapter());
        $this->element->setDestination(__DIR__);
        $this->assertEquals(__DIR__ . DIRECTORY_SEPARATOR . 'foo.jpg', $this->element->getFileName('foo', true));
        $this->assertEquals('foo.jpg', $this->element->getFileName('foo', false));
    }

    public function testEmptyFileName()
    {
        $this->element->setTransferAdapter(new TestAsset\MockFileAdapter());
        $this->element->setDestination(__DIR__);
        $this->assertEquals(__DIR__ . DIRECTORY_SEPARATOR . 'foo.jpg', $this->element->getFileName());
    }

    public function testIsReceived()
    {
        $this->element->setTransferAdapter(new TestAsset\MockFileAdapter());
        $this->assertEquals(false, $this->element->isReceived());
    }

    public function testIsUploaded()
    {
        $this->element->setTransferAdapter(new TestAsset\MockFileAdapter());
        $this->assertEquals(true, $this->element->isUploaded());
    }

    public function testIsFiltered()
    {
        $this->element->setTransferAdapter(new TestAsset\MockFileAdapter());
        $this->assertEquals(true, $this->element->isFiltered());
    }

    public function testDefaultDecorators()
    {
        $this->element->clearDecorators();
        $this->assertEquals(array(), $this->element->getDecorators());
        $this->element->setDisableLoadDefaultDecorators(true);
        $this->element->loadDefaultDecorators();
        $this->assertEquals(array(), $this->element->getDecorators());
        $this->element->setDisableLoadDefaultDecorators(false);
        $this->element->loadDefaultDecorators();
        $this->assertNotEquals(array(), $this->element->getDecorators());
    }

    public function testValueGetAndSet()
    {
        $this->element->setTransferAdapter(new TestAsset\MockFileAdapter());
        $this->assertEquals(null, $this->element->getValue());
        $this->element->setValue('something');
        $this->assertEquals(null, $this->element->getValue());
    }

    public function testMarkerInterfaceForFileElement()
    {
        $this->element->setDecorators(array('ViewHelper'));
        $this->assertEquals(1, count($this->element->getDecorators()));

        $this->setExpectedException('Zend\Form\Element\Exception\RunTimeException', 'No file decorator found');
        $content = $this->element->render(new View());
    }

    public function testFileSize()
    {
        $element = new FileElement('baz');
        $adapter = new TestAsset\MockFileAdapter();
        $element->setTransferAdapter($adapter);

        $this->assertEquals('1.14kB', $element->getFileSize('baz.text'));
        $adapter->setOptions(array('useByteString' => false));
        $this->assertEquals(1172, $element->getFileSize('baz.text'));
    }

    public function testMimeType()
    {
        $element = new FileElement('baz');
        $adapter = new TestAsset\MockFileAdapter();
        $element->setTransferAdapter($adapter);

        $this->assertEquals('text/plain', $element->getMimeType('baz.text'));
    }

    public function testAddedErrorsAreDisplayed()
    {
        Form::setDefaultTranslator(null);
        $element = new FileElement('baz');
        $element->addError('TestError3');
        $adapter = new TestAsset\MockFileAdapter();
        $element->setTransferAdapter($adapter);

        $this->assertTrue($element->hasErrors());
        $messages = $element->getMessages();
        $this->assertContains('TestError3', $messages);
    }

    public function testGetTranslatorRetrievesGlobalDefaultWhenAvailable()
    {
        $this->assertNull($this->element->getTranslator());
        $translator = new Translator('ArrayAdapter', array('foo' => 'bar'));
        Form::setDefaultTranslator($translator);
        $received = $this->element->getTranslator();
        $this->assertSame($translator->getAdapter(), $received);
    }

    public function testDefaultDecoratorsContainDescription()
    {
        $element    = new FileElement('baz');
        $decorators = $element->getDecorator('Description');
        $this->assertTrue($decorators instanceof Decorator\Description);
    }

    private function _convertIniToInteger($setting)
    {
        if (!is_numeric($setting)) {
            $type = strtoupper(substr($setting, -1));
            $setting = (integer) substr($setting, 0, -1);

            switch ($type) {
                case 'M' :
                    $setting *= 1024;
                    break;

                case 'G' :
                    $setting *= 1024 * 1024;
                    break;

                default :
                    break;
            }
        }

        return (integer) $setting;
    }

    /**
     * Ignores a raised PHP error when in effect, but throws a flag to indicate an error occurred
     *
     * @param  integer $errno
     * @param  string  $errstr
     * @param  string  $errfile
     * @param  integer $errline
     * @param  array   $errcontext
     * @return void
     */
    public function errorHandlerIgnore($errno, $errstr, $errfile, $errline, array $errcontext)
    {
        $this->_errorOccurred = true;
    }


    /**
     * Prove the fluent interface on Zend_Form_Element_File::loadDefaultDecorators
     *
     * @link http://framework.zend.com/issues/browse/ZF-9913
     * @return void
     */
    public function testFluentInterfaceOnLoadDefaultDecorators()
    {
        $this->assertSame($this->element, $this->element->loadDefaultDecorators());
    }
}
