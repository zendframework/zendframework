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
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: $
 */


/**
 * @see Zend_Rest_Client
 */
require_once 'Zend/Rest/Client.php';

/**
 * @see Zend_Rest_Client_Result
 */
require_once 'Zend/Rest/Client/Result.php';

/**
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Twitter
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Service_Twitter extends Zend_Rest_Client
{
    /**
     * Whether or not authorization has been initialized for the current user.
     * @var bool
     */
    protected $_authInitialized = false;

    /**
     * @var Zend_Http_CookieJar
     */
    protected $_cookieJar;

    /**
     * Date format for 'since' strings
     * @var string
     */
    protected $_dateFormat = 'D, d M Y H:i:s T';

    /**
     * Username
     * @var string
     */
    protected $_username;

    /**
     * Password
     * @var string
     */
    protected $_password;

    /**
     * Current method type (for method proxying)
     * @var string
     */
    protected $_methodType;

    /**
     * Types of API methods
     * @var array
     */
    protected $_methodTypes = array(
        'status',
        'user',
        'directMessage',
        'friendship',
        'account',
        'favorite'
    );

    /**
     * Constructor
     *
     * @param  string $username
     * @param  string $password
     * @return void
     */
    public function __construct($username, $password)
    {
        $this->setUsername($username);
        $this->setPassword($password);
        $this->setUri('http://twitter.com');

        $client = self::getHttpClient();
        $client->setHeaders('Accept-Charset', 'ISO-8859-1,utf-8');
    }

    /**
     * Retrieve username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->_username;
    }

    /**
     * Set username
     *
     * @param  string $value
     * @return Zend_Service_Twitter
     */
    public function setUsername($value)
    {
        $this->_username = $value;
        $this->_authInitialized = false;
        return $this;
    }

    /**
     * Retrieve password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->_password;
    }

    /**
     * Set password
     *
     * @param  string $value
     * @return Zend_Service_Twitter
     */
    public function setPassword($value)
    {
        $this->_password = $value;
        $this->_authInitialized = false;
        return $this;
    }

    /**
     * Proxy service methods
     *
     * @param  string $type
     * @return Zend_Service_Twitter
     * @throws Zend_Service_Twitter_Exception if method is not in method types list
     */
    public function __get($type)
    {
        if (!in_array($type, $this->_methodTypes)) {
            include_once 'Zend/Service/Twitter/Exception.php';
            throw new Zend_Service_Twitter_Exception('Invalid method type "' . $type . '"');
        }

        $this->_methodType = $type;
        return $this;
    }

    /**
     * Method overloading
     *
     * @param  string $method
     * @param  array $params
     * @return mixed
     * @throws Zend_Service_Twitter_Exception if unable to find method
     */
    public function __call($method, $params)
    {
        if (empty($this->_methodType)) {
            include_once 'Zend/Service/Twitter/Exception.php';
            throw new Zend_Service_Twitter_Exception('Invalid method "' . $method . '"');
        }

        $test = $this->_methodType . ucfirst($method);
        if (!method_exists($this, $test)) {
            include_once 'Zend/Service/Twitter/Exception.php';
            throw new Zend_Service_Twitter_Exception('Invalid method "' . $test . '"');
        }

        return call_user_func_array(array($this, $test), $params);
    }

    /**
     * Initialize HTTP authentication
     *
     * @return void
     */
    protected function _init()
    {
        $client = self::getHttpClient();

        $client->resetParameters();

        if (null == $this->_cookieJar) {
            $client->setCookieJar();
            $this->_cookieJar = $client->getCookieJar();
        } else {
            $client->setCookieJar($this->_cookieJar);
        }

        if (!$this->_authInitialized) {
            $client->setAuth($this->getUsername(), $this->getPassword());
            $this->_authInitialized = true;
        }
    }

    /**
     * Set date header
     *
     * @param  int|string $value
     * @return void
     */
    protected function _setDate($value)
    {
        if (is_int($value)) {
            $date = date($this->_dateFormat, $value);
        } else {
            $date = date($this->_dateFormat, strtotime($value));
        }
        self::getHttpClient()->setHeaders('If-Modified-Since', $date);
    }

    /**
     * Public Timeline status
     *
     * @return Zend_Rest_Client_Result
     */
    public function statusPublicTimeline()
    {
        $this->_init();
        $path = '/statuses/public_timeline.xml';
        $response = $this->restGet($path);
        return new Zend_Rest_Client_Result($response->getBody());
    }

    /**
     * Friend Timeline Status
     *
     * $params may include one or more of the following keys
     * - id: ID of a friend whose timeline you wish to receive
     * - count: how many statuses to return
     * - since: return results only after the date specified
     * - since_id: return results only after the specific tweet
     * - page: return page X of results
     *
     * @param  array $params
     * @return void
     */
    public function statusFriendsTimeline(array $params = array())
    {
        $this->_init();
        $path = '/statuses/friends_timeline';
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
                    $_params['since_id'] = (int) $value;
                    break;
                case 'since':
                    $this->_setDate($value);
                    break;
                case 'page':
                    $_params['page'] = (int) $value;
                    break;
                default:
                    break;
            }
        }
        $path    .= '.xml';
        $response = $this->restGet($path, $_params);
        return new Zend_Rest_Client_Result($response->getBody());
    }

    /**
     * User Timeline status
     *
     * $params may include one or more of the following keys
     * - id: ID of a friend whose timeline you wish to receive
     * - since: return results only after the date specified
     * - page: return page X of results
     * - count: how many statuses to return
     *
     * @return Zend_Rest_Client_Result
     */
    public function statusUserTimeline(array $params = array())
    {
        $this->_init();
        $path = '/statuses/user_timeline';
        $_params = array();
        foreach ($params as $key => $value) {
            switch (strtolower($key)) {
                case 'id':
                    $path .= '/' . $value;
                    break;
                case 'since':
                    $this->_setDate($value);
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
                default:
                    break;
            }
        }
        $path    .= '.xml';
        $response = $this->restGet($path, $_params);
        return new Zend_Rest_Client_Result($response->getBody());
    }

    /**
     * Show a single status
     *
     * @param  int $id Id of status to show
     * @return Zend_Rest_Client_Result
     */
    public function statusShow($id)
    {
        $this->_init();
        $path = '/statuses/show/' . $id . '.xml';
        $response = $this->restGet($path);
        return new Zend_Rest_Client_Result($response->getBody());
    }

    /**
     * Update user's current status
     *
     * @param  string $status
     * @param  int $in_reply_to_status_id
     * @return Zend_Rest_Client_Result
     * @throws Zend_Service_Twitter_Exception if message is too short or too long
     */
    public function statusUpdate($status, $in_reply_to_status_id = null)
    {
        $this->_init();
        $path = '/statuses/update.xml';
        $len  = iconv_strlen($status, 'UTF-8');
        if ($len > 140) {
            include_once 'Zend/Service/Twitter/Exception.php';
            throw new Zend_Service_Twitter_Exception('Status must be no more than 140 characters in length');
        } elseif (0 == $len) {
            include_once 'Zend/Service/Twitter/Exception.php';
            throw new Zend_Service_Twitter_Exception('Status must contain at least one character');
        }

        $data = array(
            'status' => $status
        );

        if(is_numeric($in_reply_to_status_id) && !empty($in_reply_to_status_id)) {
            $data['in_reply_to_status_id'] = $in_reply_to_status_id;
        }

        //$this->status = $status;
        $response = $this->restPost($path, $data);
        return new Zend_Rest_Client_Result($response->getBody());
    }

    /**
     * Get status replies
     *
     * $params may include one or more of the following keys
     * - since: return results only after the date specified
     * - since_id: return results only after the specified tweet id
     * - page: return page X of results
     *
     * @return Zend_Rest_Client_Result
     */
    public function statusReplies(array $params = array())
    {
        $this->_init();
        $path = '/statuses/replies.xml';

        $_params = array();
        foreach ($params as $key => $value) {
            switch (strtolower($key)) {
                case 'since':
                    $this->_setDate($value);
                    break;
                case 'since_id':
                    $_params['since_id'] = (int) $value;
                    break;
                case 'page':
                    $_params['page'] = (int) $value;
                    break;
                default:
                    break;
            }
        }

        $response = $this->restGet($path, $_params);
        return new Zend_Rest_Client_Result($response->getBody());
    }

    /**
     * Destroy a status message
     *
     * @param  int $id ID of status to destroy
     * @return Zend_Rest_Client_Result
     */
    public function statusDestroy($id)
    {
        $this->_init();
        $path = '/statuses/destroy/' . (int) $id . '.xml';

        $response = $this->restPost($path);
        return new Zend_Rest_Client_Result($response->getBody());
    }

    /**
     * User friends
     *
     * @param  int|string $id Id or username of user for whom to fetch friends
     * @return Zend_Rest_Client_Result
     */
    public function userFriends(array $params = array())
    {
        $this->_init();
        $path = '/statuses/friends';
        $_params = array();
        foreach ($params as $key => $value) {
            switch (strtolower($key)) {
                case 'id':
                    $path .= '/' . $value;
                    break;
                case 'since':
                    $this->_setDate($value);
                    break;
                case 'page':
                    $_params['page'] = (int) $value;
                    break;
                default:
                    break;
            }
        }
        $path    .= '.xml';

        $response = $this->restGet($path, $_params);
        return new Zend_Rest_Client_Result($response->getBody());
    }

    /**
     * User Followers
     *
     * @param  bool $lite If true, prevents inline inclusion of current status for followers; defaults to false
     * @return Zend_Rest_Client_Result
     */
    public function userFollowers($lite = false)
    {
        $this->_init();
        $path = '/statuses/followers.xml';
        if ($lite) {
            $this->lite = 'true';
        }

        $response = $this->restGet($path);
        return new Zend_Rest_Client_Result($response->getBody());
    }

    /**
     * Get featured users
     *
     * @return Zend_Rest_Client_Result
     */
    public function userFeatured()
    {
        $this->_init();
        $path = '/statuses/featured.xml';

        $response = $this->restGet($path);
        return new Zend_Rest_Client_Result($response->getBody());
    }

    /**
     * Show extended information on a user
     *
     * @param  int|string $id User ID or name
     * @return Zend_Rest_Client_Result
     */
    public function userShow($id)
    {
        $this->_init();
        $path = '/users/show/' . $id . '.xml';

        $response = $this->restGet($path);
        return new Zend_Rest_Client_Result($response->getBody());
    }

    /**
     * Retrieve direct messages for the current user
     *
     * $params may include one or more of the following keys
     * - since: return results only after the date specified
     * - since_id: return statuses only greater than the one specified
     * - page: return page X of results
     *
     * @param  array $params
     * @return Zend_Rest_Client_Result
     */
    public function directMessageMessages(array $params = array())
    {
        $this->_init();
        $path = '/direct_messages.xml';
        $_params = array();
        foreach ($params as $key => $value) {
            switch (strtolower($key)) {
                case 'since':
                    $this->_setDate($value);
                    break;
                case 'since_id':
                    $_params['since_id'] = (int) $value;
                    break;
                case 'page':
                    $_params['page'] = (int) $value;
                    break;
                default:
                    break;
            }
        }
        $response = $this->restGet($path, $_params);
        return new Zend_Rest_Client_Result($response->getBody());
    }

    /**
     * Retrieve list of direct messages sent by current user
     *
     * $params may include one or more of the following keys
     * - since: return results only after the date specified
     * - since_id: return statuses only greater than the one specified
     * - page: return page X of results
     *
     * @param  array $params
     * @return Zend_Rest_Client_Result
     */
    public function directMessageSent(array $params = array())
    {
        $this->_init();
        $path = '/direct_messages/sent.xml';
        $_params = array();
        foreach ($params as $key => $value) {
            switch (strtolower($key)) {
                case 'since':
                    $this->_setDate($value);
                    break;
                case 'since_id':
                    $_params['since_id'] = (int) $value;
                    break;
                case 'page':
                    $_params['page'] = (int) $value;
                    break;
                default:
                    break;
            }
        }
        $response = $this->restGet($path, $_params);
        return new Zend_Rest_Client_Result($response->getBody());
    }

    /**
     * Send a direct message to a user
     *
     * @param  int|string $user User to whom to send message
     * @param  string $text Message to send to user
     * @return Zend_Rest_Client_Result
     * @throws Zend_Service_Twitter_Exception if message is too short or too long
     */
    public function directMessageNew($user, $text)
    {
        $this->_init();
        $path = '/direct_messages/new.xml';

        $len = iconv_strlen($text, 'UTF-8');
        if (0 == $len) {
            throw new Zend_Service_Twitter_Exception('Direct message must contain at least one character');
        } elseif (140 < $len) {
            throw new Zend_Service_Twitter_Exception('Direct message must contain no more than 140 characters');
        }

        $data = array(
            'user'	=> $user,
            'text'	=> $text,
        );

        $response = $this->restPost($path, $data);
        return new Zend_Rest_Client_Result($response->getBody());
    }

    /**
     * Destroy a direct message
     *
     * @param  int $id ID of message to destroy
     * @return Zend_Rest_Client_Result
     */
    public function directMessageDestroy($id)
    {
        $this->_init();
        $path = '/direct_messages/destroy/' . $id . '.xml';

        $response = $this->restPost($path);
        return new Zend_Rest_Client_Result($response->getBody());
    }

    /**
     * Create friendship
     *
     * @param  int|string $id User ID or name of new friend
     * @return Zend_Rest_Client_Result
     */
    public function friendshipCreate($id)
    {
        $this->_init();
        $path = '/friendships/create/' . $id . '.xml';

        $response = $this->restPost($path);
        return new Zend_Rest_Client_Result($response->getBody());
    }

    /**
     * Destroy friendship
     *
     * @param  int|string $id User ID or name of friend to remove
     * @return Zend_Rest_Client_Result
     */
    public function friendshipDestroy($id)
    {
        $this->_init();
        $path = '/friendships/destroy/' . $id . '.xml';

        $response = $this->restPost($path);
        return new Zend_Rest_Client_Result($response->getBody());
    }

    /**
     * Friendship exists
     *
     * @param int|string $id User ID or name of friend to see if they are your friend
     * @return Zend_Rest_Client_result
     */
    public function friendshipExists($id)
    {
        $this->_init();
        $path = '/friendships/exists.xml';

        $data = array(
            'user_a' => $this->getUsername(),
            'user_b' => $id
        );

        $response = $this->restGet($path, $data);
        return new Zend_Rest_Client_Result($response->getBody());
    }

    /**
     * Verify Account Credentials
     *
     * @return Zend_Rest_Client_Result
     */
    public function accountVerifyCredentials()
    {
        $this->_init();
        $response = $this->restGet('/account/verify_credentials.xml');
        return new Zend_Rest_Client_Result($response->getBody());
    }

    /**
     * End current session
     *
     * @return true
     */
    public function accountEndSession()
    {
        $this->_init();
        $this->restGet('/account/end_session');
        return true;
    }

    /**
     * Returns the number of api requests you have left per hour.
     *
     * @return Zend_Rest_Client_Result
     */
    public function accountRateLimitStatus()
    {
        $this->_init();
        $response = $this->restGet('/account/rate_limit_status.xml');
        return new Zend_Rest_Client_Result($response->getBody());
    }

    /**
     * Fetch favorites
     *
     * $params may contain one or more of the following:
     * - 'id': Id of a user for whom to fetch favorites
     * - 'page': Retrieve a different page of resuls
     *
     * @param  array $params
     * @return Zend_Rest_Client_Result
     */
    public function favoriteFavorites(array $params = array())
    {
        $this->_init();
        $path = '/favorites';
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
        $response = $this->restGet($path, $_params);
        return new Zend_Rest_Client_Result($response->getBody());
    }

    /**
     * Mark a status as a favorite
     *
     * @param  int $id Status ID you want to mark as a favorite
     * @return Zend_Rest_Client_Result
     */
    public function favoriteCreate($id)
    {
        $this->_init();
        $path = '/favorites/create/' . (int) $id . '.xml';

        $response = $this->restPost($path);
        return new Zend_Rest_Client_Result($response->getBody());
    }

    /**
     * Remove a favorite
     *
     * @param  int $id Status ID you want to de-list as a favorite
     * @return Zend_Rest_Client_Result
     */
    public function favoriteDestroy($id)
    {
        $this->_init();
        $path = '/favorites/destroy/' . (int) $id . '.xml';

        $response = $this->restPost($path);
        return new Zend_Rest_Client_Result($response->getBody());
    }
}
