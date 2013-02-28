<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Zend\Mvc\Service;

use Zend\ServiceManager\ServiceLocatorInterface;

class SerializerAdapterPluginManagerFactory extends AbstractPluginManagerFactory
{
    const PLUGIN_MANAGER_CLASS = 'Zend\Serializer\AdapterPluginManager';


    /**
     * {@inheritDoc}
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var $serviceListener \Zend\ModuleManager\Listener\ServiceListener */
        $serviceListener = $serviceLocator->get('ServiceListener');

        // This will allow to register new serializers easily, either by implementing the SerializerProviderInterface
        // in your Module.php file, or by adding the "serializers" key in your module.config.php file
        $serviceListener->addServiceManager(
            $serviceLocator,
            'serializers',
            'Zend\ModuleManager\Feature\SerializerProviderInterface',
            'getSerializerConfig'
        );

        return parent::createService($serviceLocator);
    }
}
