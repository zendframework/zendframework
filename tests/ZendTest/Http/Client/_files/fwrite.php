<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Http
 */

namespace Zend\Http\Client\Adapter;

/**
 * This is a stub for PHP's `fwrite` function. It
 * allows us to check that a write operation to a
 * socket producing a returned "0 bytes" written
 * is actually valid.
 */
function fwrite($socket, $request)
{
    return 0;
}
