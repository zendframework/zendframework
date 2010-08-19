<?php
namespace Zend\View;

use Zend\Loader\PluginBroker;

class HelperBroker extends PluginBroker
{
    protected $defaultClassLoader = 'Zend\View\HelperLoader';

    /**
     * Determine if we have a valid helper
     * 
     * @param  mixed $plugin 
     * @return true
     * @throws InvalidHelperException
     */
    protected function validatePlugin($plugin)
    {
        if (!$plugin instanceof Helper) {
            throw new InvalidHelperException('View helpers must implement Zend\View\Helper');
        }
        return true;
    }
}
