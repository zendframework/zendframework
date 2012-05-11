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
 * @package    Zend_Feed_Reader
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Feed\Reader;

use Zend\Cache\Storage\Adapter\AdapterInterface as CacheAdapter,
    Zend\Http,
    Zend\Loader,
    Zend\Stdlib\ErrorHandler,
    DOMDocument,
    DOMXPath;

/**
* @category Zend
* @package Zend_Feed_Reader
* @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
* @license http://framework.zend.com/license/new-bsd New BSD License
*/
class Reader
{
    /**
     * Namespace constants
     */
    const NAMESPACE_ATOM_03  = 'http://purl.org/atom/ns#';
    const NAMESPACE_ATOM_10  = 'http://www.w3.org/2005/Atom';
    const NAMESPACE_RDF      = 'http://www.w3.org/1999/02/22-rdf-syntax-ns#';
    const NAMESPACE_RSS_090  = 'http://my.netscape.com/rdf/simple/0.9/';
    const NAMESPACE_RSS_10   = 'http://purl.org/rss/1.0/';

    /**
     * Feed type constants
     */
    const TYPE_ANY              = 'any';
    const TYPE_ATOM_03          = 'atom-03';
    const TYPE_ATOM_10          = 'atom-10';
    const TYPE_ATOM_10_ENTRY    = 'atom-10-entry';
    const TYPE_ATOM_ANY         = 'atom';
    const TYPE_RSS_090          = 'rss-090';
    const TYPE_RSS_091          = 'rss-091';
    const TYPE_RSS_091_NETSCAPE = 'rss-091n';
    const TYPE_RSS_091_USERLAND = 'rss-091u';
    const TYPE_RSS_092          = 'rss-092';
    const TYPE_RSS_093          = 'rss-093';
    const TYPE_RSS_094          = 'rss-094';
    const TYPE_RSS_10           = 'rss-10';
    const TYPE_RSS_20           = 'rss-20';
    const TYPE_RSS_ANY          = 'rss';

    /**
     * Cache instance
     *
     * @var CacheAdapter
     */
    protected static $_cache = null;

    /**
     * HTTP client object to use for retrieving feeds
     *
     * @var \Zend\Http\Client
     */
    protected static $_httpClient = null;

    /**
     * Override HTTP PUT and DELETE request methods?
     *
     * @var boolean
     */
    protected static $_httpMethodOverride = false;

    protected static $_httpConditionalGet = false;

    protected static $_pluginLoader = null;

    protected static $_prefixPaths = array();

    protected static $_extensions = array(
        'feed' => array(
            'DublinCore\Feed',
            'Atom\Feed'
        ),
        'entry' => array(
            'Content\Entry',
            'DublinCore\Entry',
            'Atom\Entry'
        ),
        'core' => array(
            'DublinCore\Feed',
            'Atom\Feed',
            'Content\Entry',
            'DublinCore\Entry',
            'Atom\Entry'
        )
    );

    /**
     * Get the Feed cache
     *
     * @return CacheAdapter
     */
    public static function getCache()
    {
        return self::$_cache;
    }

    /**
     * Set the feed cache
     *
     * @param  CacheAdapter $cache
     * @return void
     */
    public static function setCache(CacheAdapter $cache)
    {
        self::$_cache = $cache;
    }

    /**
     * Set the HTTP client instance
     *
     * Sets the HTTP client object to use for retrieving the feeds.
     *
     * @param  \Zend\Http\Client $httpClient
     * @return void
     */
    public static function setHttpClient(Http\Client $httpClient)
    {
        self::$_httpClient = $httpClient;
    }


    /**
     * Gets the HTTP client object. If none is set, a new \Zend\Http\Client will be used.
     *
     * @return \Zend\Http\Client
     */
    public static function getHttpClient()
    {
        if (!self::$_httpClient instanceof Http\Client) {
            self::$_httpClient = new Http\Client();
        }

        return self::$_httpClient;
    }

    /**
     * Toggle using POST instead of PUT and DELETE HTTP methods
     *
     * Some feed implementations do not accept PUT and DELETE HTTP
     * methods, or they can't be used because of proxies or other
     * measures. This allows turning on using POST where PUT and
     * DELETE would normally be used; in addition, an
     * X-Method-Override header will be sent with a value of PUT or
     * DELETE as appropriate.
     *
     * @param  boolean $override Whether to override PUT and DELETE.
     * @return void
     */
    public static function setHttpMethodOverride($override = true)
    {
        self::$_httpMethodOverride = $override;
    }

    /**
     * Get the HTTP override state
     *
     * @return boolean
     */
    public static function getHttpMethodOverride()
    {
        return self::$_httpMethodOverride;
    }

    /**
     * Set the flag indicating whether or not to use HTTP conditional GET
     *
     * @param  bool $bool
     * @return void
     */
    public static function useHttpConditionalGet($bool = true)
    {
        self::$_httpConditionalGet = $bool;
    }

    /**
     * Import a feed by providing a URL
     *
     * @param  string $url The URL to the feed
     * @param  string $etag OPTIONAL Last received ETag for this resource
     * @param  string $lastModified OPTIONAL Last-Modified value for this resource
     * @return Reader
     * @throws Exception\ExceptionInterface
     */
    public static function import($uri, $etag = null, $lastModified = null)
    {
        $cache       = self::getCache();
        $feed        = null;
        $responseXml = '';
        $client      = self::getHttpClient();
        $client->resetParameters();
        $headers = new Http\Headers();
        $client->setHeaders($headers);
        $client->setUri($uri);
        $cacheId = 'Zend_Feed_Reader_' . md5($uri);

        if (self::$_httpConditionalGet && $cache) {
            $data = $cache->getItem($cacheId);
            if ($data) {
                if ($etag === null) {
                    $etag = $cache->getItem($cacheId.'_etag');
                }
                if ($lastModified === null) {
                    $lastModified = $cache->getItem($cacheId.'_lastmodified');;
                }
                if ($etag) {
                    $headers->addHeaderLine('If-None-Match', $etag);
                }
                if ($lastModified) {
                    $headers->addHeaderLine('If-Modified-Since', $lastModified);
                }
            }
            $response = $client->send();
            if ($response->getStatusCode() !== 200 && $response->getStatusCode() !== 304) {
                throw new Exception\RuntimeException('Feed failed to load, got response code ' . $response->getStatusCode());
            }
            if ($response->getStatusCode() == 304) {
                $responseXml = $data;
            } else {
                $responseXml = $response->getBody();
                $cache->setItem($cacheId, $responseXml);
                if ($response->headers()->get('ETag')) {
                    $cache->setItem($cacheId . '_etag', $response->headers()->get('ETag')->getFieldValue());
                }
                if ($response->headers()->get('Last-Modified')) {
                    $cache->setItem($cacheId . '_lastmodified', $response->headers()->get('Last-Modified')->getFieldValue());
                }
            }
            return self::importString($responseXml);
        } elseif ($cache) {
            $data = $cache->getItem($cacheId);
            if ($data !== false) {
                return self::importString($data);
            }
            $response = $client->send();
            if ((int)$response->getStatusCode() !== 200) {
                throw new Exception\RuntimeException('Feed failed to load, got response code ' . $response->getStatusCode());
            }
            $responseXml = $response->getBody();
            $cache->setItem($cacheId, $responseXml);
            return self::importString($responseXml);
        } else {
            $response = $client->send();
            if ((int)$response->getStatusCode() !== 200) {
                throw new Exception\RuntimeException('Feed failed to load, got response code ' . $response->getStatusCode());
            }
            $reader = self::importString($response->getBody());
            $reader->setOriginalSourceUri($uri);
            return $reader;
        }
    }

    /**
     * Import a feed from a string
     *
     * @param  string $string
     * @return Feed\FeedInterface
     * @throws Exception\RuntimeException
     */
    public static function importString($string)
    {
        $libxml_errflag = libxml_use_internal_errors(true);
        $dom = new DOMDocument;
        $status = $dom->loadXML($string);
        libxml_use_internal_errors($libxml_errflag);

        if (!$status) {
            // Build error message
            $error = libxml_get_last_error();
            if ($error && $error->message) {
                $errormsg = "DOMDocument cannot parse XML: {$error->message}";
            } else {
                $errormsg = "DOMDocument cannot parse XML: Please check the XML document's validity";
            }
            throw new Exception\RuntimeException($errormsg);
        }

        $type = self::detectType($dom);

        self::_registerCoreExtensions();

        if (substr($type, 0, 3) == 'rss') {
            $reader = new Feed\Rss($dom, $type);
        } elseif (substr($type, 8, 5) == 'entry') {
            $reader = new Entry\Atom($dom->documentElement, 0, self::TYPE_ATOM_10);
        } elseif (substr($type, 0, 4) == 'atom') {
            $reader = new Feed\Atom($dom, $type);
        } else {
            throw new Exception\RuntimeException('The URI used does not point to a '
            . 'valid Atom, RSS or RDF feed that Zend_Feed_Reader can parse.');
        }
        return $reader;
    }

    /**
     * Imports a feed from a file located at $filename.
     *
     * @param  string $filename
     * @throws Exception\RuntimeException
     * @return Feed\FeedInterface
     */
    public static function importFile($filename)
    {
        ErrorHandler::start();
        $feed = file_get_contents($filename);
        $err  = ErrorHandler::stop();
        if ($feed === false) {
            throw new Exception\RuntimeException("File '{$filename}' could not be loaded", 0, $err);
        }
        return self::importString($feed);
    }

    /**
     * Find feed links
     *
     * @param $uri
     * @return FeedSet
     * @throws Exception\RuntimeException
     */
    public static function findFeedLinks($uri)
    {
        $client = self::getHttpClient();
        $client->setUri($uri);
        $response = $client->send();
        if ($response->getStatusCode() !== 200) {
            throw new Exception\RuntimeException("Failed to access $uri, got response code " . $response->getStatusCode());
        }
        $responseHtml = $response->getBody();
        $libxml_errflag = libxml_use_internal_errors(true);
        $dom = new DOMDocument;
        $status = $dom->loadHTML($responseHtml);
        libxml_use_internal_errors($libxml_errflag);
        if (!$status) {
            // Build error message
            $error = libxml_get_last_error();
            if ($error && $error->message) {
                $errormsg = "DOMDocument cannot parse HTML: {$error->message}";
            } else {
                $errormsg = "DOMDocument cannot parse HTML: Please check the XML document's validity";
            }
            throw new Exception\RuntimeException($errormsg);
        }
        $feedSet = new FeedSet;
        $links = $dom->getElementsByTagName('link');
        $feedSet->addLinks($links, $uri);
        return $feedSet;
    }

    /**
     * Detect the feed type of the provided feed
     *
     * @param  Feed\AbstractFeed|DOMDocument|string $feed
     * @return string
     * @throws Exception\ExceptionInterface
     */
    public static function detectType($feed, $specOnly = false)
    {
        if ($feed instanceof Feed\AbstractFeed) {
            $dom = $feed->getDomDocument();
        } elseif($feed instanceof DOMDocument) {
            $dom = $feed;
        } elseif(is_string($feed) && !empty($feed)) {
            @ini_set('track_errors', 1);
            $dom = new DOMDocument;
            $status = @$dom->loadXML($feed);
            @ini_restore('track_errors');
            if (!$status) {
                if (!isset($php_errormsg)) {
                    if (function_exists('xdebug_is_enabled')) {
                        $php_errormsg = '(error message not available, when XDebug is running)';
                    } else {
                        $php_errormsg = '(error message not available)';
                    }
                }
                throw new Exception\RuntimeException("DOMDocument cannot parse XML: $php_errormsg");
            }
        } else {
            throw new Exception\InvalidArgumentException('Invalid object/scalar provided: must'
            . ' be of type Zend\Feed\Reader\Feed, DomDocument or string');
        }
        $xpath = new DOMXPath($dom);

        if ($xpath->query('/rss')->length) {
            $type = self::TYPE_RSS_ANY;
            $version = $xpath->evaluate('string(/rss/@version)');

            if (strlen($version) > 0) {
                switch($version) {
                    case '2.0':
                        $type = self::TYPE_RSS_20;
                        break;

                    case '0.94':
                        $type = self::TYPE_RSS_094;
                        break;

                    case '0.93':
                        $type = self::TYPE_RSS_093;
                        break;

                    case '0.92':
                        $type = self::TYPE_RSS_092;
                        break;

                    case '0.91':
                        $type = self::TYPE_RSS_091;
                        break;
                }
            }

            return $type;
        }

        $xpath->registerNamespace('rdf', self::NAMESPACE_RDF);

        if ($xpath->query('/rdf:RDF')->length) {
            $xpath->registerNamespace('rss', self::NAMESPACE_RSS_10);

            if ($xpath->query('/rdf:RDF/rss:channel')->length
                || $xpath->query('/rdf:RDF/rss:image')->length
                || $xpath->query('/rdf:RDF/rss:item')->length
                || $xpath->query('/rdf:RDF/rss:textinput')->length
            ) {
                return self::TYPE_RSS_10;
            }

            $xpath->registerNamespace('rss', self::NAMESPACE_RSS_090);

            if ($xpath->query('/rdf:RDF/rss:channel')->length
                || $xpath->query('/rdf:RDF/rss:image')->length
                || $xpath->query('/rdf:RDF/rss:item')->length
                || $xpath->query('/rdf:RDF/rss:textinput')->length
            ) {
                return self::TYPE_RSS_090;
            }
        }

        $type = self::TYPE_ATOM_ANY;
        $xpath->registerNamespace('atom', self::NAMESPACE_ATOM_10);

        if ($xpath->query('//atom:feed')->length) {
            return self::TYPE_ATOM_10;
        }

        if ($xpath->query('//atom:entry')->length) {
            if ($specOnly == true) {
                return self::TYPE_ATOM_10;
            } else {
                return self::TYPE_ATOM_10_ENTRY;
            }
        }

        $xpath->registerNamespace('atom', self::NAMESPACE_ATOM_03);

        if ($xpath->query('//atom:feed')->length) {
            return self::TYPE_ATOM_03;
        }

        return self::TYPE_ANY;
    }

    /**
     * Set plugin loader for use with Extensions
     *
     * @param  \Zend\Loader\ShortNameLocator $loader
     */
    public static function setPluginLoader(Loader\ShortNameLocator $loader)
    {
        self::$_pluginLoader = $loader;
    }

    /**
     * Get plugin loader for use with Extensions
     *
     * @return  \Zend\Loader\PrefixPathLoader $loader
     */
    public static function getPluginLoader()
    {
        if (!isset(self::$_pluginLoader)) {
            self::setPluginLoader(new Loader\PrefixPathLoader(array(
                'Zend\Feed\Reader\Extension\\' => 'Zend/Feed/Reader/Extension/',
            )));
        }
        return self::$_pluginLoader;
    }

    /**
     * Add prefix path for loading Extensions
     *
     * @param  string $prefix
     * @param  string $path
     * @return void
     */
    public static function addPrefixPath($prefix, $path)
    {
        $pluginLoader = self::getPluginLoader();
        if ($pluginLoader instanceof Loader\PrefixPathMapper) {
            $prefix = rtrim($prefix, '\\');
            $path = rtrim($path, DIRECTORY_SEPARATOR);
            $pluginLoader->addPrefixPath($prefix, $path);
        }
    }

    /**
     * Add multiple Extension prefix paths at once
     *
     * @param  array $spec
     * @return void
     */
    public static function addPrefixPaths(array $spec)
    {
        $pluginLoader = self::getPluginLoader();
        if (!$pluginLoader instanceof Loader\PrefixPathMapper) {
            return;
        }
        if (isset($spec['prefix']) && isset($spec['path'])) {
            self::addPrefixPath($spec['prefix'], $spec['path']);
        }
        foreach ($spec as $prefixPath) {
            if (isset($prefixPath['prefix']) && isset($prefixPath['path'])) {
                self::addPrefixPath($prefixPath['prefix'], $prefixPath['path']);
            }
        }
    }

    /**
     * Register an Extension by name
     *
     * @param  string $name
     * @return void
     * @throws Exception\RuntimeException if unable to resolve Extension class
     */
    public static function registerExtension($name)
    {
        $feedName  = $name . '\Feed';
        $entryName = $name . '\Entry';
        $loader    = self::getPluginLoader();
        if (self::isRegistered($name)) {
            if ($loader->isLoaded($feedName) || $loader->isLoaded($entryName)) {
                return;
            }
        }
        $loader->load($feedName);
        $loader->load($entryName);
        if (!$loader->isLoaded($feedName) && !$loader->isLoaded($entryName)) {
            throw new Exception\RuntimeException('Could not load extension: ' . $name
                . ' using Plugin Loader. Check prefix paths are configured and extension exists.');
        }
        if ($loader->isLoaded($feedName)) {
            self::$_extensions['feed'][] = $feedName;
        }
        if ($loader->isLoaded($entryName)) {
            self::$_extensions['entry'][] = $entryName;
        }
    }

    /**
     * Is a given named Extension registered?
     *
     * @param  string $extensionName
     * @return boolean
     */
    public static function isRegistered($extensionName)
    {
        $feedName  = $extensionName . '\Feed';
        $entryName = $extensionName . '\Entry';
        if (in_array($feedName, self::$_extensions['feed'])
            || in_array($entryName, self::$_extensions['entry'])
        ) {
            return true;
        }
        return false;
    }

    /**
     * Get a list of extensions
     *
     * @return array
     */
    public static function getExtensions()
    {
        return self::$_extensions;
    }

    /**
     * Reset class state to defaults
     *
     * @return void
     */
    public static function reset()
    {
        self::$_cache              = null;
        self::$_httpClient         = null;
        self::$_httpMethodOverride = false;
        self::$_httpConditionalGet = false;
        self::$_pluginLoader       = null;
        self::$_prefixPaths        = array();
        self::$_extensions         = array(
            'feed' => array(
                'DublinCore\Feed',
                'Atom\Feed'
            ),
            'entry' => array(
                'Content\Entry',
                'DublinCore\Entry',
                'Atom\Entry'
            ),
            'core' => array(
                'DublinCore\Feed',
                'Atom\Feed',
                'Content\Entry',
                'DublinCore\Entry',
                'Atom\Entry'
            )
        );
    }

    /**
     * Register core (default) extensions
     *
     * @return void
     */
    protected static function _registerCoreExtensions()
    {
        self::registerExtension('DublinCore');
        self::registerExtension('Content');
        self::registerExtension('Atom');
        self::registerExtension('Slash');
        self::registerExtension('WellFormedWeb');
        self::registerExtension('Thread');
        self::registerExtension('Podcast');
    }

    /**
     * Utility method to apply array_unique operation to a multidimensional
     * array.
     *
     * @param array
     * @return array
     */
    public static function arrayUnique(array $array)
    {
        foreach ($array as &$value) {
            $value = serialize($value);
        }
        $array = array_unique($array);
        foreach ($array as &$value) {
            $value = unserialize($value);
        }
        return $array;
    }

}
