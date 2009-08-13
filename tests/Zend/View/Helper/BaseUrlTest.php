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
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id:$
 */

// Call Zend_View_Helper_BaseUrlTest::main() if this source file is executed directly.
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'Zend_View_Helper_BaseUrlTest::main');
}
require_once dirname(__FILE__) . '/../../../TestHelper.php';

/**
 * @see Zend_View_Helper_BaseUrl
 */
require_once 'Zend/View/Helper/BaseUrl.php';

/**
 * @see Zend_Controller_Front
 */
require_once 'Zend/Controller/Front.php';

/**
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
class Zend_View_Helper_BaseUrlTest extends PHPUnit_Framework_TestCase
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
     * Main
     */
    public static function main()
    {
        $suite  = new PHPUnit_Framework_TestSuite("Zend_View_Helper_BaseUrlTest");
        $result = PHPUnit_TextUI_TestRunner::run($suite);
    }
    
    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        $this->_previousBaseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
        $this->_server = $_SERVER;
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        Zend_Controller_Front::getInstance()->setBaseUrl($this->_previousBaseUrl);
        Zend_Controller_Front::getInstance()->resetInstance();

        $_SERVER = $this->_server;
    }

    /**
     * Test and make sure base url returned is consistent with the FC
     *
     */
    public function testBaseUrlIsSameAsFrontController()
    {
        $baseUrls = array('', '/subdir', '/subdir/', '/sub/sub/dir');
        foreach ($baseUrls as $baseUrl) {
            Zend_Controller_Front::getInstance()->setBaseUrl($baseUrl);
            $helper = new Zend_View_Helper_BaseUrl();

            $this->assertEquals(rtrim($baseUrl, '/\\'), $helper->baseUrl());
        }
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
            Zend_Controller_Front::getInstance()->setBaseUrl($baseUrl);
            $helper = new Zend_View_Helper_BaseUrl();

            $this->assertEquals($val, $helper->baseUrl('file.js'));
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
            Zend_Controller_Front::getInstance()->setBaseUrl($baseUrl);
            $helper = new Zend_View_Helper_BaseUrl();

            $this->assertEquals($val, $helper->baseUrl('/file.js'));
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
            Zend_Controller_Front::getInstance()->setBaseUrl($baseUrl);
            $helper = new Zend_View_Helper_BaseUrl();

            $this->assertEquals($val, $helper->baseUrl('/path/bar'));
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
            Zend_Controller_Front::getInstance()->setBaseUrl($baseUrl);
            $helper = new Zend_View_Helper_BaseUrl();

            $this->assertEquals($val, $helper->baseUrl('/'));
        }
    }

    public function testSetBaseUrlModifiesBaseUrl()
    {
        $helper = new Zend_View_Helper_BaseUrl();
        $helper->setBaseUrl('/myfoo');
        $this->assertEquals('/myfoo', $helper->getBaseUrl());
    }

    public function testGetBaseUrlReturnsBaseUrl()
    {
        Zend_Controller_Front::getInstance()->setBaseUrl('/mybar');
        $helper = new Zend_View_Helper_BaseUrl();
        $this->assertEquals('/mybar', $helper->getBaseUrl());
    }

    public function testGetBaseUrlReturnsBaseUrlWithoutScriptName()
    {
        $_SERVER['SCRIPT_NAME'] = '/foo/bar/bat/mybar/index.php';
        Zend_Controller_Front::getInstance()->setBaseUrl('/mybar/index.php');
        $helper = new Zend_View_Helper_BaseUrl();
        $this->assertEquals('/mybar', $helper->getBaseUrl());
    }
}

// Call Zend_View_Helper_BaseUrlTest::main() if this source file is executed directly.
if (PHPUnit_MAIN_METHOD == 'Zend_View_Helper_BaseUrlTest::main') {
    Zend_View_Helper_BaseUrlTest::main();
}
