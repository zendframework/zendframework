<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Pdf
 */

namespace Zend\Pdf\InternalType\StreamFilter;

use Zend\Pdf;
use Zend\Pdf\Exception;

/**
 * AsciiHex stream filter
 *
 * @package    Zend_PDF
 * @subpackage Zend_PDF_Internal
 */
class AsciiHex implements StreamFilterInterface
{
    /**
     * Encode data
     *
     * @param string $data
     * @param array $params
     * @return string
     * @throws \Zend\Pdf\Exception\ExceptionInterface
     */
    public static function encode($data, $params = null)
    {
        return bin2hex($data) . '>';
    }

    /**
     * Decode data
     *
     * @param string $data
     * @param array $params
     * @return string
     * @throws \Zend\Pdf\Exception\ExceptionInterface
     */
    public static function decode($data, $params = null)
    {
        $output  = '';
        $oddCode = true;
        $commentMode = false;

        for ($count = 0; $count < strlen($data)  &&  $data[$count] != '>'; $count++) {
            $charCode = ord($data[$count]);

            if ($commentMode) {
                if ($charCode == 0x0A  || $charCode == 0x0D ) {
                    $commentMode = false;
                }

                continue;
            }

            switch ($charCode) {
                //Skip white space
                case 0x00: // null character
                    // fall through to next case
                case 0x09: // Tab
                    // fall through to next case
                case 0x0A: // Line feed
                    // fall through to next case
                case 0x0C: // Form Feed
                    // fall through to next case
                case 0x0D: // Carriage return
                    // fall through to next case
                case 0x20: // Space
                    // Do nothing
                    break;

                case 0x25: // '%'
                    // Switch to comment mode
                    $commentMode = true;
                    break;

                default:
                    if ($charCode >= 0x30 /*'0'*/ && $charCode <= 0x39 /*'9'*/) {
                        $code = $charCode - 0x30;
                    } elseif ($charCode >= 0x41 /*'A'*/ && $charCode <= 0x46 /*'F'*/) {
                        $code = $charCode - 0x37/*0x41 - 0x0A*/;
                    } elseif ($charCode >= 0x61 /*'a'*/ && $charCode <= 0x66 /*'f'*/) {
                        $code = $charCode - 0x57/*0x61 - 0x0A*/;
                    } else {
                        throw new Exception\CorruptedPdfException('Wrong character in a encoded stream');
                    }

                    if ($oddCode) {
                        // Odd pass. Store hex digit for next pass
                        // Scope of $hexCodeHigh variable is whole function
                        $hexCodeHigh = $code;
                    } else {
                        // Even pass.
                        // Add decoded character to the output
                        // ($hexCodeHigh is stored in previous pass)
                        $output .= chr($hexCodeHigh*16 + $code);
                    }
                    $oddCode = !$oddCode;

                    break;
            }
        }

        /* Check that stream is terminated by End Of Data marker */
        if ($data[$count] != '>') {
            throw new Exception\CorruptedPdfException('Wrong encoded stream End Of Data marker.');
        }

        /* Last '0' character is omitted */
        if (!$oddCode) {
            $output .= chr($hexCodeHigh*16);
        }

        return $output;
    }
}
