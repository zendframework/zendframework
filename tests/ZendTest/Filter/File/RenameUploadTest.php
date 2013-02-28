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

use Zend\Filter\File\RenameUpload as FileRenameUpload;

/**
 * @category   Zend
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @group      Zend_Filter
 */
class RenameUploadTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Path to test files
     *
     * @var string
     */
    protected $_filesPath;

    /**
     * Testfile
     *
     * @var string
     */
    protected $_oldFile;

    /**
     * Testfile
     *
     * @var string
     */
    protected $_newFile;

    /**
     * Testdirectory
     *
     * @var string
     */
    protected $_newDir;

    /**
     * Testfile in Testdirectory
     *
     * @var string
     */
    protected $_newDirFile;

    /**
     * Sets the path to test files
     *
     * @return void
     */
    public function setUp()
    {
        $this->_filesPath  = dirname(__DIR__) . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR . 'RenameUploadTest';
        $this->_newDir = $this->_filesPath . DIRECTORY_SEPARATOR . '_testDir2';

        $this->tearDown();

        mkdir($this->_filesPath);
        mkdir($this->_newDir);

        $this->_oldFile    = $this->_filesPath . '/testfile.txt';
        $this->_newFile    = $this->_filesPath . '/newfile.xml';
        $this->_newDirFile = $this->_newDir . '/testfile.txt';

        touch($this->_oldFile);
    }

    /**
     * Sets the path to test files
     *
     * @return void
     */
    public function tearDown()
    {
        $this->removeDir($this->_newDir);
        $this->removeDir($this->_filesPath);
    }

    protected function removeDir($dir)
    {
        if (! is_dir($dir)) {
            return;
        }

        foreach (glob($dir . DIRECTORY_SEPARATOR . '*') as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }

        rmdir($dir);
    }

    /**
     * Test single parameter filter
     *
     * @return void
     */
    public function testThrowsExceptionWithNonUploadedFile()
    {
        $filter = new FileRenameUpload($this->_newFile);
        $this->assertEquals($this->_newFile, $filter->getTarget());
        $this->assertEquals('falsefile', $filter('falsefile'));
        $this->setExpectedException(
            'Zend\Filter\Exception\RuntimeException', 'could not be renamed'
        );
        $this->assertEquals($this->_newFile, $filter($this->_oldFile));
    }

    /**
     * @return void
     */
    public function testOptions()
    {
        $filter = new FileRenameUpload($this->_newFile);
        $this->assertEquals($this->_newFile, $filter->getTarget());
        $this->assertFalse($filter->getUseUploadName());
        $this->assertFalse($filter->getOverwrite());
        $this->assertFalse($filter->getRandomize());

        $filter = new FileRenameUpload(array(
            'target'          => $this->_oldFile,
            'use_upload_name' => true,
            'overwrite'       => true,
            'randomize'       => true,
        ));
        $this->assertEquals($this->_oldFile, $filter->getTarget());
        $this->assertTrue($filter->getUseUploadName());
        $this->assertTrue($filter->getOverwrite());
        $this->assertTrue($filter->getRandomize());
    }

    /**
     * @return void
     */
    public function testStringConstructorParam()
    {
        $filter = new RenameUploadMock($this->_newFile);
        $this->assertEquals($this->_newFile, $filter->getTarget());
        $this->assertEquals($this->_newFile, $filter($this->_oldFile));
        $this->assertEquals('falsefile', $filter('falsefile'));
    }

    /**
     * @return void
     */
    public function testStringConstructorWithFilesArray()
    {
        $filter = new RenameUploadMock($this->_newFile);
        $this->assertEquals($this->_newFile, $filter->getTarget());
        $this->assertEquals(
            array(
                'tmp_name' => $this->_newFile,
                'name'     => $this->_newFile,
            ),
            $filter(array(
                'tmp_name' => $this->_oldFile,
                'name' => $this->_newFile,
            ))
        );
        $this->assertEquals('falsefile', $filter('falsefile'));
    }

    /**
     * @return void
     */
    public function testArrayConstructorParam()
    {
        $filter = new RenameUploadMock(array(
            'target' => $this->_newFile,
        ));
        $this->assertEquals($this->_newFile, $filter->getTarget());
        $this->assertEquals($this->_newFile, $filter($this->_oldFile));
        $this->assertEquals('falsefile', $filter('falsefile'));
    }

    /**
     * @return void
     */
    public function testConstructTruncatedTarget()
    {
        $filter = new FileRenameUpload('*');
        $this->assertEquals('*', $filter->getTarget());
        $this->assertEquals($this->_oldFile, $filter($this->_oldFile));
        $this->assertEquals('falsefile', $filter('falsefile'));
    }

    /**
     * @return void
     */
    public function testTargetDirectory()
    {
        $filter = new RenameUploadMock($this->_newDir);
        $this->assertEquals($this->_newDir, $filter->getTarget());
        $this->assertEquals($this->_newDirFile, $filter($this->_oldFile));
        $this->assertEquals('falsefile', $filter('falsefile'));
    }

    /**
     * @return void
     */
    public function testOverwriteWithExistingFile()
    {
        $filter = new RenameUploadMock(array(
            'target'          => $this->_newFile,
            'overwrite'       => true,
        ));

        copy($this->_oldFile, $this->_newFile);

        $this->assertEquals($this->_newFile, $filter->getTarget());
        $this->assertEquals($this->_newFile, $filter($this->_oldFile));
    }

    /**
     * @return void
     */
    public function testCannotOverwriteExistingFile()
    {
        $filter = new RenameUploadMock(array(
            'target'          => $this->_newFile,
            'overwrite'       => false,
        ));

        copy($this->_oldFile, $this->_newFile);

        $this->assertEquals($this->_newFile, $filter->getTarget());
        $this->assertFalse($filter->getOverwrite());
        $this->setExpectedException(
            'Zend\Filter\Exception\InvalidArgumentException', 'already exists'
        );
        $this->assertEquals($this->_newFile, $filter($this->_oldFile));
    }

    /**
     * @return void
     */
    public function testGetRandomizedFile()
    {
        $fileNoExt = $this->_filesPath . '/newfile';
        $filter = new RenameUploadMock(array(
            'target'          => $this->_newFile,
            'randomize'       => true,
        ));

        $this->assertRegExp('#' . $fileNoExt . '_.{13}\.xml#', $filter($this->_oldFile));
    }

    /**
     * @return void
     */
    public function testGetRandomizedFileWithoutExtension()
    {
        $fileNoExt = $this->_filesPath . '/newfile';
        $filter = new RenameUploadMock(array(
            'target'          => $fileNoExt,
            'randomize'       => true,
        ));

        $this->assertRegExp('#' . $fileNoExt . '_.{13}#', $filter($this->_oldFile));
    }

    /**
     * @return void
     */
    public function testInvalidConstruction()
    {
        $this->setExpectedException('\Zend\Filter\Exception\InvalidArgumentException', 'Invalid target');
        $filter = new FileRenameUpload(1234);
    }

    /**
     * @return void
     */
    public function testCanFilterMultipleTimesWithSameResult()
    {
        $filter = new RenameUploadMock(array(
            'target'          => $this->_newFile,
            'randomize'       => true,
        ));

        $firstResult = $filter($this->_oldFile);

        $this->assertContains('newfile', $firstResult);

        $secondResult = $filter($this->_oldFile);

        $this->assertSame($firstResult, $secondResult);
    }
}
