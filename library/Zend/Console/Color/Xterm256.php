<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
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
    private function __construct($color = null)
    {
        static::$color = $color !== null ? sprintf('%%s;5;%s', $color) : null;
    }
    public static function calculate($hexColor)
    {
        $hex = str_split($hexColor, 2);
        if (count($hex) !== 3 || !preg_match('#[0-9A-F]{6}#i', $hexColor)) {
            return new static();
        }
        $ahex = array_map(function ($hex) {
            $val = round(((hexdec($hex) - 55)/40), 0);

            return $val > 0 ? (int) $val : 0;
        }, $hex);
        $dhex = array_map('hexdec', $hex);
        if (array_fill(0, 3, $dhex[0]) === $dhex && (int) substr($dhex[0], -1) === 8) {
            $x11 = 232 + (int) floor($dhex[0]/10);

            return new static($x11);
        }
        $x11 = $ahex[0] * 36 + $ahex[1] * 6 + $ahex[2] + 16;

        return new static($x11);
    }
}
