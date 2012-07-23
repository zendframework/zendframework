<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_GData
 */

namespace Zend\GData\GApps;

use Zend\GData\GApps;

/**
 * Assists in constructing queries for Google Apps email list entries.
 * Instances of this class can be provided in many places where a URL is
 * required.
 *
 * For information on submitting queries to a server, see the Google Apps
 * service class, Zend_Gdata_GApps.
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage GApps
 */
class EmailListQuery extends AbstractQuery
{

    /**
     * A string which, if not null, indicates which email list should
     * be retrieved by this query.
     *
     * @var string
     */
    protected $_emailListName = null;

    /**
     * Create a new instance.
     *
     * @param string $domain (optional) The Google Apps-hosted domain to use
     *          when constructing query URIs.
     * @param string $emailListName (optional) Value for the emailListName
     *          property.
     * @param string $recipient (optional) Value for the recipient
     *          property.
     * @param string $startEmailListName (optional) Value for the
     *          startEmailListName property.
     */
    public function __construct($domain = null, $emailListName = null,
            $recipient = null, $startEmailListName = null)
    {
        parent::__construct($domain);
        $this->setEmailListName($emailListName);
        $this->setRecipient($recipient);
        $this->setStartEmailListName($startEmailListName);
    }

    /**
     * Set the email list name to query for. When set, only lists with a name
     * matching this value will be returned in search results. Set to
     * null to disable filtering by list name.
     *
     * @param string $value The email list name to filter search results by,
     *          or null to disable.
     */
     public function setEmailListName($value)
     {
         $this->_emailListName = $value;
     }

    /**
     * Get the email list name to query for. If no name is set, null will be
     * returned.
     *
     * @see setEmailListName
     * @return string The email list name to filter search results by, or null
     *              if disabled.
     */
    public function getEmailListName()
    {
        return $this->_emailListName;
    }

    /**
     * Set the recipient to query for. When set, only subscribers with an
     * email address matching this value will be returned in search results.
     * Set to null to disable filtering by username.
     *
     * @param string $value The recipient email address to filter search
     *              results by, or null to  disable.
     */
    public function setRecipient($value)
    {
        if ($value !== null) {
            $this->_params['recipient'] = $value;
        } else {
            unset($this->_params['recipient']);
        }
    }

    /**
     * Get the recipient email address to query for. If no recipient is set,
     * null will be returned.
     *
     * @see setRecipient
     * @return string The recipient email address to filter search results by,
     *              or null if disabled.
     */
    public function getRecipient()
    {
        if (array_key_exists('recipient', $this->_params)) {
            return $this->_params['recipient'];
        } else {
            return null;
        }
    }

    /**
     * Set the first email list which should be displayed when retrieving
     * a list of email lists.
     *
     * @param string $value The first email list to be returned, or null to
     *              disable.
     */
    public function setStartEmailListName($value)
    {
        if ($value !== null) {
            $this->_params['startEmailListName'] = $value;
        } else {
            unset($this->_params['startEmailListName']);
        }
    }

    /**
     * Get the first email list which should be displayed when retrieving
     * a list of email lists.
     *
     * @return string The first email list to be returned, or null to
     *              disable.
     */
    public function getStartEmailListName()
    {
        if (array_key_exists('startEmailListName', $this->_params)) {
            return $this->_params['startEmailListName'];
        } else {
            return null;
        }
    }

    /**
     * Returns the URL generated for this query, based on it's current
     * parameters.
     *
     * @return string A URL generated based on the state of this query.
     * @throws \Zend\GData\App\InvalidArgumentException
     */
    public function getQueryUrl()
    {

        $uri = $this->getBaseUrl();
        $uri .= GApps::APPS_EMAIL_LIST_PATH;
        if ($this->_emailListName !== null) {
            $uri .= '/' . $this->_emailListName;
        }
        $uri .= $this->getQueryString();
        return $uri;
    }

}
