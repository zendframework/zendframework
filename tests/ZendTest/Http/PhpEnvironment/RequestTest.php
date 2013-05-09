<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Http
 */

namespace ZendTest\Http\PhpEnvironment;

use PHPUnit_Framework_TestCase as TestCase;
use Zend\Http\Headers;
use Zend\Http\Header\GenericHeader;
use Zend\Http\PhpEnvironment\Request;

class RequestTest extends TestCase
{
    /**
     * Original environemnt
     *
     * @var array
     */
    protected $originalEnvironment;

    /**
     * Save the original environment and set up a clean one.
     */
    public function setUp()
    {
        $this->originalEnvironment = array(
            'post'   => $_POST,
            'get'    => $_GET,
            'cookie' => $_COOKIE,
            'server' => $_SERVER,
            'env'    => $_ENV,
            'files'  => $_FILES,
        );

        $_POST   = array();
        $_GET    = array();
        $_COOKIE = array();
        $_SERVER = array();
        $_ENV    = array();
        $_FILES  = array();
    }

    /**
     * Restore the original environment
     */
    public function tearDown()
    {
        $_POST   = $this->originalEnvironment['post'];
        $_GET    = $this->originalEnvironment['get'];
        $_COOKIE = $this->originalEnvironment['cookie'];
        $_SERVER = $this->originalEnvironment['server'];
        $_ENV    = $this->originalEnvironment['env'];
        $_FILES  = $this->originalEnvironment['files'];
    }

    /**
     * Data provider for testing base URL and path detection.
     */
    public static function baseUrlAndPathProvider()
    {
        return array(
            array(
                array(
                    'REQUEST_URI'     => '/index.php/news/3?var1=val1&var2=val2',
                    'QUERY_URI'       => 'var1=val1&var2=val2',
                    'SCRIPT_NAME'     => '/index.php',
                    'PHP_SELF'        => '/index.php/news/3',
                    'SCRIPT_FILENAME' => '/var/web/html/index.php',
                ),
                '/index.php',
                ''
            ),
            array(
                array(
                    'REQUEST_URI'     => '/public/index.php/news/3?var1=val1&var2=val2',
                    'QUERY_URI'       => 'var1=val1&var2=val2',
                    'SCRIPT_NAME'     => '/public/index.php',
                    'PHP_SELF'        => '/public/index.php/news/3',
                    'SCRIPT_FILENAME' => '/var/web/html/public/index.php',
                ),
                '/public/index.php',
                '/public'
            ),
            array(
                array(
                    'REQUEST_URI'     => '/index.php/news/3?var1=val1&var2=val2',
                    'SCRIPT_NAME'     => '/home.php',
                    'PHP_SELF'        => '/index.php/news/3',
                    'SCRIPT_FILENAME' => '/var/web/html/index.php',
                ),
                '/index.php',
                ''
            ),
            array(
                array(
                    'REQUEST_URI'      => '/index.php/news/3?var1=val1&var2=val2',
                    'SCRIPT_NAME'      => '/home.php',
                    'PHP_SELF'         => '/home.php',
                    'ORIG_SCRIPT_NAME' => '/index.php',
                    'SCRIPT_FILENAME'  => '/var/web/html/index.php',
                ),
                '/index.php',
                ''
            ),
            array(
                array(
                    'REQUEST_URI'     => '/index.php/news/3?var1=val1&var2=val2',
                    'PHP_SELF'        => '/index.php/news/3',
                    'SCRIPT_FILENAME' => '/var/web/html/index.php',
                ),
                '/index.php',
                ''
            ),
            array(
                array(
                    'HTTP_X_REWRITE_URL' => '/index.php/news/3?var1=val1&var2=val2',
                    'PHP_SELF'           => '/index.php/news/3',
                    'SCRIPT_FILENAME'    => '/var/web/html/index.php',
                ),
                '/index.php',
                ''
            ),
            array(
                array(
                    'ORIG_PATH_INFO'  => '/index.php/news/3',
                    'QUERY_STRING'    => 'var1=val1&var2=val2',
                    'PHP_SELF'        => '/index.php/news/3',
                    'SCRIPT_FILENAME' => '/var/web/html/index.php',
                ),
                '/index.php',
                ''
            ),
            array(
                array(
                    'REQUEST_URI'     => '/article/archive?foo=index.php',
                    'QUERY_STRING'    => 'foo=index.php',
                    'SCRIPT_FILENAME' => '/var/www/zftests/index.php',
                ),
                '',
                ''
            ),
            array(
                array(
                    'REQUEST_URI'     => '/html/index.php/news/3?var1=val1&var2=val2',
                    'PHP_SELF'        => '/html/index.php/news/3',
                    'SCRIPT_FILENAME' => '/var/web/html/index.php',
                ),
                '/html/index.php',
                '/html'
            ),
            array(
                array(
                    'REQUEST_URI'     => '/dir/action',
                    'PHP_SELF'        => '/dir/index.php',
                    'SCRIPT_FILENAME' => '/var/web/dir/index.php',
                ),
                '/dir',
                '/dir'
            ),
            array(
                array(
                    'SCRIPT_NAME'     => '/~username/public/index.php',
                    'REQUEST_URI'     => '/~username/public/',
                    'PHP_SELF'        => '/~username/public/index.php',
                    'SCRIPT_FILENAME' => '/Users/username/Sites/public/index.php',
                    'ORIG_SCRIPT_NAME'=> null
                ),
                '/~username/public',
                '/~username/public'
            ),
            // ZF2-206
            array(
                array(
                    'SCRIPT_NAME'     => '/zf2tut/index.php',
                    'REQUEST_URI'     => '/zf2tut/',
                    'PHP_SELF'        => '/zf2tut/index.php',
                    'SCRIPT_FILENAME' => 'c:/ZF2Tutorial/public/index.php',
                    'ORIG_SCRIPT_NAME'=> null
                ),
                '/zf2tut',
                '/zf2tut'
            ),
            array(
                array(
                    'REQUEST_URI'     => '/html/index.php/news/3?var1=val1&var2=/index.php',
                    'PHP_SELF'        => '/html/index.php/news/3',
                    'SCRIPT_FILENAME' => '/var/web/html/index.php',
                ),
                '/html/index.php',
                '/html'
            ),
            array(
                array(
                    'REQUEST_URI'     => '/html/index.php/news/index.php',
                    'PHP_SELF'        => '/html/index.php/news/index.php',
                    'SCRIPT_FILENAME' => '/var/web/html/index.php',
                ),
                '/html/index.php',
                '/html'
            ),

            //Test when url quert contains a full http url
            array(
                array(
                    'REQUEST_URI' => '/html/index.php?url=http://test.example.com/path/&foo=bar',
                    'PHP_SELF' => '/html/index.php',
                    'SCRIPT_FILENAME' => '/var/web/html/index.php',
                ),
                '/html/index.php',
                '/html'
            ),
        );
    }

    /**
     * @dataProvider baseUrlAndPathProvider
     * @param array  $server
     * @param string $baseUrl
     * @param string $basePath
     */
    public function testBasePathDetection(array $server, $baseUrl, $basePath)
    {
        $_SERVER = $server;
        $request = new Request();

        $this->assertEquals($baseUrl,  $request->getBaseUrl());
        $this->assertEquals($basePath, $request->getBasePath());
    }

    /**
     * Data provider for testing server provided headers.
     */
    public static function serverHeaderProvider()
    {
        return array(
            array(
                array(
                    'HTTP_USER_AGENT'     => 'Dummy',
                ),
                'User-Agent',
                'Dummy'
            ),
            array(
                array(
                    'CONTENT_TYPE'     => 'text/html',
                ),
                'Content-Type',
                'text/html'
            ),
            array(
                array(
                    'CONTENT_LENGTH'     => 12,
                ),
                'Content-Length',
                12
            ),
            array(
                array(
                    'CONTENT_MD5'     => md5('a'),
                ),
                'Content-MD5',
                md5('a')
            ),
        );
    }

    /**
     * @dataProvider serverHeaderProvider
     * @param array  $server
     * @param string $name
     * @param string $value
     */
    public function testHeadersWithMinus(array $server, $name, $value)
    {
        $_SERVER = $server;
        $request = new Request();

        $header = $request->getHeaders()->get($name);
        $this->assertNotEquals($header, false);
        $this->assertEquals($name,  $header->getFieldName($value));
        $this->assertEquals($value, $header->getFieldValue($value));
    }

    /**
     * @dataProvider serverHeaderProvider
     * @param array  $server
     * @param string $name
     */
    public function testRequestStringHasCorrectHeaderName(array $server, $name)
    {
        $_SERVER = $server;
        $request = new Request();

        $this->assertContains($name, $request->toString());
    }

    /**
     * Data provider for testing server hostname.
     */
    public static function serverHostnameProvider()
    {
        return array(
            array(
                array(
                    'SERVER_NAME' => 'test.example.com',
                    'REQUEST_URI' => 'http://test.example.com/news',
                ),
                'test.example.com',
                '80',
                '/news',
            ),
            array(
                array(
                    'HTTP_HOST' => 'test.example.com',
                    'REQUEST_URI' => 'http://test.example.com/news',
                ),
                'test.example.com',
                '80',
                '/news',
            ),
            array(
                array(
                    'SERVER_NAME' => '[1:2:3:4:5:6::6]',
                    'SERVER_ADDR' => '1:2:3:4:5:6::6',
                    'SERVER_PORT' => '80',
                    'REQUEST_URI' => 'http://[1:2:3:4:5:6::6]/news',
                ),
                '[1:2:3:4:5:6::6]',
                '80',
                '/news',
            ),
               // Test for broken $_SERVER implementation from Windows-Safari
            array(
                array(
                    'SERVER_NAME' => '[1:2:3:4:5:6:]',
                    'SERVER_ADDR' => '1:2:3:4:5:6::6',
                    'SERVER_PORT' => '6',
                    'REQUEST_URI' => 'http://[1:2:3:4:5:6::6]/news',
                ),
                '[1:2:3:4:5:6::6]',
                '80',
                '/news',
            ),
            array(
                array(
                    'SERVER_NAME' => 'test.example.com',
                    'SERVER_PORT' => '8080',
                    'REQUEST_URI' => 'http://test.example.com/news',
                ),
                'test.example.com',
                '8080',
                '/news',
            ),
            array(
                array(
                    'SERVER_NAME' => 'test.example.com',
                    'SERVER_PORT' => '443',
                    'HTTPS'       => 'on',
                    'REQUEST_URI' => 'https://test.example.com/news',
                ),
                'test.example.com',
                '443',
                '/news',
            ),

            //Test when url quert contains a full http url
            array(
                array(
                    'SERVER_NAME' => 'test.example.com',
                    'REQUEST_URI' => '/html/index.php?url=http://test.example.com/path/&foo=bar',
                ),
                'test.example.com',
                '80',
                '/html/index.php?url=http://test.example.com/path/&foo=bar',
            ),

        );
    }

    /**
     * @dataProvider serverHostnameProvider
     * @param array  $server
     * @param string $name
     * @param string $value
     */
    public function testServerHostnameProvider(array $server, $expectedHost, $expectedPort, $expectedRequestUri)
    {
        $_SERVER = $server;
        $request = new Request();

        $host = $request->getUri()->getHost();
        $this->assertEquals($expectedHost, $host);

        $port = $request->getUri()->getPort();
        $this->assertEquals($expectedPort, $port);

        $requestUri = $request->getRequestUri();
        $this->assertEquals($expectedRequestUri, $requestUri);
    }

    /**
     * Data provider for testing mapping $_FILES
     *
     * @return array
     */
    public static function filesProvider()
    {
        return array(
            // single file
            array(
                array(
                    'file' => array (
                        'name' => 'test1.txt',
                        'type' => 'text/plain',
                        'tmp_name' => '/tmp/phpXXX',
                        'error' => 0,
                        'size' => 1,
                    ),
                ),
                array(
                    'file' => array (
                        'name' => 'test1.txt',
                        'type' => 'text/plain',
                        'tmp_name' => '/tmp/phpXXX',
                        'error' => 0,
                        'size' => 1,
                    ),
                ),
            ),

            // file name with brackets and int keys
            // file[], file[]
            array(
                array(
                    'file' => array (
                        'name' => array (
                            0 => 'test1.txt',
                            1 => 'test2.txt',
                        ),
                        'type' => array (
                            0 => 'text/plain',
                            1 => 'text/plain',
                        ),
                        'tmp_name' => array (
                            0 => '/tmp/phpXXX',
                            1 => '/tmp/phpXXX',
                        ),
                        'error' => array (
                            0 => 0,
                            1 => 0,
                        ),
                        'size' => array (
                            0 => 1,
                            1 => 1,
                        ),
                    ),
                ),
                array(
                    'file' => array (
                        0 => array(
                            'name' => 'test1.txt',
                            'type' => 'text/plain',
                            'tmp_name' => '/tmp/phpXXX',
                            'error' => 0,
                            'size' => 1,
                        ),
                        1 => array(
                            'name' => 'test2.txt',
                            'type' => 'text/plain',
                            'tmp_name' => '/tmp/phpXXX',
                            'error' => 0,
                            'size' => 1,
                        ),
                    ),
                ),
            ),

            // file name with brackets and string keys
            // file[one], file[two]
            array(
                array(
                    'file' => array (
                        'name' => array (
                            'one' => 'test1.txt',
                            'two' => 'test2.txt',
                        ),
                        'type' => array (
                            'one' => 'text/plain',
                            'two' => 'text/plain',
                        ),
                        'tmp_name' => array (
                            'one' => '/tmp/phpXXX',
                            'two' => '/tmp/phpXXX',
                        ),
                        'error' => array (
                            'one' => 0,
                            'two' => 0,
                        ),
                        'size' => array (
                            'one' => 1,
                            'two' => 1,
                        ),
                      ),
                ),
                array(
                    'file' => array (
                        'one' => array(
                            'name' => 'test1.txt',
                            'type' => 'text/plain',
                            'tmp_name' => '/tmp/phpXXX',
                            'error' => 0,
                            'size' => 1,
                        ),
                        'two' => array(
                            'name' => 'test2.txt',
                            'type' => 'text/plain',
                            'tmp_name' => '/tmp/phpXXX',
                            'error' => 0,
                            'size' => 1,
                        ),
                    ),
                ),
            ),

            // multilevel file name
            // file[], file[][], file[][][]
            array(
                array (
                    'file' => array (
                        'name' => array (
                            0 => 'test_0.txt',
                            1 => array (
                                0 => 'test_10.txt',
                            ),
                            2 => array (
                                0 => array(
                                    0 => 'test_200.txt',
                                ),
                            ),
                        ),
                        'type' => array(
                            0 => 'text/plain',
                            1 => array(
                                0 => 'text/plain',
                            ),
                            2 => array(
                                0 => array(
                                    0 => 'text/plain',
                                ),
                            ),
                        ),
                        'tmp_name' => array (
                            0 => '/tmp/phpXXX',
                            1 => array(
                                0 => '/tmp/phpXXX',
                            ),
                            2 => array (
                                0 => array(
                                    0 => '/tmp/phpXXX',
                                ),
                            ),
                        ),
                        'error' => array(
                            0 => 0,
                            1 => array(
                                0 => 0,
                            ),
                            2 => array (
                                0 => array(
                                    0 => 0,
                                ),
                            ),
                        ),
                        'size' => array(
                            0 => 1,
                            1 => array(
                                0 => 1,
                            ),
                            2 => array(
                                0 => array(
                                    0 => 1,
                                ),
                            ),
                        ),
                    )
                ),
                array(
                    'file' => array(
                        0 => array(
                            'name' => 'test_0.txt',
                            'type' => 'text/plain',
                            'tmp_name' => '/tmp/phpXXX',
                            'error' => 0,
                            'size' => 1,
                        ),
                        1 => array(
                            0 => array(
                                'name' => 'test_10.txt',
                                'type' => 'text/plain',
                                'tmp_name' => '/tmp/phpXXX',
                                'error' => 0,
                                'size' => 1,
                            ),
                        ),
                        2 => array(
                            0 => array(
                                0 => array(
                                    'name' => 'test_200.txt',
                                    'type' => 'text/plain',
                                    'tmp_name' => '/tmp/phpXXX',
                                    'error' => 0,
                                    'size' => 1,
                                ),
                            ),
                        ),
                    )
                ),
            ),
        );
    }

    /**
     * @param array $files
     * @param array $expectedFiles
     * @dataProvider filesProvider
     */
    public function testRequestMapsPhpFies(array $files, array $expectedFiles)
    {
        $_FILES = $files;
        $request = new Request();
        $this->assertEquals($expectedFiles, $request->getFiles()->toArray());
    }

    public function testParameterRetrievalDefaultValue()
    {
        $request = new Request();
        $p = new \Zend\Stdlib\Parameters(array(
            'foo' => 'bar'
        ));
        $request->setQuery($p);
        $request->setPost($p);
        $request->setFiles($p);
        $request->setServer($p);
        $request->setEnv($p);

        $default = 15;
        $this->assertSame($default, $request->getQuery('baz', $default));
        $this->assertSame($default, $request->getPost('baz', $default));
        $this->assertSame($default, $request->getFiles('baz', $default));
        $this->assertSame($default, $request->getServer('baz', $default));
        $this->assertSame($default, $request->getEnv('baz', $default));
        $this->assertSame($default, $request->getHeaders('baz', $default));
        $this->assertSame($default, $request->getHeader('baz', $default));
    }

    public function testRetrievingASingleValueForParameters()
    {
        $request = new Request();
        $p = new \Zend\Stdlib\Parameters(array(
            'foo' => 'bar'
        ));
        $request->setQuery($p);
        $request->setPost($p);
        $request->setFiles($p);
        $request->setServer($p);
        $request->setEnv($p);

        $this->assertSame('bar', $request->getQuery('foo'));
        $this->assertSame('bar', $request->getPost('foo'));
        $this->assertSame('bar', $request->getFiles('foo'));
        $this->assertSame('bar', $request->getServer('foo'));
        $this->assertSame('bar', $request->getEnv('foo'));

        $headers = new Headers();
        $h = new GenericHeader('foo','bar');
        $headers->addHeader($h);

        $request->setHeaders($headers);
        $this->assertSame($headers, $request->getHeaders());
        $this->assertSame($h, $request->getHeaders()->get('foo'));
        $this->assertSame($h, $request->getHeader('foo'));
    }

    /**
     * @group ZF2-480
     */
    public function testBaseurlFallsBackToRootPathIfScriptFilenameIsNotSet()
    {
        $request = new Request();
        $server  = $request->getServer();
        $server->set('SCRIPT_NAME', null);
        $server->set('PHP_SELF', null);
        $server->set('ORIG_SCRIPT_NAME', null);
        $server->set('ORIG_SCRIPT_NAME', null);
        $server->set('SCRIPT_FILENAME', null);

        $this->assertEquals('', $request->getBaseUrl());
    }
}
