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
 * @package    Zend_Barcode
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Barcode;

use Zend\Loader\PluginClassLoader;

/**
 * Plugin Class Loader implementation for Barcodes.
 *
 * @category   Zend
 * @package    Zend_Barcode
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ObjectLoader extends PluginClassLoader
{
    /**
     * @var array Pre-aliased filter
     */
    protected $plugins = array(
        'codabar'            => 'Zend\Barcode\Object\Codabar',
        'code128'            => 'Zend\Barcode\Object\Code128',
        'code25'             => 'Zend\Barcode\Object\Code25',
        'code25_interleaved' => 'Zend\Barcode\Object\Code25interleaved',
        'code39'             => 'Zend\Barcode\Object\Code39',
        'ean13'              => 'Zend\Barcode\Object\Ean13',
        'ean2'               => 'Zend\Barcode\Object\Ean2',
        'ean5'               => 'Zend\Barcode\Object\Ean5',
        'ean8'               => 'Zend\Barcode\Object\Ean8',
        'error'              => 'Zend\Barcode\Object\Error',
        'identcode'          => 'Zend\Barcode\Object\Identcode',
        'itf14'              => 'Zend\Barcode\Object\Itf14',
        'leitcode'           => 'Zend\Barcode\Object\Leitcode',
        'planet'             => 'Zend\Barcode\Object\Planet',
        'postnet'            => 'Zend\Barcode\Object\Postnet',
        'royalmail'          => 'Zend\Barcode\Object\Royalmail',
        'upca'               => 'Zend\Barcode\Object\Upca',
        'upce'               => 'Zend\Barcode\Object\Upce',
    );
}
