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
 */

namespace ZendTest\Authentication\Adapter\TestAsset;

use Zend\Http\Response;
use Zend\OpenId\OpenId;

OpenId::$exitOnRedirect = false;

/**
 * @category   Zend
 * @package    Zend_Authentication
 * @subpackage UnitTests
 */
class ResponseHelper extends Response
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
