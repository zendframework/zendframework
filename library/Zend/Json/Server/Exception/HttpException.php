<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Json
 */

namespace Zend\Json\Server\Exception;

/**
 * Thrown by Zend_Json_Server_Client when an HTTP error occurs during an
 * JSON-RPC method call.
 *
 * @category   Zend
 * @package    Zend_Json
 * @subpackage Server
 */
class HttpException extends RuntimeException
{}
