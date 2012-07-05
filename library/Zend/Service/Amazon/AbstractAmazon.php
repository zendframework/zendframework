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
 * @subpackage Amazon
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Service\Amazon;

use DateTime;

/**
 * Abstract Amazon class that handles the credentials for any of the Web Services that
 * Amazon offers
 *
 * @category   Zend
 * @package    Zend_Service
 * @subpackage Amazon
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class AbstractAmazon extends \Zend\Service\AbstractService
{
    /**
     * @var string Amazon Secret Key
     */
    protected $secretKey;

    /**
     * @var string Amazon Access Key
     */
    protected $accessKey;

    /**
     * Request date - useful for testing services with signature
     *
     * @var int|string|null Request date - useful for testing services with signature
     */
    protected $requestDate = null;

    /**
     * @var \Zend\Http\Response Response of last request
     */
    protected $lastResponse = null;

    /**
     * @var string attribute for preserving the date object
     */
    const DATE_PRESERVE_KEY = 'preserve';

    /**
     * Set the keys to use when accessing SQS.
     *
     * @param  string|null $accessKey       Set the current Access Key
     * @param  string|null $secretKey       Set the current Secret Key
     * @return void
     */
    public function setKeys($accessKey, $secretKey)
    {
        $this->accessKey = $accessKey;
        $this->secretKey = $secretKey;
    }

    /**
     * Set the RFC1123 request date - useful for testing the services with signature
     * If preserve is set, the specific object is kept for further requests
     *
     * @param null|DateTime $date
     * @param null|boolean  $preserve if the set date must be kept for further requests
     * @return void
     */
    public function setRequestDate(DateTime $date = null, $preserve = null)
    {

        if ($date instanceof DateTime && !is_null($preserve)) {
            $date->{self::DATE_PRESERVE_KEY} = (boolean) $preserve;
        }

        $this->requestDate = $date;
    }

    /**
     * Create Amazon client.
     *
     * @param  null|string $accessKey       Override the default Access Key
     * @param  null|string $secretKey       Override the default Secret Key
     */
    public function __construct($accessKey=null, $secretKey=null)
    {
        $this->accessKey = $accessKey;
        $this->secretKey = $secretKey;
    }

    /**
     * Method to fetch the Access Key
     *
     * @return string
     * @throws Exception\InvalidArgumentException
     */
    protected function _getAccessKey()
    {
        if (is_null($this->accessKey)) {
            throw new Exception\InvalidArgumentException('AWS access key was not supplied');
        }

        return $this->accessKey;
    }

    /**
     * Method to fetch the Secret AWS Key
     *
     * @return string
     * @throws Exception\InvalidArgumentException
     */
    protected function _getSecretKey()
    {
        if (is_null($this->secretKey)) {
            throw new Exception\InvalidArgumentException('AWS secret key was not supplied');
        }

        return $this->secretKey;
    }

    /**
     * Method to get the Response object of the last call to the service,
     * null if no call was done or no response was obtained
     *
     * @return \Zend\Http\Response
     */
    public function getLastResponse()
    {
        return $this->lastResponse;
    }

    /**
     * Method to get the request date - returns gmdate(DATE_RFC1123, time())
     *
     *     "Tue, 15 May 2012 15:18:31 +0000"
     *
     * Unless setRequestDate was set (as when testing the service)
     *
     * @return string
     */
    public function getRequestDate()
    {
        if (!is_object($this->requestDate)) {
            $date = new DateTime();
        } else {
            $date = $this->requestDate;
            if (empty($date->{self::DATE_PRESERVE_KEY})) {
                $this->requestDate = null;
            }
        }
        return $date->format(DateTime::RFC1123);
    }

    /**
     * Method to get the pesudo-ISO8601 request date
     *
     *          "2012-05-15T20:58:54.000Z"
     *
     * Unless setRequestDate was set (as when testing the service)
     *
     * @return string
     */
    public function getRequestIsoDate()
    {
        if (!is_object($this->requestDate)) {
            $date = new Date();
        } else {
            $date = $this->requestDate;
            if (empty($date->{self::DATE_PRESERVE_KEY})) {
                $this->requestDate = null;
            }
        }
        //DateTimeZone UTC
        return $date->get('Y-m-d\TH:i:s.000\Z'); //DATE_ISO8601 is not compatible with S3
    }
}