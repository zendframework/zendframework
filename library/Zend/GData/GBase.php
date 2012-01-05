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
 * @subpackage GBase
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\GData;

/**
 * Service class for interacting with the Google Base data API
 *
 * @link http://code.google.com/apis/base
 *
 * @uses       \Zend\GData\GData
 * @uses       \Zend\GData\App\InvalidArgumentException
 * @uses       \Zend\GData\GBase\ItemEntry
 * @uses       \Zend\GData\GBase\ItemFeed
 * @uses       \Zend\GData\GBase\SnippetFeed
 * @uses       \Zend\GData\GBase\SnippetEntry
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage GBase
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class GBase extends GData
{

    /**
     * Path to the customer items feeds on the Google Base server.
     */
    const GBASE_ITEM_FEED_URI = 'http://www.google.com/base/feeds/items';

    /**
     * Path to the snippets feeds on the Google Base server.
     */
    const GBASE_SNIPPET_FEED_URI = 'http://www.google.com/base/feeds/snippets';

    /**
     * Authentication service name for Google Base
     */
    const AUTH_SERVICE_NAME = 'gbase';

    /**
     * The default URI for POST methods
     *
     * @var string
     */
    protected $_defaultPostUri = self::GBASE_ITEM_FEED_URI;

    /**
     * Namespaces used for Zend_Gdata_GBase
     *
     * @var array
     */
    public static $namespaces = array(
        array('g', 'http://base.google.com/ns/1.0', 1, 0),
        array('batch', 'http://schemas.google.com/gdata/batch', 1, 0)
    );

    /**
     * Create Zend_Gdata_GBase object
     *
     * @param \Zend\Http\Client $client (optional) The HTTP client to use when
     *          when communicating with the Google Apps servers.
     * @param string $applicationId The identity of the app in the form of Company-AppName-Version
     */
    public function __construct($client = null, $applicationId = 'MyCompany-MyApp-1.0')
    {
        $this->registerPackage('Zend\GData\GBase');
        $this->registerPackage('Zend\GData\GBase\Extension');
        parent::__construct($client, $applicationId);
        $this->_httpClient->setParameterPost('service', self::AUTH_SERVICE_NAME);
    }

    /**
     * Retreive feed object
     *
     * @param mixed $location The location for the feed, as a URL or Query
     * @return \Zend\GData\GBase\ItemFeed
     */
    public function getGBaseItemFeed($location = null)
    {
        if ($location === null) {
            $uri = self::GBASE_ITEM_FEED_URI;
        } else if ($location instanceof Query) {
            $uri = $location->getQueryUrl();
        } else {
            $uri = $location;
        }
        return parent::getFeed($uri, 'Zend\GData\GBase\ItemFeed');
    }

    /**
     * Retreive entry object
     *
     * @param mixed $location The location for the feed, as a URL or Query
     * @return \Zend\GData\GBase\ItemEntry
     */
    public function getGBaseItemEntry($location = null)
    {
        if ($location === null) {
            throw new App\InvalidArgumentException(
                    'Location must not be null');
        } else if ($location instanceof Query) {
            $uri = $location->getQueryUrl();
        } else {
            $uri = $location;
        }
        return parent::getEntry($uri, 'Zend\GData\GBase\ItemEntry');
    }

    /**
     * Insert an entry
     *
     * @param \Zend\GData\GBase\ItemEntry $entry The Base entry to upload
     * @param boolean $dryRun Flag for the 'dry-run' parameter
     * @return \Zend\GData\GBase\ItemFeed
     */
    public function insertGBaseItem($entry, $dryRun = false)
    {
        if ($dryRun == false) {
            $uri = $this->_defaultPostUri;
        } else {
            $uri = $this->_defaultPostUri . '?dry-run=true';
        }
        $newitem = $this->insertEntry($entry, $uri, 'Zend\GData\GBase\ItemEntry');
        return $newitem;
    }

    /**
     * Update an entry
     *
     * @param \Zend\GData\GBase\ItemEntry $entry The Base entry to be updated
     * @param boolean $dryRun Flag for the 'dry-run' parameter
     * @return \Zend\GData\GBase\ItemEntry
     */
    public function updateGBaseItem($entry, $dryRun = false)
    {
        $returnedEntry = $entry->save($dryRun);
        return $returnedEntry;
    }

    /**
     * Delete an entry
     *
     * @param \Zend\GData\GBase\ItemEntry $entry The Base entry to remove
     * @param boolean $dryRun Flag for the 'dry-run' parameter
     * @return \Zend\GData\GBase\ItemFeed
     */
    public function deleteGBaseItem($entry, $dryRun = false)
    {
        $entry->delete($dryRun);
        return $this;
    }

    /**
     * Retrieve feed object
     *
     * @param mixed $location The location for the feed, as a URL or Query
     * @return \Zend\GData\GBase\SnippetFeed
     */
    public function getGBaseSnippetFeed($location = null)
    {
        if ($location === null) {
            $uri = self::GBASE_SNIPPET_FEED_URI;
        } else if ($location instanceof Query) {
            $uri = $location->getQueryUrl();
        } else {
            $uri = $location;
        }
        return parent::getFeed($uri, 'Zend\GData\GBase\SnippetFeed');
    }
}
