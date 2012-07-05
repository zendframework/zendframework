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
class SharedKeyLite
    extends AbstractCredentials
{
    /**
     * Sign request URL with credentials
     *
     * @param string $requestUrl         Request URL
     * @param string $resourceType       Resource type
     * @param string $requiredPermission Required permission
     * @return string Signed request URL
     */
    public function signRequestUrl(
        $requestUrl = '',
        $resourceType = Storage\Storage::RESOURCE_UNKNOWN,
        $requiredPermission = AbstractCredentials::PERMISSION_READ
    )
    {
        return $requestUrl;
    }

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
    public function signRequestHeaders(
        $httpVerb = Request::METHOD_GET,
        $path = '/',
        $queryString = '',
        $headers = null,
        $forTableStorage = false,
        $resourceType = Storage\Storage::RESOURCE_UNKNOWN,
        $requiredPermission = AbstractCredentials::PERMISSION_READ
    )
    {
        // Determine path
        if ($this->_usePathStyleUri) {
            $path = substr($path, strpos($path, '/'));
        }

        // Determine query
        $queryString = $this->_prepareQueryStringForSigning($queryString);

        // Build canonicalized resource string
        $canonicalizedResource = '/' . $this->_accountName;
        if ($this->_usePathStyleUri) {
            $canonicalizedResource .= '/' . $this->_accountName;
        }
        $canonicalizedResource .= $path;
        if ($queryString !== '') {
            $canonicalizedResource .= $queryString;
        }

        // Request date
        $requestDate = '';
        if (isset($headers[AbstractCredentials::PREFIX_STORAGE_HEADER . 'date'])) {
            $requestDate = $headers[AbstractCredentials::PREFIX_STORAGE_HEADER . 'date'];
        } else {
            $requestDate = gmdate('D, d M Y H:i:s', time()) . ' GMT'; // RFC 1123
        }

        // Create string to sign
        $stringToSign   = array();
        $stringToSign[] = $requestDate; // Date
        $stringToSign[] = $canonicalizedResource; // Canonicalized resource
        $stringToSign   = implode("\n", $stringToSign);
        $signString     = base64_encode(hash_hmac('sha256', $stringToSign, $this->_accountKey, true));

        // Sign request
        $headers[AbstractCredentials::PREFIX_STORAGE_HEADER . 'date'] = $requestDate;
        $headers['Authorization']                                     =
            'SharedKeyLite ' . $this->_accountName . ':' . $signString;

        // Return headers
        return $headers;
    }
}
