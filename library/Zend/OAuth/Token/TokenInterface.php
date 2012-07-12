<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_OAuth
 */

namespace Zend\OAuth\Token;

use Zend\Http\Response as HTTPResponse;
use Zend\OAuth\Http\Utility as HTTPUtility;

interface TokenInterface
{

    /**
     * Retrieve an arbitrary named parameter from the token
     *
     * @param  string $name
     * @return mixed
     */
    public function getParam($name);

    /**
     * Retrieve the response object this token is operating on
     *
     * @return HTTPResponse
     */
    public function getResponse();

    /**
     * Retrieve the token value
     *
     * @return string
     */
    public function getToken();

    /**
     * Retrieve the Token's secret, for use with signing requests
     *
     * @return string
     */
    public function getTokenSecret();

    /**
     * Set the Token's signing secret.
     *
     * @param  string $secret
     * @return Zend\OAuth\Token
     */
    public function setTokenSecret($secret);

    /**
     * Validate the Token against the HTTP Response
     *
     * @return boolean
     */
    public function isValid();

    /**
     * Convert token to a raw-encoded query string
     *
     * @return string
     */
    public function toString();

    /**
     * Cast Token to string representation; should proxy to toString()
     *
     * @return string
     */
    public function __toString();
}
