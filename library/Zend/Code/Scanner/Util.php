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
 * @package    Zend_Code
 * @subpackage Scanner
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Code\Scanner;

use Zend\Code\Exception,
    stdClass;

/**
 * Shared utility methods used by scanners
 *
 * @package    Zend_Code
 * @subpackage Scanner
 * @license New BSD {@link http://framework.zend.com/license/new-bsd}
 */
class Util
{
    public static function resolveImports(&$value, $key = null, stdClass $data)
    {
        if (!property_exists($data, 'uses') || !property_exists($data, 'namespace')) {
            throw new Exception\InvalidArgumentException(sprintf(
                '%s expects a data object containing "uses" and "namespace" properties; on or both missing',
                __METHOD__
            ));
        }
        
        if ($data->namespace && !$data->uses && strlen($value) > 0 && $value{0} != '\\') {
            $value = $data->namespace . '\\' . $value;
            return;
        }
        
        if (!$data->uses || strlen($value) <= 0 || $value{0} == '\\') {
            $value = ltrim($value, '\\');
            return;
        }
        
        if ($data->namespace || $data->uses) {
            $firstPart = $value;
            if (($firstPartEnd = strpos($firstPart, '\\')) !== false)  {
                $firstPart = substr($firstPart, 0, $firstPartEnd);
            } else {
                $firstPartEnd = strlen($firstPart);
            }
            if (array_key_exists($firstPart, $data->uses)) {
                $value = substr_replace($value, $data->uses[$firstPart], 0, $firstPartEnd);
                return;
            }
            if ($data->namespace) {
                $value = $data->namespace . '\\' . $value;
                return;
            }
        }
    }
}
