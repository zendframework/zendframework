<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Stdlib
 */

namespace Zend\Stdlib\StringWrapper;

use Zend\Stdlib\Exception;
use Zend\Stdlib\StringUtils;

/**
 * @category   Zend
 * @package    Zend_Stdlib
 * @subpackage StringWrapper
 */
abstract class AbstractStringWrapper implements StringWrapperInterface
{

    /**
     * List of supported character sets (upper case)
     *
     * @var string[]
     */
    protected $encodings = array();

    /**
     * Check if the given encoding is supported
     *
     * @param string $encoding
     * @return boolean
     */
    public function isEncodingSupported($encoding)
    {
        $encoding = strtoupper($encoding);
        return in_array($encoding, $this->encodings);
    }

    /**
     * Get a list of supported encodings
     *
     * @return string[]
     */
    public function getSupportedEncodings()
    {
        return $this->$encodings;
    }

    /**
     * Wraps a string to a given number of characters
     *
     * @param  string  $str
     * @param  integer $width
     * @param  string  $break
     * @param  boolean $cut
     * @param  string  $encoding
     * @return string
     */
    public function wordWrap($string, $width = 75, $break = "\n", $cut = false, $encoding    = 'UTF-8')
    {
        $string = (string) $string;
        if ($string === '') {
            return '';
        }

        $break = (string) $break;
        if ($break === '') {
            throw new Exception\InvalidArgumentException('Break string cannot be empty');
        }

        $width = (int) $width;
        $cut   = (bool) $cut;
        if ($width === 0 && $cut) {
            throw new Exception\InvalidArgumentException('Cannot force cut when width is zero');
        }

        $encoding = strtoupper($encoding);
        if (StringUtils::isSingleByteEncoding($encoding)) {
            return wordwrap($string, $width, $break, $cut);
        }

        $stringWidth = $this->strlen($string, $encoding);
        $breakWidth  = $this->strlen($break, $encoding);

        $result    = '';
        $lastStart = $lastSpace = 0;

        for ($current = 0; $current < $stringWidth; $current++) {
            $char = $this->substr($string, $current, 1, $encoding);

            $possibleBreak = $char;
            if ($breakWidth !== 1) {
                $possibleBreak = $this->substr($string, $current, $breakWidth, $encoding);
            }

            if ($possibleBreak === $break) {
                $result    .= $this->substr($string, $lastStart, $current - $lastStart + $breakWidth, $encoding);
                $current   += $breakWidth - 1;
                $lastStart  = $lastSpace = $current + 1;
                continue;
            }

            if ($char === ' ') {
                if ($current - $lastStart >= $width) {
                    $result    .= $this->substr($string, $lastStart, $current - $lastStart, $encoding) . $break;
                    $lastStart  = $current + 1;
                }

                $lastSpace = $current;
                continue;
            }

            if ($current - $lastStart >= $width && $cut && $lastStart >= $lastSpace) {
                $result    .= $this->substr($string, $lastStart, $current - $lastStart, $encoding) . $break;
                $lastStart  = $lastSpace = $current;
                continue;
            }

            if ($current - $lastStart >= $width && $lastStart < $lastSpace) {
                $result    .= $this->substr($string, $lastStart, $lastSpace - $lastStart, $encoding) . $break;
                $lastStart  = $lastSpace = $lastSpace + 1;
                continue;
            }
        }

        if ($lastStart !== $current) {
            $result .= $this->substr($string, $lastStart, $current - $lastStart, $encoding);
        }

        return $result;
    }

    /**
     * Pad a string to a certain length with another string
     *
     * @param  string  $input
     * @param  integer $padLength
     * @param  string  $padString
     * @param  integer $padType
     * @param  string  $encoding
     * @return string
     */
    public function strPad($input, $padLength, $padString = ' ', $padType = \STR_PAD_RIGHT, $encoding = 'UTF-8')
    {
        $encoding = strtoupper($encoding);
        if (StringUtils::isSingleByteEncoding($encoding)) {
            return str_pad($input, $padLength, $padString, $padType);
        }

        $return          = '';
        $lengthOfPadding = $padLength - $this->strlen($input, $encoding);
        $padStringLength = $this->strlen($padString, $encoding);

        if ($padStringLength === 0 || $lengthOfPadding <= 0) {
            $return = $input;
        } else {
            $repeatCount = floor($lengthOfPadding / $padStringLength);

            if ($padType === \STR_PAD_BOTH) {
                $lastStringLeft  = '';
                $lastStringRight = '';
                $repeatCountLeft = $repeatCountRight = ($repeatCount - $repeatCount % 2) / 2;

                $lastStringLength       = $lengthOfPadding - 2 * $repeatCountLeft * $padStringLength;
                $lastStringLeftLength   = $lastStringRightLength = floor($lastStringLength / 2);
                $lastStringRightLength += $lastStringLength % 2;

                $lastStringLeft  = $this->substr($padString, 0, $lastStringLeftLength, $encoding);
                $lastStringRight = $this->substr($padString, 0, $lastStringRightLength, $encoding);

                $return = str_repeat($padString, $repeatCountLeft) . $lastStringLeft
                    . $input
                    . str_repeat($padString, $repeatCountRight) . $lastStringRight;
            } else {
                $lastString = $this->substr($padString, 0, $lengthOfPadding % $padStringLength, $encoding);

                if ($padType === \STR_PAD_LEFT) {
                    $return = str_repeat($padString, $repeatCount) . $lastString . $input;
                } else {
                    $return = $input . str_repeat($padString, $repeatCount) . $lastString;
                }
            }
        }

        return $return;
    }
}
