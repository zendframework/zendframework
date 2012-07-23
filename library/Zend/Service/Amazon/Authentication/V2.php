<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
 */

namespace Zend\Service\Amazon\Authentication;

use Zend\Crypt\Hmac;

/**
 * @category   Zend
 * @package    Zend_Service_Amazon
 * @subpackage Authentication
 */
class V2 extends AbstractAuthentication
{
    /**
     * Signature Version
     */
    protected $_signatureVersion = '2';

    /**
     * Signature Encoding Method
     */
    protected $_signatureMethod = 'HmacSHA256';

    /**
     * Type of http request
     * @var string
     */
    protected $_httpMethod = "POST";

    /**
     * Generate the required attributes for the signature
     * @param string $url
     * @param array $parameters
     * @return string
     */
    public function generateSignature($url, array &$parameters)
    {
        $parameters['AWSAccessKeyId']   = $this->_accessKey;
        $parameters['SignatureVersion'] = $this->_signatureVersion;
        $parameters['Version']          = $this->_apiVersion;
        $parameters['SignatureMethod']  = $this->_signatureMethod;
        if(!isset($parameters['Timestamp'])) {
            $parameters['Timestamp']    = gmdate('Y-m-d\TH:i:s\Z', time()+10);
        }

        $data = $this->_signParameters($url, $parameters);

        return $data;
    }

    /**
     * Set http request type to POST or GET
     * @param $method string
     */
    public function setHttpMethod($method = "POST")
    {
        $this->_httpMethod = strtoupper($method);
    }

    /**
     * Get the current http request type
     * @return string
     */
    public function getHttpMethod()
    {
        return $this->_httpMethod;
    }

    /**
     * Computes the RFC 2104-compliant HMAC signature for request parameters
     *
     * This implements the Amazon Web Services signature, as per the following
     * specification:
     *
     * 1. Sort all request parameters (including <tt>SignatureVersion</tt> and
     *    excluding <tt>Signature</tt>, the value of which is being created),
     *    ignoring case.
     *
     * 2. Iterate over the sorted list and append the parameter name (in its
     *    original case) and then its value. Do not URL-encode the parameter
     *    values before constructing this string. Do not use any separator
     *    characters when appending strings.
     *
     * @param  string $queue_url  Queue URL
     * @param  array  $parameters the parameters for which to get the signature.
     *
     * @return string the signed data.
     */
    protected function _signParameters($url, array &$paramaters)
    {
        $data = $this->_httpMethod . "\n";
        $data .= parse_url($url, PHP_URL_HOST) . "\n";
        $data .= ('' == $path = parse_url($url, PHP_URL_PATH)) ? '/' : $path;
        $data .= "\n";

        uksort($paramaters, 'strcmp');
        unset($paramaters['Signature']);

        $arrData = array();
        foreach($paramaters as $key => $value) {
            $arrData[] = $key . '=' . str_replace('%7E', '~', rawurlencode($value));
        }

        $data .= implode('&', $arrData);

        $hmac = Hmac::compute($this->_secretKey, 'SHA256', $data, Hmac::OUTPUT_BINARY);

        $paramaters['Signature'] = base64_encode($hmac);

        return $data;
    }
}
