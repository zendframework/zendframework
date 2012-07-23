<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @package   Zend_View
 */

namespace ZendTest\View\TestAsset;

use Zend\View\Renderer\RendererInterface as Renderer;
use Zend\View\Helper\HelperInterface as Helper;

class Invokable implements Helper
{
    protected $view;

    /**
     * Set the View object
     *
     * @param Renderer $view
     * @return Helper
     */
    public function setView(Renderer $view)
    {
        $this->view = $view;
    }

    /**
     * Get the View object
     *
     * @return Renderer
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * Invokable functor
     *
     * @param  string $message
     * @return string
     */
    public function __invoke($message)
    {
        return __METHOD__ . ': ' . $message;
    }
}
