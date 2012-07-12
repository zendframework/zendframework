<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Pdf
 */

namespace Zend\Pdf\InternalType\StreamFilter\Compression;

use Zend\Pdf;
use Zend\Pdf\Exception;

/**
 * Flate stream filter
 *
 * @package    Zend_PDF
 * @subpackage Zend_PDF_Internal
 */
class Flate extends AbstractCompression
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
        global $php_errormsg;

        if ($params != null) {
            $data = self::_applyEncodeParams($data, $params);
        }

        if (extension_loaded('zlib')) {
            $trackErrors = ini_get( "track_errors");
            ini_set('track_errors', '1');

            if (($output = @gzcompress($data)) === false) {
                ini_set('track_errors', $trackErrors);
                if (!isset($php_errormsg)) {
                    $php_errormsg = 'Error occured while compressing PDF data using gzcompress() function.';
                }
                throw new Exception\RuntimeException($php_errormsg);
            }

            ini_set('track_errors', $trackErrors);
        } else {
            throw new Exception\NotImplementedException('Compression support requires zlib extension.');
        }

        return $output;
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
        global $php_errormsg;

        if (extension_loaded('zlib')) {
            $trackErrors = ini_get( "track_errors");
            ini_set('track_errors', '1');

            if (($output = @gzuncompress($data)) === false) {
                ini_set('track_errors', $trackErrors);
                if (!isset($php_errormsg)) {
                    $php_errormsg = 'Error occured while uncompressing PDF data using gzuncompress() function.';
                }
                throw new Exception\RuntimeException($php_errormsg);
            }

            ini_set('track_errors', $trackErrors);
        } else {
            throw new Exception\NotImplementedException('Compression support requires zlib extension.');
        }

        if ($params !== null) {
            return self::_applyDecodeParams($output, $params);
        } else {
            return $output;
        }
    }
}
