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
 * @package    Zend_Service_LiveDocx
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: $
 */


if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_Service_LiveDocx_MailMergeTest::main');
}


/**
 * Zend_Service_LiveDocx test case
 *
 * @category   Zend
 * @package    Zend_Service_LiveDocx
 * @subpackage UnitTests
 * @group      Zend_Service
 * @group      Zend_Service_LiveDocx
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: $
 */
class Zend_Service_LiveDocx_MailMergeTest extends PHPUnit_Framework_TestCase
{
    const TEST_TEMPLATE_1 = 'phpunit-template.docx';
    const TEST_TEMPLATE_2 = 'phpunit-template-block-fields.doc';
    const ENDPOINT = 'https://api.livedocx.com/1.2/mailmerge.asmx?wsdl';

    public $path;
    public $phpLiveDocx;

    // -------------------------------------------------------------------------

    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite(__CLASS__);
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    public function setUp()
    {
        if (!constant('TESTS_ZEND_SERVICE_LIVEDOCX_USERNAME')
            || !constant('TESTS_ZEND_SERVICE_LIVEDOCX_PASSWORD')
        ) {
            $this->markTestSkipped('LiveDocx tests disabled');
            return;
        }
        
        $this->phpLiveDocx = new Zend_Service_LiveDocx_MailMerge();
        $this->phpLiveDocx->setUsername(TESTS_ZEND_SERVICE_LIVEDOCX_USERNAME)
                          ->setPassword(TESTS_ZEND_SERVICE_LIVEDOCX_PASSWORD);

        foreach($this->phpLiveDocx->listTemplates() as $template) {
            $this->phpLiveDocx->deleteTemplate($template['filename']);
        }

        $this->path = realpath(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'MailMerge');
    }

    public function tearDown()
    {
	if (isset($this->phpLiveDocx)) {
	    foreach($this->phpLiveDocx->listTemplates() as $template) {
		$this->phpLiveDocx->deleteTemplate($template['filename']);
	    }

	    unset($this->phpLiveDocx);
	}
    }

    // -------------------------------------------------------------------------

    public function testLoginUsernamePassword()
    {
        $phpLiveDocx = new Zend_Service_LiveDocx_MailMerge();
        $phpLiveDocx->setUsername(TESTS_ZEND_SERVICE_LIVEDOCX_USERNAME);
        $phpLiveDocx->setPassword(TESTS_ZEND_SERVICE_LIVEDOCX_PASSWORD);
        $this->assertTrue($phpLiveDocx->logIn()); 
    }
    
    public function testLoginUsernamePasswordSoapClient()
    {
        $phpLiveDocx = new Zend_Service_LiveDocx_MailMerge();
        $phpLiveDocx->setUsername(TESTS_ZEND_SERVICE_LIVEDOCX_USERNAME);
        $phpLiveDocx->setPassword(TESTS_ZEND_SERVICE_LIVEDOCX_PASSWORD);
        $phpLiveDocx->setSoapClient(new Zend_Soap_Client(self::ENDPOINT));
        $this->assertTrue($phpLiveDocx->logIn()); 
    }    
        
    /**
     * @expectedException Zend_Service_LiveDocx_Exception
     */
    public function testLoginUsernamePasswordException()
    {
        $phpLiveDocx = new Zend_Service_LiveDocx_MailMerge();
        $phpLiveDocx->setUsername('phpunitInvalidUsername');
        $phpLiveDocx->setPassword('phpunitInvalidPassword');
        $phpLiveDocx->logIn();
    }
    
    /**
     * @expectedException Zend_Service_LiveDocx_Exception
     */
    public function testLoginUsernamePasswordSoapClientException()
    {
        $phpLiveDocx = new Zend_Service_LiveDocx_MailMerge();
        $phpLiveDocx->setUsername('phpunitInvalidUsername');
        $phpLiveDocx->setPassword('phpunitInvalidPassword');
        $phpLiveDocx->setSoapClient(new Zend_Soap_Client(self::ENDPOINT));
        $phpLiveDocx->logIn();
    }    
    
    public function testConstructorOptionsUsernamePassword()
    {    
        $phpLiveDocx = new Zend_Service_LiveDocx_MailMerge(
            array (
                'username' => TESTS_ZEND_SERVICE_LIVEDOCX_USERNAME,
                'password' => TESTS_ZEND_SERVICE_LIVEDOCX_PASSWORD
            )
        );   
        $this->assertTrue($phpLiveDocx->logIn()); 
    }
    
    public function testConstructorOptionsUsernamePasswordSoapClient()
    {    
        $phpLiveDocx = new Zend_Service_LiveDocx_MailMerge(
            array (
                'username' => TESTS_ZEND_SERVICE_LIVEDOCX_USERNAME,
                'password' => TESTS_ZEND_SERVICE_LIVEDOCX_PASSWORD,
                'soapClient' => new Zend_Soap_Client(self::ENDPOINT)
            )
        );   
        $this->assertTrue($phpLiveDocx->logIn()); 
    }    

    // -------------------------------------------------------------------------

    public function testSetLocalTemplate()
    {
        $this->assertTrue(is_a($this->phpLiveDocx->setLocalTemplate($this->path . DIRECTORY_SEPARATOR . self::TEST_TEMPLATE_1), 'Zend_Service_LiveDocx_MailMerge'));
        $this->setExpectedException('Zend_Service_LiveDocx_Exception');
        @$this->phpLiveDocx->setLocalTemplate('phpunit-nonexistent.doc');
    }

    public function testSetRemoteTemplate()
    {
        $this->phpLiveDocx->uploadTemplate($this->path . DIRECTORY_SEPARATOR . self::TEST_TEMPLATE_1);
        $this->assertTrue(is_a($this->phpLiveDocx->setRemoteTemplate(self::TEST_TEMPLATE_1), 'Zend_Service_LiveDocx_MailMerge'));
        $this->phpLiveDocx->deleteTemplate(self::TEST_TEMPLATE_1);
    }

    public function testSetFieldValues()
    {
        $testValues = array('software' => 'phpunit');

        // Remote Template
        $this->phpLiveDocx->uploadTemplate($this->path . DIRECTORY_SEPARATOR . self::TEST_TEMPLATE_1);
        $this->phpLiveDocx->setRemoteTemplate(self::TEST_TEMPLATE_1);
        $this->assertTrue(is_a($this->phpLiveDocx->setFieldValues($testValues), 'Zend_Service_LiveDocx_MailMerge'));
        $this->phpLiveDocx->deleteTemplate(self::TEST_TEMPLATE_1);

        // Local Template
        $this->phpLiveDocx->setLocalTemplate($this->path . DIRECTORY_SEPARATOR . self::TEST_TEMPLATE_1);
        $this->assertTrue(is_a($this->phpLiveDocx->setFieldValues($testValues), 'Zend_Service_LiveDocx_MailMerge'));
    }

    public function testSetFieldValue()
    {
        $testKey   = 'software';
        $testValue = 'phpunit';

        // Remote Template
        $this->phpLiveDocx->uploadTemplate($this->path . DIRECTORY_SEPARATOR . self::TEST_TEMPLATE_1);
        $this->assertTrue(is_a($this->phpLiveDocx->setFieldValue($testKey, $testValue), 'Zend_Service_LiveDocx_MailMerge'));
        $this->phpLiveDocx->deleteTemplate(self::TEST_TEMPLATE_1);

        // Local Template
        $this->phpLiveDocx->setLocalTemplate($this->path . DIRECTORY_SEPARATOR . self::TEST_TEMPLATE_1);
        $this->assertTrue(is_a($this->phpLiveDocx->setFieldValue($testKey, $testValue), 'Zend_Service_LiveDocx_MailMerge'));
    }

    public function testAssign()
    {
        $testKey   = 'software';
        $testValue = 'phpunit';

        // Remote Template
        $this->phpLiveDocx->uploadTemplate($this->path . DIRECTORY_SEPARATOR . self::TEST_TEMPLATE_1);
        $this->assertTrue(is_a($this->phpLiveDocx->assign($testKey, $testValue), 'Zend_Service_LiveDocx_MailMerge'));
        $this->phpLiveDocx->deleteTemplate(self::TEST_TEMPLATE_1);

        // Local Template
        $this->phpLiveDocx->setLocalTemplate($this->path . DIRECTORY_SEPARATOR . self::TEST_TEMPLATE_1);
        $this->assertTrue(is_a($this->phpLiveDocx->assign($testKey, $testValue), 'Zend_Service_LiveDocx_MailMerge'));
    }

    public function testSetBlockFieldValues()
    {
        $testKey    = 'connection';
        $testValues = array(array('connection_number' => 'unittest', 'connection_duration' => 'unittest', 'fee' => 'unittest'),
                            array('connection_number' => 'unittest', 'connection_duration' => 'unittest', 'fee' => 'unittest'),
                            array('connection_number' => 'unittest', 'connection_duration' => 'unittest', 'fee' => 'unittest'),
                            array('connection_number' => 'unittest', 'connection_duration' => 'unittest', 'fee' => 'unittest') );

        // Remote Template
        $this->phpLiveDocx->uploadTemplate($this->path . DIRECTORY_SEPARATOR . self::TEST_TEMPLATE_2);
        $this->assertTrue(is_a($this->phpLiveDocx->setBlockFieldValues($testKey, $testValues), 'Zend_Service_LiveDocx_MailMerge'));
        $this->phpLiveDocx->deleteTemplate(self::TEST_TEMPLATE_2);

        // Local Template
        $this->phpLiveDocx->setLocalTemplate($this->path . DIRECTORY_SEPARATOR . self::TEST_TEMPLATE_2);
        $this->assertTrue(is_a($this->phpLiveDocx->setBlockFieldValues($testKey, $testValues), 'Zend_Service_LiveDocx_MailMerge'));
    }

    // -------------------------------------------------------------------------

    public function testCreateDocument()
    {
        $testValues = array(
            'software' => 'phpunit',
            'licensee' => 'phpunit',
            'company'  => 'phpunit',
            'date'     => 'phpunit',
            'time'     => 'phpunit',
            'city'     => 'phpunit',
            'country'  => 'phpunit',
        );

        // Remote Template
        $this->phpLiveDocx->uploadTemplate($this->path . DIRECTORY_SEPARATOR . self::TEST_TEMPLATE_1);
        $this->phpLiveDocx->setRemoteTemplate(self::TEST_TEMPLATE_1);
        $this->phpLiveDocx->assign($testValues);
        $this->assertNull($this->phpLiveDocx->createDocument());
        $this->phpLiveDocx->deleteTemplate(self::TEST_TEMPLATE_1);

        // Local Template
        $this->phpLiveDocx->setLocalTemplate($this->path . DIRECTORY_SEPARATOR . self::TEST_TEMPLATE_1);
        $this->phpLiveDocx->assign($testValues);
        $this->assertNull($this->phpLiveDocx->createDocument());
    }

    public function testRetrieveDocument()
    {
        $testValues = array(
            'software' => 'phpunit',
            'licensee' => 'phpunit',
            'company'  => 'phpunit',
            'date'     => 'phpunit',
            'time'     => 'phpunit',
            'city'     => 'phpunit',
            'country'  => 'phpunit',
        );

        // PDF and DOCs are always slightly different:
        // - PDF because of the timestamp in meta data
        // - DOC because of ???

        $expectedResults = array(
            'docx' => '50fe2abd9b42e67c3126d355768b6e75',
            'rtf'  => '24f950ff620ba194fe5900c3a5360570',
            'txd'  => '22d7a7558b19ba8be9fe03b35068cf20',
            'txt'  => '3dc103f033ef6efba770c8196059d96d',
            'html' => '8b91dc8617651b6e3142d0716c0f616a',
        );

        // Remote Template
        $this->phpLiveDocx->uploadTemplate($this->path . DIRECTORY_SEPARATOR . self::TEST_TEMPLATE_1);
        $this->phpLiveDocx->setRemoteTemplate(self::TEST_TEMPLATE_1);
        $this->phpLiveDocx->assign($testValues);
        $this->phpLiveDocx->createDocument();
        foreach($expectedResults as $format => $hash) {
            $document = $this->phpLiveDocx->retrieveDocument($format);
            $this->assertEquals($hash, md5($document));
        }
        $this->phpLiveDocx->deleteTemplate(self::TEST_TEMPLATE_1);

        // Local Template
        $this->phpLiveDocx->setLocalTemplate($this->path . DIRECTORY_SEPARATOR . self::TEST_TEMPLATE_1);
        $this->phpLiveDocx->assign($testValues);
        $this->phpLiveDocx->createDocument();
        foreach($expectedResults as $format => $hash) {
            $document = $this->phpLiveDocx->retrieveDocument($format);
            $this->assertEquals($hash, md5($document));
        }
    }

    public function testRetrieveDocumentAppended()
    {
        $testValues = array(
            array(
                'software' => 'phpunit - document 1',
                'licensee' => 'phpunit - document 1',
                'company'  => 'phpunit - document 1',
                'date'     => 'phpunit - document 1',
                'time'     => 'phpunit - document 1',
                'city'     => 'phpunit - document 1',
                'country'  => 'phpunit - document 1',
            ),
            array(
                'software' => 'phpunit - document 2',
                'licensee' => 'phpunit - document 2',
                'company'  => 'phpunit - document 2',
                'date'     => 'phpunit - document 2',
                'time'     => 'phpunit - document 2',
                'city'     => 'phpunit - document 2',
                'country'  => 'phpunit - document 2',
            ),
        );

        // PDF and DOCs are always slightly different:
        // - PDF because of the timestamp in meta data
        // - DOC because of ???
        $expectedResults = array(
            'docx' => '0697e57da0c886dee9fa2d5c98335121',
            'rtf'  => '9a3f448519e2be0da08a13702fd9d48b',
            'txd'  => 'f76a6575e74db5b15b4c4be76157bc03',
            'txt'  => 'e997415fd0d5e766b2490fed9386da21',
            'html' => '2dfafbb8f81281dbbae99e131963cd50',
        );

        // Remote Template
        $this->phpLiveDocx->uploadTemplate($this->path . DIRECTORY_SEPARATOR . self::TEST_TEMPLATE_1);
        $this->phpLiveDocx->setRemoteTemplate(self::TEST_TEMPLATE_1);
        $this->phpLiveDocx->assign($testValues);
        $this->phpLiveDocx->createDocument();
        foreach($expectedResults as $format => $hash) {
            $document = $this->phpLiveDocx->retrieveDocument($format);
            $this->assertEquals($hash, md5($document));
        }
        $this->phpLiveDocx->deleteTemplate(self::TEST_TEMPLATE_1);

        // Local Template
        $this->phpLiveDocx->setLocalTemplate($this->path . DIRECTORY_SEPARATOR . self::TEST_TEMPLATE_1);
        $this->phpLiveDocx->assign($testValues);
        $this->phpLiveDocx->createDocument();
        foreach($expectedResults as $format => $hash) {
            $document = $this->phpLiveDocx->retrieveDocument($format);
            $this->assertEquals($hash, md5($document));
        }
    }

    // -------------------------------------------------------------------------
    
    public function testGetTemplateFormats()
    {
        $expectedResults = array('doc' , 'docx' , 'rtf' , 'txd');
        $this->assertEquals($expectedResults, $this->phpLiveDocx->getTemplateFormats());
    }

    public function testGetDocumentFormats()
    {
        $expectedResults = array('doc' , 'docx' , 'html' , 'pdf' , 'rtf' , 'txd' , 'txt');
        $this->assertEquals($expectedResults, $this->phpLiveDocx->getDocumentFormats());
    }

    public function testGetImageFormats()
    {
        $expectedResults = array('bmp' , 'gif' , 'jpg' , 'png' , 'tiff');
        $this->assertEquals($expectedResults, $this->phpLiveDocx->getImageFormats());
    }
   
    // -------------------------------------------------------------------------

    public function testGetBitmaps()
    {
        $testValues = array(
            'software' => 'phpunit',
            'licensee' => 'phpunit',
            'company'  => 'phpunit',
            'date'     => 'phpunit',
            'time'     => 'phpunit',
            'city'     => 'phpunit',
            'country'  => 'phpunit',
        );

        $expectedResults = array(
            'bmp'  => 'c588ee10d63e0598fe3541a032f509d6',
            'gif'  => '2edd4066dda5f4c2049717137d76cf58',
            'jpg'  => '8766618c572f19ceccc39af7ad0c8478',
            'png'  => '1e12e4937b9ccb0fa6d78dcd342b7f28',
            'tiff' => '014ae48643e3a50f691b7d9442605426',
        );

        $this->phpLiveDocx->setLocalTemplate($this->path . DIRECTORY_SEPARATOR . self::TEST_TEMPLATE_1);
        $this->phpLiveDocx->assign($testValues);
        $this->phpLiveDocx->createDocument();
        foreach($this->phpLiveDocx->getImageFormats() as $format) {
            $bitmaps = $this->phpLiveDocx->getBitmaps(1, 1, 20, $format);
            $this->assertEquals($expectedResults[$format], md5(serialize($bitmaps)));
        }
    }

    public function testGetAllBitmaps()
    {
        $testValues = array(
            'software' => 'phpunit',
            'licensee' => 'phpunit',
            'company'  => 'phpunit',
            'date'     => 'phpunit',
            'time'     => 'phpunit',
            'city'     => 'phpunit',
            'country'  => 'phpunit',
        );

        $expectedResults = array(
            'bmp'  => '0ae732498dd3798fc51c1ccccd09e3e3',
            'gif'  => '9a5f7bfa2aafd8b99f6955b8bdbb8bf7',
            'jpg'  => '38550446bfc84af3ddd1a0f3339a84dd',
            'png'  => 'a3b5517bb118db67b8a8259652a389c2',
            'tiff' => 'b49aa783c14bc7f07776d816085894a3',
        );

        $this->phpLiveDocx->setLocalTemplate($this->path . DIRECTORY_SEPARATOR . self::TEST_TEMPLATE_1);
        $this->phpLiveDocx->assign($testValues);
        $this->phpLiveDocx->createDocument();
        foreach($this->phpLiveDocx->getImageFormats() as $format) {
            $bitmaps = $this->phpLiveDocx->getAllBitmaps(20, $format);
            $this->assertEquals($expectedResults[$format], md5(serialize($bitmaps)));
        }
    }

    public function testGetFontNames()
    {
        $fonts = $this->phpLiveDocx->getFontNames();
        if (is_array($fonts) && count($fonts) > 5) {
            foreach (array('Courier New' , 'Verdana' , 'Arial' , 'Times New Roman') as $font) {
                if (in_array($font, $fonts)) {
                    $this->assertTrue(true);
                } else {
                    $this->assertTrue(false);
                }
            }
        } else {
            $this->assertTrue(false);
        }
    }

    public function testGetFieldNames()
    {
        $expectedResults = array('phone', 'date', 'name', 'customer_number', 'invoice_number', 'account_number', 'service_phone', 'service_fax', 'month', 'monthly_fee', 'total_net', 'tax', 'tax_value', 'total');

        // Remote Template
        $this->phpLiveDocx->uploadTemplate($this->path . DIRECTORY_SEPARATOR . self::TEST_TEMPLATE_2);
        $this->phpLiveDocx->setRemoteTemplate(self::TEST_TEMPLATE_2);
        $this->assertEquals($expectedResults, $this->phpLiveDocx->getFieldNames());
        $this->phpLiveDocx->deleteTemplate(self::TEST_TEMPLATE_2);

        // Local Template
        $this->phpLiveDocx->setLocalTemplate($this->path . DIRECTORY_SEPARATOR . self::TEST_TEMPLATE_2);
        $this->assertEquals($expectedResults, $this->phpLiveDocx->getFieldNames());
    }

    public function testGetBlockFieldNames()
    {
        $expectedResults = array('connection_number', 'connection_duration', 'fee');

        // Remote Template
        $this->phpLiveDocx->uploadTemplate($this->path . DIRECTORY_SEPARATOR . self::TEST_TEMPLATE_2);
        $this->phpLiveDocx->setRemoteTemplate(self::TEST_TEMPLATE_2);
        $this->assertEquals($expectedResults, $this->phpLiveDocx->getBlockFieldNames('connection'));
        $this->phpLiveDocx->deleteTemplate(self::TEST_TEMPLATE_2);

        // Local Template
        $this->phpLiveDocx->setLocalTemplate($this->path . DIRECTORY_SEPARATOR . self::TEST_TEMPLATE_2);
        $this->assertEquals($expectedResults, $this->phpLiveDocx->getBlockFieldNames('connection'));
    }

    public function testGetBlockNames()
    {
        $expectedResults = array('connection');

        // Remote Template
        $this->phpLiveDocx->uploadTemplate($this->path . DIRECTORY_SEPARATOR . self::TEST_TEMPLATE_2);
        $this->phpLiveDocx->setRemoteTemplate(self::TEST_TEMPLATE_2);
        $this->assertEquals($expectedResults, $this->phpLiveDocx->getBlockNames());
        $this->phpLiveDocx->deleteTemplate(self::TEST_TEMPLATE_2);

        // Local Template
        $this->phpLiveDocx->setLocalTemplate($this->path . DIRECTORY_SEPARATOR . self::TEST_TEMPLATE_2);
        $this->assertEquals($expectedResults, $this->phpLiveDocx->getBlockNames());
    }

    // -------------------------------------------------------------------------

    public function testUploadTemplate()
    {
        $this->phpLiveDocx->deleteTemplate(self::TEST_TEMPLATE_2);
        $this->assertNull($this->phpLiveDocx->uploadTemplate($this->path . DIRECTORY_SEPARATOR . self::TEST_TEMPLATE_2));
        $this->phpLiveDocx->deleteTemplate(self::TEST_TEMPLATE_2);
    }

    public function testDownloadTemplate()
    {
        $expectedResults = '2f076af778ca5f8afc9661cfb9deb7c6';
        $this->phpLiveDocx->uploadTemplate($this->path . DIRECTORY_SEPARATOR . self::TEST_TEMPLATE_2);
        $template = $this->phpLiveDocx->downloadTemplate(self::TEST_TEMPLATE_2);
        $this->assertEquals($expectedResults, md5($template));
    }

    public function testDeleteTemplate()
    {
        $this->phpLiveDocx->uploadTemplate($this->path . DIRECTORY_SEPARATOR . self::TEST_TEMPLATE_2);
        $this->phpLiveDocx->deleteTemplate(self::TEST_TEMPLATE_2);
        $templateDeleted = true;
        foreach($this->phpLiveDocx->listTemplates() as $template) {
            if($template['filename'] == self::TEST_TEMPLATE_2) {
                $templateDeleted = false;
            }
        }
        $this->assertTrue($templateDeleted);
    }

    public function testListTemplates()
    {
        $this->phpLiveDocx->uploadTemplate($this->path . DIRECTORY_SEPARATOR . self::TEST_TEMPLATE_1);
        $this->phpLiveDocx->uploadTemplate($this->path . DIRECTORY_SEPARATOR . self::TEST_TEMPLATE_2);

        // Where templates uploaded and are being listed?
        $testTemplate1Exists = false;
        $testTemplate2Exists = false;

        $templates = $this->phpLiveDocx->listTemplates();
        foreach($templates as $template) {
            if(self::TEST_TEMPLATE_1 === $template['filename']) {
                $testTemplate1Exists = true;
            } elseif(self::TEST_TEMPLATE_2 === $template['filename']) {
                $testTemplate2Exists = true;
            }
        }
        $this->assertTrue($testTemplate1Exists && $testTemplate2Exists);

        // Is all info about templates available?
        $expectedResults = array('filename', 'fileSize', 'createTime', 'modifyTime');
        foreach($templates as $template) {
            $this->assertEquals($expectedResults, array_keys($template));
        }

        // Is all info about templates correct?
        foreach($templates as $template) {
            $this->assertTrue(strlen($template['filename']) > 0);
            $this->assertTrue($template['fileSize'] > 1);
            $this->assertTrue($template['createTime'] > mktime(0, 0, 0, 1, 1, 1980));
            $this->assertTrue($template['modifyTime'] > mktime(0, 0, 0, 1, 1, 1980));
        }

        $this->phpLiveDocx->deleteTemplate(self::TEST_TEMPLATE_1);
        $this->phpLiveDocx->deleteTemplate(self::TEST_TEMPLATE_2);
    }

    public function testTemplateExists()
    {
        $this->phpLiveDocx->uploadTemplate($this->path . DIRECTORY_SEPARATOR . self::TEST_TEMPLATE_2);
        $this->assertTrue($this->phpLiveDocx->templateExists(self::TEST_TEMPLATE_2));
        $this->phpLiveDocx->deleteTemplate(self::TEST_TEMPLATE_2);
    }

    // -------------------------------------------------------------------------

    public function testAssocArrayToArrayOfArrayOfString()
    {
        $testValues = array(
            'a' => '1',
            'b' => '2',
            'c' => '3',
        );

        $expectedResults = array(
            array('a', 'b', 'c'),
            array('1', '2', '3'),
        );

        $actualResults = Zend_Service_LiveDocx_MailMerge::assocArrayToArrayOfArrayOfString($testValues);
        $this->assertEquals($expectedResults, $actualResults);
    }

    public function testMultiAssocArrayToArrayOfArrayOfString()
    {
        $testValues = array(
            array(
                'a' => '1',
                'b' => '2',
                'c' => '3',
            ),
            array(
                'a' => '4',
                'b' => '5',
                'c' => '6',
            ),
            array(
                'a' => '7',
                'b' => '8',
                'c' => '9',
            ),
        );

        $expectedResults = array(
            array('a', 'b', 'c'),
            array('1', '2', '3'),
            array('4', '5', '6'),
            array('7', '8', '9'),
        );
        $actualResults = Zend_Service_LiveDocx_MailMerge::multiAssocArrayToArrayOfArrayOfString($testValues);
        $this->assertEquals($expectedResults, $actualResults);
    }
}

if (PHPUnit_MAIN_METHOD == 'Zend_Service_LiveDocx_MailMergeTest::main') {
    Zend_Service_LiveDocx_MailMergeTest::main();
}
