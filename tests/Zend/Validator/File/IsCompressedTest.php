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
 * @package    Zend_Validator_File
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace ZendTest\Validator\File;

use Zend\Validator\File;

/**
 * IsCompressed testbed
 *
 * @category   Zend
 * @package    Zend_Validator_File
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Validator
 */
class IsCompressedTest extends \PHPUnit_Framework_TestCase
{
    protected function getMagicMime()
    {
        // As of PHP >= 5.3.11 and >= 5.4.1 the magic database format has changed.
        // http://doc.php.net/downloads/pdf/split/de/File-Information.pdf (page 11)
        if (version_compare(PHP_VERSION, '5.3.10', '<=') || (version_compare(PHP_VERSION, '5.4', '>=') &&
                                                              version_compare(PHP_VERSION, '5.4.1', '<'))) {
            return __DIR__ . '/_files/magic.lte.5.3.10.mime';
        }

        return __DIR__ . '/_files/magic.mime';
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @return void
     */
    public function testBasic()
    {
        if (!extension_loaded('fileinfo') &&
            function_exists('mime_content_type') && ini_get('mime_magic.magicfile') &&
            (mime_content_type(__DIR__ . '/_files/test.zip') == 'text/plain')
            ) {
            $this->markTestSkipped('This PHP Version has no finfo, has mime_content_type, '
                . ' but mime_content_type exhibits buggy behavior on this system.'
                );
        }

        $valuesExpected = array(
            array(null, true),
            array('zip', true),
            array('test/notype', false),
            array('application/zip, application/x-tar', true),
            array(array('application/zip', 'application/x-tar'), true),
            array(array('zip', 'tar'), true),
            array(array('tar', 'arj'), false),
        );

        $files = array(
            'name'     => 'test.zip',
            'type'     => 'application/zip',
            'size'     => 200,
            'tmp_name' => __DIR__ . '/_files/test.zip',
            'error'    => 0
        );

        foreach ($valuesExpected as $element) {
            $validator = new File\IsCompressed($element[0]);
            $validator->enableHeaderCheck();
            $this->assertEquals(
                $element[1],
                $validator->isValid(__DIR__ . '/_files/test.zip', $files),
                "Tested with " . var_export($element, 1)
            );
        }
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
        $this->assertTrue(array_key_exists('fileIsCompressedFalseType', $error));
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

    /**
     * @group ZF-11258
     */
    public function testZF11258()
    {
        $validator = new File\IsCompressed();
        $this->assertFalse($validator->isValid(__DIR__ . '/_files/nofile.mo'));
        $this->assertTrue(array_key_exists('fileIsCompressedNotReadable', $validator->getMessages()));
        $this->assertContains("'nofile.mo'", current($validator->getMessages()));
    }
}
