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
 * @package    Zend_File
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

// Call Zend_File_Transfer_Adapter_AbstractTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_File_Transfer_Adapter_AbstractTest::main");
}

require_once dirname(__FILE__) . '/../../../../TestHelper.php';

require_once 'Zend/File/Transfer/Adapter/Abstract.php';
require_once 'Zend/Filter/BaseName.php';
require_once 'Zend/Filter/StringToLower.php';
require_once 'Zend/Filter/StringToUpper.php';
require_once 'Zend/Loader/PluginLoader.php';
require_once 'Zend/Validate/File/Count.php';
require_once 'Zend/Validate/File/Extension.php';

/**
 * Test class for Zend_File_Transfer_Adapter_Abstract
 *
 * @category   Zend
 * @package    Zend_File
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_File
 */
class Zend_File_Transfer_Adapter_AbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_File_Transfer_Adapter_AbstractTest");
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
        $this->adapter = new Zend_File_Transfer_Adapter_AbstractTest_MockAdapter();
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

    /**
     * @expectedException Zend_File_Transfer_Exception
     */
    public function testAdapterShouldThrowExceptionWhenRetrievingPluginLoaderOfInvalidType()
    {
        $this->adapter->getPluginLoader('bogus');
    }

    public function testAdapterShouldHavePluginLoaderForValidators()
    {
        $loader = $this->adapter->getPluginLoader('validate');
        $this->assertTrue($loader instanceof Zend_Loader_PluginLoader);
    }

    public function testAdapterShouldAllowAddingCustomPluginLoader()
    {
        $loader = new Zend_Loader_PluginLoader();
        $this->adapter->setPluginLoader($loader, 'filter');
        $this->assertSame($loader, $this->adapter->getPluginLoader('filter'));
    }

    /**
     * @expectedException Zend_File_Transfer_Exception
     */
    public function testAddingInvalidPluginLoaderTypeToAdapterShouldRaiseException()
    {
        $loader = new Zend_Loader_PluginLoader();
        $this->adapter->setPluginLoader($loader, 'bogus');
    }

    public function testAdapterShouldProxyAddingPluginLoaderPrefixPath()
    {
        $loader = $this->adapter->getPluginLoader('validate');
        $this->adapter->addPrefixPath('Foo_Valid', 'Foo/Valid/', 'validate');
        $paths = $loader->getPaths('Foo_Valid');
        $this->assertTrue(is_array($paths));
    }

    public function testPassingNoTypeWhenAddingPrefixPathToAdapterShouldGeneratePathsForAllTypes()
    {
        $this->adapter->addPrefixPath('Foo', 'Foo');
        $validateLoader = $this->adapter->getPluginLoader('validate');
        $filterLoader   = $this->adapter->getPluginLoader('filter');
        $paths = $validateLoader->getPaths('Foo_Validate');
        $this->assertTrue(is_array($paths));
        $paths = $filterLoader->getPaths('Foo_Filter');
        $this->assertTrue(is_array($paths));
    }

    /**
     * @expectedException Zend_File_Transfer_Exception
     */
    public function testPassingInvalidTypeWhenAddingPrefixPathToAdapterShouldThrowException()
    {
        $this->adapter->addPrefixPath('Foo', 'Foo', 'bogus');
    }

    public function testAdapterShouldProxyAddingMultiplePluginLoaderPrefixPaths()
    {
        $validatorLoader = $this->adapter->getPluginLoader('validate');
        $filterLoader    = $this->adapter->getPluginLoader('filter');
        $this->adapter->addPrefixPaths(array(
            'validate' => array('prefix' => 'Foo_Valid', 'path' => 'Foo/Valid/'),
            'filter'   => array(
                'Foo_Filter' => 'Foo/Filter/',
                'Baz_Filter' => array(
                    'Baz/Filter/',
                    'My/Baz/Filter/',
                ),
            ),
            array('type' => 'filter', 'prefix' => 'Bar_Filter', 'path' => 'Bar/Filter/'),
        ));
        $paths = $validatorLoader->getPaths('Foo_Valid');
        $this->assertTrue(is_array($paths));
        $paths = $filterLoader->getPaths('Foo_Filter');
        $this->assertTrue(is_array($paths));
        $paths = $filterLoader->getPaths('Bar_Filter');
        $this->assertTrue(is_array($paths));
        $paths = $filterLoader->getPaths('Baz_Filter');
        $this->assertTrue(is_array($paths));
        $this->assertEquals(2, count($paths));
    }

    public function testValidatorPluginLoaderShouldRegisterPathsForBaseAndFileValidatorsByDefault()
    {
        $loader = $this->adapter->getPluginLoader('validate');
        $paths  = $loader->getPaths('Zend_Validate');
        $this->assertTrue(is_array($paths));
        $paths  = $loader->getPaths('Zend_Validate_File');
        $this->assertTrue(is_array($paths));
    }

    public function testAdapterShouldAllowAddingValidatorInstance()
    {
        $validator = new Zend_Validate_File_Count(array('min' => 1, 'max' => 1));
        $this->adapter->addValidator($validator);
        $test = $this->adapter->getValidator('Zend_Validate_File_Count');
        $this->assertSame($validator, $test);
    }

    public function testAdapterShouldAllowAddingValidatorViaPluginLoader()
    {
        $this->adapter->addValidator('Count', false, array('min' => 1, 'max' => 1));
        $test = $this->adapter->getValidator('Count');
        $this->assertTrue($test instanceof Zend_Validate_File_Count);
    }

    /**
     * @expectedException Zend_File_Transfer_Exception
     */
    public function testAdapterhShouldRaiseExceptionWhenAddingInvalidValidatorType()
    {
        $this->adapter->addValidator(new Zend_Filter_BaseName);
    }

    public function testAdapterShouldAllowAddingMultipleValidatorsAtOnceUsingBothInstancesAndPluginLoader()
    {
        $validators = array(
            'count' => array('min' => 1, 'max' => 1),
            'Exists' => 'C:\temp',
            array('validator' => 'Upload', 'options' => array(realpath(__FILE__))),
            new Zend_Validate_File_Extension('jpg'),
        );
        $this->adapter->addValidators($validators);
        $test = $this->adapter->getValidators();
        $this->assertTrue(is_array($test));
        $this->assertEquals(4, count($test), var_export($test, 1));
        $count = array_shift($test);
        $this->assertTrue($count instanceof Zend_Validate_File_Count);
        $exists = array_shift($test);
        $this->assertTrue($exists instanceof Zend_Validate_File_Exists);
        $size = array_shift($test);
        $this->assertTrue($size instanceof Zend_Validate_File_Upload);
        $ext = array_shift($test);
        $this->assertTrue($ext instanceof Zend_Validate_File_Extension);
        $orig = array_pop($validators);
        $this->assertSame($orig, $ext);
    }

    public function testGetValidatorShouldReturnNullWhenNoMatchingIdentifierExists()
    {
        $this->assertNull($this->adapter->getValidator('Alpha'));
    }

    public function testAdapterShouldAllowPullingValidatorsByFile()
    {
        $this->adapter->addValidator('Alpha', false, false, 'foo');
        $validators = $this->adapter->getValidators('foo');
        $this->assertEquals(1, count($validators));
        $validator = array_shift($validators);
        $this->assertTrue($validator instanceof Zend_Validate_Alpha);
    }

    public function testCallingSetValidatorsOnAdapterShouldOverwriteExistingValidators()
    {
        $this->testAdapterShouldAllowAddingMultipleValidatorsAtOnceUsingBothInstancesAndPluginLoader();
        $validators = array(
            new Zend_Validate_File_Count(1),
            new Zend_Validate_File_Extension('jpg'),
        );
        $this->adapter->setValidators($validators);
        $test = $this->adapter->getValidators();
        $this->assertSame($validators, array_values($test));
    }

    public function testAdapterShouldAllowRetrievingValidatorInstancesByClassName()
    {
        $this->testAdapterShouldAllowAddingMultipleValidatorsAtOnceUsingBothInstancesAndPluginLoader();
        $ext = $this->adapter->getValidator('Zend_Validate_File_Extension');
        $this->assertTrue($ext instanceof Zend_Validate_File_Extension);
    }

    public function testAdapterShouldAllowRetrievingValidatorInstancesByPluginName()
    {
        $this->testAdapterShouldAllowAddingMultipleValidatorsAtOnceUsingBothInstancesAndPluginLoader();
        $count = $this->adapter->getValidator('Count');
        $this->assertTrue($count instanceof Zend_Validate_File_Count);
    }

    public function testAdapterShouldAllowRetrievingAllValidatorsAtOnce()
    {
        $this->testAdapterShouldAllowAddingMultipleValidatorsAtOnceUsingBothInstancesAndPluginLoader();
        $validators = $this->adapter->getValidators();
        $this->assertTrue(is_array($validators));
        $this->assertEquals(4, count($validators));
        foreach ($validators as $validator) {
            $this->assertTrue($validator instanceof Zend_Validate_Interface);
        }
    }

    public function testAdapterShouldAllowRemovingValidatorInstancesByClassName()
    {
        $this->testAdapterShouldAllowAddingMultipleValidatorsAtOnceUsingBothInstancesAndPluginLoader();
        $this->assertTrue($this->adapter->hasValidator('Zend_Validate_File_Extension'));
        $this->adapter->removeValidator('Zend_Validate_File_Extension');
        $this->assertFalse($this->adapter->hasValidator('Zend_Validate_File_Extension'));
    }

    public function testAdapterShouldAllowRemovingValidatorInstancesByPluginName()
    {
        $this->testAdapterShouldAllowAddingMultipleValidatorsAtOnceUsingBothInstancesAndPluginLoader();
        $this->assertTrue($this->adapter->hasValidator('Count'));
        $this->adapter->removeValidator('Count');
        $this->assertFalse($this->adapter->hasValidator('Count'));
    }

    public function testRemovingNonexistentValidatorShouldDoNothing()
    {
        $this->testAdapterShouldAllowAddingMultipleValidatorsAtOnceUsingBothInstancesAndPluginLoader();
        $validators = $this->adapter->getValidators();
        $this->assertFalse($this->adapter->hasValidator('Alpha'));
        $this->adapter->removeValidator('Alpha');
        $this->assertFalse($this->adapter->hasValidator('Alpha'));
        $test = $this->adapter->getValidators();
        $this->assertSame($validators, $test);
    }

    public function testAdapterShouldAllowRemovingAllValidatorsAtOnce()
    {
        $this->testAdapterShouldAllowAddingMultipleValidatorsAtOnceUsingBothInstancesAndPluginLoader();
        $this->adapter->clearValidators();
        $validators = $this->adapter->getValidators();
        $this->assertTrue(is_array($validators));
        $this->assertEquals(0, count($validators));
    }

    public function testValidationShouldReturnTrueForValidTransfer()
    {
        $this->adapter->addValidator('Count', false, array(1, 3), 'foo');
        $this->assertTrue($this->adapter->isValid('foo'));
    }

    public function testValidationShouldReturnTrueForValidTransferOfMultipleFiles()
    {
        $this->assertTrue($this->adapter->isValid(null));
    }

    public function testValidationShouldReturnFalseForInvalidTransfer()
    {
        $this->adapter->addValidator('Extension', false, 'png', 'foo');
        $this->assertFalse($this->adapter->isValid('foo'));
    }

    public function testValidationShouldThrowExceptionForNonexistentFile()
    {
        $this->assertFalse($this->adapter->isValid('bogus'));
    }

    public function testErrorMessagesShouldBeEmptyByDefault()
    {
        $messages = $this->adapter->getMessages();
        $this->assertTrue(is_array($messages));
        $this->assertEquals(0, count($messages));
    }

    public function testErrorMessagesShouldBePopulatedAfterInvalidTransfer()
    {
        $this->testValidationShouldReturnFalseForInvalidTransfer();
        $messages = $this->adapter->getMessages();
        $this->assertTrue(is_array($messages));
        $this->assertFalse(empty($messages));
    }

    public function testErrorCodesShouldBeNullByDefault()
    {
        $errors = $this->adapter->getErrors();
        $this->assertTrue(is_array($errors));
        $this->assertEquals(0, count($errors));
    }

    public function testErrorCodesShouldBePopulatedAfterInvalidTransfer()
    {
        $this->testValidationShouldReturnFalseForInvalidTransfer();
        $errors = $this->adapter->getErrors();
        $this->assertTrue(is_array($errors));
        $this->assertFalse(empty($errors));
    }

    public function testAdapterShouldHavePluginLoaderForFilters()
    {
        $loader = $this->adapter->getPluginLoader('filter');
        $this->assertTrue($loader instanceof Zend_Loader_PluginLoader);
    }

    public function testFilterPluginLoaderShouldRegisterPathsForBaseAndFileFiltersByDefault()
    {
        $loader = $this->adapter->getPluginLoader('filter');
        $paths  = $loader->getPaths('Zend_Filter');
        $this->assertTrue(is_array($paths));
        $paths  = $loader->getPaths('Zend_Filter_File');
        $this->assertTrue(is_array($paths));
    }

    public function testAdapterShouldAllowAddingFilterInstance()
    {
        $filter = new Zend_Filter_StringToLower();
        $this->adapter->addFilter($filter);
        $test = $this->adapter->getFilter('Zend_Filter_StringToLower');
        $this->assertSame($filter, $test);
    }

    public function testAdapterShouldAllowAddingFilterViaPluginLoader()
    {
        $this->adapter->addFilter('StringTrim');
        $test = $this->adapter->getFilter('StringTrim');
        $this->assertTrue($test instanceof Zend_Filter_StringTrim);
    }

    /**
     * @expectedException Zend_File_Transfer_Exception
     */
    public function testAdapterhShouldRaiseExceptionWhenAddingInvalidFilterType()
    {
        $this->adapter->addFilter(new Zend_Validate_File_Extension('jpg'));
    }

    public function testAdapterShouldAllowAddingMultipleFiltersAtOnceUsingBothInstancesAndPluginLoader()
    {
        $filters = array(
            'Word_SeparatorToCamelCase' => array('separator' => ' '),
            array('filter' => 'Alpha', 'options' => array(true)),
            new Zend_Filter_BaseName(),
        );
        $this->adapter->addFilters($filters);
        $test = $this->adapter->getFilters();
        $this->assertTrue(is_array($test));
        $this->assertEquals(3, count($test), var_export($test, 1));
        $count = array_shift($test);
        $this->assertTrue($count instanceof Zend_Filter_Word_SeparatorToCamelCase);
        $size = array_shift($test);
        $this->assertTrue($size instanceof Zend_Filter_Alpha);
        $ext  = array_shift($test);
        $orig = array_pop($filters);
        $this->assertSame($orig, $ext);
    }

    public function testGetFilterShouldReturnNullWhenNoMatchingIdentifierExists()
    {
        $this->assertNull($this->adapter->getFilter('Alpha'));
    }

    public function testAdapterShouldAllowPullingFiltersByFile()
    {
        $this->adapter->addFilter('Alpha', false, 'foo');
        $filters = $this->adapter->getFilters('foo');
        $this->assertEquals(1, count($filters));
        $filter = array_shift($filters);
        $this->assertTrue($filter instanceof Zend_Filter_Alpha);
    }

    public function testCallingSetFiltersOnAdapterShouldOverwriteExistingFilters()
    {
        $this->testAdapterShouldAllowAddingMultipleFiltersAtOnceUsingBothInstancesAndPluginLoader();
        $filters = array(
            new Zend_Filter_StringToUpper(),
            new Zend_Filter_Alpha(),
        );
        $this->adapter->setFilters($filters);
        $test = $this->adapter->getFilters();
        $this->assertSame($filters, array_values($test));
    }

    public function testAdapterShouldAllowRetrievingFilterInstancesByClassName()
    {
        $this->testAdapterShouldAllowAddingMultipleFiltersAtOnceUsingBothInstancesAndPluginLoader();
        $ext = $this->adapter->getFilter('Zend_Filter_BaseName');
        $this->assertTrue($ext instanceof Zend_Filter_BaseName);
    }

    public function testAdapterShouldAllowRetrievingFilterInstancesByPluginName()
    {
        $this->testAdapterShouldAllowAddingMultipleFiltersAtOnceUsingBothInstancesAndPluginLoader();
        $count = $this->adapter->getFilter('Alpha');
        $this->assertTrue($count instanceof Zend_Filter_Alpha);
    }

    public function testAdapterShouldAllowRetrievingAllFiltersAtOnce()
    {
        $this->testAdapterShouldAllowAddingMultipleFiltersAtOnceUsingBothInstancesAndPluginLoader();
        $filters = $this->adapter->getFilters();
        $this->assertTrue(is_array($filters));
        $this->assertEquals(3, count($filters));
        foreach ($filters as $filter) {
            $this->assertTrue($filter instanceof Zend_Filter_Interface);
        }
    }

    public function testAdapterShouldAllowRemovingFilterInstancesByClassName()
    {
        $this->testAdapterShouldAllowAddingMultipleFiltersAtOnceUsingBothInstancesAndPluginLoader();
        $this->assertTrue($this->adapter->hasFilter('Zend_Filter_BaseName'));
        $this->adapter->removeFilter('Zend_Filter_BaseName');
        $this->assertFalse($this->adapter->hasFilter('Zend_Filter_BaseName'));
    }

    public function testAdapterShouldAllowRemovingFilterInstancesByPluginName()
    {
        $this->testAdapterShouldAllowAddingMultipleFiltersAtOnceUsingBothInstancesAndPluginLoader();
        $this->assertTrue($this->adapter->hasFilter('Alpha'));
        $this->adapter->removeFilter('Alpha');
        $this->assertFalse($this->adapter->hasFilter('Alpha'));
    }

    public function testRemovingNonexistentFilterShouldDoNothing()
    {
        $this->testAdapterShouldAllowAddingMultipleFiltersAtOnceUsingBothInstancesAndPluginLoader();
        $filters = $this->adapter->getFilters();
        $this->assertFalse($this->adapter->hasFilter('Int'));
        $this->adapter->removeFilter('Int');
        $this->assertFalse($this->adapter->hasFilter('Int'));
        $test = $this->adapter->getFilters();
        $this->assertSame($filters, $test);
    }

    public function testAdapterShouldAllowRemovingAllFiltersAtOnce()
    {
        $this->testAdapterShouldAllowAddingMultipleFiltersAtOnceUsingBothInstancesAndPluginLoader();
        $this->adapter->clearFilters();
        $filters = $this->adapter->getFilters();
        $this->assertTrue(is_array($filters));
        $this->assertEquals(0, count($filters));
    }

    public function testTransferDestinationShouldBeMutable()
    {
        $directory = dirname(__FILE__);
        $this->adapter->setDestination($directory);
        $destinations = $this->adapter->getDestination();
        $this->assertTrue(is_array($destinations));
        foreach ($destinations as $file => $destination) {
            $this->assertEquals($directory, $destination);
        }

        $newdirectory = dirname(__FILE__)
                      . DIRECTORY_SEPARATOR . '..'
                      . DIRECTORY_SEPARATOR . '..'
                      . DIRECTORY_SEPARATOR . '..'
                      . DIRECTORY_SEPARATOR . '_files';
        $this->adapter->setDestination($newdirectory, 'foo');
        $this->assertEquals($newdirectory, $this->adapter->getDestination('foo'));
        $this->assertEquals($directory, $this->adapter->getDestination('bar'));
    }

    public function testAdapterShouldAllowRetrievingDestinationsForAnArrayOfSpecifiedFiles()
    {
        $this->adapter->setDestination(dirname(__FILE__));
        $destinations = $this->adapter->getDestination(array('bar', 'baz'));
        $this->assertTrue(is_array($destinations));
        $directory = dirname(__FILE__);
        foreach ($destinations as $file => $destination) {
            $this->assertTrue(in_array($file, array('bar', 'baz')));
            $this->assertEquals($directory, $destination);
        }
    }

    public function testSettingAndRetrievingOptions()
    {
        $this->assertEquals(
            array(
                'bar' => array('ignoreNoFile' => false, 'useByteString' => true),
                'baz' => array('ignoreNoFile' => false, 'useByteString' => true),
                'foo' => array('ignoreNoFile' => false, 'useByteString' => true),
                'file_0_' => array('ignoreNoFile' => false, 'useByteString' => true),
                'file_1_' => array('ignoreNoFile' => false, 'useByteString' => true),
            ), $this->adapter->getOptions());

        $this->adapter->setOptions(array('ignoreNoFile' => true));
        $this->assertEquals(
            array(
                'bar' => array('ignoreNoFile' => true, 'useByteString' => true),
                'baz' => array('ignoreNoFile' => true, 'useByteString' => true),
                'foo' => array('ignoreNoFile' => true, 'useByteString' => true),
                'file_0_' => array('ignoreNoFile' => true, 'useByteString' => true),
                'file_1_' => array('ignoreNoFile' => true, 'useByteString' => true),
            ), $this->adapter->getOptions());

        $this->adapter->setOptions(array('ignoreNoFile' => false), 'foo');
        $this->assertEquals(
            array(
                'bar' => array('ignoreNoFile' => true, 'useByteString' => true),
                'baz' => array('ignoreNoFile' => true, 'useByteString' => true),
                'foo' => array('ignoreNoFile' => false, 'useByteString' => true),
                'file_0_' => array('ignoreNoFile' => true, 'useByteString' => true),
                'file_1_' => array('ignoreNoFile' => true, 'useByteString' => true),
            ), $this->adapter->getOptions());
    }

    public function testGetAllAdditionalFileInfos()
    {
        $files = $this->adapter->getFileInfo();
        $this->assertEquals(5, count($files));
        $this->assertEquals('baz.text', $files['baz']['name']);
    }

    public function testGetAdditionalFileInfosForSingleFile()
    {
        $files = $this->adapter->getFileInfo('baz');
        $this->assertEquals(1, count($files));
        $this->assertEquals('baz.text', $files['baz']['name']);
    }

    /**
     * @expectedException Zend_File_Transfer_Exception
     */
    public function testGetAdditionalFileInfosForUnknownFile()
    {
        $files = $this->adapter->getFileInfo('unknown');
    }

    /**
     * @expectedException Zend_File_Transfer_Exception
     */
    public function testGetUnknownOption()
    {
        $this->adapter->setOptions(array('unknownOption' => 'unknown'));
    }

    /**
     * @expectedException Zend_File_Transfer_Exception
     */
    public function testGetFileIsNotImplemented()
    {
        $this->adapter->getFile();
    }

    /**
     * @expectedException Zend_File_Transfer_Exception
     */
    public function testAddFileIsNotImplemented()
    {
        $this->adapter->addFile('foo');
    }

    /**
     * @expectedException Zend_File_Transfer_Exception
     */
    public function testGetTypeIsNotImplemented()
    {
        $this->adapter->getType();
    }

    /**
     * @expectedException Zend_File_Transfer_Exception
     */
    public function testAddTypeIsNotImplemented()
    {
        $this->adapter->addType('foo');
    }

    public function testAdapterShouldAllowRetrievingFileName()
    {
        $path = dirname(__FILE__)
              . DIRECTORY_SEPARATOR . '..'
              . DIRECTORY_SEPARATOR . '..'
              . DIRECTORY_SEPARATOR . '..'
              . DIRECTORY_SEPARATOR . '_files';
        $this->adapter->setDestination($path);
        $this->assertEquals($path . DIRECTORY_SEPARATOR . 'foo.jpg', $this->adapter->getFileName('foo'));
    }

    public function testAdapterShouldAllowRetrievingFileNameWithoutPath()
    {
        $path = dirname(__FILE__)
              . DIRECTORY_SEPARATOR . '..'
              . DIRECTORY_SEPARATOR . '..'
              . DIRECTORY_SEPARATOR . '..'
              . DIRECTORY_SEPARATOR . '_files';
        $this->adapter->setDestination($path);
        $this->assertEquals('foo.jpg', $this->adapter->getFileName('foo', false));
    }

    public function testAdapterShouldAllowRetrievingAllFileNames()
    {
        $path = dirname(__FILE__)
              . DIRECTORY_SEPARATOR . '..'
              . DIRECTORY_SEPARATOR . '..'
              . DIRECTORY_SEPARATOR . '..'
              . DIRECTORY_SEPARATOR . '_files';
        $this->adapter->setDestination($path);
        $files = $this->adapter->getFileName();
        $this->assertTrue(is_array($files));
        $this->assertEquals($path . DIRECTORY_SEPARATOR . 'bar.png', $files['bar']);
    }

    public function testAdapterShouldAllowRetrievingAllFileNamesWithoutPath()
    {
        $path = dirname(__FILE__)
              . DIRECTORY_SEPARATOR . '..'
              . DIRECTORY_SEPARATOR . '..'
              . DIRECTORY_SEPARATOR . '..'
              . DIRECTORY_SEPARATOR . '_files';
        $this->adapter->setDestination($path);
        $files = $this->adapter->getFileName(null, false);
        $this->assertTrue(is_array($files));
        $this->assertEquals('bar.png', $files['bar']);
    }

    public function testExceptionForUnknownHashValue()
    {
        try {
            $this->adapter->getHash('foo', 'unknown_hash');
            $this->fail();
        } catch (Zend_Exception $e) {
            $this->assertContains('Unknown hash algorithm', $e->getMessage());
        }
    }

    public function testIgnoreHashValue()
    {
        $this->adapter->addInvalidFile();
        $return = $this->adapter->getHash('crc32', 'test');
        $this->assertEquals(array(), $return);
    }

    public function testEmptyTempDirectoryDetection()
    {
        $this->adapter->_tmpDir = "";
        $this->assertTrue(empty($this->adapter->_tmpDir), "Empty temporary directory");
    }

    public function testTempDirectoryDetection()
    {
        $this->adapter->getTmpDir();
        $this->assertTrue(!empty($this->adapter->_tmpDir), "Temporary directory filled");
    }

    public function testTemporaryDirectoryAccessDetection()
    {
        $this->adapter->_tmpDir = ".";
        $path = "/NoPath/To/File";
        $this->assertFalse($this->adapter->isPathWriteable($path));
        $this->assertTrue($this->adapter->isPathWriteable($this->adapter->_tmpDir));
    }

    public function testFileSizeButNoFileFound()
    {
        try {
            $this->assertEquals(10, $this->adapter->getFileSize());
            $this->fail();
        } catch (Zend_File_Transfer_Exception $e) {
            $this->assertContains('does not exist', $e->getMessage());
        }
    }

    public function testIgnoreFileSize()
    {
        $this->adapter->addInvalidFile();
        $return = $this->adapter->getFileSize('test');
        $this->assertEquals(array(), $return);
    }

    public function testFileSizeByTmpName()
    {
        $options = $this->adapter->getOptions();
        $this->assertTrue($options['baz']['useByteString']);
        $this->assertEquals('8B', $this->adapter->getFileSize('baz.text'));
        $this->adapter->setOptions(array('useByteString' => false));
        $options = $this->adapter->getOptions();
        $this->assertFalse($options['baz']['useByteString']);
        $this->assertEquals(8, $this->adapter->getFileSize('baz.text'));
    }

    public function testMimeTypeButNoFileFound()
    {
        try {
            $this->assertEquals('image/jpeg', $this->adapter->getMimeType());
            $this->fail();
        } catch (Zend_File_Transfer_Exception $e) {
            $this->assertContains('does not exist', $e->getMessage());
        }
    }

    public function testIgnoreMimeType()
    {
        $this->adapter->addInvalidFile();
        $return = $this->adapter->getMimeType('test');
        $this->assertEquals(array(), $return);
    }

    public function testMimeTypeByTmpName()
    {
        $this->assertEquals('text/plain', $this->adapter->getMimeType('baz.text'));
    }

    public function testSetOwnErrorMessage()
    {
        $this->adapter->addValidator('Count', false, array('min' => 5, 'max' => 5, 'messages' => array(Zend_Validate_File_Count::TOO_FEW => 'Zu wenige')));
        $this->assertFalse($this->adapter->isValid('foo'));
        $message = $this->adapter->getMessages();
        $this->assertContains('Zu wenige', $message);

        try {
            $this->assertEquals('image/jpeg', $this->adapter->getMimeType());
            $this->fail();
        } catch (Zend_File_Transfer_Exception $e) {
            $this->assertContains('does not exist', $e->getMessage());
        }
    }

    public function testTransferDestinationAtNonExistingElement()
    {
        $directory = dirname(__FILE__);
        $this->adapter->setDestination($directory, 'nonexisting');
        $this->assertEquals($directory, $this->adapter->getDestination('nonexisting'));
        try {
            $this->assertTrue(is_string($this->adapter->getDestination('reallynonexisting')));
            $this->fail();
        } catch(Exception $e) {
            $this->assertContains('not found', $e->getMessage());
        }
    }

    /**
     * @ZF-7376
     */
    public function testSettingMagicFile()
    {
        $this->adapter->setOptions(array('magicFile' => 'test/file'));
        $this->assertEquals(
            array(
                'bar' => array('magicFile' => 'test/file', 'ignoreNoFile' => false, 'useByteString' => true),
            ), $this->adapter->getOptions('bar'));
    }
}

class Zend_File_Transfer_Adapter_AbstractTest_MockAdapter extends Zend_File_Transfer_Adapter_Abstract
{
    public $received = false;

    public $_tmpDir;

    public function __construct()
    {
        $testfile = dirname(__FILE__) . '/_files/test.txt';
        $this->_files = array(
            'foo' => array(
                'name'      => 'foo.jpg',
                'type'      => 'image/jpeg',
                'size'      => 126976,
                'tmp_name'  => '/tmp/489127ba5c89c',
                'options'   => array('ignoreNoFile' => false, 'useByteString' => true),
                'validated' => false,
                'received'  => false,
                'filtered'  => false,
            ),
            'bar' => array(
                'name'     => 'bar.png',
                'type'     => 'image/png',
                'size'     => 91136,
                'tmp_name' => '/tmp/489128284b51f',
                'options'  => array('ignoreNoFile' => false, 'useByteString' => true),
                'validated' => false,
                'received'  => false,
                'filtered'  => false,
            ),
            'baz' => array(
                'name'     => 'baz.text',
                'type'     => 'text/plain',
                'size'     => 1172,
                'tmp_name' => $testfile,
                'options'  => array('ignoreNoFile' => false, 'useByteString' => true),
                'validated' => false,
                'received'  => false,
                'filtered'  => false,
            ),
            'file_0_' => array(
                'name'      => 'foo.jpg',
                'type'      => 'image/jpeg',
                'size'      => 126976,
                'tmp_name'  => '/tmp/489127ba5c89c',
                'options'   => array('ignoreNoFile' => false, 'useByteString' => true),
                'validated' => false,
                'received'  => false,
                'filtered'  => false,
            ),
            'file_1_' => array(
                'name'     => 'baz.text',
                'type'     => 'text/plain',
                'size'     => 1172,
                'tmp_name' => $testfile,
                'options'  => array('ignoreNoFile' => false, 'useByteString' => true),
                'validated' => false,
                'received'  => false,
                'filtered'  => false,
            ),
            'file' => array(
                'name'      => 'foo.jpg',
                'multifiles' => array(0 => 'file_0_', 1 => 'file_1_')
            ),
        );
    }

    public function send($options = null)
    {
        return;
    }

    public function receive($options = null)
    {
        $this->received = true;
        return;
    }

    public function isSent($file = null)
    {
        return false;
    }

    public function isReceived($file = null)
    {
        return $this->received;
    }

    public function isUploaded($files = null)
    {
        return true;
    }

    public function isFiltered($files = null)
    {
        return true;
    }

    public static function getProgress()
    {
        return;
    }

    public function getTmpDir()
    {
        $this->_tmpDir = parent::_getTmpDir();
    }

    public function isPathWriteable($path)
    {
        return parent::_isPathWriteable($path);
    }

    public function addInvalidFile()
    {
        $this->_files += array(
            'test' => array(
                'name'      => 'test.txt',
                'type'      => 'image/jpeg',
                'size'      => 0,
                'tmp_name'  => '',
                'options'   => array('ignoreNoFile' => true, 'useByteString' => true),
                'validated' => false,
                'received'  => false,
                'filtered'  => false,
            )
        );
    }
}

// Call Zend_File_Transfer_Adapter_AbstractTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_File_Transfer_Adapter_AbstractTest::main") {
    Zend_File_Transfer_Adapter_AbstractTest::main();
}
