<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
 */

namespace Zend\Service\Flickr;

use DOMDocument;
use DOMXPath;
use Zend\I18n\Validator\Int as IntValidator;
use Zend\Http\Client as HttpClient;
use Zend\Http\Request as HttpRequest;
use Zend\Validator\Between as BetweenValidator;

/**
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Flickr
 */
class Flickr
{
    /**
     * Base URI for the REST client
     */
    const URI_BASE = 'http://www.flickr.com';

    /**
     * Your Flickr API key
     *
     * @var string
     */
    public $apiKey;

    /**
     * @var HttpClient
     */
    protected $httpClient = null;

    /**
     * Performs object initializations
     *
     *  # Sets up character encoding
     *  # Saves the API key
     *
     * @param  string $apiKey Your Flickr API key
     */
    public function __construct($apiKey, HttpClient $httpClient = null)
    {
        $this->apiKey = (string) $apiKey;
        $this->setHttpClient($httpClient ?: new HttpClient);
    }

    /**
     * @param HttpClient $httpClient
     * @return Flickr
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
     * Find Flickr photos by tag.
     *
     * Query options include:
     *
     *  # per_page:        how many results to return per query
     *  # page:            the starting page offset.  first result will be (page - 1) * per_page + 1
     *  # tag_mode:        Either 'any' for an OR combination of tags,
     *                     or 'all' for an AND combination. Default is 'any'.
     *  # min_upload_date: Minimum upload date to search on.  Date should be a unix timestamp.
     *  # max_upload_date: Maximum upload date to search on.  Date should be a unix timestamp.
     *  # min_taken_date:  Minimum upload date to search on.  Date should be a MySQL datetime.
     *  # max_taken_date:  Maximum upload date to search on.  Date should be a MySQL datetime.
     *
     * @param  string|array $query   A single tag or an array of tags.
     * @param  array        $options Additional parameters to refine your query.
     * @return ResultSet
     * @throws Exception\RuntimeException
     */
    public function tagSearch($query, array $options = array())
    {
        static $method = 'flickr.photos.search';
        static $defaultOptions = array('per_page' => 10,
                                       'page'     => 1,
                                       'tag_mode' => 'or',
                                       'extras'   => 'license, date_upload, date_taken, owner_name, icon_server');

        $options['tags'] = is_array($query) ? implode(',', $query) : $query;

        $options = $this->prepareOptions($method, $options, $defaultOptions);

        $this->validateTagSearch($options);

        // now search for photos
        $request = new HttpRequest;
        $request->setUri('/services/rest/');
        $request->getQuery()->fromArray($options);
        $response = $this->httpClient->send($request);

        if ($response->isServerError() || $response->isClientError()) {
            throw new Exception\RuntimeException('An error occurred sending request. Status code: '
                                                 . $response->getStatusCode());
        }

        $dom = new DOMDocument();
        $dom->loadXML($response->getBody());

        self::checkErrors($dom);

        return new ResultSet($dom, $this);
    }


    /**
     * Finds photos by a user's username or email.
     *
     * Additional query options include:
     *
     *  # per_page:        how many results to return per query
     *  # page:            the starting page offset.  first result will be (page - 1) * per_page + 1
     *  # min_upload_date: Minimum upload date to search on.  Date should be a unix timestamp.
     *  # max_upload_date: Maximum upload date to search on.  Date should be a unix timestamp.
     *  # min_taken_date:  Minimum upload date to search on.  Date should be a MySQL datetime.
     *  # max_taken_date:  Maximum upload date to search on.  Date should be a MySQL datetime.
     *
     * @param  string $query   username or email
     * @param  array  $options Additional parameters to refine your query.
     * @return ResultSet
     * @throws Exception\RuntimeException
     */
    public function userSearch($query, array $options = null)
    {
        static $method = 'flickr.people.getPublicPhotos';
        static $defaultOptions = array('per_page' => 10,
                                       'page'     => 1,
                                       'extras'   => 'license, date_upload, date_taken, owner_name, icon_server');


        // can't access by username, must get ID first
        if (strchr($query, '@')) {
            // optimistically hope this is an email
            $options['user_id'] = $this->getIdByEmail($query);
        } else {
            // we can safely ignore this exception here
            $options['user_id'] = $this->getIdByUsername($query);
        }

        $options = $this->prepareOptions($method, $options, $defaultOptions);
        $this->validateUserSearch($options);

        // now search for photos
        $request = new HttpRequest;
        $request->setUri('/services/rest/');
        $request->getQuery()->fromArray($options);
        $response = $this->httpClient->send($request);

        if ($response->isServerError() || $response->isClientError()) {
            throw new Exception\RuntimeException('An error occurred sending request. Status code: '
                                                 . $response->getStatusCode());
        }

        $dom = new DOMDocument();
        $dom->loadXML($response->getBody());

        self::checkErrors($dom);

        return new ResultSet($dom, $this);
    }

    /**
     * Finds photos in a group's pool.
     *
     * @param  string $query   group id
     * @param  array  $options Additional parameters to refine your query.
     * @return ResultSet
     * @throws Exception\InvalidArgumentException
     * @throws Exception\RuntimeException
     */
    public function groupPoolGetPhotos($query, array $options = array())
    {
        static $method = 'flickr.groups.pools.getPhotos';
        static $defaultOptions = array('per_page' => 10,
                                       'page'     => 1,
                                       'extras'   => 'license, date_upload, date_taken, owner_name, icon_server');

        if (empty($query) || !is_string($query)) {
            throw new Exception\InvalidArgumentException('You must supply a group id');
        }

        $options['group_id'] = $query;

        $options = $this->prepareOptions($method, $options, $defaultOptions);

        $this->validateGroupPoolGetPhotos($options);

        // now search for photos
        $request = new HttpRequest;
        $request->setUri('/services/rest/');
        $request->getQuery()->fromArray($options);
        $response = $this->httpClient->send($request);

        if ($response->isServerError() || $response->isClientError()) {
            throw new Exception\RuntimeException('An error occurred sending request. Status code: '
                                                 . $response->getStatusCode());
        }

        $dom = new DOMDocument();
        $dom->loadXML($response->getBody());

        self::checkErrors($dom);

        return new ResultSet($dom, $this);
    }


    /**
     * Utility function to find Flickr User IDs for usernames.
     *
     * (You can only find a user's photo with their NSID.)
     *
     * @param  string $username the username
     * @return string the NSID (userid)
     * @throws Exception\InvalidArgumentException
     * @throws Exception\RuntimeException
     */
    public function getIdByUsername($username)
    {
        static $method = 'flickr.people.findByUsername';

        $options = array('api_key' => $this->apiKey, 'method' => $method, 'username' => (string)$username);

        if (empty($username)) {
            throw new Exception\InvalidArgumentException('You must supply a username');
        }

        $request = new HttpRequest;
        $request->setUri('/services/rest/');
        $request->getQuery()->fromArray($options);
        $response = $this->httpClient->send($request);

        if ($response->isServerError() || $response->isClientError()) {
            throw new Exception\RuntimeException('An error occurred sending request. Status code: '
                                                 . $response->getStatusCode());
        }

        $dom = new DOMDocument();
        $dom->loadXML($response->getBody());
        self::checkErrors($dom);
        $xpath = new DOMXPath($dom);
        return (string)$xpath->query('//user')->item(0)->getAttribute('id');
    }


    /**
     * Utility function to find Flickr User IDs for emails.
     *
     * (You can only find a user's photo with their NSID.)
     *
     * @param  string $email the email
     * @return string the NSID (userid)
     * @throws Exception\InvalidArgumentException
     * @throws Exception\RuntimeException
     */
    public function getIdByEmail($email)
    {
        static $method = 'flickr.people.findByEmail';

        if (empty($email)) {
            throw new Exception\InvalidArgumentException('You must supply an e-mail address');
        }

        $options = array('api_key' => $this->apiKey, 'method' => $method, 'find_email' => (string)$email);

        $request = new HttpRequest;
        $request->setUri('/services/rest/');
        $request->getQuery()->fromArray($options);
        $response = $this->httpClient->send($request);

        if ($response->isServerError() || $response->isClientError()) {
            throw new Exception\RuntimeException('An error occurred sending request. Status code: '
                                                 . $response->getStatusCode());
        }

        $dom = new DOMDocument();
        $dom->loadXML($response->getBody());
        self::checkErrors($dom);
        $xpath = new DOMXPath($dom);
        return (string)$xpath->query('//user')->item(0)->getAttribute('id');
    }


    /**
     * Returns Flickr photo details by for the given photo ID
     *
     * @param  string $id the NSID
     * @return array of Image, details for the specified image
     * @throws Exception\InvalidArgumentException
     */
    public function getImageDetails($id)
    {
        static $method = 'flickr.photos.getSizes';

        if (empty($id)) {
            throw new Exception\InvalidArgumentException('You must supply a photo ID');
        }

        $options = array('api_key' => $this->apiKey, 'method' => $method, 'photo_id' => $id);

        $request = new HttpRequest;
        $request->setUri('/services/rest/');
        $request->getQuery()->fromArray($options);
        $response = $this->httpClient->send($request);

        $dom = new DOMDocument();
        $dom->loadXML($response->getBody());
        $xpath = new DOMXPath($dom);
        self::checkErrors($dom);
        $retval = array();
        foreach ($xpath->query('//size') as $size) {
            $label          = (string)$size->getAttribute('label');
            $retval[$label] = new Image($size);
        }

        return $retval;
    }

    /**
     * Validate User Search Options
     *
     * @param  array $options
     * @return void
     * @throws Exception\DomainException
     */
    protected function validateUserSearch(array $options)
    {
        $validOptions = array('api_key', 'method', 'user_id', 'per_page', 'page', 'extras', 'min_upload_date',
                              'min_taken_date', 'max_upload_date', 'max_taken_date', 'safe_search');

        $this->compareOptions($options, $validOptions);

        if ($options['per_page'] < 1 || $options['per_page'] > 500) {
            throw new Exception\DomainException($options['per_page'] . ' is not valid for the "per_page" option');
        }

        if (!is_int($options['page'])) {
            throw new Exception\DomainException($options['page'] . ' is not valid for the "page" option');
        }

        // validate extras, which are delivered in csv format
        if (isset($options['extras'])) {
            $extras      = explode(',', $options['extras']);
            $validExtras = array('license', 'date_upload', 'date_taken', 'owner_name', 'icon_server');
            foreach ($extras as $extra) {
                /**
                 * @todo The following does not do anything [yet], so it is commented out.
                 */
                //in_array(trim($extra), $validExtras);
            }
        }
    }


    /**
     * Validate Tag Search Options
     *
     * @param  array $options
     * @return void
     * @throws Exception\DomainException
     */
    protected function validateTagSearch(array $options)
    {
        $validOptions = array('method', 'api_key', 'user_id', 'tags', 'tag_mode', 'text', 'min_upload_date',
                              'max_upload_date', 'min_taken_date', 'max_taken_date', 'license', 'sort',
                              'privacy_filter', 'bbox', 'accuracy', 'safe_search', 'content_type', 'machine_tags',
                              'machine_tag_mode', 'group_id', 'contacts', 'woe_id', 'place_id', 'media', 'has_geo',
                              'geo_context', 'lat', 'lon', 'radius', 'radius_units', 'is_commons', 'is_gallery',
                              'extras', 'per_page', 'page');

        $this->compareOptions($options, $validOptions);

        if ($options['per_page'] < 1 || $options['per_page'] > 500) {
            throw new Exception\DomainException($options['per_page'] . ' is not valid for the "per_page" option');
        }

        if (!is_int($options['page'])) {
            throw new Exception\DomainException($options['page'] . ' is not valid for the "page" option');
        }

        // validate extras, which are delivered in csv format
        if (isset($options['extras'])) {
            $extras      = explode(',', $options['extras']);
            $validExtras = array('license', 'date_upload', 'date_taken', 'owner_name', 'icon_server');
            foreach ($extras as $extra) {
                /**
                 * @todo The following does not do anything [yet], so it is commented out.
                 */
                //in_array(trim($extra), $validExtras);
            }
        }

    }


    /**
     * Validate Group Search Options
     *
     * @param  array $options
     * @throws Exception\DomainException
     * @return void
     */
    protected function validateGroupPoolGetPhotos(array $options)
    {
        $validOptions = array('api_key', 'tags', 'method', 'group_id', 'per_page', 'page', 'extras', 'user_id');

        $this->compareOptions($options, $validOptions);

        if ($options['per_page'] < 1 || $options['per_page'] > 500) {
            throw new Exception\DomainException($options['per_page'] . ' is not valid for the "per_page" option');
        }

        if (!is_int($options['page'])) {
            throw new Exception\DomainException($options['page'] . ' is not valid for the "page" option');
        }

        // validate extras, which are delivered in csv format
        if (isset($options['extras'])) {
            $extras      = explode(',', $options['extras']);
            $validExtras = array('license', 'date_upload', 'date_taken', 'owner_name', 'icon_server');
            foreach ($extras as $extra) {
                /**
                 * @todo The following does not do anything [yet], so it is commented out.
                 */
                //in_array(trim($extra), $validExtras);
            }
        }
    }


    /**
     * Throws an exception if and only if the response status indicates a failure
     *
     * @param  DOMDocument $dom
     * @return void
     * @throws Exception\RuntimeException
     */
    protected static function checkErrors(DOMDocument $dom)
    {
        if ($dom->documentElement->getAttribute('stat') === 'fail') {
            $xpath = new DOMXPath($dom);
            $err   = $xpath->query('//err')->item(0);
            throw new Exception\RuntimeException('Search failed due to error: ' . $err->getAttribute('msg')
                                                 . ' (error #' . $err->getAttribute('code') . ')');
        }
    }


    /**
     * Prepare options for the request
     *
     * @param  string $method         Flickr Method to call
     * @param  array  $options        User Options
     * @param  array  $defaultOptions Default Options
     * @return array Merged array of user and default/required options
     */
    protected function prepareOptions($method, array $options, array $defaultOptions)
    {
        $options['method']  = (string)$method;
        $options['api_key'] = $this->apiKey;

        return array_merge($defaultOptions, $options);
    }


    /**
     * Throws an exception if and only if any user options are invalid
     *
     * @param  array $options      User options
     * @param  array $validOptions Valid options
     * @return void
     * @throws Exception\InvalidArgumentException
     */
    protected function compareOptions(array $options, array $validOptions)
    {
        $difference = array_diff(array_keys($options), $validOptions);
        if ($difference) {
            throw new Exception\InvalidArgumentException(
                'The following parameters are invalid: ' . implode(',', $difference)
            );
        }
    }
}
