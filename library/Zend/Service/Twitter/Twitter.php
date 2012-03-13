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
 * @subpackage Twitter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Service\Twitter;

use Zend\Http,
    Zend\OAuth,
    Zend\Rest,
    Zend\Uri,
    Zend\Config,
    Zend\Rest\Client;

/**
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Twitter
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Twitter extends Client\RestClient
{

    /**
     * 246 is the current limit for a status message, 140 characters are displayed
     * initially, with the remainder linked from the web UI or client. The limit is
     * applied to a html encoded UTF-8 string (i.e. entities are counted in the limit
     * which may appear unusual but is a security measure).
     *
     * This should be reviewed in the future...
     */
    const STATUS_MAX_CHARACTERS = 246;

    /**
     * OAuth Endpoint
     */
    const OAUTH_BASE_URI = 'http://twitter.com/oauth';

    /**
     * @var array
     */
    protected $cookieJar;

    /**
     * Date format for 'since' strings
     *
     * @var string
     */
    protected $dateFormat = 'D, d M Y H:i:s T';

    /**
     * Username
     *
     * @var string
     */
    protected $username;

    /**
     * Current method type (for method proxying)
     *
     * @var string
     */
    protected $methodType;

    /**
     * Zend\Oauth Consumer
     *
     * @var \Zend\OAuth\Consumer
     */
    protected $oauthConsumer = null;

    /**
     * Types of API methods
     *
     * @var array
     */
    protected $methodTypes = array(
        'status',
        'user',
        'directMessage',
        'friendship',
        'account',
        'favorite',
        'block',
    );

    /**
     * Options passed to constructor
     *
     * @var array
     */
    protected $options = array();

    /**
     * Local HTTP Client cloned from statically set client
     *
     * @var \Zend\Http\Client
     */
    protected $localHttpClient = null;

    /**
     * Constructor
     *
     * @param  array $options Optional options array
     * @return void
     */
    public function __construct($options = null, Oauth\Consumer $consumer = null)
    {
        $this->setUri('http://api.twitter.com');
        if (!is_array($options)) $options = array();
        $options['siteUrl'] = self::OAUTH_BASE_URI;
        if ($options instanceof Config\Config) {
            $options = $options->toArray();
        }
        $this->options = $options;
        if (isset($options['username'])) {
            $this->setUsername($options['username']);
        }
        if (isset($options['accessToken']) &&
            $options['accessToken'] instanceof OAuth\Token\Access) {
            $this->setLocalHttpClient($options['accessToken']->getHttpClient($options));
        } else {
            $this->setLocalHttpClient(clone self::getHttpClient());
            if ($consumer === null) {
                $this->oauthConsumer = new OAuth\Consumer($options);
            } else {
                $this->oauthConsumer = $consumer;
            }
        }
    }

    /**
     * Set local HTTP client as distinct from the static HTTP client
     * as inherited from Zend_Rest_Client.
     *
     * @param Zend\Http\Client $client
     * @return self
     */
    public function setLocalHttpClient(Http\Client $client)
    {
        $this->localHttpClient = $client;
        $this->localHttpClient->setHeaders(array('Accept-Charset' => 'ISO-8859-1,utf-8'));
        return $this;
    }

    /**
     * Get the local HTTP client as distinct from the static HTTP client
     * inherited from \Zend\Rest\Client
     *
     * @return \Zend\Http\Client
     */
    public function getLocalHttpClient()
    {
        return $this->localHttpClient;
    }

    /**
     * Checks for an authorised state
     *
     * @return bool
     */
    public function isAuthorised()
    {
        if ($this->getLocalHttpClient() instanceof OAuth\Client) {
            return true;
        }
        return false;
    }

    /**
     * Retrieve username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set username
     *
     * @param  string $value
     * @return Twitter
     */
    public function setUsername($value)
    {
        $this->username = $value;
        return $this;
    }

    /**
     * Proxy service methods
     *
     * @param  string $type
     * @return Twitter
     * @throws Exception\DomainException If method not in method types list
     */
    public function __get($type)
    {
        if (!in_array($type, $this->methodTypes)) {
            throw new Exception\DomainException(
                'Invalid method type "' . $type . '"'
            );
        }
        $this->methodType = $type;
        return $this;
    }

    /**
     * Method overloading
     *
     * @param  string $method
     * @param  array $params
     * @return mixed
     * @throws Exception\BadMethodCallException if unable to find method
     */
    public function __call($method, $params)
    {
        if (method_exists($this->oauthConsumer, $method)) {
            $return = call_user_func_array(array($this->oauthConsumer, $method), $params);
            if ($return instanceof OAuth\Token\Access) {
                $this->setLocalHttpClient($return->getHttpClient($this->options));
            }
            return $return;
        }
        if (empty($this->methodType)) {
            throw new Exception\BadMethodCallException(
                'Invalid method "' . $method . '"'
            );
        }
        $test = $this->methodType . ucfirst($method);
        if (!method_exists($this, $test)) {
            throw new Exception\BadMethodCallException(
                'Invalid method "' . $test . '"'
            );
        }

        return call_user_func_array(array($this, $test), $params);
    }

    /**
     * Initialize HTTP authentication
     *
     * @return void
     * @throws exception\DomainException
     */
    protected function init()
    {
        if (!$this->isAuthorised() && $this->getUsername() !== null) {
            throw new Exception\DomainException(
                'Twitter session is unauthorised. You need to initialize '
                . __CLASS__ . ' with an OAuth Access Token or use '
                . 'its OAuth functionality to obtain an Access Token before '
                . 'attempting any API actions that require authorisation'
            );
        }
        $client = $this->localHttpClient;
        $client->resetParameters();
        if (null == $this->cookieJar) {
            $client->clearCookies();
            $this->cookieJar = $client->getCookies();
        } else {
            $client->setCookies($this->cookieJar);
        }
    }

    /**
     * Set date header
     *
     * @param  int|string $value
     * @deprecated Not supported by Twitter since April 08, 2009
     * @return void
     */
    protected function setDate($value)
    {
        if (is_int($value)) {
            $date = date($this->dateFormat, $value);
        } else {
            $date = date($this->dateFormat, strtotime($value));
        }
        $this->localHttpClient->setHeaders(array('If-Modified-Since' => $date));
    }

    /**
     * Public Timeline status
     *
     * @throws Http\Client\Exception if HTTP request fails or times out
     * @return Client\Result
     */
    public function statusPublicTimeline()
    {
        $this->init();
        $path = '/1/statuses/public_timeline.xml';
        $response = $this->get($path);
        return new Client\Result($response->getBody());
    }

    /**
     * Friend Timeline Status
     *
     * $params may include one or more of the following keys
     * - id: ID of a friend whose timeline you wish to receive
     * - count: how many statuses to return
     * - since_id: return results only after the specific tweet
     * - page: return page X of results
     *
     * @param  array $params
     * @throws Http\Client\Exception if HTTP request fails or times out
     * @return Client\Result
     */
    public function statusFriendsTimeline(array $params = array())
    {
        $this->init();
        $path = '/1/statuses/friends_timeline';
        $_params = array();
        foreach ($params as $key => $value) {
            switch (strtolower($key)) {
                case 'count':
                    $count = (int) $value;
                    if (0 >= $count) {
                        $count = 1;
                    } elseif (200 < $count) {
                        $count = 200;
                    }
                    $_params['count'] = (int) $count;
                    break;
                case 'since_id':
                    $_params['since_id'] = $this->validInteger($value);
                    break;
                case 'page':
                    $_params['page'] = (int) $value;
                    break;
                default:
                    break;
            }
        }
        $path .= '.xml';
        $response = $this->get($path, $_params);
        $return = new Client\Result($response->getBody());
        return $return;
    }

    /**
     * User Timeline status
     *
     * $params may include one or more of the following keys
     * - id: ID of a friend whose timeline you wish to receive
     * - since_id: return results only after the tweet id specified
     * - page: return page X of results
     * - count: how many statuses to return
     * - max_id: returns only statuses with an ID less than or equal to the specified ID
     * - user_id: specfies the ID of the user for whom to return the user_timeline
     * - screen_name: specfies the screen name of the user for whom to return the user_timeline
     *
     * @throws Http\Client\Exception if HTTP request fails or times out
     * @return Client\Result
     */
    public function statusUserTimeline(array $params = array())
    {
        $this->init();
        $path = '/1/statuses/user_timeline';
        $_params = array();
        foreach ($params as $key => $value) {
            switch (strtolower($key)) {
                case 'id':
                    $path .= '/' . $value;
                    break;
                case 'page':
                    $_params['page'] = (int) $value;
                    break;
                case 'count':
                    $count = (int) $value;
                    if (0 >= $count) {
                        $count = 1;
                    } elseif (200 < $count) {
                        $count = 200;
                    }
                    $_params['count'] = $count;
                    break;
                case 'user_id':
                    $_params['user_id'] = $this->validInteger($value);
                    break;
                case 'screen_name':
                    $_params['screen_name'] = $this->validateScreenName($value);
                    break;
                case 'since_id':
                    $_params['since_id'] = $this->validInteger($value);
                    break;
                case 'max_id':
                    $_params['max_id'] = $this->validInteger($value);
                    break;
                default:
                    break;
            }
        }
        $path .= '.xml';
        $response = $this->get($path, $_params);
        return new Client\Result($response->getBody());
    }

    /**
     * Show a single status
     *
     * @param  int $id Id of status to show
     * @throws Http\Client\Exception if HTTP request fails or times out
     * @return Client\Result
     */
    public function statusShow($id)
    {
        $this->init();
        $path = '/1/statuses/show/' . $this->validInteger($id) . '.xml';
        $response = $this->get($path);
        return new Client\Result($response->getBody());
    }

    /**
     * Update user's current status
     *
     * @param  string $status
     * @param  int $in_reply_to_status_id
     * @return Client\Result
     * @throws Http\Client\Exception if HTTP request fails or times out
     * @throws Exception\OutOfRangeException if message is too long
     * @throws Exception\InvalidArgumentException if message is empty
     */
    public function statusUpdate($status, $inReplyToStatusId = null)
    {
        $this->init();
        $path = '/1/statuses/update.xml';
        $len = iconv_strlen(htmlspecialchars($status, ENT_QUOTES, 'UTF-8'), 'UTF-8');
        if ($len > self::STATUS_MAX_CHARACTERS) {
            throw new Exception\OutOfRangeException(
                'Status must be no more than '
                . self::STATUS_MAX_CHARACTERS
                . ' characters in length'
            );
        } elseif (0 == $len) {
            throw new Exception\InvalidArgumentException(
                'Status must contain at least one character'
            );
        }
        $data = array('status' => $status);
        if (is_numeric($inReplyToStatusId) && !empty($inReplyToStatusId)) {
            $data['in_reply_to_status_id'] = $inReplyToStatusId;
        }
        $response = $this->post($path, $data);
        return new Client\Result($response->getBody());
    }

    /**
     * Get status replies
     *
     * $params may include one or more of the following keys
     * - since_id: return results only after the specified tweet id
     * - page: return page X of results
     *
     * @throws Http\Client\Exception if HTTP request fails or times out
     * @return Client\Result
     */
    public function statusReplies(array $params = array())
    {
        $this->init();
        $path = '/1/statuses/mentions.xml';
        $_params = array();
        foreach ($params as $key => $value) {
            switch (strtolower($key)) {
                case 'since_id':
                    $_params['since_id'] = $this->validInteger($value);
                    break;
                case 'page':
                    $_params['page'] = (int) $value;
                    break;
                default:
                    break;
            }
        }
        $response = $this->get($path, $_params);
        return new Client\Result($response->getBody());
    }

    /**
     * Destroy a status message
     *
     * @param  int $id ID of status to destroy
     * @throws Http\Client\Exception if HTTP request fails or times out
     * @return Client\Result
     */
    public function statusDestroy($id)
    {
        $this->init();
        $path = '/1/statuses/destroy/' . $this->validInteger($id) . '.xml';
        $response = $this->post($path);
        return new Client\Result($response->getBody());
    }

    /**
     * User friends
     *
     * @param  int|string $id Id or username of user for whom to fetch friends
     * @throws Http\Client\Exception if HTTP request fails or times out
     * @return Client\Result
     */
    public function userFriends(array $params = array())
    {
        $this->init();
        $path = '/1/statuses/friends';
        $_params = array();

        foreach ($params as $key => $value) {
            switch (strtolower($key)) {
                case 'id':
                    $path .= '/' . $value;
                    break;
                case 'page':
                    $_params['page'] = (int) $value;
                    break;
                default:
                    break;
            }
        }
        $path .= '.xml';

        $response = $this->get($path, $_params);
        return new Client\Result($response->getBody());
    }

    /**
     * User Followers
     *
     * @param  bool $lite If true, prevents inline inclusion of current status for followers; defaults to false
     * @throws Http\Client\Exception if HTTP request fails or times out
     * @return Client\Result
     */
    public function userFollowers($lite = false)
    {
        $this->init();
        $path = '/1/statuses/followers.xml';
        if ($lite) {
            $this->lite = 'true';
        }
        $response = $this->get($path);
        return new Client\Result($response->getBody());
    }

    /**
     * Show extended information on a user
     *
     * @param  int|string $id User ID or name
     * @throws Http\Client\Exception if HTTP request fails or times out
     * @return Client\Result
     */
    public function userShow($id)
    {
        $this->init();
        $path = '/1/users/show.xml';
        $response = $this->get($path, array('id'=>$id));
        return new Client\Result($response->getBody());
    }

    /**
     * Retrieve direct messages for the current user
     *
     * $params may include one or more of the following keys
     * - since_id: return statuses only greater than the one specified
     * - page: return page X of results
     *
     * @param  array $params
     * @throws Http\Client\Exception if HTTP request fails or times out
     * @return Client\Result
     */
    public function directMessageMessages(array $params = array())
    {
        $this->init();
        $path = '/1/direct_messages.xml';
        $_params = array();
        foreach ($params as $key => $value) {
            switch (strtolower($key)) {
                case 'since_id':
                    $_params['since_id'] = $this->validInteger($value);
                    break;
                case 'page':
                    $_params['page'] = (int) $value;
                    break;
                default:
                    break;
            }
        }
        $response = $this->get($path, $_params);
        return new Client\Result($response->getBody());
    }

    /**
     * Retrieve list of direct messages sent by current user
     *
     * $params may include one or more of the following keys
     * - since_id: return statuses only greater than the one specified
     * - page: return page X of results
     *
     * @param  array $params
     * @throws Http\Client\Exception if HTTP request fails or times out
     * @return Client\Result
     */
    public function directMessageSent(array $params = array())
    {
        $this->init();
        $path = '/1/direct_messages/sent.xml';
        $_params = array();
        foreach ($params as $key => $value) {
            switch (strtolower($key)) {
                case 'since_id':
                    $_params['since_id'] = $this->validInteger($value);
                    break;
                case 'page':
                    $_params['page'] = (int) $value;
                    break;
                default:
                    break;
            }
        }
        $response = $this->get($path, $_params);
        return new Client\Result($response->getBody());
    }

    /**
     * Send a direct message to a user
     *
     * @param  int|string $user User to whom to send message
     * @param  string $text Message to send to user
     * @return Client\Result
     * @throws Exception\InvalidArgumentException if message is empty
     * @throws Exception\OutOfRangeException if message is too long
     * @throws Http\Client\Exception if HTTP request fails or times out
     */
    public function directMessageNew($user, $text)
    {
        $this->init();
        $path = '/1/direct_messages/new.xml';
        $len = iconv_strlen($text, 'UTF-8');
        if (0 == $len) {
            throw new Exception\InvalidArgumentException(
                'Direct message must contain at least one character'
            );
        } elseif (140 < $len) {
            throw new Exception\OutOfRangeException(
                'Direct message must contain no more than 140 characters'
            );
        }
        $data = array('user' => $user, 'text' => $text);
        $response = $this->post($path, $data);
        return new Client\Result($response->getBody());
    }

    /**
     * Destroy a direct message
     *
     * @param  int $id ID of message to destroy
     * @throws Http\Client\Exception if HTTP request fails or times out
     * @return Client\Result
     */
    public function directMessageDestroy($id)
    {
        $this->init();
        $path = '/1/direct_messages/destroy/' . $this->validInteger($id) . '.xml';
        $response = $this->post($path);
        return new Client\Result($response->getBody());
    }

    /**
     * Create friendship
     *
     * @param  int|string $id User ID or name of new friend
     * @throws Http\Client\Exception if HTTP request fails or times out
     * @return Client\Result
     */
    public function friendshipCreate($id)
    {
        $this->init();
        $path = '/1/friendships/create/' . $id . '.xml';
        $response = $this->post($path);
        return new Client\Result($response->getBody());
    }

    /**
     * Destroy friendship
     *
     * @param  int|string $id User ID or name of friend to remove
     * @throws Http\Client\Exception if HTTP request fails or times out
     * @return Client\Result
     */
    public function friendshipDestroy($id)
    {
        $this->init();
        $path = '/1/friendships/destroy/' . $id . '.xml';
        $response = $this->post($path);
        return new Client\Result($response->getBody());
    }

    /**
     * Friendship exists
     *
     * @param int|string $id User ID or name of friend to see if they are your friend
     * @throws Http\Client\Exception if HTTP request fails or times out
     * @return Client\Result
     */
    public function friendshipExists($id)
    {
        $this->init();
        $path = '/1/friendships/exists.xml';
        $data = array('user_a' => $this->getUsername(), 'user_b' => $id);
        $response = $this->get($path, $data);
        return new Client\Result($response->getBody());
    }

    /**
     * Verify Account Credentials
     * @throws Http\Client\Exception if HTTP request fails or times out
     * @return Client\Result
     */
    public function accountVerifyCredentials()
    {
        $this->init();
        $response = $this->get('/1/account/verify_credentials.xml');
        return new Client\Result($response->getBody());
    }

    /**
     * End current session
     *
     * @throws Http\Client\Exception if HTTP request fails or times out
     * @return true
     */
    public function accountEndSession()
    {
        $this->init();
        $this->get('/1/account/end_session');
        return true;
    }

    /**
     * Returns the number of api requests you have left per hour.
     *
     * @throws Http\Client\Exception if HTTP request fails or times out
     * @return Client\Result
     */
    public function accountRateLimitStatus()
    {
        $this->init();
        $response = $this->get('/1/account/rate_limit_status.xml');
        return new Client\Result($response->getBody());
    }

    /**
     * Fetch favorites
     *
     * $params may contain one or more of the following:
     * - 'id': Id of a user for whom to fetch favorites
     * - 'page': Retrieve a different page of resuls
     *
     * @param  array $params
     * @throws Http\Client\Exception if HTTP request fails or times out
     * @return Client\Result
     */
    public function favoriteFavorites(array $params = array())
    {
        $this->init();
        $path = '/1/favorites';
        $_params = array();
        foreach ($params as $key => $value) {
            switch (strtolower($key)) {
                case 'id':
                    $path .= '/' . $this->validInteger($value);
                    break;
                case 'page':
                    $_params['page'] = (int) $value;
                    break;
                default:
                    break;
            }
        }
        $path .= '.xml';
        $response = $this->get($path, $_params);
        return new Client\Result($response->getBody());
    }

    /**
     * Mark a status as a favorite
     *
     * @param  int $id Status ID you want to mark as a favorite
     * @throws Http\Client\Exception if HTTP request fails or times out
     * @return Client\Result
     */
    public function favoriteCreate($id)
    {
        $this->init();
        $path = '/1/favorites/create/' . $this->validInteger($id) . '.xml';
        $response = $this->post($path);
        return new Client\Result($response->getBody());
    }

    /**
     * Remove a favorite
     *
     * @param  int $id Status ID you want to de-list as a favorite
     * @throws Http\Client\Exception if HTTP request fails or times out
     * @return Client\Result
     */
    public function favoriteDestroy($id)
    {
        $this->init();
        $path = '/1/favorites/destroy/' . $this->validInteger($id) . '.xml';
        $response = $this->post($path);
        return new Client\Result($response->getBody());
    }

    /**
     * Blocks the user specified in the ID parameter as the authenticating user.
     * Destroys a friendship to the blocked user if it exists.
     *
     * @param integer|string $id       The ID or screen name of a user to block.
     * @return Client\Result
     */
    public function blockCreate($id)
    {
        $this->init();
        $path = '/1/blocks/create/' . $id . '.xml';
        $response = $this->post($path);
        return new Client\Result($response->getBody());
    }

    /**
     * Un-blocks the user specified in the ID parameter for the authenticating user
     *
     * @param integer|string $id       The ID or screen_name of the user to un-block.
     * @return Client\Result
     */
    public function blockDestroy($id)
    {
        $this->init();
        $path = '/1/blocks/destroy/' . $id . '.xml';
        $response = $this->post($path);
        return new Client\Result($response->getBody());
    }

    /**
     * Returns if the authenticating user is blocking a target user.
     *
     * @param string|integer $id    The ID or screen_name of the potentially blocked user.
     * @param boolean $returnResult Instead of returning a boolean return the rest response from twitter
     * @return Boolean|Client\Result
     */
    public function blockExists($id, $returnResult = false)
    {
        $this->init();
        $path = '/1/blocks/exists/' . $id . '.xml';
        $response = $this->get($path);

        $cr = new Client\Result($response->getBody());

        if ($returnResult === true)
            return $cr;

        if (!empty($cr->request)) {
            return false;
        }

        return true;
    }

    /**
     * Returns an array of user objects that the authenticating user is blocking
     *
     * @param integer $page         Optional. Specifies the page number of the results beginning at 1. A single page contains 20 ids.
     * @param boolean $returnUserIds  Optional. Returns only the userid's instead of the whole user object
     * @return Client\Result
     */
    public function blockBlocking($page = 1, $returnUserIds = false)
    {
        $this->init();
        $path = '/1/blocks/blocking';
        if ($returnUserIds === true) {
            $path .= '/ids';
        }
        $path .= '.xml';
        $response = $this->get($path, array('page' => $page));
        return new Client\Result($response->getBody());
    }

    /**
     * Returns an array of user objects that retweeted the tweets identified with the given $id
     *
     * @param integer $id  The Id of the tweet we want to know who retweeted
     * @return Client\Result
     */
    public function statusRetweetedBy($id)
    {
        $this->init();
        $path = '/1/statuses/' . $this->validInteger($id) . '/retweeted_by.xml';
        $response = $this->get($path);
        return new Client\Result($response->getBody());
    }

    /**
     * Protected function to validate that the integer is valid or return a 0
     * @param $int
     * @throws Http\Client\Exception if HTTP request fails or times out
     * @return integer
     */
    protected function validInteger($int)
    {
        if (preg_match("/(\d+)/", $int)) {
            return $int;
        }
        return 0;
    }

    /**
     * Validate a screen name using Twitter rules
     *
     * @param string $name
     * @return string
     * @throws Exception\InvalidArgumentException
     */
    protected function validateScreenName($name)
    {
        if (!preg_match('/^[a-zA-Z0-9_]{0,20}$/', $name)) {
            throw new Exception\InvalidArgumentException(
                'Screen name, "' . $name
                . '" should only contain alphanumeric characters and'
                . ' underscores, and not exceed 15 characters.');
        }
        return $name;
    }

    /**
     * Call a remote REST web service URI and return the Zend_Http_Response object
     *
     * @param  string $path            The path to append to the URI
     * @throws Client\Exception
     * @return void
     */
    protected function prepare($path)
    {
        // Get the URI object and configure it
        if (!$this->uri instanceof Uri\Uri) {
            throw new Client\Exception(
                'URI object must be set before performing call'
            );
        }

        $uri = $this->uri->toString();

        if ($path[0] != '/' && $uri[strlen($uri) - 1] != '/') {
            $path = '/' . $path;
        }

        $this->uri->setPath($path);

        /**
         * Get the HTTP client and configure it for the endpoint URI.
         * Do this each time because the Zend\Http\Client instance is shared
         * among all Zend_Service_Abstract subclasses.
         */
        $this->localHttpClient->resetParameters()->setUri((string) $this->uri);
    }

    /**
     * Performs an HTTP GET request to the $path.
     *
     * @param string $path
     * @param array  $query Array of GET parameters
     * @throws Http\Client\Exception
     * @return Http\Response
     */
    protected function get($path, array $query = array())
    {
        $this->prepare($path);
        $client = $this->localHttpClient;
        $client->setParameterGet($query);
        $client->setMethod(Http\Request::METHOD_GET);
        $response = $client->send();
        return $response;
    }

    /**
     * Performs an HTTP POST request to $path.
     *
     * @param string $path
     * @param mixed $data Raw data to send
     * @throws Http\Client\Exception
     * @return Http\Response
     */
    protected function post($path, $data = null)
    {
        $this->prepare($path);
        return $this->performPost(Http\Request::METHOD_POST, $data);
    }

    /**
     * Perform a POST or PUT
     *
     * Performs a POST or PUT request. Any data provided is set in the HTTP
     * client. String data is pushed in as raw POST data; array or object data
     * is pushed in as POST parameters.
     *
     * @param mixed $method
     * @param mixed $data
     * @return Http\Response
     */
    protected function performPost($method, $data = null)
    {
        $client = $this->localHttpClient;
        if (is_string($data)) {
            $client->setRawData($data);
        } elseif (is_array($data) || is_object($data)) {
            $client->setParameterPost((array) $data);
        }
        $client->setMethod($method);
        return $client->send();
    }
}
