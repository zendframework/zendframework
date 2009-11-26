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
 * @package    Zend_Uri
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Test helper
 */
require_once dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'TestHelper.php';

/**
 * @see Zend_Uri
 */
require_once 'Zend/Uri.php';

/**
 * @see Zend_Uri_Http
 */
require_once 'Zend/Uri/Http.php';

/**
 * PHPUnit test case
 */
require_once 'PHPUnit/Framework/TestCase.php';


/**
 * @category   Zend
 * @package    Zend_Uri
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @group      Zend_Uri
 */
class Zend_Uri_HttpTest extends PHPUnit_Framework_TestCase
{
    /**
     * Tests for proper URI decomposition
     */
    public function testSimple()
    {
        $this->_testValidUri('http://www.zend.com');
    }

    /**
     * Test that fromString() works proprerly for simple valid URLs
     *
     */
    public function testSimpleFromString()
    {
        $tests = array(
            'http://www.zend.com',
            'https://www.zend.com',
            'http://www.zend.com/path',
            'http://www.zend.com/path?query=value'
        );

        foreach ($tests as $uri) {
            $obj = Zend_Uri_Http::fromString($uri);
            $this->assertEquals($uri, $obj->getUri(),
                "getUri() returned value that differs from input for $uri");
        }
    }

    /**
     * Make sure an exception is thrown when trying to use fromString() with a
     * non-HTTP scheme
     *
     * @see http://framework.zend.com/issues/browse/ZF-4395
     *
     * @expectedException Zend_Uri_Exception
     */
    public function testFromStringInvalidScheme()
    {
        Zend_Uri_Http::fromString('ftp://example.com/file');
    }

    /**
     * Make sure an exception is thrown when trying to use fromString() with a variable that is not
     * a string.
     *
     */
    public function testFromStringWithInvalidVariableType()
    {
        $this->setExpectedException('Zend_Uri_Exception');
        Zend_Uri_Http::fromString(0);
    }

    public function testAllParts()
    {
        $this->_testValidUri('http://andi:password@www.zend.com:8080/path/to/file?a=1&b=2#top');
    }

    public function testUsernamePortPathQueryFragment()
    {
        $this->_testValidUri('http://andi@www.zend.com:8080/path/to/file?a=1&b=2#top');
    }

    public function testPortPathQueryFragment()
    {
        $this->_testValidUri('http://www.zend.com:8080/path/to/file?a=1&b=2#top');
    }

    public function testPathQueryFragment()
    {
        $this->_testValidUri('http://www.zend.com/path/to/file?a=1&b=2#top');
    }

    public function testQueryFragment()
    {
        $this->_testValidUri('http://www.zend.com/?a=1&b=2#top');
    }

    public function testFragment()
    {
        $this->_testValidUri('http://www.zend.com/#top');
    }

    public function testUsernamePassword()
    {
        $this->_testValidUri('http://andi:password@www.zend.com');
    }

    public function testUsernamePasswordColon()
    {
        $this->_testValidUri('http://an:di:password@www.zend.com');
    }

    public function testUsernamePasswordValidCharacters()
    {
        $this->_testValidUri('http://a_.!~*\'(-)n0123Di%25%26:pass;:&=+$,word@www.zend.com');
    }

    public function testUsernameInvalidCharacter()
    {
        $this->_testInvalidUri('http://an`di:password@www.zend.com');
    }

    public function testNoUsernamePassword()
    {
        $this->_testInvalidUri('http://:password@www.zend.com');
    }

    public function testPasswordInvalidCharacter()
    {
        $this->_testInvalidUri('http://andi:pass%word@www.zend.com');
    }

    public function testHostAsIP()
    {
        $this->_testValidUri('http://127.0.0.1');
    }

    public function testLocalhost()
    {
        $this->_testValidUri('http://localhost');
    }

    public function testLocalhostLocaldomain()
    {
        $this->_testValidUri('http://localhost.localdomain');
    }

    public function testSquareBrackets()
    {
        $this->_testValidUri('https://example.com/foo/?var[]=1&var[]=2&some[thing]=3');
    }

    /**
     * Ensures that successive slashes are considered valid
     *
     * @return void
     */
    public function testSuccessiveSlashes()
    {
        $this->_testValidUri('http://example.com//');
        $this->_testValidUri('http://example.com///');
        $this->_testValidUri('http://example.com/foo//');
        $this->_testValidUri('http://example.com/foo///');
        $this->_testValidUri('http://example.com/foo//bar');
        $this->_testValidUri('http://example.com/foo///bar');
        $this->_testValidUri('http://example.com/foo//bar/baz//fob/');
    }

    /**
     * Test that setQuery() can handle unencoded query parameters (as other
     * browsers do), ZF-1934
     *
     * @group ZF-1934
     * @return void
     */
    public function testUnencodedQueryParameters()
    {
         $uri = Zend_Uri::factory('http://foo.com/bar');

         // First, make sure no exceptions are thrown
         try {
             $uri->setQuery('id=123&url=http://example.com/?bar=foo baz');
         } catch (Exception $e) {
             $this->fail('setQuery() was expected to handle unencoded parameters, but failed');
         }

         // Second, make sure the query string was properly encoded
         $parts = parse_url($uri->getUri());
         $this->assertEquals('id=123&url=http%3A%2F%2Fexample.com%2F%3Fbar%3Dfoo+baz', $parts['query']);
    }

    /**
     * Test that unwise characters in the query string are not valid
     *
     */
    public function testExceptionUnwiseQueryString()
    {
        $unwise = array(
            'http://example.com/?q={',
            'http://example.com/?q=}',
            'http://example.com/?q=|',
            'http://example.com/?q=\\',
            'http://example.com/?q=^',
            'http://example.com/?q=`',
        );

        foreach ($unwise as $uri) {
            $this->assertFalse(Zend_Uri::check($uri), "failed for URI $uri");
        }
    }

    /**
     * Test that after setting 'allow_unwise' to true unwise characters are
     * accepted
     *
     */
    public function testAllowUnwiseQueryString()
    {
        $unwise = array(
            'http://example.com/?q={',
            'http://example.com/?q=}',
            'http://example.com/?q=|',
            'http://example.com/?q=\\',
            'http://example.com/?q=^',
            'http://example.com/?q=`',
        );

        Zend_Uri::setConfig(array('allow_unwise' => true));

        foreach ($unwise as $uri) {
            $this->assertTrue(Zend_Uri::check($uri), "failed for URI $uri");
        }

        Zend_Uri::setConfig(array('allow_unwise' => false));
    }

    /**
     * Test that an extremely long URI does not break things up
     *
     * @group ZF-3712
     * @group ZF-7840
     */
    public function testVeryLongUriZF3712()
    {
        if(!defined('TESTS_ZEND_URI_CRASH_TEST_ENABLED') || constant('TESTS_ZEND_URI_CRASH_TEST_ENABLED') == false) {
            $this->markTestSkipped('The constant TESTS_ZEND_URI_CRASH_TEST_ENABLED has to be defined and true to allow the test to work.');
        }
        $uri = file_get_contents(dirname(realpath(__FILE__)) . DIRECTORY_SEPARATOR .
           '_files' . DIRECTORY_SEPARATOR . 'testVeryLongUriZF3712.txt');

        $this->_testValidUri($uri);
    }

    /**
     * Test a known valid URI
     *
     * @param string $uri
     */
    protected function _testValidUri($uri)
    {
        $obj = Zend_Uri::factory($uri);
        $this->assertEquals($uri, $obj->getUri(), 'getUri() returned value that differs from input');
    }

    /**
     * Test a known invalid URI
     *
     * @param string $uri
     */
    protected function _testInvalidUri($uri)
    {
        try {
            $obj = Zend_Uri::factory($uri);
            $this->fail('Zend_Uri_Exception was expected but not thrown');
        } catch (Zend_Uri_Exception $e) {
        }
    }

    public function testSetGetUsername()
    {
        $uri = Zend_Uri::factory('http://example.com');
        $username = 'alice';
        $this->assertFalse($uri->getUsername());
        $uri->setUsername($username);
        $this->assertSame($username, $uri->getUsername());
    }

    public function testSetGetPassword()
    {
        $uri = Zend_Uri::factory('http://example.com');
        $username = 'alice';
        $password = 'secret';
        $this->assertFalse($uri->getPassword());
        $uri->setUsername($username);
        $uri->setPassword($password);
        $this->assertSame($password, $uri->getPassword());
    }

    public function testUriWithAllParts()
    {
        $uri = Zend_Uri::factory('http://alice:secret@example.com:8080/path/script.php?foo=bar&bar=foo#123');

        $this->assertSame('http', $uri->getScheme());
        $this->assertSame('alice', $uri->getUsername());
        $this->assertSame('secret', $uri->getPassword());
        $this->assertSame('example.com', $uri->getHost());
        $this->assertEquals(8080, $uri->getPort());
        $this->assertSame('/path/script.php', $uri->getPath());
        $this->assertSame('foo=bar&bar=foo', $uri->getQuery());
        $this->assertSame('123', $uri->getFragment());
    }

    public function testBuildCompleteUriFromScratch()
    {
        $uri = Zend_Uri::factory('http');

        $uri->setUsername('alice');
        $uri->setPassword('secret');
        $uri->setHost('example.com');
        $uri->setPort(8080);
        $uri->setPath('/path/script.php');
        $uri->setQuery('foo=bar&bar=foo');
        $uri->setFragment('123');

        $this->assertSame('http://alice:secret@example.com:8080/path/script.php?foo=bar&bar=foo#123', $uri->getUri());
    }

    public function testSetInvalidUsername()
    {
        $uri = Zend_Uri::factory('http://example.com');
        $this->setExpectedException('Zend_Uri_Exception');
        $uri->setUsername('alice?');
    }

    public function testSetInvalidPassword()
    {
        $uri = Zend_Uri::factory('http://example.com');
        $this->setExpectedException('Zend_Uri_Exception');
        $uri->setUsername('alice');
        $uri->setPassword('secret?');
    }

    public function testSetEmptyHost()
    {
        $uri = Zend_Uri::factory('http://example.com');
        $host = '';
        $this->setExpectedException('Zend_Uri_Exception');
        $uri->setHost($host);
    }

    public function testSetInvalidHost()
    {
        $uri = Zend_Uri::factory('http://example.com');
        $host = 'example,com';
        $this->setExpectedException('Zend_Uri_Exception');
        $uri->setHost($host);
    }

    /**
     * @group ZF-1480
     */
    public function testGetQueryAsArrayReturnsCorrectArray()
    {
        $uri = Zend_Uri_Http::fromString('http://example.com/foo/?test=a&var[]=1&var[]=2&some[thing]=3');
        $this->assertEquals(array(
            'test' => 'a',
            'var'  => array(1, 2),
            'some' => array('thing' => 3)
        ), $uri->getQueryAsArray());
    }

    /**
     * @group ZF-1480
     */
    public function testAddReplaceQueryParametersModifiesQueryAndReturnsOldQuery()
    {
        $uri = Zend_Uri_Http::fromString('http://example.com/foo/?a=1&b=2&c=3');
        $this->assertEquals('a=1&b=2&c=3', $uri->addReplaceQueryParameters(array(
            'b' => 4,
            'd' => -1
        )));
        $this->assertEquals(array(
            'a' => 1,
            'b' => 4,
            'c' => 3,
            'd' => -1
        ), $uri->getQueryAsArray());
        $this->assertEquals('a=1&b=4&c=3&d=-1', $uri->getQuery());
    }

    /**
     * @group ZF-1480
     */
    public function testRemoveQueryParametersModifiesQueryAndReturnsOldQuery()
    {
        $uri = Zend_Uri_Http::fromString('http://example.com/foo/?a=1&b=2&c=3&d=4');
        $this->assertEquals('a=1&b=2&c=3&d=4', $uri->removeQueryParameters(array('b', 'd', 'e')));
        $this->assertEquals(array(
            'a' => 1,
            'c' => 3
        ), $uri->getQueryAsArray());
        $this->assertEquals('a=1&c=3', $uri->getQuery());
    }
}