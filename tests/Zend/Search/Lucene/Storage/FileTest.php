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

namespace ZendTest\Search\Lucene\Storage;

use Zend\Search\Lucene\Storage\File;

/**
 * @category   Zend
 * @package    Zend_Search_Lucene
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Search_Lucene
 */
class FileTest extends \PHPUnit_Framework_TestCase
{
    public function testFilesystem()
    {
        $file = new File\Filesystem(__DIR__ . '/_files/sample_data'); // open file object for reading
        $this->assertTrue($file instanceof  File\FileInterface);

        $fileSize = filesize(__DIR__ . '/_files/sample_data');

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


        $testFName = __DIR__ . '/_files/sample_data_1';
        $file = new File\Filesystem($testFName, 'wb');
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
        $file = new File\Filesystem(__DIR__ . '/_files/sample_data');
        $fileData = $file->readBytes($file->size());
        $file->close();
        unset($file);

        $file = new File\Memory($fileData);
        $this->assertTrue($file instanceof  File\FileInterface);

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

