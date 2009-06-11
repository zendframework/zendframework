<?php
// Call Zend_File_Transfer_Adapter_HttpTest::main() if this source file is executed directly.
if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_File_Transfer_Adapter_HttpTest::main");
}

require_once dirname(__FILE__) . '/../../../../TestHelper.php';

require_once 'Zend/File/Transfer/Adapter/Http.php';
require_once 'Zend/Filter/BaseName.php';
require_once 'Zend/Filter/StringToLower.php';
require_once 'Zend/Loader/PluginLoader.php';
require_once 'Zend/Validate/File/Count.php';
require_once 'Zend/Validate/File/Extension.php';
require_once 'Zend/Validate/File/Upload.php';

/**
 * Test class for Zend_File_Transfer_Adapter_Http
 */
class Zend_File_Transfer_Adapter_HttpTest extends PHPUnit_Framework_TestCase
{
    /**
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_File_Transfer_Adapter_HttpTest");
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
        $_FILES = array(
            'txt' => array(
                'name' => 'file.txt',
                'type' => 'plain/text',
                'size' => 8,
                'tmp_name' => 'file.txt',
                'error' => 0));
        $this->adapter = new Zend_File_Transfer_Adapter_HttpTest_MockAdapter();
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

    public function testEmptyAdapter()
    {
        $files = $this->adapter->getFileName();
        $this->assertContains('file.txt', $files);
    }

    public function testAutoSetUploadValidator()
    {
        $validators = array(
            new Zend_Validate_File_Count(1),
            new Zend_Validate_File_Extension('jpg'),
        );
        $this->adapter->setValidators($validators);
        $test = $this->adapter->getValidator('Upload');
        $this->assertTrue($test instanceof Zend_Validate_File_Upload);
    }

    /**
     * @expectedException Zend_File_Transfer_Exception
     */
    public function testSendingFiles()
    {
        $this->adapter->send();
    }

    /**
     * @expectedException Zend_File_Transfer_Exception
     */
    public function testFileIsSent()
    {
        $this->adapter->isSent();
    }

    public function testFileIsUploaded()
    {
        $this->assertTrue($this->adapter->isUploaded());
    }

    public function testFileIsNotUploaded()
    {
        $this->assertFalse($this->adapter->isUploaded('unknownFile'));
    }

    public function testFileIsNotFiltered()
    {
        $this->assertFalse($this->adapter->isFiltered('unknownFile'));
        $this->assertFalse($this->adapter->isFiltered());
    }

    public function testFileIsNotReceived()
    {
        $this->assertFalse($this->adapter->isReceived('unknownFile'));
        $this->assertFalse($this->adapter->isReceived());
    }

    public function testReceiveUnknownFile()
    {
        try {
            $this->assertFalse($this->adapter->receive('unknownFile'));
        } catch (Zend_File_Transfer_Exception $e) {
            $this->assertContains('not found', $e->getMessage());
        }
    }

    public function testReceiveValidatedFile()
    {
        $this->assertFalse($this->adapter->receive());
    }

    public function testReceiveIgnoredFile()
    {
        $this->adapter->setOptions(array('ignoreNoFile' => true));
        $this->assertTrue($this->adapter->receive());
    }

    public function testReceiveWithRenameFilter()
    {
        $this->adapter->addFilter('Rename', array('target' => '/testdir'));
        $this->adapter->setOptions(array('ignoreNoFile' => true));
        $this->assertTrue($this->adapter->receive());
    }

    public function testReceiveWithRenameFilterButWithoutDirectory()
    {
        $this->adapter->setDestination(dirname(__FILE__));
        $this->adapter->addFilter('Rename', array('overwrite' => false));
        $this->adapter->setOptions(array('ignoreNoFile' => true));
        $this->assertTrue($this->adapter->receive());
    }

    public function testMultiFiles()
    {
        $_FILES = array(
            'txt' => array(
                'name' => 'file.txt',
                'type' => 'plain/text',
                'size' => 8,
                'tmp_name' => 'file.txt',
                'error' => 0),
            'exe' => array(
                'name' => array(
                    0 => 'file1.txt',
                    1 => 'file2.txt'),
                'type' => array(
                    0 => 'plain/text',
                    1 => 'plain/text'),
                'size' => array(
                    0 => 8,
                    1 => 8),
                'tmp_name' => array(
                    0 => 'file1.txt',
                    1 => 'file2.txt'),
                'error' => array(
                    0 => 0,
                    1 => 0)));
        $adapter = new Zend_File_Transfer_Adapter_HttpTest_MockAdapter();
        $adapter->setOptions(array('ignoreNoFile' => true));
        $this->assertTrue($adapter->receive('exe'));
        $this->assertEquals(
            array('exe_0_' => 'file1.txt',
                  'exe_1_' => 'file2.txt'),
            $adapter->getFileName('exe', false));
    }

    public function testNoUploadInProgress()
    {
        if (!(ini_get('apc.enabled') && (bool) ini_get('apc.rfc1867') && is_callable('apc_fetch')) &&
            !is_callable('uploadprogress_get_info')) {
            $this->markTestSkipped('Whether APC nor UploadExtension available');
            return;
        }

        $status = Zend_File_Transfer_Adapter_HttpTest_MockAdapter::getProgress();
        $this->assertContains('No upload in progress', $status);
    }

    public function testUploadProgressFailure()
    {
        if (!(ini_get('apc.enabled') && (bool) ini_get('apc.rfc1867') && is_callable('apc_fetch')) &&
            !is_callable('uploadprogress_get_info')) {
            $this->markTestSkipped('Whether APC nor UploadExtension available');
            return;
        }

        $_GET['progress_key'] = 'mykey';
        $status = Zend_File_Transfer_Adapter_HttpTest_MockAdapter::getProgress();
        $this->assertEquals(array(
            'total'   => 100,
            'current' => 100,
            'rate'    => 10,
            'id'      => 'mykey',
            'done'    => false,
            'message' => '100B - 100B'), $status);

        $this->adapter->switchApcToUP();
        $status = Zend_File_Transfer_Adapter_HttpTest_MockAdapter::getProgress($status);
        $this->assertEquals(array(
            'total'          => 100,
            'bytes_total'    => 100,
            'current'        => 100,
            'bytes_uploaded' => 100,
            'rate'           => 10,
            'speed_average'  => 10,
            'cancel_upload'  => true,
            'message'        => 'The upload has been canceled',
            'done'           => true,
            'id'      => 'mykey'), $status);

    }

    public function testUploadProgressAdapter()
    {
        if (!(ini_get('apc.enabled') && (bool) ini_get('apc.rfc1867') && is_callable('apc_fetch')) &&
            !is_callable('uploadprogress_get_info')) {
            $this->markTestSkipped('Whether APC nor UploadExtension available');
            return;
        }

        $_GET['progress_key'] = 'mykey';
        require_once 'Zend/ProgressBar/Adapter/Console.php';
        $adapter = new Zend_ProgressBar_Adapter_Console();
        $status = array('progress' => $adapter, 'session' => 'upload');
        $status = Zend_File_Transfer_Adapter_HttpTest_MockAdapter::getProgress($status);
        $this->assertTrue(array_key_exists('total', $status));
        $this->assertTrue(array_key_exists('current', $status));
        $this->assertTrue(array_key_exists('rate', $status));
        $this->assertTrue(array_key_exists('id', $status));
        $this->assertTrue(array_key_exists('message', $status));
        $this->assertTrue(array_key_exists('progress', $status));
        $this->assertTrue($status['progress'] instanceof Zend_ProgressBar);

        $this->adapter->switchApcToUP();
        $status = Zend_File_Transfer_Adapter_HttpTest_MockAdapter::getProgress($status);
        $this->assertTrue(array_key_exists('total', $status));
        $this->assertTrue(array_key_exists('current', $status));
        $this->assertTrue(array_key_exists('rate', $status));
        $this->assertTrue(array_key_exists('id', $status));
        $this->assertTrue(array_key_exists('message', $status));
        $this->assertTrue(array_key_exists('progress', $status));
        $this->assertTrue($status['progress'] instanceof Zend_ProgressBar);
    }

    public function testValidationOfPhpExtendsFormError()
    {
        $_SERVER['CONTENT_LENGTH'] = 10;

        $_FILES = array();
        $adapter = new Zend_File_Transfer_Adapter_HttpTest_MockAdapter();
        $this->assertFalse($adapter->isValidParent());
        $this->assertContains('exceeds the defined ini size', current($adapter->getMessages()));
    }
}

class Zend_File_Transfer_Adapter_HttpTest_MockAdapter extends Zend_File_Transfer_Adapter_Http
{
    public function __construct()
    {
        self::$_callbackApc = array('Zend_File_Transfer_Adapter_HttpTest_MockAdapter', 'apcTest');
        parent::__construct();
    }

    public function isValid($files = null)
    {
        return true;
    }

    public function isValidParent($files = null)
    {
        return parent::isValid($files);
    }

    public static function isApcAvailable()
    {
        return true;
    }

    public static function apcTest($id)
    {
        return array('total' => 100, 'current' => 100, 'rate' => 10);
    }

    public static function uPTest($id)
    {
        return array('bytes_total' => 100, 'bytes_uploaded' => 100, 'speed_average' => 10, 'cancel_upload' => true);
    }

    public function switchApcToUP()
    {
        self::$_callbackApc = null;
        self::$_callbackUploadProgress = array('Zend_File_Transfer_Adapter_HttpTest_MockAdapter', 'uPTest');
    }
}

// Call Zend_File_Transfer_Adapter_HttpTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == "Zend_File_Transfer_Adapter_HttpTest::main") {
    Zend_File_Transfer_Adapter_HttpTest::main();
}
