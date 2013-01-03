<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Console
 */

namespace Zend\Console\Color;

/**
 * @category   Zend
 * @package    Zend_Console
 */
class Xterm256
{
    public static $color;
    const FOREGROUND = 38;
    const BACKGROUND = 48;
    private function __construct($color)
    {
        static::$color = sprintf('%%s;5;%s', $color);
    }
    public static function calculate($hexColor)
    {
        $hex = str_split($hexColor, 2);
        if (count($hex) !== 3) {
            return null;
        }
        $ahex = array_map(function ($hex) {
            $val = round(((hexdec($hex) - 55)/40), 0);

            return $val > 0 ? (int) $val : 0;
        }, $hex);
        $x11 = $ahex[0] * 36 + $ahex[1] * 6 + $ahex[2] + 16;
        if ($x11 >= 16 && $x11 <= 231) {
            return new static($x11);
        } else {
            $x11 = 232 + floor(hexdec($hex[0])/10);
        }

        return new static($x11);
    }
}
