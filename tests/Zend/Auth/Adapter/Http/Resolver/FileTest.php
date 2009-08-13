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
 * @package    Zend_Auth
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */


/**
 * PHPUnit_Framework_TestCase
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @see Zend_Auth_Adapter_Http_Resolver_File
 */
require_once 'Zend/Auth/Adapter/Http/Resolver/File.php';


/**
 * @category   Zend
 * @package    Zend_Auth
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Auth
 */
class Zend_Auth_Adapter_Http_Resolver_FileTest extends PHPUnit_Framework_TestCase
{
    /**
     * Path to test files
     *
     * @var string
     */
    protected $_filesPath;

    /**
     * Path to a valid file
     *
     * @var string
     */
    protected $_validPath;

    /**
     * Invalid path; does not exist
     *
     * @var string
     */
    protected $_badPath;

    /**
     * Resolver instance
     *
     * @var Zend_Auth_Adapter_Http_Resolver_File
     */
    protected $_resolver;

    /**
     * Sets the paths to files used in this test, and creates a shared resolver instance
     * having a valid path.
     *
     * @return void
     */
    public function __construct()
    {
        $this->_filesPath = dirname(dirname(__FILE__)) . '/_files';
        $this->_validPath = "$this->_filesPath/htdigest.3";
        $this->_badPath   = 'doesnotexist';
        $this->_resolver  = new Zend_Auth_Adapter_Http_Resolver_File($this->_validPath);
    }

    /**
     * Ensures that setFile() works as expected for valid input
     *
     * @return void
     */
    public function testSetFileValid()
    {
        try {
            $this->_resolver->setFile($this->_validPath);
        } catch (Zend_Auth_Adapter_Http_Resolver_Exception $e) {
            $this->fail('Threw exception on valid file path');
        }
        $this->assertEquals($this->_validPath, $this->_resolver->getFile());
    }

    /**
     * Ensures that setFile() works as expected for invalid input
     *
     * @return void
     */
    public function testSetFileInvalid()
    {
        try {
            $this->_resolver->setFile($this->_badPath);
            $this->fail('Accepted bad path');
        } catch (Zend_Auth_Adapter_Http_Resolver_Exception $e) {
            $this->assertContains('Path not readable', $e->getMessage());
        }
    }

    /**
     * Ensures that __construct() works as expected for valid input
     *
     * @return void
     */
    public function testConstructValid()
    {
        try {
            $v = new Zend_Auth_Adapter_Http_Resolver_File($this->_validPath);
            $this->assertEquals($this->_validPath, $v->getFile());
        } catch (Zend_Auth_Adapter_Http_Resolver_Exception $e) {
            $this->fail('Constructor threw exception on valid file path');
        }
    }

    /**
     * Ensures that __construct() works as expected for invalid input
     *
     * @return void
     */
    public function testConstructInvalid()
    {
        try {
            $v = new Zend_Auth_Adapter_Http_Resolver_File($this->_badPath);
            $this->fail('Constructor accepted bad path');
        } catch(Zend_Auth_Adapter_Http_Resolver_Exception $e) {
            $this->assertContains('Path not readable', $e->getMessage());
        }
    }

    /**
     * Ensures that resolve() works as expected for empty username
     *
     * @return void
     */
    public function testResolveUsernameEmpty()
    {
        try {
            $this->_resolver->resolve('', '');
            $this->fail('Accepted empty username');
        } catch (Zend_Auth_Adapter_Http_Resolver_Exception $e) {
            $this->assertEquals('Username is required', $e->getMessage());
        }
    }

    /**
     * Ensures that resolve() works as expected for empty realm
     *
     * @return void
     */
    public function testResolveRealmEmpty()
    {
        try {
            $this->_resolver->resolve('username', '');
            $this->fail('Accepted empty realm');
        } catch (Zend_Auth_Adapter_Http_Resolver_Exception $e) {
            $this->assertEquals('Realm is required', $e->getMessage());
        }
    }

    /**
     * Ensures that resolve() works as expected for invalid username
     *
     * @return void
     */
    public function testResolveUsernameInvalid()
    {
        try {
            $this->_resolver->resolve('bad:name', 'realm');
            $this->fail('Accepted malformed username with colon');
        } catch (Zend_Auth_Adapter_Http_Resolver_Exception $e) {
            $this->assertContains('Username must consist', $e->getMessage());
        }
        try {
            $this->_resolver->resolve("badname\n", 'realm');
            $this->fail('Accepted malformed username with newline');
        } catch (Zend_Auth_Adapter_Http_Resolver_Exception $e) {
            $this->assertContains('Username must consist', $e->getMessage());
        }
    }

    /**
     * Ensures that resolve() works as expected for invalid realm
     *
     * @return void
     */
    public function testResolveRealmInvalid()
    {
        try {
            $this->_resolver->resolve('username', 'bad:realm');
            $this->fail('Accepted malformed realm with colon');
        } catch (Zend_Auth_Adapter_Http_Resolver_Exception $e) {
            $this->assertContains('Realm must consist', $e->getMessage());
        }
        try {
            $this->_resolver->resolve('username', "badrealm\n");
            $this->fail('Accepted malformed realm with newline');
        } catch (Zend_Auth_Adapter_Http_Resolver_Exception $e) {
            $this->assertContains('Realm must consist', $e->getMessage());
        }
    }

    /**
     * Ensures that resolve() works as expected when a previously readable file becomes unreadable
     *
     * @return void
     */
    public function testResolveFileDisappearsMystery()
    {
        if (rename("$this->_filesPath/htdigest.3", "$this->_filesPath/htdigest.3.renamed")) {
            try {
                $this->_resolver->resolve('username', 'realm');
                $this->fail('Expected thrown exception upon resolve() after moving valid file');
            } catch (Zend_Auth_Adapter_Http_Resolver_Exception $e) {
                $this->assertContains('Unable to open password file', $e->getMessage());
            }
            rename("$this->_filesPath/htdigest.3.renamed", "$this->_filesPath/htdigest.3");
        }
    }

    /**
     * Ensures that resolve() works as expected when provided valid credentials
     *
     * @return void
     */
    public function testResolveValid()
    {
        $this->assertEquals(
            $this->_resolver->resolve('Bryce', 'Test Realm'),
            'd5b7c330d5685beb782a9e22f0f20579',
            'Rejected valid credentials'
        );
    }

    /**
     * Ensures that resolve() works as expected when provided nonexistent realm
     *
     * @return void
     */
    public function testResolveRealmNonexistent()
    {
        $this->assertFalse(
            $this->_resolver->resolve('Bryce', 'nonexistent'),
            'Accepted a valid user in the wrong realm'
        );
    }

    /**
     * Ensures that resolve() works as expected when provided nonexistent user
     *
     * @return void
     */
    public function testResolveUserNonexistent()
    {
        $this->assertFalse(
            $this->_resolver->resolve('nonexistent', 'Test Realm'),
            'Accepted a nonexistent user from an existing realm'
        );
    }
}
