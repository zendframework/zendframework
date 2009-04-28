<?php
/**
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage UnitTests
 */


/**
 * Zend_Search_Lucene_Storage_File_Filesystem
 */
require_once 'Zend/Search/Lucene/Storage/File/Filesystem.php';

/**
 * Zend_Search_Lucene_Storage_File_Memory
 */
require_once 'Zend/Search/Lucene/Storage/File/Memory.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage UnitTests
 */
class Zend_Search_Lucene_Storage_FileTest extends PHPUnit_Framework_TestCase
{
    public function testFilesystem()
    {
        $file = new Zend_Search_Lucene_Storage_File_Filesystem(dirname(__FILE__) . '/_files/sample_data'); // open file object for reading
        $this->assertTrue($file instanceof  Zend_Search_Lucene_Storage_File);

        $fileSize = filesize(dirname(__FILE__) . '/_files/sample_data');

        $this->assertEquals($file->size(), $fileSize);

        $file->seek(0, SEEK_END);
        $this->assertEquals($file->tell(), $fileSize);

        $file->seek(2, SEEK_SET);
        $this->assertEquals($file->tell(), 2);

        $file->seek(3, SEEK_CUR);
        $this->assertEquals($file->tell(), 5);

        $file->seek(0, SEEK_SET);
        $this->assertEquals($file->tell(), 0);

        $this->assertEquals($file->readByte(),   10);
        $this->assertEquals($file->readBytes(8), "\xFF\x00\xAA\x11\xBB\x44\x66\x99");
        $this->assertEquals($file->readInt(),    49057123);
        $this->assertEquals($file->readLong(),   753823522);
        $this->assertEquals($file->readVInt(),   234586758);
        $this->assertEquals($file->readString(), "UTF-8 string with non-ascii (Cyrillic) symbols\nUTF-8 строка с не-ASCII (кириллическими) символами");
        $this->assertEquals($file->readBinary(), "\xFF\x00\xAA\x11\xBB\x44\x66\x99");

        $file->seek(0);
        $fileData = $file->readBytes($file->size());

        $file->close();
        unset($file);


        $testFName = dirname(__FILE__) . '/_files/sample_data_1';
        $file = new Zend_Search_Lucene_Storage_File_Filesystem($testFName, 'wb');
        $file->lock(LOCK_EX);
        $file->writeByte(10);
        $file->writeBytes("\xFF\x00\xAA\x11\xBB\x44\x66\x99");
        $file->writeInt(49057123);
        $file->writeLong(753823522);
        $file->writeVInt(234586758);
        $file->writeString("UTF-8 string with non-ascii (Cyrillic) symbols\nUTF-8 строка с не-ASCII (кириллическими) символами");
        $file->writeVInt(8); $file->writeBytes("\xFF\x00\xAA\x11\xBB\x44\x66\x99");
        $file->flush();
        $file->unlock();
        $file->close();

        $fh = fopen($testFName, 'rb');
        $this->assertEquals($fileData, fread($fh, filesize($testFName)));
        fclose($fh);

        unlink($testFName);
    }

    public function testMemory()
    {
        $file = new Zend_Search_Lucene_Storage_File_Filesystem(dirname(__FILE__) . '/_files/sample_data');
        $fileData = $file->readBytes($file->size());
        $file->close();
        unset($file);

        $file = new Zend_Search_Lucene_Storage_File_Memory($fileData);
        $this->assertTrue($file instanceof  Zend_Search_Lucene_Storage_File);

        $fileSize = strlen($fileData);

        $file->seek(0, SEEK_END);
        $this->assertEquals($file->tell(), $fileSize);

        $file->seek(2, SEEK_SET);
        $this->assertEquals($file->tell(), 2);

        $file->seek(3, SEEK_CUR);
        $this->assertEquals($file->tell(), 5);

        $file->seek(0, SEEK_SET);
        $this->assertEquals($file->tell(), 0);

        $this->assertEquals($file->readByte(),   10);
        $this->assertEquals($file->readBytes(8), "\xFF\x00\xAA\x11\xBB\x44\x66\x99");
        $this->assertEquals($file->readInt(),    49057123);
        $this->assertEquals($file->readLong(),   753823522);
        $this->assertEquals($file->readVInt(),   234586758);
        $this->assertEquals($file->readString(), "UTF-8 string with non-ascii (Cyrillic) symbols\nUTF-8 строка с не-ASCII (кириллическими) символами");
        $this->assertEquals($file->readBinary(), "\xFF\x00\xAA\x11\xBB\x44\x66\x99");

        // these methods do nothing, but should be provided by object
        $file->lock(LOCK_EX);
        $file->unlock();
    }
}

