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
 * @package    Zend_Service_Amazon
 * @subpackage Ec2
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

require_once 'Zend/Service/Amazon/Abstract.php';

require_once 'Zend/Service/Amazon/Ec2/Response.php';

require_once 'Zend/Service/Amazon/Ec2/Exception.php';

/**
 * Provides the basic functionality to send a request to the Amazon Ec2 Query API
 *
 * @category   Zend
 * @package    Zend_Service_Amazon
 * @subpackage Ec2
 * @copyright  Copyright (c) 22005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class Zend_Service_Amazon_Ec2_Abstract extends Zend_Service_Amazon_Abstract
{
    /**
     * The HTTP query server
     */
    const EC2_ENDPOINT = 'ec2.amazonaws.com';

    /**
     * The API version to use
     */
    const EC2_API_VERSION = '2008-12-01';

    /**
     * Signature Version
     */
    const EC2_SIGNATURE_VERSION = '2';

    /**
     * Signature Encoding Method
     */
    const EC2_SIGNATURE_METHOD = 'HmacSHA256';

    /**
     * Period after which HTTP request will timeout in seconds
     */
    const HTTP_TIMEOUT = 10;

    /**
     * Sends a HTTP request to the queue service using Zend_Http_Client
     *
     * @param array $params         List of parameters to send with the request
     * @return Zend_Service_Amazon_Ec2_Response
     * @throws Zend_Service_Amazon_Ec2_Exception
     */
    protected function sendRequest(array $params = array())
    {
        $url = 'https://' . $this->_getRegion() . self::EC2_ENDPOINT . '/';

        $params = $this->addRequiredParameters($params);

        try {
            /* @var $request Zend_Http_Client */
            $request = self::getHttpClient();
			$request->resetParameters();

            $request->setConfig(array(
                'timeout' => self::HTTP_TIMEOUT
            ));

            $request->setUri($url);
            $request->setMethod(Zend_Http_Client::POST);
            $request->setParameterPost($params);

            $httpResponse = $request->request();


        } catch (Zend_Http_Client_Exception $zhce) {
            $message = 'Error in request to AWS service: ' . $zhce->getMessage();
            throw new Zend_Service_Amazon_Ec2_Exception($message, $zhce->getCode());
        }

        $response = new Zend_Service_Amazon_Ec2_Response($httpResponse);
        $this->checkForErrors($response);

        return $response;
    }

    /**
     * Adds required authentication and version parameters to an array of
     * parameters
     *
     * The required parameters are:
     * - AWSAccessKey
     * - SignatureVersion
     * - Timestamp
     * - Version and
     * - Signature
     *
     * If a required parameter is already set in the <tt>$parameters</tt> array,
     * it is overwritten.
     *
     * @param array $parameters the array to which to add the required
     *                          parameters.
     *
     * @return array
     */
    protected function addRequiredParameters(array $parameters)
    {
        $parameters['AWSAccessKeyId']   = $this->_getAccessKey();
        $parameters['SignatureVersion'] = self::EC2_SIGNATURE_VERSION;
        $parameters['Expires']          = gmdate('c');
        $parameters['Version']          = self::EC2_API_VERSION;
        $parameters['SignatureMethod']  = self::EC2_SIGNATURE_METHOD;
        $parameters['Signature']        = $this->signParameters($parameters);

        return $parameters;
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
     * @param array  $parameters the parameters for which to get the signature.
     * @param string $secretKey  the secret key to use to sign the parameters.
     *
     * @return string the signed data.
     */
    protected function signParameters(array $paramaters)
    {
        $data = "POST\n";
        $data .= $this->_getRegion() . self::EC2_ENDPOINT . "\n";
        $data .= "/\n";

        uksort($paramaters, 'strcmp');
        unset($paramaters['Signature']);

        $arrData = array();
        foreach($paramaters as $key => $value) {
            $arrData[] = $key . '=' . str_replace("%7E", "~", urlencode($value));
        }

        $data .= implode('&', $arrData);

        require_once 'Zend/Crypt/Hmac.php';
        $hmac = Zend_Crypt_Hmac::compute($this->_getSecretKey(), 'SHA256', $data, Zend_Crypt_Hmac::BINARY);

        return base64_encode($hmac);
    }

    /**
     * Checks for errors responses from Amazon
     *
     * @param Zend_Service_Amazon_Ec2_Response $response the response object to
     *                                                   check.
     *
     * @return void
     *
     * @throws Zend_Service_Amazon_Ec2_Exception if one or more errors are
     *         returned from Amazon.
     */
    private function checkForErrors(Zend_Service_Amazon_Ec2_Response $response)
    {
        $xpath = new DOMXPath($response->getDocument());
        $list  = $xpath->query('//Error');
        if ($list->length > 0) {
            $node    = $list->item(0);
            $code    = $xpath->evaluate('string(Code/text())', $node);
            $message = $xpath->evaluate('string(Message/text())', $node);
            throw new Zend_Service_Amazon_Ec2_Exception($message, 0, $code);
        }

    }
}