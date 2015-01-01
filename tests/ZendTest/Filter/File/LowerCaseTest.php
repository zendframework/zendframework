<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace ZendTest\Filter\File;

use Zend\Filter\File\LowerCase as FileLowerCase;

/**
 * @group      Zend_Filter
 */
class LowerCaseTest extends \PHPUnit_Framework_TestCase
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
    public function setUp()
    {
        $this->_filesPath = dirname(__DIR__) . DIRECTORY_SEPARATOR . '_files' . DIRECTORY_SEPARATOR;
        $this->_origFile  = $this->_filesPath . 'testfile2.txt';
        $this->_newFile   = $this->_filesPath . 'newtestfile2.txt';

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
        $filter = new FileLowerCase();
        $filter($this->_newFile);
        $this->assertContains('this is a file', file_get_contents($this->_newFile));
    }

    /**
     * @return void
     */
    public function testNormalWorkflowWithFilesArray()
    {
        $this->assertContains('This is a File', file_get_contents($this->_newFile));
        $filter = new FileLowerCase();
        $filter(array('tmp_name' => $this->_newFile));
        $this->assertContains('this is a file', file_get_contents($this->_newFile));
    }

    /**
     * @return void
     */
    public function testFileNotFoundException()
    {
        $this->setExpectedException('\Zend\Filter\Exception\InvalidArgumentException', 'not found');
        $filter = new FileLowerCase();
        $filter($this->_newFile . 'unknown');
    }

    /**
     * @return void
     */
    public function testCheckSettingOfEncodingInIstance()
    {
        $this->assertContains('This is a File', file_get_contents($this->_newFile));
        try {
            $filter = new FileLowerCase('ISO-8859-1');
            $filter($this->_newFile);
            $this->assertContains('this is a file', file_get_contents($this->_newFile));
        } catch (\Zend\Filter\Exception\ExtensionNotLoadedException $e) {
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
            $filter = new FileLowerCase();
            $filter->setEncoding('ISO-8859-1');
            $filter($this->_newFile);
            $this->assertContains('this is a file', file_get_contents($this->_newFile));
        } catch (\Zend\Filter\Exception\ExtensionNotLoadedException $e) {
            $this->assertContains('mbstring is required', $e->getMessage());
        }
    }

    public function returnUnfilteredDataProvider()
    {
        return array(
            array(null),
            array(new \stdClass()),
            array(array(
                dirname(__DIR__).'/_files/nofile.txt',
                dirname(__DIR__).'/_files/nofile2.txt'
            ))
        );
    }

    /**
     * @dataProvider returnUnfilteredDataProvider
     * @return void
     */
    public function testReturnUnfiltered($input)
    {
        $filter = new FileLowerCase();

        $this->assertEquals($input, $filter($input));
    }
}
