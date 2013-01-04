<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_View
 */

namespace Zend\View\Renderer;

/**
 * @category   Zend
 * @package    Zend_View
 * @subpackage Renderer
 */
interface TreeRendererInterface
{
    /**
     * Indicate whether the renderer is capable of rendering trees of view models
     *
     * @return bool
     */
    public function canRenderTrees();
}
