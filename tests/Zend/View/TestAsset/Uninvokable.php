<?php

namespace ZendTest\View\TestAsset;

use Zend\View\Renderer\RendererInterface as Renderer;
use Zend\View\Helper\HelperInterface as Helper;

class Uninvokable implements Helper
{
    protected $view;

    /**
     * Set the View object
     *
     * @param  Renderer $view
     * @return Uninvokable
     */
    public function setView(Renderer $view)
    {
        $this->view = $view;
        return $this;
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
}
