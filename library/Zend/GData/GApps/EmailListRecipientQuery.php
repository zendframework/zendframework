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
 * Assists in constructing queries for Google Apps email list recipient
 * entries. Instances of this class can be provided in many places where a
 * URL is required.
 *
 * For information on submitting queries to a server, see the Google Apps
 * service class, Zend_Gdata_GApps.
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage GApps
 */
class EmailListRecipientQuery extends AbstractQuery
{

    /**
     * If not null, specifies the name of the email list which
     * should be requested by this query.
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
     * @param string $startRecipient (optional) Value for the
     *          startRecipient property.
     */
    public function __construct($domain = null, $emailListName = null,
            $startRecipient = null)
    {
        parent::__construct($domain);
        $this->setEmailListName($emailListName);
        $this->setStartRecipient($startRecipient);
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
     * @param string $value The email list name to filter search results by,
     *          or null if disabled.
     */
    public function getEmailListName()
    {
        return $this->_emailListName;
    }

    /**
     * Set the first recipient which should be displayed when retrieving
     * a list of email list recipients.
     *
     * @param string $value The first recipient to be returned, or null to
     *              disable.
     */
    public function setStartRecipient($value)
    {
        if ($value !== null) {
            $this->_params['startRecipient'] = $value;
        } else {
            unset($this->_params['startRecipient']);
        }
    }

    /**
     * Get the first recipient which should be displayed when retrieving
     * a list of email list recipients.
     *
     * @return string The first recipient to be returned, or null if
     *              disabled.
     */
    public function getStartRecipient()
    {
        if (array_key_exists('startRecipient', $this->_params)) {
            return $this->_params['startRecipient'];
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
        } else {
            throw new \Zend\GData\App\InvalidArgumentException(
                    'EmailListName must not be null');
        }
        $uri .= GApps::APPS_EMAIL_LIST_RECIPIENT_POSTFIX . '/';
        $uri .= $this->getQueryString();
        return $uri;
    }

}
