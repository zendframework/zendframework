<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
 */

namespace ZendTest\Service\Rackspace;

use Zend\Service\Rackspace\Files as RackspaceFiles;
use Zend\Service\Rackspace\Files\ContainerList;
use Zend\Http\Client\Adapter\Test as HttpTest;

/**
 * @category   Zend
 * @package    Zend\Service\Rackspace\Files
 * @subpackage UnitTests
 * @group      Zend\Service
 * @group      Zend\Service\Rackspace
 * @group      Zend\Service\Rackspace\Files
 */
class OfflineTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Reference to RackspaceFiles
     *
     * @var Zend\Service\Rackspace\Files
     */
    protected $files;

    /**
     * HTTP client adapter for testing
     *
     * @var Zend\Http\Client\Adapter\Test
     */
    protected $httpClientAdapterTest;

    /**
     * Path to test data files
     *
     * @var string
     */
    protected $filesPath;

    /**
     * Sets up this test case
     *
     * @return void
     */
    public function setUp()
    {
        $this->files = new RackspaceFiles('foo','bar');
        $this->filesPath   = __DIR__ . '/_files';
        $this->httpClientAdapterTest = new HttpTest();
    }

    /**
     * Utility method for returning a string HTTP response, which is loaded from a file
     *
     * @param  string $name
     * @return string
     */
    protected function _loadResponse($name)
    {
        return file_get_contents("$this->filesPath/$name.response");
    }

    /**
     * Ensures that __construct() throws an exception when given an empty key attribute
     *
     * @return void
     */
    public function testConstructExceptionMissingUserAttribute()
    {
        $this->setExpectedException(
            'Zend\Service\Rackspace\Exception\InvalidArgumentException',
            'The user cannot be empty'
        );
        $file= new RackspaceFiles(null,'bar');
    }

    /**
     * Ensures that __construct() throws an exception when given an empty secret attribute
     *
     * @return void
     */
    public function testConstructExceptionMissingKeyAttribute()
    {
        $this->setExpectedException(
            'Zend\Service\Rackspace\Exception\InvalidArgumentException',
            'The key cannot be empty'
        );
        $file= new RackspaceFiles('foo',null);
    }

    /**
     * Test the default authentication URL
     *
     * @return void
     */
    public function testDefaultAuthUrl()
    {
        $this->assertEquals($this->files->getAuthUrl(),RackspaceFiles::US_AUTH_URL,'The default Authentication URL is changed');
    }

    /**
     * Test the set of the key
     * 
     * @return void
     */
    public function testSetKey()
    {
        $key= '1234567890';
        $this->files->setKey($key);
        $this->assertEquals($this->files->getKey(),$key);
    }

    /**
     * Test the set of the user
     *
     * @return void
     */
    public function testSetUser()
    {
        $user= 'test';
        $this->files->setUser($user);
        $this->assertEquals($this->files->getUser(),$user);
    }

    /**
     * Test the set of an invalid authentication URL
     *
     * @return void
     */
    public function testSetInvalidAuthUrl()
    {
        $this->setExpectedException(
            'Zend\Service\Rackspace\Exception\InvalidArgumentException',
            'The authentication URL is not valid'
        );
        $this->files->setAuthUrl('http://test');
    }

    /**
     * Check the authentication and the results (token, storage_url, cdn_url)
     *
     * @return void
     */
    public function testAuthenticate()
    {
        $this->files->getHttpClient()
                    ->setAdapter($this->httpClientAdapterTest);

        $this->httpClientAdapterTest->setResponse($this->_loadResponse(__FUNCTION__));

        $this->assertTrue($this->files->authenticate(),'Authentication failed');
        $this->assertTrue($this->files->isSuccessful(),'Authentication call failed');
        $this->assertEquals($this->files->getToken(),'0f0223cd-f157-4d04-bb2d-ccda1a5643af','The token is not valid');
        $this->assertEquals($this->files->getStorageUrl(),'https://storage101.ord1.clouddrive.com/v1/test','The storage URL is not valid');
        $this->assertEquals($this->files->getCdnUrl(),'https://cdn2.clouddrive.com/v1/test','The CDN URL is not valid');
    }

    /**
     * Test the authentication error (401 Unauthorized - Bad username or password)
     *
     * @return void
     */
    public function testAuthenticateError()
    {
        $this->files->getHttpClient()
                    ->setAdapter($this->httpClientAdapterTest);

        $this->httpClientAdapterTest->setResponse($this->_loadResponse(__FUNCTION__));

        $this->assertFalse($this->files->authenticate());
        $this->assertFalse($this->files->isSuccessful());
        $this->assertEquals($this->files->getErrorCode(),'401');
        $this->assertEquals($this->files->getErrorMsg(),'Bad username or password');

    }
}
