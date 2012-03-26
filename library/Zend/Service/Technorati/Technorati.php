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
 * @package    Zend_Service
 * @subpackage Technorati
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Service\Technorati;

use DomDocument,
    Zend\Http\Response,
    Zend\Rest\Client\RestClient;

/**
 * Zend\Service\Technorati provides an easy, intuitive and object-oriented interface
 * for using the Technorati API.
 *
 * It provides access to all available Technorati API queries
 * and returns the original XML response as a friendly PHP object.
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Technorati
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Technorati
{
    /** Base Technorati API URI */
    const API_URI_BASE = 'http://api.technorati.com';

    /** Query paths */
    const API_PATH_COSMOS           = '/cosmos';
    const API_PATH_SEARCH           = '/search';
    const API_PATH_TAG              = '/tag';
    const API_PATH_DAILYCOUNTS      = '/dailycounts';
    const API_PATH_TOPTAGS          = '/toptags';
    const API_PATH_BLOGINFO         = '/bloginfo';
    const API_PATH_BLOGPOSTTAGS     = '/blogposttags';
    const API_PATH_GETINFO          = '/getinfo';
    const API_PATH_KEYINFO          = '/keyinfo';

    /** Prevent magic numbers */
    const PARAM_LIMIT_MIN_VALUE = 1;
    const PARAM_LIMIT_MAX_VALUE = 100;
    const PARAM_DAYS_MIN_VALUE  = 1;
    const PARAM_DAYS_MAX_VALUE  = 180;
    const PARAM_START_MIN_VALUE = 1;


    /**
     * Technorati API key
     *
     * @var     string
     * @access  protected
     */
    protected $apiKey;

    /**
     * RestClient instance
     *
     * @var     RestClient
     * @access  protected
     */
    protected $restClient;


    /**
     * Constructs a new Zend\Service\Technorati instance
     * and setup character encoding.
     *
     * @param  string $apiKey  Your Technorati API key
     */
    public function __construct($apiKey)
    {
        iconv_set_encoding('output_encoding', 'UTF-8');
        iconv_set_encoding('input_encoding', 'UTF-8');
        iconv_set_encoding('internal_encoding', 'UTF-8');

        $this->apiKey = $apiKey;
    }


    /**
     * Cosmos query lets you see what blogs are linking to a given URL.
     *
     * On the Technorati site, you can enter a URL in the searchbox and
     * it will return a list of blogs linking to it.
     * The API version allows more features and gives you a way
     * to use the cosmos on your own site.
     *
     * Query options include:
     *
     * 'type'       => (link|weblog)
     *      optional - A value of link returns the freshest links referencing your target URL.
     *      A value of weblog returns the last set of unique weblogs referencing your target URL.
     * 'limit'      => (int)
     *      optional - adjust the size of your result from the default value of 20
     *      to between 1 and 100 results.
     * 'start'      => (int)
     *      optional - adjust the range of your result set.
     *      Set this number to larger than zero and you will receive
     *      the portion of Technorati's total result set ranging from start to start+limit.
     *      The default start value is 1.
     * 'current'    => (true|false)
     *      optional - the default setting of true
     *      Technorati returns links that are currently on a weblog's homepage.
     *      Set this parameter to false if you would like to receive all links
     *      to the given URL regardless of their current placement on the source blog.
     *      Internally the value is converted in (yes|no).
     * 'claim'      => (true|false)
     *      optional - the default setting of FALSE returns no user information
     *      about each weblog included in the result set when available.
     *      Set this parameter to FALSE to include Technorati member data
     *      in the result set when a weblog in your result set
     *      has been successfully claimed by a member of Technorati.
     *      Internally the value is converted in (int).
     * 'highlight'  => (true|false)
     *      optional - the default setting of TRUE
     *      highlights the citation of the given URL within the weblog excerpt.
     *      Set this parameter to FALSE to apply no special markup to the blog excerpt.
     *      Internally the value is converted in (int).
     *
     * @param   string $url     the URL you are searching for. Prefixes http:// and www. are optional.
     * @param   array $options  additional parameters to refine your query
     * @return  CosmosResultSet
     * @throws  Exception\RuntimeException
     * @link    http://technorati.com/developers/api/cosmos.html Technorati API: Cosmos Query reference
     */
    public function cosmos($url, $options = null)
    {
        static $defaultOptions = array( 'type'      => 'link',
                                        'start'     => 1,
                                        'limit'     => 20,
                                        'current'   => 'yes',
                                        'format'    => 'xml',
                                        'claim'     => 0,
                                        'highlight' => 1,
                                        );

        $options['url'] = $url;

        $options = $this->prepareOptions($options, $defaultOptions);
        $this->validateCosmos($options);
        $response = $this->makeRequest(self::API_PATH_COSMOS, $options);
        $dom = $this->convertResponseAndCheckContent($response);

        return new CosmosResultSet($dom, $options);
    }

    /**
     * Search lets you see what blogs contain a given search string.
     *
     * Query options include:
     *
     * 'language'   => (string)
     *      optional - a ISO 639-1 two character language code
     *      to retrieve results specific to that language.
     *      This feature is currently beta and may not work for all languages.
     * 'authority'  => (n|a1|a4|a7)
     *      optional - filter results to those from blogs with at least
     *      the Technorati Authority specified.
     *      Technorati calculates a blog's authority by how many people link to it.
     *      Filtering by authority is a good way to refine your search results.
     *      There are four settings:
     *      - n  => Any authority: All results.
     *      - a1 => A little authority: Results from blogs with at least one link.
     *      - a4 => Some authority: Results from blogs with a handful of links.
     *      - a7 => A lot of authority: Results from blogs with hundreds of links.
     * 'limit'      => (int)
     *      optional - adjust the size of your result from the default value of 20
     *      to between 1 and 100 results.
     * 'start'      => (int)
     *      optional - adjust the range of your result set.
     *      Set this number to larger than zero and you will receive
     *      the portion of Technorati's total result set ranging from start to start+limit.
     *      The default start value is 1.
     * 'claim'      => (true|false)
     *      optional - the default setting of FALSE returns no user information
     *      about each weblog included in the result set when available.
     *      Set this parameter to FALSE to include Technorati member data
     *      in the result set when a weblog in your result set
     *      has been successfully claimed by a member of Technorati.
     *      Internally the value is converted in (int).
     *
     * @param   string $query   the words you are searching for.
     * @param   array $options  additional parameters to refine your query
     * @return  SearchResultSet
     * @throws  Exception\RuntimeException
     * @link    http://technorati.com/developers/api/search.html Technorati API: Search Query reference
     */
    public function search($query, $options = null)
    {
        static $defaultOptions = array( 'start'     => 1,
                                        'limit'     => 20,
                                        'format'    => 'xml',
                                        'claim'     => 0);

        $options['query'] = $query;

        $options = $this->prepareOptions($options, $defaultOptions);
        $this->validateSearch($options);
        $response = $this->makeRequest(self::API_PATH_SEARCH, $options);
        $dom = $this->convertResponseAndCheckContent($response);

        return new SearchResultSet($dom, $options);
    }

    /**
     * Tag lets you see what posts are associated with a given tag.
     *
     * Query options include:
     *
     * 'limit'          => (int)
     *      optional - adjust the size of your result from the default value of 20
     *      to between 1 and 100 results.
     * 'start'          => (int)
     *      optional - adjust the range of your result set.
     *      Set this number to larger than zero and you will receive
     *      the portion of Technorati's total result set ranging from start to start+limit.
     *      The default start value is 1.
     * 'excerptsize'    => (int)
     *      optional - number of word characters to include in the post excerpts.
     *      By default 100 word characters are returned.
     * 'topexcerptsize' => (int)
     *      optional - number of word characters to include in the first post excerpt.
     *      By default 150 word characters are returned.
     *
     * @param   string $tag     the tag term you are searching posts for.
     * @param   array $options  additional parameters to refine your query
     * @return  TagResultSet
     * @throws  Exception\RuntimeException
     *  @link    http://technorati.com/developers/api/tag.html Technorati API: Tag Query reference
     */
    public function tag($tag, $options = null)
    {
        static $defaultOptions = array( 'start'          => 1,
                                        'limit'          => 20,
                                        'format'         => 'xml',
                                        'excerptsize'    => 100,
                                        'topexcerptsize' => 150);

        $options['tag'] = $tag;

        $options = $this->prepareOptions($options, $defaultOptions);
        $this->validateTag($options);
        $response = $this->makeRequest(self::API_PATH_TAG, $options);
        $dom = $this->convertResponseAndCheckContent($response);

        return new TagResultSet($dom, $options);
    }

    /**
     * TopTags provides daily counts of posts containing the queried keyword.
     *
     * Query options include:
     *
     * 'days'       => (int)
     *      optional - Used to specify the number of days in the past
     *      to request daily count data for.
     *      Can be any integer between 1 and 180, default is 180
     *
     * @param   string $q       the keyword query
     * @param   array $options  additional parameters to refine your query
     * @return  DailyCountsResultSet
     * @throws  Exception\RuntimeException
     * @link    http://technorati.com/developers/api/dailycounts.html Technorati API: DailyCounts Query reference
     */
    public function dailyCounts($query, $options = null)
    {
        static $defaultOptions = array( 'days'      => 180,
                                        'format'    => 'xml'
                                        );

        $options['q'] = $query;

        $options = $this->prepareOptions($options, $defaultOptions);
        $this->validateDailyCounts($options);
        $response = $this->makeRequest(self::API_PATH_DAILYCOUNTS, $options);
        $dom = $this->convertResponseAndCheckContent($response);

        return new DailyCountsResultSet($dom);
    }

    /**
     * TopTags provides information on top tags indexed by Technorati.
     *
     * Query options include:
     *
     * 'limit'      => (int)
     *      optional - adjust the size of your result from the default value of 20
     *      to between 1 and 100 results.
     * 'start'      => (int)
     *      optional - adjust the range of your result set.
     *      Set this number to larger than zero and you will receive
     *      the portion of Technorati's total result set ranging from start to start+limit.
     *      The default start value is 1.
     *
     * @param   array $options  additional parameters to refine your query
     * @return  TagsResultSet
     * @throws  Exception\RuntimeException
     * @link    http://technorati.com/developers/api/toptags.html Technorati API: TopTags Query reference
     */
    public function topTags($options = null)
    {
        static $defaultOptions = array( 'start'     => 1,
                                        'limit'     => 20,
                                        'format'    => 'xml'
                                        );

        $options = $this->prepareOptions($options, $defaultOptions);
        $this->validateTopTags($options);
        $response = $this->makeRequest(self::API_PATH_TOPTAGS, $options);
        $dom = $this->convertResponseAndCheckContent($response);

        return new TagsResultSet($dom);
    }

    /**
     * BlogInfo provides information on what blog, if any, is associated with a given URL.
     *
     * @param   string $url     the URL you are searching for. Prefixes http:// and www. are optional.
     *                          The URL must be recognized by Technorati as a blog.
     * @param   array $options  additional parameters to refine your query
     * @return  BlogInfoResult
     * @throws  Exception\RuntimeException
     * @link    http://technorati.com/developers/api/bloginfo.html Technorati API: BlogInfo Query reference
     */
    public function blogInfo($url, $options = null)
    {
        static $defaultOptions = array( 'format'    => 'xml'
                                        );

        $options['url'] = $url;

        $options = $this->prepareOptions($options, $defaultOptions);
        $this->validateBlogInfo($options);
        $response = $this->makeRequest(self::API_PATH_BLOGINFO, $options);
        $dom = $this->convertResponseAndCheckContent($response);

        return new BlogInfoResult($dom);
    }

    /**
     * BlogPostTags provides information on the top tags used by a specific blog.
     *
     * Query options include:
     *
     * 'limit'      => (int)
     *      optional - adjust the size of your result from the default value of 20
     *      to between 1 and 100 results.
     * 'start'      => (int)
     *      optional - adjust the range of your result set.
     *      Set this number to larger than zero and you will receive
     *      the portion of Technorati's total result set ranging from start to start+limit.
     *      The default start value is 1.
     *      Note. This property is not documented.
     *
     * @param   string $url     the URL you are searching for. Prefixes http:// and www. are optional.
     *                          The URL must be recognized by Technorati as a blog.
     * @param   array $options  additional parameters to refine your query
     * @return  TagsResultSet
     * @throws  Exception\RuntimeException
     * @link    http://technorati.com/developers/api/blogposttags.html Technorati API: BlogPostTags Query reference
     */
    public function blogPostTags($url, $options = null)
    {
        static $defaultOptions = array( 'start'     => 1,
                                        'limit'     => 20,
                                        'format'    => 'xml'
                                        );

        $options['url'] = $url;

        $options = $this->prepareOptions($options, $defaultOptions);
        $this->validateBlogPostTags($options);
        $response = $this->makeRequest(self::API_PATH_BLOGPOSTTAGS, $options);
        $dom = $this->convertResponseAndCheckContent($response);

        return new TagsResultSet($dom);
    }

    /**
     * GetInfo query tells you things that Technorati knows about a member.
     *
     * The returned info is broken up into two sections:
     * The first part describes some information that the user wants
     * to allow people to know about him- or herself.
     * The second part of the document is a listing of the weblogs
     * that the user has successfully claimed and the information
     * that Technorati knows about these weblogs.
     *
     * @param   string $username    the Technorati user name you are searching for
     * @param   array $options      additional parameters to refine your query
     * @return  GetInfoResult
     * @throws  Exception\RuntimeException
     * @link    http://technorati.com/developers/api/getinfo.html Technorati API: GetInfo reference
     */
    public function getInfo($username, $options = null)
    {
        static $defaultOptions = array('format' => 'xml');

        $options['username'] = $username;

        $options = $this->prepareOptions($options, $defaultOptions);
        $this->validateGetInfo($options);
        $response = $this->makeRequest(self::API_PATH_GETINFO, $options);
        $dom = $this->convertResponseAndCheckContent($response);

        return new GetInfoResult($dom);
    }

    /**
     * KeyInfo query provides information on daily usage of an API key.
     * Key Info Queries do not count against a key's daily query limit.
     *
     * A day is defined as 00:00-23:59 Pacific time.
     *
     * @return  KeyInfoResult
     * @throws  Exception\RuntimeException
     * @link    http://developers.technorati.com/wiki/KeyInfo Technorati API: Key Info reference
     */
    public function keyInfo()
    {
        static $defaultOptions = array();

        $options = $this->prepareOptions(array(), $defaultOptions);
        // you don't need to validate this request
        // because key is the only mandatory element
        // and it's already set in #_prepareOptions
        $response = $this->makeRequest(self::API_PATH_KEYINFO, $options);
        $dom = $this->convertResponseAndCheckContent($response);

        return new KeyInfoResult($dom, $this->apiKey);
    }


    /**
     * Returns Technorati API key.
     *
     * @return string   Technorati API key
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * Returns a reference to the REST client object in use.
     *
     * If the reference hasn't being inizialized yet,
     * then a new Client instance is created.
     *
     * @return RestClient
     */
    public function getRestClient()
    {
        if ($this->restClient === null) {
            $this->restClient = new RestClient(self::API_URI_BASE);
        }

        return $this->restClient;
    }

    /**
     * Sets Technorati API key.
     *
     * Be aware that this function doesn't validate the key.
     * The key is validated as soon as the first API request is sent.
     * If the key is invalid, the API request method will throw
     * a Exception exception with Invalid Key message.
     *
     * @param   string $key     Technorati API Key
     * @return  void
     * @link    http://technorati.com/developers/apikey.html How to get your Technorati API Key
     */
    public function setApiKey($key)
    {
        $this->apiKey = $key;
        return $this;
    }


    /**
     * Validates Cosmos query options.
     *
     * @param   array $options
     * @return  void
     * @throws  Exception\RuntimeException
     * @access  protected
     */
    protected function validateCosmos(array $options)
    {
        static $validOptions = array('key', 'url',
            'type', 'limit', 'start', 'current', 'claim', 'highlight', 'format');

        // Validate keys in the $options array
        $this->compareOptions($options, $validOptions);
        // Validate url (required)
        $this->validateOptionUrl($options);
        // Validate limit (optional)
        $this->validateOptionLimit($options);
        // Validate start (optional)
        $this->validateOptionStart($options);
        // Validate format (optional)
        $this->validateOptionFormat($options);
        // Validate type (optional)
        $this->validateInArrayOption('type', $options, array('link', 'weblog'));
        // Validate claim (optional)
        $this->validateOptionClaim($options);
        // Validate highlight (optional)
        $this->validateIntegerOption('highlight', $options);
        // Validate current (optional)
        if (isset($options['current'])) {
            $tmp = (int) $options['current'];
            $options['current'] = $tmp ? 'yes' : 'no';
        }

    }

    /**
     * Validates Search query options.
     *
     * @param   array   $options
     * @return  void
     * @throws  Exception\RuntimeException
     * @access  protected
     */
    protected function validateSearch(array $options)
    {
        static $validOptions = array('key', 'query',
            'language', 'authority', 'limit', 'start', 'claim', 'format');

        // Validate keys in the $options array
        $this->compareOptions($options, $validOptions);
        // Validate query (required)
        $this->validateMandatoryOption('query', $options);
        // Validate authority (optional)
        $this->validateInArrayOption('authority', $options, array('n', 'a1', 'a4', 'a7'));
        // Validate limit (optional)
        $this->validateOptionLimit($options);
        // Validate start (optional)
        $this->validateOptionStart($options);
        // Validate claim (optional)
        $this->validateOptionClaim($options);
        // Validate format (optional)
        $this->validateOptionFormat($options);
    }

    /**
     * Validates Tag query options.
     *
     * @param   array   $options
     * @return  void
     * @throws  Exception\RuntimeException
     * @access  protected
     */
    protected function validateTag(array $options)
    {
        static $validOptions = array('key', 'tag',
            'limit', 'start', 'excerptsize', 'topexcerptsize', 'format');

        // Validate keys in the $options array
        $this->compareOptions($options, $validOptions);
        // Validate query (required)
        $this->validateMandatoryOption('tag', $options);
        // Validate limit (optional)
        $this->validateOptionLimit($options);
        // Validate start (optional)
        $this->validateOptionStart($options);
        // Validate excerptsize (optional)
        $this->validateIntegerOption('excerptsize', $options);
        // Validate excerptsize (optional)
        $this->validateIntegerOption('topexcerptsize', $options);
        // Validate format (optional)
        $this->validateOptionFormat($options);
    }


    /**
     * Validates DailyCounts query options.
     *
     * @param   array   $options
     * @return  void
     * @throws  Exception\RuntimeException
     * @access  protected
     */
    protected function validateDailyCounts(array $options)
    {
        static $validOptions = array('key', 'q',
            'days', 'format');

        // Validate keys in the $options array
        $this->compareOptions($options, $validOptions);
        // Validate q (required)
        $this->validateMandatoryOption('q', $options);
        // Validate format (optional)
        $this->validateOptionFormat($options);
        // Validate days (optional)
        if (isset($options['days'])) {
            $options['days'] = (int) $options['days'];
            if ($options['days'] < self::PARAM_DAYS_MIN_VALUE ||
                $options['days'] > self::PARAM_DAYS_MAX_VALUE) {
                throw new Exception\RuntimeException(
                            "Invalid value '" . $options['days'] . "' for 'days' option");
            }
        }
    }

    /**
     * Validates GetInfo query options.
     *
     * @param   array   $options
     * @return  void
     * @throws  Exception\RuntimeException
     * @access  protected
     */
    protected function validateGetInfo(array $options)
    {
        static $validOptions = array('key', 'username',
            'format');

        // Validate keys in the $options array
        $this->compareOptions($options, $validOptions);
        // Validate username (required)
        $this->validateMandatoryOption('username', $options);
        // Validate format (optional)
        $this->validateOptionFormat($options);
    }

    /**
     * Validates TopTags query options.
     *
     * @param   array $options
     * @return  void
     * @throws  Exception\RuntimeException
     * @access  protected
     */
    protected function validateTopTags(array $options)
    {
        static $validOptions = array('key',
            'limit', 'start', 'format');

        // Validate keys in the $options array
        $this->compareOptions($options, $validOptions);
        // Validate limit (optional)
        $this->validateOptionLimit($options);
        // Validate start (optional)
        $this->validateOptionStart($options);
        // Validate format (optional)
        $this->validateOptionFormat($options);
    }

    /**
     * Validates BlogInfo query options.
     *
     * @param   array   $options
     * @return  void
     * @throws  Exception\RuntimeException
     * @access  protected
     */
    protected function validateBlogInfo(array $options)
    {
        static $validOptions = array('key', 'url',
            'format');

        // Validate keys in the $options array
        $this->compareOptions($options, $validOptions);
        // Validate url (required)
        $this->validateOptionUrl($options);
        // Validate format (optional)
        $this->validateOptionFormat($options);
    }

    /**
     * Validates TopTags query options.
     *
     * @param   array $options
     * @return  void
     * @throws  Exception\RuntimeException
     * @access  protected
     */
    protected function validateBlogPostTags(array $options)
    {
        static $validOptions = array('key', 'url',
            'limit', 'start', 'format');

        // Validate keys in the $options array
        $this->compareOptions($options, $validOptions);
        // Validate url (required)
        $this->validateOptionUrl($options);
        // Validate limit (optional)
        $this->validateOptionLimit($options);
        // Validate start (optional)
        $this->validateOptionStart($options);
        // Validate format (optional)
        $this->validateOptionFormat($options);
    }

    /**
     * Checks whether an option is in a given array.
     *
     * @param   string $name    option name
     * @param   array $options
     * @param   array $array    array of valid options
     * @return  void
     * @throws  Exception\RuntimeException
     * @access  protected
     */
    protected function validateInArrayOption($name, $options, array $array)
    {
        if (isset($options[$name]) && !in_array($options[$name], $array)) {
            throw new Exception\RuntimeException(
                        "Invalid value '{$options[$name]}' for '$name' option");
        }
    }

    /**
     * Checks whether mandatory $name option exists and it's valid.
     *
     * @param   array $options
     * @return  void
     * @throws  Exception\RuntimeException
     * @access  protected
     */
    protected function validateMandatoryOption($name, $options)
    {
        if (!isset($options[$name]) || !trim($options[$name])) {
            throw new Exception\RuntimeException(
                        "Empty value for '$name' option");
        }
    }

    /**
     * Checks whether $name option is a valid integer and casts it.
     *
     * @param   array $options
     * @return  void
     * @access  protected
     */
    protected function validateIntegerOption($name, $options)
    {
        if (isset($options[$name])) {
            $options[$name] = (int) $options[$name];
        }
    }

    /**
     * Makes and HTTP GET request to given $path with $options.
     * HTTP Response is first validated, then returned.
     *
     * @param   string $path
     * @param   array $options
     * @return  Response
     * @throws  Exception\RuntimeException on failure
     * @access  protected
     */
    protected function makeRequest($path, $options = array())
    {
        $restClient = $this->getRestClient();
        $restClient->getHttpClient()->resetParameters();
        $response = $restClient->restGet($path, $options);
        self::checkResponse($response);
        return $response;
    }

    /**
     * Checks whether 'claim' option value is valid.
     *
     * @param   array $options
     * @return  void
     * @access  protected
     */
    protected function validateOptionClaim(array $options)
    {
        $this->validateIntegerOption('claim', $options);
    }

    /**
     * Checks whether 'format' option value is valid.
     * Be aware that Zend\Service\Technorati supports only XML as format value.
     *
     * @param   array $options
     * @return  void
     * @throws  Exception\RuntimeException if 'format' value != XML
     * @access  protected
     */
    protected function validateOptionFormat(array $options)
    {
        if (isset($options['format']) && $options['format'] != 'xml') {
            throw new Exception\RuntimeException(
                        "Invalid value '" . $options['format'] . "' for 'format' option. " .
                        "Zend\Service\Technorati supports only 'xml'");
        }
    }

    /**
     * Checks whether 'limit' option value is valid.
     * Value must be an integer greater than PARAM_LIMIT_MIN_VALUE
     * and lower than PARAM_LIMIT_MAX_VALUE.
     *
     * @param   array $options
     * @return  void
     * @throws  Exception\RuntimeException if 'limit' value is invalid
     * @access  protected
     */
    protected function validateOptionLimit(array $options)
    {
        if (!isset($options['limit'])) return;

        $options['limit'] = (int) $options['limit'];
        if ($options['limit'] < self::PARAM_LIMIT_MIN_VALUE ||
            $options['limit'] > self::PARAM_LIMIT_MAX_VALUE) {
            throw new Exception\RuntimeException(
                        "Invalid value '" . $options['limit'] . "' for 'limit' option");
        }
    }

    /**
     * Checks whether 'start' option value is valid.
     * Value must be an integer greater than 0.
     *
     * @param   array $options
     * @return  void
     * @throws  Exception\RuntimeException if 'start' value is invalid
     * @access  protected
     */
    protected function validateOptionStart(array $options)
    {
        if (!isset($options['start'])) return;

        $options['start'] = (int) $options['start'];
        if ($options['start'] < self::PARAM_START_MIN_VALUE) {
            throw new Exception\RuntimeException(
                        "Invalid value '" . $options['start'] . "' for 'start' option");
        }
    }

    /**
     * Checks whether 'url' option value exists and is valid.
     * 'url' must be a valid HTTP(s) URL.
     *
     * @param   array $options
     * @return  void
     * @throws  Exception\RuntimeException if 'url' value is invalid
     * @access  protected
     * @todo    support for Zend\Uri\Http
     */
    protected function validateOptionUrl(array $options)
    {
        $this->validateMandatoryOption('url', $options);
    }

    /**
     * Checks XML response content for errors.
     *
     * @param   DomDocument $dom    the XML response as a DOM document
     * @return  void
     * @throws  Exception\RuntimeException
     * @link    http://technorati.com/developers/api/error.html Technorati API: Error response
     * @access  protected
     */
    protected static function checkErrors(DomDocument $dom)
    {
        $xpath = new \DOMXPath($dom);

        $result = $xpath->query("/tapi/document/result/error");
        if ($result->length >= 1) {
            $error = $result->item(0)->nodeValue;
            throw new Exception\RuntimeException($error);
        }
    }

    /**
     * Converts $response body to a DOM object and checks it.
     *
     * @param   Response $response
     * @return  DOMDocument
     * @throws  Exception\RuntimeException if response content contains an error message
     * @access  protected
     */
    protected function convertResponseAndCheckContent(Response $response)
    {
        $dom = new \DOMDocument();
        $dom->loadXML($response->getBody());
        self::checkErrors($dom);
        return $dom;
    }

    /**
     * Checks ReST response for errors.
     *
     * @param   Response $response    the ReST response
     * @return  void
     * @throws  Exception\RuntimeException
     * @access  protected
     */
    protected static function checkResponse(Response $response)
    {
        if ($response->isServerError() || $response->isClientError()) {
            throw new Exception\RuntimeException(sprintf(
                        'Invalid response status code (HTTP/%s %s %s)',
                        $response->getVersion(), $response->getStatus(), $response->getMessage()));
        }
    }

    /**
     * Checks whether user given options are valid.
     *
     * @param   array $options        user options
     * @param   array $validOptions   valid options
     * @return  void
     * @throws  Exception\RuntimeException
     * @access  protected
     */
    protected function compareOptions(array $options, array $validOptions)
    {
        $difference = array_diff(array_keys($options), $validOptions);
        if ($difference) {
            throw new Exception\RuntimeException(
                        "The following parameters are invalid: '" .
                        implode("', '", $difference) . "'");
        }
    }

    /**
     * Prepares options for the request
     *
     * @param   array $options        user options
     * @param   array $defaultOptions default options
     * @return  array Merged array of user and default/required options.
     * @access  protected
     */
    protected function prepareOptions($options, array $defaultOptions)
    {
        $options = (array) $options; // force cast to convert null to array()
        $options['key'] = $this->apiKey;
        $options = array_merge($defaultOptions, $options);
        return $options;
    }
}
