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
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

/**
 * @namespace
 */
namespace Zend\Barcode;
use Zend\Loader\ShortNameLocator,
    Zend\Loader\Broker,
    Zend\Config\Config,
    Zend,
    Zend\Barcode\Exception\RendererCreationException,
    Zend\Barcode\Exception\InvalidArgumentException;

/**
 * Class for generate Barcode
 *
 * @uses       \Zend\Barcode\Exception
 * @uses       \Zend\Barcode\Object
 * @uses       \Zend\Loader
 * @category   Zend
 * @package    Zend_Barcode
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Barcode
{
    const OBJECT   = 'OBJECT';
    const RENDERER = 'RENDERER';
    /**
     * Default barcode TTF font name
     *
     * It's used by standard barcode objects derived from
     * {@link \Zend\Barcode\Object\AbstractObject} class
     * if corresponding constructor option is not provided.
     *
     * @var string
     */
    protected static $_staticFont = null;

    /**
     * The parser broker
     *
     * @var \Zend\Loader\Broker
     */
    protected static $objectBroker;

    /**
     * The renderer broker
     *
     * @var \Zend\Loader\Broker
     */
    protected static $rendererBroker;

    /**
     * Get the parser broker
     *
     * @return \Zend\Loader\Broker
     */
    public static function getObjectBroker()
    {
        if (!self::$objectBroker instanceof Broker) {
            self::$objectBroker = new ObjectBroker();
        }

        return self::$objectBroker;
    }

    /**
     * Get the renderer broker
     *
     * @return \Zend\Loader\Broker
     */
    public static function getRendererBroker()
    {
        if (!self::$rendererBroker instanceof Broker) {
            self::$rendererBroker = new RendererBroker();
        }

        return self::$rendererBroker;
    }

    /**
     * Factory for Zend_Barcode classes.
     *
     * First argument may be a string containing the base of the adapter class
     * name, e.g. 'int25' corresponds to class \Zend\Barcode\Object\Int25.  This
     * is case-insensitive.
     *
     * First argument may alternatively be an object of type \Zend\Config\Config.
     * The barcode class base name is read from the 'barcode' property.
     * The barcode config parameters are read from the 'params' property.
     *
     * Second argument is optional and may be an associative array of key-value
     * pairs.  This is used as the argument to the barcode constructor.
     *
     * If the first argument is of type \Zend\Config\Config, it is assumed to contain
     * all parameters, and the second argument is ignored.
     *
     * @param  mixed $barcode         String name of barcode class, or \Zend\Config\Config\Config object.
     * @param  mixed $renderer        String name of renderer class
     * @param  mixed $barcodeConfig   OPTIONAL; an array or \Zend\Config\Config object with barcode parameters.
     * @param  mixed $rendererConfig  OPTIONAL; an array or \Zend\Config\Config object with renderer parameters.
     * @param  boolean $automaticRenderError  OPTIONAL; set the automatic rendering of exception
     * @return \Zend\Barcode\Barcode
     * @throws \Zend\Barcode\Exception
     */
    public static function factory($barcode,
                                   $renderer = 'image',
                                   $barcodeConfig = array(),
                                   $rendererConfig = array(),
                                   $automaticRenderError = true)
    {
        /*
         * Convert \Zend\Config\Config argument to plain string
         * barcode name and separate config object.
         */
        if ($barcode instanceof Config) {
            if (isset($barcode->rendererParams)) {
                $rendererConfig = $barcode->rendererParams->toArray();
            }
            if (isset($barcode->renderer)) {
                $renderer = (string) $barcode->renderer;
            }
            if (isset($barcode->barcodeParams)) {
                $barcodeConfig = $barcode->barcodeParams->toArray();
            }
            if (isset($barcode->barcode)) {
                $barcode = (string) $barcode->barcode;
            } else {
                $barcode = null;
            }
        }

        try {
            $barcode  = self::makeBarcode($barcode, $barcodeConfig);
            $renderer = self::makeRenderer($renderer, $rendererConfig);
        } catch (Exception $e) {
            if ($automaticRenderError && !($e instanceof RendererCreationException)) {
                $barcode  = self::makeBarcode('error', array( 'text' => $e->getMessage() ));
                $renderer = self::makeRenderer($renderer, array());
            } else {
                throw $e;
            }
        }

        $renderer->setAutomaticRenderError($automaticRenderError);
        return $renderer->setBarcode($barcode);
    }

    /**
     * Barcode Constructor
     *
     * @param mixed $barcode        String name of barcode class, or \Zend\Config\Config object, or barcode object.
     * @param mixed $barcodeConfig  OPTIONAL; an array or \Zend\Config\Config object with barcode parameters.
     * @return \Zend\Barcode\Object
     */
    public static function makeBarcode($barcode, $barcodeConfig = array())
    {
        if ($barcode instanceof Object) {
            return $barcode;
        }

        /*
         * Convert \Zend\Config\Config argument to plain string
         * barcode name and separate config object.
         */
        if ($barcode instanceof Config) {
            if (isset($barcode->barcodeParams) && $barcode->barcodeParams instanceof Config) {
                $barcodeConfig = $barcode->barcodeParams->toArray();
            }
            if (isset($barcode->barcode)) {
                $barcode = (string) $barcode->barcode;
            } else {
                $barcode = null;
            }
        }
        if ($barcodeConfig instanceof Config) {
            $barcodeConfig = $barcodeConfig->toArray();
        }

        /*
         * Verify that barcode parameters are in an array.
         */
        if (!is_array($barcodeConfig)) {
            throw new InvalidArgumentException(
                'Barcode parameters must be in an array or a \Zend\Config\Config object'
            );
        }

        /*
         * Verify that an barcode name has been specified.
         */
        if (!is_string($barcode) || empty($barcode)) {
            throw new InvalidArgumentException(
                'Barcode name must be specified in a string'
            );
        }

        return self::getObjectBroker()->load($barcode, $barcodeConfig);
    }

    /**
     * Renderer Constructor
     *
     * @param mixed $renderer           String name of renderer class, or \Zend\Config\Config object.
     * @param mixed $rendererConfig     OPTIONAL; an array or \Zend\Config\Config object with renderer parameters.
     * @return \Zend\Barcode\Renderer
     */
    public static function makeRenderer($renderer = 'image', $rendererConfig = array())
    {
        if ($renderer instanceof Renderer) {
            return $renderer;
        }

        /*
         * Convert \Zend\Config\Config argument to plain string
         * barcode name and separate config object.
         */
        if ($renderer instanceof Config) {
            if (isset($renderer->rendererParams)) {
                $rendererConfig = $renderer->rendererParams->toArray();
            }
            if (isset($renderer->renderer)) {
                $renderer = (string) $renderer->renderer;
            }
        }
        if ($rendererConfig instanceof Config) {
            $rendererConfig = $rendererConfig->toArray();
        }

        /*
         * Verify that barcode parameters are in an array.
         */
        if (!is_array($rendererConfig)) {
            throw new RendererCreationException(
                'Barcode parameters must be in an array or a \Zend\Config\Config object'
            );
        }

        /*
         * Verify that an barcode name has been specified.
         */
        if (!is_string($renderer) || empty($renderer)) {
            throw new RendererCreationException(
                'Renderer name must be specified in a string'
            );
        }

        return self::getRendererBroker()->load($renderer, $rendererConfig);
    }

    /**
     * Proxy to renderer render() method
     *
     * @param string | \Zend\Barcode\Object | array | \Zend\Config\Config $barcode
     * @param string | \Zend\Barcode\Renderer $renderer
     * @param array  | \Zend\Config\Config $barcodeConfig
     * @param array  | \Zend\Config\Config $rendererConfig
     */
    public static function render($barcode,
                                  $renderer,
                                  $barcodeConfig = array(),
                                  $rendererConfig = array())
    {
        self::factory($barcode, $renderer, $barcodeConfig, $rendererConfig)->render();
    }

    /**
     * Proxy to renderer draw() method
     *
     * @param string | \Zend\Barcode\Object | array | \Zend\Config\Config $barcode
     * @param string | \Zend\Barcode\Renderer $renderer
     * @param array | \Zend\Config\Config $barcodeConfig
     * @param array | \Zend\Config\Config $rendererConfig
     * @return mixed
     */
    public static function draw($barcode,
                                $renderer,
                                $barcodeConfig = array(),
                                $rendererConfig = array())
    {
        return self::factory($barcode, $renderer, $barcodeConfig, $rendererConfig)->draw();
    }

    /**
     * Set the default font for new instances of barcode
     *
     * @param string $font
     * @return void
     */
    public static function setBarcodeFont($font)
    {
        self::$_staticFont = $font;
    }

    /**
     * Get current default font
     *
     * @return string
     */
    public static function getBarcodeFont()
    {
        return self::$_staticFont;
    }
}
