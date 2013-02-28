<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Validator
 */

namespace ZendTest\Validator\File;

use Zend\Validator\File;

/**
 * IsImage testbed
 *
 * @category   Zend
 * @package    Zend_Validator_File
 * @subpackage UnitTests
 * @group      Zend_Validator
 */
class IsImageTest extends \PHPUnit_Framework_TestCase
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
     * @return array
     */
    public function basicBehaviorDataProvider()
    {
        $testFile = __DIR__ . '/_files/picture.jpg';
        $fileUpload = array(
            'tmp_name' => $testFile, 'name' => basename($testFile),
            'size' => 200, 'error' => 0, 'type' => 'image/jpeg'
        );
        return array(
            //    Options, isValid Param, Expected value
            array(null,                              $fileUpload, true),
            array('jpeg',                            $fileUpload, true),
            array('test/notype',                     $fileUpload, false),
            array('image/gif, image/jpeg',           $fileUpload, true),
            array(array('image/vasa', 'image/jpeg'), $fileUpload, true),
            array(array('image/jpeg', 'gif'),        $fileUpload, true),
            array(array('image/gif', 'gif'),         $fileUpload, false),
            array('image/jp',                        $fileUpload, false),
            array('image/jpg2000',                   $fileUpload, false),
            array('image/jpeg2000',                  $fileUpload, false),
        );
    }

    /**
     * Ensures that the validator follows expected behavior
     *
     * @dataProvider basicBehaviorDataProvider
     * @return void
     */
    public function testBasic($options, $isValidParam, $expected)
    {
        $validator = new File\IsImage($options);
        $validator->enableHeaderCheck();
        $this->assertEquals($expected, $validator->isValid($isValidParam));
    }

    /**
     * Ensures that the validator follows expected behavior for legacy Zend\Transfer API
     *
     * @dataProvider basicBehaviorDataProvider
     * @return void
     */
    public function testLegacy($options, $isValidParam, $expected)
    {
        if (is_array($isValidParam)) {
            $validator = new File\IsImage($options);
            $validator->enableHeaderCheck();
            $this->assertEquals($expected, $validator->isValid($isValidParam['tmp_name'], $isValidParam));
        }
    }

    /**
     * Ensures that getMimeType() returns expected value
     *
     * @return void
     */
    public function testGetMimeType()
    {
        $validator = new File\IsImage('image/gif');
        $this->assertEquals('image/gif', $validator->getMimeType());

        $validator = new File\IsImage(array('image/gif', 'video', 'text/test'));
        $this->assertEquals('image/gif,video,text/test', $validator->getMimeType());

        $validator = new File\IsImage(array('image/gif', 'video', 'text/test'));
        $this->assertEquals(array('image/gif', 'video', 'text/test'), $validator->getMimeType(true));
    }

    /**
     * Ensures that setMimeType() returns expected value
     *
     * @return void
     */
    public function testSetMimeType()
    {
        $validator = new File\IsImage('image/gif');
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
        $validator = new File\IsImage('image/gif');
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

        $validator = new File\IsImage('test/notype');
        $validator->enableHeaderCheck();
        $this->assertFalse($validator->isValid(__DIR__ . '/_files/picture.jpg', $files));
        $error = $validator->getMessages();
        $this->assertTrue(array_key_exists('fileIsImageFalseType', $error));
    }

    public function testOptionsAtConstructor()
    {
        if (!extension_loaded('fileinfo')) {
            $this->markTestSkipped('This PHP Version has no finfo installed');
        }

        $magicFile = $this->getMagicMime();
        $validator = new File\IsImage(array(
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
        $validator = new File\IsImage(array(
            'enableHeaderCheck' => true,
        ));

        $this->assertNotEmpty($validator->getMimeType());
    }

    /**
     * @group ZF-11258
     */
    public function testZF11258()
    {
        $validator = new File\IsImage();
        $this->assertFalse($validator->isValid(__DIR__ . '/_files/nofile.mo'));
        $this->assertTrue(array_key_exists('fileIsImageNotReadable', $validator->getMessages()));
        $this->assertContains("does not exist", current($validator->getMessages()));
    }
}
