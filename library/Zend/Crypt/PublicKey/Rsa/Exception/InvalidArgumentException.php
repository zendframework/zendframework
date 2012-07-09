<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @package   Zend_Crypt
 */

namespace Zend\Crypt\PublicKey\Rsa\Exception;

use Zend\Crypt\Exception;

/**
 * Invalid argument exception
 *
 * @category   Zend
 * @package    Zend_Crypt
 * @subpackage PublicKey
 */
class InvalidArgumentException
    extends Exception\InvalidArgumentException
    implements ExceptionInterface
{}
