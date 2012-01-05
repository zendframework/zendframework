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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Auth\Adapter\Http;

use Zend\Authentication\Adapter\Http,
    Zend\Authentication\Adapter,
    Zend\Authentication,
    Zend\Http\Headers,
    Zend\Http\Request,
    Zend\Http\Response;

/**
 * @category   Zend
 * @package    Zend_Auth
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Auth
 */
class ObjectTest extends \PHPUnit_Framework_TestCase
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
    public function setUp()
    {
        $this->_filesPath      = __DIR__ . '/TestAsset';
        $this->_basicResolver  = new Http\FileResolver("$this->_filesPath/htbasic.1");
        $this->_digestResolver = new Http\FileResolver("$this->_filesPath/htdigest.3");
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
            $t = new Adapter\Http($this->_basicConfig);
        } catch (Adapter\Exception $e) {
            $this->fail('Valid config deemed invalid');
        }
        $this->assertFalse(empty($t));
        $this->assertInstanceOf('Zend\\Authentication\\Adapter\\Http', $t);
        unset($t);

        try {
            $t = new Adapter\Http($this->_digestConfig);
        } catch (Adapter\Exception $e) {
            $this->fail('Valid config deemed invalid');
        }
        $this->assertFalse(empty($t));
        $this->assertInstanceOf('Zend\\Authentication\\Adapter\\Http', $t);
        unset($t);

        try {
            $t = new Adapter\Http($this->_bothConfig);
        } catch (Adapter\Exception $e) {
            $this->fail('Valid config deemed invalid');
        }
        $this->assertFalse(empty($t));
        $this->assertInstanceOf('Zend\\Authentication\\Adapter\\Http', $t);
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
                $t = new Adapter\Http($cfg);
                $this->fail('Accepted an invalid config');
            } catch (Adapter\Exception $e) {
                // Good, it threw an exception
            }
        }
    }

    public function testAuthenticateArgs()
    {
        $a = new Adapter\Http($this->_basicConfig);

        try {
            $a->authenticate();
            $this->fail('Attempted authentication without request/response objects');
        } catch (Adapter\Exception $e) {
            // Good, it threw an exception
        }

        $request  = new Request;
        $response = new Response;

        // If this throws an exception, it fails
        $a->setRequest($request)
          ->setResponse($response)
          ->authenticate();
    }

    public function testNoResolvers()
    {
        // Stub request for Basic auth
        $headers  = new Headers;
        $headers->addHeaderLine('Authorization', 'Basic <followed by a space character');
        $request  = new Request;
        $request->setHeaders($headers);
        $response = new Response;

        // Once for Basic
        try {
            $a = new Adapter\Http($this->_basicConfig);
            $a->setRequest($request)
              ->setResponse($response);
            $result = $a->authenticate();
            $this->fail("Tried Basic authentication without a resolver.\n" . \Zend\Debug::dump($result->getMessages(),null,false));
        } catch (Adapter\Exception $e) {
            // Good, it threw an exception
            unset($a);
        }

        // Stub request for Digest auth, must be reseted (recreated)
        $headers  = new Headers;
        $headers->addHeaderLine('Authorization', 'Digest <followed by a space character');
        $request  = new Request;
        $request->setHeaders($headers);

        // Once for Digest
        try {
            $a = new Adapter\Http($this->_digestConfig);
            $a->setRequest($request)
              ->setResponse($response);
            $result = $a->authenticate();
            $this->fail("Tried Digest authentication without a resolver.\n" . \Zend\Debug::dump($result->getMessages(),null,false));
        } catch (Adapter\Exception $e) {
            // Good, it threw an exception
            unset($a);
        }
    }

    public function testWrongResolverUsed()
    {
        $response = new Response();
        $headers  = new Headers();
        $request  = new Request();

        $headers->addHeaderLine('Authorization', 'Basic <followed by a space character');
        $request->setHeaders($headers);

        // Test a Digest auth process while the request is containing a Basic auth header
        $a = new Adapter\Http($this->_digestConfig);
        $a->setDigestResolver($this->_digestResolver)
          ->setRequest($request)
          ->setResponse($response);
        $result = $a->authenticate();
        $this->assertEquals($result->getCode(),Authentication\Result::FAILURE_CREDENTIAL_INVALID);
    }

    public function testUnsupportedScheme()
    {
        $response = new Response();
        $headers  = new Headers();
        $request  = new Request();
        $headers->addHeaderLine('Authorization', 'NotSupportedScheme <followed by a space character');
        $request->setHeaders($headers);

        $a = new Adapter\Http($this->_digestConfig);
        $a->setDigestResolver($this->_digestResolver)
          ->setRequest($request)
          ->setResponse($response);
        $result = $a->authenticate();
        $this->assertEquals($result->getCode(),Authentication\Result::FAILURE_UNCATEGORIZED);
    }
}
