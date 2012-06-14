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

use Zend\Mail\Headers;
use Zend\Mime\Mime;

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
     * @param  string          $value 
     * @param  HeaderInterface $header 
     * @return string
     */
    public static function wrap($value, HeaderInterface $header)
    {
        if ($header instanceof UnstructuredInterface) {
            return static::wrapUnstructuredHeader($value, $header);
        } elseif ($header instanceof StructuredInterface) {
            return static::wrapStructuredHeader($value, $header);
        }
        return $value;
    }

    /**
     * Wrap an unstructured header line
     *
     * Wrap at 78 characters or before, based on whitespace.
     *
     * @param string          $value
     * @param HeaderInterface $header
     * @return string
     */
    protected static function wrapUnstructuredHeader($value, HeaderInterface $header)
    {
        $encoding = $header->getEncoding();
        if ($encoding == 'ASCII') {
            return wordwrap($value, 78, Headers::FOLDING);
        }
        return static::mimeEncodeValue($value, $encoding, 78);
    }

    /**
     * Wrap a structured header line
     * 
     * @param  string              $value
     * @param  StructuredInterface $header
     * @return string
     */
    protected static function wrapStructuredHeader($value, StructuredInterface $header)
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
        return implode(Headers::FOLDING, $lines);
    }

    /**
     * MIME-encode a value
     *
     * Performs quoted-printable encoding on a value, setting maximum 
     * line-length to 998. 
     * 
     * @param  string $value 
     * @param  string $encoding 
     * @param  int    $lineLength maximum line-length, by default 998
     * @return string Returns the mime encode value without the last line ending
     */
    public static function mimeEncodeValue($value, $encoding, $lineLength = 998)
    {
        return Mime::encodeQuotedPrintableHeader($value, $encoding, $lineLength, Headers::EOL);
    }
}
