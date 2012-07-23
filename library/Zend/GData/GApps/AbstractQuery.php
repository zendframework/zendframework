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
use Zend\GData\Query;

/**
 * Assists in constructing queries for Google Apps entries. This class
 * provides common methods used by all other Google Apps query classes.
 *
 * This class should never be instantiated directly. Instead, instantiate a
 * class which inherits from this class.
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage GApps
 */
abstract class AbstractQuery extends Query
{

    /**
     * The domain which is being administered via the Provisioning API.
     *
     * @var string
     */
    protected $_domain = null;

    /**
     * Create a new instance.
     *
     * @param string $domain (optional) The Google Apps-hosted domain to use
     *          when constructing query URIs.
     */
    public function __construct($domain = null)
    {
        parent::__construct();
        $this->_domain = $domain;
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
     * @see setDomain
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
     * @see setDomain
     * @param string $domain (optional) A fully-qualified domain to use
     *          instead of the default domain for this service instance.
     */
     public function getBaseUrl($domain = null)
     {
         if ($domain !== null) {
             return GApps::APPS_BASE_FEED_URI . '/' . $domain;
         } elseif ($this->_domain !== null) {
             return GApps::APPS_BASE_FEED_URI . '/' . $this->_domain;
         } else {
             throw new \Zend\GData\App\InvalidArgumentException(
                 'Domain must be specified.');
         }
     }

}
