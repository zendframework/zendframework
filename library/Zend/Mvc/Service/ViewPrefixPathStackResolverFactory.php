<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Mvc\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\View\Resolver as ViewResolver;

class ViewPrefixPathStackResolverFactory implements FactoryInterface
{
    /**
     * Create the template prefix view resolver
     *
     * Creates a Zend\View\Resolver\PrefixPathStackResolver and populates it with the
     * ['view_manager']['prefix_template_path_stack'] and sets the default suffix with the
     * ['view_manager']['default_template_suffix']
     *
     * @param  ServiceLocatorInterface              $serviceLocator
     * @return ViewResolver\PrefixPathStackResolver
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');

        $prefixPathStackResolver = new ViewResolver\PrefixPathStackResolver();

        if (is_array($config) && isset($config['view_manager'])) {
            $config = $config['view_manager'];
            if (is_array($config)) {
                if (isset($config['prefix_template_path_stack'])) {
                    $prefixPathStackResolver->setPrefixes($config['prefix_template_path_stack']);
                }
                if (isset($config['default_template_suffix'])) {
                    $prefixPathStackResolver->setDefaultSuffix($config['default_template_suffix']);
                }
            }
        }

        return $prefixPathStackResolver;
    }
}
