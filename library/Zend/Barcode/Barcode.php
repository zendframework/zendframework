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

use Traversable,
    Zend,
    Zend\Loader\Broker,
    Zend\Loader\ShortNameLocator,
    Zend\Stdlib\ArrayUtils;

/**
 * Class for generate Barcode
 *
 * @category   Zend
 * @package    Zend_Barcode
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
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
     * {@link Object\AbstractObject} class
     * if corresponding constructor option is not provided.
     *
     * @var string
     */
    protected static $staticFont = null;

    /**
     * The parser broker
     *
     * @var Broker
     */
    protected static $objectBroker;

    /**
     * The renderer broker
     *
     * @var Broker
     */
    protected static $rendererBroker;

    /**
     * Get the parser broker
     *
     * @return Broker
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
     * @return Broker
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
     * name, e.g. 'int25' corresponds to class Object\Int25.  This
     * is case-insensitive.
     *
     * First argument may alternatively be an object of type Traversable.
     * The barcode class base name is read from the 'barcode' property.
     * The barcode config parameters are read from the 'params' property.
     *
     * Second argument is optional and may be an associative array of key-value
     * pairs.  This is used as the argument to the barcode constructor.
     *
     * If the first argument is of type Traversable, it is assumed to contain
     * all parameters, and the second argument is ignored.
     *
     * @param  mixed $barcode         String name of barcode class, or Traversable object.
     * @param  mixed $renderer        String name of renderer class
     * @param  mixed $barcodeConfig   OPTIONAL; an array or Traversable object with barcode parameters.
     * @param  mixed $rendererConfig  OPTIONAL; an array or Traversable object with renderer parameters.
     * @param  boolean $automaticRenderError  OPTIONAL; set the automatic rendering of exception
     * @return Barcode
     * @throws Exception
     */
    public static function factory($barcode,
                                   $renderer = 'image',
                                   $barcodeConfig = array(),
                                   $rendererConfig = array(),
                                   $automaticRenderError = true)
    {
        /*
         * Convert Traversable argument to plain string
         * barcode name and separate config object.
         */
        if ($barcode instanceof Traversable) {
            $barcode = ArrayUtils::iteratorToArray($barcode);
            if (isset($barcode['rendererParams'])) {
                $rendererConfig = $barcode['rendererParams'];
            }
            if (isset($barcode['renderer'])) {
                $renderer = (string) $barcode['renderer'];
            }
            if (isset($barcode['barcodeParams'])) {
                $barcodeConfig = $barcode['barcodeParams'];
            }
            if (isset($barcode['barcode'])) {
                $barcode = (string) $barcode['barcode'];
            } else {
                $barcode = null;
            }
        }

        try {
            $barcode  = self::makeBarcode($barcode, $barcodeConfig);
            $renderer = self::makeRenderer($renderer, $rendererConfig);
        } catch (Exception\ExceptionInterface $e) {
            if ($automaticRenderError && !($e instanceof Exception\RendererCreationException)) {
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
     * @param mixed $barcode        String name of barcode class, or Traversable object, or barcode object.
     * @param mixed $barcodeConfig  OPTIONAL; an array or Traversable object with barcode parameters.
     * @return Object
     */
    public static function makeBarcode($barcode, $barcodeConfig = array())
    {
        if ($barcode instanceof Object\ObjectInterface) {
            return $barcode;
        }

        /*
         * Convert Traversable argument to plain string
         * barcode name and separate configuration.
         */
        if ($barcode instanceof Traversable) {
            $barcode = ArrayUtils::iteratorToArray($barcode);
            if (isset($barcode['barcodeParams']) && is_array($barcode['barcodeParams'])) {
                $barcodeConfig = $barcode['barcodeParams'];
            }
            if (isset($barcode['barcode'])) {
                $barcode = (string) $barcode['barcode'];
            } else {
                $barcode = null;
            }
        }
        if ($barcodeConfig instanceof Traversable) {
            $barcodeConfig = ArrayUtils::iteratorToArray($barcodeConfig);
        }

        /*
         * Verify that barcode parameters are in an array.
         */
        if (!is_array($barcodeConfig)) {
            throw new Exception\InvalidArgumentException(
                'Barcode parameters must be in an array or a Traversable object'
            );
        }

        /*
         * Verify that an barcode name has been specified.
         */
        if (!is_string($barcode) || empty($barcode)) {
            throw new Exception\InvalidArgumentException(
                'Barcode name must be specified in a string'
            );
        }

        return self::getObjectBroker()->load($barcode, $barcodeConfig);
    }

    /**
     * Renderer Constructor
     *
     * @param mixed $renderer           String name of renderer class, or Traversable object.
     * @param mixed $rendererConfig     OPTIONAL; an array or Traversable object with renderer parameters.
     * @return Renderer
     */
    public static function makeRenderer($renderer = 'image', $rendererConfig = array())
    {
        if ($renderer instanceof Renderer\RendererInterface) {
            return $renderer;
        }

        /*
         * Convert Traversable argument to plain string
         * barcode name and separate config object.
         */
        if ($renderer instanceof Traversable) {
            $renderer = ArrayUtils::iteratorToArray($renderer);
            if (isset($renderer['rendererParams'])) {
                $rendererConfig = $renderer['rendererParams'];
            }
            if (isset($renderer['renderer'])) {
                $renderer = (string) $renderer['renderer'];
            }
        }
        if ($rendererConfig instanceof Traversable) {
            $rendererConfig = ArrayUtils::iteratorToArray($rendererConfig);
        }

        /*
         * Verify that barcode parameters are in an array.
         */
        if (!is_array($rendererConfig)) {
            throw new Exception\RendererCreationException(
                'Barcode parameters must be in an array or a Traversable object'
            );
        }

        /*
         * Verify that an barcode name has been specified.
         */
        if (!is_string($renderer) || empty($renderer)) {
            throw new Exception\RendererCreationException(
                'Renderer name must be specified in a string'
            );
        }

        return self::getRendererBroker()->load($renderer, $rendererConfig);
    }

    /**
     * Proxy to renderer render() method
     *
     * @param string | Object\ObjectInterface | array | Traversable $barcode
     * @param string | Renderer $renderer
     * @param array  | Traversable $barcodeConfig
     * @param array  | Traversable $rendererConfig
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
     * @param string | Object\ObjectInterface | array | Traversable $barcode
     * @param string | Renderer $renderer
     * @param array | Traversable $barcodeConfig
     * @param array | Traversable $rendererConfig
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
        self::$staticFont = $font;
    }

    /**
     * Get current default font
     *
     * @return string
     */
    public static function getBarcodeFont()
    {
        return self::$staticFont;
    }
}
