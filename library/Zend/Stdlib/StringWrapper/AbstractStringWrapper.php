<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
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
     * The character encoding working on
     * @var string|null
     */
    protected $encoding;

    /**
     * An optionally character encoding to convert to
     * @var string|null
     */
    protected $convertEncoding;

    /**
     * Check if the given character encoding is supported by this wrapper
     * and the character encoding to convert to is also supported.
     *
     * @param string      $encoding
     * @param string|null $convertEncoding
     */
    public static function isSupported($encoding, $convertEncoding = null)
    {
        $supportedEncodings = static::getSupportedEncodings();

        if (!in_array(strtoupper($encoding), $supportedEncodings)) {
            return false;
        }

        if ($convertEncoding !== null && !in_array(strtoupper($convertEncoding), $supportedEncodings)) {
            return false;
        }

        return true;
    }

    /**
     * Constructor
     * @param string      $encoding        Character encoding working on
     * @param string|null $convertEncoding Character encoding to convert to
     * @throws Exception\InvalidArgumentException
     */
    public function __construct($encoding, $convertEncoding = null)
    {
        $this->setEncoding($encoding, $convertEncoding);
    }

    /**
     * Set character encoding working with and convert to
     *
     * @param string      $encoding         The character encoding to work with
     * @param string|null $convertEncoding  The character encoding to convert to
     * @return StringWrapperInterface
     */
    public function setEncoding($encoding, $convertEncoding = null)
    {
        $supportedEncodings = static::getSupportedEncodings();

        $encodingUpper = strtoupper($encoding);
        if (!in_array($encodingUpper, $supportedEncodings)) {
            throw new Exception\InvalidArgumentException(
                'Wrapper doesn\'t support character encoding "' . $encoding . '"'
            );
        }


        if ($convertEncoding !== null) {
            $convertEncodingUpper = strtoupper($convertEncoding);
            if (!in_array($convertEncodingUpper, $supportedEncodings)) {
                throw new Exception\InvalidArgumentException(
                    'Wrapper doesn\'t support character encoding "' . $convertEncoding . '"'
                );
            }

            $this->convertEncoding = $convertEncodingUpper;
        } else {
            $this->convertEncoding = null;
        }
        $this->encoding = $encodingUpper;

        return $this;
    }

    /**
     * Convert a string from one character encoding to another
     *
     * @param string  $str
     * @param boolean $backward
     * @return string|false
     */
    public function convert($str, $backward = false)
    {
        $from = $backward ? $this->convertEncoding : $this->encoding;
        $to   = $backward ? $this->encoding : $this->convertEncoding;

        if ($to == $from) {
            return $str;
        }

        throw new Exception\RuntimeException(sprintf(
            'Converting from "%s" to "%s" isn\'t supported by this string wrapper',
            $from,
            $to
        ));
    }

    /**
     * Wraps a string to a given number of characters
     *
     * @param  string  $str
     * @param  integer $width
     * @param  string  $break
     * @param  boolean $cut
     * @return string|false
     */
    public function wordWrap($string, $width = 75, $break = "\n", $cut = false)
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

        if (StringUtils::isSingleByteEncoding($this->encoding)) {
            return wordwrap($string, $width, $break, $cut);
        }

        $stringWidth = $this->strlen($string);
        $breakWidth  = $this->strlen($break);

        $result    = '';
        $lastStart = $lastSpace = 0;

        for ($current = 0; $current < $stringWidth; $current++) {
            $char = $this->substr($string, $current, 1);

            $possibleBreak = $char;
            if ($breakWidth !== 1) {
                $possibleBreak = $this->substr($string, $current, $breakWidth);
            }

            if ($possibleBreak === $break) {
                $result    .= $this->substr($string, $lastStart, $current - $lastStart + $breakWidth);
                $current   += $breakWidth - 1;
                $lastStart  = $lastSpace = $current + 1;
                continue;
            }

            if ($char === ' ') {
                if ($current - $lastStart >= $width) {
                    $result    .= $this->substr($string, $lastStart, $current - $lastStart) . $break;
                    $lastStart  = $current + 1;
                }

                $lastSpace = $current;
                continue;
            }

            if ($current - $lastStart >= $width && $cut && $lastStart >= $lastSpace) {
                $result    .= $this->substr($string, $lastStart, $current - $lastStart) . $break;
                $lastStart  = $lastSpace = $current;
                continue;
            }

            if ($current - $lastStart >= $width && $lastStart < $lastSpace) {
                $result    .= $this->substr($string, $lastStart, $lastSpace - $lastStart) . $break;
                $lastStart  = $lastSpace = $lastSpace + 1;
                continue;
            }
        }

        if ($lastStart !== $current) {
            $result .= $this->substr($string, $lastStart, $current - $lastStart);
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
     * @return string
     */
    public function strPad($input, $padLength, $padString = ' ', $padType = \STR_PAD_RIGHT)
    {
        if (StringUtils::isSingleByteEncoding($this->encoding)) {
            return str_pad($input, $padLength, $padString, $padType);
        }

        $lengthOfPadding = $padLength - $this->strlen($input);
        if ($lengthOfPadding <= 0) {
            return $input;
        }

        $padStringLength = $this->strlen($padString);
        if ($padStringLength === 0) {
            return $input;
        }

        $repeatCount = floor($lengthOfPadding / $padStringLength);

        if ($padType === \STR_PAD_BOTH) {
            $lastStringLeft  = '';
            $lastStringRight = '';
            $repeatCountLeft = $repeatCountRight = ($repeatCount - $repeatCount % 2) / 2;

            $lastStringLength       = $lengthOfPadding - 2 * $repeatCountLeft * $padStringLength;
            $lastStringLeftLength   = $lastStringRightLength = floor($lastStringLength / 2);
            $lastStringRightLength += $lastStringLength % 2;

            $lastStringLeft  = $this->substr($padString, 0, $lastStringLeftLength);
            $lastStringRight = $this->substr($padString, 0, $lastStringRightLength);

            return str_repeat($padString, $repeatCountLeft) . $lastStringLeft
                . $input
                . str_repeat($padString, $repeatCountRight) . $lastStringRight;
        }

        $lastString = $this->substr($padString, 0, $lengthOfPadding % $padStringLength);

        if ($padType === \STR_PAD_LEFT) {
            return str_repeat($padString, $repeatCount) . $lastString . $input;
        }

        return $input . str_repeat($padString, $repeatCount) . $lastString;
    }
}
