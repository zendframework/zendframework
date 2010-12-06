<?php

namespace Zend\View\Helper\Navigation;

use Zend\Loader\PluginClassLoader;

class HelperLoader extends PluginClassLoader
{
    protected $plugins = array(
        'breadcrumbs' => 'Zend\View\Helper\Navigation\Breadcrumbs',
        'links'       => 'Zend\View\Helper\Navigation\Links',
        'menu'        => 'Zend\View\Helper\Navigation\Menu',
        'sitemap'     => 'Zend\View\Helper\Navigation\Sitemap',
    );
}
