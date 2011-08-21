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
 * @package    Zend_OAuth
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\OAuth;

/**
 * @category   Zend
 * @package    Zend_OAuth
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Consumer extends OAuth
{
    public $switcheroo = false; // replace later when this works

    /**
     * Request Token retrieved from OAuth Provider
     *
     * @var \Zend\OAuth\Token\Request
     */
    protected $_requestToken = null;

    /**
     * Access token retrieved from OAuth Provider
     *
     * @var \Zend\OAuth\Token\Access
     */
    protected $_accessToken = null;

    /**
     * @var \Zend\OAuth\Config\Config
     */
    protected $_config = null;

    /**
     * Constructor; create a new object with an optional array|Zend_Config
     * instance containing initialising options.
     *
     * @param  array|\Zend\Config\Config $options
     * @return void
     */
    public function __construct($options = null)
    {
        $this->_config = new Config\StandardConfig;
        if ($options !== null) {
            if ($options instanceof \Zend\Config\Config) {
                $options = $options->toArray();
            }
            $this->_config->setOptions($options);
        }
    }

    /**
     * Attempts to retrieve a Request Token from an OAuth Provider which is
     * later exchanged for an authorized Access Token used to access the
     * protected resources exposed by a web service API.
     *
     * @param  null|array $customServiceParameters Non-OAuth Provider-specified parameters
     * @param  null|string $httpMethod
     * @param  null|Zend\OAuth\Http\RequestToken $request
     * @return Zend\OAuth\Token\Request
     */
    public function getRequestToken(
        array $customServiceParameters = null,
        $httpMethod = null,
        Http\RequestToken $request = null
    ) {
        if ($request === null) {
            $request = new Http\RequestToken($this, $customServiceParameters);
        } elseif($customServiceParameters !== null) {
            $request->setParameters($customServiceParameters);
        }
        if ($httpMethod !== null) {
            $request->setMethod($httpMethod);
        } else {
            $request->setMethod($this->getRequestMethod());
        }
        $this->_requestToken = $request->execute();
        return $this->_requestToken;
    }

    /**
     * After a Request Token is retrieved, the user may be redirected to the
     * OAuth Provider to authorize the application's access to their
     * protected resources - the redirect URL being provided by this method.
     * Once the user has authorized the application for access, they are
     * redirected back to the application which can now exchange the previous
     * Request Token for a fully authorized Access Token.
     *
     * @param  null|array $customServiceParameters
     * @param  null|Zend\OAuth\Token\Request $token
     * @param  null|Zend\OAuth\HTTP\UserAuthorization $redirect
     * @return string
     */
    public function getRedirectUrl(
        array $customServiceParameters = null,
        Token\Request $token = null,
        Http\UserAuthorization $redirect = null
    ) {
        if ($redirect === null) {
            $redirect = new Http\UserAuthorization($this, $customServiceParameters);
        } elseif(!is_null($customServiceParameters)) {
            $redirect->setParameters($customServiceParameters);
        }
        if ($token !== null) {
            $this->_requestToken = $token;
        }
        return $redirect->getUrl();
    }

    /**
     * Rather than retrieve a redirect URL for use, e.g. from a controller,
     * one may perform an immediate redirect.
     *
     * Sends headers and exit()s on completion.
     *
     * @param  null|array $customServiceParameters
     * @param  null|Zend\OAuth\Http\UserAuthorization $request
     * @return void
     */
    public function redirect(
        array $customServiceParameters = null,
        Http\UserAuthorization $request = null
    ) {
        $redirectUrl = $this->getRedirectUrl($customServiceParameters, $request);
        header('Location: ' . $redirectUrl);
        exit(1);
    }

    /**
     * Retrieve an Access Token in exchange for a previously received/authorized
     * Request Token.
     *
     * @param  array $queryData GET data returned in user's redirect from Provider
     * @param  Zend\OAuth\Token\Request Request Token information
     * @param  string $httpMethod
     * @param  Zend\OAuth\Http\AccessToken $request
     * @return Zend\OAuth\Token\Access
     * @throws Zend\OAuth\Exception on invalid authorization token, non-matching response authorization token, or unprovided authorization token
     */
    public function getAccessToken(
        $queryData, 
        Token\Request $token,
        $httpMethod = null, 
        Http\AccessToken $request = null
    ) {
        $authorizedToken = new Token\AuthorizedRequest($queryData);
        if (!$authorizedToken->isValid()) {
            throw new Exception(
                'Response from Service Provider is not a valid authorized request token');
        }
        if ($request === null) {
            $request = new Http\AccessToken($this);
        }

        // OAuth 1.0a Verifier
        if (!is_null($authorizedToken->getParam('oauth_verifier'))) {
            $params = array_merge($request->getParameters(), array(
                'oauth_verifier' => $authorizedToken->getParam('oauth_verifier')
            ));
            $request->setParameters($params);
        }
        if ($httpMethod !== null) {
            $request->setMethod($httpMethod);
        } else {
            $request->setMethod($this->getRequestMethod());
        }
        if (isset($token)) {
            if ($authorizedToken->getToken() !== $token->getToken()) {
                throw new Exception(
                    'Authorized token from Service Provider does not match'
                    . ' supplied Request Token details'
                );
            }
        } else {
            throw new Exception('Request token must be passed to method');
        }
        $this->_requestToken = $token;
        $this->_accessToken = $request->execute();
        return $this->_accessToken;
    }

    /**
     * Return whatever the last Request Token retrieved was while using the
     * current Consumer instance.
     *
     * @return Zend\OAuth\Token\Request
     */
    public function getLastRequestToken()
    {
        return $this->_requestToken;
    }

    /**
     * Return whatever the last Access Token retrieved was while using the
     * current Consumer instance.
     *
     * @return Zend\OAuth\Token\Access
     */
    public function getLastAccessToken()
    {
        return $this->_accessToken;
    }

    /**
     * Alias to self::getLastAccessToken()
     *
     * @return Zend\OAuth\Token\Access
     */
    public function getToken()
    {
        return $this->_accessToken;
    }

    /**
     * Simple Proxy to the current Zend_OAuth_Config method. It's that instance
     * which holds all configuration methods and values this object also presents
     * as it's API.
     *
     * @param  string $method
     * @param  array $args
     * @return mixed
     * @throws \Zend\OAuth\Exception if method does not exist in config object
     */
    public function __call($method, array $args)
    {
        if (!method_exists($this->_config, $method)) {
            throw new Exception('Method does not exist: '.$method);
        }
        return call_user_func_array(array($this->_config,$method), $args);
    }
}
