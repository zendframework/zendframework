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
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\OAuth\Token;

use Zend\Http\Response as HTTPResponse,
    Zend\OAuth\Http\Utility as HTTPUtility,
    Zend\OAuth\Client;

/**
 * @uses       \Zend\Http\Response
 * @uses       \Zend\OAuth\Client
 * @uses       \Zend\OAuth\Http\Utility
 * @uses       \Zend\OAuth\Token\Token
 * @category   Zend
 * @package    Zend_OAuth
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Request extends AbstractToken
{
    /**
     * Constructor
     *
     * @param null|Zend\Http\Response $response
     * @param null|Zend\OAuth\Http\Utility $utility
     */
    public function __construct(
        HTTPResponse $response = null,
        HTTPUtility $utility = null
    ) {
        parent::__construct($response, $utility);

        // detect if server supports OAuth 1.0a
        if (isset($this->_params[AbstractToken::TOKEN_PARAM_CALLBACK_CONFIRMED])) {
            Client::$supportsRevisionA = true;
        }
    }
}
