<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Mvc\Service;

use ArrayAccess;
use Zend\ServiceManager\FactoryInterface;
use Zend\Mvc\View\Http\InjectTemplateListener;
use Zend\ServiceManager\ServiceLocatorInterface;

class InjectTemplateListenerFactory implements FactoryInterface
{
    /**
     * Create and return an InjectTemplateListener instance.
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return InjectTemplateListener
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = $serviceLocator->get('Config');
        if (isset($config['view_manager'])
            && (is_array($config['view_manager']) || $config['view_manager'] instanceof ArrayAccess)) {
            $config = $config['view_manager'];
        } else {
            $config = array();
        }

        $listener = new InjectTemplateListener();

        if (isset($config['controller_map']) && method_exists($listener, 'setControllerMap')) {
            $listener->setControllerMap($config['controller_map']);
        }

        return $listener;
    }
}
