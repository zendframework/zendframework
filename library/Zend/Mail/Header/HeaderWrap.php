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
 * @package    Zend_Mail
 * @subpackage Header
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Mail\Header;

use Zend\Mail\Header;

/**
 * Utility class used for creating wrapped or MIME-encoded versions of header
 * values.
 *
 * @category   Zend
 * @package    Zend_Mail
 * @subpackage Header
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
abstract class HeaderWrap
{
    /**
     * Wrap a long header line
     * 
     * @param  string $value 
     * @param  Header $header 
     * @return string
     */
    public static function wrap($value, Header $header)
    {
        if ($header instanceof UnstructuredHeader) {
            return static::wrapUnstructuredHeader($value);
        } elseif ($header instanceof StructuredHeader) {
            return static::wrapStructuredHeader($value, $header);
        }
        return $value;
    }

    /**
     * Wrap an unstructured header line
     *
     * Wrap at 78 characters or before, based on whitespace.
     * 
     * @param  string $value 
     * @return string
     */
    protected static function wrapUnstructuredHeader($value)
    {
        return wordwrap($value, 78, "\r\n ");
    }

    /**
     * Wrap a structured header line
     * 
     * @param  string $value 
     * @param  Header $header 
     * @return string
     */
    protected static function wrapStructuredHeader($value, Header $header)
    {
        $delimiter = $header->getDelimiter();

        $length = strlen($value);
        $lines  = array();
        $temp   = '';
        for ($i = 0; $i < $length; $i++) {
            $temp .= $value[$i];
            if ($value[$i] == $delimiter) {
                $lines[] = $temp;
                $temp    = '';
            }
        }
        return implode("\r\n ", $lines);
    }

    /**
     * MIME-encode a value
     *
     * Performs quoted-printable encoding on a value, setting maximum 
     * line-length to 998. 
     * 
     * @param  string $value 
     * @param  string $encoding 
     * @param  bool $splitWords Whether or not to split the $value on whitespace 
     *                          and encode each word separately.
     * @return string
     */
    public static function mimeEncodeValue($value, $encoding, $splitWords = false)
    {
        if ($splitWords) {
            $words = array_map(function($word) use ($encoding) {
                $header = iconv_mime_encode('Header', $word, array(
                    'scheme'         => 'Q',
                    'line-length'    => 78,
                    'output-charset' => $encoding,
                ));
                return str_replace('Header: ', '', $header);
            }, explode(' ', $value));
            return implode("\r\n ", $words);
        }

        $header = iconv_mime_encode('Header', $value, array(
            'scheme'         => 'Q',
            'line-length'    => 998,
            'output-charset' => $encoding,
        ));
        return str_replace('Header: ', '', $header);
    }
}
