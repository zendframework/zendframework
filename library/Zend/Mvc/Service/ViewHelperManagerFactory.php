<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage Service
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */

namespace Zend\Mvc\Service;

use Zend\Mvc\Exception;
use Zend\Mvc\Router\RouteMatch;
use Zend\View\Helper as ViewHelper;
use Zend\ServiceManager\ConfigurationInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

/**
 * @category   Zend
 * @package    Zend_Mvc
 * @subpackage Service
 * @copyright  Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class ViewHelperManagerFactory extends AbstractPluginManagerFactory
{
    const PLUGIN_MANAGER_CLASS = 'Zend\View\HelperPluginManager';

    /**
     * An array of helper configuration classes to ensure are on the helper_map stack.
     *
     * @var array
     */
    protected $defaultHelperMapClasses = array(
        'Zend\Form\View\HelperConfiguration',
        'Zend\I18n\View\HelperConfiguration',
        'Zend\Navigation\View\HelperConfiguration'
    );

    /**
     * Create and return the view helper manager
     *
     * @param  ServiceLocatorInterface $serviceLocator
     * @return ControllerPluginManager
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $plugins = parent::createService($serviceLocator);

        foreach ($this->defaultHelperMapClasses as $configClass) {
            if (is_string($configClass) && class_exists($configClass)) {
                $config = new $configClass;
            }

            if (!$config instanceof ConfigurationInterface) {
                throw new Exception\RuntimeException(sprintf(
                    'Invalid service manager configuration class provided; received "%s", expected class implementing %s',
                    $configClass,
                    'Zend\ServiceManager\ConfigurationInterface'
                ));
            }

            $config->configureServiceManager($plugins);
        }

        // Configure URL view helper with router
        $plugins->setFactory('url', function($sm) use($serviceLocator) {
            $helper = new ViewHelper\Url;
            $helper->setRouter($serviceLocator->get('Router'));

            $match = $serviceLocator->get('application')
                        ->getMvcEvent()
                        ->getRouteMatch();

            if ($match instanceof RouteMatch) {

                $helper->setRouteMatch($match);
            }

            return $helper;
        });

        $plugins->setFactory('basepath', function($sm) use($serviceLocator) {
            $config = $serviceLocator->get('Configuration');
            $config = $config['view_manager'];
            $basePathHelper = new ViewHelper\BasePath;
            if (isset($config['base_path'])) {
                $basePath = $config['base_path'];
            } else {
                $basePath = $serviceLocator->get('Request')->getBasePath();
            }
            $basePathHelper->setBasePath($basePath);
            return $basePathHelper;
        });

        /**
         * Configure doctype view helper with doctype from configuration, if available.
         *
         * Other view helpers depend on this to decide which spec to generate their tags
         * based on. This is why it must be set early instead of later in the layout phtml.
         */
        $plugins->setFactory('doctype', function($sm) use($serviceLocator) {
            $config = $serviceLocator->get('Configuration');
            $config = $config['view_manager'];
            $doctypeHelper = new ViewHelper\Doctype;
            if (isset($config['doctype'])) {
                $doctypeHelper->setDoctype($config['doctype']);
            }
            return $doctypeHelper;
        });

        return $plugins;
    }
}
