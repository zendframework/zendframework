<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service_WindowsAzure
 */

namespace Zend\Service\WindowsAzure\Credentials;

use Zend\Service\WindowsAzure\Storage;
use Zend\Http\Request;

/**
 * @category   Zend
 * @package    Zend_Service_WindowsAzure
 */
abstract class AbstractCredentials
{
    /**
     * Development storage account and key
     */
    const DEVSTORE_ACCOUNT = "devstoreaccount1";
    const DEVSTORE_KEY     = "Eby8vdM02xNOcqFlqUwJPLlmEtlCDXJ1OUzFT50uSRZ6IFsuFq2UVErCz4I6tq/K1SZFPTOtr/KBHBeksoGMGw==";

    /**
     * HTTP header prefixes
     */
    const PREFIX_PROPERTIES     = "x-ms-prop-";
    const PREFIX_METADATA       = "x-ms-meta-";
    const PREFIX_STORAGE_HEADER = "x-ms-";

    /**
     * Permissions
     */
    const PERMISSION_READ   = "r";
    const PERMISSION_WRITE  = "w";
    const PERMISSION_DELETE = "d";
    const PERMISSION_LIST   = "l";

    /**
     * Account name for Windows Azure
     *
     * @var string
     */
    protected $_accountName = '';

    /**
     * Account key for Windows Azure
     *
     * @var string
     */
    protected $_accountKey = '';

    /**
     * Use path-style URI's
     *
     * @var boolean
     */
    protected $_usePathStyleUri = false;

    /**
     * Creates a new AbstractCredentials instance
     *
     * @param string  $accountName     Account name for Windows Azure
     * @param string  $accountKey      Account key for Windows Azure
     * @param boolean $usePathStyleUri Use path-style URI's
     */
    public function __construct(
        $accountName = AbstractCredentials::DEVSTORE_ACCOUNT,
        $accountKey = AbstractCredentials::DEVSTORE_KEY,
        $usePathStyleUri = false
    )
    {
        $this->_accountName     = $accountName;
        $this->_accountKey      = base64_decode($accountKey);
        $this->_usePathStyleUri = $usePathStyleUri;
    }

    /**
     * Set account name for Windows Azure
     *
     * @param  string $value
     * @return AbstractCredentials
     */
    public function setAccountName($value = AbstractCredentials::DEVSTORE_ACCOUNT)
    {
        $this->_accountName = $value;
        return $this;
    }

    /**
     * Set account key for Windows Azure
     *
     * @param  string $value
     * @return AbstractCredentials
     */
    public function setAccountkey($value = AbstractCredentials::DEVSTORE_KEY)
    {
        $this->_accountKey = base64_decode($value);
        return $this;
    }

    /**
     * Set use path-style URI's
     *
     * @param  boolean $value
     * @return AbstractCredentials
     */
    public function setUsePathStyleUri($value = false)
    {
        $this->_usePathStyleUri = $value;
        return $this;
    }

    /**
     * Sign request URL with credentials
     *
     * @param string $requestUrl         Request URL
     * @param string $resourceType       Resource type
     * @param string $requiredPermission Required permission
     * @return string Signed request URL
     */
    abstract public function signRequestUrl(
        $requestUrl = '',
        $resourceType = Storage\Storage::RESOURCE_UNKNOWN,
        $requiredPermission = AbstractCredentials::PERMISSION_READ
    );

    /**
     * Sign request headers with credentials
     *
     * @param string  $httpVerb           HTTP verb the request will use
     * @param string  $path               Path for the request
     * @param string  $queryString        Query string for the request
     * @param array   $headers            x-ms headers to add
     * @param boolean $forTableStorage    Is the request for table storage?
     * @param string  $resourceType       Resource type
     * @param string  $requiredPermission Required permission
     * @return array Array of headers
     */
    abstract public function signRequestHeaders(
        $httpVerb = Request::METHOD_GET,
        $path = '/',
        $queryString = '',
        $headers = null,
        $forTableStorage = false,
        $resourceType = Storage\Storage::RESOURCE_UNKNOWN,
        $requiredPermission = AbstractCredentials::PERMISSION_READ
    );


    /**
     * Prepare query string for signing
     *
     * @param  string $value Original query string
     * @return string        Query string for signing
     */
    protected function _prepareQueryStringForSigning($value)
    {
        // Check for 'comp='
        if (strpos($value, 'comp=') === false) {
            // If not found, no query string needed
            return '';
        } else {
            // If found, make sure it is the only parameter being used
            if (strlen($value) > 0 && strpos($value, '?') === 0) {
                $value = substr($value, 1);
            }

            // Split parts
            $queryParts = explode('&', $value);
            foreach ($queryParts as $queryPart) {
                if (strpos($queryPart, 'comp=') !== false) {
                    return '?' . $queryPart;
                }
            }

            // Should never happen...
            return '';
        }
    }
}
