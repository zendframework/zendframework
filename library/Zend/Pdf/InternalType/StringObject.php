<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Pdf
 */

namespace Zend\Pdf\InternalType;

use Zend\Pdf;

/**
 * PDF file 'string' element implementation
 *
 * @category   Zend
 * @package    Zend_PDF
 * @subpackage Zend_PDF_Internal
 */
class StringObject extends AbstractTypeObject
{
    /**
     * Object value
     *
     * @var string
     */
    public $value;

    /**
     * Object constructor
     *
     * @param string $val
     */
    public function __construct($val)
    {
        $this->value   = (string)$val;
    }


    /**
     * Return type of the element.
     *
     * @return integer
     */
    public function getType()
    {
        return AbstractTypeObject::TYPE_STRING;
    }


    /**
     * Return object as string
     *
     * @param \Zend\Pdf\ObjectFactory $factory
     * @return string
     */
    public function toString(Pdf\ObjectFactory $factory = null)
    {
        return '(' . self::escape((string)$this->value) . ')';
    }


    /**
     * Escape string according to the PDF rules
     *
     * @param string $str
     * @return string
     */
    public static function escape($str)
    {
        $outEntries = array();

        foreach (str_split($str, 128) as $chunk) {
            // Collect sequence of unescaped characters
            $offset = strcspn($chunk, "\n\r\t\x08\x0C()\\");
            $chunkOut = substr($chunk, 0, $offset);

            while ($offset < strlen($chunk)) {
                $nextCode = ord($chunk[$offset++]);
                switch ($nextCode) {
                    // "\n" - line feed (LF)
                    case 10:
                        $chunkOut .= '\\n';
                        break;

                    // "\r" - carriage return (CR)
                    case 13:
                        $chunkOut .= '\\r';
                        break;

                    // "\t" - horizontal tab (HT)
                    case 9:
                        $chunkOut .= '\\t';
                        break;

                    // "\b" - backspace (BS)
                    case 8:
                        $chunkOut .= '\\b';
                        break;

                    // "\f" - form feed (FF)
                    case 12:
                        $chunkOut .= '\\f';
                        break;

                    // '(' - left paranthesis
                    case 40:
                        $chunkOut .= '\\(';
                        break;

                    // ')' - right paranthesis
                    case 41:
                        $chunkOut .= '\\)';
                        break;

                    // '\' - backslash
                    case 92:
                        $chunkOut .= '\\\\';
                        break;

                    default:
                        // This code is never executed extually
                        //
                        // Don't use non-ASCII characters escaping
                        // if ($nextCode >= 32 && $nextCode <= 126 ) {
                        //     // Visible ASCII symbol
                        //     $chunkEntries[] = chr($nextCode);
                        // } else {
                        //     $chunkEntries[] = sprintf('\\%03o', $nextCode);
                        // }

                        break;
                }

                // Collect sequence of unescaped characters
                $start = $offset;
                $offset += strcspn($chunk, "\n\r\t\x08\x0C()\\", $offset);
                $chunkOut .= substr($chunk, $start, $offset - $start);
            }

            $outEntries[] = $chunkOut;
        }

        return implode("\\\n", $outEntries);
    }


    /**
     * Unescape string according to the PDF rules
     *
     * @param string $str
     * @return string
     */
    public static function unescape($str)
    {
        $outEntries = array();

        $offset = 0;
        while ($offset < strlen($str)) {
            // Searche for the next escaped character/sequence
            $escapeCharOffset = strpos($str, '\\', $offset);
            if ($escapeCharOffset === false  ||  $escapeCharOffset == strlen($str) - 1) {
                // There are no escaped characters or '\' char has came at the end of string
                $outEntries[] = substr($str, $offset);
                break;
            } else {
                // Collect unescaped characters sequence
                $outEntries[] = substr($str, $offset, $escapeCharOffset - $offset);
                // Go to the escaped character
                $offset = $escapeCharOffset + 1;

                switch ($str[$offset]) {
                    // '\\n' - line feed (LF)
                    case 'n':
                        $outEntries[] = "\n";
                        break;

                    // '\\r' - carriage return (CR)
                    case 'r':
                        $outEntries[] = "\r";
                        break;

                    // '\\t' - horizontal tab (HT)
                    case 't':
                        $outEntries[] = "\t";
                        break;

                    // '\\b' - backspace (BS)
                    case 'b':
                        $outEntries[] = "\x08";
                        break;

                    // '\\f' - form feed (FF)
                    case 'f':
                        $outEntries[] = "\x0C";
                        break;

                    // '\\(' - left paranthesis
                    case '(':
                        $outEntries[] = '(';
                        break;

                    // '\\)' - right paranthesis
                    case ')':
                        $outEntries[] = ')';
                        break;

                    // '\\\\' - backslash
                    case '\\':
                        $outEntries[] = '\\';
                        break;

                    // "\\\n" or "\\\n\r"
                    case "\n":
                        // skip new line symbol
                        if ($str[$offset + 1] == "\r") {
                            $offset++;
                        }
                        break;

                    default:
                        if (strpos('0123456789', $str[$offset]) !== false) {
                            // Character in octal representation
                            // '\\xxx'
                            $nextCode = '0' . $str[$offset];

                            if (strpos('0123456789', $str[$offset + 1]) !== false) {
                                $nextCode .= $str[++$offset];

                                if (strpos('0123456789', $str[$offset + 1]) !== false) {
                                    $nextCode .= $str[++$offset];
                                }
                            }

                            $outEntries[] = chr(octdec($nextCode));
                        } else {
                            $outEntries[] = $str[$offset];
                        }
                        break;
                }

                $offset++;
            }
        }

        return implode($outEntries);
    }

}
