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
use Zend\Loader\PrefixPathLoader,
    Zend\Loader\ShortNameLocator,
    Zend\Config\Config,
    Zend,
    Zend\Barcode\Exception\RendererCreationException,
    Zend\Barcode\Exception\InvalidArgumentException;

/**
 * Class for generate Barcode
 *
 * @uses       \Zend\Barcode\Exception
 * @uses       \Zend\Barcode\BarcodeObject
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
     * Plugin loaders for object and renderer
     * @var array
     */
    protected static $_loaders = array();

    /**
     * Set plugin loader to use for validator or filter chain
     *
     * @param  \Zend\Loader\ShortNameLocator $loader
     * @param  string $type 'object', or 'renderer'
     * @return \Zend\Form\Element
     * @throws \Zend\Form\Exception on invalid type
     */
    public static function setPluginLoader(ShortNameLocator $loader, $type)
    {
        $type = strtoupper($type);
        switch ($type) {
            case self::OBJECT:
            case self::RENDERER:
                self::$_loaders[$type] = $loader;
                return;
            default:
                throw new InvalidArgumentException(sprintf('Invalid type "%s" provided to setPluginLoader()', $type));
        }
    }

    /**
     * Retrieve plugin loader for validator or filter chain
     *
     * Instantiates with default rules if none available for that type. Use
     * 'decorator', 'filter', or 'validate' for $type.
     *
     * @param  string $type
     * @return \Zend\Loader\ShortNameLocator
     * @throws \Zend\Loader\Exception on invalid type.
     */
    public static function getPluginLoader($type)
    {
        $type = strtoupper($type);
        switch ($type) {
            case self::OBJECT:
            case self::RENDERER:
                $prefixSegment = ucfirst(strtolower($type));
                $pathSegment   = $prefixSegment;
                if (!isset(self::$_loaders[$type])) {
                    self::$_loaders[$type] = new PrefixPathLoader(
                        array('Zend\\Barcode\\' . $prefixSegment . '\\' => 'Zend/Barcode/' . $pathSegment . '/')
                    );
                }
                return self::$_loaders[$type];
            default:
                throw new InvalidArgumentException(sprintf('Invalid type "%s" provided to getPluginLoader()', $type));
        }
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
        if ($barcode instanceof BarcodeObject) {
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

        $barcodeName = self::getPluginLoader(self::OBJECT)->load($barcode);

        if (!class_exists($barcodeName)) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Invalid Barcode "%s"; does not resolve to a valid class', $barcode
            ));
        }

        /*
         * Create an instance of the barcode class.
         * Pass the config to the barcode class constructor.
         */
        $bcAdapter = new $barcodeName($barcodeConfig);

        /*
         * Verify that the object created is a descendent of the abstract barcode type.
         */
        if (!$bcAdapter instanceof BarcodeObject) {
            throw new InvalidArgumentException(
                "Barcode class '$barcodeName' does not implement \Zend\Barcode\BarcodeObject"
            );
        }
        return $bcAdapter;
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
        if ($renderer instanceof BarcodeRenderer) {
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

        $rendererName = self::getPluginLoader(self::RENDERER)->load($renderer);
        if (!class_exists($rendererName)) {
            throw new InvalidArgumentException(sprintf(
                'Invalid renderer "%s"; does not resolve to a valid class', $renderer
            ));
        }

        /*
         * Create an instance of the barcode class.
         * Pass the config to the barcode class constructor.
         */
        $rdrAdapter = new $rendererName($rendererConfig);

        /*
         * Verify that the object created is a descendent of the abstract barcode type.
         */
        if (!$rdrAdapter instanceof BarcodeRenderer) {
            throw new RendererCreationException(
                "Renderer class '$rendererName' does not implements \Zend\Barcode\Renderer"
            );
        }
        return $rdrAdapter;
    }

    /**
     * Proxy to renderer render() method
     *
     * @param string | \Zend\Barcode\BarcodeObject | array | \Zend\Config\Config $barcode
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
     * @param string | \Zend\Barcode\BarcodeObject | array | \Zend\Config\Config $barcode
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
