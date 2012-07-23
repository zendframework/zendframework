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

/**
 * @category   Zend
 * @package    Zend_Service_Amazon
 * @subpackage Authentication
 */
abstract class AbstractAuthentication
{
    /**
     * @var string
     */
    protected $_accessKey;

    /**
     * @var string
     */
    protected $_secretKey;

    /**
     * @var string
     */
    protected $_apiVersion;

    /**
     * Constructor
     *
     * @param  string $accessKey
     * @param  string $secretKey
     * @param  string $apiVersion
     */
    public function __construct($accessKey, $secretKey, $apiVersion)
    {
        $this->setAccessKey($accessKey);
        $this->setSecretKey($secretKey);
        $this->setApiVersion($apiVersion);
    }

    /**
     * Set access key
     *
     * @param  string $accessKey
     * @return void
     */
    public function setAccessKey($accessKey)
    {
        $this->_accessKey = $accessKey;
    }

    /**
     * Set secret key
     *
     * @param  string $secretKey
     * @return void
     */
    public function setSecretKey($secretKey)
    {
        $this->_secretKey = $secretKey;
    }

    /**
     * Set API version
     *
     * @param  string $apiVersion
     * @return void
     */
    public function setApiVersion($apiVersion)
    {
        $this->_apiVersion = $apiVersion;
    }
}
