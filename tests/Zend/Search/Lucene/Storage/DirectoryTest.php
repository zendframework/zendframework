<?php
/**
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage UnitTests
 */


/**
 * Zend_Search_Lucene_Storage_Directory_Filesystem
 */
require_once 'Zend/Search/Lucene/Storage/Directory/Filesystem.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage UnitTests
 */
class Zend_Search_Lucene_Storage_DirectoryTest extends PHPUnit_Framework_TestCase
{
    public function testFilesystem()
    {
        $tempPath = dirname(__FILE__) . '/_tempFiles/_files';

        // remove files from temporary direcytory
        $dir = opendir($tempPath);
        while (($file = readdir($dir)) !== false) {
            if (!is_dir($tempPath . '/' . $file)) {
                @unlink($tempPath . '/' . $file);
            }
        }
        closedir($dir);

        $directory = new Zend_Search_Lucene_Storage_Directory_Filesystem($tempPath);

        $this->assertTrue($directory instanceof Zend_Search_Lucene_Storage_Directory);
        $this->assertEquals(count($directory->fileList()), 0);

        $fileObject = $directory->createFile('file1');
        $this->assertTrue($fileObject instanceof Zend_Search_Lucene_Storage_File);
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
        $directory = new Zend_Search_Lucene_Storage_Directory_Filesystem(dirname(__FILE__) . '/_tempFiles/_files/dir1/dir2/dir3');
        $this->assertTrue($directory instanceof Zend_Search_Lucene_Storage_Directory);
        $directory->close();

        rmdir(dirname(__FILE__) . '/_tempFiles/_files/dir1/dir2/dir3');
        rmdir(dirname(__FILE__) . '/_tempFiles/_files/dir1/dir2');
        rmdir(dirname(__FILE__) . '/_tempFiles/_files/dir1');
    }
}

