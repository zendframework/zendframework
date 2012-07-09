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
class Zend_Service_DeveloperGarden_Response_SecurityTokenServer_GetTokensResponse
    extends Zend_Service_DeveloperGarden_Response_AbstractResponse
    implements Zend_Service_DeveloperGarden_Response_SecurityTokenServer_Interface
{
    /**
     * the security token
     * @var Zend_Service_DeveloperGarden_Response_SecurityTokenServer_SecurityTokenResponse
     */
    public $securityToken = null;

    /**
     * returns the security token
     *
     * @return string
     */
    public function getTokenData()
    {
        return $this->getSecurityToken();
    }

    /**
     * returns the security token
     *
     * @return string
     */
    public function getSecurityToken()
    {
        if (!$this->securityToken instanceof Zend_Service_DeveloperGarden_Response_SecurityTokenServer_SecurityTokenResponse) {
            throw new Zend_Service_DeveloperGarden_Response_SecurityTokenServer_Exception(
                'No valid securityToken found.'
            );
        }
        return $this->securityToken->getTokenData();
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
        if (isset($this->securityToken)
            && !empty($this->securityToken->tokenData)
        ) {
            return true;
        }
        return false;
    }
}
