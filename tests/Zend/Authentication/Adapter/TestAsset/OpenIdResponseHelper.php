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
 * @package    Zend_OpenId
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace ZendTest\Authentication\Adapter\TestAsset;

use Zend\Controller\Response\AbstractResponse;

/**
 * Zend_OpenId
 */

\Zend\OpenID\OpenID::$exitOnRedirect = false;

/**
 * @category   Zend
 * @package    Zend_OpenId
 * @subpackage UnitTests
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class OpenIdResponseHelper extends AbstractResponse
{
    private $_canSendHeaders;

    public function __construct($canSendHeaders)
    {
        $this->_canSendHeaders = $canSendHeaders;
    }

    public function canSendHeaders($throw = false)
    {
        return $this->_canSendHeaders;
    }

    public function sendResponse()
    {
    }
}
