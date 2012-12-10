<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
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
     * Original testfile
     *
     * @var string
     */
    protected $_origFile;

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
        $this->_filesPath  = dirname(__DIR__) . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR;
        $this->_origFile   = $this->_filesPath . 'original.file';
        $this->_oldFile    = $this->_filesPath . 'testfile.txt';
        $this->_newFile    = $this->_filesPath . 'newfile.xml';
        $this->_newDir     = $this->_filesPath . DIRECTORY_SEPARATOR . '_testDir2';
        $this->_newDirFile = $this->_newDir . DIRECTORY_SEPARATOR . 'testfile.txt';

        if (file_exists($this->_origFile)) {
            unlink($this->_origFile);
        }

        if (file_exists($this->_newFile)) {
            unlink($this->_newFile);
        }

        if (file_exists($this->_newDirFile)) {
            unlink($this->_newDirFile);
        }

        copy($this->_oldFile, $this->_origFile);
    }

    /**
     * Sets the path to test files
     *
     * @return void
     */
    public function tearDown()
    {
        if (!file_exists($this->_oldFile)) {
            copy($this->_origFile, $this->_oldFile);
        }

        if (file_exists($this->_origFile)) {
            unlink($this->_origFile);
        }

        if (file_exists($this->_newFile)) {
            unlink($this->_newFile);
        }

        if (file_exists($this->_newDirFile)) {
            unlink($this->_newDirFile);
        }

        if (function_exists("runkit_function_rename")
            && function_exists('move_uploaded_file_orig')
        ) {
            runkit_function_rename('move_uploaded_file',      'move_uploaded_file_mock');
            runkit_function_rename('move_uploaded_file_orig', 'move_uploaded_file');
        }
    }

    /**
     * Test single parameter filter
     *
     * @return void
     */
    public function testThrowsExceptionWithNonUploadedFile()
    {
        $filter = new FileRenameUpload($this->_newFile);

        $this->assertEquals(
            array(0 => array(
                'source'    => '*',
                'target'    => $this->_newFile,
                'overwrite' => false,
                'randomize' => false,
            )),
            $filter->getFile()
        );
        $this->assertEquals('falsefile', $filter('falsefile'));
        $this->setExpectedException(
            'Zend\Filter\Exception\RuntimeException', 'could not be renamed'
        );
        $this->assertEquals($this->_newFile, $filter($this->_oldFile));
    }

    /**
     * Mock the move_uploaded_file() function with rename() functionality
     *
     * @return void
     */
    protected function setUpMockMoveUploadedFile()
    {
        if (!function_exists("runkit_function_rename")
            || !ini_get('runkit.internal_override')
        ) {
            $this->markTestSkipped(
                'move_uploaded_file cannot be unit tested without runkit module'
            );
            return;
        }
        runkit_function_rename('move_uploaded_file',      'move_uploaded_file_orig');
        runkit_function_rename('move_uploaded_file_mock', 'move_uploaded_file');
    }

    /**
     * Test single parameter filter
     *
     * @return void
     */
    public function testConstructSingleValue()
    {
        $this->setUpMockMoveUploadedFile();

        $filter = new FileRenameUpload($this->_newFile);

        $this->assertEquals(
            array(0 => array(
                'source'    => '*',
                'target'    => $this->_newFile,
                'overwrite' => false,
                'randomize' => false,
            )),
            $filter->getFile()
        );
        $this->assertEquals($this->_newFile, $filter($this->_oldFile));
        $this->assertEquals('falsefile', $filter('falsefile'));
    }

    /**
     * Test single array parameter filter
     *
     * @return void
     */
    public function testConstructSingleArray()
    {
        $this->setUpMockMoveUploadedFile();

        $filter = new FileRenameUpload(array(
            'source' => $this->_oldFile,
            'target' => $this->_newFile));

        $this->assertEquals(
            array(0 => array(
                'source'    => $this->_oldFile,
                'target'    => $this->_newFile,
                'overwrite' => false,
                'randomize' => false,
            )),
            $filter->getFile()
        );
        $this->assertEquals($this->_newFile, $filter($this->_oldFile));
        $this->assertEquals('falsefile', $filter('falsefile'));
    }

    /**
     * Test full array parameter filter
     *
     * @return void
     */
    public function testConstructFullOptionsArray()
    {
        $this->setUpMockMoveUploadedFile();

        $filter = new FileRenameUpload(array(
            'source' => $this->_oldFile,
            'target' => $this->_newFile,
            'overwrite' => true,
            'randomize' => false,
            'unknown'   => false
        ));

        $this->assertEquals(
            array(0 => array(
                'source'    => $this->_oldFile,
                'target'    => $this->_newFile,
                'overwrite' => true,
                'randomize' => false,
            )),
            $filter->getFile()
        );
        $this->assertEquals($this->_newFile, $filter($this->_oldFile));
        $this->assertEquals('falsefile', $filter('falsefile'));
    }

    /**
     * Test single array parameter filter
     *
     * @return void
     */
    public function testConstructDoubleArray()
    {
        $this->setUpMockMoveUploadedFile();

        $filter = new FileRenameUpload(array(
            0 => array(
                'source' => $this->_oldFile,
                'target' => $this->_newFile)));

        $this->assertEquals(
            array(0 => array(
                'source'    => $this->_oldFile,
                'target'    => $this->_newFile,
                'overwrite' => false,
                'randomize' => false,
            )),
            $filter->getFile()
        );
        $this->assertEquals($this->_newFile, $filter($this->_oldFile));
        $this->assertEquals('falsefile', $filter('falsefile'));
    }

    /**
     * Test single array parameter filter
     *
     * @return void
     */
    public function testConstructTruncatedTarget()
    {
        $filter = new FileRenameUpload(array(
            'source' => $this->_oldFile));

        $this->assertEquals(
            array(0 => array(
                'source'    => $this->_oldFile,
                'target'    => '*',
                'overwrite' => false,
                'randomize' => false,
            )),
            $filter->getFile()
        );
        $this->assertEquals($this->_oldFile, $filter($this->_oldFile));
        $this->assertEquals('falsefile', $filter('falsefile'));
    }

    /**
     * Test single array parameter filter
     *
     * @return void
     */
    public function testConstructTruncatedSource()
    {
        $this->setUpMockMoveUploadedFile();

        $filter = new FileRenameUpload(array(
            'target' => $this->_newFile));

        $this->assertEquals(
            array(0 => array(
                'source'    => '*',
                'target'    => $this->_newFile,
                'overwrite' => false,
                'randomize' => false,
            )),
            $filter->getFile()
        );
        $this->assertEquals($this->_newFile, $filter($this->_oldFile));
        $this->assertEquals('falsefile', $filter('falsefile'));
    }

    /**
     * Test single parameter filter by using directory only
     *
     * @return void
     */
    public function testConstructSingleDirectory()
    {
        $this->setUpMockMoveUploadedFile();

        $filter = new FileRenameUpload($this->_newDir);

        $this->assertEquals(
            array(0 => array(
                'source'    => '*',
                'target'    => $this->_newDir,
                'overwrite' => false,
                'randomize' => false,
            )),
            $filter->getFile()
        );
        $this->assertEquals($this->_newDirFile, $filter($this->_oldFile));
        $this->assertEquals('falsefile', $filter('falsefile'));
    }

    /**
     * Test single array parameter filter by using directory only
     *
     * @return void
     */
    public function testConstructSingleArrayDirectory()
    {
        $this->setUpMockMoveUploadedFile();

        $filter = new FileRenameUpload(array(
            'source' => $this->_oldFile,
            'target' => $this->_newDir));

        $this->assertEquals(
            array(0 => array(
                'source'    => $this->_oldFile,
                'target'    => $this->_newDir,
                'overwrite' => false,
                'randomize' => false,
            )),
            $filter->getFile()
        );
        $this->assertEquals($this->_newDirFile, $filter($this->_oldFile));
        $this->assertEquals('falsefile', $filter('falsefile'));
    }

    /**
     * Test single array parameter filter by using directory only
     *
     * @return void
     */
    public function testConstructDoubleArrayDirectory()
    {
        $this->setUpMockMoveUploadedFile();

        $filter = new FileRenameUpload(array(
            0 => array(
                'source' => $this->_oldFile,
                'target' => $this->_newDir)));

        $this->assertEquals(
            array(0 => array(
                'source'    => $this->_oldFile,
                'target'    => $this->_newDir,
                'overwrite' => false,
                'randomize' => false,
            )),
            $filter->getFile()
        );
        $this->assertEquals($this->_newDirFile, $filter($this->_oldFile));
        $this->assertEquals('falsefile', $filter('falsefile'));
    }

    /**
     * Test single array parameter filter by using directory only
     *
     * @return void
     */
    public function testConstructTruncatedSourceDirectory()
    {
        $this->setUpMockMoveUploadedFile();

        $filter = new FileRenameUpload(array(
            'target' => $this->_newDir));

        $this->assertEquals(
            array(0 => array(
                'source'    => '*',
                'target'    => $this->_newDir,
                'overwrite' => false,
                'randomize' => false,
            )),
            $filter->getFile()
        );
        $this->assertEquals($this->_newDirFile, $filter($this->_oldFile));
        $this->assertEquals('falsefile', $filter('falsefile'));
    }

    /**
     * @return void
     */
    public function testAddSameFileAgainAndOverwriteExistingTarget()
    {
        $this->setUpMockMoveUploadedFile();

        $filter = new FileRenameUpload(array(
            'source' => $this->_oldFile,
            'target' => $this->_newDir));

        $filter->addFile(array(
            'source' => $this->_oldFile,
            'target' => $this->_newFile));

        $this->assertEquals(
            array(0 => array(
                'source'    => $this->_oldFile,
                'target'    => $this->_newFile,
                'overwrite' => false,
                'randomize' => false,
            )),
            $filter->getFile()
        );
        $this->assertEquals($this->_newFile, $filter($this->_oldFile));
        $this->assertEquals('falsefile', $filter('falsefile'));
    }

    /**
     * @return void
     */
    public function testGetNewName()
    {
        $filter = new FileRenameUpload(array(
            'source' => $this->_oldFile,
            'target' => $this->_newDir));

        $this->assertEquals(
            array(0 => array(
                'source'    => $this->_oldFile,
                'target'    => $this->_newDir,
                'overwrite' => false,
                'randomize' => false,
            )),
            $filter->getFile()
        );
        $this->assertEquals($this->_newDirFile, $filter->getNewName($this->_oldFile));
    }

    /**
     * @return void
     */
    public function testGetNewNameExceptionWithExistingFile()
    {
        $filter = new FileRenameUpload(array(
            'source' => $this->_oldFile,
            'target' => $this->_newFile));

        copy($this->_oldFile, $this->_newFile);

        $this->assertEquals(
            array(0 => array(
                'source'    => $this->_oldFile,
                'target'    => $this->_newFile,
                'overwrite' => false,
                'randomize' => false,
            )),
            $filter->getFile()
        );
        $this->setExpectedException('\Zend\Filter\Exception\InvalidArgumentException', 'could not be renamed');
        $this->assertEquals($this->_newFile, $filter->getNewName($this->_oldFile));
    }

    /**
     * @return void
     */
    public function testGetNewNameOverwriteWithExistingFile()
    {
        $filter = new FileRenameUpload(array(
            'source'    => $this->_oldFile,
            'target'    => $this->_newFile,
            'overwrite' => true));

        copy($this->_oldFile, $this->_newFile);

        $this->assertEquals(
            array(0 => array(
                'source'    => $this->_oldFile,
                'target'    => $this->_newFile,
                'overwrite' => true,
                'randomize' => false,
            )),
            $filter->getFile()
        );
        $this->assertEquals($this->_newFile, $filter->getNewName($this->_oldFile));
    }

    /**
     * @return void
     */
    public function testGetRandomizedFile()
    {
        $filter = new FileRenameUpload(array(
            'source'    => $this->_oldFile,
            'target'    => $this->_newFile,
            'randomize' => true
        ));

        $this->assertEquals(
            array(0 => array(
                'source'    => $this->_oldFile,
                'target'    => $this->_newFile,
                'overwrite' => false,
                'randomize' => true,
            )),
            $filter->getFile()
        );
        $fileNoExt = $this->_filesPath . 'newfile';
        $this->assertRegExp('#' . $fileNoExt . '_.{13}\.xml#', $filter->getNewName($this->_oldFile));
    }

    /**
     * @return void
     */
    public function testGetRandomizedFileWithoutExtension()
    {
        $fileNoExt = $this->_filesPath . 'newfile';
        $filter = new FileRenameUpload(array(
            'source'    => $this->_oldFile,
            'target'    => $fileNoExt,
            'randomize' => true
        ));

        $this->assertEquals(
            array(0 => array(
                'source'    => $this->_oldFile,
                'target'    => $fileNoExt,
                'overwrite' => false,
                'randomize' => true,
            )),
            $filter->getFile()
        );
        $this->assertRegExp('#' . $fileNoExt . '_.{13}#', $filter->getNewName($this->_oldFile));
    }

    /**
     * @return void
     */
    public function testAddFileWithString()
    {
        $this->setUpMockMoveUploadedFile();

        $filter = new FileRenameUpload($this->_oldFile);
        $filter->addFile($this->_newFile);

        $this->assertEquals(
            array(0 => array(
                'source'    => '*',
                'target'    => $this->_newFile,
                'overwrite' => false,
                'randomize' => false,
            )),
            $filter->getFile()
        );
        $this->assertEquals($this->_newFile, $filter($this->_oldFile));
        $this->assertEquals('falsefile', $filter('falsefile'));
    }

    /**
     * @return void
     */
    public function testAddFileWithInvalidOption()
    {
        $filter = new FileRenameUpload($this->_oldFile);
        $this->setExpectedException('\Zend\Filter\Exception\InvalidArgumentException', 'Invalid options');
        $filter->addFile(1234);
    }

    /**
     * @return void
     */
    public function testInvalidConstruction()
    {
        $this->setExpectedException('\Zend\Filter\Exception\InvalidArgumentException', 'Invalid options');
        $filter = new FileRenameUpload(1234);
    }
}
