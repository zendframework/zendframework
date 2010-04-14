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
 * @package    Zend_PDF_Internal
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * @namespace
 */
namespace Zend\PDF\InternalType\StreamFilter\Compression;
use Zend\PDF;

/**
 * LZW stream filter
 *
 * @uses       \Zend\PDF\Exception
 * @uses       \Zend\PDF\InternalType\StreamFilter\Compression\AbstractCompression
 * @package    Zend_PDF
 * @package    Zend_PDF_Internal
 * @copyright  Copyright (c) 2005-2010 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class LZW extends AbstractCompression
{
    /**
     * Get EarlyChange decode param value
     *
     * @param array $params
     * @return integer
     * @throws \Zend\PDF\Exception
     */
    private static function _getEarlyChangeValue($params)
    {
        if (isset($params['EarlyChange'])) {
            $earlyChange = $params['EarlyChange'];

            if ($earlyChange != 0  &&  $earlyChange != 1) {
                throw new PDF\Exception('Invalid value of \'EarlyChange\' decode param - ' . $earlyChange . '.' );
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
     * @throws \Zend\PDF\Exception
     */
    public static function encode($data, $params = null)
    {
        if ($params != null) {
            $data = self::_applyEncodeParams($data, $params);
        }

        throw new PDF\Exception('Not implemented yet');
    }

    /**
     * Decode data
     *
     * @param string $data
     * @param array $params
     * @return string
     * @throws \Zend\PDF\Exception
     */
    public static function decode($data, $params = null)
    {
        throw new PDF\Exception('Not implemented yet');

        if ($params !== null) {
            return self::_applyDecodeParams($data, $params);
        } else {
            return $data;
        }
    }
}
