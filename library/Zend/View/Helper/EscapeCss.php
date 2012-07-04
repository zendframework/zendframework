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
 * @package    Zend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\View\Helper;

use Zend\View\Helper\Escaper;
use Zend\View\Exception;

/**
 * Helper for escaping values
 *
 * @package    Zend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class EscapeCss extends Escaper\AbstractHelper
{
    /**@+
     * @const Recursion constants
     */
    const RECURSE_NONE   = 0x00;
    const RECURSE_ARRAY  = 0x01;
    const RECURSE_OBJECT = 0x02;
    /**@-*/

    /**
     * Invoke this helper: escape a value
     * 
     * @param  mixed $value 
     * @param  int $recurse Expects one of the recursion constants; used to decide whether or not to recurse the given value when escaping
     * @return mixed Given a scalar, a scalar value is returned. Given an object, with the $recurse flag not allowing object recursion, returns a string. Otherwise, returns an array.
     * @throws Exception\InvalidArgumentException
     */
    public function __invoke($value, $recurse = self::RECURSE_NONE)
    {
        if (is_string($value)) {
            return $this->getEscaper()->escapeCss($value);
        }
        if (is_array($value)) {
            if (!(self::RECURSE_ARRAY & $recurse)) {
                throw new Exception\InvalidArgumentException(
                    'Array provided to Escape helper, but flags do not allow recursion'
                );
            }
            foreach ($value as $k => $v) {
                $value[$k] = $this->__invoke($v, $recurse);
            }
            return $value;
        }
        if (is_object($value)) {
            if (!(self::RECURSE_OBJECT & $recurse)) {
                // Attempt to cast it to a string
                if (method_exists($value, '__toString')) {
                    return $this->getEscaper()->escapeCss((string) $value);
                }
                throw new Exception\InvalidArgumentException(
                    'Object provided to Escape helper, but flags do not allow recursion'
                );
            }
            if (method_exists($value, 'toArray')) {
                return $this->__invoke($value->toArray(), $recurse | self::RECURSE_ARRAY);
            }
            return $this->__invoke((array) $value, $recurse | self::RECURSE_ARRAY);
        }
        // At this point, we have a scalar; simply return it
        return $value;
    }
}
