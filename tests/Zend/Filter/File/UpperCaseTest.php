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

/**
 * Test helper
 */

/**
 * @see Zend_Filter_File_UpperCase
 */

/**
 * @category   Zend
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Filter
 */
class Zend_Filter_File_UpperCaseTest extends PHPUnit_Framework_TestCase
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
    protected $_newFile;

    /**
     * Sets the path to test files
     *
     * @return void
     */
    public function __construct()
    {
        $this->_filesPath = dirname(__FILE__) . DIRECTORY_SEPARATOR
                          . '..' . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR;
        $this->_origFile  = $this->_filesPath . 'testfile2.txt';
        $this->_newFile   = $this->_filesPath . 'newtestfile2.txt';
    }

    /**
     * Sets the path to test files
     *
     * @return void
     */
    public function setUp()
    {
        if (!file_exists($this->_newFile)) {
            copy($this->_origFile, $this->_newFile);
        }
    }

    /**
     * Sets the path to test files
     *
     * @return void
     */
    public function tearDown()
    {
        if (file_exists($this->_newFile)) {
            unlink($this->_newFile);
        }
    }

    /**
     * @return void
     */
    public function testInstanceCreationAndNormalWorkflow()
    {
        $this->assertContains('This is a File', file_get_contents($this->_newFile));
        $filter = new Zend_Filter_File_UpperCase();
        $filter->filter($this->_newFile);
        $this->assertContains('THIS IS A FILE', file_get_contents($this->_newFile));
    }

    /**
     * @return void
     */
    public function testFileNotFoundException()
    {
        try {
            $filter = new Zend_Filter_File_UpperCase();
            $filter->filter($this->_newFile . 'unknown');
            $this->fail('Unknown file exception expected');
        } catch (Zend_Filter_Exception $e) {
            $this->assertContains('not found', $e->getMessage());
        }
    }

    /**
     * @return void
     */
    public function testCheckSettingOfEncodingInIstance()
    {
        $this->assertContains('This is a File', file_get_contents($this->_newFile));
        try {
            $filter = new Zend_Filter_File_UpperCase('ISO-8859-1');
            $filter->filter($this->_newFile);
            $this->assertContains('THIS IS A FILE', file_get_contents($this->_newFile));
        } catch (Zend_Filter_Exception $e) {
            $this->assertContains('mbstring is required', $e->getMessage());
        }
    }

    /**
     * @return void
     */
    public function testCheckSettingOfEncodingWithMethod()
    {
        $this->assertContains('This is a File', file_get_contents($this->_newFile));
        try {
            $filter = new Zend_Filter_File_UpperCase();
            $filter->setEncoding('ISO-8859-1');
            $filter->filter($this->_newFile);
            $this->assertContains('THIS IS A FILE', file_get_contents($this->_newFile));
        } catch (Zend_Filter_Exception $e) {
            $this->assertContains('mbstring is required', $e->getMessage());
        }
    }
}
