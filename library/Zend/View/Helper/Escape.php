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
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\View\Helper;

use Zend\View\Exception;

/**
 * Helper for escaping values
 *
 * @uses       Iterator
 * @package    Zend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Escape extends AbstractHelper
{
    /**@+
     * @const Recursion constants
     */
    const RECURSE_NONE   = 0x00;
    const RECURSE_ARRAY  = 0x01;
    const RECURSE_OBJECT = 0x02;
    /**@-*/

    /**
     * @var callback
     */
    protected $callback;

    /**
     * @var string Encoding
     */
    protected $encoding = 'UTF-8';

    /**
     * Set the encoding to use for escape operations
     * 
     * @param  string $encoding 
     * @return Escape
     */
    public function setEncoding($encoding)
    {
        $this->encoding = $encoding;
        return $this;
    }

    /**
     * Get the encoding to use for escape operations
     * 
     * @return string
     */
    public function getEncoding()
    {
        return $this->encoding;
    }

    /**
     * Set a callback to use for escaping
     * 
     * @param  callback $callback 
     * @return Escape
     * @throws Exception if provided callback is not callable
     */
    public function setCallback($callback)
    {
        if (!is_callable($callback)) {
            throw new Exception('Invalid callback provided to ' . get_called_class());
        }
        $this->callback = $callback;
        return $this;
    }

    /**
     * Get the attached callback
     *
     * If none defined, creates a closure wrapping htmlspecialchars, providing 
     * the currently set encoding.
     * 
     * @return callback
     */
    public function getCallback()
    {
        if (!is_callable($this->callback)) {
            $encoding = $this->getEncoding();
            $callback = function($value) use ($encoding) {
                return htmlspecialchars($value, ENT_COMPAT, $encoding, false);
            };
            $this->setCallback($callback);
        }
        return $this->callback;
    }

    /**
     * Invoke this helper: escape a value
     * 
     * @param  mixed $value 
     * @param  int $recurse Expects one of the recursion constants; used to decide whether or not to recurse the given value when escaping
     * @return mixed Given a scalar, a scalar value is returned. Given an object, with the $recurse flag not allowing object recursion, returns a string. Otherwise, returns an array.
     */
    public function __invoke($value, $recurse = self::RECURSE_NONE)
    {
        if (is_string($value)) {
            $callback = $this->getCallback();
            return call_user_func($callback, $value);
        }
        if (is_array($value)) {
            if (!(self::RECURSE_ARRAY & $recurse)) {
                throw new Exception('Array provided to Escape helper, but flags do not allow recursion');
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
                    $callback = $this->getCallback();
                    return call_user_func($callback, (string) $value);
                }

                throw new Exception('Object provided to Escape helper, but flags do not allow recursion');
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
