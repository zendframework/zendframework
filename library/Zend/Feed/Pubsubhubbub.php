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
 * @package    Zend_Feed_Pubsubhubbub
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: Pubsubhubbub.php 20785 2010-01-31 09:43:03Z mikaelkael $
 */

/**
 * @see Zend_Http_Client
 */
<<<<<<< HEAD:library/Zend/Feed/Pubsubhubbub.php
require_once 'Zend/Http/Client.php';

/**
 * @see Zend_Uri
 */
require_once 'Zend/Uri.php';

/**
 * @see Zend_Version
 */
require_once 'Zend/Version.php';

/**
 * @see Zend_Feed_Reader
 */
require_once 'Zend/Feed/Reader.php';

/**
 * @see Zend_Feed_Abstract
 */
require_once 'Zend/Feed/Abstract.php';

/**
=======
namespace Zend\Feed\PubSubHubbub;
use Zend\Feed\Reader;
use Zend\Http;

/**
 * @uses       \Zend\Http\Client
 * @uses       \Zend\Feed\AbstractFeed
 * @uses       \Zend\Feed\PubSubHubbub\Exception
 * @uses       \Zend\Feed\Reader\Reader
 * @uses       \Zend\Uri\Uri
 * @uses       \Zend\Version
>>>>>>> master:library/Zend/Feed/PubSubHubbub/PubSubHubbub.php
 * @category   Zend
 * @package    Zend_Feed_Pubsubhubbub
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Feed_Pubsubhubbub
{
    /**
     * Verification Modes
     */
    const VERIFICATION_MODE_SYNC  = 'sync';
    const VERIFICATION_MODE_ASYNC = 'async';
    
    /**
     * Subscription States
     */
    const SUBSCRIPTION_VERIFIED    = 'verified';
    const SUBSCRIPTION_NOTVERIFIED = 'not_verified';
    const SUBSCRIPTION_TODELETE    = 'to_delete';

    /**
     * Singleton instance if required of the HTTP client
     *
<<<<<<< HEAD:library/Zend/Feed/Pubsubhubbub.php
     * @var Zend_Http_Client
=======
     * @var \Zend\Http\Client
>>>>>>> master:library/Zend/Feed/PubSubHubbub/PubSubHubbub.php
     */
    protected static $httpClient = null;

    /**
     * Simple utility function which imports any feed URL and
     * determines the existence of Hub Server endpoints. This works
     * best if directly given an instance of Zend_Feed_Reader_Atom|Rss
     * to leverage off.
     *
     * @param  Zend_Feed_Reader_FeedAbstract|Zend_Feed_Abstract|string $source
     * @return array
     */
    public static function detectHubs($source)
    {
        if (is_string($source)) {
<<<<<<< HEAD:library/Zend/Feed/Pubsubhubbub.php
            $feed = Zend_Feed_Reader::import($source);
        } elseif (is_object($source) && $source instanceof Zend_Feed_Reader_FeedAbstract) {
=======
            $feed = Reader\Reader::import($source);
        } elseif (is_object($source) && $source instanceof Reader\AbstractFeed) {
>>>>>>> master:library/Zend/Feed/PubSubHubbub/PubSubHubbub.php
            $feed = $source;
        } elseif (is_object($source) && $source instanceof Zend_Feed_Abstract) {
            $feed = Zend_Feed_Reader::importFeed($source);
        } else {
<<<<<<< HEAD:library/Zend/Feed/Pubsubhubbub.php
            require_once 'Zend/Feed/Pubsubhubbub/Exception.php';
            throw new Zend_Feed_Pubsubhubbub_Exception('The source parameter was'
=======
            throw new Exception('The source parameter was'
>>>>>>> master:library/Zend/Feed/PubSubHubbub/PubSubHubbub.php
            . ' invalid, i.e. not a URL string or an instance of type'
            . ' Zend\Feed\Reader\FeedAbstract or Zend\Feed\Abstract');
        }
        return $feed->getHubs();
    }

    /**
     * Allows the external environment to make Zend_Oauth use a specific
     * Client instance.
     *
<<<<<<< HEAD:library/Zend/Feed/Pubsubhubbub.php
     * @param  Zend_Http_Client $httpClient
     * @return void
     */
    public static function setHttpClient(Zend_Http_Client $httpClient)
=======
     * @param  \Zend\Http\Client $httpClient
     * @return void
     */
    public static function setHttpClient(Http\Client $httpClient)
>>>>>>> master:library/Zend/Feed/PubSubHubbub/PubSubHubbub.php
    {
        self::$httpClient = $httpClient;
    }

    /**
     * Return the singleton instance of the HTTP Client. Note that
     * the instance is reset and cleared of previous parameters GET/POST.
     * Headers are NOT reset but handled by this component if applicable.
     *
<<<<<<< HEAD:library/Zend/Feed/Pubsubhubbub.php
     * @return Zend_Http_Client
=======
     * @return \Zend\Http\Client
>>>>>>> master:library/Zend/Feed/PubSubHubbub/PubSubHubbub.php
     */
    public static function getHttpClient()
    {
        if (!isset(self::$httpClient)):
<<<<<<< HEAD:library/Zend/Feed/Pubsubhubbub.php
            self::$httpClient = new Zend_Http_Client;
=======
            self::$httpClient = new Http\Client;
>>>>>>> master:library/Zend/Feed/PubSubHubbub/PubSubHubbub.php
        else:
            self::$httpClient->resetParameters();
        endif;
        return self::$httpClient;
    }

    /**
     * Simple mechanism to delete the entire singleton HTTP Client instance
     * which forces an new instantiation for subsequent requests.
     *
     * @return void
     */
    public static function clearHttpClient()
    {
        self::$httpClient = null;
    }

    /**
     * RFC 3986 safe url encoding method
     *
     * @param  string $string
     * @return string
     */
    public static function urlencode($string)
    {
        $rawencoded = rawurlencode($string);
        $rfcencoded = str_replace('%7E', '~', $rawencoded);
        return $rfcencoded;
    }
}
