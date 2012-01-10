<?php
require_once 'Zend/Oauth/Consumer.php';
require_once 'Zend/Gdata/Query.php';

/**
 * Wrapper class for Google's OAuth implementation. In particular, this helper
 * bundles the token endpoints and manages the Google-specific parameters such
 * as the hd and scope parameter.
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage Demos
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

class Gdata_OAuth_Helper extends Zend_Oauth_Consumer {
  // Google's default oauth parameters/constants.
  private $_defaultOptions = array(
      'requestScheme' => Zend_Oauth::REQUEST_SCHEME_HEADER,
      'version' => '1.0',
      'requestTokenUrl' => 'https://www.google.com/accounts/OAuthGetRequestToken',
      'userAuthorizationUrl' => 'https://www.google.com/accounts/OAuthAuthorizeToken',
      'accessTokenUrl' => 'https://www.google.com/accounts/OAuthGetAccessToken'
  );

  /**
   * Create Gdata_OAuth_Helper object
   *
   * @param string $consumerKey OAuth consumer key (domain).
   * @param string $consumerSecret (optional) OAuth consumer secret. Required if
   *     using HMAC-SHA1 for a signature method.
   * @param string $sigMethod (optional) The oauth_signature method to use.
   *     Defaults to HMAC-SHA1. RSA-SHA1 is also supported.
   */
  public function __construct($consumerKey, $consumerSecret=null,
                              $sigMethod='HMAC-SHA1') {
    $this->_defaultOptions['consumerKey'] = $consumerKey;
    $this->_defaultOptions['consumerSecret'] = $consumerSecret;
    $this->_defaultOptions['signatureMethod'] = $sigMethod;
    parent::__construct($this->_defaultOptions);
  }

  /**
   * Getter for the oauth options array.
   *
   * @return array
   */
  public function getOauthOptions() {
    return $this->_defaultOptions;
  }

  /**
   * Fetches a request token.
   *
   * @param string $scope The API scope or scopes separated by spaces to
   *     restrict data access to.
   * @param mixed $callback The URL to redirect the user to after they have
   *     granted access on the approval page. Either a string or
   *     Zend_Gdata_Query object.
   * @return Zend_OAuth_Token_Request|null
   */
  public function fetchRequestToken($scope, $callback) {
    if ($callback instanceof Zend_Gdata_Query) {
        $uri = $callback->getQueryUrl();
    } else {
        $uri = $callback;
    }

    $this->_defaultOptions['callbackUrl'] = $uri;
    $this->_config->setCallbackUrl($uri);
    if (!isset($_SESSION['ACCESS_TOKEN'])) {
        return parent::getRequestToken(array('scope' => $scope));
    }
    return null;
  }

  /**
   * Redirects the user to the approval page
   *
   * @param string $domain (optional) The Google Apps domain to logged users in
   *     under or 'default' for Google Accounts. Leaving this parameter off
   *     will give users the universal login to choose an account to login
   *     under.
   * @return void
   */
  public function authorizeRequestToken($domain=null) {
    $params = array();
    if ($domain != null) {
      $params = array('hd' => $domain);
    }
    $this->redirect($params);
  }

  /**
   * Upgrades an authorized request token to an access token.
   *
   * @return Zend_OAuth_Token_Access||null
   */
  public function fetchAccessToken() {
    if (!isset($_SESSION['ACCESS_TOKEN'])) {
        if (!empty($_GET) && isset($_SESSION['REQUEST_TOKEN'])) {
            return parent::getAccessToken(
                $_GET, unserialize($_SESSION['REQUEST_TOKEN']));
        }
    }
    return null;
  }
}
