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
class Zend_Service_DeveloperGarden_SecurityTokenServer
    extends Zend_Service_DeveloperGarden_Client_AbstractClient
{
    /**
     * wsdl file
     *
     * @var string
     */
    protected $_wsdlFile = 'https://sts.idm.telekom.com/TokenService?wsdl';

    /**
     * wsdl file local
     *
     * @var string
     */
    protected $_wsdlFileLocal = 'Wsdl/TokenService.wsdl';

    /**
     * Response, Request Classmapping
     *
     * @var array
     *
     */
    protected $_classMap = array(
        'SecurityTokenResponse' => 'Zend_Service_DeveloperGarden_Response_SecurityTokenServer_SecurityTokenResponse',
        'getTokensResponse'     => 'Zend_Service_DeveloperGarden_Response_SecurityTokenServer_GetTokensResponse'
    );

    /**
     * does the login and return the specific response
     *
     * @return Zend_Service_DeveloperGarden_Response_SecurityTokenServer_SecurityTokenResponse
     */
    public function getLoginToken()
    {
        $token = Zend_Service_DeveloperGarden_SecurityTokenServer_Cache::getTokenFromCache(
            'securityToken'
        );

        if ($token === null
            || !$token->isValid()
        ) {
            $token = $this->getSoapClient()->login('login');
            Zend_Service_DeveloperGarden_SecurityTokenServer_Cache::setTokenToCache(
                'securityToken',
                $token
            );
        }

        return $token;
    }

    /**
     * returns the fetched token from token server
     *
     * @return Zend_Service_DeveloperGarden_Response_SecurityTokenServer_GetTokensResponse
     */
    public function getTokens()
    {
        $token = Zend_Service_DeveloperGarden_SecurityTokenServer_Cache::getTokenFromCache(
            'getTokens'
        );

        if ($token === null
            || !$token->isValid()
        ) {
            $token = $this->getSoapClient()->getTokens(array(
                'serviceId' => $this->_serviceAuthId
            ));
            Zend_Service_DeveloperGarden_SecurityTokenServer_Cache::setTokenToCache(
                'getTokens',
                $token
            );
        }
        return $token;
    }
}
