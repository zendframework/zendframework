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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

if (!defined("PHPUnit_MAIN_METHOD")) {
    define("PHPUnit_MAIN_METHOD", "Zend_Filter_File_RenameTest::main");
}

/**
 * Test helper
 */
require_once dirname(__FILE__) . '/../../../TestHelper.php';

/**
 * @see Zend_Filter_File_Rename
 */
require_once 'Zend/Filter/File/Rename.php';

/**
 * @category   Zend
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Filter
 */
class Zend_Filter_File_RenameTest extends PHPUnit_Framework_TestCase
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
     * Runs the test methods of this class.
     *
     * @return void
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_Filter_File_RenameTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }

    /**
     * Sets the path to test files
     *
     * @return void
     */
    public function __construct()
    {
        $this->_filesPath = dirname(__FILE__) . DIRECTORY_SEPARATOR
                          . '..' . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR;
        $this->_origFile  = $this->_filesPath . 'original.file';
        $this->_oldFile   = $this->_filesPath . 'testfile.txt';
        $this->_newFile   = $this->_filesPath . 'newfile.xml';
        $this->_newDir    = $this->_filesPath . DIRECTORY_SEPARATOR . '_testDir2';
        $this->_newDirFile = $this->_newDir . DIRECTORY_SEPARATOR . 'testfile.txt';
    }

    /**
     * Sets the path to test files
     *
     * @return void
     */
    public function setUp()
    {
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
        $filter = new Zend_Filter_File_Rename($this->_newFile);

        $this->assertEquals(array(0 =>
            array('source'    => '*',
                  'target'    => $this->_newFile,
                  'overwrite' => false)),
            $filter->getFile());
        $this->assertEquals($this->_newFile, $filter->filter($this->_oldFile));
        $this->assertEquals('falsefile', $filter->filter('falsefile'));
    }

    /**
     * Test single array parameter filter
     *
     * @return void
     */
    public function testConstructSingleArray()
    {
        $filter = new Zend_Filter_File_Rename(array(
            'source' => $this->_oldFile,
            'target' => $this->_newFile));

        $this->assertEquals(array(0 =>
            array('source'    => $this->_oldFile,
                  'target'    => $this->_newFile,
                  'overwrite' => false)), $filter->getFile());
        $this->assertEquals($this->_newFile, $filter->filter($this->_oldFile));
        $this->assertEquals('falsefile', $filter->filter('falsefile'));
    }

    /**
     * Test full array parameter filter
     *
     * @return void
     */
    public function testConstructFullOptionsArray()
    {
        $filter = new Zend_Filter_File_Rename(array(
            'source' => $this->_oldFile,
            'target' => $this->_newFile,
            'overwrite' => true,
            'unknown'   => false));

        $this->assertEquals(array(0 =>
            array('source'    => $this->_oldFile,
                  'target'    => $this->_newFile,
                  'overwrite' => true)), $filter->getFile());
        $this->assertEquals($this->_newFile, $filter->filter($this->_oldFile));
        $this->assertEquals('falsefile', $filter->filter('falsefile'));
    }

    /**
     * Test single array parameter filter
     *
     * @return void
     */
    public function testConstructDoubleArray()
    {
        $filter = new Zend_Filter_File_Rename(array(
            0 => array(
                'source' => $this->_oldFile,
                'target' => $this->_newFile)));

        $this->assertEquals(array(0 =>
            array('source'    => $this->_oldFile,
                  'target'    => $this->_newFile,
                  'overwrite' => false)), $filter->getFile());
        $this->assertEquals($this->_newFile, $filter->filter($this->_oldFile));
        $this->assertEquals('falsefile', $filter->filter('falsefile'));
    }

    /**
     * Test single array parameter filter
     *
     * @return void
     */
    public function testConstructTruncatedTarget()
    {
        $filter = new Zend_Filter_File_Rename(array(
            'source' => $this->_oldFile));

        $this->assertEquals(array(0 =>
            array('source'    => $this->_oldFile,
                  'target'    => '*',
                  'overwrite' => false)), $filter->getFile());

        $this->assertEquals($this->_oldFile, $filter->filter($this->_oldFile));
        $this->assertEquals('falsefile', $filter->filter('falsefile'));
    }

    /**
     * Test single array parameter filter
     *
     * @return void
     */
    public function testConstructTruncatedSource()
    {
        $filter = new Zend_Filter_File_Rename(array(
            'target' => $this->_newFile));

        $this->assertEquals(array(0 =>
            array('source'    => '*',
                  'target'    => $this->_newFile,
                  'overwrite' => false)), $filter->getFile());

        $this->assertEquals($this->_newFile, $filter->filter($this->_oldFile));
        $this->assertEquals('falsefile', $filter->filter('falsefile'));
    }

    /**
     * Test single parameter filter by using directory only
     *
     * @return void
     */
    public function testConstructSingleDirectory()
    {
        $filter = new Zend_Filter_File_Rename($this->_newDir);

        $this->assertEquals(array(0 =>
            array('source'    => '*',
                  'target'    => $this->_newDir,
                  'overwrite' => false)), $filter->getFile());
        $this->assertEquals($this->_newDirFile, $filter->filter($this->_oldFile));
        $this->assertEquals('falsefile', $filter->filter('falsefile'));
    }

    /**
     * Test single array parameter filter by using directory only
     *
     * @return void
     */
    public function testConstructSingleArrayDirectory()
    {
        $filter = new Zend_Filter_File_Rename(array(
            'source' => $this->_oldFile,
            'target' => $this->_newDir));

        $this->assertEquals(array(0 =>
            array('source'    => $this->_oldFile,
                  'target'    => $this->_newDir,
                  'overwrite' => false)), $filter->getFile());
        $this->assertEquals($this->_newDirFile, $filter->filter($this->_oldFile));
        $this->assertEquals('falsefile', $filter->filter('falsefile'));
    }

    /**
     * Test single array parameter filter by using directory only
     *
     * @return void
     */
    public function testConstructDoubleArrayDirectory()
    {
        $filter = new Zend_Filter_File_Rename(array(
            0 => array(
                'source' => $this->_oldFile,
                'target' => $this->_newDir)));

        $this->assertEquals(array(0 =>
            array('source'    => $this->_oldFile,
                  'target'    => $this->_newDir,
                  'overwrite' => false)), $filter->getFile());
        $this->assertEquals($this->_newDirFile, $filter->filter($this->_oldFile));
        $this->assertEquals('falsefile', $filter->filter('falsefile'));
    }

    /**
     * Test single array parameter filter by using directory only
     *
     * @return void
     */
    public function testConstructTruncatedSourceDirectory()
    {
        $filter = new Zend_Filter_File_Rename(array(
            'target' => $this->_newDir));

        $this->assertEquals(array(0 =>
            array('source'    => '*',
                  'target'    => $this->_newDir,
                  'overwrite' => false)), $filter->getFile());

        $this->assertEquals($this->_newDirFile, $filter->filter($this->_oldFile));
        $this->assertEquals('falsefile', $filter->filter('falsefile'));
    }

    /**
     * @return void
     */
    public function testAddSameFileAgainAndOverwriteExistingTarget()
    {
        $filter = new Zend_Filter_File_Rename(array(
            'source' => $this->_oldFile,
            'target' => $this->_newDir));

        $filter->addFile(array(
            'source' => $this->_oldFile,
            'target' => $this->_newFile));

        $this->assertEquals(array(0 =>
            array('source'    => $this->_oldFile,
                  'target'    => $this->_newFile,
                  'overwrite' => false)), $filter->getFile());
        $this->assertEquals($this->_newFile, $filter->filter($this->_oldFile));
        $this->assertEquals('falsefile', $filter->filter('falsefile'));
    }

    /**
     * @return void
     */
    public function testGetNewName()
    {
        $filter = new Zend_Filter_File_Rename(array(
            'source' => $this->_oldFile,
            'target' => $this->_newDir));

        $this->assertEquals(array(0 =>
            array('source'    => $this->_oldFile,
                  'target'    => $this->_newDir,
                  'overwrite' => false)), $filter->getFile());
        $this->assertEquals($this->_newDirFile, $filter->getNewName($this->_oldFile));
    }

    /**
     * @return void
     */
    public function testGetNewNameExceptionWithExistingFile()
    {
        $filter = new Zend_Filter_File_Rename(array(
            'source' => $this->_oldFile,
            'target' => $this->_newFile));

        copy($this->_oldFile, $this->_newFile);

        $this->assertEquals(array(0 =>
            array('source'    => $this->_oldFile,
                  'target'    => $this->_newFile,
                  'overwrite' => false)), $filter->getFile());
        try {
            $this->assertEquals($this->_newFile, $filter->getNewName($this->_oldFile));
            $this->fail();
        } catch (Zend_Filter_Exception $e) {
            $this->assertContains('could not be renamed', $e->getMessage());
        }
    }

    /**
     * @return void
     */
    public function testGetNewNameOverwriteWithExistingFile()
    {
        $filter = new Zend_Filter_File_Rename(array(
            'source'    => $this->_oldFile,
            'target'    => $this->_newFile,
            'overwrite' => true));

        copy($this->_oldFile, $this->_newFile);

        $this->assertEquals(array(0 =>
            array('source'    => $this->_oldFile,
                  'target'    => $this->_newFile,
                  'overwrite' => true)), $filter->getFile());
        $this->assertEquals($this->_newFile, $filter->getNewName($this->_oldFile));
    }

    /**
     * @return void
     */
    public function testAddFileWithString()
    {
        $filter = new Zend_Filter_File_Rename($this->_oldFile);
        $filter->addFile($this->_newFile);

        $this->assertEquals(array(0 =>
            array('source'    => '*',
                  'target'    => $this->_newFile,
                  'overwrite' => false)), $filter->getFile());
        $this->assertEquals($this->_newFile, $filter->filter($this->_oldFile));
        $this->assertEquals('falsefile', $filter->filter('falsefile'));
    }

    /**
     * @return void
     */
    public function testAddFileWithInvalidOption()
    {
        $filter = new Zend_Filter_File_Rename($this->_oldFile);
        try {
            $filter->addFile(1234);
            $this->fail();
        } catch (Zend_Filter_Exception $e) {
            $this->assertContains('Invalid options', $e->getMessage());
        }
    }

    /**
     * @return void
     */
    public function testInvalidContruction()
    {
        try {
            $filter = new Zend_Filter_File_Rename(1234);
            $this->fail();
        } catch (Zend_Filter_Exception $e) {
            $this->assertContains('Invalid options', $e->getMessage());
        }
    }
}

if (PHPUnit_MAIN_METHOD == "Zend_Filter_File_RenameTest::main") {
    Zend_Filter_File_RenameTest::main();
}
