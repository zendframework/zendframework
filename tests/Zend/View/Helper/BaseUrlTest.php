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
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\View\Helper;

use PHPUnit_Framework_TestCase as TestCase,
    Zend\View\Helper;

/**
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
class BaseUrlTest extends TestCase
{
    /**
     * Previous baseUrl before changing
     *
     * @var string
     */
    protected $_previousBaseUrl;

    /**
     * Server backup
     *
     * @var array
     */
    protected $_server;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        $this->_server = $_SERVER;
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $_SERVER = $this->_server;
    }

    /**
     * Test and make sure if paths given without / prefix are fixed
     *
     */
    public function testBaseUrlIsCorrectingFilePath()
    {
        $baseUrls = array(
            ''             => '/file.js',
            '/subdir'      => '/subdir/file.js',
            '/sub/sub/dir' => '/sub/sub/dir/file.js',
        );

        foreach ($baseUrls as $baseUrl => $val) {
            $helper = new Helper\BaseUrl();
            $helper->setBaseUrl($baseUrl);

            $this->assertEquals($val, $helper('file.js'));
        }
    }

    /**
     * Test and make sure baseUrl appended with file works
     *
     */
    public function testBaseUrlIsAppendedWithFile()
    {
        $baseUrls = array(
            ''             => '/file.js',
            '/subdir'      => '/subdir/file.js',
            '/sub/sub/dir' => '/sub/sub/dir/file.js',
        );

        foreach ($baseUrls as $baseUrl => $val) {
            $helper = new Helper\BaseUrl();
            $helper->setBaseUrl($baseUrl);

            $this->assertEquals($val, $helper('/file.js'));
        }
    }

    /**
     * Test and makes sure that baseUrl appended with path works
     *
     */
    public function testBaseUrlIsAppendedWithPath()
    {
        $baseUrls = array(
            ''             => '/path/bar',
            '/subdir'      => '/subdir/path/bar',
            '/sub/sub/dir' => '/sub/sub/dir/path/bar',
        );

        foreach ($baseUrls as $baseUrl => $val) {
            $helper = new Helper\BaseUrl();
            $helper->setBaseUrl($baseUrl);

            $this->assertEquals($val, $helper('/path/bar'));
        }
    }

    /**
     * Test and makes sure that baseUrl appended with root path
     *
     */
    public function testBaseUrlIsAppendedWithRootPath()
    {
        $baseUrls = array(
            ''     => '/',
            '/foo' => '/foo/'
        );

        foreach ($baseUrls as $baseUrl => $val) {
            $helper = new Helper\BaseUrl();
            $helper->setBaseUrl($baseUrl);

            $this->assertEquals($val, $helper('/'));
        }
    }

    public function testSetBaseUrlModifiesBaseUrl()
    {
        $helper = new Helper\BaseUrl();
        $helper->setBaseUrl('/myfoo');
        $this->assertEquals('/myfoo', $helper->getBaseUrl());
    }

    public function testGetBaseUrlReturnsBaseUrl()
    {
        $helper = new Helper\BaseUrl();
        $helper->setBaseUrl('/mybar');
        $this->assertEquals('/mybar', $helper->getBaseUrl());
    }

    public function testGetBaseUrlReturnsBaseUrlWithoutScriptName()
    {
        $_SERVER['SCRIPT_NAME'] = '/foo/bar/bat/mybar/index.php';
        $helper = new Helper\BaseUrl();
        $helper->setBaseUrl('/mybar/index.php');
        $this->assertEquals('/mybar', $helper->getBaseUrl());
    }
}
