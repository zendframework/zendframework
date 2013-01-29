<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Authentication
 */

namespace ZendTest\Authentication\Adapter\Http;

use Zend\Authentication\Adapter\Http\ApacheResolver as Apache;
use Zend\Authentication\Result as AuthResult;

/**
 * @category   Zend
 * @package    Zend_Auth
 * @subpackage UnitTests
 * @group      Zend_Auth
 */
class ApacheTest extends \PHPUnit_Framework_TestCase
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
    public function setUp()
    {
        $this->_path      = __DIR__ . '/TestAsset';
        $this->_validPath = $this->_path . '/htbasic.plaintext';
        $this->_digest    = $this->_path . '/htdigest';
        $this->_apache    = new Apache($this->_validPath);
        $this->_badPath   = 'invalid path';
    }

    /**
     * Ensures that setFile() works as expected for valid input
     *
     * @return void
     */
    public function testSetFileValid()
    {
        $this->_apache->setFile($this->_validPath);
        $this->assertEquals($this->_validPath, $this->_apache->getFile());
    }

    /**
     * Ensures that setFile() works as expected for invalid input
     *
     * @return void
     */
    public function testSetFileInvalid()
    {
        $this->setExpectedException('Zend\\Authentication\\Adapter\\Http\\Exception\\ExceptionInterface', 'Path not readable');
        $this->_apache->setFile($this->_badPath);
    }

    /**
     * Ensures that __construct() works as expected for valid input
     *
     * @return void
     */
    public function testConstructValid()
    {
        $apache = new Apache($this->_validPath);
        $this->assertEquals($this->_validPath, $apache->getFile());
    }

    /**
     * Ensures that __construct() works as expected for invalid input
     *
     * @return void
     */
    public function testConstructInvalid()
    {
        $this->setExpectedException('Zend\\Authentication\\Adapter\\Http\\Exception\\ExceptionInterface', 'Path not readable');
        $apache = new Apache($this->_badPath);
    }

    /**
     *
     */
    public function providePasswordFiles()
    {
        $path = __DIR__ . '/TestAsset';
        return array(
            array( $path . '/htbasic.plaintext' ),
            array( $path . '/htbasic.md5' ),
            array( $path . '/htbasic.sha1' ),
            array( $path . '/htbasic.crypt' )
        );
    }

    /**
     * Ensure that resolve() works fine with the specified password format
     *
     * @dataProvider providePasswordFiles
     */
    public function testResolveValidBasic($file)
    {
        $this->_apache->setFile($file);
        $result = $this->_apache->resolve('test', null, 'password');
        $this->assertTrue($result instanceof AuthResult);
        $this->assertTrue($result->isValid());
    }

    /**
     * Ensure that resolve() works fine with the specified password format
     * even if we pass a realm fake string for a basic authentication
     *
     * @dataProvider providePasswordFiles
     */
    public function testResolveValidBasicWithRealm($file)
    {
        $this->_apache->setFile($file);
        $result = $this->_apache->resolve('test', 'realm', 'password');
        $this->assertTrue($result instanceof AuthResult);
        $this->assertTrue($result->isValid());
    }

    /**
     * Ensure that resolve() failed for not valid users
     *
     * @dataProvider providePasswordFiles
     */
    public function testResolveNoUsers($file)
    {
        $this->_apache->setFile($file);
        $result = $this->_apache->resolve('foo', null, 'password');
        $this->assertTrue($result instanceof AuthResult);
        $this->assertFalse($result->isValid());
    }

        /**
     * Ensure that resolve() failed for not valid password
     *
     * @dataProvider providePasswordFiles
     */
    public function testResolveNoValidPassword($file)
    {
        $this->_apache->setFile($file);
        $result = $this->_apache->resolve('test', null, 'bar');
        $this->assertTrue($result instanceof AuthResult);
        $this->assertFalse($result->isValid());
    }

    /**
     *  Ensure that resolve() works fine with the digest password format
     */
    public function testResolveValidDigest()
    {
        $this->_apache->setFile($this->_digest);
        $result = $this->_apache->resolve('test', 'auth', 'password');
        $this->assertTrue($result instanceof AuthResult);
        $this->assertTrue($result->isValid());
    }
}
