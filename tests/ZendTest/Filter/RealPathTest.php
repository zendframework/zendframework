<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Filter
 */

namespace ZendTest\Filter;

use Zend\Filter\RealPath as RealPathFilter;

/**
 * @category   Zend
 * @package    Zend_Filter
 * @subpackage UnitTests
 * @group      Zend_Filter
 */
class RealPathTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Path to test files
     *
     * @var string
     */
    protected $_filesPath;

    /**
     * Zend_Filter_Basename object
     *
     * @var Zend_Filter_Basename
     */
    protected $_filter;

    /**
     * Creates a new Zend_Filter_Basename object for each test method
     *
     * @return void
     */
    public function setUp()
    {
        $this->_filesPath = __DIR__ . DIRECTORY_SEPARATOR . '_files';
        $this->_filter    = new RealPathFilter();
    }

    /**
     * Ensures expected behavior for existing file
     *
     * @return void
     */
    public function testFileExists()
    {
        $filter   = $this->_filter;
        $filename = 'file.1';
        $this->assertContains($filename, $filter($this->_filesPath . DIRECTORY_SEPARATOR . $filename));
    }

    /**
     * Ensures expected behavior for nonexistent file
     *
     * @return void
     */
    public function testFileNonexistent()
    {
        $filter = $this->_filter;
        $path   = '/path/to/nonexistent';
        if (false !== strpos(PHP_OS, 'BSD')) {
            $this->assertEquals($path, $filter($path));
        } else {
            $this->assertEquals(false, $filter($path));
        }
    }

    /**
     * @return void
     */
    public function testGetAndSetExistsParameter()
    {
        $this->assertTrue($this->_filter->getExists());
        $this->_filter->setExists(false);
        $this->assertFalse($this->_filter->getExists());

        $this->_filter->setExists(array('unknown'));
        $this->assertTrue($this->_filter->getExists());
    }

    /**
     * @return void
     */
    public function testNonExistantPath()
    {
        $filter = $this->_filter;
        $filter->setExists(false);

        $path = __DIR__ . DIRECTORY_SEPARATOR . '_files';
        $this->assertEquals($path, $filter($path));

        $path2 = __DIR__ . DIRECTORY_SEPARATOR . '_files'
               . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '_files';
        $this->assertEquals($path, $filter($path2));

        $path3 = __DIR__ . DIRECTORY_SEPARATOR . '_files'
               . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '.'
               . DIRECTORY_SEPARATOR . '_files';
        $this->assertEquals($path, $filter($path3));
    }
}
