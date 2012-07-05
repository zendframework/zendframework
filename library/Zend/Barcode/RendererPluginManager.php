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
 * Plugin manager implementation for barcode renderers.
 *
 * Enforces that barcode parsers retrieved are instances of
 * Renderer\AbstractRenderer. Additionally, it registers a number of default 
 * barcode renderers.
 *
 * @category   Zend
 * @package    Zend_Barcode
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
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
