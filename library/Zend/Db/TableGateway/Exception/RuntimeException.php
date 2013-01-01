<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Db
 */

namespace Zend\Db\TableGateway\Exception;

use Zend\Db\Exception;

/**
 * @category   Zend
 * @package    Zend_Db
 * @subpackage TableGateway
 */
class RuntimeException extends Exception\InvalidArgumentException implements ExceptionInterface
{
}
