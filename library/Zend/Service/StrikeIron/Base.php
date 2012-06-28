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
 * @package    Zend_Service
 * @subpackage StrikeIron
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Service\StrikeIron;

use SoapHeader;
use SoapClient;

/**
 * @category   Zend
 * @package    Zend_Service
 * @subpackage StrikeIron
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Base
{
    /**
     * Configuration options
     * @param array
     */
    protected $options = array(
        'username' => null,
        'password' => null,
        'client'   => null,
        'options'  => null,
        'headers'  => null,
        'wsdl'     => null,
    );

    /**
     * Output headers returned by the last call to SOAPClient->_soapCall()
     * @param array
     */
    protected $outputHeaders = array();

    /**
     * Class constructor
     *
     * @param  array  $options  Key/value pair options
     * @throws Exception\RuntimeException if soap extension is not loaded
     */
    public function __construct($options = array())
    {
        if (!extension_loaded('soap')) {
            throw new Exception\RuntimeException('SOAP extension is not enabled');
        }

        $this->options  = array_merge($this->options, $options);

        $this->initSoapHeaders();
        $this->initSoapClient();
    }

    /**
     * Proxy method calls to the SOAPClient instance, transforming method
     * calls and responses for convenience.
     *
     * @param  string  $method  Method name
     * @param  array   $params  Parameters for method
     * @return mixed            Result
     * @throws Exception\RuntimeException if error occurs during soap call
     */
    public function __call($method, $params)
    {
        // prepare method name and parameters for soap call
        list($method, $params) = $this->transformCall($method, $params);
        $params = isset($params[0]) ? array($params[0]) : array();

        // make soap call, capturing the result and output headers
        try {
            $result = $this->options['client']->__soapCall(
                $method,
                $params,
                $this->options['options'],
                $this->options['headers'],
                $this->outputHeaders
            );
        } catch (\Exception $e) {
            $message = get_class($e) . ': ' . $e->getMessage();
            throw new Exception\RuntimeException($message, $e->getCode(), $e);
        }

        // transform/decorate the result and return it
        $result = $this->transformResult($result, $method, $params);
        return $result;
    }

    /**
     * Initialize the SOAPClient instance
     *
     * @return void
     */
    protected function initSoapClient()
    {
        if (!isset($this->options['options'])) {
            $this->options['options'] = array();
        }

        if (!isset($this->options['client'])) {
            $this->options['client'] = new SoapClient(
                $this->options['wsdl'],
                $this->options['options']
            );
        }
    }

    /**
     * Initialize the headers to pass to SOAPClient->_soapCall()
     *
     * @return void
     * @throws Exception\RuntimeException if invalid headers encountered
     */
    protected function initSoapHeaders()
    {
        // validate headers and check if LicenseInfo was given
        $foundLicenseInfo = false;
        if (isset($this->options['headers'])) {
            if (! is_array($this->options['headers'])) {
                $this->options['headers'] = array($this->options['headers']);
            }

            foreach ($this->options['headers'] as $header) {
                if (!$header instanceof SoapHeader) {
                    throw new Exception\RuntimeException('Header must be instance of SoapHeader');
                } elseif ($header->name == 'LicenseInfo') {
                    $foundLicenseInfo = true;
                    break;
                }
            }
        } else {
            $this->options['headers'] = array();
        }

        // add default LicenseInfo header if a custom one was not supplied
        if (!$foundLicenseInfo) {
            $this->options['headers'][] = new SoapHeader(
                'http://ws.strikeiron.com',
                'LicenseInfo',
                array(
                    'RegisteredUser' => array(
                        'UserID'   => $this->options['username'],
                        'Password' => $this->options['password'],
                    ),
                )
            );
        }
    }

    /**
     * Transform a method name or method parameters before sending them
     * to the remote service.  This can be useful for inflection or other
     * transforms to give the method call a more PHP-like interface.
     *
     * @see    __call()
     * @param  string  $method  Method name called from PHP
     * @param  mixed   $param   Parameters passed from PHP
     * @return array            [$method, $params] for SOAPClient->_soapCall()
     */
    protected function transformCall($method, $params)
    {
        return array(ucfirst($method), $params);
    }

    /**
     * Transform the result returned from a method before returning
     * it to the PHP caller.  This can be useful for transforming
     * the SOAPClient returned result to be more PHP-like.
     *
     * The $method name and $params passed to the method are provided to
     * allow decisions to be made about how to transform the result based
     * on what was originally called.
     *
     * @see    __call()
     * @param  $result  Raw result returned from SOAPClient_>__soapCall()
     * @param  $method  Method name that was passed to SOAPClient->_soapCall()
     * @param  $params  Method parameters that were passed to SOAPClient->_soapCall()
     * @return mixed    Transformed result
     */
    protected function transformResult($result, $method, $params)
    {
        $resultObjectName = "{$method}Result";
        if (isset($result->$resultObjectName)) {
            $result = $result->$resultObjectName;
        }
        if (is_object($result)) {
            $result = new Decorator($result, $resultObjectName);
        }
        return $result;
    }

    /**
     * Get the WSDL URL for this service.
     *
     * @return string
     */
    public function getWsdl()
    {
        return $this->options['wsdl'];
    }

    /**
     * Get the SOAP Client instance for this service.
     */
    public function getSoapClient()
    {
        return $this->options['client'];
    }

    /**
     * Get the StrikeIron output headers returned with the last method response.
     *
     * @return array
     */
    public function getLastOutputHeaders()
    {
        return $this->outputHeaders;
    }

    /**
     * Get the StrikeIron subscription information for this service.
     * If any service method was recently called, the subscription info
     * should have been returned in the SOAP headers so it is cached
     * and returned from the cache.  Otherwise, the getRemainingHits()
     * method is called as a dummy to get the subscription info headers.
     *
     * @param  boolean    $now          Force a call to getRemainingHits instead of cache?
     * @param  string     $queryMethod  Method that will cause SubscriptionInfo header to be sent
     * @return Decorator  Decorated subscription info
     * @throws Exception\RuntimeException if no subscription information headers present
     */
    public function getSubscriptionInfo($now = false, $queryMethod = 'GetRemainingHits')
    {
        if ($now || empty($this->outputHeaders['SubscriptionInfo'])) {
            $this->$queryMethod();
        }

        // capture subscription info if returned in output headers
        if (isset($this->outputHeaders['SubscriptionInfo'])) {
            $info = (object)$this->outputHeaders['SubscriptionInfo'];
            $subscriptionInfo = new Decorator($info, 'SubscriptionInfo');
        } else {
            $msg = 'No SubscriptionInfo header found in last output headers';
            throw new Exception\RuntimeException($msg);
        }

        return $subscriptionInfo;
    }
}
