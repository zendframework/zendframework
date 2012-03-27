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
 * @package    Zend_PDF
 * @subpackage Zend_PDF_Internal
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Pdf\InternalType\StreamFilter\Compression;
use Zend\Pdf\Exception;
use Zend\Pdf;

/**
 * Flate stream filter
 *
 * @uses       \Zend\Pdf\Exception
 * @uses       \Zend\Pdf\InternalType\StreamFilter\Compression\AbstractCompression
 * @package    Zend_PDF
 * @subpackage Zend_PDF_Internal
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Flate extends AbstractCompression
{
    /**
     * Encode data
     *
     * @param string $data
     * @param array $params
     * @return string
     * @throws \Zend\Pdf\Exception
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
     * @throws \Zend\Pdf\Exception
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
