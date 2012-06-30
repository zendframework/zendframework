<?php
namespace ZendTest\Http\PhpEnvironment;

use PHPUnit_Framework_TestCase as TestCase,
    Zend\Http\PhpEnvironment\Request;

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
        );

        $_POST   = array();
        $_GET    = array();
        $_COOKIE = array();
        $_SERVER = array();
        $_ENV    = array();
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
                '/news',
            ),
            array(
                array(
                    'HTTP_HOST' => 'test.example.com',
                    'REQUEST_URI' => 'http://test.example.com/news',
                ),
                'test.example.com',
                '/news',
            ),
            array(
                array(
                    'SERVER_NAME' => 'test.example.com',
                    'SERVER_PORT' => '8080',
                    'REQUEST_URI' => 'http://test.example.com/news',
                ),
                'test.example.com',
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
                '/news',
            ),
        );
    }

    /**
     * @dataProvider serverHostnameProvider
     * @param array  $server
     * @param string $name
     * @param string $value
     */
    public function testServerHostnameProvider(array $server, $expectedHost, $expectedRequestUri)
    {
        $_SERVER = $server;
        $request = new Request();

        $host = $request->getUri()->getHost();
        $this->assertEquals($expectedHost, $host);

        $requestUri = $request->getRequestUri();
        $this->assertEquals($expectedRequestUri, $requestUri);
    }
}
