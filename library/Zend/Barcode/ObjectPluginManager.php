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

use Zend\ServiceManager\AbstractPluginManager;

/**
 * Plugin manager implementation for barcode parsers.
 *
 * Enforces that barcode parsers retrieved are instances of
 * Object\AbstractObject. Additionally, it registers a number of default 
 * barcode parsers.
 *
 * @category   Zend
 * @package    Zend_Barcode
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ObjectPluginManager extends AbstractPluginManager
{
    /**
     * Default set of barcode parsers
     * 
     * @var array
     */
    protected $invokableClasses = array(
        'codabar'           => 'Zend\Barcode\Object\Codabar',
        'code128'           => 'Zend\Barcode\Object\Code128',
        'code25'            => 'Zend\Barcode\Object\Code25',
        'code25interleaved' => 'Zend\Barcode\Object\Code25interleaved',
        'code39'            => 'Zend\Barcode\Object\Code39',
        'ean13'             => 'Zend\Barcode\Object\Ean13',
        'ean2'              => 'Zend\Barcode\Object\Ean2',
        'ean5'              => 'Zend\Barcode\Object\Ean5',
        'ean8'              => 'Zend\Barcode\Object\Ean8',
        'error'             => 'Zend\Barcode\Object\Error',
        'identcode'         => 'Zend\Barcode\Object\Identcode',
        'itf14'             => 'Zend\Barcode\Object\Itf14',
        'leitcode'          => 'Zend\Barcode\Object\Leitcode',
        'planet'            => 'Zend\Barcode\Object\Planet',
        'postnet'           => 'Zend\Barcode\Object\Postnet',
        'royalmail'         => 'Zend\Barcode\Object\Royalmail',
        'upca'              => 'Zend\Barcode\Object\Upca',
        'upce'              => 'Zend\Barcode\Object\Upce',
    );

    /**
     * Validate the plugin
     *
     * Checks that the barcode parser loaded is an instance
     * of Object\AbstractObject.
     * 
     * @param  mixed $plugin 
     * @return void
     * @throws Exception\InvalidArgumentException if invalid
     */
    public function validatePlugin($plugin)
    {
        if ($plugin instanceof Object\AbstractObject) {
            // we're okay
            return;
        }

        throw new Exception\InvalidArgumentException(sprintf(
            'Plugin of type %s is invalid; must extend %s\Object\AbstractObject',
            (is_object($plugin) ? get_class($plugin) : gettype($plugin)),
            __NAMESPACE__
        ));
    }
}
