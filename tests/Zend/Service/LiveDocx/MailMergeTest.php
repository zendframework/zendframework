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
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Service;
namespace Zend\Service\LiveDocx;

use Zend\Soap\Client as SoapClient;


/**
 * Zend_Service_LiveDocx test case
 *
 * @category   Zend
 * @package    Zend_Service_LiveDocx
 * @subpackage UnitTests
 * @group      Zend_Service
 * @group      Zend_Service_LiveDocx
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class MailMergeTest extends \PHPUnit_Framework_TestCase
{
    const TEST_TEMPLATE_1 = 'phpunit-template.docx';
    const TEST_TEMPLATE_2 = 'phpunit-template-block-fields.doc';

    const TEST_IMAGE_1    = 'image-01.png';
    const TEST_IMAGE_2    = 'image-02.png';
    
    const ENDPOINT        = 'https://api.livedocx.com/2.0/mailmerge.asmx?wsdl';

    public $_path;
    public $_mailMerge;

    // -------------------------------------------------------------------------

    public function setUp()
    {
        if (!constant('TESTS_ZEND_SERVICE_LIVEDOCX_USERNAME') ||
                !constant('TESTS_ZEND_SERVICE_LIVEDOCX_PASSWORD')) {
            $this->markTestSkipped('LiveDocx tests disabled');
            return true;
        }
        
        $this->_mailMerge = new MailMerge();
        $this->_mailMerge->setUsername(TESTS_ZEND_SERVICE_LIVEDOCX_USERNAME)
                         ->setPassword(TESTS_ZEND_SERVICE_LIVEDOCX_PASSWORD);

        foreach($this->_mailMerge->listTemplates() as $template) {
            $this->_mailMerge->deleteTemplate($template['filename']);
        }

        $this->_path = realpath(__DIR__ . DIRECTORY_SEPARATOR . 'MailMerge');
    }

    public function tearDown()
    {
        if (isset($this->_mailMerge)) {
            foreach ($this->_mailMerge->listTemplates() as $template) {
                $this->_mailMerge->deleteTemplate($template['filename']);
            }
            unset($this->_mailMerge);
        }
    }

    // -------------------------------------------------------------------------

    public function testMissingUsername()
    {
        $_mailMerge = new MailMerge();

        try {
            $_mailMerge->logIn();
            $this->fail('exception expected');
        } catch (\Zend\Service\LiveDocx\Exception\InvalidArgumentException $e) {}

        unset($_mailMerge);
    }

    public function testMissingPassword()
    {
        $_mailMerge = new MailMerge();
        $_mailMerge->setUsername(TESTS_ZEND_SERVICE_LIVEDOCX_USERNAME);

        try {
            $_mailMerge->logIn();
            $this->fail('exception expected');
        } catch (\Zend\Service\LiveDocx\Exception\InvalidArgumentException $e) {}

        unset($_mailMerge);
    }

    public function testInvalidCredentials()
    {
        $_mailMerge = new MailMerge();
        $_mailMerge->setUsername(TESTS_ZEND_SERVICE_LIVEDOCX_USERNAME)
                   ->setPassword('invalid-password');
        
        try {
            $_mailMerge->logIn();
            $this->fail('exception expected');
        } catch (\Zend\Service\LiveDocx\Exception\RuntimeException $e) {}

        unset($_mailMerge);

        $_mailMerge = new MailMerge();
        $_mailMerge->setUsername('invalid-username')
                   ->setPassword(TESTS_ZEND_SERVICE_LIVEDOCX_PASSWORD);

        try {
            $_mailMerge->logIn();
            $this->fail('exception expected');
        } catch (\Zend\Service\LiveDocx\Exception\RuntimeException $e) {}

        unset($_mailMerge);
    }

    public function testWsdlHttpFileNotFound()
    {
        $_mailMerge = new MailMerge();
        $_mailMerge->setUsername(TESTS_ZEND_SERVICE_LIVEDOCX_USERNAME)
                   ->setPassword(TESTS_ZEND_SERVICE_LIVEDOCX_PASSWORD)
                   ->setWsdl('http://www.livedocx.com/file-not-found.wsdl');

        try {
            $_mailMerge->logIn();
            $this->fail('exception expected');
        } catch (\Zend\Service\LiveDocx\Exception\RuntimeException $e) {}

        $this->assertFalse($_mailMerge->logOut());

        unset($_mailMerge);
    }

    // -------------------------------------------------------------------------

    
    public function testSetLocalTemplateNotOnLocalFileSystem()
    {
        try {
            $this->_mailMerge->setLocalTemplate('invalid-template'); 
            $this->fail('exception expected');
        } catch (\Zend\Service\LiveDocx\Exception\InvalidArgumentException $e) {}
    }

    public function testSetRemoteTemplateNotOnRemoteFileSystem()
    {
        try {
            $this->_mailMerge->setRemoteTemplate('invalid-template');
            $this->fail('exception expected');
        } catch (\Zend\Service\LiveDocx\Exception\RuntimeException $e) {}
        
    }

    public function testUploadTemplateNotOnLocalFileSystem()
    {
        try {
            $this->_mailMerge->uploadTemplate('invalid-template');
            $this->fail('exception expected');
        } catch (\Zend\Service\LiveDocx\Exception\InvalidArgumentException $e) {}
    }

    // -------------------------------------------------------------------------

    public function testLoginUsernamePassword()
    {
        $_mailMerge = new MailMerge();

        $_mailMerge->setUsername(TESTS_ZEND_SERVICE_LIVEDOCX_USERNAME)
                   ->setPassword(TESTS_ZEND_SERVICE_LIVEDOCX_PASSWORD);

        $this->assertTrue($_mailMerge->logIn());

        unset($_mailMerge);
    }

    public function testLoginUsernamePasswordSoapClient()
    {
        $_mailMerge = new MailMerge();

        $_mailMerge->setUsername(TESTS_ZEND_SERVICE_LIVEDOCX_USERNAME)
                   ->setPassword(TESTS_ZEND_SERVICE_LIVEDOCX_PASSWORD)
                   ->setSoapClient(new SoapClient(self::ENDPOINT));

        $this->assertTrue($_mailMerge->logIn());

        unset($_mailMerge);
    }

    public function testConstructorOptionsUsernamePassword()
    {
        $_mailMerge = new MailMerge(
            array (
                'username' => TESTS_ZEND_SERVICE_LIVEDOCX_USERNAME,
                'password' => TESTS_ZEND_SERVICE_LIVEDOCX_PASSWORD
            )
        );

        $this->assertTrue($_mailMerge->logIn());

        unset($_mailMerge);
    }

    public function testConstructorOptionsUsernamePasswordSoapClient()
    {
        $_mailMerge = new MailMerge(
            array (
                'username'   => TESTS_ZEND_SERVICE_LIVEDOCX_USERNAME,
                'password'   => TESTS_ZEND_SERVICE_LIVEDOCX_PASSWORD,
                'soapClient' => new SoapClient(self::ENDPOINT)
            )
        );

        $this->assertTrue($_mailMerge->logIn());

        unset($_mailMerge);
    }

    // -------------------------------------------------------------------------

    public function testSetLocalTemplate()
    {
        $this->assertTrue(is_a($this->_mailMerge->setLocalTemplate($this->_path . DIRECTORY_SEPARATOR . self::TEST_TEMPLATE_1), '\Zend\Service\LiveDocx\MailMerge'));
        $this->setExpectedException('\Zend\Service\LiveDocx\Exception');
        @$this->_mailMerge->setLocalTemplate('phpunit-nonexistent.doc');
    }

    public function testSetRemoteTemplate()
    {
        $this->_mailMerge->uploadTemplate($this->_path . DIRECTORY_SEPARATOR . self::TEST_TEMPLATE_1);
        $this->assertTrue(is_a($this->_mailMerge->setRemoteTemplate(self::TEST_TEMPLATE_1), '\Zend\Service\LiveDocx\MailMerge'));
        $this->_mailMerge->deleteTemplate(self::TEST_TEMPLATE_1);
    }
    
    public function testSetFieldValues()
    {
        $testValues = array('software' => 'phpunit');

        // Remote Template
        $this->_mailMerge->uploadTemplate($this->_path . DIRECTORY_SEPARATOR . self::TEST_TEMPLATE_1);
        $this->_mailMerge->setRemoteTemplate(self::TEST_TEMPLATE_1);
        $this->assertTrue(is_a($this->_mailMerge->setFieldValues($testValues), '\Zend\Service\LiveDocx\MailMerge'));
        $this->_mailMerge->deleteTemplate(self::TEST_TEMPLATE_1);

        // Local Template
        $this->_mailMerge->setLocalTemplate($this->_path . DIRECTORY_SEPARATOR . self::TEST_TEMPLATE_1);
        $this->assertTrue(is_a($this->_mailMerge->setFieldValues($testValues), '\Zend\Service\LiveDocx\MailMerge'));
    }

    public function testSetFieldValue()
    {
        $testKey   = 'software';
        $testValue = 'phpunit';

        // Remote Template
        $this->_mailMerge->uploadTemplate($this->_path . DIRECTORY_SEPARATOR . self::TEST_TEMPLATE_1);
        $this->assertTrue(is_a($this->_mailMerge->setFieldValue($testKey, $testValue), '\Zend\Service\LiveDocx\MailMerge'));
        $this->_mailMerge->deleteTemplate(self::TEST_TEMPLATE_1);

        // Local Template
        $this->_mailMerge->setLocalTemplate($this->_path . DIRECTORY_SEPARATOR . self::TEST_TEMPLATE_1);
        $this->assertTrue(is_a($this->_mailMerge->setFieldValue($testKey, $testValue), '\Zend\Service\LiveDocx\MailMerge'));
    }

    public function testAssign()
    {
        $testKey   = 'software';
        $testValue = 'phpunit';

        // Remote Template
        $this->_mailMerge->uploadTemplate($this->_path . DIRECTORY_SEPARATOR . self::TEST_TEMPLATE_1);
        $this->assertTrue(is_a($this->_mailMerge->assign($testKey, $testValue), '\Zend\Service\LiveDocx\MailMerge'));
        $this->_mailMerge->deleteTemplate(self::TEST_TEMPLATE_1);

        // Local Template
        $this->_mailMerge->setLocalTemplate($this->_path . DIRECTORY_SEPARATOR . self::TEST_TEMPLATE_1);
        $this->assertTrue(is_a($this->_mailMerge->assign($testKey, $testValue), '\Zend\Service\LiveDocx\MailMerge'));
    }

    public function testSetBlockFieldValues()
    {
        $testKey    = 'connection';
        $testValues = array(array('connection_number' => 'unittest', 'connection_duration' => 'unittest', 'fee' => 'unittest'),
                            array('connection_number' => 'unittest', 'connection_duration' => 'unittest', 'fee' => 'unittest'),
                            array('connection_number' => 'unittest', 'connection_duration' => 'unittest', 'fee' => 'unittest'),
                            array('connection_number' => 'unittest', 'connection_duration' => 'unittest', 'fee' => 'unittest') );

        // Remote Template
        $this->_mailMerge->uploadTemplate($this->_path . DIRECTORY_SEPARATOR . self::TEST_TEMPLATE_2);
        $this->assertTrue(is_a($this->_mailMerge->setBlockFieldValues($testKey, $testValues), '\Zend\Service\LiveDocx\MailMerge'));
        $this->_mailMerge->deleteTemplate(self::TEST_TEMPLATE_2);

        // Local Template
        $this->_mailMerge->setLocalTemplate($this->_path . DIRECTORY_SEPARATOR . self::TEST_TEMPLATE_2);
        $this->assertTrue(is_a($this->_mailMerge->setBlockFieldValues($testKey, $testValues), '\Zend\Service\LiveDocx\MailMerge'));
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
            'country'  => 'phpunit'
        );

        // Remote Template
        $this->_mailMerge->uploadTemplate($this->_path . DIRECTORY_SEPARATOR . self::TEST_TEMPLATE_1);
        $this->_mailMerge->setRemoteTemplate(self::TEST_TEMPLATE_1);
        $this->_mailMerge->assign($testValues);
        $this->assertNull($this->_mailMerge->createDocument());
        $this->_mailMerge->deleteTemplate(self::TEST_TEMPLATE_1);

        // Local Template
        $this->_mailMerge->setLocalTemplate($this->_path . DIRECTORY_SEPARATOR . self::TEST_TEMPLATE_1);
        $this->_mailMerge->assign($testValues);
        $this->assertNull($this->_mailMerge->createDocument());
    }

    public function testRetrieveDocument()
    {
        $formats = array('doc', 'docx', 'html', 'pdf', 'rtf', 'txd', 'txt');

        $testValues = array(
            'software' => 'phpunit',
            'licensee' => 'phpunit',
            'company'  => 'phpunit',
            'date'     => 'phpunit',
            'time'     => 'phpunit',
            'city'     => 'phpunit',
            'country'  => 'phpunit'
        );

        // Remote Template
        $this->_mailMerge->uploadTemplate($this->_path . DIRECTORY_SEPARATOR . self::TEST_TEMPLATE_1);
        $this->_mailMerge->setRemoteTemplate(self::TEST_TEMPLATE_1);
        $this->_mailMerge->assign($testValues);
        $this->_mailMerge->createDocument();
        foreach ($formats as $format) {
            $document = $this->_mailMerge->retrieveDocument($format);
            $this->assertGreaterThan(2048, strlen($document));
        }
        $this->_mailMerge->deleteTemplate(self::TEST_TEMPLATE_1);

        // Local Template
        $this->_mailMerge->setLocalTemplate($this->_path . DIRECTORY_SEPARATOR . self::TEST_TEMPLATE_1);
        $this->_mailMerge->assign($testValues);
        $this->_mailMerge->createDocument();
        foreach ($formats as $format) {
            $document = $this->_mailMerge->retrieveDocument($format);
            $this->assertGreaterThan(2048, strlen($document));
        }
    }

    public function testRetrieveDocumentAppended()
    {
        $formats = array('doc', 'docx', 'html', 'pdf', 'rtf', 'txd', 'txt');

        $testValues = array(
            array(
                'software' => 'phpunit - document 1',
                'licensee' => 'phpunit - document 1',
                'company'  => 'phpunit - document 1',
                'date'     => 'phpunit - document 1',
                'time'     => 'phpunit - document 1',
                'city'     => 'phpunit - document 1',
                'country'  => 'phpunit - document 1'
            ),
            array(
                'software' => 'phpunit - document 2',
                'licensee' => 'phpunit - document 2',
                'company'  => 'phpunit - document 2',
                'date'     => 'phpunit - document 2',
                'time'     => 'phpunit - document 2',
                'city'     => 'phpunit - document 2',
                'country'  => 'phpunit - document 2'
            ),
        );

        // Remote Template
        $this->_mailMerge->uploadTemplate($this->_path . DIRECTORY_SEPARATOR . self::TEST_TEMPLATE_1);
        $this->_mailMerge->setRemoteTemplate(self::TEST_TEMPLATE_1);
        $this->_mailMerge->assign($testValues);
        $this->_mailMerge->createDocument();
        foreach ($formats as $format) {
            $document = $this->_mailMerge->retrieveDocument($format);
            $this->assertGreaterThan(2048, strlen($document));
        }
        $this->_mailMerge->deleteTemplate(self::TEST_TEMPLATE_1);

        // Local Template
        $this->_mailMerge->setLocalTemplate($this->_path . DIRECTORY_SEPARATOR . self::TEST_TEMPLATE_1);
        $this->_mailMerge->assign($testValues);
        $this->_mailMerge->createDocument();
        foreach ($formats as $format) {
            $document = $this->_mailMerge->retrieveDocument($format);
            $this->assertGreaterThan(2048, strlen($document));
        }
    }

    // -------------------------------------------------------------------------

    public function testGetTemplateFormats()
    {
        $expectedResults = array('doc' , 'docx' , 'rtf' , 'txd');
        $this->assertEquals($expectedResults, $this->_mailMerge->getTemplateFormats());
    }

    public function testGetDocumentFormats()
    {
        $expectedResults = array('doc' , 'docx' , 'html' , 'pdf' , 'rtf' , 'txd' , 'txt');
        $this->assertEquals($expectedResults, $this->_mailMerge->getDocumentFormats());
    }

    public function testGetImageImportFormats()
    {
        $expectedResults = array('bmp' , 'gif' , 'jpg' , 'png' , 'tiff', 'wmf');
        $this->assertEquals($expectedResults, $this->_mailMerge->getImageImportFormats());
    }

    public function testGetImageExportFormats()
    {
        $expectedResults = array('bmp' , 'gif' , 'jpg' , 'png' , 'tiff');
        $this->assertEquals($expectedResults, $this->_mailMerge->getImageExportFormats());
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
            'country'  => 'phpunit'
        );

        $expectedResults = array(
            'bmp'  => 'a1934f2153172f021847af7ece9049ce',
            'gif'  => 'd7281d7b6352ff897917e25d6b92746f',
            'jpg'  => 'e0b20ea2c9a6252886f689f227109085',
            'png'  => 'c449f0c2726f869e9a42156e366f1bf9',
            'tiff' => '20a96a94762a531e9879db0aa6bd673f'
        );

        $this->_mailMerge->setLocalTemplate($this->_path . DIRECTORY_SEPARATOR . self::TEST_TEMPLATE_1);
        $this->_mailMerge->assign($testValues);
        $this->_mailMerge->createDocument();
        foreach($this->_mailMerge->getImageExportFormats() as $format) {
            $bitmaps = $this->_mailMerge->getBitmaps(1, 1, 20, $format);
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
            'country'  => 'phpunit'
        );

        $expectedResults = array(
            'bmp'  => 'e8a884ee61c394deec8520fb397d1cf1',
            'gif'  => '2255fee47b4af8438b109efc3cb0d304',
            'jpg'  => 'e1acfc3001fc62567de2a489eccdb552',
            'png'  => '15eac34d08e602cde042862b467fa865',
            'tiff' => '98bad79380a80c9cc43dfffc5158d0f9'
        );

        $this->_mailMerge->setLocalTemplate($this->_path . DIRECTORY_SEPARATOR . self::TEST_TEMPLATE_1);
        $this->_mailMerge->assign($testValues);
        $this->_mailMerge->createDocument();
        foreach($this->_mailMerge->getImageExportFormats() as $format) {
            $bitmaps = $this->_mailMerge->getAllBitmaps(20, $format);
            $this->assertEquals($expectedResults[$format], md5(serialize($bitmaps)));
        }
    }

    public function testGetFontNames()
    {
        $fonts = $this->_mailMerge->getFontNames();
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
        $expectedResults = array(
            'phone', 'date', 'name', 'customer_number', 'invoice_number',
            'account_number', 'service_phone', 'service_fax', 'month',
            'monthly_fee', 'total_net', 'tax', 'tax_value', 'total');

        // Remote Template
        $this->_mailMerge->uploadTemplate($this->_path . DIRECTORY_SEPARATOR . self::TEST_TEMPLATE_2);
        $this->_mailMerge->setRemoteTemplate(self::TEST_TEMPLATE_2);
        $this->assertEquals($expectedResults, $this->_mailMerge->getFieldNames());
        $this->_mailMerge->deleteTemplate(self::TEST_TEMPLATE_2);

        // Local Template
        $this->_mailMerge->setLocalTemplate($this->_path . DIRECTORY_SEPARATOR . self::TEST_TEMPLATE_2);
        $this->assertEquals($expectedResults, $this->_mailMerge->getFieldNames());
    }

    public function testGetBlockFieldNames()
    {
        $expectedResults = array('connection_number', 'connection_duration', 'fee');

        // Remote Template
        $this->_mailMerge->uploadTemplate($this->_path . DIRECTORY_SEPARATOR . self::TEST_TEMPLATE_2);
        $this->_mailMerge->setRemoteTemplate(self::TEST_TEMPLATE_2);
        $this->assertEquals($expectedResults, $this->_mailMerge->getBlockFieldNames('connection'));
        $this->_mailMerge->deleteTemplate(self::TEST_TEMPLATE_2);

        // Local Template
        $this->_mailMerge->setLocalTemplate($this->_path . DIRECTORY_SEPARATOR . self::TEST_TEMPLATE_2);
        $this->assertEquals($expectedResults, $this->_mailMerge->getBlockFieldNames('connection'));
    }

    public function testGetBlockNames()
    {
        $expectedResults = array('connection');

        // Remote Template
        $this->_mailMerge->uploadTemplate($this->_path . DIRECTORY_SEPARATOR . self::TEST_TEMPLATE_2);
        $this->_mailMerge->setRemoteTemplate(self::TEST_TEMPLATE_2);
        $this->assertEquals($expectedResults, $this->_mailMerge->getBlockNames());
        $this->_mailMerge->deleteTemplate(self::TEST_TEMPLATE_2);

        // Local Template
        $this->_mailMerge->setLocalTemplate($this->_path . DIRECTORY_SEPARATOR . self::TEST_TEMPLATE_2);
        $this->assertEquals($expectedResults, $this->_mailMerge->getBlockNames());
    }

    // -------------------------------------------------------------------------

    public function testUploadTemplate()
    {
        $this->_mailMerge->deleteTemplate(self::TEST_TEMPLATE_2);
        $this->assertNull($this->_mailMerge->uploadTemplate($this->_path . DIRECTORY_SEPARATOR . self::TEST_TEMPLATE_2));
        $this->_mailMerge->deleteTemplate(self::TEST_TEMPLATE_2);
    }

    public function testDownloadTemplate()
    {
        $expectedResults = '2f076af778ca5f8afc9661cfb9deb7c6';
        $this->_mailMerge->uploadTemplate($this->_path . DIRECTORY_SEPARATOR . self::TEST_TEMPLATE_2);
        $template = $this->_mailMerge->downloadTemplate(self::TEST_TEMPLATE_2);
        $this->assertEquals($expectedResults, md5($template));
    }

    public function testDeleteTemplate()
    {
        $this->_mailMerge->uploadTemplate($this->_path . DIRECTORY_SEPARATOR . self::TEST_TEMPLATE_2);
        $this->_mailMerge->deleteTemplate(self::TEST_TEMPLATE_2);
        $templateDeleted = true;
        foreach($this->_mailMerge->listTemplates() as $template) {
            if($template['filename'] == self::TEST_TEMPLATE_2) {
                $templateDeleted = false;
            }
        }
        $this->assertTrue($templateDeleted);
    }

    public function testListTemplates()
    {
        $this->_mailMerge->uploadTemplate($this->_path . DIRECTORY_SEPARATOR . self::TEST_TEMPLATE_1);
        $this->_mailMerge->uploadTemplate($this->_path . DIRECTORY_SEPARATOR . self::TEST_TEMPLATE_2);

        // Where templates uploaded and are being listed?
        $testTemplate1Exists = false;
        $testTemplate2Exists = false;

        $templates = $this->_mailMerge->listTemplates();
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

        $this->_mailMerge->deleteTemplate(self::TEST_TEMPLATE_1);
        $this->_mailMerge->deleteTemplate(self::TEST_TEMPLATE_2);
    }

    public function testTemplateExists()
    {
        $this->_mailMerge->uploadTemplate($this->_path . DIRECTORY_SEPARATOR . self::TEST_TEMPLATE_2);
        $this->assertTrue($this->_mailMerge->templateExists(self::TEST_TEMPLATE_2));
        $this->_mailMerge->deleteTemplate(self::TEST_TEMPLATE_2);
    }

    // -------------------------------------------------------------------------

    public function testUploadImage()
    {
        $this->_mailMerge->deleteImage(self::TEST_IMAGE_2);
        $this->assertNull($this->_mailMerge->uploadImage($this->_path . DIRECTORY_SEPARATOR . self::TEST_IMAGE_2));
        $this->_mailMerge->deleteImage(self::TEST_IMAGE_2);
    }

    public function testDownloadImage()
    {
        $expectedResults = 'f8b663e465acd570414395d5c33541ab';
        $this->_mailMerge->uploadImage($this->_path . DIRECTORY_SEPARATOR . self::TEST_IMAGE_2);
        $image = $this->_mailMerge->downloadImage(self::TEST_IMAGE_2);
        $this->assertEquals($expectedResults, md5($image));
    }

    public function testDeleteImage()
    {
        $this->_mailMerge->uploadImage($this->_path . DIRECTORY_SEPARATOR . self::TEST_IMAGE_2);
        $this->_mailMerge->deleteImage(self::TEST_IMAGE_2);
        $imageDeleted = true;
        foreach($this->_mailMerge->listImages() as $image) {
            if($image['filename'] == self::TEST_IMAGE_2) {
                $imageDeleted = false;
            }
        }
        $this->assertTrue($imageDeleted);
    }

    public function testListImages()
    {
        $this->_mailMerge->uploadImage($this->_path . DIRECTORY_SEPARATOR . self::TEST_IMAGE_1);
        $this->_mailMerge->uploadImage($this->_path . DIRECTORY_SEPARATOR . self::TEST_IMAGE_2);

        // Where images uploaded and are being listed?
        $testImage1Exists = false;
        $testImage2Exists = false;

        $images = $this->_mailMerge->listImages();
        foreach($images as $image) {
            if(self::TEST_IMAGE_1 === $image['filename']) {
                $testImage1Exists = true;
            } elseif(self::TEST_IMAGE_2 === $image['filename']) {
                $testImage2Exists = true;
            }
        }
        $this->assertTrue($testImage1Exists && $testImage2Exists);

        // Is all info about images available?
        $expectedResults = array('filename', 'fileSize', 'createTime', 'modifyTime');
        foreach($images as $image) {
            $this->assertEquals($expectedResults, array_keys($image));
        }

        // Is all info about images correct?
        foreach($images as $image) {
            $this->assertTrue(strlen($image['filename']) > 0);
            $this->assertTrue($image['fileSize'] > 1);
            $this->assertTrue($image['createTime'] > mktime(0, 0, 0, 1, 1, 1980));
            $this->assertTrue($image['modifyTime'] > mktime(0, 0, 0, 1, 1, 1980));
        }

        $this->_mailMerge->deleteImage(self::TEST_IMAGE_1);
        $this->_mailMerge->deleteImage(self::TEST_IMAGE_2);
    }

    public function testImageExists()
    {
        $this->_mailMerge->uploadImage($this->_path . DIRECTORY_SEPARATOR . self::TEST_IMAGE_2);
        $this->assertTrue($this->_mailMerge->imageExists(self::TEST_IMAGE_2));
        $this->_mailMerge->deleteImage(self::TEST_IMAGE_2);
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

        $actualResults = MailMerge::assocArrayToArrayOfArrayOfString($testValues);
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
        $actualResults = MailMerge::multiAssocArrayToArrayOfArrayOfString($testValues);
        $this->assertEquals($expectedResults, $actualResults);
    }
    
}
