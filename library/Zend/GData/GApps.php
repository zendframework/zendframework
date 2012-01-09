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
 * @package    Zend_Gdata
 * @subpackage GApps
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\GData;

/**
 * Service class for interacting with the Google Apps Provisioning API.
 *
 * Like other service classes in this module, this class provides access via
 * an HTTP client to Google servers for working with entries and feeds.
 *
 * Because of the nature of this API, all access must occur over an
 * authenticated connection.
 *
 * @link http://code.google.com/apis/apps/gdata_provisioning_api_v2.0_reference.html
 *
 * @uses       \Zend\GData\GData
 * @uses       \Zend\GData\App\Exception
 * @uses       \Zend\GData\App\InvalidArgumentException
 * @uses       \Zend\GData\App\IOException
 * @uses       \Zend\GData\GApps\EmailListFeed
 * @uses       \Zend\GData\GApps\EmailListRecipientFeed
 * @uses       \Zend\GData\GApps\Error
 * @uses       \Zend\GData\GApps\NicknameFeed
 * @uses       \Zend\GData\GApps\ServiceException
 * @uses       \Zend\GData\GApps\UserFeed
 * @uses       \Zend\Loader
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage GApps
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class GApps extends GData
{

    const APPS_BASE_FEED_URI = 'https://apps-apis.google.com/a/feeds';
    const AUTH_SERVICE_NAME = 'apps';

    /**
     * Path to user feeds on the Google Apps server.
     */
    const APPS_USER_PATH = '/user/2.0';

    /**
     * Path to nickname feeds on the Google Apps server.
     */
    const APPS_NICKNAME_PATH = '/nickname/2.0';

    /**
     * Path to group feeds on the Google Apps server.
     */
    const APPS_GROUP_PATH = '/group/2.0';

    /**
     * Path to email list feeds on the Google Apps server.
     */
    const APPS_EMAIL_LIST_PATH = '/emailList/2.0';

    /**
     * Path to email list recipient feeds on the Google Apps server.
     */
    const APPS_EMAIL_LIST_RECIPIENT_POSTFIX = '/recipient';

    /**
     * The domain which is being administered via the Provisioning API.
     *
     * @var string
     */
    protected $_domain = null;

    /**
     * Namespaces used for \Zend\Gdata\GApps
     *
     * @var array
     */
    public static $namespaces = array(
        array('apps', 'http://schemas.google.com/apps/2006', 1, 0)
    );

    /**
     * Create \Gdata\GApps object
     *
     * @param \Zend\Http\Client $client (optional) The HTTP client to use when
     *          when communicating with the Google Apps servers.
     * @param string $domain (optional) The Google Apps domain which is to be
     *          accessed.
     * @param string $applicationId The identity of the app in the form of Company-AppName-Version
     */
    public function __construct($client = null, $domain = null, $applicationId = 'MyCompany-MyApp-1.0')
    {
        $this->registerPackage('Zend\GData\GApps');
        $this->registerPackage('Zend\GData\GApps\Extension');
        parent::__construct($client, $applicationId);
        $this->_httpClient->setParameterPost(array('service' => self::AUTH_SERVICE_NAME));
        $this->_domain = $domain;
    }

    /**
     * Convert an exception to an ServiceException if an AppsForYourDomain
     * XML document is contained within the original exception's HTTP
     * response. If conversion fails, throw the original error.
     *
     * @param \Zend\Gdata\Exception $e The exception to convert.
     * @throws \Zend\GData\GApps\ServiceException
     * @throws mixed
     */
    public static function throwServiceExceptionIfDetected($e)
    {
        // Check to make sure that there actually response!
        // This can happen if the connection dies before the request
        // completes. (See ZF-5949)
        $response = $e->getResponse();
        if (!$response) {
            throw new App\IOException('No HTTP response received (possible connection failure)');
        }

        try {
            // Check to see if there is an AppsForYourDomainErrors
            // datastructure in the response. If so, convert it to
            // an exception and throw it.
            $error = new GApps\ServiceException();
            $error->importFromString($response->getBody());
            throw $error;
        } catch (App\Exception $e2) {
            // Unable to convert the response to a ServiceException,
            // most likely because the server didn't return an
            // AppsForYourDomainErrors document. Throw the original
            // exception.
            throw $e;
        }
    }

    /**
     * Imports a feed located at $uri.
     * This method overrides the default behavior of \Zend\Gdata\App,
     * providing support for \Zend\Gdata\GApps\ServiceException.
     *
     * @param  string $uri
     * @param  \Zend\Http\Client $client (optional) The client used for
     *          communication
     * @param  string $className (optional) The class which is used as the
     *          return type
     * @throws \Zend\GData\App\Exception
     * @throws \Zend\GData\App\HttpException
     * @throws \Zend\GData\GApps\ServiceException
     * @return \Zend\GData\App\Feed
     */
    public static function import($uri, $client = null, $className='Zend\GData\App\Feed')
    {
        try {
            return parent::import($uri, $client, $className);
        } catch (App\HttpException $e) {
            self::throwServiceExceptionIfDetected($e);
        }
    }

    /**
     * GET a URI using client object.
     * This method overrides the default behavior of \Zend\Gdata\App,
     * providing support for \Zend\Gdata\GApps\ServiceException.
     *
     * @param string $uri GET URI
     * @param array $extraHeaders Extra headers to add to the request, as an
     *        array of string-based key/value pairs.
     * @throws \Zend\GData\App\HttpException
     * @throws \Zend\GData\GApps\ServiceException
     * @return \Zend\Http\Response
     */
    public function get($uri, $extraHeaders = array())
    {
        try {
            return parent::get($uri, $extraHeaders);
        } catch (App\HttpException $e) {
            self::throwServiceExceptionIfDetected($e);
        }
    }

    /**
     * POST data with client object.
     * This method overrides the default behavior of \Zend\Gdata\App,
     * providing support for \Zend\Gdata\GApps\ServiceException.
     *
     * @param mixed $data The \Zend\GData\App\Entry or XML to post
     * @param string $uri (optional) POST URI
     * @param integer $remainingRedirects (optional)
     * @param string $contentType Content-type of the data
     * @param array $extraHaders Extra headers to add tot he request
     * @return \Zend\Http\Response
     * @throws \Zend\GData\App\HttpException
     * @throws \Zend\GData\App\InvalidArgumentException
     * @throws \Zend\GData\GApps\ServiceException
     */
    public function post($data, $uri = null, $remainingRedirects = null,
            $contentType = null, $extraHeaders = null)
    {
        try {
            return parent::post($data, $uri, $remainingRedirects, $contentType, $extraHeaders);
        } catch (App\HttpException $e) {
            self::throwServiceExceptionIfDetected($e);
        }
    }

    /**
     * PUT data with client object
     * This method overrides the default behavior of \Zend\Gdata\App,
     * providing support for \Zend\Gdata\GApps\ServiceException.
     *
     * @param mixed $data The \Zend\GData\App\Entry or XML to post
     * @param string $uri (optional) PUT URI
     * @param integer $remainingRedirects (optional)
     * @param string $contentType Content-type of the data
     * @param array $extraHaders Extra headers to add tot he request
     * @return \Zend\Http\Response
     * @throws \Zend\GData\App\HttpException
     * @throws \Zend\GData\App\InvalidArgumentException
     * @throws \Zend\GData\GApps\ServiceException
     */
    public function put($data, $uri = null, $remainingRedirects = null,
            $contentType = null, $extraHeaders = null)
    {
        try {
            return parent::put($data, $uri, $remainingRedirects, $contentType, $extraHeaders);
        } catch (App\HttpException $e) {
            self::throwServiceExceptionIfDetected($e);
        }
    }

    /**
     * DELETE entry with client object
     * This method overrides the default behavior of \Zend\Gdata\App,
     * providing support for \Zend\Gdata\GApps\ServiceException.
     *
     * @param mixed $data The \Zend\GData\App\Entry or URL to delete
     * @param integer $remainingRedirects (optional)
     * @return void
     * @throws \Zend\GData\App\HttpException
     * @throws \Zend\GData\App\InvalidArgumentException
     * @throws \Zend\GData\GApps\ServiceException
     */
    public function delete($data, $remainingRedirects = null)
    {
        try {
            return parent::delete($data, $remainingRedirects);
        } catch (App\HttpException $e) {
            self::throwServiceExceptionIfDetected($e);
        }
    }

    /**
     * Set domain for this service instance. This should be a fully qualified
     * domain, such as 'foo.example.com'.
     *
     * This value is used when calculating URLs for retrieving and posting
     * entries. If no value is specified, a URL will have to be manually
     * constructed prior to using any methods which interact with the Google
     * Apps provisioning service.
     *
     * @param string $value The domain to be used for this session.
     */
    public function setDomain($value)
    {
        $this->_domain = $value;
    }

    /**
     * Get domain for this service instance. This should be a fully qualified
     * domain, such as 'foo.example.com'. If no domain is set, null will be
     * returned.
     *
     * @return string The domain to be used for this session, or null if not
     *          set.
     */
    public function getDomain()
    {
        return $this->_domain;
    }

    /**
     * Returns the base URL used to access the Google Apps service, based
     * on the current domain. The current domain can be temporarily
     * overridden by providing a fully qualified domain as $domain.
     *
     * @param string $domain (optional) A fully-qualified domain to use
     *          instead of the default domain for this service instance.
     * @throws \Zend\GData\App\InvalidArgumentException
     */
     public function getBaseUrl($domain = null)
     {
         if ($domain !== null) {
             return self::APPS_BASE_FEED_URI . '/' . $domain;
         } else if ($this->_domain !== null) {
             return self::APPS_BASE_FEED_URI . '/' . $this->_domain;
         } else {
             throw new App\InvalidArgumentException(
                     'Domain must be specified.');
         }
     }

    /**
     * Retrieve a UserFeed containing multiple UserEntry objects.
     *
     * @param mixed $location (optional) The location for the feed, as a URL
     *          or Query.
     * @return \Zend\GData\GApps\UserFeed
     * @throws \Zend\GData\App\Exception
     * @throws \Zend\GData\App\HttpException
     * @throws \Zend\GData\GApps\ServiceException
     */
    public function getUserFeed($location = null)
    {
        if ($location === null) {
            $uri = $this->getBaseUrl() . self::APPS_USER_PATH;
        } else if ($location instanceof Query) {
            $uri = $location->getQueryUrl();
        } else {
            $uri = $location;
        }
        return parent::getFeed($uri, 'Zend\GData\GApps\UserFeed');
    }

    /**
     * Retreive NicknameFeed object containing multiple NicknameEntry objects.
     *
     * @param mixed $location (optional) The location for the feed, as a URL
     *          or Query.
     * @return \Zend\GData\GApps\NicknameFeed
     * @throws \Zend\GData\App\Exception
     * @throws \Zend\GData\App\HttpException
     * @throws \Zend\GData\GApps\ServiceException
     */
    public function getNicknameFeed($location = null)
    {
        if ($location === null) {
            $uri = $this->getBaseUrl() . self::APPS_NICKNAME_PATH;
        } else if ($location instanceof Query) {
            $uri = $location->getQueryUrl();
        } else {
            $uri = $location;
        }
        return parent::getFeed($uri, 'Zend\GData\GApps\NicknameFeed');
    }

    /**
     * Retreive GroupFeed object containing multiple GroupEntry
     * objects.
     *
     * @param mixed $location (optional) The location for the feed, as a URL
     *          or Query.
     * @return Zend\GData\GApps\GroupFeed
     * @throws Zend\GData\App\Exception
     * @throws Zend\GData\App\HttpException
     * @throws Zend\GData\GApps\ServiceException
     */
    public function getGroupFeed($location = null)
    {
        if ($location === null) {
            $uri  = self::APPS_BASE_FEED_URI . self::APPS_GROUP_PATH . '/';
            $uri .= $this->getDomain();
        } else if ($location instanceof Query) {
            $uri = $location->getQueryUrl();
        } else {
            $uri = $location;
        }
        return parent::getFeed($uri, 'Zend\GData\GApps\GroupFeed');
    }

    /**
     * Retreive MemberFeed object containing multiple MemberEntry
     * objects.
     *
     * @param mixed $location (optional) The location for the feed, as a URL
     *          or Query.
     * @return Zend\GData\GApps\MemberFeed
     * @throws Zend\GData\App\Exception
     * @throws Zend\GData\App\HttpException
     * @throws Zend\GData\GApps\ServiceException
     */
    public function getMemberFeed($location = null)
    {
        if ($location === null) {
            throw new App\InvalidArgumentException(
                    'Location must not be null');
        } else if ($location instanceof Query) {
            $uri = $location->getQueryUrl();
        } else {
            $uri = $location;
        }
        return parent::getFeed($uri, 'Zend\GData\GApps\MemberFeed');
    }

    /**
     * Retreive OwnerFeed object containing multiple OwnerEntry
     * objects.
     *
     * @param mixed $location (optional) The location for the feed, as a URL
     *          or Query.
     * @return Zend\GData\GApps\OwnerFeed
     * @throws Zend\GData\App\Exception
     * @throws Zend\GData\App\HttpException
     * @throws Zend\GData\GApps\ServiceException
     */
    public function getOwnerFeed($location = null)
    {
        if ($location === null) {
            throw new App\InvalidArgumentException(
                    'Location must not be null');
        } else if ($location instanceof Query) {

            $uri = $location->getQueryUrl();
        } else {
            $uri = $location;
        }
        return parent::getFeed($uri, 'Zend\GData\GApps\OwnerFeed');
    }

    /**
     * Retreive EmailListFeed object containing multiple EmailListEntry
     * objects.
     *
     * @param mixed $location (optional) The location for the feed, as a URL
     *          or Query.
     * @return \Zend\GData\GApps\EmailListFeed
     * @throws \Zend\GData\App\Exception
     * @throws \Zend\GData\App\HttpException
     * @throws \Zend\GData\GApps\ServiceException
     */
    public function getEmailListFeed($location = null)
    {
        if ($location === null) {
            $uri = $this->getBaseUrl() . self::APPS_NICKNAME_PATH;
        } else if ($location instanceof Query) {
            $uri = $location->getQueryUrl();
        } else {
            $uri = $location;
        }
        return parent::getFeed($uri, 'Zend\GData\GApps\EmailListFeed');
    }

    /**
     * Retreive EmailListRecipientFeed object containing multiple
     * EmailListRecipientEntry objects.
     *
     * @param mixed $location The location for the feed, as a URL or Query.
     * @return \Zend\GData\GApps\EmailListRecipientFeed
     * @throws \Zend\GData\App\Exception
     * @throws \Zend\GData\App\HttpException
     * @throws \Zend\GData\GApps\ServiceException
     */
    public function getEmailListRecipientFeed($location)
    {
        if ($location === null) {
            throw new App\InvalidArgumentException(
                    'Location must not be null');
        } else if ($location instanceof Query) {
            $uri = $location->getQueryUrl();
        } else {
            $uri = $location;
        }
        return parent::getFeed($uri, 'Zend\GData\GApps\EmailListRecipientFeed');
    }

    /**
     * Retreive a single UserEntry object.
     *
     * @param mixed $location The location for the feed, as a URL or Query.
     * @return \Zend\GData\GApps\UserEntry
     * @throws \Zend\GData\App\Exception
     * @throws \Zend\GData\App\HttpException
     * @throws \Zend\GData\GApps\ServiceException
     */
    public function getUserEntry($location)
    {
        if ($location === null) {
            throw new App\InvalidArgumentException(
                    'Location must not be null');
        } else if ($location instanceof Query) {
            $uri = $location->getQueryUrl();
        } else {
            $uri = $location;
        }
        return parent::getEntry($uri, 'Zend\GData\GApps\UserEntry');
    }

    /**
     * Retreive a single NicknameEntry object.
     *
     * @param mixed $location The location for the feed, as a URL or Query.
     * @return \Zend\GData\GApps\NicknameEntry
     * @throws \Zend\GData\App\Exception
     * @throws \Zend\GData\App\HttpException
     * @throws \Zend\GData\GApps\ServiceException
     */
    public function getNicknameEntry($location)
    {
        if ($location === null) {
            throw new App\InvalidArgumentException(
                    'Location must not be null');
        } else if ($location instanceof Query) {
            $uri = $location->getQueryUrl();
        } else {
            $uri = $location;
        }
        return parent::getEntry($uri, 'Zend\GData\GApps\NicknameEntry');
    }

    /**
     * Retreive a single GroupEntry object.
     *
     * @param mixed $location The location for the feed, as a URL or Query.
     * @return Zend\GData\GApps\GroupEntry
     * @throws Zend\GData\App\Exception
     * @throws Zend\GData\App\HttpException
     * @throws Zend\GData\GApps\ServiceException
     */
    public function getGroupEntry($location = null)
    {
        if ($location === null) {
            throw new App\InvalidArgumentException(
                    'Location must not be null');
        } else if ($location instanceof Query) {
            $uri = $location->getQueryUrl();
        } else {
            $uri = $location;
        }
        return parent::getEntry($uri, 'Zend\GData\GApps\GroupEntry');
    }

    /**
     * Retreive a single MemberEntry object.
     *
     * @param mixed $location The location for the feed, as a URL or Query.
     * @return Zend\GData\GApps\MemberEntry
     * @throws Zend\GData\App\Exception
     * @throws Zend\GData\App\HttpException
     * @throws Zend\GData\GApps\ServiceException
     */
    public function getMemberEntry($location = null)
    {
        if ($location === null) {
            throw new App\InvalidArgumentException(
                    'Location must not be null');
        } else if ($location instanceof Query) {
            $uri = $location->getQueryUrl();
        } else {
            $uri = $location;
        }
        return parent::getEntry($uri, 'Zend\GData\GApps\MemberEntry');
    }

    /**
     * Retreive a single OwnerEntry object.
     *
     * @param mixed $location The location for the feed, as a URL or Query.
     * @return Zend\GData\GApps\OwnerEntry
     * @throws Zend\GData\App\Exception
     * @throws Zend\GData\App\HttpException
     * @throws Zend\GData\GApps\ServiceException
     */
    public function getOwnerEntry($location = null)
    {
        if ($location === null) {
            throw new App\InvalidArgumentException(
                    'Location must not be null');
        } else if ($location instanceof Query) {
            $uri = $location->getQueryUrl();
        } else {
            $uri = $location;
        }
        return parent::getEntry($uri, 'Zend\GData\GApps\OwnerEntry');
    }

    /**
     * Retreive a single EmailListEntry object.
     *
     * @param mixed $location The location for the feed, as a URL or Query.
     * @return \Zend\GData\GApps\EmailListEntry
     * @throws \Zend\GData\App\Exception
     * @throws \Zend\GData\App\HttpException
     * @throws \Zend\GData\GApps\ServiceException
     */
    public function getEmailListEntry($location)
    {
        if ($location === null) {
            throw new App\InvalidArgumentException(
                    'Location must not be null');
        } else if ($location instanceof Query) {
            $uri = $location->getQueryUrl();
        } else {
            $uri = $location;
        }
        return parent::getEntry($uri, 'Zend\GData\GApps\EmailListEntry');
    }

    /**
     * Retreive a single EmailListRecipientEntry object.
     *
     * @param mixed $location The location for the feed, as a URL or Query.
     * @return \Zend\GData\GApps\EmailListRecipientEntry
     * @throws \Zend\GData\App\Exception
     * @throws \Zend\GData\App\HttpException
     * @throws \Zend\GData\GApps\ServiceException
     */
    public function getEmailListRecipientEntry($location)
    {
        if ($location === null) {
            throw new App\InvalidArgumentException(
                    'Location must not be null');
        } else if ($location instanceof Query) {
            $uri = $location->getQueryUrl();
        } else {
            $uri = $location;
        }
        return parent::getEntry($uri, 'Zend\GData\GApps\EmailListRecipientEntry');
    }

    /**
     * Create a new user from a UserEntry.
     *
     * @param \Zend\GData\GApps\UserEntry $user The user entry to insert.
     * @param string $uri (optional) The URI where the user should be
     *          uploaded to. If null, the default user creation URI for
     *          this domain will be used.
     * @return \Zend\GData\GApps\UserEntry The inserted user entry as
     *          returned by the server.
     * @throws \Zend\GData\App\Exception
     * @throws \Zend\GData\App\HttpException
     * @throws \Zend\GData\GApps\ServiceException
     */
    public function insertUser($user, $uri = null)
    {
        if ($uri === null) {
            $uri = $this->getBaseUrl() . self::APPS_USER_PATH;
        }
        $newEntry = $this->insertEntry($user, $uri, 'Zend\GData\GApps\UserEntry');
        return $newEntry;
    }

    /**
     * Create a new nickname from a NicknameEntry.
     *
     * @param \Zend\GData\GApps\NicknameEntry $nickname The nickname entry to
     *          insert.
     * @param string $uri (optional) The URI where the nickname should be
     *          uploaded to. If null, the default nickname creation URI for
     *          this domain will be used.
     * @return \Zend\GData\GApps\NicknameEntry The inserted nickname entry as
     *          returned by the server.
     * @throws \Zend\GData\App\Exception
     * @throws \Zend\GData\App\HttpException
     * @throws \Zend\GData\GApps\ServiceException
     */
    public function insertNickname($nickname, $uri = null)
    {
        if ($uri === null) {
            $uri = $this->getBaseUrl() . self::APPS_NICKNAME_PATH;
        }
        $newEntry = $this->insertEntry($nickname, $uri, 'Zend\GData\GApps\NicknameEntry');
        return $newEntry;
    }

    /**
     * Create a new group from a GroupEntry.
     *
     * @param Zend\GData\GApps\GroupEntry $group The group entry to insert.
     * @param string $uri (optional) The URI where the group should be
     *          uploaded to. If null, the default user creation URI for
     *          this domain will be used.
     * @return Zend\GData\GApps\GroupEntry The inserted group entry as
     *          returned by the server.
     * @throws Zend\GData\App\Exception
     * @throws Zend\GData\App\HttpException
     * @throws Zend\GData\GApps\ServiceException
     */
    public function insertGroup($group, $uri = null)
    {
        if ($uri === null) {
            $uri  = self::APPS_BASE_FEED_URI . self::APPS_GROUP_PATH . '/';
            $uri .= $this->getDomain();
        }
        $newEntry = $this->insertEntry($group, $uri, 'Zend\GData\GApps\GroupEntry');
        return $newEntry;
    }

    /**
     * Create a new member from a MemberEntry.
     *
     * @param Zend\GData\GApps\MemberEntry $member The member entry to insert.
     * @param string $uri (optional) The URI where the group should be
     *          uploaded to. If null, the default user creation URI for
     *          this domain will be used.
     * @return Zend\GData\GApps\MemberEntry The inserted member entry as
     *          returned by the server.
     * @throws Zend\GData\App\Exception
     * @throws Zend\GData\App\HttpException
     * @throws Zend\GData\GApps\ServiceException
     */
    public function insertMember($member, $uri = null)
    {
        if ($uri === null) {
            throw new App\InvalidArgumentException(
                    'URI must not be null');
        }
        $newEntry = $this->insertEntry($member, $uri, 'Zend\GData\GApps\MemberEntry');
        return $newEntry;
    }

    /**
     * Create a new group from a OwnerEntry.
     *
     * @param Zend\GData\GApps\OwnerEntry $owner The owner entry to insert.
     * @param string $uri (optional) The URI where the owner should be
     *          uploaded to. If null, the default user creation URI for
     *          this domain will be used.
     * @return Zend\GData\GApps\OwnerEntry The inserted owner entry as
     *          returned by the server.
     * @throws Zend\GData\App\Exception
     * @throws Zend\GData\App\HttpException
     * @throws Zend\GData\GApps\ServiceException
     */
    public function insertOwner($owner, $uri = null)
    {
        if ($uri === null) {
            throw new App\InvalidArgumentException(
                    'URI must not be null');
        }
        $newEntry = $this->insertEntry($owner, $uri, 'Zend\GData\GApps\OwnerEntry');
        return $newEntry;
    }

    /**
     * Create a new email list from an EmailListEntry.
     *
     * @param \Zend\GData\GApps\EmailListEntry $emailList The email list entry
     *          to insert.
     * @param string $uri (optional) The URI where the email list should be
     *          uploaded to. If null, the default email list creation URI for
     *          this domain will be used.
     * @return \Zend\GData\GApps\EmailListEntry The inserted email list entry
     *          as returned by the server.
     * @throws \Zend\GData\App\Exception
     * @throws \Zend\GData\App\HttpException
     * @throws \Zend\GData\GApps\ServiceException
     */
    public function insertEmailList($emailList, $uri = null)
    {
        if ($uri === null) {
            $uri = $this->getBaseUrl() . self::APPS_EMAIL_LIST_PATH;
        }
        $newEntry = $this->insertEntry($emailList, $uri, 'Zend\GData\GApps\EmailListEntry');
        return $newEntry;
    }

    /**
     * Create a new email list recipient from an EmailListRecipientEntry.
     *
     * @param \Zend\GData\GApps\EmailListRecipientEntry $recipient The recipient
     *          entry to insert.
     * @param string $uri (optional) The URI where the recipient should be
     *          uploaded to. If null, the default recipient creation URI for
     *          this domain will be used.
     * @return \Zend\GData\GApps\EmailListRecipientEntry The inserted
     *          recipient entry as returned by the server.
     * @throws \Zend\GData\App\Exception
     * @throws \Zend\GData\App\HttpException
     * @throws \Zend\GData\GApps\ServiceException
     */
    public function insertEmailListRecipient($recipient, $uri = null)
    {
        if ($uri === null) {
            throw new App\InvalidArgumentException(
                    'URI must not be null');
        } elseif ($uri instanceof EmailListEntry) {
            $uri = $uri->getLink('edit')->href;
        }
        $newEntry = $this->insertEntry($recipient, $uri, 'Zend\GData\GApps\EmailListRecipientEntry');
        return $newEntry;
    }

    /**
     * Provides a magic factory method to instantiate new objects with
     * shorter syntax than would otherwise be required by the Zend Framework
     * naming conventions. For more information, see \Zend\Gdata\App::__call().
     *
     * This overrides the default behavior of __call() so that query classes
     * do not need to have their domain manually set when created with
     * a magic factory method.
     *
     * @see \Zend\Gdata\App::__call()
     * @param string $method The method name being called
     * @param array $args The arguments passed to the call
     * @throws \Zend\GData\App\Exception
     */
    public function __call($method, $args) {
        if (preg_match('/^new(\w+Query)/', $method, $matches)) {
            $class = $matches[1];
            $foundClassName = null;
            foreach ($this->_registeredPackages as $name) {
                 try {
                     // Autoloading disabled on next line for compatibility
                     // with magic factories. See ZF-6660.
                     if (class_exists($name . '\\' . $class)) {
                        $foundClassName = $name . '\\' . $class;
                     }
                     break;
                 } catch (\Exception $e) {
                     // package wasn't here- continue searching
                 }
            }
            if ($foundClassName != null) {
                $reflectionObj = new \ReflectionClass($foundClassName);
                // Prepend the domain to the query
                $args = array_merge(array($this->getDomain()), $args);
                return $reflectionObj->newInstanceArgs($args);
            } else {
                throw new App\Exception(
                        "Unable to find '${class}' in registered packages");
            }
        } else {
            return parent::__call($method, $args);
        }

    }

    // Convenience methods
    // Specified at http://code.google.com/apis/apps/gdata_provisioning_api_v2.0_reference.html#appendix_e

    /**
     * Create a new user entry and send it to the Google Apps servers.
     *
     * @param string $username The username for the new user.
     * @param string $givenName The given name for the new user.
     * @param string $familyName The family name for the new user.
     * @param string $password The password for the new user as a plaintext string
     *                 (if $passwordHashFunction is null) or a SHA-1 hashed
     *                 value (if $passwordHashFunction = 'SHA-1').
     * @param string $quotaLimitInMB (optional) The quota limit for the new user in MB.
     * @return \Zend\GData\GApps\UserEntry (optional) The new user entry as returned by
     *                 server.
     * @throws \Zend\GData\App\Exception
     * @throws \Zend\GData\App\HttpException
     * @throws \Zend\GData\GApps\ServiceException
     */
    public function createUser ($username, $givenName, $familyName, $password,
            $passwordHashFunction = null, $quotaLimitInMB = null) {
        $user = $this->newUserEntry();
        $user->login = $this->newLogin();
        $user->login->username = $username;
        $user->login->password = $password;
        $user->login->hashFunctionName = $passwordHashFunction;
        $user->name = $this->newName();
        $user->name->givenName = $givenName;
        $user->name->familyName = $familyName;
        if ($quotaLimitInMB !== null) {
            $user->quota = $this->newQuota();
            $user->quota->limit = $quotaLimitInMB;
        }
        return $this->insertUser($user);
    }

    /**
     * Retrieve a user based on their username.
     *
     * @param string $username The username to search for.
     * @return \Zend\GData\GApps\UserEntry The username to search for, or null
     *              if no match found.
     * @throws \Zend\GData\App\InvalidArgumentException
     * @throws \Zend\GData\App\HttpException
     */
    public function retrieveUser ($username)
    {
        $query = $this->newUserQuery($username);
        try {
            $user = $this->getUserEntry($query);
        } catch (GApps\ServiceException $e) {
            // Set the user to null if not found
            if ($e->hasError(GApps\Error::ENTITY_DOES_NOT_EXIST)) {
                $user = null;
            } else {
                throw $e;
            }
        }
        return $user;
    }

    /**
     * Retrieve a page of users in alphabetical order, starting with the
     * provided username.
     *
     * @param string $startUsername (optional) The first username to retrieve.
     *          If null or not declared, the page will begin with the first
     *          user in the domain.
     * @return Zend_Gdata_GApps_UserFeed Collection of Zend_Gdata_UserEntry
     *              objects representing all users in the domain.
     * @throws \Zend\GData\App\Exception
     * @throws \Zend\GData\App\HttpException
     * @throws \Zend\GData\GApps\ServiceException
     */
    public function retrievePageOfUsers ($startUsername = null) {
        $query = $this->newUserQuery();
        $query->setStartUsername($startUsername);
        return $this->getUserFeed($query);
    }

    /**
     * Retrieve all users in the current domain. Be aware that
     * calling this function on a domain with many users will take a
     * signifigant amount of time to complete. On larger domains this may
     * may cause execution to timeout without proper precautions in place.
     *
     * @return Zend_Gdata_GApps_UserFeed Collection of Zend_Gdata_UserEntry
     *              objects representing all users in the domain.
     * @throws \Zend\GData\App\Exception
     * @throws \Zend\GData\App\HttpException
     * @throws \Zend\GData\GApps\ServiceException
     */
    public function retrieveAllUsers () {
        return $this->retrieveAllEntriesForFeed($this->retrievePageOfUsers());
    }

    /**
     * Overwrite a specified username with the provided UserEntry.  The
     * UserEntry does not need to contain an edit link.
     *
     * This method is provided for compliance with the Google Apps
     * Provisioning API specification. Normally users will instead want to
     * call UserEntry::save() instead.
     *
     * @see Zend_Gdata_App_Entry::save
     * @param string $username The username whose data will be overwritten.
     * @param \Zend\GData\GApps\UserEntry $userEntry The user entry which
     *          will be overwritten.
     * @return \Zend\GData\GApps\UserEntry The UserEntry returned by the
     *          server.
     * @throws \Zend\GData\App\Exception
     * @throws \Zend\GData\App\HttpException
     * @throws \Zend\GData\GApps\ServiceException
     */
    public function updateUser($username, $userEntry) {
        return $this->updateEntry($userEntry, $this->getBaseUrl() .
            self::APPS_USER_PATH . '/' . $username);
    }

    /**
     * Mark a given user as suspended.
     *
     * @param string $username The username associated with the user who
     *          should be suspended.
     * @return \Zend\GData\GApps\UserEntry The UserEntry for the modified
     *          user.
     * @throws \Zend\GData\App\Exception
     * @throws \Zend\GData\App\HttpException
     * @throws \Zend\GData\GApps\ServiceException
     */
    public function suspendUser($username) {
        $user = $this->retrieveUser($username);
        $user->login->suspended = true;
        return $user->save();
    }

    /**
     * Mark a given user as not suspended.
     *
     * @param string $username The username associated with the user who
     *          should be restored.
     * @return \Zend\GData\GApps\UserEntry The UserEntry for the modified
     *          user.
     * @throws \Zend\GData\App\Exception
     * @throws \Zend\GData\App\HttpException
     * @throws \Zend\GData\GApps\ServiceException
     */
    public function restoreUser($username) {
        $user = $this->retrieveUser($username);
        $user->login->suspended = false;
        return $user->save();
    }

    /**
     * Delete a user by username.
     *
     * @param string $username The username associated with the user who
     *          should be deleted.
     * @throws \Zend\GData\App\Exception
     * @throws \Zend\GData\App\HttpException
     * @throws \Zend\GData\GApps\ServiceException
     */
    public function deleteUser($username) {
        $this->delete($this->getBaseUrl() . self::APPS_USER_PATH . '/' .
            $username);
    }

    /**
     * Create a nickname for a given user.
     *
     * @param string $username The username to which the new nickname should
     *          be associated.
     * @param string $nickname The new nickname to be created.
     * @return \Zend\GData\GApps\NicknameEntry The nickname entry which was
     *          created by the server.
     * @throws \Zend\GData\App\Exception
     * @throws \Zend\GData\App\HttpException
     * @throws \Zend\GData\GApps\ServiceException
     */
    public function createNickname($username, $nickname) {
        $entry = $this->newNicknameEntry();
        $nickname = $this->newNickname($nickname);
        $login = $this->newLogin($username);
        $entry->nickname = $nickname;
        $entry->login = $login;
        return $this->insertNickname($entry);
    }

    /**
     * Retrieve the entry for a specified nickname.
     *
     * @param string $nickname The nickname to be retrieved.
     * @return \Zend\GData\GApps\NicknameEntry The requested nickname entry.
     * @throws \Zend\GData\App\Exception
     * @throws \Zend\GData\App\HttpException
     * @throws \Zend\GData\GApps\ServiceException
     */
    public function retrieveNickname($nickname) {
        $query = $this->newNicknameQuery();
        $query->setNickname($nickname);
        try {
            $nickname = $this->getNicknameEntry($query);
        } catch (GApps\ServiceException $e) {
            // Set the nickname to null if not found
            if ($e->hasError(GApps\Error::ENTITY_DOES_NOT_EXIST)) {
                $nickname = null;
            } else {
                throw $e;
            }
        }
        return $nickname;
    }

    /**
     * Retrieve all nicknames associated with a specific username.
     *
     * @param string $username The username whose nicknames should be
     *          returned.
     * @return \Zend\GData\GApps\NicknameFeed A feed containing all nicknames
     *          for the given user, or null if
     * @throws \Zend\GData\App\Exception
     * @throws \Zend\GData\App\HttpException
     * @throws \Zend\GData\GApps\ServiceException
     */
    public function retrieveNicknames($username) {
        $query = $this->newNicknameQuery();
        $query->setUsername($username);
        $nicknameFeed = $this->retrieveAllEntriesForFeed(
            $this->getNicknameFeed($query));
        return $nicknameFeed;
    }

    /**
     * Retrieve a page of nicknames in alphabetical order, starting with the
     * provided nickname.
     *
     * @param string $startNickname (optional) The first nickname to
     *          retrieve. If null or not declared, the page will begin with
     *          the first nickname in the domain.
     * @return Zend_Gdata_GApps_NicknameFeed Collection of Zend_Gdata_NicknameEntry
     *              objects representing all nicknames in the domain.
     * @throws \Zend\GData\App\Exception
     * @throws \Zend\GData\App\HttpException
     * @throws \Zend\GData\GApps\ServiceException
     */
    public function retrievePageOfNicknames ($startNickname = null) {
        $query = $this->newNicknameQuery();
        $query->setStartNickname($startNickname);
        return $this->getNicknameFeed($query);
    }

    /**
     * Retrieve all nicknames in the current domain. Be aware that
     * calling this function on a domain with many nicknames will take a
     * signifigant amount of time to complete. On larger domains this may
     * may cause execution to timeout without proper precautions in place.
     *
     * @return Zend_Gdata_GApps_NicknameFeed Collection of Zend_Gdata_NicknameEntry
     *              objects representing all nicknames in the domain.
     * @throws \Zend\GData\App\Exception
     * @throws \Zend\GData\App\HttpException
     * @throws \Zend\GData\GApps\ServiceException
     */
    public function retrieveAllNicknames () {
        return $this->retrieveAllEntriesForFeed($this->retrievePageOfNicknames());
    }

    /**
     * Delete a specified nickname.
     *
     * @param string $nickname The name of the nickname to be deleted.
     * @throws \Zend\GData\App\Exception
     * @throws \Zend\GData\App\HttpException
     * @throws \Zend\GData\GApps\ServiceException
     */
    public function deleteNickname($nickname) {
        $this->delete($this->getBaseUrl() . self::APPS_NICKNAME_PATH . '/' . $nickname);
    }

    /**
     * Create a new group.
     *
     * @param string $groupId A unique identifier for the group
     * @param string $groupName The name of the group
     * @param string $description A description of the group
     * @param string $emailPermission The subscription permission of the group
     * @return Zend\GData\GApps\GroupEntry The group entry as created on the server.
     */
    public function createGroup($groupId, $groupName, $description = null, $emailPermission = null)
    {
        $i = 0;
        $group = $this->newGroupEntry();

        $properties[$i] = $this->newProperty();
        $properties[$i]->name = 'groupId';
        $properties[$i]->value = $groupId;
        $i++;
        $properties[$i] = $this->newProperty();
        $properties[$i]->name = 'groupName';
        $properties[$i]->value = $groupName;
        $i++;

        if($description != null) {
            $properties[$i] = $this->newProperty();
            $properties[$i]->name = 'description';
            $properties[$i]->value = $description;
            $i++;
        }

        if($emailPermission != null) {
            $properties[$i] = $this->newProperty();
            $properties[$i]->name = 'emailPermission';
            $properties[$i]->value = $emailPermission;
            $i++;
        }

        $group->property = $properties;

        return $this->insertGroup($group);
    }

    /**
     * Retrieves a group based on group id
     *
     * @param string $groupId The unique identifier for the group
     * @return Zend\GData\GApps\GroupEntry The group entry as returned by the server.
     */
    public function retrieveGroup($groupId)
    {
        $query = $this->newGroupQuery($groupId);
        //$query->setGroupId($groupId);

        try {
            $group = $this->getGroupEntry($query);
        } catch (GApps\ServiceException $e) {
            // Set the group to null if not found
            if ($e->hasError(GApps\Error::ENTITY_DOES_NOT_EXIST)) {
                $group = null;
            } else {
                throw $e;
            }
        }
        return $group;
    }

    /**
     * Retrieve all groups in the current domain. Be aware that
     * calling this function on a domain with many groups will take a
     * signifigant amount of time to complete. On larger domains this may
     * may cause execution to timeout without proper precautions in place.
     *
     * @return Zend\GData\GApps\GroupFeed Collection of Zend\GData\GroupEntry objects
     *              representing all groups apart of the domain.
     */
    public function retrieveAllGroups()
    {
        return $this->retrieveAllEntriesForFeed($this->retrievePageOfGroups());
    }

    /**
     * Delete a group
     *
     * @param string $groupId The unique identifier for the group
     */
    public function deleteGroup($groupId)
    {
        $uri  = self::APPS_BASE_FEED_URI . self::APPS_GROUP_PATH . '/';
        $uri .= $this->getDomain() . '/' . $groupId;

        $this->delete($uri);
    }

    /**
     * Check to see if a member id or group id is a member of group
     *
     * @param string $memberId Member id or group group id
     * @param string $groupId Group to be checked for
     * @return bool True, if given entity is a member
     */
    public function isMember($memberId, $groupId)
    {
        $uri  = self::APPS_BASE_FEED_URI . self::APPS_GROUP_PATH . '/';
        $uri .= $this->getDomain() . '/' . $groupId . '/member/' . $memberId;

        //if the enitiy is not a member, an exception is thrown
        try {
            $results = $this->get($uri);
        } catch (Exception $e) {
            $results = false;
        }

        if($results) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Add an email address to a group as a member
     *
     * @param string $recipientAddress Email address, member id, or group id
     * @param string $groupId The unique id of the group
     * @return Zend\GData\GApps\MemberEntry The member entry returned by the server
     */
    public function addMemberToGroup($recipientAddress, $groupId)
    {
        $member = $this->newMemberEntry();

        $properties[] = $this->newProperty();
        $properties[0]->name = 'memberId';
        $properties[0]->value = $recipientAddress;

        $member->property = $properties;

        $uri  = self::APPS_BASE_FEED_URI . self::APPS_GROUP_PATH . '/';
        $uri .= $this->getDomain() . '/' . $groupId . '/member';

        return $this->insertMember($member, $uri);
    }

    /**
     * Remove a member id from a group
     *
     * @param string $memberId Member id or group id
     * @param string $groupId The unique id of the group
     */
    public function removeMemberFromGroup($memberId, $groupId)
    {
        $uri  = self::APPS_BASE_FEED_URI . self::APPS_GROUP_PATH . '/';
        $uri .= $this->getDomain() . '/' . $groupId . '/member/' . $memberId;

        return $this->delete($uri);
    }

    /**
     * Retrieves all the members of a group
     *
     * @param string $groupId The unique id of the group
     * @return Zend\GData\GApps\MemberFeed Collection of MemberEntry objects
     *              representing all members apart of the group.
     */
    public function retrieveAllMembers($groupId)
    {
        return $this->retrieveAllEntriesForFeed(
                $this->retrievePageOfMembers($groupId));
    }

    /**
     * Add an email as an owner of a group
     *
     * @param string $email Owner's email
     * @param string $groupId Group ownership to be checked for
     * @return Zend\GData\GApps\OwnerEntry The OwnerEntry returned by the server
     */
    public function addOwnerToGroup($email, $groupId)
    {
        $owner = $this->newOwnerEntry();

        $properties[] = $this->newProperty();
        $properties[0]->name = 'email';
        $properties[0]->value = $email;

        $owner->property = $properties;

        $uri  = self::APPS_BASE_FEED_URI . self::APPS_GROUP_PATH . '/';
        $uri .= $this->getDomain() . '/' . $groupId . '/owner';

        return $this->insertOwner($owner, $uri);
    }

    /**
     * Retrieves all the owners of a group
     *
     * @param string $groupId The unique identifier for the group
     * @return Zend\GData\GApps\OwnerFeed Collection of Zend\GData\OwnerEntry
     *              objects representing all owners apart of the group.
     */
    public function retrieveGroupOwners($groupId)
    {
        $uri  = self::APPS_BASE_FEED_URI . self::APPS_GROUP_PATH . '/';
        $uri .= $this->getDomain() . '/' . $groupId . '/owner';

        return $this->getOwnerFeed($uri);
    }

    /**
     * Checks to see if an email is an owner of a group
     *
     * @param string $email Owner's email
     * @param string $groupId Group ownership to be checked for
     * @return bool True, if given entity is an owner
     */
    public function isOwner($email, $groupId)
    {
        $uri  = self::APPS_BASE_FEED_URI . self::APPS_GROUP_PATH . '/';
        $uri .= $this->getDomain() . '/' . $groupId . '/owner/' . $email;

        //if the enitiy is not an owner of the group, an exception is thrown
        try {
            $results = $this->get($uri);
        } catch (Exception $e) {
            $results = false;
        }

        if($results) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * Remove email as an owner of a group
     *
     * @param string $email Owner's email
     * @param string $groupId The unique identifier for the group
     */
    public function removeOwnerFromGroup($email, $groupId)
    {
        $uri  = self::APPS_BASE_FEED_URI . self::APPS_GROUP_PATH . '/';
        $uri .= $this->getDomain() . '/' . $groupId . '/owner/' . $email;

        return $this->delete($uri);
    }

    /**
     * Update group properties with new values. any property not defined will not
     * be updated
     *
     * @param string $groupId A unique identifier for the group
     * @param string $groupName The name of the group
     * @param string $description A description of the group
     * @param string $emailPermission The subscription permission of the group
     * @return Zend\GData\GApps\GroupEntry The group entry as updated on the server.
     */
    public function updateGroup($groupId, $groupName = null, $description = null,
            $emailPermission = null)
    {
        $i = 0;
        $group = $this->newGroupEntry();

        $properties[$i] = $this->newProperty();
        $properties[$i]->name = 'groupId';
        $properties[$i]->value = $groupId;
        $i++;

        if($groupName != null) {
            $properties[$i] = $this->newProperty();
            $properties[$i]->name = 'groupName';
            $properties[$i]->value = $groupName;
            $i++;
        }

        if($description != null) {
            $properties[$i] = $this->newProperty();
            $properties[$i]->name = 'description';
            $properties[$i]->value = $description;
            $i++;
        }

        if($emailPermission != null) {
            $properties[$i] = $this->newProperty();
            $properties[$i]->name = 'emailPermission';
            $properties[$i]->value = $emailPermission;
            $i++;
        }

        $group->property = $properties;

        $uri  = self::APPS_BASE_FEED_URI . self::APPS_GROUP_PATH . '/';
        $uri .= $this->getDomain() . '/' . $groupId;

        return $this->updateEntry($group, $uri, 'Zend\GData\GApps\GroupEntry');
    }

    /**
     * Retrieve all of the groups that a user is a member of
     *
     * @param string $memberId Member username
     * @param bool $directOnly (Optional) If true, members with direct association
     *             only will be considered
     * @return Zend\GData\GApps\GroupFeed Collection of Zend\GData\GroupEntry
     *              objects representing all groups member is apart of in the domain.
     */
    public function retrieveGroups($memberId, $directOnly = null)
    {
        $query = $this->newGroupQuery();
        $query->setMember($memberId);
        if($directOnly != null) {
            $query->setDirectOnly($directOnly);
        }
        return $this->getGroupFeed($query);
    }

    /**
     * Retrieve a page of groups in alphabetical order, starting with the
     * provided group.
     *
     * @param string $startGroup (optional) The first group to
     *              retrieve. If null or not defined, the page will begin
     *              with the first group in the domain.
     * @return Zend\GData\GApps\GroupFeed Collection of Zend\GData\GroupEntry
     *              objects representing the groups in the domain.
     * @throws Zend\GData\App\Exception
     * @throws Zend\GData\App\HttpException
     * @throws Zend\GData\GApps\ServiceException
     */
    public function retrievePageOfGroups ($startGroup = null)
    {
        $query = $this->newGroupQuery();
        $query->setStartGroupId($startGroup);
        return $this->getGroupFeed($query);
    }

    /**
     * Gets page of Members
     *
     * @param string $groupId The group id which should be searched.
     * @param string $startMember (optinal) The address of the first member,
     *              or null to start with the first member in the list.
     * @return Zend\GData\GApps\MemberFeed Collection of Zend\GData\MemberEntry
     *              objects
     */
    public function retrievePageOfMembers($groupId, $startMember = null)
    {
        $query = $this->newMemberQuery($groupId);
        $query->setStartMemberId($startMember);
        return $this->getMemberFeed($query);
    }

    /**
     * Create a new email list.
     *
     * @param string $emailList The name of the email list to be created.
     * @return \Zend\GData\GApps\EmailListEntry The email list entry
     *          as created on the server.
     * @throws \Zend\GData\App\Exception
     * @throws \Zend\GData\App\HttpException
     * @throws \Zend\GData\GApps\ServiceException
     */
    public function createEmailList($emailList) {
        $entry = $this->newEmailListEntry();
        $list = $this->newEmailList();
        $list->name = $emailList;
        $entry->emailList = $list;
        return $this->insertEmailList($entry);
    }

    /**
     * Retrieve all email lists associated with a recipient.
     *
     * @param string $username The recipient whose associated email lists
     *          should be returned.
     * @return \Zend\GData\GApps\EmailListFeed The list of email lists found as
     *          Zend_Gdata_EmailListEntry objects.
     * @throws \Zend\GData\App\Exception
     * @throws \Zend\GData\App\HttpException
     * @throws \Zend\GData\GApps\ServiceException
     */
    public function retrieveEmailLists($recipient) {
        $query = $this->newEmailListQuery();
        $query->recipient = $recipient;
        return $this->getEmailListFeed($query);
    }

    /**
     * Retrieve a page of email lists in alphabetical order, starting with the
     * provided email list.
     *
     * @param string $startEmailListName (optional) The first list to
     *              retrieve. If null or not defined, the page will begin
     *              with the first email list in the domain.
     * @return Zend_Gdata_GApps_EmailListFeed Collection of Zend_Gdata_EmailListEntry
     *              objects representing all nicknames in the domain.
     * @throws \Zend\GData\App\Exception
     * @throws \Zend\GData\App\HttpException
     * @throws \Zend\GData\GApps\ServiceException
     */
    public function retrievePageOfEmailLists ($startNickname = null) {
        $query = $this->newEmailListQuery();
        $query->setStartEmailListName($startNickname);
        return $this->getEmailListFeed($query);
    }

    /**
     * Retrieve all email lists associated with the curent domain. Be aware that
     * calling this function on a domain with many email lists will take a
     * signifigant amount of time to complete. On larger domains this may
     * may cause execution to timeout without proper precautions in place.
     *
     * @return \Zend\GData\GApps\EmailListFeed The list of email lists found
     *              as Zend_Gdata_GApps_EmailListEntry objects.
     * @throws \Zend\GData\App\Exception
     * @throws \Zend\GData\App\HttpException
     * @throws \Zend\GData\GApps\ServiceException
     */
    public function retrieveAllEmailLists() {
        return $this->retrieveAllEntriesForFeed($this->retrievePageOfEmailLists());
    }

    /**
     * Delete a specified email list.
     *
     * @param string $emailList The name of the emailList to be deleted.
     * @throws \Zend\GData\App\Exception
     * @throws \Zend\GData\App\HttpException
     * @throws \Zend\GData\GApps\ServiceException
     */
    public function deleteEmailList($emailList) {
        $this->delete($this->getBaseUrl() . self::APPS_EMAIL_LIST_PATH . '/'
            . $emailList);
    }

    /**
     * Add a specified recipient to an existing emailList.
     *
     * @param string $recipientAddress The address of the recipient to be
     *              added to the email list.
     * @param string $emailList The name of the email address to which the
     *              recipient should be added.
     * @return \Zend\GData\GApps\EmailListRecipientEntry The recipient entry
     *              created by the server.
     * @throws \Zend\GData\App\Exception
     * @throws \Zend\GData\App\HttpException
     * @throws \Zend\GData\GApps\ServiceException
     */
    public function addRecipientToEmailList($recipientAddress, $emailList) {
        $entry = $this->newEmailListRecipientEntry();
        $who = $this->newWho();
        $who->email = $recipientAddress;
        $entry->who = $who;
        $address = $this->getBaseUrl() .  self::APPS_EMAIL_LIST_PATH . '/' .
            $emailList . self::APPS_EMAIL_LIST_RECIPIENT_POSTFIX . '/';
        return $this->insertEmailListRecipient($entry, $address);
    }

    /**
     * Retrieve a page of email list recipients in alphabetical order,
     * starting with the provided email list recipient.
     *
     * @param string $emaiList The email list which should be searched.
     * @param string $startRecipient (optinal) The address of the first
     *              recipient, or null to start with the first recipient in
     *              the list.
     * @return \Zend\GData\GApps\EmailListRecipientFeed Collection of
     *              Zend_Gdata_EmailListRecipientEntry objects representing all
     *              recpients in the specified list.
     * @throws \Zend\GData\App\Exception
     * @throws \Zend\GData\App\HttpException
     * @throws \Zend\GData\GApps\ServiceException
     */
    public function retrievePageOfRecipients ($emailList,
            $startRecipient = null) {
        $query = $this->newEmailListRecipientQuery();
        $query->setEmailListName($emailList);
        $query->setStartRecipient($startRecipient);
        return $this->getEmailListRecipientFeed($query);
    }

    /**
     * Retrieve all recipients associated with an email list. Be aware that
     * calling this function on a domain with many email lists will take a
     * signifigant amount of time to complete. On larger domains this may
     * may cause execution to timeout without proper precautions in place.
     *
     * @param string $emaiList The email list which should be searched.
     * @return \Zend\GData\GApps\EmailListRecipientFeed The list of email lists
     *              found as Zend_Gdata_GApps_EmailListRecipientEntry objects.
     * @throws \Zend\GData\App\Exception
     * @throws \Zend\GData\App\HttpException
     * @throws \Zend\GData\GApps\ServiceException
     */
    public function retrieveAllRecipients($emailList) {
        return $this->retrieveAllEntriesForFeed(
                $this->retrievePageOfRecipients($emailList));
    }

    /**
     * Remove a specified recipient from an email list.
     *
     * @param string $recipientAddress The recipient to be removed.
     * @param string $emailList The list from which the recipient should
     *              be removed.
     * @throws \Zend\GData\App\Exception
     * @throws \Zend\GData\App\HttpException
     * @throws \Zend\GData\GApps\ServiceException
     */
    public function removeRecipientFromEmailList($recipientAddress, $emailList) {
        $this->delete($this->getBaseUrl() . self::APPS_EMAIL_LIST_PATH . '/'
            . $emailList . self::APPS_EMAIL_LIST_RECIPIENT_POSTFIX . '/'
            . $recipientAddress);
    }

}
