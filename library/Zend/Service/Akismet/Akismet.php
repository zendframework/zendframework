<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
 */

namespace Zend\Service\Akismet;

use Zend\Http\Client as HttpClient;
use Zend\Http\Request as HttpRequest;
use Zend\Uri;

/**
 * Akismet REST service implementation
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Akismet
 */
class Akismet
{
    /**
     * Akismet API key
     * @var string
     */
    protected $apiKey;

    /**
     * Blog URL
     * @var string
     */
    protected $blogUrl;

    /**
     * @var HttpClient
     */
    protected $httpClient = null;

    /**
     * Charset used for encoding
     * @var string
     */
    protected $charset = 'UTF-8';

    /**
     * TCP/IP port to use in requests
     * @var int
     */
    protected $port = 80;

    /**
     * User Agent string to send in requests
     * @var string
     */
    protected $userAgent;

    /**
     * Constructor
     *
     * @param string $apiKey Akismet API key
     * @param string $blog Blog URL
     * @return void
     */
    public function __construct($apiKey, $blog, HttpClient $httpClient = null)
    {
        $this->setBlogUrl($blog);
        $this->setApiKey($apiKey);
        $this->setHttpClient(($httpClient) ?: new HttpClient);
        $this->setUserAgent('Zend Framework/' . \Zend\Version::VERSION . ' | Akismet/1.11');
    }

    /**
     * Retrieve API key
     *
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * Set API key
     *
     * @param string $apiKey
     * @return Zend_Service_Akismet
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
        return $this;
    }

    /**
     * Retrieve blog URL
     *
     * @return string
     */
    public function getBlogUrl()
    {
        return $this->blogUrl;
    }

    /**
     * Set blog URL
     *
     * @param string $blogUrl
     * @return Zend_Service_Akismet
     * @throws Zend\Service\Exception if invalid URL provided
     */
    public function setBlogUrl($blogUrl)
    {
        if (!Uri\UriFactory::factory($blogUrl)->isValid()) {
            throw new Exception\InvalidArgumentException('Invalid url provided for blog');
        }

        $this->blogUrl = $blogUrl;
        return $this;
    }

    /**
     * @param HttpClient $httpClient
     * @return Akismet
     */
    public function setHttpClient(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
        return $this;
    }

    /**
     * @return HttpClient
     */
    public function getHttpClient()
    {
        return $this->httpClient;
    }

    /**
     * Retrieve charset
     *
     * @return string
     */
    public function getCharset()
    {
        return $this->charset;
    }

    /**
     * Set charset
     *
     * @param string $charset
     * @return Zend_Service_Akismet
     */
    public function setCharset($charset)
    {
        $this->charset = $charset;
        return $this;
    }

    /**
     * Retrieve TCP/IP port
     *
     * @return int
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Set TCP/IP port
     *
     * @param int $port
     * @return Zend_Service_Akismet
     * @throws Zend\Service\Exception if non-integer value provided
     */
    public function setPort($port)
    {
        if (!is_int($port)) {
            throw new Exception\InvalidArgumentException('Invalid port');
        }

        $this->port = $port;
        return $this;
    }

    /**
     * Retrieve User Agent string
     *
     * @return string
     */
    public function getUserAgent()
    {
        return $this->userAgent;
    }

    /**
     * Set User Agent
     *
     * Should be of form "Some user agent/version | Akismet/version"
     *
     * @param string $userAgent
     * @return Zend_Service_Akismet
     * @throws Zend\Service\Exception with invalid user agent string
     */
    public function setUserAgent($userAgent)
    {
        if (!is_string($userAgent)
            || !preg_match(":^[^\n/]*/[^ ]* \| Akismet/[0-9\.]*$:i", $userAgent))
        {
            throw new Exception\InvalidArgumentException('Invalid User Agent string; must be of format "Application name/version | Akismet/version"');
        }

        $this->userAgent = $userAgent;
        return $this;
    }

    /**
     * Verify an API key
     *
     * @param string $key Optional; API key to verify
     * @param string $blog Optional; blog URL against which to verify key
     * @return boolean
     */
    public function verifyKey($key = null, $blog = null)
    {
        if (null === $key) {
            $key = $this->getApiKey();
        }

        if (null === $blog) {
            $blog = $this->getBlogUrl();
        }

        $response = $this->post('rest.akismet.com', '/1.1/verify-key', array(
            'key'  => $key,
            'blog' => $blog
        ));

        return ('valid' == $response->getBody());
    }



    /**
     * Check a comment for spam
     *
     * Checks a comment to see if it is spam. $params should be an associative
     * array with one or more of the following keys (unless noted, all keys are
     * optional):
     * - blog: URL of the blog. If not provided, uses value returned by {@link getBlogUrl()}
     * - user_ip (required): IP address of comment submitter
     * - user_agent (required): User Agent used by comment submitter
     * - referrer: contents of HTTP_REFERER header
     * - permalink: location of the entry to which the comment was submitted
     * - comment_type: typically, one of 'blank', 'comment', 'trackback', or 'pingback', but may be any value
     * - comment_author: name submitted with the content
     * - comment_author_email: email submitted with the content
     * - comment_author_url: URL submitted with the content
     * - comment_content: actual content
     *
     * Additionally, Akismet suggests returning the key/value pairs in the
     * $_SERVER array, and these may be included in the $params.
     *
     * This method implements the Akismet comment-check REST method.
     *
     * @param array $params
     * @return boolean
     * @throws Zend\Service\Exception with invalid API key
     */
    public function isSpam($params)
    {
        $response = $this->makeApiCall('/1.1/comment-check', $params);

        $return = trim($response->getBody());

        if ('invalid' == $return) {
            throw new Exception\InvalidArgumentException('Invalid API key');
        }

        if ('true' == $return) {
            return true;
        }

        return false;
    }

    /**
     * Submit spam
     *
     * Takes the same arguments as {@link isSpam()}.
     *
     * Submits known spam content to Akismet to help train it.
     *
     * This method implements Akismet's submit-spam REST method.
     *
     * @param array $params
     * @return void
     * @throws Zend\Service\Exception with invalid API key
     */
    public function submitSpam($params)
    {
        $response = $this->makeApiCall('/1.1/submit-spam', $params);
        $value    = trim($response->getBody());
        if ('invalid' == $value) {
            throw new Exception\InvalidArgumentException('Invalid API key');
        }
    }

    /**
     * Submit ham
     *
     * Takes the same arguments as {@link isSpam()}.
     *
     * Submits a comment that has been falsely categorized as spam by Akismet
     * as a false positive, telling Akismet's filters not to filter such
     * comments as spam in the future.
     *
     * Unlike {@link submitSpam()} and {@link isSpam()}, a valid API key is
     * never necessary; as a result, this method never throws an exception
     * (unless an exception happens with the HTTP client layer).
     *
     * this method implements Akismet's submit-ham REST method.
     *
     * @param array $params
     * @return void
     */
    public function submitHam($params)
    {
        $this->makeApiCall('/1.1/submit-ham', $params);
    }

    /**
     * Perform an API call
     *
     * @param string $path
     * @param array $params
     * @return Zend\Http\Response
     * @throws Zend\Service\Exception if missing user_ip or user_agent fields
     */
    protected function makeApiCall($path, $params)
    {
        if (empty($params['user_ip']) || empty($params['user_agent'])) {
            throw new Exception\InvalidArgumentException('Missing required Akismet fields (user_ip and user_agent are required)');
        }

        if (!isset($params['blog'])) {
            $params['blog'] = $this->getBlogUrl();
        }

        return $this->post($this->getApiKey() . '.rest.akismet.com', $path, $params);
    }


    /**
     * Post a request
     *
     * @param string $host
     * @param string $path
     * @param array  $params
     * @return mixed
     */
    protected function post($host, $path, array $params)
    {
        $uri    = 'http://' . $host . ':' . $this->getPort() . $path;
        $client = $this->getHttpClient();
        $client->setUri($uri);
        $client->setOptions(array(
            'useragent'    => $this->getUserAgent(),
        ));

        $client->setHeaders(array(
            'Host'         => $host,
            'Content-Type' => 'application/x-www-form-urlencoded; charset=' . $this->getCharset()
        ));
        $client->setParameterPost($params);

        $client->setMethod(HttpRequest::METHOD_POST);
        return $client->send();
    }

}
