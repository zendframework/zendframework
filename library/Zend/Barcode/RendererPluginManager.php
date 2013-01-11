<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_Barcode
 */

namespace Zend\Barcode;

use Zend\ServiceManager\AbstractPluginManager;

/**
 * Plugin manager implementation for barcode renderers.
 *
 * Enforces that barcode parsers retrieved are instances of
 * Renderer\AbstractRenderer. Additionally, it registers a number of default
 * barcode renderers.
 *
 * @category   Zend
 * @package    Zend_Barcode
 */
class RendererPluginManager extends AbstractPluginManager
{
    /**
     * Default set of barcode renderers
     *
     * @var array
     */
    protected $invokableClasses = array(
        'image' => 'Zend\Barcode\Renderer\Image',
        'pdf'   => 'Zend\Barcode\Renderer\Pdf',
        'svg'   => 'Zend\Barcode\Renderer\Svg'
    );

    /**
     * Validate the plugin
     *
     * Checks that the barcode parser loaded is an instance
     * of Renderer\AbstractRenderer.
     *
     * @param  mixed $plugin
     * @return void
     * @throws Exception\InvalidArgumentException if invalid
     */
    public function validatePlugin($plugin)
    {
        if ($plugin instanceof Renderer\AbstractRenderer) {
            // we're okay
            return;
        }

        throw new Exception\InvalidArgumentException(sprintf(
            'Plugin of type %s is invalid; must extend %s\Renderer\AbstractRenderer',
            (is_object($plugin) ? get_class($plugin) : gettype($plugin)),
            __NAMESPACE__
        ));
    }
}
