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
 * LZW stream filter
 *
 * @package    Zend_PDF
 * @subpackage Zend_PDF_Internal
 */
class Lzw extends AbstractCompression
{
    /**
     * Get EarlyChange decode param value
     *
     * @param array $params
     * @return integer
     * @throws \Zend\Pdf\Exception\ExceptionInterface
     */
    private static function _getEarlyChangeValue($params)
    {
        if (isset($params['EarlyChange'])) {
            $earlyChange = $params['EarlyChange'];

            if ($earlyChange != 0  &&  $earlyChange != 1) {
                throw new Exception\CorruptedPdfException('Invalid value of \'EarlyChange\' decode param - ' . $earlyChange . '.' );
            }
            return $earlyChange;
        } else {
            return 1;
        }
    }


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
        if ($params != null) {
            $data = self::_applyEncodeParams($data, $params);
        }

        throw new Exception\NotImplementedException('Not implemented yet');
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
        throw new Exception\NotImplementedException('Not implemented yet');

        if ($params !== null) {
            return self::_applyDecodeParams($data, $params);
        } else {
            return $data;
        }
    }
}
