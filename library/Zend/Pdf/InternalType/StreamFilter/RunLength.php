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

/**
 * RunLength stream filter
 *
 * @package    Zend_PDF
 * @subpackage Zend_PDF_Internal
 */
class RunLength implements StreamFilterInterface
{
    /**
     * Encode data
     *
     * @param string $data
     * @param array $params
     * @return string
     */
    public static function encode($data, $params = null)
    {
        $output = '';

        $chainStartOffset = 0;
        $offset = 0;

        while ($offset < strlen($data)) {
            // Do not encode 2 char chains since they produce 2 char run sequence,
            // but it takes more time to decode such output (because of processing additional run)
            if (($repeatedCharChainLength = strspn($data, $data[$offset], $offset + 1, 127) + 1)  >  2) {
                if ($chainStartOffset != $offset) {
                    // Drop down previouse (non-repeatable chars) run
                    $output .= chr($offset - $chainStartOffset - 1)
                             . substr($data, $chainStartOffset, $offset - $chainStartOffset);
                }

                $output .= chr(257 - $repeatedCharChainLength) . $data[$offset];

                $offset += $repeatedCharChainLength;
                $chainStartOffset = $offset;
            } else {
                $offset++;

                if ($offset - $chainStartOffset == 128) {
                    // Maximum run length is reached
                    // Drop down non-repeatable chars run
                    $output .= "\x7F" . substr($data, $chainStartOffset, 128);

                    $chainStartOffset = $offset;
                }
            }
        }

        if ($chainStartOffset != $offset) {
            // Drop down non-repeatable chars run
            $output .= chr($offset - $chainStartOffset - 1) . substr($data, $chainStartOffset, $offset - $chainStartOffset);
        }

        $output .= "\x80";

        return $output;
    }

    /**
     * Decode data
     *
     * @param string $data
     * @param array $params
     * @return string
     */
    public static function decode($data, $params = null)
    {
        $dataLength = strlen($data);
        $output = '';
        $offset = 0;

        while($offset < $dataLength) {
            $length = ord($data[$offset]);

            $offset++;

            if ($length == 128) {
                // EOD byte
                break;
            } elseif ($length < 128) {
                $length++;

                $output .= substr($data, $offset, $length);

                $offset += $length;
            } elseif ($length > 128) {
                $output .= str_repeat($data[$offset], 257 - $length);

                $offset++;
            }
        }

        return $output;
    }
}
