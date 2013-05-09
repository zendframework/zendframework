<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Filter
 */

namespace ZendTest\Filter\File;

use Zend\Filter\File\Decrypt as FileDecrypt;
use Zend\Filter\File\Encrypt as FileEncrypt;

/**
 * @category   Zend
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @group      Zend_Filter
 */
class DecryptTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (!extension_loaded('mcrypt')) {
            $this->markTestSkipped('This filter needs the mcrypt extension');
        }

        if (file_exists(dirname(__DIR__).'/_files/newencryption.txt')) {
            unlink(dirname(__DIR__).'/_files/newencryption.txt');
        }

        if (file_exists(dirname(__DIR__).'/_files/newencryption2.txt')) {
            unlink(dirname(__DIR__).'/_files/newencryption2.txt');
        }
    }

    public function tearDown()
    {
        if (file_exists(dirname(__DIR__).'/_files/newencryption.txt')) {
            unlink(dirname(__DIR__).'/_files/newencryption.txt');
        }

        if (file_exists(dirname(__DIR__).'/_files/newencryption2.txt')) {
            unlink(dirname(__DIR__).'/_files/newencryption2.txt');
        }
    }

    /**
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testBasic()
    {
        $filter = new FileEncrypt();
        $filter->setFilename(dirname(__DIR__).'/_files/newencryption.txt');

        $this->assertEquals(
            dirname(__DIR__).'/_files/newencryption.txt',
            $filter->getFilename());

        $filter->setKey('1234567890123456');
        $filter->filter(dirname(__DIR__).'/_files/encryption.txt');

        $filter = new FileDecrypt();

        $this->assertNotEquals(
            'Encryption',
            file_get_contents(dirname(__DIR__).'/_files/newencryption.txt'));

        $filter->setKey('1234567890123456');
        $this->assertEquals(
            dirname(__DIR__).'/_files/newencryption.txt',
            $filter->filter(dirname(__DIR__).'/_files/newencryption.txt'));

        $this->assertEquals(
            'Encryption',
            trim(file_get_contents(dirname(__DIR__).'/_files/newencryption.txt')));
    }

    public function testEncryptionWithDecryption()
    {
        $filter = new FileEncrypt();
        $filter->setFilename(dirname(__DIR__).'/_files/newencryption.txt');
        $filter->setKey('1234567890123456');
        $this->assertEquals(dirname(__DIR__).'/_files/newencryption.txt',
            $filter->filter(dirname(__DIR__).'/_files/encryption.txt'));

        $this->assertNotEquals(
            'Encryption',
            file_get_contents(dirname(__DIR__).'/_files/newencryption.txt'));

        $filter = new FileDecrypt();
        $filter->setFilename(dirname(__DIR__).'/_files/newencryption2.txt');

        $this->assertEquals(
            dirname(__DIR__).'/_files/newencryption2.txt',
            $filter->getFilename());

        $filter->setKey('1234567890123456');
        $input = $filter->filter(dirname(__DIR__).'/_files/newencryption.txt');
        $this->assertEquals(dirname(__DIR__).'/_files/newencryption2.txt', $input);

        $this->assertEquals(
            'Encryption',
            trim(file_get_contents(dirname(__DIR__).'/_files/newencryption2.txt')));
    }

    /**
     * @return void
     */
    public function testNonExistingFile()
    {
        $filter = new FileDecrypt();
        $filter->setVector('1234567890123456');

        $this->setExpectedException('\Zend\Filter\Exception\InvalidArgumentException', 'not found');
        $filter->filter(dirname(__DIR__).'/_files/nofile.txt');
    }
}
