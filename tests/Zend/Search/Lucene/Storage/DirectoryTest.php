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
 * @package    Zend_Search_Lucene
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Search\Lucene\Storage;
use Zend\Search\Lucene\Storage\Directory;

/**
 * Zend_Search_Lucene_Storage_Directory_Filesystem
 */

/**
 * PHPUnit test case
 */

/**
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Search_Lucene
 */
class DirectoryTest extends \PHPUnit_Framework_TestCase
{
    public function testFilesystem()
    {
        $tempPath = __DIR__ . '/_tempFiles/_files';

        // remove files from temporary direcytory
        $dir = opendir($tempPath);
        while (($file = readdir($dir)) !== false) {
            if (!is_dir($tempPath . '/' . $file)) {
                @unlink($tempPath . '/' . $file);
            }
        }
        closedir($dir);

        $directory = new Directory\Filesystem($tempPath);

        $this->assertTrue($directory instanceof Directory);
        $this->assertEquals(count($directory->fileList()), 0);

        $fileObject = $directory->createFile('file1');
        $this->assertTrue($fileObject instanceof \Zend\Search\Lucene\Storage\File);
        unset($fileObject);
        $this->assertEquals($directory->fileLength('file1'), 0);

        $this->assertEquals(count(array_diff($directory->fileList(), array('file1'))), 0);

        $directory->deleteFile('file1');
        $this->assertEquals(count($directory->fileList()), 0);

        $this->assertFalse($directory->fileExists('file2'));

        $fileObject = $directory->createFile('file2');
        $this->assertEquals($directory->fileLength('file2'), 0);
        $fileObject->writeBytes('0123456789');
        unset($fileObject);
        $this->assertEquals($directory->fileLength('file2'), 10);

        $directory->renameFile('file2', 'file3');
        $this->assertEquals(count(array_diff($directory->fileList(), array('file3'))), 0);

        $modifiedAt1 = $directory->fileModified('file3');
        clearstatcache();
        $directory->touchFile('file3');
        $modifiedAt2 = $directory->fileModified('file3');
        sleep(1);
        clearstatcache();
        $directory->touchFile('file3');
        $modifiedAt3 = $directory->fileModified('file3');

        $this->assertTrue($modifiedAt2 >= $modifiedAt1);
        $this->assertTrue($modifiedAt3 >  $modifiedAt2);

        $fileObject = $directory->getFileObject('file3');
        $this->assertEquals($fileObject->readBytes($directory->fileLength('file3')), '0123456789');
        unset($fileObject);

        $fileObject = $directory->createFile('file3');
        $this->assertEquals($fileObject->readBytes($directory->fileLength('file3')), '');
        unset($fileObject);

        $directory->deleteFile('file3');
        $this->assertEquals(count($directory->fileList()), 0);

        $directory->close();
    }

    public function testFilesystemSubfoldersAutoCreation()
    {
        $directory = new Directory\Filesystem(__DIR__ . '/_tempFiles/_files/dir1/dir2/dir3');
        $this->assertTrue($directory instanceof Directory);
        $directory->close();

        rmdir(__DIR__ . '/_tempFiles/_files/dir1/dir2/dir3');
        rmdir(__DIR__ . '/_tempFiles/_files/dir1/dir2');
        rmdir(__DIR__ . '/_tempFiles/_files/dir1');
    }
}

