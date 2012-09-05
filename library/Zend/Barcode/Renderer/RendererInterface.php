<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Barcode
 */

namespace Zend\Barcode\Renderer;

use Zend\Barcode\Object\ObjectInterface;

/**
 * Class for rendering the barcode
 *
 * @category   Zend
 * @package    Zend_Barcode
 * @subpackage Renderer
 */
interface RendererInterface
{
    /**
     * Constructor
     * @param array|\Traversable $options
     */
    public function __construct($options = null);

    /**
     * Set renderer state from options array
     * @param  array $options
     * @return Renderer
     */
    public function setOptions($options);

    /**
     * Set renderer namespace for autoloading
     *
     * @param string $namespace
     * @return Renderer
     */
    public function setRendererNamespace($namespace);

    /**
     * Retrieve renderer namespace
     *
     * @return string
     */
    public function getRendererNamespace();

    /**
     * Retrieve renderer type
     * @return string
     */
    public function getType();

    /**
     * Manually adjust top position
     * @param integer $value
     * @return Renderer
     */
    public function setTopOffset($value);

    /**
     * Retrieve vertical adjustment
     * @return integer
     */
    public function getTopOffset();

    /**
     * Manually adjust left position
     * @param integer $value
     * @return Renderer
     */
    public function setLeftOffset($value);

    /**
     * Retrieve vertical adjustment
     * @return integer
     */
    public function getLeftOffset();

    /**
     * Activate/Deactivate the automatic rendering of exception
     * @param boolean $value
     */
    public function setAutomaticRenderError($value);

    /**
     * Horizontal position of the barcode in the rendering resource
     * @param string $value
     * @return Renderer
     */
    public function setHorizontalPosition($value);

    /**
     * Horizontal position of the barcode in the rendering resource
     * @return string
     */
    public function getHorizontalPosition();

    /**
     * Vertical position of the barcode in the rendering resource
     * @param string $value
     * @return Renderer
     */
    public function setVerticalPosition($value);

    /**
     * Vertical position of the barcode in the rendering resource
     * @return string
     */
    public function getVerticalPosition();

    /**
     * Set the size of a module
     * @param float $value
     * @return Renderer
     */
    public function setModuleSize($value);

    /**
     * Set the size of a module
     * @return float
     */
    public function getModuleSize();

    /**
     * Retrieve the automatic rendering of exception
     * @return boolean
     */
    public function getAutomaticRenderError();

    /**
     * Set the barcode object
     * @param  ObjectInterface $barcode
     * @return Renderer
     */
    public function setBarcode(ObjectInterface $barcode);

    /**
     * Retrieve the barcode object
     * @return ObjectInterface
     */
    public function getBarcode();

    /**
     * Checking of parameters after all settings
     * @return boolean
     */
    public function checkParams();

    /**
     * Draw the barcode in the rendering resource
     * @return mixed
     */
    public function draw();

    /**
     * Render the resource by sending headers and drawed resource
     * @return mixed
     */
    public function render();
}
