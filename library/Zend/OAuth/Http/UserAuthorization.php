<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_OAuth
 */

namespace Zend\OAuth\Http;

use Zend\OAuth\Http as HTTPClient;
use Zend\Uri;

/**
 * @category   Zend
 * @package    Zend_OAuth
 */
class UserAuthorization extends HTTPClient
{
    /**
     * Generate a redirect URL from the allowable parameters and configured
     * values.
     *
     * @return string
     */
    public function getUrl()
    {
        $params = $this->assembleParams();
        $uri    = Uri\UriFactory::factory($this->_consumer->getUserAuthorizationUrl());

        $uri->setQuery(
            $this->_httpUtility->toEncodedQueryString($params)
        );

        return $uri->toString();
    }

    /**
     * Assemble all parameters for inclusion in a redirect URL.
     *
     * @return array
     */
    public function assembleParams()
    {
        $params = array(
            'oauth_token' => $this->_consumer->getLastRequestToken()->getToken(),
        );

        if (!\Zend\OAuth\Client::$supportsRevisionA) {
            $callback = $this->_consumer->getCallbackUrl();
            if (!empty($callback)) {
                $params['oauth_callback'] = $callback;
            }
        }

        if (!empty($this->_parameters)) {
            $params = array_merge($params, $this->_parameters);
        }

        return $params;
    }
}
