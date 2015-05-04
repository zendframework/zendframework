<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Validator\File;

use Zend\Validator\File;

/**
 * IsCompressed testbed
 *
 * @group      Zend_Validator
 */
class IsCompressedTest extends \PHPUnit_Framework_TestCase
{
    protected function getMagicMime()
    {
        // PHP 7 uses yet another version of libmagic, and thus a new magic
        // database format.
        if (version_compare(PHP_VERSION, '7.0', '>=')) {
            return __DIR__ . '/_files/magic.7.mime';
        }

        // As of PHP >= 5.3.11 and >= 5.4.1 the magic database format has changed.
        // http://doc.php.net/downloads/pdf/split/de/File-Information.pdf (page 11)
        if (version_compare(PHP_VERSION, '5.4', '>=')
                && version_compare(PHP_VERSION, '5.4.1', '<')
        ) {
            return __DIR__ . '/_files/magic.lte.5.3.10.mime';
        }

        return __DIR__ . '/_files/magic.mime';
    }

    /**
     * @return array
     */
    public function basicBehaviorDataProvider()
    {
        $testFile = __DIR__ . '/_files/test.zip';

        // Sometimes finfo gives application/zip and sometimes
        // application/x-zip ...
        $expectedMimeType = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $testFile);

        $allowed          = array('application/zip', 'application/x-zip');
        $fileUpload       = array(
            'tmp_name' => $testFile,
            'name'     => basename($testFile),
            'size'     => 200,
            'error'    => 0,
            'type'     => in_array($expectedMimeType, $allowed) ? $expectedMimeType : 'application/zip',
        );

        return array(
            //    Options, isValid Param, Expected value
            array(null,                                                               $fileUpload, true),
            array('zip',                                                              $fileUpload, true),
            array('test/notype',                                                      $fileUpload, false),
            array('application/x-zip, application/zip, application/x-tar',            $fileUpload, true),
            array(array('application/x-zip', 'application/zip', 'application/x-tar'), $fileUpload, true),
            array(array('zip', 'tar'),                                                $fileUpload, true),
            array(array('tar', 'arj'),                                                $fileUpload, false),
        );
    }

    /**
     * Skip a test if the file info extension is missing
     */
    protected function skipIfNoFileInfoExtension()
    {
        if (!extension_loaded('fileinfo')) {
            $this->markTestSkipped(
                'This PHP Version has no finfo extension'
            );
        }
    }

    /**
     * Skip a test if finfo returns buggy information
     */
    protected function skipIfBuggyMimeContentType($options)
    {
        if (!is_array($options)) {
            $options = (array) $options;
        }

        if (!in_array('application/zip', $options)) {
            // finfo does not play a role; no need to skip
            return;
        }

        // Sometimes finfo gives application/zip and sometimes
        // application/x-zip ...
        $expectedMimeType = finfo_file(finfo_open(FILEINFO_MIME_TYPE), __DIR__ . '/_files/test.zip');
        if (!in_array($expectedMimeType, array('application/zip', 'application/x-zip'))) {
            $this->markTestSkipped('finfo exhibits buggy behavior on this system!');
        }
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @dataProvider basicBehaviorDataProvider
     * @return void
     */
    public function testBasic($options, $isValidParam, $expected)
    {
        $this->skipIfNoFileInfoExtension();
        $this->skipIfBuggyMimeContentType($options);

        $validator = new File\IsCompressed($options);
        $validator->enableHeaderCheck();
        $this->assertEquals($expected, $validator->isValid($isValidParam));
    }

    /**
     * Ensures that the validator follows expected behavior for legacy Zend\Transfer API
     *
     * @dataProvider basicBehaviorDataProvider
     */
    public function testLegacy($options, $isValidParam, $expected)
    {
        if (!is_array($isValidParam)) {
            // nothing to test
            return;
        }

        $this->skipIfNoFileInfoExtension();
        $this->skipIfBuggyMimeContentType($options);

        $validator = new File\IsCompressed($options);
        $validator->enableHeaderCheck();
        $this->assertEquals($expected, $validator->isValid($isValidParam['tmp_name'], $isValidParam));
    }

    /**
     * Ensures that getMimeType() returns expected value
     *
     * @return void
     */
    public function testGetMimeType()
    {
        $validator = new File\IsCompressed('image/gif');
        $this->assertEquals('image/gif', $validator->getMimeType());

        $validator = new File\IsCompressed(array('image/gif', 'video', 'text/test'));
        $this->assertEquals('image/gif,video,text/test', $validator->getMimeType());

        $validator = new File\IsCompressed(array('image/gif', 'video', 'text/test'));
        $this->assertEquals(array('image/gif', 'video', 'text/test'), $validator->getMimeType(true));
    }

    /**
     * Ensures that setMimeType() returns expected value
     *
     * @return void
     */
    public function testSetMimeType()
    {
        $validator = new File\IsCompressed('image/gif');
        $validator->setMimeType('image/jpeg');
        $this->assertEquals('image/jpeg', $validator->getMimeType());
        $this->assertEquals(array('image/jpeg'), $validator->getMimeType(true));

        $validator->setMimeType('image/gif, text/test');
        $this->assertEquals('image/gif,text/test', $validator->getMimeType());
        $this->assertEquals(array('image/gif', 'text/test'), $validator->getMimeType(true));

        $validator->setMimeType(array('video/mpeg', 'gif'));
        $this->assertEquals('video/mpeg,gif', $validator->getMimeType());
        $this->assertEquals(array('video/mpeg', 'gif'), $validator->getMimeType(true));
    }

    /**
     * Ensures that addMimeType() returns expected value
     *
     * @return void
     */
    public function testAddMimeType()
    {
        $validator = new File\IsCompressed('image/gif');
        $validator->addMimeType('text');
        $this->assertEquals('image/gif,text', $validator->getMimeType());
        $this->assertEquals(array('image/gif', 'text'), $validator->getMimeType(true));

        $validator->addMimeType('jpg, to');
        $this->assertEquals('image/gif,text,jpg,to', $validator->getMimeType());
        $this->assertEquals(array('image/gif', 'text', 'jpg', 'to'), $validator->getMimeType(true));

        $validator->addMimeType(array('zip', 'ti'));
        $this->assertEquals('image/gif,text,jpg,to,zip,ti', $validator->getMimeType());
        $this->assertEquals(array('image/gif', 'text', 'jpg', 'to', 'zip', 'ti'), $validator->getMimeType(true));

        $validator->addMimeType('');
        $this->assertEquals('image/gif,text,jpg,to,zip,ti', $validator->getMimeType());
        $this->assertEquals(array('image/gif', 'text', 'jpg', 'to', 'zip', 'ti'), $validator->getMimeType(true));
    }

    /**
     * @ZF-8111
     */
    public function testErrorMessages()
    {
        $files = array(
            'name'     => 'picture.jpg',
            'type'     => 'image/jpeg',
            'size'     => 200,
            'tmp_name' => __DIR__ . '/_files/picture.jpg',
            'error'    => 0
        );

        $validator = new File\IsCompressed('test/notype');
        $validator->enableHeaderCheck();
        $this->assertFalse($validator->isValid(__DIR__ . '/_files/picture.jpg', $files));
        $error = $validator->getMessages();
        $this->assertArrayHasKey('fileIsCompressedFalseType', $error);
    }

    public function testOptionsAtConstructor()
    {
        if (!extension_loaded('fileinfo')) {
            $this->markTestSkipped('This PHP Version has no finfo installed');
        }

        $magicFile = $this->getMagicMime();
        $validator = new File\IsCompressed(array(
            'image/gif',
            'image/jpg',
            'magicFile'   => $magicFile,
            'enableHeaderCheck' => true));

        $this->assertEquals($magicFile, $validator->getMagicFile());
        $this->assertTrue($validator->getHeaderCheck());
        $this->assertEquals('image/gif,image/jpg', $validator->getMimeType());
    }

    public function testNonMimeOptionsAtConstructorStillSetsDefaults()
    {
        $validator = new File\IsCompressed(array(
            'enableHeaderCheck' => true,
        ));

        $this->assertNotEmpty($validator->getMimeType());
    }

    /**
     * @group ZF-11258
     */
    public function testZF11258()
    {
        $validator = new File\IsCompressed();
        $this->assertFalse($validator->isValid(__DIR__ . '/_files/nofile.mo'));
        $this->assertArrayHasKey('fileIsCompressedNotReadable', $validator->getMessages());
        $this->assertContains("does not exist", current($validator->getMessages()));
    }
}
