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
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/**
 * Tests Zend_View_Helper_ServerUrl
 *
 * @category   Zend
 * @package    Zend_View
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_View
 * @group      Zend_View_Helper
 */
class Zend_View_Helper_ServerUrlTest extends PHPUnit_Framework_TestCase
{
    /**
     * Back up of $_SERVER
     *
     * @var array
     */
    protected $_serverBackup;

    /**
     * Prepares the environment before running a test.
     */
    protected function setUp()
    {
        $this->_serverBackup = $_SERVER;
        unset($_SERVER['HTTPS']);
    }

    /**
     * Cleans up the environment after running a test.
     */
    protected function tearDown()
    {
        $_SERVER = $this->_serverBackup;
    }

    public function testConstructorWithOnlyHost()
    {
        $_SERVER['HTTP_HOST'] = 'example.com';

        $url = new Zend_View_Helper_ServerUrl();
        $this->assertEquals('http://example.com', $url->serverUrl());
    }

    public function testConstructorWithOnlyHostIncludingPort()
    {
        $_SERVER['HTTP_HOST'] = 'example.com:8000';

        $url = new Zend_View_Helper_ServerUrl();
        $this->assertEquals('http://example.com:8000', $url->serverUrl());
    }

    public function testConstructorWithHostAndHttpsOn()
    {
        $_SERVER['HTTP_HOST'] = 'example.com';
        $_SERVER['HTTPS'] = 'on';

        $url = new Zend_View_Helper_ServerUrl();
        $this->assertEquals('https://example.com', $url->serverUrl());
    }

    public function testConstructorWithHostAndHttpsTrue()
    {
        $_SERVER['HTTP_HOST'] = 'example.com';
        $_SERVER['HTTPS'] = true;

        $url = new Zend_View_Helper_ServerUrl();
        $this->assertEquals('https://example.com', $url->serverUrl());
    }

    public function testConstructorWithHostIncludingPortAndHttpsTrue()
    {
        $_SERVER['HTTP_HOST'] = 'example.com:8181';
        $_SERVER['HTTPS'] = true;

        $url = new Zend_View_Helper_ServerUrl();
        $this->assertEquals('https://example.com:8181', $url->serverUrl());
    }

    public function testConstructorWithHttpHostAndServerNameAndPortSet()
    {
        $_SERVER['HTTP_HOST'] = 'example.com';
        $_SERVER['SERVER_NAME'] = 'example.org';
        $_SERVER['SERVER_PORT'] = 8080;

        $url = new Zend_View_Helper_ServerUrl();
        $this->assertEquals('http://example.com', $url->serverUrl());
    }

    public function testConstructorWithNoHttpHostButServerNameAndPortSet()
    {
        unset($_SERVER['HTTP_HOST']);
        $_SERVER['SERVER_NAME'] = 'example.org';
        $_SERVER['SERVER_PORT'] = 8080;

        $url = new Zend_View_Helper_ServerUrl();
        $this->assertEquals('http://example.org:8080', $url->serverUrl());
    }

    public function testServerUrlWithTrueParam()
    {
        $_SERVER['HTTPS']       = 'off';
        $_SERVER['HTTP_HOST']   = 'example.com';
        $_SERVER['REQUEST_URI'] = '/foo.html';

        $url = new Zend_View_Helper_ServerUrl();
        $this->assertEquals('http://example.com/foo.html', $url->serverUrl(true));
    }

    public function testServerUrlWithInteger()
    {
        $_SERVER['HTTPS']     = 'off';
        $_SERVER['HTTP_HOST'] = 'example.com';
        $_SERVER['REQUEST_URI'] = '/foo.html';

        $url = new Zend_View_Helper_ServerUrl();
        $this->assertEquals('http://example.com', $url->serverUrl(1337));
    }

    public function testServerUrlWithObject()
    {
        $_SERVER['HTTPS']     = 'off';
        $_SERVER['HTTP_HOST'] = 'example.com';
        $_SERVER['REQUEST_URI'] = '/foo.html';

        $url = new Zend_View_Helper_ServerUrl();
        $this->assertEquals('http://example.com', $url->serverUrl(new stdClass()));
    }
}
