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
 * @subpackage DeveloperGarden
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @uses       Zend_Service_DeveloperGarden_Response_AbstractResponse
 * @uses       Zend_Service_DeveloperGarden_Response_SecurityTokenServer_Exception
 * @uses       Zend_Service_DeveloperGarden_Response_SecurityTokenServer_Interface
 * @category   Zend
 * @package    Zend_Service
 * @subpackage DeveloperGarden
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @author     Marco Kaiser
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
