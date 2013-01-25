<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_File
 */

namespace ZendTest\File\Transfer\Adapter;

use Zend\File;
use Zend\Filter;
use Zend\Filter\Word;
use Zend\Loader;
use Zend\Validator;
use Zend\Validator\File as FileValidator;

/**
 * Test class for Zend\File\Transfer\Adapter\AbstractAdapter
 *
 * @category   Zend
 * @package    Zend_File
 * @subpackage UnitTests
 * @group      Zend_File
 */
class AbstractTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @return void
     */
    public function setUp()
    {
        $this->adapter = new AbstractAdapterTestMockAdapter();
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

    public function testAdapterShouldLazyLoadValidatorPluginManager()
    {
        $loader = $this->adapter->getValidatorManager();
        $this->assertInstanceOf('Zend\File\Transfer\Adapter\ValidatorPluginManager', $loader);
    }

    public function testAdapterShouldAllowSettingFilterPluginManagerInstance()
    {
        $manager = new File\Transfer\Adapter\FilterPluginManager();
        $this->adapter->setFilterManager($manager);
        $this->assertSame($manager, $this->adapter->getFilterManager());
    }

    public function testAdapterShouldAllowAddingValidatorInstance()
    {
        $validator = new FileValidator\Count(array('min' => 1, 'max' => 1));
        $this->adapter->addValidator($validator);
        $test = $this->adapter->getValidator('Zend\Validator\File\Count');
        $this->assertSame($validator, $test);
    }

    public function testAdapterShouldAllowAddingValidatorViaPluginManager()
    {
        $this->adapter->addValidator('Count', false, array('min' => 1, 'max' => 1));
        $test = $this->adapter->getValidator('Count');
        $this->assertTrue($test instanceof FileValidator\Count);
    }

    public function testAdapterhShouldRaiseExceptionWhenAddingInvalidValidatorType()
    {
        $this->setExpectedException('Zend\File\Transfer\Exception\InvalidArgumentException', 'Invalid validator provided to addValidator');
        $this->adapter->addValidator(new Filter\BaseName);
    }

    public function testAdapterShouldAllowAddingMultipleValidatorsAtOnceUsingBothInstancesAndPluginLoader()
    {
        $validators = array(
            'count' => array('min' => 1, 'max' => 1),
            'Exists' => 'C:\temp',
            array(
                'validator' => 'Upload',
                'options' => array(realpath(__FILE__))
            ),
            new FileValidator\Extension('jpg'),
        );
        $this->adapter->addValidators($validators);
        $test = $this->adapter->getValidators();
        $this->assertTrue(is_array($test));
        $this->assertEquals(4, count($test), var_export($test, 1));
        $count = array_shift($test);
        $this->assertTrue($count instanceof FileValidator\Count);
        $exists = array_shift($test);
        $this->assertTrue($exists instanceof FileValidator\Exists);
        $size = array_shift($test);
        $this->assertTrue($size instanceof FileValidator\Upload);
        $ext = array_shift($test);
        $this->assertTrue($ext instanceof FileValidator\Extension);
        $orig = array_pop($validators);
        $this->assertSame($orig, $ext);
    }

    public function testGetValidatorShouldReturnNullWhenNoMatchingIdentifierExists()
    {
        $this->assertNull($this->adapter->getValidator('Between'));
    }

    public function testAdapterShouldAllowPullingValidatorsByFile()
    {
        $this->adapter->addValidator('Between', false, false, 'foo');
        $validators = $this->adapter->getValidators('foo');
        $this->assertEquals(1, count($validators));
        $validator = array_shift($validators);
        $this->assertTrue($validator instanceof Validator\Between);
    }

    public function testCallingSetValidatorsOnAdapterShouldOverwriteExistingValidators()
    {
        $this->testAdapterShouldAllowAddingMultipleValidatorsAtOnceUsingBothInstancesAndPluginLoader();
        $validators = array(
            new FileValidator\Count(1),
            new FileValidator\Extension('jpg'),
        );
        $this->adapter->setValidators($validators);
        $test = $this->adapter->getValidators();
        $this->assertSame($validators, array_values($test));
    }

    public function testAdapterShouldAllowRetrievingValidatorInstancesByClassName()
    {
        $this->testAdapterShouldAllowAddingMultipleValidatorsAtOnceUsingBothInstancesAndPluginLoader();
        $ext = $this->adapter->getValidator('Zend\Validator\File\Extension');
        $this->assertTrue($ext instanceof FileValidator\Extension);
    }

    public function testAdapterShouldAllowRetrievingValidatorInstancesByPluginName()
    {
        $this->testAdapterShouldAllowAddingMultipleValidatorsAtOnceUsingBothInstancesAndPluginLoader();
        $count = $this->adapter->getValidator('Count');
        $this->assertTrue($count instanceof FileValidator\Count);
    }

    public function testAdapterShouldAllowRetrievingAllValidatorsAtOnce()
    {
        $this->testAdapterShouldAllowAddingMultipleValidatorsAtOnceUsingBothInstancesAndPluginLoader();
        $validators = $this->adapter->getValidators();
        $this->assertTrue(is_array($validators));
        $this->assertEquals(4, count($validators));
        foreach ($validators as $validator) {
            $this->assertTrue($validator instanceof Validator\ValidatorInterface);
        }
    }

    public function testAdapterShouldAllowRemovingValidatorInstancesByClassName()
    {
        $this->testAdapterShouldAllowAddingMultipleValidatorsAtOnceUsingBothInstancesAndPluginLoader();
        $this->assertTrue($this->adapter->hasValidator('Zend\Validator\File\Extension'));
        $this->adapter->removeValidator('Zend\Validator\File\Extension');
        $this->assertFalse($this->adapter->hasValidator('Zend\Validator\File\Extension'));
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
        $this->assertFalse($this->adapter->hasValidator('Between'));
        $this->adapter->removeValidator('Between');
        $this->assertFalse($this->adapter->hasValidator('Between'));
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

    public function testAdapterShouldLazyLoadFilterPluginManager()
    {
        $loader = $this->adapter->getFilterManager();
        $this->assertInstanceOf('Zend\File\Transfer\Adapter\FilterPluginManager', $loader);
    }

    public function testAdapterShouldAllowAddingFilterInstance()
    {
        $filter = new Filter\StringToLower();
        $this->adapter->addFilter($filter);
        $test = $this->adapter->getFilter('Zend\Filter\StringToLower');
        $this->assertSame($filter, $test);
    }

    public function testAdapterShouldAllowAddingFilterViaPluginManager()
    {
        $this->adapter->addFilter('StringTrim');
        $test = $this->adapter->getFilter('StringTrim');
        $this->assertTrue($test instanceof Filter\StringTrim);
    }


    public function testAdapterhShouldRaiseExceptionWhenAddingInvalidFilterType()
    {
        $this->setExpectedException('Zend\File\Transfer\Exception\InvalidArgumentException', 'Invalid filter specified');
        $this->adapter->addFilter(new FileValidator\Extension('jpg'));
    }

    public function testAdapterShouldAllowAddingMultipleFiltersAtOnceUsingBothInstancesAndPluginLoader()
    {
        $filters = array(
            'Word\SeparatorToCamelCase' => array('separator' => ' '),
            array(
                'filter' => 'Boolean',
                'casting' => true
            ),
            new Filter\BaseName(),
        );
        $this->adapter->addFilters($filters);
        $test = $this->adapter->getFilters();
        $this->assertTrue(is_array($test));
        $this->assertEquals(3, count($test), var_export($test, 1));
        $count = array_shift($test);
        $this->assertTrue($count instanceof Word\SeparatorToCamelCase);
        $size = array_shift($test);
        $this->assertTrue($size instanceof Filter\Boolean);
        $ext  = array_shift($test);
        $orig = array_pop($filters);
        $this->assertSame($orig, $ext);
    }

    public function testGetFilterShouldReturnNullWhenNoMatchingIdentifierExists()
    {
        $this->assertNull($this->adapter->getFilter('Boolean'));
    }

    public function testAdapterShouldAllowPullingFiltersByFile()
    {
        $this->adapter->addFilter('Boolean', 1, 'foo');
        $filters = $this->adapter->getFilters('foo');
        $this->assertEquals(1, count($filters));
        $filter = array_shift($filters);
        $this->assertTrue($filter instanceof Filter\Boolean);
    }

    public function testCallingSetFiltersOnAdapterShouldOverwriteExistingFilters()
    {
        $this->testAdapterShouldAllowAddingMultipleFiltersAtOnceUsingBothInstancesAndPluginLoader();
        $filters = array(
            new Filter\StringToUpper(),
            new Filter\Boolean(),
        );
        $this->adapter->setFilters($filters);
        $test = $this->adapter->getFilters();
        $this->assertSame($filters, array_values($test));
    }

    public function testAdapterShouldAllowRetrievingFilterInstancesByClassName()
    {
        $this->testAdapterShouldAllowAddingMultipleFiltersAtOnceUsingBothInstancesAndPluginLoader();
        $ext = $this->adapter->getFilter('Zend\Filter\BaseName');
        $this->assertTrue($ext instanceof Filter\BaseName);
    }

    public function testAdapterShouldAllowRetrievingFilterInstancesByPluginName()
    {
        $this->testAdapterShouldAllowAddingMultipleFiltersAtOnceUsingBothInstancesAndPluginLoader();
        $count = $this->adapter->getFilter('Boolean');
        $this->assertTrue($count instanceof Filter\Boolean);
    }

    public function testAdapterShouldAllowRetrievingAllFiltersAtOnce()
    {
        $this->testAdapterShouldAllowAddingMultipleFiltersAtOnceUsingBothInstancesAndPluginLoader();
        $filters = $this->adapter->getFilters();
        $this->assertTrue(is_array($filters));
        $this->assertEquals(3, count($filters));
        foreach ($filters as $filter) {
            $this->assertTrue($filter instanceof Filter\FilterInterface);
        }
    }

    public function testAdapterShouldAllowRemovingFilterInstancesByClassName()
    {
        $this->testAdapterShouldAllowAddingMultipleFiltersAtOnceUsingBothInstancesAndPluginLoader();
        $this->assertTrue($this->adapter->hasFilter('Zend\Filter\BaseName'));
        $this->adapter->removeFilter('Zend\Filter\BaseName');
        $this->assertFalse($this->adapter->hasFilter('Zend\Filter\BaseName'));
    }

    public function testAdapterShouldAllowRemovingFilterInstancesByPluginName()
    {
        $this->testAdapterShouldAllowAddingMultipleFiltersAtOnceUsingBothInstancesAndPluginLoader();
        $this->assertTrue($this->adapter->hasFilter('Boolean'));
        $this->adapter->removeFilter('Boolean');
        $this->assertFalse($this->adapter->hasFilter('Boolean'));
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
        $directory = __DIR__;
        $this->adapter->setDestination($directory);
        $destinations = $this->adapter->getDestination();
        $this->assertTrue(is_array($destinations));
        foreach ($destinations as $file => $destination) {
            $this->assertEquals($directory, $destination);
        }

        $newdirectory = __DIR__
                      . DIRECTORY_SEPARATOR . '_files';
        $this->adapter->setDestination($newdirectory, 'foo');
        $this->assertEquals($newdirectory, $this->adapter->getDestination('foo'));
        $this->assertEquals($directory, $this->adapter->getDestination('bar'));
    }

    public function testAdapterShouldAllowRetrievingDestinationsForAnArrayOfSpecifiedFiles()
    {
        $this->adapter->setDestination(__DIR__);
        $destinations = $this->adapter->getDestination(array('bar', 'baz'));
        $this->assertTrue(is_array($destinations));
        $directory = __DIR__;
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
                'foo' => array('ignoreNoFile' => false, 'useByteString' => true, 'detectInfos' => true),
                'file_0_' => array('ignoreNoFile' => false, 'useByteString' => true),
                'file_1_' => array('ignoreNoFile' => false, 'useByteString' => true),
            ), $this->adapter->getOptions());

        $this->adapter->setOptions(array('ignoreNoFile' => true));
        $this->assertEquals(
            array(
                'bar' => array('ignoreNoFile' => true, 'useByteString' => true),
                'baz' => array('ignoreNoFile' => true, 'useByteString' => true),
                'foo' => array('ignoreNoFile' => true, 'useByteString' => true, 'detectInfos' => true),
                'file_0_' => array('ignoreNoFile' => true, 'useByteString' => true),
                'file_1_' => array('ignoreNoFile' => true, 'useByteString' => true),
            ), $this->adapter->getOptions());

        $this->adapter->setOptions(array('ignoreNoFile' => false), 'foo');
        $this->assertEquals(
            array(
                'bar' => array('ignoreNoFile' => true, 'useByteString' => true),
                'baz' => array('ignoreNoFile' => true, 'useByteString' => true),
                'foo' => array('ignoreNoFile' => false, 'useByteString' => true, 'detectInfos' => true),
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

    public function testGetAdditionalFileInfosForUnknownFile()
    {
        $this->setExpectedException('Zend\File\Transfer\Exception\RuntimeException', 'The file transfer adapter can not find "unknown"');
        $files = $this->adapter->getFileInfo('unknown');
    }

    public function testAdapterShouldAllowRetrievingFileName()
    {
        $path = __DIR__
              . DIRECTORY_SEPARATOR . '_files';
        $this->adapter->setDestination($path);
        $this->assertEquals($path . DIRECTORY_SEPARATOR . 'foo.jpg', $this->adapter->getFileName('foo'));
    }

    public function testAdapterShouldAllowRetrievingFileNameWithoutPath()
    {
        $path = __DIR__
              . DIRECTORY_SEPARATOR . '_files';
        $this->adapter->setDestination($path);
        $this->assertEquals('foo.jpg', $this->adapter->getFileName('foo', false));
    }

    public function testAdapterShouldAllowRetrievingAllFileNames()
    {
        $path = __DIR__
              . DIRECTORY_SEPARATOR . '_files';
        $this->adapter->setDestination($path);
        $files = $this->adapter->getFileName();
        $this->assertTrue(is_array($files));
        $this->assertEquals($path . DIRECTORY_SEPARATOR . 'bar.png', $files['bar']);
    }

    public function testAdapterShouldAllowRetrievingAllFileNamesWithoutPath()
    {
        $path = __DIR__
              . DIRECTORY_SEPARATOR . '_files';
        $this->adapter->setDestination($path);
        $files = $this->adapter->getFileName(null, false);
        $this->assertTrue(is_array($files));
        $this->assertEquals('bar.png', $files['bar']);
    }

    public function testExceptionForUnknownHashValue()
    {
        $this->setExpectedException('Zend\File\Transfer\Exception\InvalidArgumentException', 'Unknown hash algorithm');
        $this->adapter->getHash('foo', 'unknown_hash');
    }

    public function testIgnoreHashValue()
    {
        $this->adapter->addInvalidFile();
        $return = $this->adapter->getHash('crc32', 'test');
        $this->assertEquals(array(), $return);
    }

    public function testEmptyTempDirectoryDetection()
    {
        $this->adapter->tmpDir = "";
        $this->assertTrue(empty($this->adapter->tmpDir), "Empty temporary directory");
    }

    public function testTempDirectoryDetection()
    {
        $this->adapter->getTmpDir();
        $this->assertTrue(!empty($this->adapter->tmpDir), "Temporary directory filled");
    }

    public function testTemporaryDirectoryAccessDetection()
    {
        $this->adapter->tmpDir = ".";
        $path = "/NoPath/To/File";
        $this->assertFalse($this->adapter->isPathWriteable($path));
        $this->assertTrue($this->adapter->isPathWriteable($this->adapter->tmpDir));
    }

    public function testFileSizeButNoFileFound()
    {
        $this->setExpectedException('Zend\File\Transfer\Exception\InvalidArgumentException', 'does not exist');
        $this->assertEquals(10, $this->adapter->getFileSize());
    }

    public function testIgnoreFileSize()
    {
        $this->adapter->addInvalidFile();
        $return = $this->adapter->getFileSize('test');
        $this->assertEquals(array(), $return);
    }

    public function testFileSizeByTmpName()
    {
        $expectedSize = sprintf("%.2fkB", 1.14);
        $options = $this->adapter->getOptions();
        $this->assertTrue($options['baz']['useByteString']);
        $this->assertEquals($expectedSize, $this->adapter->getFileSize('baz.text'));
        $this->adapter->setOptions(array('useByteString' => false));
        $options = $this->adapter->getOptions();
        $this->assertFalse($options['baz']['useByteString']);
        $this->assertEquals(1172, $this->adapter->getFileSize('baz.text'));
    }

    public function testMimeTypeButNoFileFound()
    {
        $this->setExpectedException('Zend\File\Transfer\Exception\InvalidArgumentException', 'does not exist');
        $this->assertEquals('image/jpeg', $this->adapter->getMimeType());
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
        $this->adapter->addValidator('Count', false, array('min' => 5, 'max' => 5, 'messages' => array(FileValidator\Count::TOO_FEW => 'Zu wenige')));
        $this->assertFalse($this->adapter->isValid('foo'));
        $message = $this->adapter->getMessages();
        $this->assertContains('Zu wenige', $message);

        $this->setExpectedException('Zend\File\Transfer\Exception\InvalidArgumentException', 'does not exist');
        $this->assertEquals('image/jpeg', $this->adapter->getMimeType());
    }

    public function testTransferDestinationAtNonExistingElement()
    {
        $directory = __DIR__;
        $this->adapter->setDestination($directory, 'nonexisting');
        $this->assertEquals($directory, $this->adapter->getDestination('nonexisting'));

        $this->setExpectedException('Zend\File\Transfer\Exception\InvalidArgumentException', 'not find');
        $this->assertTrue(is_string($this->adapter->getDestination('reallynonexisting')));
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

    /**
     * @ZF-8693
     */
    public function testAdapterShouldAllowAddingMultipleValidatorsAtOnceUsingBothInstancesAndPluginLoaderForDifferentFiles()
    {
        $validators = array(
            array('MimeType', true, array('image/jpeg')), // no files
            array('FilesSize', true, array('max' => '1MB', 'message' => 'файл больше 1MБ')), // no files
            array('Count', true, array('min' => 1, 'max' => '1', 'message' => 'файл не 1'), 'bar'), // 'bar' from config
            array('MimeType', true, array('image/jpeg'), 'bar'), // 'bar' from config
        );

        $this->adapter->addValidators($validators, 'foo'); // set validators to 'foo'

        $test = $this->adapter->getValidators();
        $this->assertEquals(3, count($test));

        //test files specific validators
        $test = $this->adapter->getValidators('foo');
        $this->assertEquals(2, count($test));
        $mimeType = array_shift($test);
        $this->assertTrue($mimeType instanceof FileValidator\MimeType);
        $filesSize = array_shift($test);
        $this->assertTrue($filesSize instanceof FileValidator\FilesSize);

        $test = $this->adapter->getValidators('bar');
        $this->assertEquals(2, count($test));
        $filesSize = array_shift($test);
        $this->assertTrue($filesSize instanceof FileValidator\Count);
        $mimeType = array_shift($test);
        $this->assertTrue($mimeType instanceof FileValidator\MimeType);

        $test = $this->adapter->getValidators('baz');
        $this->assertEquals(0, count($test));
    }

    /**
     * @ZF-9132
     */
    public function testSettingAndRetrievingDetectInfosOption()
    {
        $this->assertEquals(array(
            'foo' => array(
                'ignoreNoFile' => false,
                'useByteString' => true,
                'detectInfos' => true))
            , $this->adapter->getOptions('foo'));
        $this->adapter->setOptions(array('detectInfos' => false));
        $this->assertEquals(array(
            'foo' => array(
                'ignoreNoFile' => false,
                'useByteString' => true,
                'detectInfos' => false))
            , $this->adapter->getOptions('foo'));
    }
}
