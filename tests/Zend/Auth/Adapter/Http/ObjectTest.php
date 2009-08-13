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
 * @see Zend_Auth_Adapter_Http
 */
require_once 'Zend/Auth/Adapter/Http.php';


/**
 * @see Zend_Auth_Adapter_Http_Resolver_File
 */
require_once 'Zend/Auth/Adapter/Http/Resolver/File.php';


/**
 * @see Zend_Controller_Request_Http
 */
require_once 'Zend/Controller/Request/Http.php';


/**
 * @see Zend_Controller_Response_Http
 */
require_once 'Zend/Controller/Response/Http.php';

/**
 * @see Zend_Debug
 */
require_once 'Zend/Debug.php';

/**
 * @category   Zend
 * @package    Zend_Auth
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Auth
 */
class Zend_Auth_Adapter_Http_ObjectTest extends PHPUnit_Framework_TestCase
{
    /**
     * Path to test files
     *
     * @var string
     */
    protected $_filesPath;

    /**
     * HTTP Basic configuration
     *
     * @var array
     */
    protected $_basicConfig;

    /**
     * HTTP Digest configuration
     *
     * @var array
     */
    protected $_digestConfig;

    /**
     * HTTP Basic Digest configuration
     *
     * @var array
     */
    protected $_bothConfig;

    /**
     * File resolver setup against with HTTP Basic auth file
     *
     * @var Zend_Auth_Adapter_Http_Resolver_File
     */
    protected $_basicResolver;

    /**
     * File resolver setup against with HTTP Digest auth file
     *
     * @var Zend_Auth_Adapter_Http_Resolver_File
     */
    protected $_digestResolver;

    /**
     * Sets up test configuration
     *
     * @return void
     */
    public function __construct()
    {
        $this->_filesPath      = dirname(__FILE__) . '/_files';
        $this->_basicResolver  = new Zend_Auth_Adapter_Http_Resolver_File("$this->_filesPath/htbasic.1");
        $this->_digestResolver = new Zend_Auth_Adapter_Http_Resolver_File("$this->_filesPath/htdigest.3");
        $this->_basicConfig    = array(
            'accept_schemes' => 'basic',
            'realm'          => 'Test Realm'
        );
        $this->_digestConfig   = array(
            'accept_schemes' => 'digest',
            'realm'          => 'Test Realm',
            'digest_domains' => '/ http://localhost/',
            'nonce_timeout'  => 300
        );
        $this->_bothConfig     = array(
            'accept_schemes' => 'basic digest',
            'realm'          => 'Test Realm',
            'digest_domains' => '/ http://localhost/',
            'nonce_timeout'  => 300
        );
    }

    public function testValidConfigs()
    {
        try {
            $t = new Zend_Auth_Adapter_Http($this->_basicConfig);
        } catch (Zend_Auth_Adapter_Exception $e) {
            $this->fail('Valid config deemed invalid');
        }
        $this->assertFalse(empty($t));
        $this->assertType('Zend_Auth_Adapter_Http', $t);
        unset($t);

        try {
            $t = new Zend_Auth_Adapter_Http($this->_digestConfig);
        } catch (Zend_Auth_Adapter_Exception $e) {
            $this->fail('Valid config deemed invalid');
        }
        $this->assertFalse(empty($t));
        $this->assertType('Zend_Auth_Adapter_Http', $t);
        unset($t);

        try {
            $t = new Zend_Auth_Adapter_Http($this->_bothConfig);
        } catch (Zend_Auth_Adapter_Exception $e) {
            $this->fail('Valid config deemed invalid');
        }
        $this->assertFalse(empty($t));
        $this->assertType('Zend_Auth_Adapter_Http', $t);
        unset($t);
    }

    public function testInvalidConfigs()
    {
        $badConfigs = array(
            'bad1' => array(
                'auth_type' => 'bogus',
                'realm'     => 'Test Realm'
            ),
            'bad2' => array(
                'auth_type'      => 'digest',
                'realm'          => 'Bad: "Chars"'."\n",
                'digest_domains' => '/ /admin',
                'nonce_timeout'  => 300
            ),
            'bad3' => array(
                'auth_type'      => 'digest',
                'realm'          => 'Test Realm',
                'digest_domains' => 'no"quotes'."\tor tabs",
                'nonce_timeout'  => 300
            ),
            'bad4' => array(
                'auth_type'      => 'digest',
                'realm'          => 'Test Realm',
                'digest_domains' => '/ /admin',
                'nonce_timeout'  => 'junk'
            )
        );

        foreach ($badConfigs as $cfg) {
            $t = null;
            try {
                $t = new Zend_Auth_Adapter_Http($cfg);
                $this->fail('Accepted an invalid config');
            } catch (Zend_Auth_Adapter_Exception $e) {
                // Good, it threw an exception
            }
        }
    }

    public function testAuthenticateArgs()
    {
        $a = new Zend_Auth_Adapter_Http($this->_basicConfig);

        try {
            $a->authenticate();
            $this->fail('Attempted authentication without request/response objects');
        } catch (Zend_Auth_Adapter_Exception $e) {
            // Good, it threw an exception
        }

        $request  = $this->getMock('Zend_Controller_Request_Http');
        $response = $this->getMock('Zend_Controller_Response_Http');

        // If this throws an exception, it fails
        $a->setRequest($request)
          ->setResponse($response)
          ->authenticate();
    }

    public function testNoResolvers()
    {
        $request  = $this->getMock('Zend_Controller_Request_Http');
        $response = $this->getMock('Zend_Controller_Response_Http');

        // Stub request for Basic auth
        $request->expects($this->any())
                ->method('getHeader')
                ->will($this->returnValue('Basic <followed by a space caracter'));

        // Once for Basic
        try {
            $a = new Zend_Auth_Adapter_Http($this->_basicConfig);
            $a->setRequest($request)
              ->setResponse($response);
            $result = $a->authenticate();
            $this->fail("Tried Basic authentication without a resolver.\n" . Zend_Debug::dump($result->getMessages(),null,false));
        } catch (Zend_Auth_Adapter_Exception $e) {
            // Good, it threw an exception
            unset($a);
        }

        // Stub request for Digest auth, must be reseted (recreated)
        $request  = $this->getMock('Zend_Controller_Request_Http');
        $request->expects($this->any())
                ->method('getHeader')
                ->will($this->returnValue('Digest <followed by a space caracter'));

        // Once for Digest
        try {
            $a = new Zend_Auth_Adapter_Http($this->_digestConfig);
            $a->setRequest($request)
              ->setResponse($response);
            $result = $a->authenticate();
            $this->fail("Tried Digest authentication without a resolver.\n" . Zend_Debug::dump($result->getMessages(),null,false));
        } catch (Zend_Auth_Adapter_Exception $e) {
            // Good, it threw an exception
            unset($a);
        }
    }
    
    public function testWrongResolverUsed()
    {
        $response = $this->getMock('Zend_Controller_Response_Http');
        $request  = $this->getMock('Zend_Controller_Request_Http');
        $request->expects($this->any())
                ->method('getHeader')
                ->will($this->returnValue('Basic <followed by a space caracter')); // A basic Header will be provided by that request

        // Test a Digest auth process while the request is containing a Basic auth header
        $a = new Zend_Auth_Adapter_Http($this->_digestConfig);
        $a->setDigestResolver($this->_digestResolver)
          ->setRequest($request)
          ->setResponse($response);
        $result = $a->authenticate();
        $this->assertEquals($result->getCode(),Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID);
    }
    
    public function testUnsupportedScheme()
    {
        $response = $this->getMock('Zend_Controller_Response_Http');
        $request  = $this->getMock('Zend_Controller_Request_Http');
        $request->expects($this->any())
                ->method('getHeader')
                ->will($this->returnValue('NotSupportedScheme <followed by a space caracter'));

        $a = new Zend_Auth_Adapter_Http($this->_digestConfig);
        $a->setDigestResolver($this->_digestResolver)
          ->setRequest($request)
          ->setResponse($response);            
        $result = $a->authenticate();
        $this->assertEquals($result->getCode(),Zend_Auth_Result::FAILURE_UNCATEGORIZED);
    }
}
