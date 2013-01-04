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

use Zend\Filter\File\Rename as FileRename;

/**
 * @category   Zend
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @group      Zend_Filter
 */
class RenameTest extends \PHPUnit_Framework_TestCase
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
    }

    /**
     * Test single parameter filter
     *
     * @return void
     */
    public function testConstructSingleValue()
    {
        $filter = new FileRename($this->_newFile);

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
     * Test single parameter filter
     *
     * @return void
     */
    public function testConstructSingleValueWithFilesArray()
    {
        $filter = new FileRename($this->_newFile);

        $this->assertEquals(
            array(0 => array(
                'source'    => '*',
                'target'    => $this->_newFile,
                'overwrite' => false,
                'randomize' => false,
            )),
            $filter->getFile()
        );
        $this->assertEquals(
            array('tmp_name' => $this->_newFile),
            $filter(array('tmp_name' => $this->_oldFile))
        );
        $this->assertEquals('falsefile', $filter('falsefile'));
    }

    /**
     * Test single array parameter filter
     *
     * @return void
     */
    public function testConstructSingleArray()
    {
        $filter = new FileRename(array(
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
        $filter = new FileRename(array(
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
        $filter = new FileRename(array(
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
        $filter = new FileRename(array(
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
        $filter = new FileRename(array(
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
        $filter = new FileRename($this->_newDir);

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
        $filter = new FileRename(array(
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
        $filter = new FileRename(array(
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
        $filter = new FileRename(array(
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
        $filter = new FileRename(array(
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
        $filter = new FileRename(array(
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
        $filter = new FileRename(array(
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
        $filter = new FileRename(array(
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
        $filter = new FileRename(array(
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
        $filter = new FileRename(array(
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
        $filter = new FileRename($this->_oldFile);
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
        $filter = new FileRename($this->_oldFile);
        $this->setExpectedException('\Zend\Filter\Exception\InvalidArgumentException', 'Invalid options');
        $filter->addFile(1234);
    }

    /**
     * @return void
     */
    public function testInvalidConstruction()
    {
        $this->setExpectedException('\Zend\Filter\Exception\InvalidArgumentException', 'Invalid options');
        $filter = new FileRename(1234);
    }
}
