<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Service
 */

/**
 * @category   Zend
 * @package    Zend_Service
 * @subpackage DeveloperGarden
 * @author     Marco Kaiser
 */
class Zend_Service_DeveloperGarden_Response_SecurityTokenServer_SecurityTokenResponse
    extends Zend_Service_DeveloperGarden_Response_AbstractResponse
    implements Zend_Service_DeveloperGarden_Response_SecurityTokenServer_Interface
{
    /**
     * the token format, should be saml20
     *
     * @var string
     */
    public $tokenFormat = null;

    /**
     * the token encoding, should be text/xml
     *
     * @var string
     */
    public $tokenEncoding = null;

    /**
     * the tokenData should be a valid Assertion value
     *
     * @var unknown_type
     */
    public $tokenData = null;

    /**
     * returns the tokenData
     *
     * @return string
     */
    public function getTokenData()
    {
        if (empty($this->tokenData)) {
            throw new Zend_Service_DeveloperGarden_Response_Exception('No valid tokenData found.');
        }

        return $this->tokenData;
    }

    /**
     * returns the token format value
     *
     * @return string
     */
    public function getTokenFormat()
    {
        return $this->tokenFormat;
    }

    /**
     * returns the token encoding
     *
     * @return string
     */
    public function getTokenEncoding()
    {
        return $this->tokenEncoding;
    }

    /**
     * returns true if the stored token data is valid
     *
     * @return boolean
     */
    public function isValid()
    {
        /**
         * @todo implement the true token validation check
         */
        if (!empty($this->securityTokenData)) {
            return true;
        }
        return false;
    }
}
