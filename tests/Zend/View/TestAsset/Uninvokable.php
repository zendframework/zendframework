<?php

namespace ZendTest\View\TestAsset;

use Zend\View\Renderer,
    Zend\View\Helper;

class Uninvokable implements Helper
{
    protected $view;

    /**
     * Set the View object
     *
     * @param  \Zend\View\Renderer $view
     * @return \Zend\View\Helper
     */
    public function setView(Renderer $view)
    {
        $this->view = $view;
    }

    /**
     * Get the View object
     *
     * @return \Zend\View\Renderer
     */
    public function getView()
    {
        return $this->view;
    }
}
