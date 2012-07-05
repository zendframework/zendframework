<?php

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
