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
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: $
 */

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../../TestHelper.php';

/**
 * @see Zend_Filter_File_Decrypt
 */
require_once 'Zend/Filter/File/Decrypt.php';
require_once 'Zend/Filter/File/Encrypt.php';

/**
 * @category   Zend
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Filter
 */
class Zend_Filter_File_DecryptTest extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        if (!extension_loaded('mcrypt')) {
            $this->markTestSkipped('This filter needs the mcrypt extension');
        }

        if (file_exists(dirname(__FILE__).'/../_files/newencryption.txt')) {
            unlink(dirname(__FILE__).'/../_files/newencryption.txt');
        }

        if (file_exists(dirname(__FILE__).'/../_files/newencryption2.txt')) {
            unlink(dirname(__FILE__).'/../_files/newencryption2.txt');
        }
    }

    public function tearDown()
    {
        if (file_exists(dirname(__FILE__).'/../_files/newencryption.txt')) {
            unlink(dirname(__FILE__).'/../_files/newencryption.txt');
        }

        if (file_exists(dirname(__FILE__).'/../_files/newencryption2.txt')) {
            unlink(dirname(__FILE__).'/../_files/newencryption2.txt');
        }
    }

    /**
     * Ensures that the filter follows expected behavior
     *
     * @return void
     */
    public function testBasic()
    {
        $filter = new Zend_Filter_File_Encrypt();
        $filter->setFilename(dirname(__FILE__).'/../_files/newencryption.txt');

        $this->assertEquals(
            dirname(__FILE__).'/../_files/newencryption.txt',
            $filter->getFilename());

        $filter->setVector('testvect');
        $filter->filter(dirname(__FILE__).'/../_files/encryption.txt');

        $filter = new Zend_Filter_File_Decrypt();

        $this->assertNotEquals(
            'Encryption',
            file_get_contents(dirname(__FILE__).'/../_files/newencryption.txt'));

        $filter->setVector('testvect');
        $this->assertEquals(
            dirname(__FILE__).'/../_files/newencryption.txt',
            $filter->filter(dirname(__FILE__).'/../_files/newencryption.txt'));

        $this->assertEquals(
            'Encryption',
            trim(file_get_contents(dirname(__FILE__).'/../_files/newencryption.txt')));
    }

    public function testEncryptionWithDecryption()
    {
        $filter = new Zend_Filter_File_Encrypt();
        $filter->setFilename(dirname(__FILE__).'/../_files/newencryption.txt');
        $filter->setVector('testvect');
        $this->assertEquals(dirname(__FILE__).'/../_files/newencryption.txt',
            $filter->filter(dirname(__FILE__).'/../_files/encryption.txt'));

        $this->assertNotEquals(
            'Encryption',
            file_get_contents(dirname(__FILE__).'/../_files/newencryption.txt'));

        $filter = new Zend_Filter_File_Decrypt();
        $filter->setFilename(dirname(__FILE__).'/../_files/newencryption2.txt');

        $this->assertEquals(
            dirname(__FILE__).'/../_files/newencryption2.txt',
            $filter->getFilename());

        $filter->setVector('testvect');
        $input = $filter->filter(dirname(__FILE__).'/../_files/newencryption.txt');
        $this->assertEquals(dirname(__FILE__).'/../_files/newencryption2.txt', $input);

        $this->assertEquals(
            'Encryption',
            trim(file_get_contents(dirname(__FILE__).'/../_files/newencryption2.txt')));
    }

    /**
     * @return void
     */
    public function testNonExistingFile()
    {
        $filter = new Zend_Filter_File_Decrypt();
        $filter->setVector('testvect');

        try {
            $filter->filter(dirname(__FILE__).'/../_files/nofile.txt');
            $this->fail();
        } catch (Zend_Filter_Exception $e) {
            $this->assertContains('not found', $e->getMessage());
        }
    }
}
